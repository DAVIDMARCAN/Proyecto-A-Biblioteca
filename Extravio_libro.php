<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extravio de libros</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="formularios.css">
</head>
<body>
<div class="flex-1 h-full max-w-4xl mx-auto bg-white rounded-lg shadow-xl">
    <div class="flex flex-col md:flex-row">
        <div class="h-32 md:h-auto md:w-1/2">
            <img class="object-cover w-full h-full" src="./img/Extravio.jpg" alt="img" />
        </div>
        <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
            <div class="w-full">
                <h1 class="mb-4 text-2xl font-bold text-center text-gray-700">
                    Registrar Exravio de un Libro
                </h1>
                <form action="registrar_perdida.php" method="POST">
                    <label for="alumno_id" class="block text-sm">Alumno:</label>
                    <select name="alumno_id" id="alumnoSelectExtravio" onchange="cargarLibrosPorAlumnoExtravio()" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Seleccione un alumno</option>
                        <?php
                        include 'conexion.php';
                        $resultado = $conexion->query("SELECT id, nombre FROM alumnos WHERE vigente = 1");
                        while ($alumno = $resultado->fetch_assoc()) {
                            echo "<option value='{$alumno['id']}'>{$alumno['nombre']}</option>";
                        }
                        ?>
                    </select>
                    <br><br>
                    <label for="libro_id" class="block text-sm">Libro:</label>
                    <select name="libro_id" id="libroSelectExtravio" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Seleccione un libro</option>
                    </select>

                    <button type="submit" class="btn_extravio">Registrar Extravio</button>
                    <button onclick="window.location.href='index.html'" class="regresar">Regresar al Inicio</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>