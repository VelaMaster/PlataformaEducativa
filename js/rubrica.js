// Mostrar rúbrica
function mostrarRubrica() {
    document.getElementById('rubricaContainer').style.display = 'block';
}

// Agregar fila a la rúbrica
function agregarFilaRubrica() {
    const tableBody = document.querySelector('#rubricaTable tbody');
    const row = document.createElement('tr');

    row.innerHTML = `
        <td><input type="text" name="criterio[]" required></td>
        <td><input type="text" name="descripcionCriterio[]" required></td>
        <td><input type="number" name="puntos[]" min="0" max="100" required oninput="actualizarTotalPuntos()"></td>
        <td><button type="button" onclick="eliminarFilaRubrica(this)">Eliminar</button></td>
    `;

    tableBody.appendChild(row);
    actualizarTotalPuntos();
}

// Eliminar fila de la rúbrica
function eliminarFilaRubrica(button) {
    const row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
    actualizarTotalPuntos(); // Recalcular puntos
}

// Actualizar el total de puntos
function actualizarTotalPuntos() {
    const puntosInputs = document.getElementsByName('puntos[]');
    let totalPuntos = 0;

    // Calcular total de puntos actuales
    puntosInputs.forEach(input => {
        totalPuntos += parseFloat(input.value) || 0;
    });

    // Verificar si excede 100 puntos
    if (totalPuntos > 100) {
        const excedente = totalPuntos - 100; // Calcular excedente
        const ultimoInput = puntosInputs[puntosInputs.length - 1]; // Último campo de puntos

        // Ajustar el último input al valor restante
        ultimoInput.value = (parseFloat(ultimoInput.value) || 0) - excedente;

        // Mostrar alerta de ajuste
        Swal.fire({
            icon: 'warning',
            title: 'Límite Excedido',
            text: `Los puntos totales no pueden exceder de 100. Se han ajustado automáticamente los puntos del último criterio.`,
            confirmButtonText: 'Entendido',
            footer: `Se ajustaron ${excedente} puntos.`
        });

        totalPuntos = 100; // Establecer total en el máximo permitido
    }

    // Actualizar total visible en la página
    document.getElementById('totalPuntos').textContent = totalPuntos;
}

// Validar total de puntos al enviar el formulario
function validarTotalPuntos() {
    const totalPuntos = parseFloat(document.getElementById('totalPuntos').textContent);
    if (totalPuntos > 100) {
        alert('El total de puntos asignados en la rúbrica no puede exceder 100.');
        return false;
    }
    return true;
}

// Actualizar total de puntos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarTotalPuntos();
});
