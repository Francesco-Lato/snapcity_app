<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnapCity</title>
    <link rel="icon" href="../public/img/simbolo_amarillo.png" type="image/x-icon">
    
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/home.css?v=<?= time(); ?>">
</head>
<body>

<div class="bento-grid">
    
    <div class="bento-item item-header">
        <h1>SNAPCITY</h1>
        <p>La red social para fotógrafos urbanos.</p>
    </div>

    <div class="bento-item item-foto foto-1" style="background-image: url('https://picsum.photos/600/800?random=1');"></div>
    
    <div class="bento-item item-buscador">
        <h3>Encuentra fotógrafos o lugares</h3>
        <form action="index.php" method="GET" class="form-buscador">
            <input type="hidden" name="action" value="buscar">
            <input type="text" name="q" class="input-buscador" placeholder="Ej: Barcelona, #street..." required>
            <button type="submit" class="btn-buscador-submit">Buscar</button>
        </form>
    </div>

    <div class="bento-item item-foto foto-2" style="background-image: url('https://picsum.photos/500/500?random=2');"></div>

    <div class="bento-item item-luz-info">
        <h3>La luz perfecta de hoy</h3>
        <div id="widget-hora-dorada">
            <em>Conectando con el satélite solar...</em>
        </div>
    </div>

    <div class="bento-item item-foto foto-4" style="background-image: url('https://picsum.photos/400/400?random=4');"></div>

    <div class="bento-item item-isotipo">
        <img src="../public/img/simbolo_amarillo.png" alt="Isotipo SnapCity">
    </div>

    <div class="bento-item item-foto foto-3" style="background-image: url('https://picsum.photos/800/400?random=3');"></div>

    <div class="bento-item item-menu">
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <h3 style="margin-bottom: 10px;">Hola, <?= htmlspecialchars($_SESSION['usuario_nickname']) ?></h3>
        
        <div class="botones-grid" style="flex-direction: row; flex-wrap: wrap; justify-content: space-between;">
            <a href="index.php?action=gincanas" class="btn-gincana" style="flex: 1 1 100%;">Ver Gincanas</a>
            
            <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin'): ?>
                <a href="index.php?action=crear_gincana" class="btn-crear" style="flex: 1 1 45%;">Crear</a>
            <?php else: ?>
                <a href="index.php?action=perfil" class="btn-perfil" style="flex: 1 1 45%;">Perfil</a>
            <?php endif; ?>
            
            <a href="index.php?action=logout" class="btn-logout" style="flex: 1 1 45%;">Salir</a>
        </div>
        
    <?php else: ?>
        <div class="botones-grid">
            <h3 style="margin-bottom: 5px;">Únete</h3>
            <a href="index.php?action=login" class="btn-login">Entrar</a>
            <a href="index.php?action=registro" class="btn-registro">Registro</a>
        </div>
    <?php endif; ?>
    </div>

    <div class="bento-item item-foto foto-5" style="background-image: url('https://picsum.photos/400/400?random=5');"></div>

</div>

<script src="js/hora_dorada.js"></script>

</body>
</html>