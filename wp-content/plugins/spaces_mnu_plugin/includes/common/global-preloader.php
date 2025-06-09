<?php
function gp_enqueue_preloader_scripts()
{
    wp_enqueue_style('gp-preloader-style', SPACES_MNU_PLUGIN_URL . 'assets/css/preloader.css');
    wp_enqueue_script('gp-preloader-script', SPACES_MNU_PLUGIN_URL . 'assets/js/preloader.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'gp_enqueue_preloader_scripts');

function gp_add_preloader_html()
{
    echo '<div id="preloader">
            <div class="spinner"></div>
          </div>';
}

add_action('wp_footer', 'gp_add_preloader_html');
