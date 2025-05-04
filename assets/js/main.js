// Validación de formularios con jQuery
$(document).ready(function() {
    // Formulario de registro
    const $formularioRegistro = $('form[action*="registro"]');
    if ($formularioRegistro.length) {
        $formularioRegistro.on("submit", function(e) {
            const password = $("#password").val();
            const confirmarPassword = $("#confirmar_password").val();

            if (password !== confirmarPassword) {
                e.preventDefault();
                mostrarToast("Las contraseñas no coinciden", "error");
            }

            if (password.length < 6) {
                e.preventDefault();
                mostrarToast("La contraseña debe tener al menos 6 caracteres", "error");
            }
        });
    }

    // Formulario de añadir juego
    const $formularioJuego = $('form[action*="anyadirJuego"]');
    if ($formularioJuego.length) {
        $formularioJuego.on("submit", function(e) {
            const fechaInicio = new Date($("#fecha_inicio").val());
            const fechaFin = new Date($("#fecha_fin").val());

            if (fechaFin < fechaInicio) {
                e.preventDefault();
                mostrarToast("La fecha de finalización no puede ser anterior a la fecha de inicio", "error");
            }

            // Usamos una mezcla de jQuery y vanilla JS para archivos porque jQuery no tiene un método directo para acceder a File API
            const caratula = $("#caratula")[0].files[0];
            if (caratula && caratula.size > 2 * 1024 * 1024) {
                e.preventDefault();
                mostrarToast("La imagen de carátula no puede superar los 2MB", "error");
            }
        });
    }

    // Mensajes temporales con animación jQuery
    const $mensajes = $(".mensaje");
    if ($mensajes.length > 0) {
        setTimeout(function() {
            $mensajes.animate({ opacity: 0 }, 500, function() {
                $(this).slideUp();
            });
        }, 5000);
    }

    // Validación del input del avatar en el perfil al seleccionar archivo
    $(document).on('change', '#avatar', function() {
        const entrada = this;
        const $contenedorVista = $('#avatar-preview-container');
        const $imagenVista = $('#avatar-preview-modal');
        const urlImagenActual = $imagenVista.attr('src'); // Guardar la URL actual

        if (entrada.files && entrada.files[0]) {
            const archivo = entrada.files[0];
            const tiposPermitidos = ["image/jpeg", "image/jpg", "image/png", "image/gif", "image/webp"];
            const tamanoMaximo = 1 * 1024 * 1024; // 1MB en bytes

            // 1. Validar formato (tipo MIME)
            if (!tiposPermitidos.includes(archivo.type)) {
                mostrarToast("Formato de imagen no permitido.", "error");
                entrada.value = '';
                $(entrada).val('');
                // NO ocultamos el contenedor ni cambiamos la imagen si ya había una
                return;
            }

            // 2. Validar tamaño
            if (archivo.size > tamanoMaximo) {
                const tamanoArchivoMB = (archivo.size / (1024 * 1024)).toFixed(2);
                mostrarToast(`La imagen es demasiado grande (${tamanoArchivoMB}MB).`, "error");
                entrada.value = '';
                $(entrada).val('');
                // NO ocultamos el contenedor ni cambiamos la imagen si ya había una
                return;
            }

            // --- INICIO: Lógica de vista previa ---
            const lector = new FileReader();

            lector.onload = function(e) {
                // Mostrar la imagen seleccionada en la vista previa
                $imagenVista.attr('src', e.target.result);
                $contenedorVista.removeClass('d-none'); // Mostrar contenedor
            }

            // Leer el archivo como Data URL
            lector.readAsDataURL(archivo);
            // --- FIN: Lógica de vista previa ---
        } else {
            // Si se cancela la selección, mantenemos la imagen actual si existe
            if (urlImagenActual && urlImagenActual !== '#') {
                // No hacemos nada, mantenemos la imagen actual
            } else {
                // Solo ocultamos si no había imagen previa
                $contenedorVista.addClass('d-none');
                $imagenVista.attr('src', '#');
            }
        }
    });
});

// Funciones para filtrado y ordenamiento
function filtrarJuegos(filtro) {
    // Tu código existente...
    const $contenedorJuegos = $('#juegos-container');
    const $tarjetasJuego = $('.tarjetaJuego');
    
    // Resto del código...
}

function ordenarJuegos(criterio) {
    const $contenedorJuegos = $('#juegos-container');
    const $tarjetasJuego = $('.tarjetaJuego').parent().parent();
    
    // Resto del código...
}
