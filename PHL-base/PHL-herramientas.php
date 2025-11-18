<?php

// FUNCIONES GENERALES
// ===========================
// Función para comprobar si un plugin está activo
function plugin_activo( $plugin ){
    return in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||  ( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) );   
}

// FUNCIONES AUXILIARES de CSS
// ===========================
// Función para combinar y minificar CSS
function combine_and_minify_css($css_dir) {
    $css_files = scandir($css_dir);
    $combined_content = '';
    $files_to_combine = array();
    
    // Recopilar archivos CSS
    foreach ($css_files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'css' && $file !== '.' && $file !== '..') {
            $files_to_combine[] = $file;
            $file_content = file_get_contents($css_dir . $file);
            $combined_content .= "/* === $file === */\n" . $file_content . "\n";
        }
    }
    
    if (empty($files_to_combine)) {
        return false;
    }
    
    // Minificar el CSS combinado
    $minified_css = minify_css($combined_content);
    
    // Crear nombre de archivo único basado en los archivos combinados
    $combined_filename = 'PHL-combined-' . md5(implode('', $files_to_combine)) . '.min.css';
    
    // Crear directorio si no existe
    $cache_dir = get_stylesheet_directory() . '/cache/css/';
    wp_mkdir_p($cache_dir);
    
    // Añadir archivos de seguridad si el directorio era nuevo
    if (!file_exists($cache_dir . 'index.php')) {
        file_put_contents($cache_dir . 'index.php', '<?php // Silence is golden');
    }
    if (!file_exists($cache_dir . '.htaccess')) {
        file_put_contents($cache_dir . '.htaccess', "Options -Indexes\nDeny from all");
    }
    
    $combined_path = $cache_dir . $combined_filename;
    $combined_url = get_stylesheet_directory_uri() . '/cache/css/' . $combined_filename;
    
    // Guardar el archivo combinado (si no existe o ha cambiado)
    if (!file_exists($combined_path) || file_get_contents($combined_path) !== $minified_css) {
        $result = file_put_contents($combined_path, $minified_css);
        if ($result === false) {
            // Fallback: cargar archivos individualmente si no se pudo guardar
            return false;
        }
    }
    
    return $combined_url;
}

// Función para minificar CSS
function minify_css($css) {
    // Eliminar comentarios
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Eliminar espacios, tabs, newlines, etc.
    $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
    
    // Eliminar espacios innecesarios
    $css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $css);
    $css = preg_replace('/\s\s+(.*)/', '$1', $css);
    
    // Eliminar último punto y coma en cada bloque
    $css = str_replace(';}', '}', $css);
    
    return trim($css);
}

// Opcional: Limpiar archivos combinados antiguos periódicamente
function clean_old_combined_css() {
    $css_dir_cached = get_stylesheet_directory() . '/cache/css/';
    $files = glob($css_dir_cached . 'PHL-combined-*.min.css');
    
    // Mantener solo los últimos 5 archivos combinados
    if (count($files) > 5) {
        // Ordenar por fecha de modificación (más antiguos primero)
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Eliminar los más antiguos (excepto los 5 más recientes)
        for ($i = 0; $i < count($files) - 5; $i++) {
            unlink($files[$i]);
        }
    }
}

// Ejecutar limpieza ocasionalmente
add_action('wp', 'clean_old_combined_css');


// FUNCIONES AUXILIARES de FUENTES
// ===============================
function get_font_format($extension) {
    $formats = [
        'woff' => 'woff',
        'woff2' => 'woff2',
        'ttf' => 'truetype',
        'otf' => 'opentype',
        'eot' => 'embedded-opentype'
    ];
    return $formats[$extension] ?? 'truetype';
}

