document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos DOM
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('capture-btn');
    const uploadBtn = document.getElementById('upload-btn');
    const fileInput = document.getElementById('file-input');
    const preview = document.getElementById('preview');
    const saveForm = document.getElementById('save-form');
    const saveBtn = document.getElementById('save-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const imageTitle = document.getElementById('image-title');
    const stickerPreview = document.getElementById('sticker-preview');
    
    // Variables de estado
    let streaming = false;
    let selectedSticker = null;
    let capturedImage = null;
    let stream = null;
    
    // Configurar canvas
    const context = canvas.getContext('2d');
    canvas.width = 640;
    canvas.height = 480;
    
    // Iniciar la webcam
    async function startWebcam() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: true, 
                audio: false 
            });
            
            video.srcObject = stream;
            video.play();
            
            // Habilitar botón de captura una vez que se haya seleccionado un sticker
            if (selectedSticker) {
                captureBtn.disabled = false;
            }
        } catch (err) {
            console.error('Error al acceder a la webcam:', err);
            alert('No se pudo acceder a la webcam. Por favor, permite el acceso o utiliza la opción de subir imagen.');
        }
    }
    
    // Iniciar webcam al cargar la página
    startWebcam();
    
    // Evento cuando el video está listo para reproducirse
    video.addEventListener('canplay', function() {
        if (!streaming) {
            // Ajustar el tamaño del video
            const aspectRatio = video.videoWidth / video.videoHeight;
            video.width = 640;
            video.height = video.width / aspectRatio;
            streaming = true;
        }
    });
    
    // Selección de sticker
    const stickers = document.querySelectorAll('.sticker');
    stickers.forEach(sticker => {
        sticker.addEventListener('click', function() {
            // Eliminar selección anterior
            document.querySelectorAll('.sticker').forEach(s => {
                s.classList.remove('selected');
            });
            
            // Aplicar nueva selección
            this.classList.add('selected');
            selectedSticker = this.dataset.src;
            
            // Mostrar vista previa del sticker
            stickerPreview.innerHTML = '';
            const stickerImg = document.createElement('img');
            stickerImg.src = this.src;
            stickerImg.classList.add('sticker-preview-img');
            stickerPreview.appendChild(stickerImg);
            
            // Habilitar botón de captura si la webcam está activa
            if (streaming) {
                captureBtn.disabled = false;
            }
        });
    });
    
    // Habilitar botón de captura incluso sin sticker
    if (streaming) {
        captureBtn.disabled = false;
    }
    
    // Capturar foto
    captureBtn.addEventListener('click', function() {
        // Dibujar el video en el canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convertir canvas a imagen
        capturedImage = canvas.toDataURL('image/png');
        
        // Mostrar vista previa con o sin sticker
        showPreview(capturedImage);
    });
    
    // Subir imagen en lugar de usar webcam
    uploadBtn.addEventListener('click', function() {
        fileInput.click();
    });
    
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length === 0) return;
        
        const file = e.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                // Dibujar la imagen subida en el canvas
                context.clearRect(0, 0, canvas.width, canvas.height);
                
                // Calcular proporciones para ajustar la imagen al canvas
                const aspectRatio = img.width / img.height;
                let drawWidth = canvas.width;
                let drawHeight = canvas.width / aspectRatio;
                
                if (drawHeight > canvas.height) {
                    drawHeight = canvas.height;
                    drawWidth = canvas.height * aspectRatio;
                }
                
                // Centrar la imagen
                const x = (canvas.width - drawWidth) / 2;
                const y = (canvas.height - drawHeight) / 2;
                
                context.drawImage(img, x, y, drawWidth, drawHeight);
                
                // Convertir canvas a imagen
                capturedImage = canvas.toDataURL('image/png');
                
                // Mostrar vista previa con o sin sticker
                showPreview(capturedImage);
            };
            img.src = event.target.result;
        };
        
        reader.readAsDataURL(file);
    });
    
    // Mostrar vista previa con o sin sticker
    function showPreview(imageData) {
        // Crear imagen para la vista previa
        const img = new Image();
        img.onload = function() {
            // Crear un nuevo canvas para la vista previa
            const previewCanvas = document.createElement('canvas');
            previewCanvas.width = img.width;
            previewCanvas.height = img.height;
            const previewContext = previewCanvas.getContext('2d');
            
            // Dibujar la imagen capturada
            previewContext.drawImage(img, 0, 0);
            
            if (selectedSticker) {
                // Si hay un sticker seleccionado, cargarlo y dibujarlo
                const stickerImg = new Image();
                stickerImg.onload = function() {
                    // Calcular tamaño del sticker (1/4 del tamaño de la imagen)
                    const stickerWidth = previewCanvas.width / 4;
                    const stickerHeight = (stickerImg.height / stickerImg.width) * stickerWidth;
                    
                    // Posición central
                    const x = (previewCanvas.width - stickerWidth) / 2;
                    const y = (previewCanvas.height - stickerHeight) / 2;
                    
                    // Dibujar sticker
                    previewContext.drawImage(stickerImg, x, y, stickerWidth, stickerHeight);
                    
                    // Actualizar vista previa
                    preview.src = previewCanvas.toDataURL('image/png');
                    capturedImage = previewCanvas.toDataURL('image/png');
                    
                    // Mostrar formulario de guardado
                    saveForm.style.display = 'block';
                };
                stickerImg.src = selectedSticker;
            } else {
                // Si no hay sticker, simplemente mostrar la imagen
                preview.src = previewCanvas.toDataURL('image/png');
                capturedImage = previewCanvas.toDataURL('image/png');
                
                // Mostrar formulario de guardado
                saveForm.style.display = 'block';
            }
        };
        img.src = imageData;
    }
    
    // Guardar imagen
    saveBtn.addEventListener('click', function() {
        if (!capturedImage) {
            alert('Por favor, captura una imagen primero.');
            return;
        }
        
        // Preparar datos para enviar
        const formData = new FormData();
        formData.append('image', capturedImage);
        formData.append('title', imageTitle.value);
        
        // Enviar imagen al servidor
        fetch('/editor/saveImage', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Imagen guardada correctamente');
                // Recargar la página para mostrar la nueva imagen
                window.location.reload();
            } else {
                alert('Error al guardar la imagen: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar la imagen. Por favor, intenta de nuevo.');
        });
    });
    
    // Cancelar guardado
    cancelBtn.addEventListener('click', function() {
        saveForm.style.display = 'none';
        capturedImage = null;
    });
    
    // Eliminar imagen
    document.querySelectorAll('.delete-image-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                const imageId = this.dataset.id;
                
                const formData = new FormData();
                formData.append('image_id', imageId);
                
                fetch('/delete-image', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Eliminar el elemento del DOM
                        const imageElement = document.querySelector(`.user-image-item[data-id="${imageId}"]`);
                        if (imageElement) {
                            imageElement.remove();
                        }
                        
                        // Si no quedan imágenes, mostrar mensaje
                        if (document.querySelectorAll('.user-image-item').length === 0) {
                            const noImagesMessage = document.createElement('p');
                            noImagesMessage.classList.add('no-images');
                            noImagesMessage.textContent = 'Aún no has creado ninguna imagen.';
                            document.querySelector('.user-images-section').appendChild(noImagesMessage);
                        }
                    } else {
                        alert('Error al eliminar la imagen: ' + (data.error || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar la imagen. Por favor, intenta de nuevo.');
                });
            }
        });
    });
});