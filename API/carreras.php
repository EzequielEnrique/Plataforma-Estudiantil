<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

       
require_once 'conexionDB.php';

    $pdo = new Conexion();

    if($_SERVER['REQUEST_METHOD'] == 'GET'){

            if(isset($_GET['id'])){
                $sql = $pdo->prepare("SELECT * FROM carreras WHERE idCarreras=:id");
                $sql->bindValue(':id', $_GET['id']);
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                header("HTTP/1.1 200 OK");
                echo json_encode($sql->fetchAll());
                exit;
            }

            elseif(isset($_GET['carNombre'])){
                $carNombre = $_GET['carNombre'];
                $carNombre = strtolower($carNombre); 
                $sql = $pdo->prepare("SELECT * FROM carreras WHERE LOWER(carNombre) LIKE :carNombre");
                $sql->bindValue(':carNombre', '%' . $carNombre . '%', PDO::PARAM_STR);
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                header("HTTP/1.1 200 OK");
                echo json_encode($sql->fetchAll());
                exit;
            } elseif (isset($_GET['carreraID'])) {
                
                $carreraID = $_GET['carreraID'];
                $sql = $pdo->prepare("SELECT * FROM asignaturas WHERE carreraID=:carreraID");
                $sql->bindValue(':carreraID', $carreraID);
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                header("HTTP/1.1 200 OK");
                echo json_encode($sql->fetchAll());
                exit;
            }
                     

            else{
                $sql = $pdo->prepare("SELECT * FROM carreras");
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                header("HTTP/1.1 200 OK");
                echo json_encode($sql->fetchAll());
                exit;
            }
        }
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $sql="INSERT INTO carreras (carNombre) 
        VALUES (:carNombre)";
        $data = json_decode(file_get_contents("php://input"));
        $carNombre = $data->carNombre;
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':carNombre', $carNombre);
        $stmt->execute();

        $idPost=$pdo->lastInsertId();

        if($idPost){
            header("HTTP/1.1 200 OK");
            echo json_encode($idPost);
        }
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        
        if (isset($data['idCarreras']) && isset($data['carNombre'])) {
            $carreraId = $data['idCarreras'];
            $carNombre = $data['carNombre'];
        
            
            $sql = "UPDATE carreras SET carNombre = :carNombre WHERE idCarreras = :idCarreras";
        
            
            $stmt = $pdo->prepare($sql);
        
            
            $stmt->bindParam(':carNombre', $carNombre);
            $stmt->bindParam(':idCarreras', $carreraId);
        
            
            try {
                $stmt->execute();
                echo json_encode(array("mensaje" => "Actualización exitosa"));
            } catch (PDOException $e) {
                echo json_encode(array("error" => "Error al actualizar: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array("error" => "Se requiere un ID de carrera y el campo 'carNombre'"));
        }
        
        
        $pdo = null;
            }

    if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $sql="DELETE FROM carreras WHERE idCarreras=:id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->execute();       
        header("HTTP/1.1 200 OK");
        exit;
    }


?>