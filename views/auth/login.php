<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SnapCity</title>
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/auth.css?v=<?= time(); ?>">
</head>
<body class="auth-body">

<div class="auth-container">
    <div class="auth-header">
        <h2>Bienvenido a <span>SnapCity</span></h2>
        <p>Inicia sesión para continuar</p>
    </div>
    
    <form action="index.php?action=procesar_login" method="POST" class="auth-form">
        
        <div class="form-group">
            <label for="identificador">Nickname o Correo Electrónico</label>
            <input type="text" id="identificador" name="identificador" placeholder="ejemplo@correo.com" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-auth">Entrar</button>
    </form>
    
    <p class="auth-footer">
        ¿No tienes cuenta? <a href="index.php?action=registro">Regístrate aquí</a>
    </p>
</div>

</body>
</html>