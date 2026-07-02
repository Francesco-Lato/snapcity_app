
document.addEventListener('DOMContentLoaded', function() {
    let contenedorSol = document.getElementById('widget-hora-dorada');
    if (!contenedorSol) return;

    // Coordenadas fijas
    let lat = 37.8882;
    let lng = -4.7794;

    fetch(`https://api.sunrise-sunset.org/json?lat=${lat}&lng=${lng}&formatted=0`)
        .then(response => response.json())
        .then(data => {
            if(data.status === "OK") {
                
                // hora universal a la hora local
                let amanecer = new Date(data.results.sunrise).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                let atardecer = new Date(data.results.sunset).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                let horaDorada = new Date(data.results.civil_twilight_begin).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                let horaAzul = new Date(data.results.civil_twilight_end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                // 4 tarjetas
                contenedorSol.innerHTML = `
                    <div class="hora-dorada-grid">
                        <div class="tarjeta-hora">
                            <strong style="color: #2196F3;">Amanecer</strong><br>
                            <span class="hora">${amanecer}</span>
                        </div>
                        
                        <div class="tarjeta-hora destacada">
            
                            <strong style="color: #ff9800;">Hora Dorada</strong><br>
                            <span class="hora">${horaDorada}</span>
                        </div>
                        
                        <div class="tarjeta-hora">
                            <strong style="color: #e91e63;">Atardecer</strong><br>
                            <span class="hora">${atardecer}</span>
                        </div>

                        <div class="tarjeta-hora azul">
                            <strong style="color: #3f51b5;">Hora Azul</strong><br>
                            <span class="hora">${horaAzul}</span>
                        </div>
                    </div>
                `;
            } else {
                contenedorSol.innerHTML = "<p style='color: red; text-align: center;'>No hemos podido calcular la hora del sol hoy.</p>";
            }
        })
        .catch(error => {
            console.log("Error con la API del Sol:", error);
            contenedorSol.innerHTML = "<p style='color: red; text-align: center;'>Hubo un problema de conexión con el satélite.</p>";
        });
});