document.addEventListener('DOMContentLoaded', function() {
    let contenedorMapa = document.getElementById('mapa-gincana');
    
    if (contenedorMapa && typeof datosFotosGincana !== 'undefined' && typeof infoGincana !== 'undefined') {
        
        // 1. Inicializamos el mapa centrado en la zona de la gincana (si existe)
        let centro = [37.8841, -4.7797]; // Centro Córdoba
        if (infoGincana.latitud && infoGincana.longitud) {
            centro = [infoGincana.latitud, infoGincana.longitud];
        }

        let map = L.map('mapa-gincana').setView(centro, 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // --- NUEVO: Dibujamos el círculo de la zona del reto ---
        if (infoGincana.latitud && infoGincana.longitud) {
            L.circle([infoGincana.latitud, infoGincana.longitud], {
                color: '#ff9800',      // Borde naranja
                fillColor: '#ff9800',  // Relleno naranja
                fillOpacity: 0.2,      // Muy transparente para ver las calles
                radius: parseInt(infoGincana.radio) // El radio que puso el admin
            }).addTo(map);
        }

        // 2. Ponemos los marcadores de las fotos (Tu código de antes)
        let markers = [];
        datosFotosGincana.forEach(function(foto) {
            if (foto.latitud && foto.longitud) {
                let marker = L.marker([foto.latitud, foto.longitud]).addTo(map);
                marker.bindPopup(`
                    <div style="text-align:center;">
                        <strong>${foto.titulo}</strong><br>
                        <img src="${foto.ruta_archivo}" style="width:100px; margin-top:5px; border-radius:4px;"><br>
                        <small>Por: ${foto.nickname}</small>
                    </div>
                `);
                markers.push([foto.latitud, foto.longitud]);
            }
        });

        // 3. Si no hay fotos, centramos el mapa en la zona de la gincana
        if (markers.length === 0 && infoGincana.latitud) {
            map.setView([infoGincana.latitud, infoGincana.longitud], 15);
        } else if (markers.length > 0) {
            let bounds = L.latLngBounds(markers);
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
});