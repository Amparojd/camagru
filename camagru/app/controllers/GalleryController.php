<?php

class GalleryController {
    private $imageModel;
    
    public function __construct() {
        require_once '../app/models/Database.php';
        require_once '../app/models/Image.php';
        $this->imageModel = new Image();
    }
    
    private function render($view, $data = []) {
        if ($data) {
            extract($data);
        }
        
        $content = "../app/views/{$view}.php";
        require_once "../app/views/layouts/main.php";
    }
    
    /**
     * Muestra la página principal de la galería
     */
    public function index() {
        // Por ahora, solo obtenemos algunas imágenes sin paginación
        $images = $this->imageModel->getLatestImages();
        
        $this->render('gallery/index', [
            'images' => $images,
            'page' => 1,
            'totalPages' => 1
        ]);
    }
}
