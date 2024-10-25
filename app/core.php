<?php
function error_404() {
    global $config;
    header("HTTP/1.0 404 Not Found");
    
    // If there's a custom 404 template in the theme, use that; otherwise, use default
    if ($config['use_frontend'] && file_exists('themes/' . $config['frontend_theme'] . '/404.php')) {
        require 'themes/' . $config['frontend_theme'] . '/404.php';
    } else {
        require 'views/404.php';
    }
}
