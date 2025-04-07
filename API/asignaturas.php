<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


require_once 'conexionDB.php';

    $pdo = new Conexion();

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

    


    
    if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        $sql="UPDATE asignaturas SET asiNombre=:asiNombre WHERE idAsignaturas=:id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':asiNombre', $_GET['asiNombre']);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->execute();       
        header("HTTP/1.1 200 OK");
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $sql="DELETE FROM asignaturas WHERE idAsignaturas=:id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->execute();       
        header("HTTP/1.1 200 OK");
        exit;
    }
?>