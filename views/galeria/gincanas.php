<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gincanas Activas - SnapCity</title>
    
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/gincanas.css?v=<?= time(); ?>">
</head>
<body class="auth-body">

<!-- Envoltorio para permitir que el sidebar sea sticky al lado del contenido -->
<div class="page-wrapper">

    <!-- SIDEBAR STICKY -->
    <aside class="sticky-sidebar">
        <a href="index.php" class="btn-secundario">Inicio</a>
        <a href="index.php?action=perfil" class="btn-secundario">Mi perfil</a>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="auth-container" style="max-width: 750px; width: 100%; margin: 0;">
        
        <div class="auth-header">
            <h2>Gincanas Fotográficas <span>Activas</span></h2>
            <p>Únete a los retos urbanos. Sal a la calle, busca la mejor captura y súbela con la etiqueta oficial.</p>
        </div>
        
        <div class="gincana-lista">
            <?php if (empty($gincanas)): ?>
                <div class="gincana-vacia">
                    <p>Ahora mismo no hay ninguna gincana activa. ¡Vuelve pronto!</p>
                </div>
            <?php else: ?>
                <?php foreach ($gincanas as $g): ?>
                    <div class="gincana-card">
                        <div class="gincana-header">
                            <h3><?= htmlspecialchars($g['zona']) ?></h3>
                            <span class="gincana-tag"><?= htmlspecialchars($g['etiqueta']) ?></span>
                        </div>

                        <div class="gincana-detalles">
                            <p><strong>Inicio:</strong> <?= date('d/m/Y H:i', strtotime($g['fecha_inicio'])) ?></p>
                            <p><strong>Fin:</strong> <?= date('d/m/Y H:i', strtotime($g['fecha_fin'])) ?></p>
                        </div>
                        
                        <div class="gincana-footer">
                            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] !== 'admin'): ?>
                                <a href="index.php?action=subir_foto&etiqueta=<?= urlencode($g['etiqueta']) ?>" class="btn-primario">
                                    Participar
                                </a>
                            <?php endif; ?>
                            
                            <a href="index.php?action=ver_gincana&etiqueta=<?= urlencode($g['etiqueta']) ?>" class="btn-secundario">
                                Ver fotos &rarr;
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>

</div>

</body>
</html>