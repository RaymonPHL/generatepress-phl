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

// 1. CARGAR FUENTES EN TODO EL SITIO
function phl_load_custom_fonts() {
    $fonts_dir = get_stylesheet_directory() . '/resources/fonts/';
    $fonts_url = get_stylesheet_directory_uri() . '/resources/fonts/';
    
    if (!file_exists($fonts_dir)) {
        return;
    }

    $font_files = scandir($fonts_dir);
    $css = '';

    foreach ($font_files as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $is_font_file = in_array($extension, ['woff', 'woff2', 'ttf', 'otf', 'eot']);

        if ($is_font_file && $file !== '.' && $file !== '..') {
            $font_name = pathinfo($file, PATHINFO_FILENAME);
            $base_font_name = preg_replace('/-[a-z0-9]+$/i', '', $font_name);
            
            $font_url = $fonts_url . $file;
            $format = get_font_format($extension);
            $weight_style = detect_font_weight_style($font_name);

            $css .= "
@font-face {
    font-family: '{$base_font_name}';
    src: url('{$font_url}') format('{$format}');
    font-weight: {$weight_style['weight']};
    font-style: {$weight_style['style']};
    font-display: swap;
}";
        }
    }

    if (!empty($css)) {
        // Registrar un estilo para las fuentes
        wp_register_style('phl-custom-fonts', false);
        wp_enqueue_style('phl-custom-fonts');
        wp_add_inline_style('phl-custom-fonts', $css);
    }
}
add_action('wp_enqueue_scripts', 'phl_load_custom_fonts');

// 2. AGREGAR FUENTES A ELEMENTOR
function phl_add_fonts_to_elementor($fonts) {
    $fonts_dir = get_stylesheet_directory() . '/resources/fonts/';
    
    if (!file_exists($fonts_dir)) {
        return $fonts;
    }

    $font_files = scandir($fonts_dir);
    $custom_fonts = [];

    foreach ($font_files as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $is_font_file = in_array($extension, ['woff', 'woff2', 'ttf', 'otf', 'eot']);

        if ($is_font_file && $file !== '.' && $file !== '..') {
            $font_name = pathinfo($file, PATHINFO_FILENAME);
            $base_font_name = preg_replace('/-[a-z0-9]+$/i', '', $font_name);
            
            if (!isset($custom_fonts[$base_font_name])) {
                $custom_fonts[$base_font_name] = $base_font_name;
            }
        }
    }

    // Agregar a Elementor
    foreach ($custom_fonts as $font_family) {
        $fonts[$font_family] = 'system'; // 'system' para fuentes locales
    }

    return $fonts;
}
add_filter('elementor/fonts/groups', 'phl_add_fonts_to_elementor');

// 3. AGREGAR FUENTES A LA LISTA DE TIPOGRAFÍAS DE ELEMENTOR
function phl_add_fonts_to_elementor_list($additional_fonts) {
    $fonts_dir = get_stylesheet_directory() . '/resources/fonts/';
    
    if (!file_exists($fonts_dir)) {
        return $additional_fonts;
    }

    $font_files = scandir($fonts_dir);
    $custom_fonts = [];

    foreach ($font_files as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $is_font_file = in_array($extension, ['woff', 'woff2', 'ttf', 'otf', 'eot']);

        if ($is_font_file && $file !== '.' && $file !== '..') {
            $font_name = pathinfo($file, PATHINFO_FILENAME);
            $base_font_name = preg_replace('/-[a-z0-9]+$/i', '', $font_name);
            
            if (!isset($custom_fonts[$base_font_name])) {
                $custom_fonts[$base_font_name] = $base_font_name;
            }
        }
    }

    // Agregar cada fuente a Elementor
    foreach ($custom_fonts as $font_family) {
        $additional_fonts[$font_family] = 'system';
    }

    return $additional_fonts;
}
add_filter('elementor/fonts/additional_fonts', 'phl_add_fonts_to_elementor_list');

