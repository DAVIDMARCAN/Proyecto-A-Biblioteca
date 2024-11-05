<?php
session_start(); // Iniciar la sesión
include 'conexion.php';
// Obtener el ID del alumno
$alumno_id = isset($_GET['alumno_id']) ? (int)$_GET['alumno_id'] : 0;

if ($alumno_id > 0) {
    $query = $conexion->prepare("SELECT p.id, l.titulo, p.retardo 
                                  FROM prestamos p 
                                  JOIN libros l ON p.libro_id = l.id 
                                  WHERE p.alumno_id = ? AND p.retardo > 0");
    $query->bind_param("i", $alumno_id);
    $query->execute();
    $result = $query->get_result();

    $prestamos = [];
    while ($row = $result->fetch_assoc()) {
        $prestamos[] = $row;
    }
    $query->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prestamos Pendientes</title>
</head>
<body>
<h2>Prestamos Pendientes</h2>
<?php if (!empty($prestamos)): ?>
    <ul>
        <?php foreach ($prestamos as $prestamo): ?>
            <li>ID: <?= $prestamo['id'] ?> - Título: <?= $prestamo['titulo'] ?> - Días de retardo: <?= $prestamo['retardo'] ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No hay préstamos pendientes.</p>
<?php endif; ?>
<a href="Retardo.php">Regresar</a>
</body>
</html>
