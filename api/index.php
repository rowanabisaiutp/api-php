<?php
// Configuración de la conexión a la base de datos
$servername = "b5csg5yxkxhmm54nb0ac-mysql.services.clever-cloud.com";
$username = "ulxjdpc2g5rz5ri9";
$password = "5do23K3iLKdlfSN0raHW";
$dbname = "b5csg5yxkxhmm54nb0ac";

// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");

// Permitir los métodos HTTP especificados
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// Permitir ciertos encabezados HTTP
header("Access-Control-Allow-Headers: Content-Type");

// Permitir que las cookies sean enviadas desde el cliente al servidor
header("Access-Control-Allow-Credentials: true");

// Establecer la duración máxima de la caché para los resultados preflight (opcional)
header("Access-Control-Max-Age: 3600");

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar el método de la solicitud HTTP
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Obtener todos los registros de la tabla pc
    $sql_select = "SELECT * FROM pc";
    $result_select = $conn->query($sql_select);

    // Verificar si hay resultados y mostrarlos
    if ($result_select->num_rows > 0) {
        // Inicializar un array para almacenar los registros
        $registros = array();

        // Iterar sobre cada fila de resultados
        while($row = $result_select->fetch_assoc()) {
            // Agregar el registro al array
            $registros[] = $row;
        }

        // Mostrar los registros en formato JSON
        echo json_encode($registros);
    } else {
        // Si no hay resultados, mostrar un mensaje de error
        echo "No se encontraron registros.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos enviados en la solicitud POST
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Verificar si se recibieron los datos correctamente
    if ($data) {
        // Obtener los valores de los datos recibidos
        $nombre = $data['nombre'];
        $modelo = $data['modelo'];
        $nserie = $data['nserie'];
        $teclado = $data['teclado'];
        $mouse = $data['mouse'];
        $observacion = $data['observacion'];
        $estado_id = $data['estado_id'];
        $mesa_id = $data['mesa_id'];

        // Consulta SQL para insertar un nuevo registro
        $sql_insert = "INSERT INTO pc (nombre, modelo, nserie, teclado, mouse, observacion, estado_id, mesa_id) 
                       VALUES ('$nombre', '$modelo', '$nserie', $teclado, $mouse, '$observacion', $estado_id, $mesa_id)";
        
        // Ejecutar la consulta de inserción
        if ($conn->query($sql_insert) === TRUE) {
            echo "Registro insertado correctamente.";
        } else {
            echo "Error al insertar el registro: " . $conn->error;
        }
    } else {
        echo "Error al recibir los datos.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
    // Obtener los datos enviados en la solicitud PUT
    parse_str(file_get_contents("php://input"), $data);
    
    // Verificar si se recibieron los datos correctamente
    if ($data && isset($data['id'])) {
        // Obtener el ID del registro a actualizar
        $id = $data['id'];

        // Verificar y asignar los valores de los datos recibidos
        $nombre = isset($data['nombre']) ? $data['nombre'] : '';
        $modelo = isset($data['modelo']) ? $data['modelo'] : '';
        $nserie = isset($data['nserie']) ? $data['nserie'] : '';
        $teclado = isset($data['teclado']) ? $data['teclado'] : 0;
        $mouse = isset($data['mouse']) ? $data['mouse'] : 0;
        $observacion = isset($data['observacion']) ? $data['observacion'] : '';
        $estado_id = isset($data['estado_id']) ? $data['estado_id'] : 0;
        $mesa_id = isset($data['mesa_id']) ? $data['mesa_id'] : 0;

        // Consulta SQL para actualizar el registro
        $sql_update = "UPDATE pc 
                       SET nombre='$nombre', modelo='$modelo', nserie='$nserie', teclado=$teclado, mouse=$mouse, observacion='$observacion', estado_id=$estado_id, mesa_id=$mesa_id
                       WHERE id=$id";
        
        // Ejecutar la consulta de actualización
        if ($conn->query($sql_update) === TRUE) {
            echo "Registro actualizado correctamente.";
        } else {
            echo "Error al actualizar el registro: " . $conn->error;
        }
    } else {
        echo "Error al recibir los datos.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    // Verificar si se ha enviado un ID para eliminar
    if(isset($_GET['id'])) {
        // Obtener el ID del parámetro GET
        $id = $_GET['id'];
        
        // Consulta SQL para eliminar el registro con el ID proporcionado
        $sql_delete = "DELETE FROM pc WHERE id = $id";
        
        // Ejecutar la consulta de eliminación
        if ($conn->query($sql_delete) === TRUE) {
            echo "Registro eliminado correctamente.";
        } else {
            echo "Error al eliminar el registro: " . $conn->error;
        }
    } else {
        echo "No se proporcionó un ID para eliminar.";
    }
} else {
    echo "Método HTTP no permitido.";
}

// Cerrar la conexión
$conn->close();
?>
