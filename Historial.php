<?php
include 'conexion.php';

// Consulta para obtener los cobros
$cobrosQuery = "
    SELECT c.id, a.nombre AS alumno, l.titulo AS libro, c.monto, c.fecha_cobro, c.concepto
    FROM cobros c
    JOIN alumnos a ON c.alumno_id = a.id
    JOIN prestamos p ON c.prestamo_id = p.id
    JOIN libros l ON p.libro_id = l.id
";

$cobrosResult = $conexion->query($cobrosQuery);

// Consulta para obtener las deudas
$deudasQuery = "
    SELECT a.nombre AS alumno, l.titulo AS libro, p.retardo
    FROM prestamos p
    JOIN alumnos a ON p.alumno_id = a.id
    JOIN libros l ON p.libro_id = l.id
    WHERE p.retardo IS NOT NULL AND p.retardo > 0
";

$deudasResult = $conexion->query($deudasQuery);

// Consulta para obtener los extravíos
$extraviosQuery = "
    SELECT a.nombre AS alumno, l.titulo AS libro, p.fecha_prestamo
    FROM prestamos p
    JOIN alumnos a ON p.alumno_id = a.id
    JOIN libros l ON p.libro_id = l.id
    WHERE p.estado = 'perdido'
";

$extraviosResult = $conexion->query($extraviosQuery);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cobros, Deudas y Extravíos</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .cobro {
            color: lightgreen;
        }
        .deuda {
            color: red;
        }
        .extravio {
            color: orange;
        }
    </style>
</head>
<body>

<h2>Historial de Cobros</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Alumno</th>
            <th>Libro</th>
            <th>Monto</th>
            <th>Fecha de Cobro</th>
            <th>Concepto</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($cobrosResult->num_rows > 0): ?>
            <?php while($row = $cobrosResult->fetch_assoc()): ?>
                <tr class="cobro">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['alumno']; ?></td>
                    <td><?php echo $row['libro']; ?></td>
                    <td><?php echo number_format($row['monto'], 2); ?></td>
                    <td><?php echo $row['fecha_cobro']; ?></td>
                    <td><?php echo $row['concepto']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No hay registros de cobros.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>Deudas</h2>
<table>
    <thead>
        <tr>
            <th>Alumno</th>
            <th>Libro</th>
            <th>Días de Retardo</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($deudasResult->num_rows > 0): ?>
            <?php while($row = $deudasResult->fetch_assoc()): ?>
                <tr class="deuda">
                    <td><?php echo $row['alumno']; ?></td>
                    <td><?php echo $row['libro']; ?></td>
                    <td><?php echo $row['retardo']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No hay deudas registradas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>Extravíos</h2>
<table>
    <thead>
        <tr>
            <th>Alumno</th>
            <th>Libro</th>
            <th>Fecha de Préstamo</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($extraviosResult->num_rows > 0): ?>
            <?php while($row = $extraviosResult->fetch_assoc()): ?>
                <tr class="extravio">
                    <td><?php echo $row['alumno']; ?></td>
                    <td><?php echo $row['libro']; ?></td>
                    <td><?php echo $row['fecha_prestamo']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No hay extravíos registrados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php
$conexion->close();
?>