// 4. AGREGAR FUENTES AL SELECTOR DE WORDPRESS (Gutenberg)
function phl_add_fonts_to_theme_json($theme_json) {
    $fonts_dir = get_stylesheet_directory() . '/resources/fonts/';
    
    if (!file_exists($fonts_dir)) {
        return $theme_json;
    }

    $font_files = scandir($fonts_dir);
    $font_families = [];

    foreach ($font_files as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $is_font_file = in_array($extension, ['woff', 'woff2', 'ttf', 'otf', 'eot']);

        if ($is_font_file && $file !== '.' && $file !== '..') {
            $font_name = pathinfo($file, PATHINFO_FILENAME);
            $base_font_name = preg_replace('/-[a-z0-9]+$/i', '', $font_name);
            
            if (!in_array($base_font_name, $font_families)) {
                $font_families[] = $base_font_name;
            }
        }
    }

    if (!empty($font_families)) {
        $data = $theme_json->get_data();
        
        // Inicializar el array si no existe
        if (!isset($data['settings']['typography']['fontFamilies'])) {
            $data['settings']['typography']['fontFamilies'] = array();
        }

        // Agregar cada fuente
        foreach ($font_families as $font_family) {
            $data['settings']['typography']['fontFamilies'][] = array(
                'fontFamily' => "'{$font_family}', sans-serif",
                'slug' => sanitize_title($font_family),
                'name' => $font_family
            );
        }

        $theme_json = new WP_Theme_JSON_Data($data);
    }

    return $theme_json;
}
add_filter('wp_theme_json_data_theme', 'phl_add_fonts_to_theme_json');

// 5. CARGAR FUENTES EN EL EDITOR DE ELEMENTOR
function phl_load_fonts_in_elementor_editor() {
    $fonts_dir = get_stylesheet_directory() . '/resources/fonts/';
    $fonts_url = get_stylesheet_directory_uri() . '/resources/fonts/';
    
    if (!file_exists($fonts_dir)) {
        return;
    }

    $font_files = scandir($fonts_dir);
    $css = '';

    foreach ($font_files as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $is_font_file = in_array($extension, ['woff', 'woff2', 'ttf', 'otf', 'eot']);

        if ($is_font_file && $file !== '.' && $file !== '..') {
            $font_name = pathinfo($file, PATHINFO_FILENAME);
            $base_font_name = preg_replace('/-[a-z0-9]+$/i', '', $font_name);
            
            $font_url = $fonts_url . $file;
            $format = get_font_format($extension);
            $weight_style = detect_font_weight_style($font_name);

            $css .= "
@font-face {
    font-family: '{$base_font_name}';
    src: url('{$font_url}') format('{$format}');
    font-weight: {$weight_style['weight']};
    font-style: {$weight_style['style']};
    font-display: swap;
}";
        }
    }

    if (!empty($css)) {
        echo '<style>' . $css . '</style>';
    }
}
add_action('elementor/editor/after_enqueue_styles', 'phl_load_fonts_in_elementor_editor');

// 6. DEBUG - Verificar que todo funciona
function phl_debug_custom_fonts() {
    if (current_user_can('manage_options') && isset($_GET['debug_fonts'])) {
        echo '<div style="background: #f5f5f5; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>Debug Fuentes Personalizadas</h3>';
        
        // Verificar directorio
        $fonts_dir = get_stylesheet_directory() . '/resources/fonts/';
        echo '<h4>Archivos en /resources/fonts/:</h4>';
        echo '<pre>';
        if (file_exists($fonts_dir)) {
            $files = scandir($fonts_dir);
            print_r($files);
        } else {
            echo 'Directorio no existe: ' . $fonts_dir;
        }
        echo '</pre>';
        
        echo '</div>';
    }
}
add_action('wp_footer', 'phl_debug_custom_fonts');