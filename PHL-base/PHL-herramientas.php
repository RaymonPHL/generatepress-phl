<?php

// FUNCIONES GENERALES
// ===========================
function phl_theme_config($key = null, $default = null) {
    static $config = null;

    if ($config === null) {
        $config_file = get_stylesheet_directory() . '/theme.config.php';
        $config = file_exists($config_file) ? require $config_file : array();

        if (!is_array($config)) {
            $config = array();
        }

        $config = apply_filters('phl_theme_config', $config);
    }

    if ($key === null) {
        return $config;
    }

    $value = $config;

    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }

        $value = $value[$part];
    }

    return $value;
}

function phl_is_development_mode() {
    $debug_mode = phl_theme_config('assets.debug_mode', false);

    return $debug_mode ||
        (defined('WP_DEBUG') && WP_DEBUG) ||
        (defined('WP_ENV') && WP_ENV !== 'production');
}

// Funcion para comprobar si un plugin esta activo.
function phl_plugin_activo($plugin) {
    return in_array($plugin, apply_filters('active_plugins', (array) get_option('active_plugins', array())), true) ||
        (is_multisite() && array_key_exists($plugin, (array) get_site_option('active_sitewide_plugins', array())));
}

// Limpiar archivos combinados antiguos periodicamente en /uploads/PHL-cache/.
function phl_clean_old_combined_cache() {
    $upload_dir = wp_upload_dir();
    $base_cache_dir = trailingslashit($upload_dir['basedir']) . 'PHL-cache/';

    if (!is_dir($base_cache_dir)) {
        return;
    }

    $subdirectories = glob($base_cache_dir . '*', GLOB_ONLYDIR);

    foreach ($subdirectories as $directory) {
        $files = glob($directory . '/PHL-combined-*');
        $files = array_filter($files, 'is_file');

        $max_files = (int) phl_theme_config('cache.max_combined_files', 5);

        if (count($files) > $max_files) {
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            $files_to_delete = count($files) - $max_files;

            for ($i = 0; $i < $files_to_delete; $i++) {
                unlink($files[$i]);
            }

            error_log('PHL Cache: Limpiados ' . $files_to_delete . ' archivos antiguos en ' . basename($directory));
        }
    }
}

add_action('wp', function() {
    $probability = (int) phl_theme_config('cache.cleanup_probability', 1);

    if ($probability > 0 && mt_rand(1, 100) <= $probability) {
        phl_clean_old_combined_cache();
    }
});

// FUNCIONES AUXILIARES DE ASSETS
// ==============================
function phl_prepare_combined_cache_dir($type) {
    $upload_dir = wp_upload_dir();
    $cache_dir = trailingslashit($upload_dir['basedir']) . 'PHL-cache/' . $type . '/';

    wp_mkdir_p($cache_dir);

    if (!file_exists($cache_dir . 'index.php')) {
        file_put_contents($cache_dir . 'index.php', '<?php // Silence is golden');
    }

    $htaccess_path = $cache_dir . '.htaccess';
    $htaccess_content = "Options -Indexes\n";

    if (!file_exists($htaccess_path) || file_get_contents($htaccess_path) !== $htaccess_content) {
        file_put_contents($htaccess_path, $htaccess_content);
    }

    return array(
        'path' => $cache_dir,
        'url'  => trailingslashit($upload_dir['baseurl']) . 'PHL-cache/' . $type . '/',
    );
}

function phl_get_asset_files($directory, $extension) {
    if (!is_dir($directory)) {
        return array();
    }

    $files = array_filter(scandir($directory), function($file) use ($directory, $extension) {
        return is_file($directory . $file) && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === $extension;
    });

    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    return $files;
}

// Combina CSS sin minificarlo con regex agresivas.
function phl_combine_css($css_dir) {
    $css_files = phl_get_asset_files($css_dir, 'css');
    $combined_content = '';

    foreach ($css_files as $file) {
        $combined_content .= "/* === $file === */\n" . file_get_contents($css_dir . $file) . "\n";
    }

    if ($combined_content === '') {
        return false;
    }

    $version = md5($combined_content);
    $combined_filename = 'PHL-combined-' . $version . '.css';
    $cache = phl_prepare_combined_cache_dir('css');
    $combined_path = $cache['path'] . $combined_filename;

    if (!file_exists($combined_path) || file_get_contents($combined_path) !== $combined_content) {
        $result = file_put_contents($combined_path, $combined_content);

        if ($result === false) {
            return false;
        }
    }

    return array(
        'url'     => $cache['url'] . $combined_filename,
        'version' => $version,
    );
}

// FUNCIONES AUXILIARES de FUENTES
// ===============================
function phl_get_font_format($extension) {
    $formats = array(
        'woff'  => 'woff',
        'woff2' => 'woff2',
        'ttf'   => 'truetype',
        'otf'   => 'opentype',
        'eot'   => 'embedded-opentype',
    );

    return $formats[$extension] ?? 'truetype';
}

function phl_detect_font_weight_style($font_name) {
    $weight = 400;
    $style = 'normal';
    $lower_name = strtolower($font_name);

    if (strpos($lower_name, 'thin') !== false) {
        $weight = 100;
    } elseif (strpos($lower_name, 'extralight') !== false || strpos($lower_name, 'ultralight') !== false) {
        $weight = 200;
    } elseif (strpos($lower_name, 'light') !== false) {
        $weight = 300;
    } elseif (strpos($lower_name, 'regular') !== false || strpos($lower_name, 'normal') !== false || strpos($lower_name, 'book') !== false) {
        $weight = 400;
    } elseif (strpos($lower_name, 'medium') !== false) {
        $weight = 500;
    } elseif (strpos($lower_name, 'semibold') !== false || strpos($lower_name, 'demi') !== false) {
        $weight = 600;
    } elseif (strpos($lower_name, 'bold') !== false) {
        $weight = 700;
    } elseif (strpos($lower_name, 'extrabold') !== false || strpos($lower_name, 'ultrabold') !== false) {
        $weight = 800;
    } elseif (strpos($lower_name, 'black') !== false || strpos($lower_name, 'heavy') !== false) {
        $weight = 900;
    }

    if (strpos($lower_name, 'italic') !== false) {
        $style = 'italic';
    } elseif (strpos($lower_name, 'oblique') !== false) {
        $style = 'oblique';
    }

    return array(
        'weight' => $weight,
        'style'  => $style,
    );
}

// FUNCIONES AUXILIARES de JS
// ==========================
// Combina JS sin minificarlo con regex agresivas.
function phl_combine_js($js_dir) {
    $js_files = phl_get_asset_files($js_dir, 'js');
    $combined_content = '';

    foreach ($js_files as $file) {
        $combined_content .= "/* === $file === */\n" . file_get_contents($js_dir . $file) . "\n";
    }

    if ($combined_content === '') {
        return false;
    }

    $version = md5($combined_content);
    $combined_filename = 'PHL-combined-' . $version . '.js';
    $cache = phl_prepare_combined_cache_dir('js');
    $combined_path = $cache['path'] . $combined_filename;

    if (!file_exists($combined_path) || file_get_contents($combined_path) !== $combined_content) {
        $result = file_put_contents($combined_path, $combined_content);

        if ($result === false) {
            return false;
        }
    }

    return array(
        'url'     => $cache['url'] . $combined_filename,
        'version' => $version,
    );
}
