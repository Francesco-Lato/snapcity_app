document.addEventListener('DOMContentLoaded', function() {
    // Cambiar el texto del botón cuando se selecciona una foto
    const inputAvatar = document.getElementById('subida-avatar');
    const labelAvatar = document.getElementById('label-avatar');

    if (inputAvatar && labelAvatar) {
        // Guardamos el texto original por si cancelan
        const textoOriginal = labelAvatar.innerHTML; 

        inputAvatar.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Si seleccionan archivo, cambiamos texto y color a verde
                labelAvatar.textContent = "📎 " + this.files[0].name;
                labelAvatar.style.borderColor = "#4CAF50"; 
                labelAvatar.style.color = "#4CAF50";
            } else {
                // Si cancelan, vuelve a la normalidad
                labelAvatar.innerHTML = textoOriginal;
                labelAvatar.style.borderColor = "#ffc107";
                labelAvatar.style.color = "#ffc107";
            }
        });
    }
});