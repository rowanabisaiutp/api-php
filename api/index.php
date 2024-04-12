<?php
$host = "b5csg5yxkxhmm54nb0ac-mysql.services.clever-cloud.com";
$username = "ulxjdpc2g5rz5ri9";
$password = "5do23K3iLKdlfSN0raHW";
$dbname = "b5csg5yxkxhmm54nb0ac";

try {
    // Crear una nueva instancia de PDO (PHP Data Objects)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Configurar el modo de error para que PDO lance excepciones en caso de errores
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Opcional: Configurar el juego de caracteres a UTF-8
    $pdo->exec("set names utf8");

    // Permitir solicitudes desde cualquier origen
    header("Access-Control-Allow-Origin: *");

    // Permitir los métodos GET, POST, PUT, DELETE y OPTIONS
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    // Permitir los encabezados de solicitud Authorization y Content-Type
    header("Access-Control-Allow-Headers: Authorization, Content-Type");

    // Permitir que el navegador envíe cookies con la solicitud
    header("Access-Control-Allow-Credentials: true");

    // Verificar si la solicitud es de tipo OPTIONS (preflight)
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Verificar si se proporcionó un ID en la URL
        if(isset($_GET['id'])) {
            $id = $_GET['id'];

            // Consulta SQL para seleccionar un registro por su ID
            $sql = "SELECT pc.id, pc.nombre, pc.modelo, pc.nserie, pc.teclado, pc.mouse, pc.observacion, estado.id as estado_id, estado.estado, mesa.id as mesa_id, mesa.numero_mesa
                    FROM pc 
                    INNER JOIN estado ON pc.estado_id = estado.id 
                    LEFT JOIN mesa ON pc.mesa_id = mesa.id
                    WHERE pc.id = :id";
            
            // Preparar la consulta
            $stmt = $pdo->prepare($sql);
            
            // Vincular el parámetro :id
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Obtener el resultado como un array asociativo
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Comprobar si se encontró el registro
            if ($result) {
                // Convertir a formato JSON y enviar la respuesta
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                // Enviar respuesta de error si no se encontró el registro
                header('HTTP/1.1 404 Not Found');
                echo json_encode(array('message' => 'Registro no encontrado'));
            }
        } else {
            // Consulta SQL para recuperar todos los registros
            $sql = "SELECT pc.id, pc.nombre, pc.modelo, pc.nserie, pc.teclado, pc.mouse, pc.observacion, estado.id as estado_id, estado.estado, mesa.id as mesa_id, mesa.numero_mesa
                    FROM pc 
                    INNER JOIN estado ON pc.estado_id = estado.id 
                    LEFT JOIN mesa ON pc.mesa_id = mesa.id";
            
            // Preparar la consulta
            $stmt = $pdo->prepare($sql);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Obtener los resultados como un array asociativo
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Comprobar si se encontraron resultados
            if ($results) {
                // Convertir a formato JSON y enviar la respuesta
                header('Content-Type: application/json');
                echo json_encode($results);
            } else {
                // Enviar respuesta de error si no se encontraron resultados
                header('HTTP/1.1 404 Not Found');
                echo json_encode(array('message' => 'No se encontraron registros'));
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Leer el cuerpo de la solicitud JSON
        $json_data = file_get_contents('php://input');
        
        // Decodificar el JSON en un array asociativo
        $pcs_data = json_decode($json_data, true);
        
        // Insertar los datos en la base de datos
        $sql = "INSERT INTO pc (nombre, modelo, nserie, teclado, mouse, observacion, estado_id, mesa_id) VALUES (:nombre, :modelo, :nserie, :teclado, :mouse, :observacion, :estado_id, :mesa_id)";
        
        // Preparar la consulta
        $stmt = $pdo->prepare($sql);
        
        // Vincular los parámetros
        $stmt->bindParam(':nombre', $pcs_data['nombre']);
        $stmt->bindParam(':modelo', $pcs_data['modelo']);
        $stmt->bindParam(':nserie', $pcs_data['nserie']);
        $stmt->bindParam(':teclado', $pcs_data['teclado']);
        $stmt->bindParam(':mouse', $pcs_data['mouse']);
        $stmt->bindParam(':observacion', $pcs_data['observacion']);
        $stmt->bindParam(':estado_id', $pcs_data['estado_id']);
        $stmt->bindParam(':mesa_id', $pcs_data['mesa_id']);
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Enviar respuesta de éxito
        header('HTTP/1.1 201 Created');
        echo json_encode(array('message' => 'Registro creado exitosamente'));
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Leer el cuerpo de la solicitud JSON
        $json_data = file_get_contents('php://input');
        
        // Decodificar el JSON en un array asociativo
        $pcs_data = json_decode($json_data, true);
        
        // Verificar si se proporcionó un ID en el cuerpo de la solicitud
        if(isset($pcs_data['id'])) {
            $id = $pcs_data['id'];

            // Actualizar los datos en la base de datos
            $sql = "UPDATE pc SET nombre = :nombre, modelo = :modelo, nserie = :nserie, teclado = :teclado, mouse = :mouse, observacion = :observacion, estado_id = :estado_id, mesa_id = :mesa_id WHERE id = :id";
            
            // Preparar la consulta
            $stmt = $pdo->prepare($sql);
            
            // Vincular los parámetros
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $pcs_data['nombre']);
            $stmt->bindParam(':modelo', $pcs_data['modelo']);
            $stmt->bindParam(':nserie', $pcs_data['nserie']);
            $stmt->bindParam(':teclado', $pcs_data['teclado']);
            $stmt->bindParam(':mouse', $pcs_data['mouse']);
            $stmt->bindParam(':observacion', $pcs_data['observacion']);
            $stmt->bindParam(':estado_id', $pcs_data['estado_id']);
            $stmt->bindParam(':mesa_id', $pcs_data['mesa_id']);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Comprobar si se actualizó el registro
            if ($stmt->rowCount() > 0) {
                // Enviar respuesta de éxito
                header('HTTP/1.1 200 OK');
                echo json_encode(array('message' => 'Registro actualizado exitosamente'));
            } else {
                // Enviar respuesta de error si no se encontró el registro
                header('HTTP/1.1 404 Not Found');
                echo json_encode(array('message' => 'Registro no encontrado'));
            }
        } else {
            // Enviar respuesta de error si no se proporcionó un ID
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(array('message' => 'Se debe proporcionar un ID'));
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Verificar si se proporcionó un ID en la URL
        if(isset($_GET['id'])) {
            $id = $_GET['id'];

            // Consulta SQL para eliminar un registro por su ID
            $sql = "DELETE FROM pc WHERE id = :id";
            
            // Preparar la consulta
            $stmt = $pdo->prepare($sql);
            
            // Vincular el parámetro :id
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Comprobar si se eliminó el registro
            if ($stmt->rowCount() > 0) {
                // Enviar respuesta de éxito
                header('HTTP/1.1 200 OK');
                echo json_encode(array('message' => 'Registro eliminado exitosamente'));
            } else {
                // Enviar respuesta de error si no se encontró el registro
                header('HTTP/1.1 404 Not Found');
                echo json_encode(array('message' => 'Registro no encontrado'));
            }
        } else {
            // Enviar respuesta de error si no se proporcionó un ID
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(array('message' => 'Se debe proporcionar un ID'));
        }
    } else {
        // Enviar respuesta de error si el método no es GET, POST, PUT o DELETE
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode(array('message' => 'Método no permitido'));
    }

} catch(PDOException $e) {
    // En caso de error en la conexión, mostrar el mensaje de error
    echo "Error de conexión: " . $e->getMessage();
}
?>
