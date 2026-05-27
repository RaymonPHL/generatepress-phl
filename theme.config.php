<?php

return array(
    'assets' => array(
        // Combina los assets (estilos y scripts) en un único archivo en producción.
        'combine_in_production' => true,
        // Activa o desactiva el modo debug desde este archivo de configuración.
        'debug_mode' => false,
    ),

    'cache' => array(
        // Probabilidad de limpieza de caché en cada carga (1 = siempre, 0 = nunca).
        'cleanup_probability' => 1,
        // Número máximo de archivos combinados que se guardan en caché.
        'max_combined_files' => 5,
    ),

    'styles' => array(
        // Cargar o no la hoja de estilos del tema hijo.
        'load_child_stylesheet' => false,

        // Deshabilita estilos CSS estándar de WordPress.
        'disable_wp_styles' => true,
        // Deshabilita los estilos predeterminados de GeneratePress.
        'disable_generatepress_styles' => false,
        // Deshabilita estilos específicos de Elementor.
        'disable_elementor_styles' => true,

        // Handles de estilos de WordPress que se deben gestionar/deshabilitar.
        'wp_handles' => array(
            'classic-theme-styles',
            'global-styles',
            'wp-block-library',
            'wp-block-library-theme',
            'wc-block-style',
        ),

        // Handles de estilos de GeneratePress que se deben gestionar/deshabilitar.
        'generatepress_handles' => array(
            'generate-style',
            'generate-style-grid',
            'generate-mobile-style',
            'generate-font-icons',
            'generate-fonts',
            'generate-google-fonts',
            'generate-child',
            'generate-rtl',
        ),

        // Prefijos de handles de Elementor que se deben reconocer.
        'elementor_handle_prefixes' => array(
            'elementor',
            'e-',
        ),

        // Handles específicos de Elementor que se deben gestionar.
        'elementor_handles' => array(
            'eicons',
        ),
    ),
);
