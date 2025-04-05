<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexionDB.php';
require __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$conexion = new Conexion();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$key = $_ENV['SECRET_KEY'];

try {
    if (!isset($_GET['Token'])) {
        echo json_encode(['error' => 'Token no proporcionado']);
        http_response_code(403);
        exit;
    }

    $token = $_GET['Token'];

    // Verificar el token
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $rol = $decoded->data->role; // Extrae el rol del usuario

        // Solo permitir la eliminación si el usuario es "Admin" o tiene permisos
        if ($rol !== 'Bibliotecario') {
            echo json_encode(['error' => 'No tienes permisos para eliminar libros']);
            http_response_code(403);
            exit;
        }

        // Si tiene permisos, proceder con la eliminación
        if (!isset($_GET['id'])) {
            echo json_encode(['error' => 'ID no proporcionado']);
            http_response_code(400);
            exit;
        }

        $id = $_GET['id'];
        $query = "DELETE FROM librosdebase WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Token inválido o expirado']);
        http_response_code(403);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}
?>


