<?php
session_start(); // Iniciar la sesión

include 'conexion.php'; // Incluir archivo de conexión

$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alumno_id = $_POST['alumno_id'];
    $prestamo_id = $_POST['prestamo_id'];
    $monto = $_POST['pago']; // Monto especificado por el usuario
    
    // Registrar el cobro en la tabla de cobros
    $stmt = $conexion->prepare("INSERT INTO cobros (alumno_id, prestamo_id, monto, fecha_cobro, concepto) VALUES (?, ?, ?, NOW(), 'Extravio')");
    $stmt->bind_param("iid", $alumno_id, $prestamo_id, $monto);

    if ($stmt->execute()) {
        // Actualizar el estado del préstamo a 'pagado' y la fecha de devolución
        $stmt2 = $conexion->prepare("UPDATE prestamos SET estado = 'pagado', fecha_devolucion = NOW() WHERE id = ?");
        $stmt2->bind_param("i", $prestamo_id);
        $stmt2->execute();
        $stmt2->close();

        $mensaje = "Pago registrado exitosamente.";
    } else {
        $mensaje = "Error al registrar el pago: " . $conexion->error;
    }

    $stmt->close();
    header("Location: cobros.php?mensaje=" . urlencode($mensaje));
    exit();
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
        // Mostrar alerta si hay un mensaje en la URL
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const mensaje = urlParams.get('mensaje');
            if (mensaje) {
                alert(mensaje);
                window.location.href = 'cobros.php'; // Limpiar el parámetro de la URL
            }
        };
    </script>
</head>
<body>
<div class="flex-1 h-full max-w-4xl mx-auto bg-white rounded-lg shadow-xl">
    <div class="flex flex-col md:flex-row">
        <div class="h-32 md:h-auto md:w-1/2">
            <img class="object-cover w-full h-full" src="./img/Cobrar.jpg" alt="img" />
        </div>
        <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
            <div class="w-full">
                <h1 class="mb-4 text-2xl font-bold text-center text-gray-700">
                    Gestión de cobros
                </h1>
                <form method="POST" action="cobros.php">
                    <label for="alumno_id" class="block text-sm">Alumno:</label>
                    <select name="alumno_id" id="alumnoSelect" onchange="cargarPrestamosPendientes()" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Seleccione un alumno</option>
                        <?php
                        // Modificar la consulta para obtener alumnos con préstamos perdidos
                        $resultado = $conexion->query("SELECT DISTINCT a.id, a.nombre 
                                                    FROM alumnos a 
                                                    JOIN prestamos p ON a.id = p.alumno_id 
                                                    WHERE p.estado = 'perdido'");
                        while ($alumno = $resultado->fetch_assoc()) {
                            echo "<option value='{$alumno['id']}'>{$alumno['nombre']}</option>";
                        }
                        ?>
                    </select>
                    <br><br>
                    <!-- Prestamo -->
                    <label for="prestamo_id" class="block text-sm">Prestamo Pendiente:</label>
                    <select name="prestamo_id" id="prestamoSelect" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Seleccione un extravio</option>
                    </select>
                    <br><br>
                    <label for="pago" class="block text-sm">Monto a pagar:</label>
                    <input type="number" name="pago" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                    
                    <button type="submit" class="btn_cobrar">Registrar Pago</button>
                    <button onclick="window.location.href='index.html'" class="regresar">Regresar al Inicio</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function cargarPrestamosPendientes() {
    const alumnoId = document.getElementById("alumnoSelect").value;
    const prestamoSelect = document.getElementById("prestamoSelect");

    prestamoSelect.innerHTML = "<option value=''>Seleccione un extravio</option>";

    if (!alumnoId) return;

    fetch(`obtener_prestamos_pendientes.php?alumno_id=${alumnoId}`)
        .then(response => response.json())
        .then(prestamos => {
            prestamos.forEach(prestamo => {
                const option = document.createElement("option");
                option.value = prestamo.id;
                option.textContent = `Libro extraviado: ${prestamo.titulo}`; // Mostrar el título del libro
                prestamoSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error al cargar préstamos:", error));
}
</script>
</body>
</html>
