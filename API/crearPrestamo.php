<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

require_once 'conexionDB.php';

$pdo = new Conexion();

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !empty($data['nombre']) &&
        !empty($data['apellido']) &&
        !empty($data['dni']) &&
        !empty($data['libro']) &&
        !empty($data['fecha_prestamo'])
    ) {
        $sql = $pdo->prepare("
            INSERT INTO prestamos (nombre, apellido, dni, libro, fecha_prestamo, fecha_devolucion) 
            VALUES (:nombre, :apellido, :dni, :libro, :fecha_prestamo, :fecha_devolucion)
        ");
        $sql->bindValue(':nombre', $data['nombre']);
        $sql->bindValue(':apellido', $data['apellido']);
        $sql->bindValue(':dni', $data['dni']);
        $sql->bindValue(':libro', $data['libro']);
        $sql->bindValue(':fecha_prestamo', $data['fecha_prestamo']);
        $sql->bindValue(':fecha_devolucion', $data['fecha_devolucion'] ?? null);
        $sql->execute();

        echo json_encode(["message" => "Préstamo creado correctamente"]);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "Datos incompletos"]);
    }
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error en el servidor", "details" => $e->getMessage()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>



