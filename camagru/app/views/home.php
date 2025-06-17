
<div class="home-welcome">
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="center-content">
            <div class="logo-container">
                <img src="<?php echo URL_ROOT; ?>/img/camagru_logo.png" alt="Logo Camagru" class="main-logo hover-scale">
            </div>
            <div class="welcome-text">
                <h1>Bienvenido a Camagru</h1>
                <p>Una aplicación para crear y compartir fotos con filtros divertidos</p>
            </div>
            <div class="auth-buttons">
                <a href="<?php echo URL_ROOT; ?>/login" class="btn btn-primary">Iniciar Sesión</a>
                <a href="<?php echo URL_ROOT; ?>/register" class="btn btn-secondary">Registrarse</a>
            </div>
        </div>
    <?php else: ?>
        <div class="center-content">
            <div class="welcome-text">
                <h1>Bienvenido de nuevo</h1>
                <p>Estás conectado en Camagru. ¡Explora las opciones del menú para comenzar!</p>
            </div>
            <div class="auth-buttons">
                <a href="<?php echo URL_ROOT; ?>/edit" class="btn btn-primary">Crear nueva foto</a>
                <a href="<?php echo URL_ROOT; ?>/gallery" class="btn btn-secondary">Ver galería</a>
            </div>
        </div>
    <?php endif; ?>
</div>