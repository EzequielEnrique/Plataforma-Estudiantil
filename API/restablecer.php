<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require 'conexion.php';
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$headers = apache_request_headers();
$token = $headers['Authorization'] ?? '';

$jwt_secret = "mi_secreto";
$input = json_decode(file_get_contents('php://input'), true);

try {
    $decoded = JWT::decode($token, new Key($jwt_secret, 'HS256'));
    $usuario = $decoded->data->usuario;

    $nuevaContrasena = $input['pssNuevo'];
    $hashedPassword = password_hash($nuevaContrasena, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("UPDATE personas SET pssHash = :pssHash WHERE usuario = :usuario");
    $stmt->execute([':pssHash' => $hashedPassword, ':usuario' => $usuario]);

    echo json_encode(["message" => "Contraseña actualizada"]);
} catch (Exception $e) {
    echo json_encode(["error" => "Acceso denegado"]);
    http_response_code(401);
}
?>
