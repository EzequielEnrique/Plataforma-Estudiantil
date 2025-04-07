<?php
// Habilitar CORS
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    
    http_response_code(200);
    exit();
}

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__ . '/../vendor/autoload.php';

class JWT_Helper {
    private static $key = 'secreto'; // Clave secreta para el JWT 

    public static function encode($payload) {
        return JWT::encode($payload, self::$key, 'HS256');
    }

    public static function decode($jwt) {
        return JWT::decode($jwt, new Key(self::$key, 'HS256')); 
    }
}
?>

