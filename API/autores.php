<?php
// Habilita el acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Habilita los métodos HTTP permitidos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Establece el tipo de contenido de la respuesta como JSON
header("Content-Type: application/json; charset=UTF-8");
// Habilita las cabeceras permitidas
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexionDB.php';

$pdo = new Conexion();

// Maneja la solicitud según el método HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest($pdo);
        break;
    case 'POST':
        handlePostRequest($pdo);
        break;
    case 'PUT':
        handlePutRequest($pdo);
        break;
    case 'DELETE':
        handleDeleteRequest($pdo);
        break;
    case 'OPTIONS':
        // Maneja las solicitudes preflight
        header("HTTP/1.1 200 OK");
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}

// Maneja solicitudes GET
function handleGetRequest($pdo) {
    /*
    GET
Por ID (idAutor):
Parámetro: idAutor=1
Retorno: Información del rol con ID 1.
Por nombre (rolNombre):
Parámetro: rolNombre=admin
Retorno: Información de los roles cuyo nombre contenga "admin".
    
    */
    // Si se proporciona el parámetro 'idRol', busca por ID
    if (isset($_GET['idAutor'])) {
        $sql = $pdo->prepare("SELECT * FROM Autores WHERE idAutor=:idAutor");
        $sql->bindValue(':idAutor', $_GET['idAutor']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    } 
    
    // Si se proporciona el autámetro 'rolNombre', busca por nombre
    elseif (isset($_GET['autNombre'])) {
        $autNombre = strtolower($_GET['autNombre']);
        $sql = $pdo->prepare("SELECT * Autores WHERE LOWER(autNombre) LIKE :autNombre");
        $sql->bindValue(':rolNombre', '%' . $autNombre . '%', PDO::PARAM_STR);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    } 
    // Si no se proporciona ningún parámetro, obtiene todos los roles
    else {
        $sql = $pdo->prepare("SELECT * FROM Autores");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }
    exit;
}
/*
    POST
    Crear un nuevo Autor:
    Cuerpo JSON: {"autNombre": "nuevoAutor"}
    Retorno: ID del nuevo rol creado.
    */
// Maneja solicitudes POST
// Maneja solicitudes POST (Crear un nuevo autor)
function handlePostRequest($pdo) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->autNombre, $data->autApellido, $data->autFecNac, $data->autBiografia)) {
        try {
            $sql = "INSERT INTO Autores (autNombre, autApellido, autFecNac, autBiografia, autFecDes) 
                    VALUES (:autNombre, :autApellido, :autFecNac, :autBiografia, :autFecDes)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':autNombre', $data->autNombre);
            $stmt->bindParam(':autApellido', $data->autApellido);
            $stmt->bindParam(':autFecNac', $data->autFecNac);
            $stmt->bindParam(':autBiografia', $data->autBiografia);
            
            // Si autFecDes no está definido, lo dejamos como NULL
            $autFecDes = isset($data->autFecDes) ? $data->autFecDes : null;
            $stmt->bindParam(':autFecDes', $autFecDes);

            if ($stmt->execute()) {
                $idPost = $pdo->lastInsertId();
                header("HTTP/1.1 201 Created");
                echo json_encode(['idAutor' => $idPost]);
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(['error' => 'No se pudo crear el autor']);
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Parámetros incompletos']);
    }
    exit;
}
/*
PUT
Actualizar un rol existente:
Cuerpo JSON: {"idAutor": 1, "autNombre": "autorActualizado"}
Retorno: Mensaje de actualización exitosa.

*/
// Maneja solicitudes PUT
function handlePutRequest($pdo) {
    $data = json_decode(file_get_contents("php://input"));
    // Verifica si se proporcionan 'idAutor' y 'autNombre'
    if (isset($data->idAutor) && isset($data->autNombre)) {
        $sql = "UPDATE Autores SET autNombre = (:autNombre), autApellido = (:autApellido), autFecNac = (:autFecNac), autBiografia = (:autBiografia), autFecDes = (:autFecDes) WHERE idAutor = (:idAutor)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':autNombre', $data->autNombre);
        $stmt->bindParam(':autApellido', $data->autApellido);
        $stmt->bindParam(':autFecNac', $data->autFecNac);
        $stmt->bindParam(':autBiografia', $data->autBiografia);
        $stmt->bindParam(':autFecDes', $data->autFecDes);
        $stmt->bindParam(':idAutor', $data->idAutor);
        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Actualización exitosa']); // Retorna un mensaje de éxito
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo actualizar el Autor']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;
}
/*
DELETE
Eliminar un rol por ID (idAutor):
Parámetro: idAutor=1
Retorno: Mensaje de eliminación exitosa.
*/
// Maneja solicitudes DELETE
function handleDeleteRequest($pdo) {
    // Verifica si se proporciona 'idAutor'
    if (isset($_GET['idAutor'])) {
        $sql = "DELETE FROM Autores WHERE idAutor=:idAutor";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idAutor', $_GET['idAutor']);
        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']); // Retorna un mensaje de éxito
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo eliminar el autor']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;
}
?>