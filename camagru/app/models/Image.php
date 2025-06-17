<?php

class Image {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
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
        $this->db->query("INSERT INTO images (user_id, file_path, title, created_at) VALUES (:user_id, :file_path, :title, NOW())");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':file_path', $file_path);
        $this->db->bind(':title', $title);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Obtiene todas las imágenes para la galería, ordenadas por fecha de creación
     * 
     * @param int $page Número de página actual
     * @param int $perPage Número de elementos por página
     * @return array Lista de imágenes
     */
    public function getAllImages($page = 1, $perPage = 5) {
        $offset = ($page - 1) * $perPage;
        
        $this->db->query("SELECT i.*, u.username 
                          FROM images i 
                          JOIN users u ON i.user_id = u.id 
                          ORDER BY i.created_at DESC 
                          LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', $offset);
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtiene las imágenes de un usuario específico
     * 
     * @param int $user_id ID del usuario
     * @return array Lista de imágenes del usuario
     */
    public function getUserImages($user_id) {
        $this->db->query("SELECT * FROM images WHERE user_id = :user_id ORDER BY created_at DESC");
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtiene una imagen por su ID
     * 
     * @param int $id ID de la imagen
     * @return object|bool Datos de la imagen o false si no existe
     */
    public function getImageById($id) {
        $this->db->query("SELECT i.*, u.username 
                          FROM images i 
                          JOIN users u ON i.user_id = u.id 
                          WHERE i.id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    /**
     * Elimina una imagen por su ID
     * 
     * @param int $id ID de la imagen
     * @param int $user_id ID del usuario que intenta eliminar (para verificar propiedad)
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function delete($id, $user_id) {
        // Primero verificamos que la imagen pertenece al usuario
        $this->db->query("SELECT * FROM images WHERE id = :id AND user_id = :user_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        
        $image = $this->db->single();
        
        if (!$image) {
            return false; // La imagen no existe o no pertenece al usuario
        }
        
        // Eliminar la imagen física del servidor
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $image->file_path;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Eliminar los likes y comentarios asociados
        $this->db->query("DELETE FROM likes WHERE image_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        
        $this->db->query("DELETE FROM comments WHERE image_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        
        // Eliminar la imagen de la base de datos
        $this->db->query("DELETE FROM images WHERE id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * Cuenta el número total de imágenes para la paginación
     * 
     * @return int Número total de imágenes
     */
    public function countImages() {
        $this->db->query("SELECT COUNT(*) as total FROM images");
        $result = $this->db->single();
        
        return $result->total;
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
}