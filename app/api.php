<?php
$config = include 'config.php';

function sanitize_input($input) {
    return htmlspecialchars(strip_tags($input));
}

function api_feed() {
    global $config;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = max(1, (int)($_GET['perpage'] ?? $config['posts_per_page']));
    $tag = isset($_GET['tag']) ? sanitize_input($_GET['tag']) : null;
    $post_type = isset($_GET['post_type']) ? sanitize_input($_GET['post_type']) : 'posts';
    return get_posts($page, $perPage, $tag, $post_type);
}

function api_single() {
    $slug = isset($_GET['slug']) ? sanitize_input($_GET['slug']) : null;
    $post = get_single($slug);
    return $post;
}
