<?php

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($username, $email, $password) {
        error_log("Iniciando creación de usuario: $username con email: $email");
        
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(50));
        
        $sql = "INSERT INTO users (username, email, password, verification_token) 
                VALUES (:username, :email, :password, :token)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hash,
                ':token' => $token
            ]);
            
            $userId = $this->db->lastInsertId();
            error_log("Usuario creado con ID: $userId");
            
            if ($userId) {
                // Enviar email de verificación
                error_log("Enviando email de verificación para usuario ID: $userId");
                return $this->sendVerificationEmail($email, $username, $token);
            }
            error_log("Error: No se pudo obtener el ID del usuario creado");
            return false;
        } catch(PDOException $e) {
            error_log("Error PDO al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyEmail($token) {
        $sql = "UPDATE users SET verified = true WHERE verification_token = :token";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function sendVerificationEmail($email, $username, $token) {
    $to = $email;
    $subject = "Verifica tu cuenta de Camagru";
    $verifyUrl = "http://localhost:8080/verify?token=" . $token;
    
    $message = "Hola $username,\n\n";
    $message .= "Por favor, verifica tu cuenta haciendo clic en el siguiente enlace:\n";
    $message .= $verifyUrl . "\n\n";
    $message .= "Si no has creado esta cuenta, por favor ignora este email.\n";
    
    $headers = "From: noreply@camagru.com\r\n";
    
    // Registrar información de depuración
    error_log("INICIO - Proceso de verificación para usuario: $username");
    error_log("CORREO - Intentando enviar verificación a: $email");
    error_log("TOKEN - $token");
    error_log("ENLACE - Enlace de verificación para $username: $verifyUrl");
    
    // En modo desarrollo, no intentamos enviar el correo real
    // Solo registramos la información y devolvemos true
    error_log("FIN - Proceso de verificación registrado correctamente");
    
    // Para desarrollo, siempre devolvemos true sin intentar enviar el correo
    return true;
}
}
