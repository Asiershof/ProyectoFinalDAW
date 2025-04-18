// Validación de formularios
document.addEventListener("DOMContentLoaded", () => {
    // Formulario de registro
    const formRegistro = document.querySelector('form[action*="registro"]')
    if (formRegistro) {
        formRegistro.addEventListener("submit", (e) => {
        const password = document.getElementById("password").value
        const confirmarPassword = document.getElementById("confirmar_password").value

        if (password !== confirmarPassword) {
            e.preventDefault()
            alert("Las contraseñas no coinciden")
        }

        if (password.length < 6) {
            e.preventDefault()
            alert("La contraseña debe tener al menos 6 caracteres")
        }
        })
    }

    // Formulario de añadir juego
    const formJuego = document.querySelector('form[action*="anyadirJuego"]')
    if (formJuego) {
        formJuego.addEventListener("submit", (e) => {
        const fechaInicio = new Date(document.getElementById("fecha_inicio").value)
        const fechaFin = new Date(document.getElementById("fecha_fin").value)

        if (fechaFin < fechaInicio) {
            e.preventDefault()
            alert("La fecha de finalización no puede ser anterior a la fecha de inicio")
        }

        const caratula = document.getElementById("caratula").files[0]
        if (caratula && caratula.size > 2 * 1024 * 1024) {
            e.preventDefault()
            alert("La imagen de carátula no puede superar los 2MB")
        }
        })
    }

    // Mensajes temporales
    const mensajes = document.querySelectorAll(".mensaje")
    if (mensajes.length > 0) {
        setTimeout(() => {
        mensajes.forEach((mensaje) => {
            mensaje.style.opacity = "0"
            setTimeout(() => {
            mensaje.style.display = "none"
            }, 500)
        })
        }, 5000)
    }
})
  