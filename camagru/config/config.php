<?php

// Configuración de la base de datos
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_USER', getenv('DB_USER') ?: 'camagru_user');
define('DB_PASS', getenv('DB_PASSWORD') ?: 'secure_password');
define('DB_NAME', getenv('DB_NAME') ?: 'camagru_db');

// Configuración de la aplicación
define('SITE_NAME', 'Camagru');
define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', 'http://localhost:8080');

// Configuración de desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/apache2/error.log');
