<?php
if (isset($_POST['titulo']) && isset($_POST['autor']) && isset($_POST['genero']) && isset($_POST['ejemplares'])) {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $genero = $_POST['genero'];
    $ejemplares = $_POST['ejemplares'];


    include 'conexion.php';

    // Insertar el nuevo libro en la base de datos
    $sql = "INSERT INTO libros (titulo, autor, genero, ejemplares) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $autor, $genero, $ejemplares);

    if ($stmt->execute()) {
        echo "<script>alert('Libro registrado exitosamente.'); window.location.href = 'ingresar_libro.php';</script>";
    } else {
        echo "<script>alert('Error al registrar el libro.'); window.location.href = 'ingresar_libro.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
