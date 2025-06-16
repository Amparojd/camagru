<?php
require_once '../config/config.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/models/Database.php';


// Iniciar sesión
session_start();

// Instanciar controlador
$homeController = new HomeController();
$authController = new AuthController();

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
    default:
        $homeController->error404();
        break;
}
