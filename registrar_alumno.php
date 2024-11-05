<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $vigente = $_POST['vigente'];

    $stmt = $conexion->prepare("INSERT INTO alumnos (nombre, vigente) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $vigente);

    if ($stmt->execute()) {
        $mensaje = "Alumno registrado exitosamente.";
    } else {
        $mensaje = "Error al registrar el alumno: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();

    session_start();
    $_SESSION['mensaje'] = $mensaje;
    header("Location: registrar_alumno.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Alumno</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="formularios.css">
    <script>
        // Función para mostrar la alerta y redirigir después de aceptar
        function mostrarAlerta(mensaje) {
            alert(mensaje);
            window.location.href = 'index.html'; // Redirige a index.html después de aceptar
        }
    </script>
</head>
<body>
    <?php 
    session_start();
    if (isset($_SESSION['mensaje'])): ?>
        <script>
            // Llama a la función para mostrar la alerta con el mensaje
            mostrarAlerta("<?php echo htmlspecialchars($_SESSION['mensaje']); ?>");
        </script>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    
    <div class="flex-1 h-full max-w-4xl mx-auto bg-white rounded-lg shadow-xl">
        <div class="flex flex-col md:flex-row">
            <div class="h-32 md:h-auto md:w-1/2">
                <img class="object-cover w-full h-full" src="./img/Estudiante.jpg" alt="img" />
            </div>
            <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
                <div class="w-full">
                    <h1 class="mb-4 text-2xl font-bold text-center text-gray-700">
                        Registrar Alumno
                    </h1>
                    <form action="registrar_alumno.php" method="POST">
                        <label for="nombre" class="block text-sm">Nombre del Alumno:</label>
                        <input type="text" name="nombre" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <br><br>
                        <label for="vigente" class="block text-sm">¿Vigente?</label>
                        <select name="vigente" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>

                        <button type="submit" class="btn_alumno">Registrar Alumno</button>
                        <button onclick="window.location.href='index.html'" class="regresar">Regresar al Inicio</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
