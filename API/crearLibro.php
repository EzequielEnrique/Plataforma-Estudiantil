<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST,OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require_once 'conexionDB.php';

$data = json_decode(file_get_contents('php://input'), true);
$conexion = new Conexion();

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
