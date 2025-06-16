<!DOCTYPE html>
<html>
<head>
    <title><?php echo SITE_NAME; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <nav>
            <h1><?php echo SITE_NAME; ?></h1>
            <ul>
                <li><a href="/">Inicio</a></li>
                <li><a href="/gallery">Galería</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/edit">Editar</a></li>
                    <li><a href="/logout">Salir</a></li>
                <?php else: ?>
                    <li><a href="/login">Entrar</a></li>
                    <li><a href="/register">Registro</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <?php require $content; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
    </footer>
</body>
</html>
