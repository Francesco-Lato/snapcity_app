// public/js/selector_foto.js
document.addEventListener('DOMContentLoaded', function() {
    let map = L.map('mapa-subida').setView([41.3851, 2.1734], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marcador;

    function situarMarcador(lat, lng) {
        if (marcador) {
            marcador.setLatLng([lat, lng]);
        } else {
            marcador = L.marker([lat, lng]).addTo(map);
        }
        document.getElementById('latitud_foto').value = lat;
        document.getElementById('longitud_foto').value = lng;
    }

    // Función principal de búsqueda
    function buscarDireccion() {
        let inputDireccion = document.getElementById('buscar_direccion');
        let texto = inputDireccion.value;
        
        if (texto.length < 3) {
            alert("Por favor, escribe una dirección un poco más larga.");
            return;
        }

        // Cambiamos el texto del botón temporalmente para que el usuario sepa que está cargando
        let btn = document.getElementById('btn-verificar');
        let textoOriginal = btn.innerHTML;
        btn.innerHTML = "Buscando...";

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(texto)}`)
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = textoOriginal; // Restauramos el botón
                
                if (data.length > 0) {
                    let lat = data[0].lat;
                    let lon = data[0].lon;
                    map.setView([lat, lon], 16);
                    situarMarcador(lat, lon);
                } else {
                    alert("No hemos podido encontrar esa dirección en el mapa. Prueba a añadir la ciudad o país.");
                }
            })
            .catch(error => {
                console.log('Error al buscar dirección:', error);
                btn.innerHTML = textoOriginal;
            });
    }

    // A. Click manual en el mapa
    map.on('click', function(e) {
        situarMarcador(e.latlng.lat, e.latlng.lng);
    });

    // B. Buscar al hacer clic en el botón nuevo
    document.getElementById('btn-verificar').addEventListener('click', buscarDireccion);

    // C. Si el usuario pulsa ENTER dentro del campo de texto, también buscamos en vez de subir la foto
    document.getElementById('buscar_direccion').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); 
            buscarDireccion();
        }
    });
});