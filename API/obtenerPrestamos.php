<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexionDB.php';

$pdo = new Conexion();

try {
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

            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode(["message" => "Préstamo no encontrado"]);
            }
        } else {
            // Obtener todos los préstamos
            $sql = $pdo->prepare("
                SELECT id, nombre, apellido, dni, libro, fecha_prestamo, fecha_devolucion 
                FROM prestamos
            ");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode(["message" => "No hay préstamos registrados"]);
            }
        }
    } else {
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["error" => "Método HTTP no permitido"]);
    }
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error en el servidor", "details" => $e->getMessage()]);
}
?>





