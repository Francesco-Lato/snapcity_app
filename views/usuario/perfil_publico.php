<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= htmlspecialchars($usuario_publico['nickname']) ?> - SnapCity</title>
    
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/perfil-publico.css?v=<?= time(); ?>">
</head>
<body>

<div class="perfil-layout">
    
    <aside class="perfil-sidebar bento-card">
        <div class="acciones-superior">
            <a href="javascript:history.back()" class="btn-secundario">&larr; Volver atrás</a>
        </div>

        <div class="perfil-header">
            <div class="avatar-placeholder">📸</div>
            <h2><?= htmlspecialchars($usuario_publico['nickname']) ?></h2>
            <span class="zona-badge">📍 <?= htmlspecialchars($usuario_publico['zona']) ?></span>
        </div>

        <div class="perfil-detalles">
            <p><strong>Nombre real:</strong> <span><?= htmlspecialchars($usuario_publico['nombre']) ?></span></p>
            <p><strong>Estilos preferidos:</strong> <span><?= htmlspecialchars($usuario_publico['estilos']) ?></span></p>
            <div class="perfil-bio">
                <strong>Sobre mí:</strong>
                <p><?= nl2br(htmlspecialchars($usuario_publico['bio'])) ?></p>
            </div>
        </div>
    </aside>

    <main class="perfil-galeria bento-card">
        <div class="galeria-cabecera">
            <h3>Galería de <?= htmlspecialchars($usuario_publico['nickname']) ?></h3>
        </div>

        <div class="galeria-grid">
            <?php if (empty($fotos_publicas)): ?>
                <div class="galeria-vacia">
                    <p>Este usuario aún no tiene fotos en su galería.</p>
                </div>
            <?php else: ?>
                <?php foreach ($fotos_publicas as $foto): ?>
                    <div class="foto-tarjeta">
                        <div class="foto-wrapper">
                            <img src="<?= htmlspecialchars($foto['ruta_archivo']) ?>" 
                                 alt="<?= htmlspecialchars($foto['titulo']) ?>" 
                                 class="foto-clicable">
                        </div>
                        <div class="foto-info">
                            <h4><?= htmlspecialchars($foto['titulo']) ?></h4>
                            <p class="foto-meta">📷 <?= htmlspecialchars($foto['camara']) ?> | <?= htmlspecialchars($foto['distancia_focal']) ?></p>
                            <p class="foto-meta">📍 <?= htmlspecialchars($foto['ubicacion']) ?></p>
                            <?php if (!empty($foto['etiqueta'])): ?>
                                <span class="etiqueta-badge"><?= htmlspecialchars($foto['etiqueta']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

</div>

<div id="lightboxModal" class="lightbox">
    <span class="cerrar-lightbox">&times;</span>
    <img class="lightbox-contenido" id="imgLightbox">
    <div id="captionLightbox" class="lightbox-caption"></div>
</div>

<script src="js/lightbox.js"></script>

</body>
</html>