<?php
session_start(); // Iniciar la sesión

include 'conexion.php';

// Mensaje de éxito desde la sesión
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']); // Limpiar el mensaje después de mostrarlo
} else {
    $mensaje = "";
}

$libros = []; // Array para almacenar libros por alumno
$selected_alumno_id = null; // Para almacenar el id del alumno seleccionado

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alumno_id = $_POST['alumno_id'];
    $prestamo_id = $_POST['prestamo_id'];
    $tarifa = $_POST['pago']; // Tarifa por día

    // Obtener el número de días de retardo
    $query = $conexion->prepare("SELECT retardo FROM prestamos WHERE id = ?");
    $query->bind_param("i", $prestamo_id);
    $query->execute();
    $query->bind_result($retardo);
    $query->fetch();
    $query->close();

    if ($retardo > 0) {
        $monto = $retardo * $tarifa; // Calcular monto total

        // Registrar el cobro en la tabla de cobros
        $stmt = $conexion->prepare("INSERT INTO cobros (alumno_id, prestamo_id, monto, fecha_cobro, concepto) VALUES (?, ?, ?, NOW(), 'Atraso')");
        $stmt->bind_param("iid", $alumno_id, $prestamo_id, $monto);

        if ($stmt->execute()) {
            // Actualizar el estado del préstamo a 'pagado' y la fecha de devolución
            $stmt2 = $conexion->prepare("UPDATE prestamos SET estado = 'pagado', fecha_devolucion = NOW(), retardo = NULL WHERE id = ?");
            $stmt2->bind_param("i", $prestamo_id);
            $stmt2->execute();
            $stmt2->close();

            // Guardar el mensaje en la sesión
            $_SESSION['mensaje'] = "Se cobraron \$" . number_format($monto, 2) . " por retardo.";
            
            // Redirigir a la misma página
            header("Location: Retardo.php");
            exit();
        } else {
            $mensaje = "Error al registrar el pago: " . $conexion->error;
        }

        $stmt->close();
    } else {
        $mensaje = "No hay retardo para este préstamo.";
    }

    // Almacenar el alumno seleccionado para mantenerlo en el dropdown
    $selected_alumno_id = $alumno_id;

} elseif (isset($_GET['alumno_id'])) {
    $selected_alumno_id = $_GET['alumno_id']; // Guardar el alumno seleccionado desde el GET
}

// Cargar alumnos con retardo
$resultadoAlumnos = $conexion->query("SELECT DISTINCT a.id, a.nombre 
                                       FROM alumnos a 
                                       JOIN prestamos p ON a.id = p.alumno_id 
                                       WHERE p.retardo > 0");

if ($selected_alumno_id) {
    $prestamos = [];

    // Obtener préstamos del alumno seleccionado con el título del libro
    $query = $conexion->prepare("
        SELECT p.id, l.titulo, p.retardo 
        FROM prestamos p
        JOIN libros l ON p.libro_id = l.id
        WHERE p.alumno_id = ? AND p.retardo > 0
    ");
    $query->bind_param("i", $selected_alumno_id);
    $query->execute();
    $query->bind_result($id, $titulo, $retardo);

    while ($query->fetch()) {
        $prestamos[] = ['id' => $id, 'titulo' => $titulo, 'retardo' => $retardo];
    }

    $query->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestion de Cobros</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="formularios.css">
    <script>
        function cargarPrestamosPendientes() {
            const alumnoId = document.getElementById("alumnoSelect").value;
            const prestamoSelect = document.getElementById("prestamoSelect");

            // Limpiar el select de préstamos
            prestamoSelect.innerHTML = "<option value=''>Seleccione un préstamo</option>";

            if (alumnoId) {
                // Redirigir a la misma página con el alumno_id como parámetro
                window.location.href = `Retardo.php?alumno_id=${alumnoId}`;
            }
        }
    </script>
</head>
<body>
<div class="flex-1 h-full max-w-4xl mx-auto bg-white rounded-lg shadow-xl">
    <div class="flex flex-col md:flex-row">
        <div class="h-32 md:h-auto md:w-1/2">
            <img class="object-cover w-full h-full" src="./img/Retardo.jpg" alt="img" />
        </div>
        <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
            <div class="w-full">
                <h1 class="mb-4 text-2xl font-bold text-center text-gray-700">
                    Cobro por retardo
                </h1>
                <?php if ($mensaje): ?>
                    <div class="bg-green-200 border border-green-600 text-green-700 px-4 py-2 rounded mb-4">
                        <?= htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="Retardo.php">
                    <label for="alumno_id" class="block text-sm">Alumno:</label>
                    <select name="alumno_id" id="alumnoSelect" onchange="cargarPrestamosPendientes()" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Seleccione un alumno</option>
                        <?php
                        while ($alumno = $resultadoAlumnos->fetch_assoc()) {
                            // Marcar el alumno como seleccionado si coincide
                            $selected = ($alumno['id'] == $selected_alumno_id) ? 'selected' : '';
                            echo "<option value='{$alumno['id']}' {$selected}>{$alumno['nombre']}</option>";
                        }
                        ?>
                    </select>
                    <br><br>
                    <label for="prestamo_id" class="block text-sm">Libros entregados con retardo:</label>
                    <select name="prestamo_id" id="prestamoSelect" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Entregas con retardo</option>
                        <?php
                        if (isset($prestamos)) {
                            foreach ($prestamos as $prestamo) {
                                echo "<option value='{$prestamo['id']}'>Nombre del libro: {$prestamo['titulo']} - Días de Retardo: {$prestamo['retardo']}</option>";
                            }
                        }
                        ?>
                    </select>
                    <br><br>
                    <label for="pago" class="block text-sm">Tarifa por día:</label>
                    <input type="number" name="pago" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600" min="0">
                    
                    <button type="submit" class="btn_cobrar">Registrar Pago</button>
                    <button onclick="window.location.href='index.html'" class="regresar">Regresar al Inicio</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
