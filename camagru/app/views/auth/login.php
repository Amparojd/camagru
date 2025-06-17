<div class="auth-page">
    <div class="logo-container">
        <img src="<?php echo URL_ROOT; ?>/img/camagru_logo.png" alt="Logo" class="main-logo">
    </div>
    <div class="auth-form-container">
        <div class="auth-header">
            <h2>Iniciar Sesión</h2>
        </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST">
        <div class="form-group">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required 
                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <a href="/register">Regístrate</a></p>
</div>
