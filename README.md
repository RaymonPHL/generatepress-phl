# GeneratePress-PHL
Tema hijo de WordPress con estructura organizada de resources.
Versión 1.0

## Instalación
1. Descargar e instalar tema GeneratePress
2. Descargar e instalar este tema hijo en `/wp-content/themes/`
3. Activar el tema desde el administrador de WordPress

## Uso
Agrega tus archivos personalizados en las carpetas correspondientes dentro de `/resources/`,
    se incluirán y minimizarán de manera automática en la manera de lo posible salvo images.
Mantener sin tocar los archivos `PHL-*` porque podrían ser cambiados en la siguiente versión.

Habilita el debug renombrando `_debug.flag` a `debug.flag` para separar archivos y debugear mejor y viceversa para producción.

La configuracion general del tema esta en `theme.config.php`.
Desde ahi puedes activar/desactivar la combinacion de assets, el nombre del flag de debug,
la limpieza de cache y los estilos base que se eliminan de WordPress, GeneratePress y Elementor.

Utiliza `/assets/` y el resto de estructura del tema padre de generatepress para sobrescribirle.

Hay algunos recursos en `/examples/` que puedes utilizar para copiarlos a `/resources/`
