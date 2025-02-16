<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST,OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require_once 'conexionDB.php';

$pdo = new Conexion();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Listar todos los préstamos
        $stmt = $pdo->query("
            SELECT p.id, per.perNombre, per.perDni, l.titulo 
            FROM prestamos p
            INNER JOIN personas per ON p.idPersona = per.idPersona
            INNER JOIN librosdebase l ON p.idLibro = l.id
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        // Crear un nuevo préstamo
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("INSERT INTO prestamos (idPersona, idLibro) VALUES (:idPersona, :idLibro)");
        $stmt->execute([
            ':idPersona' => $data['idPersona'],
            ':idLibro' => $data['idLibro']
        ]);
        echo json_encode(['message' => 'Préstamo creado']);
        break;

    case 'PUT':
        // Actualizar un préstamo
        parse_str(file_get_contents("php://input"), $data);
        $stmt = $pdo->prepare("UPDATE prestamos SET idPersona = :idPersona, idLibro = :idLibro WHERE id = :id");
        $stmt->execute([
            ':idPersona' => $data['idPersona'],
            ':idLibro' => $data['idLibro'],
            ':id' => $data['id']
        ]);
        echo json_encode(['message' => 'Préstamo actualizado']);
        break;

// DELETE revisado para aceptar id en query params
case 'DELETE':
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM prestamos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['message' => 'Préstamo eliminado']);
    } else {
        echo json_encode(['error' => 'ID no especificado']);
    }
    break;

    

}
?>


