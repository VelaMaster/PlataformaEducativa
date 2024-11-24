function mostrarNombreArchivo() {
    const fileInput = document.getElementById('archivo');
    const fileNameDisplay = document.getElementById('fileName');
    if (fileInput.files.length > 0) {
        fileNameDisplay.textContent = fileInput.files[0].name;
    } else {
        fileNameDisplay.textContent = "Ningún archivo seleccionado";
    }
}
function previewFile() {
    const fileInput = document.getElementById('archivo');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileIcon = document.getElementById('fileIcon');
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        fileName.textContent = file.name;
        filePreview.style.display = 'flex';
        
        if (file.type.startsWith('image/')) {
            fileIcon.src = URL.createObjectURL(file);
            fileIcon.onclick = function() {
                abrirModal(fileIcon.src, 'image');
            };
        } else if (file.type === "application/pdf") {
            fileIcon.src = 'img/pdf-icon.png'; // Asegúrate de que este ícono exista
            fileIcon.onclick = function() {
                abrirModal(URL.createObjectURL(file), 'pdf');
            };
        } else if (file.type === "text/plain") {
            const reader = new FileReader();
            reader.onload = function(event) {
                abrirModal(event.target.result, 'text');
            };
            reader.readAsText(file);
            fileIcon.src = 'img/txt-icon.png'; // Asegúrate de que este ícono exista
        } else {
            fileIcon.src = 'img/file-icon.png'; // Asegúrate de que este ícono exista
            fileIcon.onclick = function() {
                abrirModal('', 'unsupported');
            };
        }
    } else {
        filePreview.style.display = 'none';
    }
}

function abrirModal(content, type) {
    const modal = document.getElementById("previewModal");
    const modalImage = document.getElementById("modalImage");
    const modalText = document.getElementById("modalText");
    const modalIframe = document.getElementById("modalIframe");
    const unsupportedText = document.getElementById("unsupportedText");

    modal.style.display = "block";
    
    modalImage.style.display = 'none';
    modalText.style.display = 'none';
    modalIframe.style.display = 'none';
    unsupportedText.style.display = 'none';
    
    if (type === 'image') {
        modalImage.src = content;
        modalImage.style.display = 'block';
    } else if (type === 'pdf') {
        modalIframe.src = content;
        modalIframe.style.display = 'block';
    } else if (type === 'text') {
        modalText.textContent = content;
        modalText.style.display = 'block';
    } else {
        unsupportedText.style.display = 'block';
    }
}

function cerrarModal() {
    const modal = document.getElementById("previewModal");
    modal.style.display = "none";
    document.getElementById("modalImage").src = "";
    document.getElementById("modalIframe").src = "";
}
function mostrarPrevisualizacion(input) {
    const archivo = input.files[0]; // Obtén el archivo seleccionado
    const previewContainer = document.getElementById('preview-container');

    // Limpia el contenido previo
    previewContainer.innerHTML = '';

    if (archivo) {
        const tipoArchivo = archivo.type;
        const url = URL.createObjectURL(archivo); // Crear URL temporal para previsualizar el archivo

        let contenidoPreview = `
            <div class="preview-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #f9f9f9; padding: 15px; border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15); max-width: 200px; margin: 20px auto; cursor: pointer;">
                <h4 style="margin: 0 0 10px; font-size: 16px; font-weight: bold; color: #333; text-align: center;">Vista previa</h4>
        `;

        if (tipoArchivo.startsWith('image/')) {
            contenidoPreview += `<img src="${url}" alt="Previsualización de Imagen" style="width: 180px; height: 180px; object-fit: cover; border-radius: 8px;">`;
        } else if (tipoArchivo === 'application/pdf') {
            contenidoPreview += `<embed src="${url}#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="180" height="180" style="border-radius: 8px; border: none;">`;
        } else {
            contenidoPreview += `<p style="font-size: 13px; color: #888; text-align: center;">Vista previa no disponible</p>`;
        }

        contenidoPreview += '</div>';
        previewContainer.innerHTML = contenidoPreview;
    }
}
