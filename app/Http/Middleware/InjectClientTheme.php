<?php

namespace App\Http\Middleware;

use App\Models\ClientTheme;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectClientTheme
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo aplicar en respuestas HTML y para usuarios autenticados
        if (! auth()->check() ||
            ! $response instanceof \Illuminate\Http\Response ||
            ! str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        try {
            // Obtener el cliente actual del usuario
            $user = auth()->user();
            $currentClient = $this->getCurrentClient($user);

            if (! $currentClient) {
                return $response;
            }

            // Obtener el tema del cliente
            $theme = ClientTheme::getActiveForClient($currentClient->id);

            if (! $theme) {
                return $response;
            }

            // Generar CSS del tema
            $themeCSS = $this->generateThemeCSS($theme);

            // Inyectar CSS en el HTML
            $content = $response->getContent();
            $content = $this->injectCSS($content, $themeCSS);
            $response->setContent($content);

        } catch (\Exception $e) {
            // En caso de error, simplemente retornar la respuesta original
            // para evitar romper la aplicación
            logger()->error('Error inyectando tema del cliente: '.$e->getMessage());
        }

        return $response;
    }

    /**
     * Obtener el cliente actual del usuario
     */
    private function getCurrentClient($user)
    {
        // Si el usuario tiene un cliente por defecto, usarlo
        if ($user->default_client_id) {
            return $user->clients()->where('client_id', $user->default_client_id)->first();
        }

        // Si no, usar el primer cliente disponible
        return $user->clients()->first();
    }

    /**
     * Generar CSS del tema
     */
    private function generateThemeCSS(ClientTheme $theme): string
    {
        $css = '<style id="client-theme">';

        // Agregar variables CSS
        $variables = $theme->getCssVariables();
        $css .= ':root {';
        foreach ($variables as $property => $value) {
            $css .= $property.': '.$value.';';
        }
        $css .= '}';

        // Agregar estilos específicos para elementos comunes
        $css .= '
        /* Botones primarios */
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .btn-primary:hover {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            opacity: 0.9;
        }

        /* Botones secundarios */
        .btn-secondary {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }

        /* Enlaces */
        a {
            color: var(--primary-color) !important;
        }

        /* Sidebar */
        .sidebar {
            background-color: var(--sidebar-color) !important;
        }

        .sidebar, .sidebar .nav-link, .sidebar .nav-item a,
        .sidebar .menu-item, .sidebar .menu-text,
        .sidebar .sidebar-nav a, .sidebar .sidebar-menu a,
        .sidebar .nav a, .sidebar ul li a {
            color: var(--sidebar-text-color) !important;
        }

        .sidebar .nav-link:hover, .sidebar .nav-item a:hover,
        .sidebar .menu-item:hover, .sidebar .sidebar-nav a:hover,
        .sidebar .sidebar-menu a:hover, .sidebar .nav a:hover,
        .sidebar ul li a:hover {
            color: var(--sidebar-text-color) !important;
            opacity: 0.8;
        }
        
        /* Iconos del sidebar */
        .sidebar i, .sidebar .fas, .sidebar .far, .sidebar .fab,
        .sidebar .nav-link i, .sidebar .menu-item i,
        .sidebar .sidebar-nav i, .sidebar .sidebar-menu i,
        .sidebar ul li i, .sidebar .nav i {
            color: var(--sidebar-text-color) !important;
        }
        
        .sidebar .nav-link:hover i, .sidebar .menu-item:hover i,
        .sidebar .sidebar-nav:hover i, .sidebar .sidebar-menu:hover i,
        .sidebar ul li:hover i, .sidebar .nav:hover i {
            color: var(--sidebar-text-color) !important;
            opacity: 0.8;
        }

        /* Header */
        .header, .navbar {
            background-color: var(--header-color) !important;
        }

        /* Alertas de estado */
        .alert-success {
            background-color: '.$this->hexToRgba($theme->success_color, 0.1).' !important;
            border-color: var(--success-color) !important;
            color: var(--success-color) !important;
        }

        .alert-warning {
            background-color: '.$this->hexToRgba($theme->warning_color, 0.1).' !important;
            border-color: var(--warning-color) !important;
            color: var(--warning-color) !important;
        }

        .alert-danger {
            background-color: '.$this->hexToRgba($theme->danger_color, 0.1).' !important;
            border-color: var(--danger-color) !important;
            color: var(--danger-color) !important;
        }

        .alert-info {
            background-color: '.$this->hexToRgba($theme->info_color, 0.1).' !important;
            border-color: var(--info-color) !important;
            color: var(--info-color) !important;
        }

        /* Cards */
        .card {
            border-color: var(--border-color) !important;
        }

        /* Texto */
        body {
            font-family: var(--font-family) !important;
            color: var(--text-color) !important;
        }

        /* Fondo */
        .main-wrapper, .content {
            background-color: var(--background-color) !important;
        }

        /* Tablas */
        .table thead th {
            background-color: var(--table-header-color) !important;
            color: white !important;
        }

        /* Bordes */
        .border, .card, .table, .form-control {
            border-color: var(--border-color) !important;
        }

         #toggle_btn {
            padding:0!important;
        }

        #toggle_btn i{
            color:#fff;
            padding:0!important;
        }

        .top-nav-search .form-control,.btn-light {
           background-color: '.$this->hexToRgba($theme->primary_color, 0.1).' !important;
        }
        ';

        // Agregar CSS personalizado si existe
        if ($theme->custom_css) {
            $css .= "\n/* CSS Personalizado */\n".$theme->custom_css;
        }

        $css .= '</style>';

        return $css;
    }

    /**
     * Inyectar CSS en el contenido HTML
     */
    private function injectCSS(string $content, string $css): string
    {
        // Eliminar cualquier tema anterior
        $content = preg_replace('/<style id="client-theme">.*?<\/style>/s', '', $content);

        // Inyectar el nuevo CSS antes del cierre de </head>
        $content = str_replace('</head>', $css."\n</head>", $content);

        return $content;
    }

    /**
     * Convertir color hex a rgba
     */
    private function hexToRgba(string $hex, float $alpha = 1): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6) {
            return 'rgba(0, 0, 0, '.$alpha.')';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba($r, $g, $b, $alpha)";
    }
}