function detect_font_weight_style($font_name) {
    $weight = 400;
    $style = 'normal';

    $lower_name = strtolower($font_name);

    // Detectar peso
    if (strpos($lower_name, 'thin') !== false) $weight = 100;
    elseif (strpos($lower_name, 'extralight') !== false || strpos($lower_name, 'ultralight') !== false) $weight = 200;
    elseif (strpos($lower_name, 'light') !== false) $weight = 300;
    elseif (strpos($lower_name, 'regular') !== false || strpos($lower_name, 'normal') !== false || strpos($lower_name, 'book') !== false) $weight = 400;
    elseif (strpos($lower_name, 'medium') !== false) $weight = 500;
    elseif (strpos($lower_name, 'semibold') !== false || strpos($lower_name, 'demi') !== false) $weight = 600;
    elseif (strpos($lower_name, 'bold') !== false) $weight = 700;
    elseif (strpos($lower_name, 'extrabold') !== false || strpos($lower_name, 'ultrabold') !== false) $weight = 800;
    elseif (strpos($lower_name, 'black') !== false || strpos($lower_name, 'heavy') !== false) $weight = 900;

    // Detectar estilo
    if (strpos($lower_name, 'italic') !== false) $style = 'italic';
    elseif (strpos($lower_name, 'oblique') !== false) $style = 'oblique';

    return [
        'weight' => $weight,
        'style' => $style
    ];
}

// FUNCIONES AUXILIARES de JS
// ===========================
// Función para combinar y minificar JS
function combine_and_minify_js($js_dir) {
    $js_files = scandir($js_dir);
    $combined_content = '';
    $files_to_combine = array();
    
    // Recopilar archivos JS
    foreach ($js_files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'js' && $file !== '.' && $file !== '..') {
            $files_to_combine[] = $file;
            $file_content = file_get_contents($js_dir . $file);
            $combined_content .= "/* === $file === */\n" . $file_content . "\n";
        }
    }
    
    if (empty($files_to_combine)) {
        return false;
    }
    
    // Minificar el JS combinado
    $minified_js = minify_js($combined_content);
    
    // Crear nombre de archivo único basado en los archivos combinados
    $combined_filename = 'PHL-combined-' . md5(implode('', $files_to_combine)) . '.min.js';
    
    // Crear directorio si no existe
    $cache_dir = get_stylesheet_directory() . '/cache/js/';
    wp_mkdir_p($cache_dir);
    
    // Añadir archivos de seguridad si el directorio era nuevo
    if (!file_exists($cache_dir . 'index.php')) {
        file_put_contents($cache_dir . 'index.php', '<?php // Silence is golden');
    }
    if (!file_exists($cache_dir . '.htaccess')) {
        file_put_contents($cache_dir . '.htaccess', "Options -Indexes\nDeny from all");
    }
    
    $combined_path = $cache_dir . $combined_filename;
    $combined_url = get_stylesheet_directory_uri() . '/cache/js/' . $combined_filename;
    
    // Guardar el archivo combinado (si no existe o ha cambiado)
    if (!file_exists($combined_path) || file_get_contents($combined_path) !== $minified_js) {
        $result = file_put_contents($combined_path, $minified_js);
        if ($result === false) {
            // Fallback: cargar archivos individualmente si no se pudo guardar
            return false;
        }
    }
    
    return $combined_url;
}

// Función para minificar JS (básica)
function minify_js($js) {
    // Eliminar comentarios de una línea
    $js = preg_replace('!/\*.*?\*/!s', '', $js);
    $js = preg_replace('!//.*?\n!', "\n", $js);
    
    // Eliminar espacios innecesarios
    $js = preg_replace('/\s*([{}()\[\]<>:;=+\-*\/,%&|!?])\s*/', '$1', $js);
    
    // Eliminar espacios alrededor de puntos
    $js = preg_replace('/\s*\.\s*/', '.', $js);
    
    // Eliminar punto y coma finales antes de }
    $js = preg_replace('/;\s*}/', '}', $js);
    
    // Eliminar múltiples newlines
    $js = preg_replace('/\n+/', "\n", $js);
    
    return trim($js);
}

// Opcional: Limpiar archivos combinados antiguos periódicamente
function clean_old_combined_js() {
    $js_dir_cached = get_stylesheet_directory() . '/cache/js/';
    $files = glob($js_dir_cached . 'PHL-combined-*.min.js');
    
    // Mantener solo los últimos 5 archivos combinados
    if (count($files) > 5) {
        // Ordenar por fecha de modificación (más antiguos primero)
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Eliminar los más antiguos (excepto los 5 más recientes)
        for ($i = 0; $i < count($files) - 5; $i++) {
            unlink($files[$i]);
        }
    }
}

// Ejecutar limpieza ocasionalmente
add_action('wp', 'clean_old_combined_js');