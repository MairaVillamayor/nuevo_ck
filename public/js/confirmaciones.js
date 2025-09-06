
function confirmarBaja(id) {
    Swal.fire({
        title: '¿Dar de baja el insumo?',
        text: "El insumo quedará desactivado pero no se eliminará.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ec407a',
        cancelButtonColor: '#ffb6c1',
        confirmButtonText: 'Sí, dar de baja',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-baja-' + id).submit();
        }
    });
}

function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará el insumo permanentemente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#ffb6c1',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-eliminar-' + id).submit();
        }
    });
}
