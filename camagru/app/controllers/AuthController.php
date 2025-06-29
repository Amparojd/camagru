<?php

require_once '../app/models/Database.php';
require_once '../app/models/User.php';
class AuthController {
    private $errors = [];
    private $user;

    public function __construct() {
        $this->user = new User();
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = [];
        
        // Si se desea destruir la sesión completamente, borra también la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Finalmente, destruir la sesión
        session_destroy();
        
        // Redireccionar al login
        header('Location: ' . URL_ROOT . '/login');
        exit;
    }

    private function render($view, $data = []) {
        if ($data) {
            extract($data);
        }
        $content = "../app/views/{$view}.php";
        require_once "../app/views/layouts/main.php";
    }

    private function validateLogin($username, $password) {
        if (empty($username)) {
            $this->errors[] = "El usuario es obligatorio";
        }
        if (empty($password)) {
            $this->errors[] = "La contraseña es obligatoria";
        }
        if (strlen($password) < 8) {
            $this->errors[] = "La contraseña debe tener al menos 8 caracteres";
        }
        return empty($this->errors);
    }

    private function validateRegistration($username, $email, $password, $confirm_password) {
        if (empty($username)) {
            $this->errors[] = "El usuario es obligatorio";
        }
        if (empty($email)) {
            $this->errors[] = "El email es obligatorio";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "El email no es válido";
        }
        if (empty($password)) {
            $this->errors[] = "La contraseña es obligatoria";
        }
        if (strlen($password) < 8) {
            $this->errors[] = "La contraseña debe tener al menos 8 caracteres";
        }
        if ($password !== $confirm_password) {
            $this->errors[] = "Las contraseñas no coinciden";
        }
        return empty($this->errors);
    }

     public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($this->validateLogin($username, $password)) {
                $user = $this->user->findByUsername($username);
                
                if ($user && password_verify($password, $user['password'])) {
                    if (!$user['verified']) {
                        $this->errors[] = "Por favor, verifica tu cuenta de email antes de iniciar sesión";
                        $this->render('auth/login', ['errors' => $this->errors]);
                        return;
                    }
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header('Location: ' . URL_ROOT . '/');
                    exit;
                }
                $this->errors[] = "Usuario o contraseña incorrectos";
            }
            $this->render('auth/login', ['errors' => $this->errors]);
        } else {
            $this->render('auth/login');
        }
    }

    public function verify($token) {
        if ($this->user->verifyEmail($token)) {
            $this->render('auth/login', [
                'success' => 'Tu cuenta ha sido verificada. Ya puedes iniciar sesión.'
            ]);
        } else {
            $this->render('auth/login', [
                'errors' => ['Token de verificación inválido o expirado.']
            ]);
        }
    }


     public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if ($this->validateRegistration($username, $email, $password, $confirm_password)) {
                // Intentar crear el usuario
                if ($this->user->create($username, $email, $password)) {
                    // Redirigir al login con mensaje de éxito
                    $this->render('auth/login', [
                        'success' => 'Registro exitoso. Por favor, verifica tu email para activar tu cuenta.'
                    ]);
                    return;
                }
                $this->errors[] = "Error al crear el usuario. El nombre de usuario o email ya existe.";
            }
            
            $this->render('auth/register', [
                'errors' => $this->errors,
                'username' => $username,
                'email' => $email
            ]);
        } else {
            $this->render('auth/register');
        }
    }
}
