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

// Añadimos los includes del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-includes.php'; // Funciones PHL del tema hijo GeneratePress PHL

// Controla si GeneratePress carga automaticamente el style.css del tema hijo.
add_filter('generate_load_child_theme_stylesheet', function() {
    return (bool) phl_theme_config('styles.load_child_stylesheet', false);
});

// Desactivar CSS base de WordPress, GeneratePress y Elementor en frontend.
function phl_dequeue_base_styles() {
    $disable_wp_styles = apply_filters('phl_disable_wp_styles', phl_theme_config('styles.disable_wp_styles', true));
    $disable_generatepress_styles = apply_filters('phl_disable_generatepress_styles', phl_theme_config('styles.disable_generatepress_styles', false));
    $disable_elementor_styles = apply_filters('phl_disable_elementor_styles', phl_theme_config('styles.disable_elementor_styles', true));

    if ($disable_wp_styles) {
        $wp_style_handles = phl_theme_config('styles.wp_handles', array());

        foreach ($wp_style_handles as $handle) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
    }

    if ($disable_generatepress_styles) {
        $generatepress_style_handles = phl_theme_config('styles.generatepress_handles', array());

        foreach ($generatepress_style_handles as $handle) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
    }

    if ($disable_elementor_styles) {
        global $wp_styles;

        if ($wp_styles instanceof WP_Styles) {
            $elementor_prefixes = phl_theme_config('styles.elementor_handle_prefixes', array());
            $elementor_handles = phl_theme_config('styles.elementor_handles', array());

            foreach ((array) $wp_styles->queue as $handle) {
                $is_elementor_handle = in_array($handle, $elementor_handles, true);

                foreach ($elementor_prefixes as $prefix) {
                    if (strpos($handle, $prefix) === 0) {
                        $is_elementor_handle = true;
                        break;
                    }
                }

                if ($is_elementor_handle) {
                    wp_dequeue_style($handle);
                    wp_deregister_style($handle);
                }
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'phl_dequeue_base_styles', 100);

// Añadimos los estilos del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-styles.php'; // Estilos PHL del tema hijo GeneratePress PHL

// Añadimos los scripts del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-scripts.php'; // Scripts PHL del tema hijo GeneratePress PHL

// Añadimos las fuentes del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-fonts.php'; // Fuentes PHL del tema hijo GeneratePress PHL
