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

// Incluir los styles PHL
// ===========================
// Incluir TODOS los archivos CSS del directorio /resources/css
function add_ALL_styles() {
    $css_dir = get_stylesheet_directory() . '/resources/css/';
    $css_url = get_stylesheet_directory_uri() . '/resources/css/';
    
    // Definir si estamos en producción (ajusta según tu entorno)
    $WebEnDesarrollo = file_exists(get_stylesheet_directory() . '/debug.flag') || 
                   (defined('WP_DEBUG') && WP_DEBUG) ||
                   (defined('WP_ENV') && WP_ENV !== 'production'); // Puedes crear este archivo para desarrollo
    
    // $WebEnProduccion=false;
    if (!$WebEnDesarrollo) {
        // MODO MINIFICADO: Combinar y minificar todos los CSS
        $combined_css = combine_and_minify_css($css_dir);
        
        if ($combined_css) {
            wp_enqueue_style(
                'all-styles-minified',
                $combined_css
            );
        }
    } else {
        // MODO NORMAL: Cargar archivos individualmente
        $css_files = scandir($css_dir);
        
        foreach ($css_files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'css' && $file !== '.' && $file !== '..') {
                $handle = 'style-' . pathinfo($file, PATHINFO_FILENAME);
                wp_enqueue_style(
                    $handle,
                    $css_url . $file,
                    array(),
                    filemtime($css_dir . $file), // Cache busting
                    'all'
                );
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'add_ALL_styles');