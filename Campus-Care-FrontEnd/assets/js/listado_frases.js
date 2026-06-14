document.addEventListener('DOMContentLoaded', function() {
    const botones = document.querySelectorAll('.btn-eliminar');

    botones.forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.dataset.id;

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'eliminar_frase.php?id_frase=' + id;
                }
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const boton = document.getElementById("buscarBoton");
    const campoBuscar = document.getElementById("campoBuscar");

    const params = new URLSearchParams(window.location.search);
    const valor = params.get('buscar');

    if (valor && valor.trim().length > 0) {
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