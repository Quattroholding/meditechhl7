/**
 * DASHBOARD ANIMATIONS V2 - SMOOTH & PROFESSIONAL
 * Versión mejorada con animaciones más suaves y profesionales
 */

class DashboardAnimationsV2 {
    constructor() {
        this.animationComplete = false;
        this.isFirstVisit = this.checkFirstVisit();
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DashboardAnimationsV2 init - isFirstVisit:', this.isFirstVisit);

            if (this.isFirstVisit) {
                // Mostrar animación de saludo suave
                console.log('Showing welcome animation...');
                this.setupSmoothWelcome();
            } else {
                // Ocultar good-morning y mostrar widgets directamente
                console.log('Skipping animation, showing widgets directly...');
                this.hideGoodMorningBlock();
                this.showWidgetsDirectly();
            }
        });
    }

    /**
     * Verificar si debe mostrar el saludo animado
     */
    checkFirstVisit() {
        const urlParams = new URLSearchParams(window.location.search);
        const showSalute = urlParams.get('show_salute') === 'true';

        console.log('checkFirstVisit - URL params:', window.location.search);
        console.log('checkFirstVisit - show_salute value:', urlParams.get('show_salute'));
        console.log('checkFirstVisit - showSalute === true:', showSalute);

        if (showSalute) {
            const alreadyExecuted = sessionStorage.getItem('animation_v2_executed_this_load');
            console.log('checkFirstVisit - Already executed:', alreadyExecuted);

            if (alreadyExecuted) {
                console.log('checkFirstVisit - Animation already executed, skipping');
                return false;
            }

            sessionStorage.setItem('animation_v2_executed_this_load', 'true');
            console.log('checkFirstVisit - Will show animation');
            return true;
        }

        console.log('checkFirstVisit - No show_salute param, skipping animation');
        return false;
    }

    /**
     * Ocultar el bloque good-morning inmediatamente
     */
    hideGoodMorningBlock() {
        const goodMorningBlock = document.querySelector('.good-morning-blk-v2');
        if (goodMorningBlock) {
            goodMorningBlock.style.display = 'none';
        }
    }

    /**
     * Configurar animación de bienvenida suave (sin typewriter)
     */
    setupSmoothWelcome() {
        const goodMorningBlock = document.querySelector('.good-morning-blk-v2');
        const dashboardInit = document.querySelector('.dashboard-init-v2');

        if (!goodMorningBlock) {
            this.showWidgetsDirectly();
            return;
        }

        // Ocultar los widgets mientras se muestra la animación
        if (dashboardInit) {
            dashboardInit.classList.add('hide');
        }

        // Agregar partículas flotantes (opcional)
        this.addFloatingParticles(goodMorningBlock);

        // Mostrar el bloque con animación suave
        setTimeout(() => {
            goodMorningBlock.classList.add('show-animation');

            // Animar el texto con reveal suave
            this.animateTextReveal();

            // Mantener el bloque visible por 8 segundos (más corto que la versión original)
            setTimeout(() => {
                this.fadeOutWelcome(goodMorningBlock);
            }, 8000);
        }, 300);
    }

    /**
     * Agregar partículas flotantes decorativas
     */
    addFloatingParticles(container) {
        const particlesContainer = document.createElement('div');
        particlesContainer.className = 'particles';

        // Crear 5 partículas
        for (let i = 0; i < 5; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particlesContainer.appendChild(particle);
        }

        container.appendChild(particlesContainer);
    }

    /**
     * Animar texto con reveal suave (sin efecto typewriter)
     */
    animateTextReveal() {
        const textElements = document.querySelectorAll('.smooth-text-reveal');

        textElements.forEach((element, index) => {
            setTimeout(() => {
                element.classList.add('animate-in');
            }, 600 + (index * 300)); // Delay escalonado entre elementos
        });
    }

    /**
     * Desvanecer el bloque de bienvenida suavemente
     */
    fadeOutWelcome(element) {
        element.classList.add('fade-out');

        // Ocultar completamente después de la animación
        setTimeout(() => {
            element.style.display = 'none';
            this.showWidgets();
            this.triggerLayoutAdjustment();
        }, 1500); // Duración de la animación fade-out
    }

    /**
     * Mostrar widgets con animación en cascada
     */
    showWidgets() {
        const dashboardInit = document.querySelector('.dashboard-init-v2');

        if (dashboardInit) {
            // Mostrar el contenedor principal
            setTimeout(() => {
                dashboardInit.classList.remove('hide');
                dashboardInit.classList.add('show');

                // Animar widgets internos en cascada
                setTimeout(() => {
                    this.animateWidgetsCascade(dashboardInit);
                }, 400);
            }, 200);
        } else {
            // Fallback para versión sin dashboard-init-v2
            this.showWidgetsFallback();
        }
    }

    /**
     * Mostrar widgets directamente sin animación de bienvenida
     */
    showWidgetsDirectly() {
        const dashboardInit = document.querySelector('.dashboard-init-v2');

        if (dashboardInit) {
            dashboardInit.classList.add('show');
            // Sin animación en cascada cuando se muestra directamente
            const widgets = dashboardInit.querySelectorAll('.widget-cascade-enter');
            widgets.forEach(widget => {
                widget.style.opacity = '1';
                widget.style.transform = 'none';
            });
        }
    }

    /**
     * Animar widgets en cascada
     */
    animateWidgetsCascade(container) {
        const widgets = container.querySelectorAll('.row, .card, [class*="dashboard-tile"]');

        widgets.forEach((widget, index) => {
            // Agregar clase para animación en cascada
            widget.classList.add('widget-cascade-enter');

            setTimeout(() => {
                widget.classList.add('animate');
            }, index * 80); // Delay más corto para efecto más fluido
        });
    }

    /**
     * Método fallback para dashboards antiguos
     */
    showWidgetsFallback() {
        const widgets = document.querySelectorAll('.row, .card, .doctor-list-blk');

        widgets.forEach((widget, index) => {
            widget.style.opacity = '0';
            widget.style.transform = 'translateY(30px)';
            widget.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';

            setTimeout(() => {
                widget.style.opacity = '1';
                widget.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    /**
     * Ajustar layout después de ocultar welcome
     */
    triggerLayoutAdjustment() {
        window.dispatchEvent(new CustomEvent('welcomeV2Hidden'));

        // Smooth scroll suave hacia arriba
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    /**
     * Reiniciar animaciones
     */
    static restart() {
        sessionStorage.removeItem('animation_v2_executed_this_load');

        // Limpiar clases de animación
        document.querySelectorAll('.smooth-text-reveal').forEach(el => {
            el.classList.remove('animate-in');
        });

        document.querySelectorAll('.good-morning-blk-v2').forEach(el => {
            el.classList.remove('fade-out', 'show-animation');
            el.style.display = '';
            el.style.opacity = '';

            // Remover partículas existentes
            const particles = el.querySelector('.particles');
            if (particles) {
                particles.remove();
            }
        });

        document.querySelectorAll('.dashboard-init-v2').forEach(el => {
            el.classList.remove('show');
            el.querySelectorAll('*').forEach(child => {
                child.style.opacity = '';
                child.style.transform = '';
                child.style.transition = '';
                child.classList.remove('widget-cascade-enter', 'animate');
            });
        });

        // Reiniciar
        new DashboardAnimationsV2();
    }

    /**
     * Forzar mostrar animación
     */
    static forceAnimation() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('show_salute', 'true');
        window.location.href = currentUrl.toString();
    }
}

// Inicializar automáticamente cuando el DOM esté listo
let dashboardAnimationsV2;

function initDashboardV2() {
    if (typeof DashboardAnimationsV2 !== 'undefined') {
        dashboardAnimationsV2 = new DashboardAnimationsV2();
        console.log('Dashboard Animations V2 initialized');
    }
}

// Múltiples formas de inicializar para asegurar que funcione
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardV2);
} else {
    // DOM ya está listo
    initDashboardV2();
}

// Exportar para uso manual
window.DashboardAnimationsV2 = DashboardAnimationsV2;

/**
 * CONFIGURACIONES ADICIONALES
 */

// Limpiar flag al salir de la página
window.addEventListener('beforeunload', () => {
    sessionStorage.removeItem('animation_v2_executed_this_load');
});

// Limpiar en navegación interna
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link && link.href && !link.href.includes('dashboard')) {
        sessionStorage.removeItem('animation_v2_executed_this_load');
    }
});

// Integración con Livewire
document.addEventListener('livewire:navigated', () => {
    console.log('Livewire navigated - Dashboard Animations V2');
    setTimeout(() => {
        DashboardAnimationsV2.restart();
    }, 100);
});

// Soporte para intersección (lazy loading de widgets)
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '50px'
    });

    // Observar widgets cuando aparezcan en viewport
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.querySelectorAll('.widget-cascade-enter').forEach(widget => {
                observer.observe(widget);
            });
        }, 2000);
    });
}

/**
 * UTILIDADES ADICIONALES
 */

// Función helper para agregar efecto shimmer a elementos cargando
function addShimmerEffect(element) {
    if (element) {
        element.classList.add('shimmer-effect');

        // Remover después de cargar
        const removeShimmer = () => {
            element.classList.remove('shimmer-effect');
        };

        element.addEventListener('load', removeShimmer);
        element.addEventListener('animationend', removeShimmer);
    }
}

// Función para animar cambios de número (counters)
function animateCounter(element, start, end, duration = 1000) {
    const range = end - start;
    const increment = range / (duration / 16); // 60fps
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.round(current);
    }, 16);
}

// Exportar utilidades
window.DashboardUtils = {
    addShimmerEffect,
    animateCounter
};
