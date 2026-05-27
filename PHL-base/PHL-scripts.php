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
function phl_add_all_scripts() {
    $js_dir = get_stylesheet_directory() . '/resources/js/';
    $js_url = get_stylesheet_directory_uri() . '/resources/js/';

    if (!is_dir($js_dir)) {
        return;
    }

    if (!phl_is_development_mode() && phl_theme_config('assets.combine_in_production', true)) {
        // MODO PRODUCCION: Combinar todos los JS
        $combined_js = phl_combine_js($js_dir);
        
        if ($combined_js) {
            wp_enqueue_script(
                'phl-all-scripts',
                $combined_js['url'],
                array(),
                $combined_js['version'],
                true
            );
        }
    } else {
        // MODO NORMAL: Cargar archivos individualmente
        $js_files = phl_get_asset_files($js_dir, 'js');
        
        foreach ($js_files as $file) {
            $handle = 'phl-script-' . sanitize_key(pathinfo($file, PATHINFO_FILENAME));
            wp_enqueue_script(
                $handle,
                $js_url . $file,
                array(),
                filemtime($js_dir . $file), // Cache busting
                true // Cargar en el footer
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'phl_add_all_scripts', 20);
