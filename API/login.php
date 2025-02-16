<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST,OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require_once 'conexionDB.php';
require 'auth.php';
require __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


//Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new Conexion();
$auth = new Authentication($key);

$ruta = "/API/login.php";

//Verificar si el metodo HTTP es POST para iniciar sesion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Se realiza el control de las rutas de la API
    if ($_SERVER['REQUEST_URI'] == $ruta . "/auth") {
        authenticar($auth);
    } else {
        loginUser($pdo, $auth);
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        passwordChange($pdo, $auth);
        exit;
    }
    // Manejar la solicitud OPTIONS (preflight) para evitar errores de CORS
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        header("HTTP/1.1 200 OK");
        exit;
    }
    //En caso de que no sea una solicitud POST, retornar un error
    header("HTTP/1.1 405 Method not Allowed");
    echo json_encode(array("message" => "Metodo no permitido. Use POST"));
}

function loginUser($pdo, $auth)
{
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['perDni']) && isset($data['perContrasena'])) {
        $perDni = $data['perDni'];
        $perContrasena = $data['perContrasena'];

        try {
            //Verificar si el usuario existe
            $stmt = $pdo->prepare("SELECT p.idPersona ,p.perDni,p.perContrasena, r.rolNombre FROM Personas p INNER JOIN Roles r ON p.rolID = r.idRol WHERE p.perDni = :perDni");
            $stmt->bindParam(':perDni', $perDni);
            $stmt->execute();

            if ($stmt->rowCount()  == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                //Verificar la contraseña
                if (password_verify($perContrasena, $user['perContrasena'])) {

                    $token = $auth->generateToken($user['idPersona'], $user['rolNombre']);

                    //Retornar el token al cliente
                    header("HTTP/1.1 200 OK");
                    echo json_encode(array(
                        "message" => "Inicio de sesion exitoso",
                        "Rol" => $user['rolNombre'],
                        "Token" => $token
                    ));
                } else {
                    header("HTTP/1.1 401 Unauthorized");
                    echo json_encode(array("message" => "Credenciales incorrectas"));
                    exit;
                }
            } else {
                header("HTTP/1.1 401 Unauthorized");
                echo json_encode(array("message" => "Credenciales incorrectas"));
                exit;
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("message" => "Error al iniciar sesion: " . $e->getMessage()));
            exit;
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array("message" => "Debe proporcionar nombre de usuario"));
        exit;
    }
}

function authenticar($auth)
{
    // Procesar el JSON de la solicitud
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);
    $token = $data['Authorization'];


    if ($data['Authorization'] == null) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["message" => "No se proporcionó un token de autenticación"]);
        exit;
    } else {

        // Decodificar y autenticar el token
        $decodedToken = $auth->authenticateToken($token);
        if ($decodedToken == null) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["message" => "Token no válido"]);
            exit;
        } else {
            header("HTTP/1.1 200 OK");
            echo json_encode(["message" => "Token correcto"]);
        }
    }
}

function passwordChange($pdo, $auth)
{
    // Procesar el JSON de la solicitud
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);
    $token = $data['Authorization'];


    if ($data['Authorization'] == null) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["message" => "No se proporcionó un token de autenticación"]);
        exit;
    } else {

        // Decodificar y autenticar el token
        $decodedToken = $auth->authenticateToken($token);
        if ($decodedToken == null) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["message" => "Token no válido"]);
            exit;
        } else {
            //Lógica de Actualización de Contraseña TOKEN VÁLIDO
            // Verificar si se proporciona un ID de carrera y al menos el campo 'carNombre'
            if (isset($data['oldPass']) && isset($data['newPass'])) {
                $oldPass = $data['oldPass'];
                $newPass = $data['newPass'];
                $idPersona = $decodedToken->data->id;

                //Compruebo si la contraseña antigua es correcta
                $sql = "SELECT Personas.perContrasena FROM `Personas` WHERE Personas.idPersona = :idPersona ";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':idPersona', $idPersona);
                $stmt->execute();

                $persona = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($oldPass, $persona['perContrasena'])) {
                    //Actualizo la nueva contraseña
                    $hashedPassword = password_hash($newPass, PASSWORD_BCRYPT);

                    $sql = "UPDATE Personas SET perContrasena = :newPss WHERE idPersona = :idPersona";

                    // Preparar la consulta
                    $stmt = $pdo->prepare($sql);

                    // Vincular los valores y ejecutar la consulta
                    $stmt->bindParam(':newPss', $hashedPassword);
                    $stmt->bindParam(':idPersona', $idPersona);
                    // Ejecutar la consulta
                    try {
                        $stmt->execute();
                        header("HTTP/1.1 200 OK");
                        echo json_encode(["message" => "Actualización exitosa"]);
                    } catch (PDOException $e) {
                        echo json_encode(array("error" => "Error al actualizar: " . $e->getMessage()));
                    }
                    exit;
                } else {
                    header("HTTP/1.1 401 Unauthorized");
                    echo json_encode(array("message" => "Credenciales incorrectas"));
                    exit;
                }
            }
        }
    }
}



