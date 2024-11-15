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