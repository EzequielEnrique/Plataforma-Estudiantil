<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE,OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require_once 'conexionDB.php';

$pdo = new Conexion();

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['id'])) {
        // Eliminar el préstamo
        $sql = $pdo->prepare("
            DELETE FROM prestamos 
            WHERE id = :id
        ");
        $sql->bindValue(':id', $data['id']);
        $sql->execute();

        echo json_encode(["message" => "Préstamo eliminado correctamente"]);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "ID no proporcionado"]);
    }
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error en el servidor", "details" => $e->getMessage()]);
}

// Manejo de preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

?>


