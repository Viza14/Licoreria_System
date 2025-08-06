<?php
/**
 * CONFIGURACIÓN DE URL BASE
 * 
 * Este archivo te permite controlar cómo se genera la URL base de tu aplicación.
 * Puedes usar detección automática o configurar manualmente la URL.
 */

// ============================================================================
// CONFIGURACIÓN MANUAL DE URL
// ============================================================================

/**
 * INSTRUCCIONES:
 * 1. Para usar detección automática: deja $MANUAL_BASE_URL = null;
 * 2. Para configurar manualmente: asigna tu URL a $MANUAL_BASE_URL
 */

// Configuración de URL base (cambia según tu entorno)
$MANUAL_BASE_URL = null; // null = detección automática

// EJEMPLOS DE CONFIGURACIÓN:
// $MANUAL_BASE_URL = 'https://midominio.com/';                    // Dominio propio
// $MANUAL_BASE_URL = 'https://midominio.com/licoreria/';          // Subdirectorio
// $MANUAL_BASE_URL = 'http://192.168.1.100/licoreria/';          // IP en red local
// $MANUAL_BASE_URL = 'http://localhost:8080/licoreria/';          // Puerto específico
// $MANUAL_BASE_URL = 'https://licoreria.midominio.com/';         // Subdominio

// ============================================================================
// FUNCIÓN PARA OBTENER LA URL BASE
// ============================================================================

function getBaseUrl() {
    global $MANUAL_BASE_URL;
    
    // Si NO hay una URL manual definida, DEJA COMO ESTA
    if ($MANUAL_BASE_URL !== null) {
        return $MANUAL_BASE_URL;
    }
/*     // Si hay una URL manual definida, usarla AQUÍ (DESCOMENTA EL CODIGO)
    if ($MANUAL_BASE_URL !== null) {
        return $MANUAL_BASE_URL;
    } */
    
    // Detección automática (funciona en la mayoría de casos)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = dirname($script);
    
    // Normalizar la ruta (eliminar barras dobles y asegurar que termine en /)
    $path = str_replace('\\', '/', $path);
    $path = rtrim($path, '/') . '/';
    
    return $protocol . $host . $path;
}

// ============================================================================
// EJEMPLOS DE CONFIGURACIÓN PARA DIFERENTES ENTORNOS
// ============================================================================

/*
DESARROLLO LOCAL:
$MANUAL_BASE_URL = null; // Detección automática
- http://localhost:8000/ (si usas php -S localhost:8000)
- http://localhost/licoreria/ (si usas XAMPP/WAMP)

DESARROLLO CON IP EN RED LOCAL:
$MANUAL_BASE_URL = 'http://192.168.1.100/licoreria/';

SERVIDOR DE PRUEBAS:
$MANUAL_BASE_URL = 'http://test.midominio.com/licoreria/';

PRODUCCIÓN:
$MANUAL_BASE_URL = 'https://www.midominio.com/';

SUBDOMINIO:
$MANUAL_BASE_URL = 'https://licoreria.midominio.com/';

HOSTING COMPARTIDO:
$MANUAL_BASE_URL = 'https://midominio.com/public_html/licoreria/';
*/