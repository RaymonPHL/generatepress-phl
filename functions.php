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

// Cargar el estilo del tema padre GeneratePress y del hijo GeneratePress PHL
function gp_phl_child_enqueue_styles() {
    $parent_style = 'generatepress-style';
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style(
        'gp-phl-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [$parent_style],
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'gp_phl_child_enqueue_styles');

// Añadimos los includes del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-includes.php'; // Funciones PHL del tema hijo GeneratePress PHL

// Añadimos los estilos del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-styles.php'; // Estilos PHL del tema hijo GeneratePress PHL

// Añadimos los scripts del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-scripts.php'; // Scripts PHL del tema hijo GeneratePress PHL

// Añadimos las fuentes del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-fonts.php'; // Fuentes PHL del tema hijo GeneratePress PHL

