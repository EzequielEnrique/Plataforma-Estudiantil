<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: token, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


require_once 'conexionDB.php';

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
   
    if (isset($_GET['idPersona'])) {
        $sql = $pdo->prepare("SELECT p.idPersona, p.perNombre, p.perApellido, p.perTelefono, p.perDomicilio, p.perMail, p.perDni, p.perContrasena, loc.locNombre, rol.idRol, rol.rolNombre FROM Personas p INNER JOIN Localidad loc ON p.localidadID = loc.idLocalidad INNER JOIN Roles rol ON p.rolID = rol.idRol where rol.idRol = 2 && p.idPersona = :idPersona ORDER BY p.perApellido");
        $sql->bindValue(':idPersona', $_GET['idPersona']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }else if (isset($_GET['perDni'])) {
        $sql = $pdo->prepare("SELECT 
        p.idPersona, 
        p.perNombre, 
        p.perApellido, 
        p.perDni, 
        p.perContrasena, 
        rol.idRol, 
        rol.rolNombre 
    FROM 
        Personas p 
    INNER JOIN 
        Roles rol ON p.rolID = rol.idRol 
    WHERE 
        rol.idRol = 2 
        AND p.perDni LIKE CONCAT('%', :perDni, '%') 
    ORDER BY 
        p.perApellido;
    ");
        $sql->bindValue(':perDni', $_GET['perDni']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }else{
        $sql = $pdo->prepare("SELECT p.idPersona, p.perNombre, p.perApellido, p.perTelefono, p.perDomicilio, p.perMail, p.perDni, p.perContrasena, rol.idRol, rol.rolNombre FROM Personas p INNER JOIN Roles rol ON p.rolID = rol.idRol where rol.idRol = 2 ORDER BY p.perApellido");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
    }
    
}
/*
    POST
    Crear un nueva estudiante:
    Cuerpo JSON: {"ediNombre": "nuevaestudiante"}
    Retorno: ID del nueva estudiante creado.
    */
// Maneja solicitudes POST
function handlePostRequest($pdo) {
    $data = json_decode(file_get_contents("php://input"));
    // Verifica si se proporciona 'ediNombre'
    if (isset($data->ediNombre)) {
        $sql = "INSERT INTO estudiante (ediDireccion,ediEmail,ediNombre,ediTelefono,LocalidadID) 
        VALUES ((:ediDireccion), (:ediEmail),(:ediNombre),(:ediTelefono),(:LocalidadID))";
        //$sql = "INSERT INTO estudiante (ediNombre) VALUES (:ediNombre)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ediDireccion', $data->ediDireccion);
        $stmt->bindParam(':ediEmail', $data->ediEmail);
        $stmt->bindParam(':ediNombre', $data->ediNombre);
        $stmt->bindParam(':ediTelefono', $data->ediTelefono);
        $stmt->bindParam(':LocalidadID', $data->LocalidadID);

        if ($stmt->execute()) {
            $idPost = $pdo->lastInsertId();
            header("HTTP/1.1 201 Created");
            echo json_encode($idPost); // Retorna el ID de la estudiante creada
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo crear la estudiante']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;
}
/*
PUT
Actualizar un estudiante existente:
Cuerpo JSON: {"idestudiante": 1, "ediNombre": "estudianteActualizada"}
Retorno: Mensaje de actualización exitosa.

*/
// Maneja solicitudes PUT
function handlePutRequest($pdo) {
    $data = json_decode(file_get_contents("php://input"));
    // Verifica si se proporcionan 'idestudiante' y 'ediNombre'
    if (isset($data->idestudiante) && isset($data->ediNombre)) {
        $sql = "UPDATE estudiante SET ediDireccion = (:ediDireccion), ediEmail = (:ediEmail), ediNombre = (:ediNombre),
         ediTelefono = (:ediTelefono), LocalidadID = (:LocalidadID) WHERE idestudiante = (:idestudiante)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ediDireccion', $data->ediDireccion);
        $stmt->bindParam(':ediEmail', $data->ediEmail);
        $stmt->bindParam(':ediNombre', $data->ediNombre);
        $stmt->bindParam(':ediTelefono', $data->ediTelefono);
        $stmt->bindParam(':LocalidadID', $data->LocalidadID);
        $stmt->bindParam(':idestudiante', $data->idestudiante);
        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Actualización exitosa']); // Retorna un mensaje de éxito
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo actualizar la estudiante']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;
}
/*
DELETE
Eliminar un estudiante por ID (idestudiante):
Parámetro: idestudiante=1
Retorno: Mensaje de eliminación exitosa.
*/
// Maneja solicitudes DELETE
function handleDeleteRequest($pdo) {
    // Verifica si se proporciona 'idestudiante'
    if (isset($_GET['idPersona'])) {
        $sql = "DELETE FROM Personas WHERE idPersona =:idPersona";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idPersona', $_GET['idPersona']);
        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']); // Retorna un mensaje de éxito
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo eliminar el estudiante']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;
}
?>