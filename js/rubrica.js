function agregarFilaRubrica() {
    const tableBody = document.querySelector('#rubricaTable tbody');
    const row = document.createElement('tr');

    row.innerHTML = `
        <td><input type="text" name="criterio[]" required></td>
        <td><input type="text" name="descripcionCriterio[]" required></td>
        <td><input type="number" name="puntos[]" min="0" max="100" required oninput="validarPuntos(this); actualizarTotalPuntos();"></td>
        <td><input type="checkbox" name="cumple[]"></td>
        <td><input type="checkbox" name="no_cumple[]"></td>
        <td><input type="text" name="observaciones[]"></td>
        <td>
            <button type="button" class="btn btn-danger" onclick="eliminarFilaRubrica(this)">Eliminar</button>
        </td>
    `;

    tableBody.appendChild(row);
    actualizarTotalPuntos(); // Actualiza el total al agregar una nueva fila
}

function validarPuntos(input) {
    const puntosInputs = document.querySelectorAll('[name="puntos[]"]');
    let totalPuntos = 0;

    // Sumar los puntos actuales excluyendo el valor del input que se está editando
    puntosInputs.forEach(puntos => {
        if (puntos !== input) {
            totalPuntos += parseFloat(puntos.value) || 0;
        }
    });

    const valorActual = parseFloat(input.value) || 0;
    const limiteRestante = 100 - totalPuntos;

    // Si el valor excede el límite restante, ajustar el valor automáticamente
    if (valorActual > limiteRestante) {
        input.value = limiteRestante;
        Swal.fire({
            icon: 'warning',
            title: 'Límite Excedido',
            text: `No puedes asignar más de ${limiteRestante} puntos restantes.`,
            confirmButtonText: 'Entendido'
        });
    }

    actualizarTotalPuntos(); // Actualizar total después de validar
}

function actualizarTotalPuntos() {
    const puntosInputs = document.querySelectorAll('[name="puntos[]"]');
    let totalPuntos = 0;

    // Sumar los valores de todos los inputs de puntos
    puntosInputs.forEach(input => {
        totalPuntos += parseFloat(input.value) || 0;
    });

    // Actualizar el total en el elemento correspondiente
    const totalPuntosElement = document.getElementById('totalPuntos');
    totalPuntosElement.textContent = totalPuntos;

    // Validar si el total excede 100
    if (totalPuntos > 100) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El total de puntos no puede exceder los 100.',
            confirmButtonText: 'Revisar'
        });
    }
}

function eliminarFilaRubrica(button) {
    const row = button.closest('tr');
    row.remove();
    actualizarTotalPuntos(); // Recalcular el total después de eliminar una fila
}
