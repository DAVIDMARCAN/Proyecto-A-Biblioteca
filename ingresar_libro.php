<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de Libros</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="formularios.css">
</head>
<body>
<div class="flex-1 h-full max-w-4xl mx-auto bg-white rounded-lg shadow-xl">
    <div class="flex flex-col md:flex-row">
        <div class="h-32 md:h-auto md:w-1/2">
            <img class="object-cover w-full h-full" src="./img/LibroNuevo.jpg" alt="img" />
        </div>
        <div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
            <div class="w-full">
                <h1 class="mb-4 text-2xl font-bold text-center text-gray-700">
                    Registro de Nuevo Libro
                </h1>
                <form action="procesar_libro.php" method="POST">
                    <label for="titulo" class="block text-sm">Título del Libro:</label>
                    <input type="text" name="titulo" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">

                    <label for="autor" class="block text-sm">Autor:</label>
                    <input type="text" name="autor" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">

                    <label for="genero" class="block text-sm">Género:</label>
                    <input type="text" name="genero" required class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">

                    <label for="ejemplares" class="block text-sm">Número de Ejemplares:</label>
                    <input type="number" name="ejemplares" required min="1" class="w-full px-4 py-2 text-sm border rounded-md focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-600">

                    <button type="submit" class="btn_nuevo">Agregar Libro</button>
                    <button onclick="window.location.href='index.html'" class="regresar">Regresar al Inicio</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

