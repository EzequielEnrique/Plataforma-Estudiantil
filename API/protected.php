<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once 'conexionDB.php';
require 'auth.php';
require __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new Conexion();
$auth = new Authentication($key);

$headers = apache_request_headers();
$token = null;

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    if (preg_match('/Bearer\s(\S+/)', $authHeader, $matches)) {
        $token = $matches[1];
    }
}

if ($token) {
    $decoded = $auth->authenticateToken($token);
    if ($decoded) {
        //Token valido, retornar informacion protegida
        header("HTTP/1,1 200 OK");
        echo json_encode(array(
            "message" => "Acceso autorizado",
            "data" => $decoded->data
        ));
    }else {
        //Token invalido
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(array("message" => "Token invalido o expirado"));
    }
}else {
    //No se proporciono token
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array("message" => "Token no proporcionado"));
}