<?php

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($username, $email, $password) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(50));
            
            $sql = "INSERT INTO users (username, email, password, verification_token) 
                    VALUES (:username, :email, :password, :token)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hash,
                ':token' => $token
            ]);
            
            if ($result) {
                // Log para desarrollo
                error_log("\n+------------------------------------------+");
                error_log("|     NUEVA SOLICITUD DE VERIFICACIÓN      |");
                error_log("+------------------------------------------+");
                error_log("| Usuario: " . $username);
                error_log("| Email: " . $email);
                error_log("| Token: " . $token);
                error_log("| URL de verificación:");
                error_log("|    http://localhost:8080/verify?token=" . $token);
                error_log("+------------------------------------------+\n");
                
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
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
        error_log("Iniciando envío de email a: " . $email);

        $to = $email;
        $subject = "Verifica tu cuenta de Camagru";
        $verifyUrl = "http://localhost:8080/verify?token=" . $token;
        
        $message = "Hola $username,\n\n";
        $message .= "Por favor, verifica tu cuenta haciendo clic en el siguiente enlace:\n";
        $message .= $verifyUrl . "\n\n";
        $message .= "Si no has creado esta cuenta, por favor ignora este email.\n";
        
        // Simplificamos los headers a un string simple
        $headers = "From: noreply@camagru.com\r\n";
        $headers .= "Reply-To: noreply@camagru.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        error_log("Headers: " . $headers);
        $result = mail($to, $subject, $message, $headers);
    
    error_log("Resultado del envío: " . ($result ? "éxito" : "fallo"));
    return $result;
}
}
