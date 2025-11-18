<?php
// Función para eliminar el crédito por defecto de GeneratePress
function remove_generatepress_credit() {
    remove_action('generate_credits', 'generate_add_footer_info');
}
add_action('init', 'remove_generatepress_credit');

// Función para añadir crédito personalizado en el footer y además añadiendo un link a la pagina que ha creado el tema tipo created by PHL.cc con la imagen del favicon de la web de phl.cc
function custom_footer_credit() {
    echo '<div class="footer-credit">';
    echo '<p>© ' . date('Y') . ' ' . get_bloginfo('name') . '. Todos los derechos reservados.<br> 
    Creado por <a href="https://phl.cc" target="_blank">PHL Soluciones digitales inteligentes</a></p>';
    echo '</div>';
}
add_action('generate_credits', 'custom_footer_credit');