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
function phl_is_framework_css_file($file) {
    return strpos($file, 'PHL-fw-') === 0;
}

function phl_filter_css_files($css_files) {
    if (phl_theme_config('styles.load_phl_css_framework', true)) {
        return $css_files;
    }

    return array_values(array_filter($css_files, function($file) {
        return !phl_is_framework_css_file($file);
    }));
}

// Incluir TODOS los archivos CSS del directorio /resources/css
function phl_add_all_styles() {
    $css_dir = get_stylesheet_directory() . '/resources/css/';
    $css_url = get_stylesheet_directory_uri() . '/resources/css/';

    if (!is_dir($css_dir)) {
        return;
    }

    $css_files = phl_filter_css_files(phl_get_asset_files($css_dir, 'css'));

    // $WebEnProduccion=false;
    if (!phl_is_development_mode() && phl_theme_config('assets.combine_in_production', true)) {
        // MODO PRODUCCION: Combinar todos los CSS
        $combined_css = phl_combine_css($css_dir, $css_files);
        
        if ($combined_css) {
            wp_enqueue_style(
                'phl-all-styles',
                $combined_css['url'],
                array(),
                $combined_css['version'],
                'all'
            );
        }
    } else {
        // MODO NORMAL: Cargar archivos individualmente
        foreach ($css_files as $file) {
            $handle = 'phl-style-' . sanitize_key(pathinfo($file, PATHINFO_FILENAME));
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
add_action('wp_enqueue_scripts', 'phl_add_all_styles', 20);
