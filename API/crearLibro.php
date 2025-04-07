<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'conexionDB.php';
require_once 'auth.php';

$conexion = new Conexion();
$auth = new Authentication($_ENV['SECRET_KEY']); 


$data = json_decode(file_get_contents('php://input'), true);


if (!isset($data['Token']) || empty($data['Token'])) {
    echo json_encode(['error' => 'Token no proporcionado']);
    http_response_code(401);
    exit;
}


$token = $data['Token'];
$decoded = $auth->authenticateToken($token);

if (!$decoded) {
    echo json_encode(['error' => 'Token inválido o expirado']);
    http_response_code(401);
    exit;
}

try {
    $query = "INSERT INTO librosdebase (nombre, fecha_publicacion, autor, genero, sinopsis) 
              VALUES (:nombre, :fecha_publicacion, :autor, :genero, :sinopsis)";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':nombre', $data['nombre']);
    $stmt->bindParam(':fecha_publicacion', $data['fecha_publicacion']);
    $stmt->bindParam(':autor', $data['autor']);
    $stmt->bindParam(':genero', $data['genero']);
    $stmt->bindParam(':sinopsis', $data['sinopsis']);
    $stmt->execute();
    
    echo json_encode(['id' => $conexion->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>


