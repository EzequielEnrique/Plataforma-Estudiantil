<?php
// Habilita el acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexionDB.php';
require_once 'auth.php'; 

$pdo = new Conexion();
$auth = new Authentication($_ENV['SECRET_KEY']); 


if (!isset($_GET['Authorization'])) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["error" => "Token no proporcionado"]);
    exit;
}

$token = $_GET['Authorization'];
$decodedToken = $auth->authenticateToken($token);


if (!$decodedToken) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["error" => "Token inválido"]);
    exit;
}


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest($pdo, $auth);
        break;
    case 'POST':
        handlePostRequest($pdo, $auth);
        break;
    case 'PUT':
        handlePutRequest($pdo, $auth);
        break;
    case 'DELETE':
        handleDeleteRequest($pdo, $auth);
        break;
    case 'OPTIONS':
        header("HTTP/1.1 200 OK");
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}


function handleGetRequest($pdo, $auth) {
    if (isset($_GET['idAutor'])) {
        $sql = $pdo->prepare("SELECT * FROM Autores WHERE idAutor=:idAutor");
        $sql->bindValue(':idAutor', $_GET['idAutor']);
    } elseif (isset($_GET['autNombre'])) {
        $sql = $pdo->prepare("SELECT * FROM Autores WHERE LOWER(autNombre) LIKE :autNombre");
        $sql->bindValue(':autNombre', '%' . strtolower($_GET['autNombre']) . '%', PDO::PARAM_STR);
    } else {
        $sql = $pdo->prepare("SELECT * FROM Autores");
    }
    
    $sql->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetchAll());
    exit;
}


function handlePostRequest($pdo, $auth) {
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
            
            $autFecDes = isset($data->autFecDes) ? $data->autFecDes : null;
            $stmt->bindParam(':autFecDes', $autFecDes);

            if ($stmt->execute()) {
                header("HTTP/1.1 201 Created");
                echo json_encode(['idAutor' => $pdo->lastInsertId()]);
            } else {
                throw new Exception("No se pudo crear el autor");
            }
        } catch (Exception $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Parámetros incompletos']);
    }
    exit;
}


function handlePutRequest($pdo, $auth) {
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->idAutor) && isset($data->autNombre)) {
        $sql = "UPDATE Autores SET autNombre = :autNombre, autApellido = :autApellido, autFecNac = :autFecNac, autBiografia = :autBiografia, autFecDes = :autFecDes WHERE idAutor = :idAutor";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':autNombre', $data->autNombre);
        $stmt->bindParam(':autApellido', $data->autApellido);
        $stmt->bindParam(':autFecNac', $data->autFecNac);
        $stmt->bindParam(':autBiografia', $data->autBiografia);
        $stmt->bindParam(':autFecDes', $data->autFecDes);
        $stmt->bindParam(':idAutor', $data->idAutor);

        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Actualización exitosa']);
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


function handleDeleteRequest($pdo, $auth) {
    if (isset($_GET['idAutor'])) {
        $sql = "DELETE FROM Autores WHERE idAutor=:idAutor";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idAutor', $_GET['idAutor']);

        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']);
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
