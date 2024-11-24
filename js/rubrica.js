function mostrarRubrica() {
    document.getElementById('rubricaContainer').style.display = 'block';
}

function agregarFilaRubrica() {
    const tableBody = document.querySelector('#rubricaTable tbody');
    const row = document.createElement('tr');

    row.innerHTML = `
        <td><input type="text" name="criterio[]" required></td>
        <td><input type="text" name="descripcionCriterio[]" required></td>
        <td><input type="number" name="puntos[]" min="0" max="100" required oninput="actualizarTotalPuntos()"></td>
        <td><button type="button" class="delete-row-button" onclick="eliminarFilaRubrica(this)">Eliminar</button></td>
    `;

    tableBody.appendChild(row);
    actualizarTotalPuntos();
}
function eliminarFilaRubrica(button) {
    const row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
    actualizarTotalPuntos(); // Recalcular puntos
}
function actualizarTotalPuntos() {
    const puntosInputs = document.getElementsByName('puntos[]');
    let totalPuntos = 0;

    puntosInputs.forEach(input => {
        totalPuntos += parseFloat(input.value) || 0;
    });

    if (totalPuntos > 100) {
        const excedente = totalPuntos - 100;
        const ultimoInput = puntosInputs[puntosInputs.length - 1];

        ultimoInput.value = (parseFloat(ultimoInput.value) || 0) - excedente;

        Swal.fire({
            icon: 'warning',
            title: 'Límite Excedido',
            text: `Los puntos totales no pueden exceder de 100. Se han ajustado automáticamente los puntos del último criterio.`,
            confirmButtonText: 'Entendido',
            footer: `Se ajustaron ${excedente} puntos.`
        });

        totalPuntos = 100;
    }

    document.getElementById('totalPuntos').textContent = totalPuntos;
}

function validarTotalPuntos() {
    const totalPuntos = parseFloat(document.getElementById('totalPuntos').textContent);
    if (totalPuntos > 100) {
        alert('El total de puntos asignados en la rúbrica no puede exceder 100.');
        return false;
    }
    return true;
}
document.addEventListener('DOMContentLoaded', function() {
    actualizarTotalPuntos();
});
