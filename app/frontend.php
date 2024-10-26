<?php
function get_theme_directory_url() {
    global $config;
    return $config['base_path'] . '/themes/' . $config['frontend_theme'];
}

function get_header($title = null, $description = null, $image = null) {
    global $config;

    if ($title === null) {
        $title = $config['blog_name'];
    } else {
        $title = htmlspecialchars($title) . ' ' . $config['title_separator'] . ' ' . htmlspecialchars($config['blog_name']);
    }

    if ($description === null) {
        $description = $config['blog_description'];
    } else {
        $description = htmlspecialchars($description);
    }

    $image = $image ? htmlspecialchars($image) : '';

    require 'themes/' . $config['frontend_theme'] . '/header.php';
}

function get_footer() {
    global $config;
    require 'themes/' . $config['frontend_theme'] . '/footer.php';
}

function get_post_link($post) {
    global $config;
    $post_base = '';
    $post_types_config = $config['post_types'];
    $post_type_settings = $post_types_config[$post->post_type];
    $slug_prefix = $post_type_settings['slug_prefix'];

    if ($config['post_base'] && $post_type_settings['date_prefix']) {
        $post_base = date('Y/m', $post->date) . '/';
    }

    $url = $config['base_path'] . '/';
    if ($slug_prefix !== '') {
        $url .= $slug_prefix . '/';
    }
    $url .= $post_base . $post->slug . '/';
    return $url;
}

function get_taxonomy_link($taxonomy_name, $term) {
    global $config;
    $url = $config['base_path'] . '/' . $taxonomy_name . '/' . urlencode($term) . '/';
    return $url;
}

function get_pagination_link($page, $taxonomy_term = null, $taxonomy_name = null, $post_type = 'posts') {
    global $config;
    $pagination = [];

    $post_types_config = $config['post_types'];
    $post_type_settings = $post_types_config[$post_type];
    $slug_prefix = $post_type_settings['slug_prefix'];

    $postList = get_posts(1, PHP_INT_MAX, $taxonomy_term, $taxonomy_name, $post_type);
    $count = count($postList);

    $basePath = $config['base_path'] . '/';
    if ($slug_prefix !== '') {
        $basePath .= $slug_prefix . '/';
    }

    if ($taxonomy_term !== null && $taxonomy_name !== null) {
        $basePath .= $taxonomy_name . '/' . urlencode($taxonomy_term) . '/';
    }

    if (($count / $config['posts_per_page']) > $page) {
        $pagination['next'] = $basePath . ($page + 1) . '/';
    } else {
        $pagination['next'] = null;
    }

    if ($page > 1) {
        $pagination['prev'] = $basePath . ($page - 1) . '/';
    } else {
        $pagination['prev'] = null;
    }

    return $pagination;
}
