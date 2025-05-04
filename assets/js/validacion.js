/**
 * Validación de formularios
 */
function validarFormularioJuego($formulario) {
    let errores = [];

    const titulo = $("#titulo").val().trim();
    const fechaInicio = $("#fecha_inicio").val();
    const fechaFin = $("#fecha_fin").val();
    const horasJugadas = $("#horas_jugadas").val();
    const plataforma = $("#plataforma").val();
    const caratula = $("#caratula").length ? $("#caratula")[0].files[0] : null;

    if (!titulo) errores.push("El título es obligatorio");
    if (!fechaInicio) errores.push("La fecha de inicio es obligatoria");
    if (!fechaFin) errores.push("La fecha de finalización es obligatoria");
    if (fechaInicio && fechaFin && new Date(fechaFin) < new Date(fechaInicio)) 
        errores.push("La fecha de finalización no puede ser anterior a la de inicio");
    if (!horasJugadas || horasJugadas < 1) 
        errores.push("Las horas jugadas deben ser al menos 1");
    if (!plataforma) errores.push("Debes seleccionar una plataforma");
    if (caratula && caratula.size > 2 * 1024 * 1024) 
        errores.push("La imagen de carátula no puede superar los 2MB");
    if (caratula && !["image/jpeg", "image/png", "image/gif", "image/webp"].includes(caratula.type)) 
        errores.push("Formato de imagen no permitido");

    if (errores.length > 0) {
        mostrarToast(errores.join("<br>"), "error");
        return false;
    }
    return true;
}

$(document).ready(function() {
    const $formularioJuego = $('form[action=""], form[action*="anyadirJuego"], form[action*="editarJuego"]');
    if ($formularioJuego.length) {
        $formularioJuego.on("submit", function(e) {
            if (!validarFormularioJuego($(this))) {
                e.preventDefault();
            }
        });
    }

    // Sistema de puntuación con estrellas
    const $puntuacionInput = $('#puntuacion');
    const $estrellas = $('.estrellasPuntuacion .estrella');
    
    // Función para actualizar estrellas basadas en el valor
    function actualizarEstrellas(valor) {
        $estrellas.each(function() {
            $(this).toggleClass('activa', parseInt($(this).data('value')) <= valor);
        });
    }
    
    // Evento para cuando se cambia el valor manualmente
    $puntuacionInput.on('input', function() {
        actualizarEstrellas($(this).val());
    });
    
    // Evento para cuando se hace clic en una estrella
    $estrellas.on('click', function() {
        const valor = $(this).data('value');
        $puntuacionInput.val(valor);
        actualizarEstrellas(valor);
    });
    
    // Inicializar con un valor
    if ($puntuacionInput.length) {
        if ($puntuacionInput.val()) {
            actualizarEstrellas($puntuacionInput.val());
        } else {
            $puntuacionInput.val(5);
            actualizarEstrellas(5);
        }
    }

    // Vista previa de la carátula al seleccionar archivo en editarJuego
    $('#caratula').on('change', function() {
        const entrada = this;
        if (entrada.files && entrada.files[0]) {
            const lector = new FileReader();
            lector.onload = function(evento) {
                $('#caratula-preview').attr('src', evento.target.result)
                                     .removeClass('d-none');
                $('#caratula-container').removeClass('d-none');
                $('.sinCaratula').addClass('d-none'); // Ocultar el mensaje "Sin imagen"
            };
            lector.readAsDataURL(entrada.files[0]);
        }
    });
});