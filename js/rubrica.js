function mostrarRubrica() {
    document.getElementById('rubricaContainer').style.display = 'block';
}

function agregarFilaRubrica() {
    const tableBody = document.querySelector("#rubricaTable tbody");
    const newRow = document.createElement("tr");

    newRow.innerHTML = `
        <td><input type="text" name="criterios[]" placeholder="Criterio" required></td>
        <td><input type="text" name="descripciones[]" placeholder="Descripción" required></td>
        <td><input type="number" name="puntos[]" placeholder="Puntos" min="0" required></td>
        <td><button type="button" onclick="eliminarFilaRubrica(this)">Eliminar</button></td>
    `;

    tableBody.appendChild(newRow);
}

function eliminarFilaRubrica(button) {
    const row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
}
