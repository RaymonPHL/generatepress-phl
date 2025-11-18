<?php
/*
Theme Name: GeneratePress PHL
Theme URI: http://phl.cc
Author: RaymonPHL
Author URI: http://phl.cc
Description: Tema hijo de GeneratePress de PHL.cc
Text Domain: generatepress-phl
Template: generatepress
*/

// Incluir TODOS los archivos JS del directorio /resources/js
function add_ALL_scripts() {
    $js_dir = get_stylesheet_directory() . '/resources/js/';
    $js_url = get_stylesheet_directory_uri() . '/resources/js/';
    
    // Definir si estamos en desarrollo
    $WebEnDesarrollo = file_exists(get_stylesheet_directory() . '/debug.flag') || 
                   (defined('WP_DEBUG') && WP_DEBUG) ||
                   (defined('WP_ENV') && WP_ENV !== 'production');
    
    if (!$WebEnDesarrollo) {
        // MODO MINIFICADO: Combinar y minificar todos los JS
        $combined_js = combine_and_minify_js($js_dir);
        
        if ($combined_js) {
            wp_enqueue_script(
                'all-scripts-minified',
                $combined_js,
                array('jquery'), // Dependencia de jQuery
                filemtime($combined_js), // Usar timestamp para cache busting
                true // Cargar en el footer
            );
        }
    } else {
        // MODO NORMAL: Cargar archivos individualmente
        $js_files = scandir($js_dir);
        
        foreach ($js_files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'js' && $file !== '.' && $file !== '..') {
                $handle = 'script-' . pathinfo($file, PATHINFO_FILENAME);
                wp_enqueue_script(
                    $handle,
                    $js_url . $file,
                    array('jquery'), // Dependencia de jQuery
                    filemtime($js_dir . $file), // Cache busting
                    true // Cargar en el footer
                );
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'add_ALL_scripts');