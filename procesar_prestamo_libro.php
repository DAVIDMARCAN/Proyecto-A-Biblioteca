<?php
if (isset($_POST['alumno_id']) && isset($_POST['libro_id']) && isset($_POST['fecha_devolucion'])) {
    $alumno_id = $_POST['alumno_id'];
    $libro_id = $_POST['libro_id'];
    $fecha_devolucion = $_POST['fecha_devolucion'];

    include 'conexion.php';

    // Verificar si el alumno está vigente
    $sql_verificar_vigencia = "SELECT vigente FROM alumnos WHERE id = ?";
    $stmt = $conexion->prepare($sql_verificar_vigencia);
    $stmt->bind_param("i", $alumno_id);
    $stmt->execute();
    $stmt->bind_result($vigente);
    $stmt->fetch();
    $stmt->close();

    if (!$vigente) {
        echo "<script>alert('El alumno no está vigente y no puede realizar préstamos.'); window.location.href = 'index.html';</script>";
        $conexion->close();
        exit();
    }

    // Verificar el número de préstamos activos del alumno
    $sql_prestamos_activos = "SELECT COUNT(*) FROM prestamos WHERE alumno_id = ? AND fecha_devolucion IS NULL";
    $stmt = $conexion->prepare($sql_prestamos_activos);
    $stmt->bind_param("i", $alumno_id);
    $stmt->execute();
    $stmt->bind_result($prestamos_activos);
    $stmt->fetch();
    $stmt->close();

    if ($prestamos_activos >= 3) {
        echo "<script>alert('El alumno ya tiene el máximo de 3 préstamos activos.'); window.location.href = 'index.html';</script>";
        $conexion->close();
        exit();
    }

    // Verificar si ya tiene este libro prestado y si el estado es activo
    $sql_verificar_libro_prestado = "SELECT COUNT(*) FROM prestamos WHERE alumno_id = ? AND libro_id = ? AND estado = 'activo' AND (retardo IS NULL OR retardo > 0)";
    $stmt = $conexion->prepare($sql_verificar_libro_prestado);
    $stmt->bind_param("ii", $alumno_id, $libro_id);
    $stmt->execute();
    $stmt->bind_result($libro_prestado);
    $stmt->fetch();
    $stmt->close();

    if ($libro_prestado > 0) {
        echo "<script>alert('Este libro ya está prestado a este alumno'); window.location.href = 'index.html';</script>";
        $conexion->close();
        exit();
    }
    
    // Verificar el número de préstamos activos del alumno
    $sql_prestamos_activos = "SELECT COUNT(*) FROM prestamos WHERE alumno_id = ? AND estado = 'activo' AND (retardo IS NULL OR retardo >= 0)";
    $stmt = $conexion->prepare($sql_prestamos_activos);
    $stmt->bind_param("i", $alumno_id);
    $stmt->execute();
    $stmt->bind_result($prestamos_activos);
    $stmt->fetch();
    $stmt->close();

    if ($prestamos_activos >= 3) {
        echo "<script>alert('El alumno ya tiene el máximo de 3 préstamos activos.'); window.location.href = 'index.html';</script>";
        $conexion->close();
        exit();
    }

    // Verificar si hay ejemplares disponibles del libro
    $sql_verificar_ejemplares = "SELECT ejemplares FROM libros WHERE id = ?";
    $stmt = $conexion->prepare($sql_verificar_ejemplares);
    $stmt->bind_param("i", $libro_id);
    $stmt->execute();
    $stmt->bind_result($ejemplares);
    $stmt->fetch();
    $stmt->close();

    if ($ejemplares <= 0) {
        echo "<script>alert('No hay ejemplares disponibles para este libro.'); window.location.href = 'index.html';</script>";
        $conexion->close();
        exit();
    }

    // Registrar el préstamo con la fecha de devolución
    $fecha_prestamo = date("Y-m-d");
    $sql_prestamo = "INSERT INTO prestamos (alumno_id, libro_id, fecha_prestamo, fecha_devolucion, estado) VALUES (?, ?, ?, ?, 'activo')";
    $stmt = $conexion->prepare($sql_prestamo);
    $stmt->bind_param("iiss", $alumno_id, $libro_id, $fecha_prestamo, $fecha_devolucion);

    if ($stmt->execute()) {
        // Actualizar el número de ejemplares del libro
        $sql_actualizar_ejemplares = "UPDATE libros SET ejemplares = ejemplares - 1 WHERE id = ?";
        $stmt = $conexion->prepare($sql_actualizar_ejemplares);
        $stmt->bind_param("i", $libro_id);
        $stmt->execute();

        echo "<script>alert('Préstamo registrado exitosamente.'); window.location.href = 'index.html';</script>";
    } else {
        echo "<script>alert('Error al registrar el préstamo.'); window.location.href = 'index.html';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
