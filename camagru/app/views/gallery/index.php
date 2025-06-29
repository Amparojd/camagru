<div class="gallery-container">
    <h1>Galería de Fotos</h1>
    
    <div class="gallery-grid">
        <?php if (empty($images)): ?>
            <p class="no-images">No hay imágenes disponibles en la galería.</p>
        <?php else: ?>
            <?php foreach($images as $image): ?>
                <div class="gallery-item">
                    <div class="image-container">
                        <img src="<?php echo URL_ROOT . '/' . $image->file_path; ?>" alt="<?php echo htmlspecialchars($image->title ?: 'Imagen de usuario'); ?>">
                    </div>
                    <div class="image-info">
                        <p class="image-title"><?php echo htmlspecialchars($image->title ?: 'Sin título'); ?></p>
                        <p class="image-author">Por: <?php echo htmlspecialchars($image->username); ?></p>
                        <p class="image-date"><?php echo date('d/m/Y', strtotime($image->created_at)); ?></p>
                    </div>
                    <div class="image-actions">
                        <!-- Aquí irán los botones de like y comentarios cuando se implementen -->
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Paginación básica -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?php echo URL_ROOT; ?>/gallery?page=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Estilos CSS específicos para la galería -->
<style>
.gallery-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.gallery-item {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background-color: #fff;
    transition: transform 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
}

.image-container {
    height: 250px;
    overflow: hidden;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-info {
    padding: 15px;
}

.image-title {
    font-weight: bold;
    margin-bottom: 5px;
}

.image-author, .image-date {
    color: #666;
    font-size: 0.9em;
    margin: 5px 0;
}

.image-actions {
    padding: 10px 15px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
}

.pagination {
    margin-top: 30px;
    text-align: center;
}

.pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 5px;
    border-radius: 4px;
    background-color: #f5f5f5;
    color: #333;
    text-decoration: none;
}

.pagination a.active {
    background-color: #4CAF50;
    color: white;
}

.no-images {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px;
    background-color: #f9f9f9;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: 1fr;
    }
}
</style>
