<?php
include 'conexion.php';

$alumno_id = $_GET['alumno_id'];

$resultado = $conexion->prepare("
    SELECT p.id, l.titulo 
    FROM prestamos p
    JOIN libros l ON p.libro_id = l.id
    WHERE p.alumno_id = ? AND p.estado = 'perdido'
");
$resultado->bind_param("i", $alumno_id);
$resultado->execute();
$resultado = $resultado->get_result();

$prestamos = [];
while ($prestamo = $resultado->fetch_assoc()) {
    $prestamos[] = $prestamo;
}

echo json_encode($prestamos);
?>
