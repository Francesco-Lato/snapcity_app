<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Gincana - Panel Admin</title>
    
    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/admin-gincana.css?v=<?= time(); ?>">
    
    <!-- Leaflet para el mapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body class="auth-body">

<div class="auth-container admin-card" style="max-width: 700px;">
    
    <div class="auth-header">
        <h2>Panel de <span>Administración</span></h2>
        <p>Organizar nueva Gincana Urbana</p>
    </div>
    
    <form action="index.php?action=procesar_gincana" method="POST" class="auth-form">
        
        <div class="form-row">
            <div class="form-group">
                <label for="zona">Zona / Barrio:</label>
                <input type="text" id="zona" name="zona" placeholder="Ej: Barrio de Las Letras" required>
            </div>

            <div class="form-group">
                <label for="etiqueta">Etiqueta Oficial:</label>
                <input type="text" id="etiqueta" name="etiqueta" placeholder="#GincanaCentro2024" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="datetime-local" id="fecha_fin" name="fecha_fin" required>
            </div>
        </div>

        <div class="form-group">
            <label>Delimita la zona de la gincana:</label>
            <p class="instrucciones-mapa">Haz clic en el mapa para situar el centro del evento.</p>
            
            <div id="mapa-selector"></div>
            
            <!-- Inputs ocultos para el mapa -->
            <input type="hidden" name="latitud" id="input-lat">
            <input type="hidden" name="longitud" id="input-lng">
            <input type="hidden" name="radio" id="input-radio" value="500">
        </div>
        
        <div class="acciones-perfil">
            <a href="index.php" class="btn-secundario">Cancelar</a>
            <button type="submit" class="btn-admin">Crear Gincana</button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/selector_zona.js"></script>

</body>
</html>