<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: token, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require_once 'conexionDB.php';
<<<<<<< HEAD
/*include 'auth.php';*/

$pdo = new Conexion();
/*$auth = new Authentication($key);*/
=======

$pdo = new Conexion();
>>>>>>> 9b3a4ca1fa48ab9e0c58462c9c5b18bc447cb80f


// Maneja la solicitud según el método HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest($pdo);
        echo json_encode("Estoy en GET");
        break;
    case 'POST':
        handlePostCargarInscripcion($pdo, $auth);
        break;
    case 'PUT':
        handlePutRequest($pdo);
        break;
    case 'DELETE':
        handleDeleteRequest($pdo);
        break;
    case 'OPTIONS':
        // Maneja las solicitudes preflight
        header("HTTP/1.1 200 OK");
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}

// Maneja solicitudes GET
function handleGetRequest($pdo) {

    if (isset($_GET['idPersona'])) {
        $sql = $pdo->prepare("SELECT i.idInscripciones, p.idPersona, CONCAT(p.perNombre, ' ', p.perApellido) AS nombreCompleto, p.perDni ,a.asiNombre AS nombreAsignatura, t.turNombre, l.llaNombre, c.conNombre, i.insAnioCursado FROM inscripciones i INNER JOIN asignaturas a ON i.asignaturaID = a.idAsignaturas INNER JOIN Personas p ON i.personaID = p.idPersona INNER JOIN turnos t ON i.turnoID = t.idTurnos INNER JOIN llamados l ON i.llamadoID = l.idLlamados INNER JOIN condiciones c ON i.condicionID = c.idCondiciones WHERE p.idPersona = :idPersona");
        $sql->bindValue(':idPersona', $_GET['idPersona']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }else if (isset($_GET['perDni'])) {
        $sql = $pdo->prepare("SELECT i.idInscripciones, p.idPersona, CONCAT(p.perNombre, ' ', p.perApellido) AS nombreCompleto, p.perDni, a.asiNombre AS nombreAsignatura, t.turNombre, l.llaNombre, c.conNombre, i.insAnioCursado
                            FROM inscripciones i INNER JOIN asignaturas a ON i.asignaturaID = a.idAsignaturas INNER JOIN Personas p ON i.personaID = p.idPersona INNER JOIN
                                turnos t ON i.turnoID = t.idTurnos
                            INNER JOIN
                                llamados l ON i.llamadoID = l.idLlamados
                            INNER JOIN
                                condiciones c ON i.condicionID = c.idCondiciones
                            WHERE
                                p.perDni LIKE CONCAT('%', :perDni, '%');
");

        $sql->bindValue(':perDni', $_GET['perDni']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());

    }else if ((isset($_GET['asignaturaID']) && isset($_GET['llamadoID']))) {

        $sql = $pdo->prepare("SELECT Personas.perApellido, Personas.perNombre, Personas.perDni,
        inscripciones.insAnioCursado, condiciones.conNombre FROM inscripciones
        INNER JOIN Personas ON (inscripciones.personaID = Personas.idPersona)
        INNER JOIN condiciones ON (inscripciones.condicionID = condiciones.idCondiciones)
        WHERE inscripciones.asignaturaID = :asignaturaID AND inscripciones.llamadoID = :llamadoID
        ORDER BY conNombre, perApellido ASC");
        
        $sql->bindValue(':asignaturaID',$_GET['asignaturaID']);
        $sql->bindValue(':llamadoID', $_GET['llamadoID']);

        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }
    else{
        $sql = $pdo->prepare("SELECT i.idInscripciones, p.idPersona, CONCAT(p.perNombre, ' ', p.perApellido) AS nombreCompleto, p.perDni ,a.asiNombre AS nombreAsignatura, t.turNombre, l.llaNombre, c.conNombre, i.insAnioCursado FROM inscripciones i INNER JOIN asignaturas a ON i.asignaturaID = a.idAsignaturas INNER JOIN Personas p ON i.personaID = p.idPersona INNER JOIN turnos t ON i.turnoID = t.idTurnos INNER JOIN llamados l ON i.llamadoID = l.idLlamados INNER JOIN condiciones c ON i.condicionID = c.idCondiciones");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }

}


function handleDeleteRequest($pdo) {
    // Verifica si se proporciona 'idestudiante'
    if (isset($_GET['idIncripciones'])) {
        $sql = "DELETE FROM inscripciones WHERE inscripciones.idInscripciones = :idIncripciones";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idIncripciones', $_GET['idIncripciones']);
        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']); // Retorna un mensaje de éxito
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo eliminar el estudiante']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;
}

// Maneja solicitudes POST y usa datos del token--------------------------
function handlePostCargarInscripcion($pdo, $auth) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtener el token del encabezado de Authorization
        $headers = getallheaders();
        $token = isset($headers['Token']);

        if (!$token) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["error" => "No se proporcionó un token de autenticación"]);
            exit;
        }

        // Decodificar y autenticar el token
        $decodedToken = $auth->authenticateToken($token);
        if ($decodedToken == null) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["error" => "Token no válido"]);
            exit;
        }

        // Extraer personaID del token decodificado
        $personaID = $decodedToken->data->id;

        // Procesar el JSON de la solicitud
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true);

        try {
            $sql = "INSERT INTO inscripciones (personaID, asignaturaID, llamadoID, condicionID, insAnioCursado, turnoID)
                    VALUES (:personaID, :asignaturaID, :llamadoID, :condicionID, :insAnioCursado, :turnoID)";

            $stmt = $pdo->prepare($sql);

            // Procesar inscripciones utilizando el ID del token en lugar del JSON de datos
            $turno = 1;
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


/*
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Habilita la notificación de todos los errores
error_reporting(E_ALL);

// Muestra los errores en el navegador
ini_set('display_errors', 1);

include_once 'conexionBD.php'; // Asegúrate de que este archivo incluye la configuración de la base de datos.
$pdo = new Conexion();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['dni'])) {
        $dni = $_GET['dni'];

        $sql = $pdo->prepare("SELECT * FROM inscripciones
                              INNER JOIN personas ON inscripciones.personaID = personas.idPersona
                              WHERE personas.perDNI = :dni");
        $sql->bindValue(':dni', $dni);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    } else if (isset($_GET['estDNI'])) {
        $estDNI = $_GET['estDNI'];

        $sql = $pdo->prepare("SELECT inscripciones.idInscripciones, personas.perApellido, personas.perNombre, personas.perDNI, asignaturas.asiNombre,
        llamados.llaNombre, condiciones.conNombre, inscripciones.insAnioCursado,
        turnos.turNombre FROM inscripciones
        INNER JOIN personas ON (inscripciones.personaID = personas.idPersona)
        INNER JOIN condiciones ON (inscripciones.condicionID = condiciones.idCondiciones)
        INNER JOIN asignaturas ON (inscripciones.asignaturaID = asignaturas.idAsignaturas)
        INNER JOIN llamados ON (inscripciones.llamadoID = llamados.idLlamados)
        INNER JOIN turnos ON (inscripciones.turnoID = turnos.idTurnos)
        WHERE personas.perDNI = :estDNI
        ORDER BY asiNombre ASC");
        $sql->bindValue(':estDNI', $estDNI);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    } else if (isset($_GET['asignaturaID']) && isset($_GET['llamadoID'])) {
        $asignaturaID = $_GET['asignaturaID'];
        $llamadoID = $_GET['llamadoID'];

        $sql = $pdo->prepare("SELECT personas.perApellido, personas.perNombre, personas.perDNI,
        inscripciones.insAnioCursado, condiciones.conNombre FROM inscripciones
        INNER JOIN personas ON (inscripciones.personaID = personas.idPersona)
        INNER JOIN condiciones ON (inscripciones.condicionID = condiciones.idCondiciones)
        WHERE inscripciones.asignaturaID = :asignaturaID AND inscripciones.llamadoID = :llamadoID
        ORDER BY conNombre, perApellido ASC");
        $sql->bindValue(':asignaturaID', $asignaturaID);
        $sql->bindValue(':llamadoID', $llamadoID);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    } else {
        $sql = $pdo->prepare("SELECT i.idInscripciones, p.idPersona, CONCAT(p.perNombre, ' ', p.perApellido) AS nombreCompleto, p.perDni ,a.asiNombre AS nombreAsignatura, t.turNombre, l.llaNombre, c.conNombre, i.insAnioCursado FROM inscripciones i INNER JOIN asignaturas a ON i.asignaturaID = a.idAsignaturas INNER JOIN Personas p ON i.personaID = p.idPersona INNER JOIN turnos t ON i.turnoID = t.idTurnos INNER JOIN llamados l ON i.llamadoID = l.idLlamados INNER JOIN condiciones c ON i.condicionID = c.idCondiciones");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    }
}
 */



/*
//POST ANTERIOR<------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    try {
        $sql = "INSERT INTO inscripciones (personaID, asignaturaID, llamadoID, condicionID, insAnioCursado, turnoID)
                VALUES (:personaID, :asignaturaID, :llamadoID, :condicionID, :insAnioCursado, :turnoID)";

        $stmt = $pdo->prepare($sql);

        $turno = 1;

        // Obtener la cantidad de elementos en estudiantesID para iterar sobre todos los arrays
        $count = count($data['personaID']);

        for ($i = 0; $i < $count; $i++) {
            $stmt->bindParam(':personaID', $data['personaID'][$i]);
            $stmt->bindParam(':asignaturaID', $data['asignaturaID'][$i]);
            $stmt->bindParam(':llamadoID', $data['llamadoID'][$i]);
            $stmt->bindParam(':insAnioCursado', $data['insAnioCursado'][$i]);
            $stmt->bindParam(':condicionID', $data['condicionID'][$i]);
            $stmt->bindParam(':turnoID', $turno);

            $stmt->execute();
        }

        // Respuesta después de la inserción
    } catch(PDOException $e) {
        // Manejo de errores
    }
}/*

/* if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    $sql="DELETE FROM inscripciones WHERE idInscripciones=:id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_GET['id']);
    $stmt->execute();
    header("HTTP/1.1 200 OK");
    exit;
} */

?>