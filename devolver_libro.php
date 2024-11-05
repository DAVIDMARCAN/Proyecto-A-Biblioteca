<?php
include 'conexion.php';

// Obtener los datos enviados desde el formulario
$alumno_id = $_POST['alumno_id'];
$libro_id = $_POST['libro_id'];
$fecha_entrega = $_POST['fecha_entrega']; // La fecha que el usuario ingresa
$estado_libro = $_POST['estado']; // Estado del libro enviado desde el formulario

// Verificar que los datos no estén vacíos
if (!empty($alumno_id) && !empty($libro_id)) {
    // Obtener la fecha de devolución actual
    $sql_fecha = "SELECT fecha_devolucion FROM prestamos WHERE alumno_id = ? AND libro_id = ? AND estado = 'activo'";
    $stmt_fecha = $conexion->prepare($sql_fecha);
    $stmt_fecha->bind_param("ii", $alumno_id, $libro_id);
    $stmt_fecha->execute();
    $resultado_fecha = $stmt_fecha->get_result();
    
    if ($resultado_fecha->num_rows > 0) {
        $row = $resultado_fecha->fetch_assoc();
        $fecha_devolucion_actual = $row['fecha_devolucion'];

        // Obtener el nombre del alumno
        $sql_nombre = "SELECT nombre FROM alumnos WHERE id = ?";
        $stmt_nombre = $conexion->prepare($sql_nombre);
        $stmt_nombre->bind_param("i", $alumno_id);
        $stmt_nombre->execute();
        $resultado_nombre = $stmt_nombre->get_result();
        $nombre_alumno = $resultado_nombre->fetch_assoc()['nombre'];

        // Comparar las fechas
        if ($fecha_entrega < $fecha_devolucion_actual) {
            // Si la fecha de entrega es antes de la fecha de devolución
            // Actualizar la fecha de devolución
            $sql_update = "UPDATE prestamos SET fecha_devolucion = ?, estado = 'devuelto', retardo = 0 WHERE alumno_id = ? AND libro_id = ? AND estado = 'activo'";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("sii", $fecha_entrega, $alumno_id, $libro_id);
            $dias_retorno = 0; // Sin retraso
        } else {
            // Si hay retardo
            $dias_retorno = (strtotime($fecha_entrega) - strtotime($fecha_devolucion_actual)) / (60 * 60 * 24); // Calcular los días de retardo
            
            // Actualizar el estado y la fecha de devolución
            $sql_update = "UPDATE prestamos SET fecha_devolucion = ?, estado = 'retardo', retardo = ? WHERE alumno_id = ? AND libro_id = ? AND estado = 'activo'";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("siii", $fecha_entrega, $dias_retorno, $alumno_id, $libro_id);
        }

        // Ejecutar la actualización
        if ($stmt_update->execute()) {
            // Actualizar el número de ejemplares del libro al devolverlo
            if ($estado_libro == 1) { // Estado Bueno
                $sql_update_libro = "UPDATE libros SET ejemplares = ejemplares + 1 WHERE id = ?";
            } else { // Estado Malo
                $sql_update_libro = "UPDATE libros SET ejemplares = ejemplares + 0 WHERE id = ?";
            }

            $stmt_libro = $conexion->prepare($sql_update_libro);
            $stmt_libro->bind_param("i", $libro_id);
            $stmt_libro->execute();
            
            // Redirigir con mensaje de éxito
            if ($dias_retorno > 0) {
                // Redirigir a la página principal con el mensaje de retraso
                header("Location: Devolucion_libro.php?mensaje=El alumno $nombre_alumno tiene $dias_retorno días de retraso");
                exit();
            } else {
                // Redirigir normalmente
                header("Location: Devolucion_libro.php?mensaje=Devolución registrada exitosamente");
                exit();
            }
        } else {
            echo "Error al registrar la devolución: " . $stmt_update->error;
        }
    } else {
        echo "No se encontró un préstamo activo para el alumno y libro seleccionados.";
    }

    $stmt_fecha->close();
    $stmt_nombre->close();
    $stmt_update->close();
} else {
    echo "Por favor, seleccione un alumno y un libro.";
}

$conexion->close();
?>
