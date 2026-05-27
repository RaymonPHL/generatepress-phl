<?php
function add_social_links_target_blank() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var socialLinks = document.querySelectorAll('.wp-block-social-links a.wp-block-social-link-anchor');
        socialLinks.forEach(function(link) {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'add_social_links_target_blank');