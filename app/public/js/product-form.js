document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const fileInputContainer = document.getElementById('fileInputContainer');
    const selectedFiles = document.getElementById('selectedFiles');
    const fileCounter = document.getElementById('fileCounter');
    const maxFiles = 5;

    // Gestion du drag & drop
    fileInputContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileInputContainer.classList.add('dragover');
    });

    fileInputContainer.addEventListener('dragleave', function(e) {
        e.preventDefault();
        fileInputContainer.classList.remove('dragover');
    });

    fileInputContainer.addEventListener('drop', function(e) {
        e.preventDefault();
        fileInputContainer.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });

    // Gestion de la sélection de fichiers
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        handleFiles(files);
    });

    function handleFiles(files) {
        // Vider la prévisualisation existante
        selectedFiles.innerHTML = '';
        // Limiter le nombre de fichiers
        const filesToProcess = files.slice(0, maxFiles);
        filesToProcess.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                // Vérifier la taille du fichier (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showError(`Le fichier "${file.name}" est trop volumineux (max 5MB)`);
                    return;
                }
                createFilePreview(file, index);
            } else {
                showError(`Le fichier "${file.name}" n'est pas une image valide`);
            }
        });
        updateFileCounter(filesToProcess.length);
    }

    function createFilePreview(file, index) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('div');
            preview.className = 'file-preview';
            preview.dataset.filename = file.name;
            preview.innerHTML = `
                <img src="${e.target.result}" alt="${file.name}">
                <div class="file-info">
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                </div>
                <button type="button" class="remove-file" onclick="removeFile('${file.name}')" title="Supprimer cette image">×</button>
            `;
            selectedFiles.appendChild(preview);
        };
        reader.readAsDataURL(file);
    }

    function removeFile(filename) {
        // Supprimer la prévisualisation
        const preview = selectedFiles.querySelector(`[data-filename="${filename}"]`);
        if (preview) {
            preview.remove();
        }
        // Mettre à jour le compteur
        const remainingFiles = selectedFiles.querySelectorAll('.file-preview');
        updateFileCounter(remainingFiles.length);
    }

    function updateFileCounter(count) {
        if (count === 0) {
            fileCounter.textContent = 'Aucune image sélectionnée';
        } else if (count === 1) {
            fileCounter.textContent = '1 image sélectionnée';
        } else {
            fileCounter.textContent = `${count} images sélectionnées`;
        }
        // Changer la couleur selon le nombre de fichiers
        if (count >= maxFiles) {
            fileCounter.style.color = '#e74c3c';
            fileInputContainer.style.borderColor = '#e74c3c';
        } else {
            fileCounter.style.color = '#7f8c8d';
            fileInputContainer.style.borderColor = '#bdc3c7';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showError(message) {
        // Créer un message d'erreur temporaire
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = `
            background: #f8d7da;
            color: #721c24;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin: 0.5rem 0;
            border: 1px solid #f5c6cb;
            font-size: 0.9rem;
        `;
        errorDiv.textContent = message;
        selectedFiles.parentNode.insertBefore(errorDiv, selectedFiles);
        // Supprimer le message après 3 secondes
        setTimeout(() => {
            errorDiv.remove();
        }, 3000);
    }

    // Validation en temps réel
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    function validateField(field) {
        const value = field.value.trim();
        field.classList.remove('error', 'success');
        if (field.hasAttribute('required') && !value) {
            field.classList.add('error');
        } else if (value) {
            field.classList.add('success');
        }
    }
    // Initialiser le compteur
    updateFileCounter(0);
    // Rendre la fonction removeFile globale pour qu'elle soit accessible depuis le HTML
    window.removeFile = removeFile;
}); 