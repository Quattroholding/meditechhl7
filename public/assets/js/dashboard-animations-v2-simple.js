/**
 * DASHBOARD ANIMATIONS V2 - SIMPLE VERSION
 * Versión simplificada y más robusta
 */

(function() {
    'use strict';

    console.log('Dashboard Animations V2 Simple - Loading...');

    // Verificar si debe mostrar animación
    function shouldShowAnimation() {
        const urlParams = new URLSearchParams(window.location.search);
        const showSalute = urlParams.get('show_salute') === 'true';
        const alreadyShown = sessionStorage.getItem('animation_v2_shown');

        console.log('Should show animation?', {
            showSalute,
            alreadyShown,
            urlSearch: window.location.search
        });

        if (showSalute && !alreadyShown) {
            sessionStorage.setItem('animation_v2_shown', 'true');
            return true;
        }

        return false;
    }

    // Inicializar cuando el DOM esté listo
    function init() {
        console.log('Dashboard Animations V2 - Init');

        const welcomeBlock = document.querySelector('.good-morning-blk-v2');
        const dashboardContent = document.querySelector('.dashboard-init-v2');

        console.log('Elements found:', {
            welcomeBlock: !!welcomeBlock,
            dashboardContent: !!dashboardContent
        });

        if (!welcomeBlock || !dashboardContent) {
            console.warn('Required elements not found');
            if (dashboardContent) {
                showContentImmediate(dashboardContent);
            }
            return;
        }

        if (shouldShowAnimation()) {
            console.log('Starting welcome animation...');
            showWelcomeAnimation(welcomeBlock, dashboardContent);
        } else {
            console.log('Skipping animation, showing content directly');
            hideWelcomeImmediate(welcomeBlock);
            showContentImmediate(dashboardContent);
        }
    }

    // Mostrar animación de bienvenida
    function showWelcomeAnimation(welcomeBlock, dashboardContent) {
        // Ocultar contenido del dashboard
        dashboardContent.classList.add('hide');

        // Obtener duración personalizada del atributo data
        const duration = parseInt(welcomeBlock.getAttribute('data-duration')) || 8000;
        console.log('Animation duration:', duration);

        // Mostrar welcome block con animación
        welcomeBlock.style.display = 'block';

        setTimeout(() => {
            welcomeBlock.classList.add('show-animation');
            animateText();
        }, 100);

        // Después de la duración especificada, ocultar welcome y mostrar dashboard
        setTimeout(() => {
            hideWelcome(welcomeBlock);
            setTimeout(() => {
                showContent(dashboardContent);
            }, 1500);
        }, duration);
    }

    // Animar texto con reveal
    function animateText() {
        const textElements = document.querySelectorAll('.smooth-text-reveal');
        console.log('Animating text elements:', textElements.length);

        textElements.forEach((element, index) => {
            setTimeout(() => {
                element.classList.add('animate-in');
            }, 600 + (index * 300));
        });
    }

    // Ocultar welcome block
    function hideWelcome(welcomeBlock) {
        // Agregar clase fade-out que también colapsa el espacio
        welcomeBlock.classList.add('fade-out');

        // Después de la animación, remover completamente del DOM
        setTimeout(() => {
            welcomeBlock.style.display = 'none';
            // Remover el elemento del DOM completamente para no dejar espacio
            if (welcomeBlock.parentNode) {
                welcomeBlock.parentNode.removeChild(welcomeBlock);
            }
        }, 1500);
    }

    // Mostrar contenido del dashboard con animación
    function showContent(dashboardContent) {
        console.log('Showing dashboard content with animation');
        dashboardContent.classList.remove('hide');
        dashboardContent.classList.add('show');
    }

    // Mostrar contenido inmediatamente (sin animación)
    function showContentImmediate(dashboardContent) {
        console.log('Showing dashboard content immediately');
        dashboardContent.style.opacity = '1';
        dashboardContent.style.visibility = 'visible';
        dashboardContent.style.transform = 'translateY(0)';
        dashboardContent.style.pointerEvents = 'auto';
    }

    // Ocultar welcome inmediatamente (sin animación)
    function hideWelcomeImmediate(welcomeBlock) {
        welcomeBlock.style.display = 'none';
    }

    // Limpiar flag al navegar
    window.addEventListener('beforeunload', () => {
        sessionStorage.removeItem('animation_v2_shown');
    });

    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Exportar función para forzar animación
    window.forceWelcomeAnimation = function() {
        sessionStorage.removeItem('animation_v2_shown');
        location.href = location.pathname + '?show_salute=true';
    };

    console.log('Dashboard Animations V2 Simple - Ready');
})();
