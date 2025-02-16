<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexionDB.php';

$pdo = new Conexion();

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Manejar preflight request
    header("HTTP/1.1 204 No Content");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Obtener los datos enviados en el cuerpo de la petición
    $input = json_decode(file_get_contents("php://input"), true);

    // Verificar que se envió un ID
    if (!isset($input['id']) || empty($input['id'])) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "El ID es requerido"]);
        exit;
    }

    // Verificar si hay datos para actualizar
    $fields = [];
    $params = [':id' => $input['id']];

    if (isset($input['nombre'])) {
        $fields[] = "nombre = :nombre";
        $params[':nombre'] = $input['nombre'];
    }
    if (isset($input['autor'])) {
        $fields[] = "autor = :autor";
        $params[':autor'] = $input['autor'];
    }
    if (isset($input['fecha_publicacion'])) {
        $fields[] = "fecha_publicacion = :fecha_publicacion";
        $params[':fecha_publicacion'] = $input['fecha_publicacion'];
    }
    if (isset($input['genero'])) {
        $fields[] = "genero = :genero";
        $params[':genero'] = $input['genero'];
    }
    if (isset($input['sinopsis'])) {
        $fields[] = "sinopsis = :sinopsis";
        $params[':sinopsis'] = $input['sinopsis'];
    }

    if (empty($fields)) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "No se proporcionaron datos para actualizar"]);
        exit;
    }

    // Construir y ejecutar la consulta SQL
    $sql = "UPDATE librosdebase SET " . implode(", ", $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute($params)) {
        header("HTTP/1.1 200 OK");
        echo json_encode(["success" => "Libro actualizado correctamente"]);
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(["error" => "No se pudo actualizar el libro"]);
    }
    exit;
}

header("HTTP/1.1 405 Method Not Allowed");
echo json_encode(["error" => "Método HTTP no permitido"]);
exit;
?>

