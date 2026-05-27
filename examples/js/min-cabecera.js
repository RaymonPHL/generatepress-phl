window.addEventListener('scroll', function () {
    const distanciaScroll = window.scrollY;
    const logos = document.getElementsByClassName('header-image is-logo-image');
    const branding = document.querySelector('.inside-header.grid-container');
    const header = document.querySelector('#masthead');
    const primaryheader = document.querySelector('.ast-primary-header-bar .site-primary-header-wrap');
    const content = document.querySelector('.entry-content.clear');

    if (distanciaScroll > 1) {
        // Reducir logo con animación
        Array.from(logos).forEach(function (logo) {
            logo.style.transition = 'all 0.3s ease-in-out';
            logo.style.maxWidth = '88px';
            logo.style.marginLeft = '2rem';
        });

        // Quitar padding del contenedor
        if (branding) {
            branding.style.transition = 'all 0.3s ease-in-out';
            branding.style.padding = '0';
        }

        // Hacer fixed la cabecera
        if (header) {
            header.style.position = 'fixed';
            header.style.top = '0';
            header.style.zIndex = '9999';
            header.style.width = '100%';
        }

    } else {
        // Restablecer logo
        Array.from(logos).forEach(function (logo) {
            logo.style.maxWidth = '177px';
            logo.style.marginLeft = '0';
        });

        // Restablecer padding del contenedor
        if (branding) {
            branding.style.removeProperty('padding');
        }

        // Restaurar posición de la cabecera
        if (header) {
            header.style.position = 'unset';
        }
    }
});