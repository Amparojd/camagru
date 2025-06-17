// Funcionalidad del sidebar móvil
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            toggleBtn.classList.toggle('active');
        });

        // Cerrar el sidebar al hacer clic fuera de él
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                toggleBtn.classList.remove('active');
            }
        });
    }
});