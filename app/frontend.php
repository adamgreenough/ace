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
        $title = $title . ' ' . $config['title_separator'] . ' ' . $config['blog_name']; 
    }
    
    if ($description === null) {
        $description = $config['blog_description'];
    }
    
    require 'themes/' . $config['frontend_theme'] . '/header.php';
}

function get_footer() {
    global $config;
    require 'themes/' . $config['frontend_theme'] . '/footer.php';
}

function get_post_link($post) {
    global $config;
    $post_base = '';
    
    if ($config['post_base']) {
        $post_base = date('Y/m', $post->date) . '/';
    }
    
    return $config['base_path'] . '/' . $post_base . $post->slug . '/';
}

function get_pagination_link($page, $posts, $tag = '') {
    global $config;
    $pagination = [];

    if ($tag) {
        $postList = get_tag_list($tag);
    } else {
        $postList = get_post_list();
    }

    $count = count($postList);
    $tagPath = $tag ? 'tag/' . urlencode($tag) . '/' : '';

    if (($count / $config['posts_per_page']) > $page) {
        $pagination['next'] = $config['base_path'] . '/' . $tagPath . ($page + 1) . '/';
    } else {
        $pagination['next'] = null;
    }

    if ($page > 1) {
        $pagination['prev'] = $config['base_path'] . '/' . $tagPath . ($page - 1) . '/';
    } else {
        $pagination['prev'] = null;
    }
     
    return $pagination;
}
