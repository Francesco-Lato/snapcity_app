<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Foto - SnapCity</title>
    
    <link rel="stylesheet" href="css/global.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/subir-foto.css?v=<?= time(); ?>">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="auth-body">

<div class="auth-container" style="max-width: 650px;">
    
    <div class="auth-header">
        <h2>Subir nueva <span>foto urbana</span></h2>
        <p>Sube tu foto. ¡Nosotros extraeremos la cámara, la focal y la fecha automáticamente!</p>
    </div>
    
    <form action="index.php?action=procesar_subida" method="POST" enctype="multipart/form-data" class="auth-form">
        
        <div class="form-group">
            <label for="foto">Selecciona tu fotografía (JPG o JPEG):</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg, image/jpg" required>
        </div>

        <div class="form-group">
            <label for="titulo">Título de la foto:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Ej: Atardecer urbano" required>
        </div>

        <div class="form-group">
            <label for="etiqueta">Etiqueta (Para búsquedas o gincanas):</label>
            <input type="text" id="etiqueta" name="etiqueta" 
                value="<?= isset($_GET['etiqueta']) ? htmlspecialchars($_GET['etiqueta']) : '' ?>" 
                placeholder="Ej: #streetbcn">
        </div>

        <div class="form-group">
            <label for="buscar_direccion">Ubicación de la foto:</label>
            <div class="input-group-mapa">
                <input type="text" id="buscar_direccion" name="ubicacion" 
                    placeholder="Escribe la calle o lugar (Ej: Calle Mayor, Madrid)" 
                    class="form-control">
                
                <button type="button" id="btn-verificar" class="btn-verificar">
                    Verificar calle
                </button>
            </div>
            
            <p class="instrucciones-mapa">O haz clic directamente en el mapa para situar la foto de forma manual:</p>
            
            <div id="mapa-subida"></div>

            <input type="hidden" name="latitud" id="latitud_foto">
            <input type="hidden" name="longitud" id="longitud_foto">
        </div>

        <div class="acciones-perfil">
            <a href="index.php?action=perfil" class="btn-secundario">Cancelar</a>
            <button type="submit" class="btn-primario">Subir Foto</button>
        </div>
    </form>
</div>

<script src="js/selector_foto.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputUbicacion = document.getElementById('buscar_direccion');
        const inputLat = document.getElementById('latitud_foto');
        const inputLon = document.getElementById('longitud_foto');

        var map = L.map('mapa-subida').setView([37.8882, -4.7794], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marcador = null;

        <?php if (!empty($datos_gincana) && !empty($datos_gincana['latitud'])): ?>
            var gincanaLat = <?= json_encode($datos_gincana['latitud']) ?>;
            var gincanaLon = <?= json_encode($datos_gincana['longitud']) ?>;
            var gincanaRadio = <?= !empty($datos_gincana['radio']) ? json_encode($datos_gincana['radio']) : 500 ?>;

            map.setView([gincanaLat, gincanaLon], 15);

            L.circle([gincanaLat, gincanaLon], {
                color: '#e91e63',
                fillColor: '#e91e63',
                fillOpacity: 0.15,
                radius: gincanaRadio
            }).addTo(map);
        <?php endif; ?>

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lon = e.latlng.lng;

            if (marcador) {
                map.removeLayer(marcador);
            }
            marcador = L.marker([lat, lon]).addTo(map);

            inputLat.value = lat;
            inputLon.value = lon;
            inputUbicacion.value = "Buscando calle...";

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        var calle = data.address.road || data.address.pedestrian || data.address.suburb || "Ubicación seleccionada";
                        inputUbicacion.value = calle; 
                    } else {
                        inputUbicacion.value = "Coordenadas: " + lat.toFixed(4) + ", " + lon.toFixed(4);
                    }
                })
                .catch(error => {
                    inputUbicacion.value = "Error al obtener la calle";
                });
        });
    });
</script>

</body>
</html>