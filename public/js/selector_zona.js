document.addEventListener('DOMContentLoaded', function() {
    let mapaDiv = document.getElementById('mapa-selector');
    if (!mapaDiv) return;

    // Inicializar mapa (Barcelona por defecto)
    let map = L.map('mapa-selector').setView([41.3851, 2.1734], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marcador;
    let circulo;

    // Función para actualizar los inputs ocultos del formulario
    function actualizarInputs(lat, lng, radio) {
        document.getElementById('input-lat').value = lat;
        document.getElementById('input-lng').value = lng;
        document.getElementById('input-radio').value = Math.round(radio);
    }

    // Evento de clic en el mapa
    map.on('click', function(e) {
        let lat = e.latlng.lat;
        let lng = e.latlng.lng;
        let radioInicial = 500; // 500 metros por defecto

        if (marcador) {
            marcador.setLatLng(e.latlng);
            circulo.setLatLng(e.latlng);
        } else {
            // Crear marcador arrastrable
            marcador = L.marker(e.latlng, { draggable: true }).addTo(map);
            // Crear círculo de zona
            circulo = L.circle(e.latlng, {
                color: '#ff9800',
                fillColor: '#ff9800',
                fillOpacity: 0.3,
                radius: radioInicial
            }).addTo(map);

            // Si el admin arrastra el marcador, actualizamos posición
            marcador.on('drag', function(event) {
                let position = marcador.getLatLng();
                circulo.setLatLng(position);
                actualizarInputs(position.lat, position.lng, circulo.getRadius());
            });
        }

        actualizarInputs(lat, lng, radioInicial);
    });
});