<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - SnapCity</title>
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/estilosPerfil.css?v=<?= time(); ?>">
</head>
<body>

<div class="perfil-layout">
    
    <aside class="perfil-sidebar bento-card">
        <div class="perfil-header">
            <?php if (!empty($usuario['avatar'])): ?>
                <img src="<?= htmlspecialchars($usuario['avatar']) ?>" alt="Avatar de <?= htmlspecialchars($usuario['nickname']) ?>" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #ffc107; margin: 0 auto 15px auto; display: block; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
            <?php else: ?>
                <div class="avatar-placeholder">📸</div>
            <?php endif; ?>

            <form action="index.php?action=cambiar_avatar" method="POST" enctype="multipart/form-data" class="form-avatar">
                
                <label for="subida-avatar" id="label-avatar" class="btn-examinar">
                    📸 Cambiar foto de perfil
                </label>
                
                <input type="file" id="subida-avatar" name="avatar" accept="image/jpeg, image/png, image/webp" required style="display: none;">
                
                <button type="submit" class="btn-actualizar-avatar">Actualizar Avatar</button>
            </form>

            <h2><?= htmlspecialchars($usuario['nickname']) ?></h2>
            <span class="zona-badge">📍 <?= htmlspecialchars($usuario['zona']) ?></span>
        </div>

        <div class="perfil-detalles">
            <p><strong>Nombre:</strong> <span><?= htmlspecialchars($usuario['nombre']) ?></span></p>
            <p><strong>Estilos:</strong> <span><?= htmlspecialchars($usuario['estilos']) ?></span></p>
            <div class="perfil-bio">
                <strong>Sobre mí:</strong>
                <p><?= nl2br(htmlspecialchars($usuario['bio'])) ?></p>
            </div>
        </div>

        
        <div class="acciones-perfil" style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
            <a href="index.php" class="btn-secundario" style="text-align: center;">Volver al inicio</a>
            <a href="index.php?action=gincanas" class="btn-secundario" style="text-align: center;">Ver Gincanas</a>
            <a href="index.php?action=logout" class="btn-peligro" style="text-align: center; margin-top: 10px;">Cerrar Sesión</a>
        </div>
        
        <div class="contenedor-eliminar-cuenta" style="margin-top: 15px;">
            <form action="index.php?action=eliminar_cuenta" method="POST" onsubmit="return confirm('ATENCIÓN: ¿Estás completamente seguro de que quieres eliminar tu cuenta, tus fotos y tus votos para siempre?');" class="form-eliminar-cuenta">
                <button type="submit" class="btn-eliminar-cuenta" style="width: 100%;">
                    Eliminar cuenta
                </button>
            </form>
        </div>
    </aside>

    <main class="perfil-galeria bento-card">
        <div class="galeria-cabecera">
            <h3>Mi Galería Urbana</h3>
            <a href="index.php?action=subir_foto" class="btn-primario">+ Subir Nueva Foto</a>
        </div>

        <div class="galeria-grid">
            <?php if (empty($fotos)): ?>
                <div class="galeria-vacia">
                    <p>Aún no has subido ninguna foto. ¡Estrena tu galería!</p>
                </div>
            <?php else: ?>
                <?php foreach ($fotos as $foto): ?>
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
                        <div style="margin-top: auto; text-align: center; padding-bottom: 15px;">
                            <a href="index.php?action=eliminar_foto&id=<?= $foto['id'] ?>" 
                               onclick="return confirm('¿Estás seguro de que quieres eliminar esta foto para siempre?');" 
                               style="display: inline-block; padding: 6px 12px; background: #e91e63; color: white; text-decoration: none; border-radius: 4px; font-size: 0.9em; font-weight: bold;">
                               Eliminar foto
                            </a>
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
<script src="js/avatar.js"></script>

</body>
</html>