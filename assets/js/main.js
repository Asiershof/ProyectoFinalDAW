document.addEventListener('DOMContentLoaded', function() {
    const userInfo = document.querySelector('.user-info');
    const dropdown = document.querySelector('.dropdown');

    if (userInfo && dropdown) {
        userInfo.addEventListener('click', function() {
            dropdown.classList.toggle('visible');
        });

        // Cerrar el menú desplegable si se hace clic fuera de él
        document.addEventListener('click', function(event) {
            if (!userInfo.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('visible');
            }
        });
    }
});
