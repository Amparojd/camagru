<?php

class Image {
    private $db;
    
    public function __construct() {
        // Usamos el singleton para obtener la instancia de Database
        $dbInstance = Database::getInstance();
        $this->db = $dbInstance->getConnection();
    }
    
    /**
     * Guarda una nueva imagen en la base de datos
     * 
     * @param int $user_id ID del usuario que creó la imagen
     * @param string $file_path Ruta relativa de la imagen guardada
     * @param string $title Título opcional para la imagen
     * @return bool|int ID de la imagen si se guarda correctamente, false en caso contrario
     */
    public function save($user_id, $file_path, $title = '') {
        try {
            $stmt = $this->db->prepare("INSERT INTO images (user_id, file_path, title, created_at) VALUES (:user_id, :file_path, :title, NOW())");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':file_path', $file_path);
            $stmt->bindParam(':title', $title);
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Error al guardar imagen: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todas las imágenes para la galería, ordenadas por fecha de creación
     * 
     * @param int $page Número de página actual
     * @param int $perPage Número de elementos por página
     * @return array Lista de imágenes
     */
    public function getAllImages($page = 1, $perPage = 5) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $stmt = $this->db->prepare("SELECT i.*, u.username 
                              FROM images i 
                              JOIN users u ON i.user_id = u.id 
                              ORDER BY i.created_at DESC 
                              LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error al obtener todas las imágenes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene las imágenes de un usuario específico
     * 
     * @param int $user_id ID del usuario
     * @return array Lista de imágenes del usuario
     */
    public function getUserImages($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM images WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error al obtener imágenes del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene una imagen por su ID
     * 
     * @param int $id ID de la imagen
     * @return object|bool Datos de la imagen o false si no existe
     */
    public function getImageById($id) {
        try {
            $stmt = $this->db->prepare("SELECT i.*, u.username 
                              FROM images i 
                              JOIN users u ON i.user_id = u.id 
                              WHERE i.id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error al obtener imagen por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina una imagen por su ID
     * 
     * @param int $id ID de la imagen
     * @param int $user_id ID del usuario que intenta eliminar (para verificar propiedad)
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function delete($id, $user_id) {
        try {
            // Primero verificamos que la imagen pertenece al usuario
            $stmt = $this->db->prepare("SELECT * FROM images WHERE id = :id AND user_id = :user_id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $image = $stmt->fetch(PDO::FETCH_OBJ);
            
            if (!$image) {
                return false; // La imagen no existe o no pertenece al usuario
            }
            
            // Eliminar la imagen física del servidor
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $image->file_path;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Eliminar los likes y comentarios asociados
            $stmtLikes = $this->db->prepare("DELETE FROM likes WHERE image_id = :id");
            $stmtLikes->bindParam(':id', $id);
            $stmtLikes->execute();
            
            $stmtComments = $this->db->prepare("DELETE FROM comments WHERE image_id = :id");
            $stmtComments->bindParam(':id', $id);
            $stmtComments->execute();
        
            // Eliminar la imagen de la base de datos
            $stmtImage = $this->db->prepare("DELETE FROM images WHERE id = :id");
            $stmtImage->bindParam(':id', $id);
            
            return $stmtImage->execute();
        } catch(PDOException $e) {
            error_log("Error al eliminar imagen: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cuenta el número total de imágenes para la paginación
     * 
     * @return int Número total de imágenes
     */
    public function countImages() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM images");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            return $result->total;
        } catch(PDOException $e) {
            error_log("Error al contar imágenes: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Cuenta el número de likes de una imagen
     * 
     * @param int $image_id ID de la imagen
     * @return int Número de likes
     */
    public function countLikes($image_id) {
        $this->db->query("SELECT COUNT(*) as total FROM likes WHERE image_id = :image_id");
        $this->db->bind(':image_id', $image_id);
        $result = $this->db->single();
        
        return $result->total;
    }
    
    /**
     * Verifica si un usuario ha dado like a una imagen
     * 
     * @param int $image_id ID de la imagen
     * @param int $user_id ID del usuario
     * @return bool True si el usuario dio like, false en caso contrario
     */
    public function userLiked($image_id, $user_id) {
        $this->db->query("SELECT * FROM likes WHERE image_id = :image_id AND user_id = :user_id");
        $this->db->bind(':image_id', $image_id);
        $this->db->bind(':user_id', $user_id);
        
        $result = $this->db->single();
        
        return $result ? true : false;
    }
    
    /**
     * Obtiene las imágenes más recientes para mostrar en la galería principal
     * 
     * @param int $limit Número máximo de imágenes a retornar
     * @return array Lista de imágenes
     */
    public function getLatestImages($limit = 10) {
        try {
            $stmt = $this->db->prepare("SELECT i.*, u.username 
                              FROM images i 
                              JOIN users u ON i.user_id = u.id 
                              ORDER BY i.created_at DESC 
                              LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error al obtener las últimas imágenes: " . $e->getMessage());
            return [];
        }
    }
}