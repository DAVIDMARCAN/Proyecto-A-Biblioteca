function cargarLibrosPorAlumno() {
    const alumnoId = document.getElementById("alumnoSelect").value;
    const libroSelect = document.getElementById("libroSelect");

    // Limpiar la lista de libros
    libroSelect.innerHTML = "<option value=''>Seleccione un libro</option>";

    // Solo continuar si se selecciona un alumno
    if (!alumnoId) return;

    // Crear solicitud para obtener libros de ese alumno
    fetch(`obtener_libros.php?alumno_id=${alumnoId}`)
        .then(response => response.json())
        .then(libros => {
            libros.forEach(libro => {
                const option = document.createElement("option");
                option.value = libro.id;
                option.textContent = libro.titulo;
                libroSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error al cargar libros:", error));
}

function cargarLibrosPorAlumnoExtravio() {
    const alumnoId = document.getElementById("alumnoSelectExtravio").value;
    const libroSelect = document.getElementById("libroSelectExtravio");

    // Limpiar la lista de libros
    libroSelect.innerHTML = "<option value=''>Seleccione un libro</option>";

    // Solo continuar si se selecciona un alumno
    if (!alumnoId) return;

    // Crear solicitud para obtener libros de ese alumno
    fetch(`obtener_libros.php?alumno_id=${alumnoId}`)
        .then(response => response.json())
        .then(libros => {
            libros.forEach(libro => {
                const option = document.createElement("option");
                option.value = libro.id;
                option.textContent = libro.titulo;
                libroSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error al cargar libros:", error));
}

function obtenerParametroURL(nombre) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(nombre);
}

const mensaje = obtenerParametroURL("mensaje");
if (mensaje) {
    alert(mensaje);
    history.replaceState(null, "", window.location.pathname);
}
