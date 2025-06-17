<!DOCTYPE html>
<html>
<head>
    <title><?php echo SITE_NAME; ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $cssVersion = filemtime(APP_ROOT . '/public/css/style.css');
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo URL_ROOT; ?>/css/style.css?v=<?php echo $cssVersion; ?>" media="all">
</head>
<?php
// Determinar cuándo mostrar el sidebar
$isAuthenticated = isset($_SESSION['user_id']);
$currentPath = $_SERVER['REQUEST_URI'];
// Páginas que siempre deben ser fullwidth cuando no hay autenticación
$fullwidthPaths = ['/', '', '/login', '/register'];
$useFullwidth = !$isAuthenticated && in_array($currentPath, $fullwidthPaths);
?>
<body>
    <div class="app-container<?php echo $useFullwidth ? ' fullwidth' : ''; ?>">
        <div class="sidebar">
            <div class="logo">
                <img src="<?php echo URL_ROOT; ?>/img/camagru_logo.png" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="<?php echo URL_ROOT; ?>/">Inicio</a></li>
                    <li><a href="<?php echo URL_ROOT; ?>/gallery">Galería</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo URL_ROOT; ?>/edit">Editar</a></li>
                        <li><a href="<?php echo URL_ROOT; ?>/logout">Salir</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo URL_ROOT; ?>/login">Entrar</a></li>
                        <li><a href="<?php echo URL_ROOT; ?>/register">Registro</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <?php if (!$useFullwidth): ?>
                <div class="main-header">
                    <button id="toggleSidebar" class="toggle-sidebar">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <?php echo SITE_NAME; ?>
                </div>
            <?php endif; ?>

            <div id="main-content">
                <?php require $content; ?>
            </div>

            <footer class="footer">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
            </footer>
        </div>
    </div>
</body>
</html>
