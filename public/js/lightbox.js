document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('lightboxModal');
    const imgModal = document.getElementById('imgLightbox');
    const captionText = document.getElementById('captionLightbox');
    const spanCerrar = document.getElementsByClassName('cerrar-lightbox')[0];

    // 1. Seleccionamos todas las fotos que tengan la clase clicable
    const fotos = document.querySelectorAll('.foto-clicable');

    // 2. Recorremos cada foto y le añadimos el evento 'click'
    fotos.forEach(function(foto) {
        foto.addEventListener('click', function() {
            // Mostramos el modal
            modal.style.display = 'block';
            
            // Ponemos la ruta de la foto clicada en la imagen del modal
            imgModal.src = this.src;
            
            // Usamos el texto 'alt' de la foto como pie de foto
            captionText.innerHTML = this.alt;
            
            // Bloqueamos el scroll del body para mejorar la experiencia
            document.body.style.overflow = 'hidden';
        });
    });

    // 3. Función para cerrar el modal y restaurar el scroll
    function cerrarModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // 4. Cerrar al clicar en la 'X'
    if (spanCerrar) {
        spanCerrar.addEventListener('click', cerrarModal);
    }

    // 5. Cerrar al clicar en cualquier parte oscura (fuera de la imagen)
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            cerrarModal();
        }
    });

    // 6. Cerrar al pulsar la tecla Escape (ESC)
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            cerrarModal();
        }
    });

});