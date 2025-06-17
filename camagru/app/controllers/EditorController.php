<?php

require_once '../app/models/Image.php';

class EditorController {
    private $imageModel;
    
    public function __construct() {
        // Verificar que el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
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
     * Muestra la página del editor de imágenes
     */
    public function index() {
        // Obtener las imágenes del usuario para la sección de miniaturas
        $user_id = $_SESSION['user_id'];
        $userImages = $this->imageModel->getUserImages($user_id);
        
        // Obtener stickers disponibles
        $stickersPath = 'img/stickers/';
        $stickers = [];
        
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $stickersPath;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != "." && $file != ".." && pathinfo($file, PATHINFO_EXTENSION) == 'png') {
                        $stickers[] = $stickersPath . $file;
                    }
                }
                closedir($dh);
            }
        }
        
        $this->render('editor/index', [
            'userImages' => $userImages,
            'stickers' => $stickers
        ]);
    }
    
    /**
     * Guarda una imagen capturada y procesada
     */
    public function saveImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar que se recibieron los datos necesarios
            if (!isset($_POST['image']) || empty($_POST['image'])) {
                echo json_encode(['error' => 'No se recibió ninguna imagen']);
                return;
            }
            
            $user_id = $_SESSION['user_id'];
            $title = $_POST['title'] ?? '';
            $imageData = $_POST['image'];
            
            // Eliminar el encabezado 'data:image/png;base64,' de la imagen
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            
            // Decodificar la imagen
            $imageData = base64_decode($imageData);
            
            // Generar un nombre único para la imagen
            $imageName = uniqid('img_') . '.png';
            $uploadPath = 'uploads/';
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadPath;
            
            // Crear el directorio si no existe
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
            
            // Guardar la imagen en el servidor
            if (file_put_contents($fullPath . $imageName, $imageData)) {
                // Guardar la información de la imagen en la base de datos
                $savedImageId = $this->imageModel->save($user_id, $uploadPath . $imageName, $title);
                
                if ($savedImageId) {
                    echo json_encode([
                        'success' => true,
                        'image_id' => $savedImageId,
                        'image_path' => $uploadPath . $imageName
                    ]);
                    return;
                }
            }
            
            echo json_encode(['error' => 'Error al guardar la imagen']);
        }
    }
    
    /**
     * Elimina una imagen del usuario
     */
    public function deleteImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $image_id = $_POST['image_id'] ?? 0;
            
            if ($image_id && $this->imageModel->delete($image_id, $user_id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Error al eliminar la imagen']);
            }
        }
    }
}