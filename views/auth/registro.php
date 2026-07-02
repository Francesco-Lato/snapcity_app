<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SnapCity</title>
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/auth.css?v=<?= time(); ?>">
</head>
<body class="auth-body">

<div class="auth-container" style="max-width: 550px;">
    <div class="auth-header">
        <h2>Únete a <span>SnapCity</span></h2>
        <p>Crea tu perfil y comparte tu visión de la ciudad</p>
    </div>
    
    <form action="index.php?action=procesar_registro" method="POST" class="auth-form">
        
        <div class="form-group">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Laura Gómez" required>
        </div>

        <div class="form-group">
            <label for="nickname">Nickname</label>
            <input type="text" id="nickname" name="nickname" placeholder="Tu nombre de usuario único" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
        </div>

        <div class="form-group">
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required 
           pattern="(?=.*[A-Z])(?=.*[\W_]).{8,}" 
           title="Debe tener al menos 8 caracteres, incluir una letra mayúscula y un carácter especial (ej: !@#$%).">
    
    <small style="color: #666; display: block; margin-top: 5px;">
        La contraseña debe tener mínimo 8 caracteres, 1 mayúscula y 1 carácter especial (ej: @, #, $, !).
    </small>
</div>

        <div class="form-group">
            <label for="fecha_nac">Fecha de Nacimiento</label>
            <input type="date" id="fecha_nac" name="fecha_nac" required>
        </div>

        <div class="form-group">
            <label for="zona">Zona de la ciudad donde sueles fotografiar</label>
            <input type="text" id="zona" name="zona" placeholder="Ej: Barrio Gótico, Centro histórico...">
        </div>

        <div class="form-group">
            <label for="bio">Breve presentación</label>
            <textarea id="bio" name="bio" rows="3" placeholder="Cuéntanos sobre ti y tu cámara..."></textarea>
        </div>

        <div class="form-group">
            <label>Tus estilos de foto urbana preferidos</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="estilos[]" value="Street photo"> Street photo</label>
                <label><input type="checkbox" name="estilos[]" value="Arquitectura urbana"> Arquitectura urbana</label>
                <label><input type="checkbox" name="estilos[]" value="Arte urbano"> Arte urbano</label>
                <label><input type="checkbox" name="estilos[]" value="Minimalista"> Minimalista</label>
                <label><input type="checkbox" name="estilos[]" value="Retrato urbano"> Retrato urbano</label>
                <label><input type="checkbox" name="estilos[]" value="Nocturna"> Nocturna</label>
                <label><input type="checkbox" name="estilos[]" value="Urbex"> Urbex</label>
                <label><input type="checkbox" name="estilos[]" value="Abstraccion urbana"> Abstracción urbana</label>
                <label><input type="checkbox" name="estilos[]" value="Deporte en la ciudad"> Deporte en ciudad</label>
                <label><input type="checkbox" name="estilos[]" value="Eventos urbanos"> Eventos urbanos</label>
            </div>
        </div>

        <button type="submit" class="btn-auth">Crear mi perfil</button>
    </form>
    
    <p class="auth-footer">
        ¿Ya tienes una cuenta? <a href="index.php?action=login">Inicia Sesión</a>
    </p>
</div>

</body>
</html>