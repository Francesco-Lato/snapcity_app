<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador - SnapCity</title>
    
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/buscador-vista.css?v=<?= time(); ?>">
</head>
<body class="auth-body">

<div class="auth-container" style="max-width: 1100px;">
    
    <div class="acciones-superior">
        <a href="index.php" class="btn-secundario">&larr; Volver al inicio</a>
    </div>

    <div class="auth-header">
        <h2>Explorar <span>SnapCity</span></h2>
        <p>Encuentra lugares, fotógrafos o explora por etiquetas.</p>
    </div>
    
    <div class="bento-card buscador-tarjeta">
        <form action="index.php" method="GET" class="buscador-form">
            <input type="hidden" name="action" value="buscar">
            <input type="text" name="q" class="input-buscar" placeholder="Ej: Barcelona, #street, noche, Juan..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" required>
            <button type="submit" class="btn-primario">Buscar</button>
        </form>
    </div>

    <?php if (isset($_GET['q']) && !empty(trim($_GET['q']))): ?>
        <div class="resultados-titulo">
            <h3>Resultados para: "<?= htmlspecialchars($_GET['q']) ?>"</h3>
        </div>
        
        <hr style="border: 0; border-top: 1px solid #2a2a2a; margin-bottom: 30px;">

        <?php if (!empty($usuarios_encontrados)): ?>
            <div class="resultados-seccion">
                <h4 class="seccion-titulo" style="color: #4CAF50;">Usuarios encontrados (<?= count($usuarios_encontrados) ?>)</h4>
                <div class="usuarios-grid">
                    <?php foreach ($usuarios_encontrados as $u): ?>
                        <div class="usuario-resultado bento-card">
                            <div class="usuario-info">
                                <strong><?= htmlspecialchars($u['nickname']) ?></strong> 
                                <span>(<?= htmlspecialchars($u['nombre']) ?>) - 📍 <?= htmlspecialchars($u['zona']) ?></span>
                            </div>
                            <a href="index.php?action=perfil_publico&id=<?= $u['id'] ?>" class="btn-secundario">Ver Perfil</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <br>
        <?php endif; ?>

        <?php if (!empty($fotos_encontradas)): ?>
            <div class="resultados-seccion">
                <h4 class="seccion-titulo" style="color: #2196F3;">Fotos encontradas (<?= count($fotos_encontradas) ?>)</h4>
                <div class="galeria-grid">
                    <?php foreach ($fotos_encontradas as $foto): ?>
                        <div class="foto-tarjeta">
                            <div class="foto-wrapper">
                                <img src="<?= htmlspecialchars($foto['ruta_archivo']) ?>" alt="<?= htmlspecialchars($foto['titulo']) ?>" class="foto-clicable">
                            </div>
                            <div class="foto-info">
                                <h4><?= htmlspecialchars($foto['titulo']) ?></h4>
                                <p style="font-size: 0.85em; color: #ffc107;">Por: <a href="index.php?action=perfil_publico&id=<?= $foto['id_usuario'] ?>" style="color: #ffc107;"><?= htmlspecialchars($foto['nickname']) ?></a></p>
                                <p style="font-size: 0.8em; color: #888;">📍 <?= htmlspecialchars($foto['ubicacion']) ?></p>
                                <?php if (!empty($foto['etiqueta'])): ?>
                                    <span class="etiqueta-badge"><?= htmlspecialchars($foto['etiqueta']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($usuarios_encontrados) && empty($fotos_encontradas)): ?>
            <div class="galeria-vacia">
                <p>No hemos encontrado ningún usuario ni foto que coincida con tu búsqueda. </p>
                <p>Prueba con otras palabras, ubicaciones o etiquetas.</p>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<div id="lightboxModal" class="lightbox">
    <span class="cerrar-lightbox">&times;</span>
    <img class="lightbox-contenido" id="imgLightbox">
    <div id="captionLightbox" class="lightbox-caption"></div>
</div>

<script src="js/lightbox.js"></script>

</body>
</html>