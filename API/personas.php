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
        handleGetPersona($pdo, $auth);
        break;
    case 'PUT':
        updatePersona($pdo, $auth);
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

// Maneja solicitudes GET para obtener datos del estudiante
function handleGetPersona($pdo, $auth) {
    $token = $_GET['Authorization'];

    if ($token == null) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["error" => "No se proporcionó un token de autenticación"]);
        exit;
    }

    $decodedToken = $auth->authenticateToken($token);

    if ($decodedToken == null) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["error" => "Token no válido"]);
        exit;
    }

    $idPersona = $decodedToken->data->id;

    try {
        $sql = "SELECT idPersona, perNombre, perApellido, perTelefono, perDomicilio, perMail, localidadID, rolID, perDni FROM Personas WHERE idPersona = :idPersona";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idPersona', $idPersona);
        $stmt->execute();

        $persona = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($persona) {
            header("HTTP/1.1 200 OK");
            echo json_encode($persona);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["error" => "Persona no encontrada"]);
        }
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(["error" => $e->getMessage()]);
    }
}

function updatePersona($pdo, $auth) {
    $data = json_decode(file_get_contents("php://input"), true);
    $token = $data['Authorization'];

    if ($data['Authorization'] == null) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["error" => "No se proporcionó un token de autenticación"]);
        exit;
    }

    $decodedToken = $auth->authenticateToken($token);

    if ($decodedToken == null) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["error" => "Token no válido"]);
        exit;
    }

    $perID = $decodedToken->data->id;

    if (isset($data['perApellido']) && isset($data['perNombre']) && isset($data['perDni']) && isset($data['perMail'])) {
        $sql = "UPDATE Personas SET perMail = :perMail, perNombre = :perNombre, perApellido = :perApellido, perDni = :perDni WHERE idPersona = :perID";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':perMail', $data['perMail']);
        $stmt->bindParam(':perNombre', $data['perNombre']);
        $stmt->bindParam(':perApellido', $data['perApellido']);
        $stmt->bindParam(':perDni', $data['perDni']);
        $stmt->bindParam(':perID', $perID);

        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Actualización exitosa']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo actualizar la persona']);
        }
    } elseif (isset($data['perContrasena'])) {
        $password = $data['perContrasena'];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "UPDATE Personas SET perContrasena = :perContrasena WHERE idPersona = :perID";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':perContrasena', $hashedPassword);
        $stmt->bindParam(':perID', $perID);

        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Contraseña actualizada correctamente']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo actualizar la contraseña']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    $pdo = null;
    exit;
}


