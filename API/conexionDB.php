<?php
/* require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load(); */

    class Conexion extends PDO
    {
        private $servidor = "localhost";
        private $usuario = "root"; 
        private $contrasenia = ""; 
        private $nombreBaseDatos = "base";

        public function __construct()
        {
            try{
                parent::__construct('mysql:host=' . $this->servidor . ';dbname=' . $this->nombreBaseDatos
                . ';charset=utf8', $this->usuario, $this->contrasenia, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
            }catch(PDOException $e){
                echo 'Error: '. $e->getMessage();
                exit;
            }
        }
    }
?>
