function validarFormularioJuego($formulario) {
    let errores = [];

    const titulo = $("#titulo").val().trim();
    const fechaInicio = $("#fecha_inicio").val();
    const fechaFin = $("#fecha_fin").val();
    const horasJugadas = $("#horas_jugadas").val();
    const plataforma = $("#plataforma").val();
    const resenya = $("#resenya").val().trim();
    const caratula = $("#caratula").length ? $("#caratula")[0].files[0] : null;
    
    // Obtener la fecha actual en formato YYYY-MM-DD para comparar con la fecha de finalización
    const hoy = new Date();
    const fechaActual = hoy.toISOString().split('T')[0];

    // Validar cada campo individualmente con mensajes específicos
    if (!titulo) errores.push("Debes añadir un título para el juego");
    if (!fechaInicio) errores.push("Debes especificar la fecha de inicio");
    if (!fechaFin) errores.push("Debes especificar la fecha de finalización");
    if (fechaInicio && fechaFin && new Date(fechaFin) < new Date(fechaInicio))
        errores.push("La fecha de finalización no puede ser anterior a la de inicio");
    // Añadimos la validación de que la fecha de fin no sea posterior a hoy
    if (fechaFin && fechaFin > fechaActual)
        errores.push("La fecha de finalización no puede ser posterior a hoy");
    if (!horasJugadas || horasJugadas < 1)
        errores.push("Las horas jugadas deben ser al menos 1");
    if (!plataforma) errores.push("Debes seleccionar una plataforma");
    if (!resenya) errores.push("Debes añadir una descripción o reseña del juego");
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

$(document).ready(function () {
    const $formularioJuego = $('form[action=""], form[action*="anyadirJuego"], form[action*="editarJuego"]');
    if ($formularioJuego.length) {
        // Recuperar datos almacenados si existen
        if (localStorage.getItem('formData')) {
            try {
                const formData = JSON.parse(localStorage.getItem('formData'));

                // Rellenar el formulario con los datos guardados
                $('#titulo').val(formData.titulo || '');
                $('#fecha_inicio').val(formData.fechaInicio || '');
                $('#fecha_fin').val(formData.fechaFin || '');
                $('#horas_jugadas').val(formData.horasJugadas || '');
                $('#plataforma').val(formData.plataforma || '');
                $('#puntuacion').val(formData.puntuacion || 5);
                $('#resenya').val(formData.resenya || '');

                // Actualizar las estrellas
                if (formData.puntuacion) {
                    actualizarEstrellas(formData.puntuacion);
                }

                // Limpiar datos guardados después de usarlos
                localStorage.removeItem('formData');
            } catch (e) {
                console.error("Error al recuperar datos del formulario:", e);
                localStorage.removeItem('formData');
            }
        }

        $formularioJuego.on("submit", function (e) {
            const formData = {
                titulo: $('#titulo').val(),
                fechaInicio: $('#fecha_inicio').val(),
                fechaFin: $('#fecha_fin').val(),
                horasJugadas: $('#horas_jugadas').val(),
                plataforma: $('#plataforma').val(),
                puntuacion: $('#puntuacion').val(),
                resenya: $('#resenya').val()
            };

            localStorage.setItem('formData', JSON.stringify(formData));

            if (!validarFormularioJuego($(this))) {
                e.preventDefault();
            } else {
                // Si la validación es exitosa, borrar los datos guardados
                localStorage.removeItem('formData');
            }
        });
    }

    const $puntuacionInput = $('#puntuacion');
    const $estrellas = $('.estrellasPuntuacion .estrella');

    function actualizarEstrellas(valor) {
        $estrellas.each(function () {
            $(this).toggleClass('activa', parseInt($(this).data('value')) <= valor);
        });
    }

    $puntuacionInput.on('input', function () {
        actualizarEstrellas($(this).val());
    });

    $estrellas.on('click', function () {
        const valor = $(this).data('value');
        $puntuacionInput.val(valor);
        actualizarEstrellas(valor);
    });

    if ($puntuacionInput.length) {
        if ($puntuacionInput.val()) {
            actualizarEstrellas($puntuacionInput.val());
        } else {
            $puntuacionInput.val(5);
            actualizarEstrellas(5);
        }
    }

    let ultimaCaratulaValida = null;
    let ultimoArchivoSeleccionado = null;

    let ultimoAvatarValido = null;
    let ultimoAvatarSeleccionado = null;

    $('#caratula').on('change', function () {
        const entrada = this;

        if (!entrada.files || entrada.files.length === 0) {
            if (ultimoArchivoSeleccionado) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(ultimoArchivoSeleccionado);
                entrada.files = dataTransfer.files;

                if (ultimaCaratulaValida) {
                    $('#caratula-preview').attr('src', ultimaCaratulaValida);
                    $('#caratula-preview').removeClass('d-none');
                    $('#caratula-container').removeClass('d-none');
                    $('.sinCaratula').addClass('d-none');
                }
            }
            return;
        }

        if (entrada.files && entrada.files[0]) {
            ultimoArchivoSeleccionado = entrada.files[0];

            const lector = new FileReader();
            lector.onload = function (evento) {
                ultimaCaratulaValida = evento.target.result;

                $('#caratula-preview').attr('src', ultimaCaratulaValida)
                    .removeClass('d-none');
                $('#caratula-container').removeClass('d-none');
                $('.sinCaratula').addClass('d-none');
            };
            lector.readAsDataURL(entrada.files[0]);
        }
    });

    if ($('#caratula-preview').attr('src')) {
        ultimaCaratulaValida = $('#caratula-preview').attr('src');
    }

    $('#avatar').on('change', function () {
        const entrada = this;

        if (!entrada.files || entrada.files.length === 0) {
            if (ultimoAvatarSeleccionado) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(ultimoAvatarSeleccionado);
                entrada.files = dataTransfer.files;

                if (ultimoAvatarValido) {
                    $('#avatar-preview').attr('src', ultimoAvatarValido);
                    $('#avatar-preview').removeClass('d-none');
                }
            }
            return;
        }

        if (entrada.files && entrada.files[0]) {
            const archivo = entrada.files[0];
            const tiposPermitidos = ["image/jpeg", "image/jpg", "image/png", "image/gif", "image/webp"];
            const tamanoMaximo = 1 * 1024 * 1024; // 1MB

            if (!tiposPermitidos.includes(archivo.type)) {
                mostrarToast("El formato de imagen no está permitido", "error");
                entrada.value = '';
                return;
            }

            if (archivo.size > tamanoMaximo) {
                mostrarToast("La imagen no puede superar 1MB", "error");
                entrada.value = '';
                return;
            }

            ultimoAvatarSeleccionado = archivo;

            const lector = new FileReader();
            lector.onload = function (evento) {
                ultimoAvatarValido = evento.target.result;

                $('#avatar-preview').attr('src', ultimoAvatarValido)
                    .removeClass('d-none');
            };
            lector.readAsDataURL(archivo);
        }
    });

    if ($('#avatar-preview').attr('src')) {
        ultimoAvatarValido = $('#avatar-preview').attr('src');
    }
});