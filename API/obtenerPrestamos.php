<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexionDB.php';
require __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Cargar clave secreta desde .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$key = $_ENV['SECRET_KEY'];

$pdo = new Conexion();

try {
    // Verificar token
    if (!isset($_GET['Token'])) {
        echo json_encode(['error' => 'Token no proporcionado']);
        http_response_code(403);
        exit;
    }

    $token = $_GET['Token'];

    try {
        // Decodificar el token
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $rol = $decoded->data->role;

        // Si no es bibliotecario ni estudiante, no tiene acceso
        if ($rol !== 'Bibliotecario' && $rol !== 'Estudiante') {
            echo json_encode(['error' => 'No tienes permisos para ver los préstamos']);
            http_response_code(403);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (isset($_GET['id'])) {
                // Obtener un préstamo por ID
                $sql = $pdo->prepare("
                    SELECT id, nombre, apellido, dni, libro, fecha_prestamo, fecha_devolucion 
                    FROM prestamos 
                    WHERE id = :id
                ");
                $sql->bindValue(':id', $_GET['id']);
                $sql->execute();
                $result = $sql->fetch(PDO::FETCH_ASSOC);

                echo $result ? json_encode($result) : json_encode(["message" => "Préstamo no encontrado"]);
            } else {
                // Obtener todos los préstamos
                $sql = $pdo->prepare("
                    SELECT id, nombre, apellido, dni, libro, fecha_prestamo, fecha_devolucion 
                    FROM prestamos
                ");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);

                echo $result ? json_encode($result) : json_encode(["message" => "No hay préstamos registrados"]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método HTTP no permitido"]);
        }

    } catch (Exception $e) {
        http_response_code(403);
        echo json_encode(['error' => 'Token inválido o expirado']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en el servidor", "details" => $e->getMessage()]);
}
?>






