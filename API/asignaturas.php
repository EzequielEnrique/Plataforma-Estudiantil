<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST,DELETE,PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conexión y autenticación
require_once 'conexionDB.php';
require_once 'auth.php'; // ← Añadimos la autenticación

$pdo = new Conexion();
$auth = new Authentication($_ENV['SECRET_KEY']); // ← Inicializamos auth

// Obtener token de la URL
if (!isset($_GET['Authorization'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Token no proporcionado']);
    exit;
}

$token = $_GET['Authorization'];
$payload = $auth->authenticateToken($token);

// Validar token
if (!$payload) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido o expirado']);
    exit;
}

// ✅ Opcional: Podés acceder al ID del usuario o rol así:
$idUsuario = $payload->data->id;
$rolUsuario = $payload->data->role;

// Métodos
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $sql = $pdo->prepare("SELECT * FROM asignaturas WHERE idAsignaturas=:id");
        $sql->bindValue(':id', $_GET['id']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;

    } elseif (isset($_GET['carreraID'])) {
        $carreraID = $_GET['carreraID'];
        $sql = $pdo->prepare("SELECT * FROM asignaturas WHERE carreraID=:carreraID ORDER BY asiNombre");
        $sql->bindValue(':carreraID', $carreraID);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;

    } else {
        $sql = $pdo->prepare("SELECT * FROM asignaturas");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $sql = "UPDATE asignaturas SET asiNombre=:asiNombre WHERE idAsignaturas=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':asiNombre', $_GET['asiNombre']);
    $stmt->bindValue(':id', $_GET['id']);
    $stmt->execute();
    header("HTTP/1.1 200 OK");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $sql = "DELETE FROM asignaturas WHERE idAsignaturas=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_GET['id']);
    $stmt->execute();
    header("HTTP/1.1 200 OK");
    exit;
}
?>
