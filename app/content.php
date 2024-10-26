<?php
// Include configuration
$config = include 'config.php';

// Get the list of content files for a given slug, date, and post types
function get_content_list($slug = '*', $date = '*', $post_types = null) {
    global $config;
    $post_types_config = $config['post_types'];

    $contentList = [];

    // If no specific post types are provided, use all post types in priority order
    if ($post_types === null) {
        $post_types = array_keys($post_types_config);
    }

    foreach ($post_types as $post_type) {
        $settings = $post_types_config[$post_type];
        $folder = $settings['folder'];
        $date_prefix = $settings['date_prefix'];

        if ($date_prefix) {
            $pattern = 'content/' . $folder . '/' . $date . '_' . $slug . '.md';
        } else {
            $pattern = 'content/' . $folder . '/' . $slug . '.md';
        }

        $files = array_reverse(glob($pattern));

        foreach ($files as $file) {
            $contentList[] = [
                'file' => $file,
                'post_type' => $post_type,
            ];
        }
    }

    return $contentList;
}

// Parse a content file into a post object
function parse_content_file($file, $post_type) {
    global $config;
    $post_types_config = $config['post_types'];
    $settings = $post_types_config[$post_type];

    $frontMatter = new Webuni\FrontMatter\FrontMatter();
    $content = $frontMatter->parse(file_get_contents($file));

    $filename = basename($file);
    $post = new stdClass;
    $post->post_type = $post_type;

    $date_prefix = $settings['date_prefix'];

    if ($date_prefix) {
        if (preg_match('/^([\d]{4}-[\d]{2}-[\d]{2})_(.+)\.md$/', $filename, $matches)) {
            $post->date = strtotime($matches[1]);
            $post->slug = $matches[2];
        } else {
            // If date prefix is expected but not found, skip this file
            return null;
        }
    } else {
        $post->date = filemtime($file);
        $post->slug = basename($filename, '.md');
    }

    // Get the contents and convert it to HTML
    $meta = $content->getData();
    $post->title = $meta['title'] ?? 'No title';
    $post->body = convert_markdown($content->getContent());
    $post->image = $meta['image'] ?? '';
    $post->excerpt = $meta['excerpt'] ?? substr(strip_tags($post->body), 0, 140);
    $post->tags = [];

    // Load taxonomy terms
    $taxonomy = $settings['taxonomy'];
    if ($taxonomy && isset($meta[$taxonomy])) {
        $post->tags = array_map('trim', explode(',', $meta[$taxonomy]));
    }

    return $post;
}

// Get the full content for a given slug, checking post types in order of priority
function get_single($slug) {
    $contentList = get_content_list($slug);

    foreach ($contentList as $contentInfo) {
        $file = $contentInfo['file'];
        $post_type = $contentInfo['post_type'];
        $post = parse_content_file($file, $post_type);
        if ($post) {
            return $post;
        }
    }

    return null;
}

// Get a list of posts, possibly filtered by taxonomy term and/or post_type
function get_posts($page = 1, $perPage = null, $taxonomy_term = null, $taxonomy_name = null, $post_type = 'posts') {
    global $config;

    if ($perPage === null) {
        $perPage = $config['posts_per_page'];
    }

    $post_types_config = $config['post_types'];

    // Determine which post types to include
    if ($post_type === 'all') {
        $post_types_to_include = array_keys($post_types_config);
    } else {
        if (!isset($post_types_config[$post_type])) {
            return false;
        }
        $post_types_to_include = [$post_type];
    }

    $posts = [];

    foreach ($post_types_to_include as $pt) {
        $settings = $post_types_config[$pt];
        $folder = $settings['folder'];
        $taxonomy = $settings['taxonomy'];
        $date_prefix = $settings['date_prefix'];

        // If filtering by taxonomy term and taxonomy names match or not specified
        if ($taxonomy_term !== null) {
            if ($taxonomy_name !== null && $taxonomy_name !== $taxonomy) {
                // Skip post types with different taxonomy names
                continue;
            }
        }

        // Get all content files for this post type
        $pattern = 'content/' . $folder . '/*.md';
        $files = array_reverse(glob($pattern));

        foreach ($files as $file) {
            $post = parse_content_file($file, $pt);
            if ($post) {
                // If filtering by taxonomy term
                if ($taxonomy_term !== null) {
                    if (!in_array($taxonomy_term, $post->tags)) {
                        continue;
                    }
                }
                $posts[] = $post;
            }
        }
    }

    // Sort posts by date descending
    usort($posts, function ($a, $b) {
        return $b->date <=> $a->date;
    });

    // Pagination
    $offset = ($page - 1) * $perPage;
    $posts = array_slice($posts, $offset, $perPage);

    return $posts;
}

// Function to get all taxonomy terms and associated post types
function get_taxonomy_terms($taxonomy_name = null) {
    global $config;
    $post_types_config = $config['post_types'];

    $terms = [];

    foreach ($post_types_config as $post_type => $settings) {
        $taxonomy = $settings['taxonomy'];
        if ($taxonomy === null) {
            continue;
        }

        if ($taxonomy_name !== null && $taxonomy_name !== $taxonomy) {
            continue;
        }

        // Get all content files for this post type
        $folder = $settings['folder'];
        $pattern = 'content/' . $folder . '/*.md';
        $files = glob($pattern);

        foreach ($files as $file) {
            $post = parse_content_file($file, $post_type);
            if ($post) {
                foreach ($post->tags as $tag) {
                    $terms[$taxonomy][$tag][] = $post_type;
                }
            }
        }
    }

    return $terms;
}
