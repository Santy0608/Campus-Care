document.addEventListener('DOMContentLoaded', function() {
    const boton = document.getElementById("buscarBoton");
    const campoBuscar = document.getElementById("campoBuscar");

    const urlParams = new URLSearchParams(window.location.search);
    const valorBusqueda = urlParams.get('buscar');

    if (valorBusqueda && valorBusqueda.trim().length > 0) {
        boton.textContent = "Reestablecer";
    }

    boton.addEventListener('click', function(e) {
        if (boton.textContent.trim() === "Reestablecer") {
            e.preventDefault();
            campoBuscar.value = "";
            window.location.href = window.location.pathname;
        }
    });
});