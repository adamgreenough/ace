<?php

// Return list of all posts
function get_post_list($slug = '*', $date = '*') {
    $postList = array_reverse(glob('posts/' . $date . '_' . $slug . '.md'));
    return $postList;
}

// Return list of all posts within a tag
function get_tag_list($tag) {
    $frontMatter = new Webuni\FrontMatter\FrontMatter();
    $postFiles = array_reverse(glob('posts/*.md'));
    $tagList = [];

    foreach ($postFiles as $file) {
        $content = $frontMatter->parse(file_get_contents($file));
        $meta = $content->getData();
        $tags = isset($meta['tags']) ? array_map('trim', explode(',', $meta['tags'])) : [];
        $tags = array_map('strtolower', $tags);

        if (in_array(strtolower(urldecode($tag)), $tags)) {
            $tagList[] = $file;
        }
    }

    return $tagList;
}

// Return an array of posts with front matter parsed
function get_posts($page = 1, $perPage = null, $tag = null) {
    global $config;
    $frontMatter = new Webuni\FrontMatter\FrontMatter();

    if ($perPage === null) {
        $perPage = $config['posts_per_page'];
    }

    if ($tag === null) {
        $posts = get_post_list();
    } else {
        $posts = get_tag_list($tag);
    }

    // Check we found some posts
    if (!$posts) {
        return false;
    }

    // Extract a specific page with results
    $posts = array_slice($posts, ($page - 1) * $perPage, $perPage);
    $result = [];

    foreach ($posts as $filePath) {
        $post = new stdClass;
        $content = $frontMatter->parse(file_get_contents($filePath));

        // Split the date & slug from file name
        $arr = explode('_', basename($filePath));
        $post->date = strtotime($arr[0]);
        $post->slug = basename($arr[1], '.md') ?? '';

        // Get the contents and convert it to HTML
        $meta = $content->getData();
        $post->title = $meta['title'] ?? 'No title';
        $post->body = convert_markdown($content->getContent());
        $post->image = $meta['image'] ?? '';
        $post->excerpt = $meta['excerpt'] ?? substr(strip_tags($post->body), 0, 140);
        $post->tags = isset($meta['tags']) ? array_map('trim', explode(',', $meta['tags'])) : [];

        $result[] = $post;
    }

    return $result;
}

// Get the full contents of a single post
function get_single($slug, $year = '*', $month = '*') {
    $frontMatter = new Webuni\FrontMatter\FrontMatter();
    $date = $year . '-' . $month . '-*';

    $single = get_post_list($slug, $date);

    if (isset($single[0])) { // Check post exists
        $post = new stdClass;
        $content = $frontMatter->parse(file_get_contents($single[0]));

        $arr = explode('_', basename($single[0]));
        $post->date = strtotime($arr[0]);

        // Get the contents and convert it to HTML
        $meta = $content->getData();
        $post->title = $meta['title'] ?? 'No title';
        $post->body = convert_markdown($content->getContent());
        $post->image = $meta['image'] ?? '';
        $post->excerpt = $meta['excerpt'] ?? substr(strip_tags($post->body), 0, 140);
        $post->tags = isset($meta['tags']) ? array_map('trim', explode(',', $meta['tags'])) : [];

        return $post;
    }
    return null;
}
