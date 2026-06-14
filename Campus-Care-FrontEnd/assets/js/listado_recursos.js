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
                    window.location.href = 'eliminar_recurso.php?id_recurso=' + id;
                }
            });
        });
    });
});