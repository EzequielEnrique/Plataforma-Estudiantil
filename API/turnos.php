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
        $sql = $pdo->prepare("SELECT * FROM turnos WHERE idTurnos=:id");
        $sql->bindValue(':id', $_GET['id']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit;
    } else {
        if (isset($_GET['turEstado'])) {
            $sql = $pdo->prepare("SELECT * FROM turnos WHERE turEstado=:turEstado");
            $sql->bindValue(':turEstado', $_GET['turEstado']);
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_ASSOC);
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll());
            exit;
        } else {
            $sql = $pdo->prepare("SELECT * FROM turnos");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_ASSOC);
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll());
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));


    if (isset($data->turNombre) && isset($data->turnEstado)) {
        $sql = "INSERT INTO turnos (turNombre, turEstado) 
        VALUES (:turNombre, :turEstado)";

        $turNombre = $data->turNombre;
        $turEstado = $data->turEstado;
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':turNombre', $turNombre);
        $stmt->bindParam(':turEstado', $turEstado);


        if ($stmt->execute()) {
            header("HTTP/1.1 201 Created");
            echo json_encode("Turno generado correctamente");
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo crear el turno']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }

    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Obtener los datos enviados mediante la solicitud PUT
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se proporciona un ID de carrera y al menos el campo 'carNombre'
    if (isset($data['idTurno']) && isset($data['tuNombre']) && isset($data['turEstado'])) {
        $turnoId = $data['idTurno'];
        $turNombre = $data['turNombre'];
        $turEstado = $data['turEstado'];

        // Construir la consulta SQL para actualizar el nombre de la carrera
        $sql = "UPDATE turnos SET turNombre = :turNombre, turEstado = :turEstado 
        WHERE idTurnos = :idTurno";

        // Preparar la consulta
        $stmt = $pdo->prepare($sql);

        // Vincular los valores y ejecutar la consulta
        $stmt->bindParam(':turNombre', $turNombre);
        $stmt->bindParam(':turEstado', $turEstado);
        $stmt->bindParam(':idTurno', $turnoId);

        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo actualizar el turno']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }

    // Cerrar la conexión
    $pdo = null;
}

if ($_SERVER['REQUEST_METHOD'] == 'PATH') {
    // Obtener los datos enviados mediante la solicitud PUT
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se proporciona un ID de carrera y al menos el campo 'carNombre'
    if (isset($data['idTurno']) && isset($data['turEstado'])) {
        $turnoId = $data['idTurno'];
        $turEstado = $data['turEstado'];

        // Construir la consulta SQL para actualizar el nombre de la carrera
        $sql = "UPDATE turnos SET turEstado = :turEstado 
        WHERE idTurnos = :idTurno";

        // Preparar la consulta
        $stmt = $pdo->prepare($sql);

        // Vincular los valores y ejecutar la consulta
        $stmt->bindParam(':turEstado', $turEstado);
        $stmt->bindParam(':idTurno', $turnoId);

        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo actualizar el turno']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }

    // Cerrar la conexión
    $pdo = null;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['idTurno'])) {
        $sql = "DELETE FROM turnos WHERE idTurnos=:idTurno";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':idTurno', $_GET['idTurno']);

        if ($stmt->execute()) {
            header("HTTP/1.1 200 OK");
            echo json_encode(['message' => 'Eliminación exitosa']); // Retorna un mensaje de éxito
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'No se pudo eliminar el turno']);
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Entrada inválida']);
    }
    exit;
}