<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería del Reto: <?= htmlspecialchars($etiqueta) ?></title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/galeria-gincana.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/mapa.css"> 
</head>
<body class="auth-body">

<div class="page-wrapper" style="max-width: 1300px;">

    <aside class="sticky-sidebar">
        <a href="index.php" class="btn-secundario">Inicio</a>
        <a href="index.php?action=perfil" class="btn-secundario">Mi perfil</a>
        <a href="index.php?action=gincanas" class="btn-secundario">Volver a Gincanas</a>
    </aside>

    <div class="auth-container" style="max-width: 1100px; width: 100%; margin: 0;">
        
        <div class="auth-header">
            <h2>Gincana fotográfica: <span><?= htmlspecialchars($etiqueta) ?></span></h2>
            <p>Descubre las capturas y vota tus 3 favoritas.</p>
        </div>
        
        <div id="mapa-gincana" style="height: 300px; width: 100%; border-radius: 15px; border: 1px solid #333; margin-bottom: 30px;"></div>

        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] == 'limite_votos'): ?>
                <div class="alert-box error" style="background: rgba(244, 67, 54, 0.1); color: #f44336; padding: 15px; border-radius: 10px; border: 1px solid #f44336; margin-bottom: 20px;">
                    <strong>¡Límite alcanzado!</strong> Ya has gastado tus 3 votos. Si quieres votar esta foto, primero haz clic en "Quitar Voto" en otra.
                </div>
            <?php elseif ($_GET['error'] == 'voto_propio'): ?>
                <div class="alert-box warning" style="background: rgba(255, 193, 7, 0.1); color: #ffc107; padding: 15px; border-radius: 10px; border: 1px solid #ffc107; margin-bottom: 20px;">
                    <strong>¡Aviso!</strong> No puedes votar tus propias fotografías. ¡Vota por tus compañeros!
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] !== 'admin'): ?>
            <div class="votos-badge" style="background: #333; padding: 10px 15px; border-radius: 8px; display: inline-block; margin-bottom: 20px;">
                Tienes <strong style="color: #ffc107;"><?= 3 - $votos_usados ?> votos restantes</strong> (Has usado <?= $votos_usados ?> de 3).
            </div>
        <?php endif; ?>

        <div class="acciones-bar" style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] !== 'admin'): ?>
                <a href="index.php?action=subir_foto&etiqueta=<?= urlencode($etiqueta) ?>" class="btn-primario" style="padding: 12px 25px;">📸 Subir foto al reto</a>
            <?php endif; ?>
        </div>

        <hr style="border: 0; border-top: 1px solid #2a2a2a; margin: 20px 0 30px 0;">

        <div class="galeria-grid">
            <?php if (empty($fotos_gincana)): ?>
                <div class="galeria-vacia">
                    <p>Aún no hay fotos participando en esta gincana. ¡Anímate!</p>
                </div>
            <?php else: ?>
                <?php foreach ($fotos_gincana as $foto): ?>
                    <div class="foto-tarjeta">
                        <div class="foto-wrapper">
                            <img src="<?= htmlspecialchars($foto['ruta_archivo']) ?>" 
                                 alt="<?= htmlspecialchars($foto['titulo']) ?>" 
                                 class="foto-clicable">
                        </div>
                        
                        <div class="foto-info">
                            <h4><?= htmlspecialchars($foto['titulo']) ?></h4>
                            <p class="autor-meta">
                                Por: <a href="index.php?action=perfil_publico&id=<?= $foto['id_usuario'] ?>" style="color: #ffc107;">
                                    <?= htmlspecialchars($foto['nickname']) ?>
                                </a>
                            </p>
                            <p class="meta-texto" style="font-size: 0.85em; color: #888; margin-bottom: 10px;">📷 <?= htmlspecialchars($foto['camara']) ?> | <?= htmlspecialchars($foto['distancia_focal']) ?></p>
                            
                            <div class="foto-footer" style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                <span class="votos-count" style="font-weight: bold; color: #ff5252;">❤️ <?= $foto['total_votos'] ?></span>
                                
                                <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] !== 'admin'): ?>
                                    <?php if ($foto['id_usuario'] == $_SESSION['usuario_id']): ?>
                                        <span class="badge-propia" style="background: #333; padding: 5px 10px; border-radius: 5px; font-size: 0.85em;">⭐ Tu foto</span>
                                    <?php else: ?>
                                        <?php if (in_array($foto['id'], $mis_votos)): ?>
                                            <a href="index.php?action=votar_foto&id_foto=<?= $foto['id'] ?>&etiqueta=<?= urlencode($etiqueta) ?>" class="btn-secundario" style="font-size: 0.85em; padding: 6px 12px; border-color: #f44336; color: #f44336;">
                                                Quitar voto
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?action=votar_foto&id_foto=<?= $foto['id'] ?>&etiqueta=<?= urlencode($etiqueta) ?>" class="btn-primario" style="font-size: 0.85em; padding: 6px 12px;">
                                                Votar
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<div id="lightboxModal" class="lightbox">
    <span class="cerrar-lightbox">&times;</span>
    <img class="lightbox-contenido" id="imgLightbox">
    <div id="captionLightbox" class="lightbox-caption"></div>
</div>

<script>
    const datosFotosGincana = <?= json_encode($fotos_gincana); ?>;
    const infoGincana = <?= json_encode($datos_gincana); ?>;
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/mapa_gincana.js"></script>
<script src="js/lightbox.js"></script>

</body>
</html>