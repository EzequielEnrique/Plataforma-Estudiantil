<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE,OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


require_once 'conexionDB.php';
require __DIR__ . '/../vendor/autoload.php'; 
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$key = $_ENV['SECRET_KEY'];

$pdo = new Conexion();

try {
    
    if (!isset($_GET['Token'])) {
        echo json_encode(['error' => 'Token no proporcionado']);
        http_response_code(403);
        exit;
    }

    $token = $_GET['Token'];

    try {
        
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $rol = $decoded->data->role;

        if ($rol !== 'Bibliotecario') {
            echo json_encode(['error' => 'No tienes permisos para eliminar préstamos']);
            http_response_code(403);
            exit;
        }

        
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = $pdo->prepare("DELETE FROM prestamos WHERE id = :id");
            $sql->bindValue(':id', $id);
            $sql->execute();
        
            echo json_encode(["message" => "Préstamo eliminado correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID no proporcionado"]);
        }        
    } catch (Exception $e) {
        http_response_code(403);
        echo json_encode(['error' => 'Token inválido o expirado']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en el servidor", "details" => $e->getMessage()]);
}


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>



