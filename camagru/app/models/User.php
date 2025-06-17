<?php

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($username, $email, $password) {
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
             if ($this->db->lastInsertId()) {
            // Enviar email de verificación
            return $this->sendVerificationEmail($email, $username, $token);
        }
        return false;
    } catch(PDOException $e) {
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
    
    // Para desarrollo: escribir el enlace de verificación en los logs
    error_log("Enlace de verificación para $username: $verifyUrl");
    
    return mail($to, $subject, $message, $headers);
}
}
