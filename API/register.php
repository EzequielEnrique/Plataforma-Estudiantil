<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST,OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexionDB.php'; // Conexión a la base de datos

try {
    $pdo = new Conexion();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error en la conexión a la base de datos: " . $e->getMessage()]);
    exit();
}

// Manejar solicitudes de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    registerUser($pdo);
} elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
} else {
    http_response_code(405);
    echo json_encode(["message" => "Método no permitido."]);
    exit();
}

function registerUser($pdo)
{
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        isset($data['perNombre'], $data['perApellido'], $data['perMail'], $data['perDni'], $data['perContrasena'])
    ) {
        $perNombre = $data['perNombre'];
        $perApellido = $data['perApellido'];
        $perMail = $data['perMail'];
        $perDni = $data['perDni'];
        $perContrasena = $data['perContrasena'];

        // Cifrar la contraseña
        $hashedPassword = password_hash($perContrasena, PASSWORD_BCRYPT);

        try {
            // Verificar si el usuario ya existe
            $checkUser = $pdo->prepare("SELECT * FROM Personas WHERE perDni = :perDni OR perMail = :perMail");
            $checkUser->bindParam(':perDni', $perDni);
            $checkUser->bindParam(':perMail', $perMail);
            $checkUser->execute();

            if ($checkUser->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(["message" => "El usuario ya existe."]);
                return;
            }

            // Insertar el nuevo usuario
            $rol = 2; // Rol por defecto
            $insertUser = $pdo->prepare("
                INSERT INTO Personas (perNombre, perApellido, perMail, perDni, perContrasena, rolID) 
                VALUES (:perNombre, :perApellido, :perMail, :perDni, :perContrasena, :rolID)
            ");
            $insertUser->bindParam(':perNombre', $perNombre);
            $insertUser->bindParam(':perApellido', $perApellido);
            $insertUser->bindParam(':perMail', $perMail);
            $insertUser->bindParam(':perDni', $perDni);
            $insertUser->bindParam(':perContrasena', $hashedPassword);
            $insertUser->bindParam(':rolID', $rol);

            if ($insertUser->execute()) {
                http_response_code(201);
                echo json_encode(["message" => "Usuario registrado exitosamente."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error al registrar el usuario."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error en la base de datos: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Datos incompletos."]);
    }
}




