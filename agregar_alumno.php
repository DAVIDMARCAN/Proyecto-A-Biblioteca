<?php
include 'conexion.php';
/////////////////////////////////////////////////////////////////////////////////
$nombre = $_POST['nombre'];
$vigente = $_POST['vigente'];

// Insertar el nuevo alumno
$sql = "INSERT INTO alumnos (nombre, vigente) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $nombre, $vigente);

if ($stmt->execute()) {
////////////////////////////////////////////////////////////////////////////////////////
    header("Location: index.html?mensaje=Alumno registrado con éxito");
} else {
    echo "Error al registrar el alumno: " . $conexion->error;
}

$stmt->close();
$conexion->close();
?>
