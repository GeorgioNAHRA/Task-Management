document.addEventListener('DOMContentLoaded', function () {
    const uploadArea = document.getElementById('file-upload-area');
    const fileInput = document.getElementById('file-input');

    if (!uploadArea || !fileInput) {
        console.error("Les éléments HTML nécessaires au drag-and-drop ne sont pas trouvés.");
        return;
    }

    // Clic pour sélectionner un fichier
    uploadArea.addEventListener('click', () => fileInput.click());

    // Gestion du changement de fichier
    fileInput.addEventListener('change', () => {
        const form = document.getElementById('file-upload-form');
        if (form) {
            form.submit();
        } else {
            console.error("Formulaire non trouvé pour soumettre le fichier.");
        }
    });

    // Drag-and-drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileInput.files = e.dataTransfer.files;
        const form = document.getElementById('file-upload-form');
        if (form) {
            form.submit();
        } else {
            console.error("Formulaire non trouvé pour soumettre le fichier.");
        }
    });
});