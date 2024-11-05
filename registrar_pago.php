<?php
if (isset($_POST['prestamo_id'])) {
    $prestamo_id = $_POST['prestamo_id'];
    $monto_pagado = $_POST['monto_pagado'];

    // Conexion a la base de datos
    include 'conexion.php';

    // Consultar el prestamo para verificar si tiene una multa
    $sql = "SELECT * FROM prestamos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $prestamo = $resultado->fetch_assoc();

        // Calcular la multa (50 claudolares por día de retraso)
        $fecha_prestamo = new DateTime($prestamo['fecha_prestamo']);
        $hoy = new DateTime();
        $dias_transcurridos = $hoy->diff($fecha_prestamo)->days;
        $dias_de_retraso = max(0, $dias_transcurridos - 7); // Se da un plazo de 7 días

        $multa_total = $dias_de_retraso * 50; // 50 claudolares por día de retraso

        
        if ($monto_pagado >= $multa_total) {
            // Actualizar el estado del préstamo a 'devuelto' y la fecha de devolución
            $sql_update = "UPDATE prestamos SET estado = 'devuelto', fecha_devolucion = NOW() WHERE id = ?";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("i", $prestamo_id);
            $stmt_update->execute();

            echo "<script>alert('Pago registrado y libro marcado como devuelto.'); window.location.href = 'cobros.php';</script>";
        } else {
            echo "<script>alert('El monto no cubre la multa total.'); window.location.href = 'cobros.php';</script>";
        }
    } else {
        echo "<script>alert('Prestamo no encontrado.'); window.location.href = 'cobros.php';</script>";
    }

    $conexion->close();
}
?>
