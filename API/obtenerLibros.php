<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'conexionDB.php';
require_once 'auth.php'; // Archivo donde validas el token

$pdo = new Conexion();

// Validar que el token esté presente en la URL
if (!isset($_GET['Token'])) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["error" => "Token no proporcionado"]);
    exit;
}

$token = $_GET['Token'];

// Instanciar la clase Authentication con la clave secreta
$auth = new Authentication($_ENV['SECRET_KEY']); 

// Verificar si el token es válido
$decodedToken = $auth->authenticateToken($token);

if (!$decodedToken) {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(["error" => "Token inválido o expirado"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        // Obtener un libro por su ID
        $sql = $pdo->prepare("SELECT * FROM librosdebase WHERE id=:id");
        $sql->bindValue(':id', $_GET['id']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetch());
        exit;
    } else {
        // Obtener todos los libros
        $sql = $pdo->prepare("SELECT * FROM librosdebase");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    }
}

header("HTTP/1.1 405 Method Not Allowed");
echo json_encode(["error" => "Método HTTP no permitido"]);
exit;
?>







