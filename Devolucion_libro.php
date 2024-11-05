
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolucion de libro</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="formularios.css">
</head>
<body>
<script>
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const mensaje = urlParams.get('mensaje');
        if (mensaje) {
            alert(mensaje); // Mostrar el mensaje en una alerta
        }
    }
</script>
<div class="flex-1 h-full max-w-4xl mx-auto bg-white rounded-lg shadow-xl">
    <div class="flex flex-col md:flex-row">
        <div class="h-32 md:h-auto md:w-1/2">
            <img class="object-cover w-full h-full" src="./img/Devolver.jpg" alt="img" />
        </div>
        <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
            <div class="w-full">
                <h1 class="mb-4 text-2xl font-bold text-center text-gray-700">
                    Devolucion de libro
                </h1>
                <form action="devolver_libro.php" method="POST">
                    <label for="alumno_id" class="block text-sm">Alumno:</label>
                    <select name="alumno_id" id="alumnoSelect" onchange="cargarLibrosPorAlumno()" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Seleccione un alumno</option>
                        <?php
                        include 'conexion.php';
                        $resultado = $conexion->query("SELECT DISTINCT alumnos.id, alumnos.nombre 
                                                        FROM prestamos 
                                                        JOIN alumnos ON prestamos.alumno_id = alumnos.id 
                                                        WHERE prestamos.estado = 'activo'");
                        while ($alumno = $resultado->fetch_assoc()) {
                            echo "<option value='{$alumno['id']}'>{$alumno['nombre']}</option>";
                        }
                        ?>
                    </select>
                    <br><br>
                    <label for="libro_id" class="block text-sm">Libro:</label>
                    <select name="libro_id" id="libroSelect" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Seleccione un libro</option>
                    </select>
                    <br><br>
                    <label for="libro_id" class="block text-sm">Fecha de entrega:</label>
                    <input type="date" name="fecha_entrega" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <br><br>
                    <label for="estado" class="block text-sm">Estado del libro:</label>
                    <select name="estado" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="1">Bueno</option>
                        <option value="0">Malo</option>
                    </select>
                    <button type="submit" class="btn_devolucion">Registrar Devolucion</button>
                    <button onclick="window.location.href='index.html'" class="regresar">Regresar al Inicio</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>