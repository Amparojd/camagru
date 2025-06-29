<?php
require_once '../config/config.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/EditorController.php';
require_once '../app/controllers/GalleryController.php';
require_once '../app/models/Database.php';


// Iniciar sesión
session_start();

// Instanciar controladores según se necesiten
$homeController = new HomeController();
$authController = new AuthController();

// Los controladores que requieren autenticación se instancian solo cuando se necesitan
// para evitar redirecciones en cascada

// Routing básico
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($request) {
    case '/':
        $homeController->index();
        break;
    case '/login':
        $authController->login();
        break;
    case '/register':
        $authController->register();
        break;
    case '/verify':
        $token = $_GET['token'] ?? '';
        $authController->verify($token);
        break;
    case '/editor':
        $editorController = new EditorController();
        $editorController->index();
        break;
    case '/gallery':
        $galleryController = new GalleryController();
        $galleryController->index();
        break;
    case '/logout':
        $authController->logout();
        break;
    default:
        $homeController->error404();
        break;
}
