<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require_once 'conexionDB.php';
include 'auth.php';


$pdo = new Conexion();
$auth = new Authentication($key);

// Maneja la solicitud según el método HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getInscripcionesEstudiante($pdo);
        break;
    case 'POST':
        handlePostCargarInscripcion($pdo, $auth);
        break;
    case 'OPTIONS':
        // Maneja las solicitudes preflight
        header("HTTP/1.1 200 OK");
        http_response_code(200);
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}
//Obtener todas la inscripciones de un Estudiante
function getInscripcionesEstudiante($pdo){
    if (isset($_GET['estDNI'])) {
        $estDNI = $_GET['estDNI'];

        $sql = $pdo->prepare("SELECT inscripciones.idInscripciones, asignaturas.asiNombre,
        llamados.llaNombre, condiciones.conNombre, inscripciones.insAnioCursado,
        turnos.turNombre, turnos.idTurnos FROM inscripciones
        INNER JOIN Personas ON (inscripciones.personaID = Personas.idPersona)
        INNER JOIN condiciones ON (inscripciones.condicionID = condiciones.idCondiciones)
        INNER JOIN asignaturas ON (inscripciones.asignaturaID = asignaturas.idAsignaturas)
        INNER JOIN llamados ON (inscripciones.llamadoID = llamados.idLlamados)
        INNER JOIN turnos ON (inscripciones.turnoID = turnos.idTurnos)
        WHERE Personas.perDNI =:estDNI
        ");
        $sql->bindValue(':estDNI', $estDNI);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    }else
    
    {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'No proporciona el DNI']);
    }
}


// Maneja solicitudes POST y usa datos del token--------------------------
function handlePostCargarInscripcion($pdo, $auth) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtener el token del encabezado de Authorization
        // Procesar el JSON de la solicitud
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true);
        $token = $data['Authorization'];

        
        if ($data['Authorization'] == null) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["error" => "No se proporcionó un token de autenticación"]);
            exit;
        }

        // Decodificar y autenticar el token
        $decodedToken = $auth->authenticateToken($token);


        //echo json_encode($decodedToken);
        if ($decodedToken == null) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["error" => "Token no válido"]);
            exit;
        }

        // Extraer personaID del token decodificado
        $personaID = $decodedToken->data->id;


        // Procesar el JSON de la solicitud
       /*  $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true); */

        try {
            $sql = "INSERT INTO inscripciones (personaID, asignaturaID, llamadoID, condicionID, insAnioCursado, turnoID)
                    VALUES (:personaID, :asignaturaID, :llamadoID, :condicionID, :insAnioCursado, :turnoID)";

            $stmt = $pdo->prepare($sql);
            

            // Procesar inscripciones utilizando el ID del token en lugar del JSON de datos
            $turno = $data['turno'];
            $count = count($data['asignaturaID']);

            for ($i = 0; $i < $count; $i++) {
                $stmt->bindParam(':personaID', $personaID); // Usa personaID del token
                $stmt->bindParam(':asignaturaID', $data['asignaturaID'][$i]);
                $stmt->bindParam(':llamadoID', $data['llamadoID'][$i]);
                $stmt->bindParam(':insAnioCursado', $data['insAnioCursado'][$i]);
                $stmt->bindParam(':condicionID', $data['condicionID'][$i]);
                $stmt->bindParam(':turnoID', $turno);
                $stmt->execute();
            }

            header("HTTP/1.1 200 OK");
            echo json_encode(["message" => "Inscripciones realizadas con éxito."]);
        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}