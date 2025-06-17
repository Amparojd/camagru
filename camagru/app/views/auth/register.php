<div class="auth-page">
    <div class="logo-container">
        <img src="<?php echo URL_ROOT; ?>/img/camagru_logo.png" alt="Logo" class="main-logo">
    </div>
    <div class="auth-form-container">
        <div class="auth-header">
            <h2>Registro</h2>
        </div>
    <form action="/register" method="POST">
        <div class="form-group">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirmar Contraseña:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn">Registrarse</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>
</div>
