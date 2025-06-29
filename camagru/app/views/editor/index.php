<div class="editor-container">
    <h1>Editor de Imágenes</h1>
    
    <div class="editor-layout">
        <!-- Sección de captura de cámara -->
        <div class="camera-section">
            <div class="video-container">
                <video id="video" autoplay></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div id="sticker-preview"></div>
            </div>
            
            <div class="camera-controls">
                <button id="capture-btn" class="btn btn-primary" disabled>Capturar Foto</button>
                <input type="file" id="file-input" accept="image/*" style="display: none;">
                <button id="upload-btn" class="btn btn-secondary">Subir Imagen</button>
            </div>
            
            <!-- Formulario para guardar la imagen -->
            <div id="save-form" style="display: none;">
                <div class="preview-container">
                    <img id="preview" src="" alt="Vista previa">
                </div>
                <div class="form-group">
                    <input type="text" id="image-title" placeholder="Título (opcional)" class="form-control">
                </div>
                <div class="action-buttons">
                    <button id="save-btn" class="btn btn-primary">Guardar</button>
                    <button id="cancel-btn" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>
        
        <!-- Sección de stickers -->
        <div class="stickers-section">
            <h3>Stickers</h3>
            <p class="sticker-info">Los stickers son opcionales. Selecciona uno si deseas aplicarlo a tu imagen.</p>
            <div class="stickers-grid">
                <?php foreach($stickers as $sticker): ?>
                <div class="sticker-item">
                    <img src="<?php echo URL_ROOT . '/' . $sticker; ?>" alt="Sticker" data-src="<?php echo $sticker; ?>" class="sticker">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Sección de imágenes del usuario -->
    <div class="user-images-section">
        <h3>Tus Imágenes</h3>
        <?php if (empty($userImages)): ?>
            <p class="no-images">Aún no has creado ninguna imagen.</p>
        <?php else: ?>
            <div class="user-images-grid">
                <?php foreach($userImages as $image): ?>
                    <div class="user-image-item" data-id="<?php echo $image->id; ?>">
                        <img src="<?php echo URL_ROOT . '/' . $image->file_path; ?>" alt="Tu imagen">
                        <div class="image-actions">
                            <button class="delete-image-btn" data-id="<?php echo $image->id; ?>">Eliminar</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Script específico para el editor -->
<script src="<?php echo URL_ROOT; ?>/js/editor.js"></script>