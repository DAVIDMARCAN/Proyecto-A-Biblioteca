<?php
include 'conexion.php';

$alumno_id = $_GET['alumno_id'];

// Obtener libros en prestamo activos para el alumno seleccionado
$resultado = $conexion->query("SELECT libros.id, libros.titulo 
                               FROM prestamos 
                               JOIN libros ON prestamos.libro_id = libros.id 
                               WHERE prestamos.alumno_id = $alumno_id AND prestamos.estado = 'activo'");

$libros = [];
while ($libro = $resultado->fetch_assoc()) {
    $libros[] = $libro;
}

echo json_encode($libros);
?>
