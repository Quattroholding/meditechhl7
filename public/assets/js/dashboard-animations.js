/**
 * DASHBOARD ANIMATIONS
 * Efectos de animación para los dashboards médicos
 */

// Asegurar que dashboard-init esté oculto inmediatamente
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            const dashboardInit = document.querySelector('.dashboard-init');
            if (dashboardInit && !dashboardInit.classList.contains('show')) {
               // dashboardInit.style.opacity = '0';
               // dashboardInit.style.visibility = 'hidden';
            }
        });
    } else {
        const dashboardInit = document.querySelector('.dashboard-init');
        if (dashboardInit && !dashboardInit.classList.contains('show')) {
           // dashboardInit.style.opacity = '0';
            //dashboardInit.style.visibility = 'hidden';
        }
    }
})();

class DashboardAnimations {
    constructor() {
        this.typewriterComplete = false;
        this.typewriterElements = [];
        this.completedTypewriters = 0;
        this.isFirstVisit = this.checkFirstVisit();
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            //this.hideWidgetsInitially();

            if (this.isFirstVisit) {
                // Mostrar animación de saludo (viene con parámetro show_salute=true)
                this.setupTypewriter();
                this.setupGoodMorningBlock();
                //this.markVisited();
            } else {
                // Sin parámetro show_salute: ocultar good-morning y mostrar widgets directamente
                this.hideGoodMorningBlock();
               // this.showWidgets();
            }
        });
    }

    /**
     * Verificar si debe mostrar el saludo animado
     */
    checkFirstVisit() {
        // Verificar si debe mostrar saludo (parámetro URL)
        const urlParams = new URLSearchParams(window.location.search);
        const showSalute = urlParams.get('show_salute') === 'true';

        if (showSalute) {
            // Verificar si ya se ejecutó la animación en esta carga de página
            if (sessionStorage.getItem('animation_executed_this_load')) {
                return false; // Ya se ejecutó, no repetir
            }

            // Marcar que se está ejecutando la animación
            sessionStorage.setItem('animation_executed_this_load', 'true');
            return true;
        }

        // Si no hay parámetro show_salute, no mostrar animación
        return false;
    }

    /**
     * Marcar que el usuario ya visitó el dashboard
     */
    markVisited() {
        // Ya no necesitamos sessionStorage, el parámetro URL controla todo
    }

    /**
     * Ocultar el bloque good-morning inmediatamente
     */
    hideGoodMorningBlock() {
        const goodMorningBlock = document.querySelector('.good-morning-blk');
        if (goodMorningBlock) {
            goodMorningBlock.style.display = 'none';
        }
    }

    /**
     * Ocultar todos los widgets del dashboard inicialmente
     */
    hideWidgetsInitially() {
        // Ocultar el contenedor principal dashboard-init
        const dashboardInit = document.querySelector('.dashboard-init');
        if (dashboardInit) {
            // Ya está oculto por CSS, pero asegurarnos
            dashboardInit.classList.remove('show');
        }

        // Fallback: ocultar elementos específicos si no existe dashboard-init
        if (!dashboardInit) {
            const fallbackElements = document.querySelectorAll('.row:not(.good-morning-blk .row), .card:not(.good-morning-blk .card), .doctor-list-blk, .patient-widget');
            fallbackElements.forEach(element => {
                if (!element.closest('.good-morning-blk')) {
                    element.classList.add('dashboard-widgets-hidden');
                }
            });
        }
    }

    /**
     * Configurar animación de máquina de escribir
     */
    setupTypewriter() {
        this.typewriterElements = document.querySelectorAll('.typewriter-text');

        if (this.typewriterElements.length === 0) {
            // Si no hay elementos typewriter, mostrar widgets inmediatamente
            this.showWidgets();
            return;
        }

        this.typewriterElements.forEach((element, index) => {
            // Obtener el texto completo, manejando HTML
            const originalHTML = element.innerHTML;
            const text = element.textContent || element.innerText;
            const speed = 100; // Velocidad de escritura en ms

            // Guardar el texto original y HTML
            element.setAttribute('data-text', text);
            element.setAttribute('data-html', originalHTML);

            // Ocultar el contenido inmediatamente y agregar clase ready
            element.innerHTML = '';
            element.classList.add('ready');

            // Iniciar la animación con delay escalonado
            const delay = 2000 + (index * 2000); // 2 segundos inicial + 2 segundos entre elementos

            setTimeout(() => {
                // Cambiar a estado typing y comenzar animación
                element.classList.remove('ready');
                element.classList.add('typing');

                // Si el contenido original tiene HTML, usar el método especial
                if (originalHTML !== text) {
                    this.typeHTML(element, originalHTML, text, speed, index);
                } else {
                    this.typeText(element, text, speed, index);
                }
            }, delay);
        });
    }

    /**
     * Función para escribir texto letra por letra
     */
    typeText(element, text, speed, index) {
        let i = 0;

        // Limpiar el contenido y preparar para escribir
        element.textContent = '';

        const typeInterval = setInterval(() => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
            } else {
                clearInterval(typeInterval);
                // Quitar el cursor después de terminar
                setTimeout(() => {
                    element.classList.remove('typing');
                    element.classList.add('finished');

                    // Incrementar contador de typewriters completados
                    this.completedTypewriters++;

                    // Si todos los typewriters han terminado, mostrar widgets
                    if (this.completedTypewriters >= this.typewriterElements.length) {
                        this.onTypewriterComplete();
                    }
                }, 800);
            }
        }, speed);
    }

    /**
     * Función para escribir contenido HTML letra por letra
     */
    typeHTML(element, originalHTML, text, speed, index) {
        let i = 0;

        // Limpiar el contenido y preparar para escribir
        element.innerHTML = '';

        const typeInterval = setInterval(() => {
            if (i < text.length) {
                // Reconstruir el HTML progresivamente basado en el texto mostrado
                const currentText = text.substring(0, i + 1);
                const updatedHTML = this.rebuildHTMLWithText(originalHTML, currentText);
                element.innerHTML = updatedHTML;
                i++;
            } else {
                clearInterval(typeInterval);
                // Restaurar el HTML completo y quitar cursor
                setTimeout(() => {
                    element.innerHTML = originalHTML;
                    element.classList.remove('typing');
                    element.classList.add('finished');

                    // Incrementar contador de typewriters completados
                    this.completedTypewriters++;

                    // Si todos los typewriters han terminado, mostrar widgets
                    if (this.completedTypewriters >= this.typewriterElements.length) {
                        this.onTypewriterComplete();
                    }
                }, 800);
            }
        }, speed);
    }

    /**
     * Reconstruir HTML progresivamente con el texto actual
     */
    rebuildHTMLWithText(originalHTML, currentText) {
        // Crear un elemento temporal para procesar el HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = originalHTML;

        // Obtener todo el texto del HTML original
        const fullText = tempDiv.textContent || tempDiv.innerText;

        // Si el texto actual es menor que el completo, truncar
        if (currentText.length < fullText.length) {
            // Método simple: reemplazar el texto manteniendo la estructura HTML
            let updatedHTML = originalHTML;

            // Buscar y reemplazar el texto, manteniendo las etiquetas HTML
            const textNodes = this.getTextNodes(tempDiv);
            let charCount = 0;

            textNodes.forEach(node => {
                const nodeText = node.textContent;
                const nodeStart = charCount;
                const nodeEnd = charCount + nodeText.length;

                if (currentText.length > nodeStart && currentText.length <= nodeEnd) {
                    // Este es el nodo donde debemos cortar
                    const partialText = currentText.substring(nodeStart);
                    node.textContent = partialText;
                } else if (currentText.length <= nodeStart) {
                    // Este nodo debe estar vacío
                    node.textContent = '';
                }

                charCount += nodeText.length;
            });

            return tempDiv.innerHTML;
        }

        return originalHTML;
    }

    /**
     * Obtener todos los nodos de texto de un elemento
     */
    getTextNodes(element) {
        const textNodes = [];
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );

        let node;
        while (node = walker.nextNode()) {
            if (node.textContent.trim() !== '') {
                textNodes.push(node);
            }
        }

        return textNodes;
    }

    /**
     * Ejecutar cuando todos los typewriters hayan terminado
     */
    onTypewriterComplete() {
        this.typewriterComplete = true;
        setTimeout(() => {
            this.showWidgets();
        }, 500); // Pequeño delay antes de mostrar widgets
    }

    /**
     * Configurar animaciones del bloque good-morning
     */
    setupGoodMorningBlock() {
        const goodMorningBlock = document.querySelector('.good-morning-blk');

        if (goodMorningBlock) {
            // Mostrar el bloque con animación
            goodMorningBlock.classList.add('show-animation');

            // Mantener el bloque visible por 15 segundos
            setTimeout(() => {
                this.fadeOutGoodMorning(goodMorningBlock);
            }, 15000);
        }
    }

    /**
     * Desvanecer el bloque good-morning
     */
    fadeOutGoodMorning(element) {
        element.classList.add('fade-out');

        // Ocultar completamente después de la animación (2s como en CSS)
        setTimeout(() => {
            element.style.display = 'none';
            // Trigger event para que otros elementos se ajusten
            this.triggerLayoutAdjustment();
        }, 2000);
    }

    /**
     * Mostrar todos los widgets del dashboard después del typewriter
     */
    showWidgets() {
        // Buscar el contenedor principal dashboard-init
        const dashboardInit = document.querySelector('.dashboard-init');

        if (dashboardInit) {
            // Mostrar todo el contenido dashboard-init de una vez
            setTimeout(() => {
                dashboardInit.classList.add('show');

                // Agregar animaciones escalonadas a elementos internos después de que aparezca
                setTimeout(() => {
                    this.animateInternalElements(dashboardInit);
                }, 300);
            }, 200);
        } else {
            // Fallback: usar el método anterior si no existe dashboard-init
            this.showWidgetsFallback();
        }
    }

    /**
     * Animar elementos internos de dashboard-init
     */
    animateInternalElements(container) {
        // Animar contadores y cards internos con stagger
        const internalElements = container.querySelectorAll('.row, .card, .doctor-list-blk, [class*="livewire:"]');

        internalElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'all 0.5s ease-out';

            setTimeout(() => {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    /**
     * Método fallback para dashboards sin dashboard-init
     */
    showWidgetsFallback() {
        const hiddenElements = document.querySelectorAll('.dashboard-widgets-hidden');

        hiddenElements.forEach((element, index) => {
            setTimeout(() => {
                element.classList.remove('dashboard-widgets-hidden');
                element.classList.add('show');
            }, index * 100);
        });
    }

    /**
     * Ajustar layout después de ocultar good-morning
     */
    triggerLayoutAdjustment() {
        // Trigger custom event para componentes que necesiten ajustarse
        window.dispatchEvent(new CustomEvent('goodMorningHidden'));

        // Smooth scroll hacia arriba si es necesario
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        this.rest
    }

    /**
     * Función para reiniciar animaciones (útil para SPA)
     */
    static restart() {
        // Limpiar flag de animación
        sessionStorage.removeItem('animation_executed_this_load');

        // Limpiar clases de animación
        document.querySelectorAll('.typewriter-text').forEach(el => {
            el.classList.remove('typing', 'finished', 'ready');
        });

        document.querySelectorAll('.good-morning-blk').forEach(el => {
            el.classList.remove('fade-out', 'show-animation');
            el.style.display = '';
            el.style.opacity = '';
        });

        // Limpiar dashboard-init
        document.querySelectorAll('.dashboard-init').forEach(el => {
            el.classList.remove('show');
            // Limpiar estilos inline de elementos internos
            el.querySelectorAll('*').forEach(child => {
                child.style.opacity = '';
                child.style.transform = '';
                child.style.transition = '';
            });
        });

        // Limpiar widgets y animaciones (fallback)
        document.querySelectorAll('.dashboard-widgets-hidden, .dashboard-counters, .dashboard-card').forEach(el => {
            el.classList.remove('dashboard-widgets-hidden', 'show', 'animate-in', 'dashboard-counters', 'dashboard-card');
        });

        // Reiniciar
        new DashboardAnimations();
    }

    /**
     * Función para forzar mostrar animación (útil para testing)
     */
    static forceAnimation() {
        // Agregar el parámetro show_salute a la URL actual
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('show_salute', 'true');
        window.location.href = currentUrl.toString();
    }
}

// Inicializar automáticamente
const dashboardAnimations = new DashboardAnimations();

// Exportar para uso manual si es necesario
window.DashboardAnimations = DashboardAnimations;

/**
 * CONFIGURACIONES ADICIONALES
 */

// Detectar cambios de tema para ajustar colores del cursor
function adjustCursorColor() {
    const primaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--primary-color') || '#3498db';

    document.documentElement.style.setProperty('--cursor-color', primaryColor);
}

// Ejecutar al cargar y cuando cambie el tema
document.addEventListener('DOMContentLoaded', adjustCursorColor);
document.addEventListener('theme-updated', adjustCursorColor);

/**
 * UTILIDADES PARA LIVEWIRE
 */

// Limpiar flag de animación cuando se navega fuera del dashboard
window.addEventListener('beforeunload', () => {
    sessionStorage.removeItem('animation_executed_this_load');
});

// También limpiar en navegación interna
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link && link.href && !link.href.includes('dashboard')) {
        sessionStorage.removeItem('animation_executed_this_load');
    }
});

// Reiniciar animaciones cuando Livewire actualiza el DOM
document.addEventListener('livewire:navigated', () => {
    // NO reiniciar automáticamente porque interfiere con las animaciones de login
    console.log('Livewire navigated - skipping auto-restart to avoid interfering with login animations');
     setTimeout(() => {
         DashboardAnimations.restart();
     }, 10000);
});

// Compatibility con Turbo/Hotwire
document.addEventListener('turbo:load', () => {
    console.log('Turbo load - skipping auto-restart to avoid interfering with login animations');
    // DashboardAnimations.restart();
});
