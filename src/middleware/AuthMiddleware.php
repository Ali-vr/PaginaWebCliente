<?php
namespace App\Middleware;

class AuthMiddleware {
    /**
     * Protege una ruta para que solo puedan ingresar usuarios autenticados.
     */
    public static function verificarSesion(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si el usuario NO está autenticado
        if (!isset($_SESSION['usuario']) && !isset($_SESSION['user_id'])) {
            // Evitar que la página quede almacenada en el historial en caché del navegador
            self::deshabilitarCache();
            
            header('Location: /login');
            exit;
        }

        // Si está autenticado, igualmente desactivamos caché en zonas privadas
        self::deshabilitarCache();
    }

    /**
     * Evita que el navegador guarde la página en caché (Solución al botón "Atrás")
     */
    public static function deshabilitarCache(): void {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }
}
