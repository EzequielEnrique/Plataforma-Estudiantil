<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexionDB.php';
require_once 'auth.php'; 


$token = $_GET['Token'] ?? null;

if (!$token || !$auth->authenticateToken($token)) {
    http_response_code(403);
    echo json_encode(["error" => "Token no proporcionado o inválido"]);
    exit();
}

$pdo = new Conexion();

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !empty($data['id']) &&
        !empty($data['nombre']) &&
        !empty($data['apellido']) &&
        !empty($data['dni']) &&
        !empty($data['libro']) &&
        !empty($data['fecha_prestamo'])
    ) {
        $sql = $pdo->prepare("
            UPDATE prestamos 
            SET nombre = :nombre, 
                apellido = :apellido, 
                dni = :dni, 
                libro = :libro, 
                fecha_prestamo = :fecha_prestamo, 
                fecha_devolucion = :fecha_devolucion 
            WHERE id = :id
        ");
        $sql->bindValue(':id', $data['id']);
        $sql->bindValue(':nombre', $data['nombre']);
        $sql->bindValue(':apellido', $data['apellido']);
        $sql->bindValue(':dni', $data['dni']);
        $sql->bindValue(':libro', $data['libro']);
        $sql->bindValue(':fecha_prestamo', $data['fecha_prestamo']);
        $sql->bindValue(':fecha_devolucion', $data['fecha_devolucion'] ?? null);
        $sql->execute();

        echo json_encode(["message" => "Préstamo actualizado correctamente"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en el servidor", "details" => $e->getMessage()]);
}



?>




