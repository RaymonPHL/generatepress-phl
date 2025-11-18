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

// --------------------------------------------------------------------------- Añadir otros includes del tema hijo
require_once get_stylesheet_directory() . '/PHL-base/PHL-herramientas.php'; // Herramientas PHL del tema hijo

// Incluir TODOS los archivos PHP del directorio /resources/inc
function include_all_php_files() {
    $inc_dir = get_stylesheet_directory() . '/resources/inc/';
    
    // Escanear el directorio
    $php_files = scandir($inc_dir);
    
    foreach ($php_files as $file) {
        // Verificar que es un archivo PHP
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $file_path = $inc_dir . $file;
            
            // Incluir el archivo
            require_once $file_path;
        }
    }
}
add_action('after_setup_theme', 'include_all_php_files');