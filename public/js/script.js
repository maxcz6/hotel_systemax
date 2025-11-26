document.addEventListener('DOMContentLoaded', function () {
    console.log('Native JS loaded');

    // Dropdown Logic
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (toggle && menu) {
            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(m => {
                    if (m !== menu) m.classList.remove('show');
                });
                menu.classList.toggle('show');
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    });
});
