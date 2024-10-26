<?php
// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect to URL with trailing slash if not a filename
$request_uri = $_SERVER['REQUEST_URI'];
if (!preg_match('/\.[a-zA-Z0-9]+$/', $request_uri) && substr($request_uri, -1) !== '/') {
    header("Location: $request_uri/", true, 301);
    exit();
}

$start_time = microtime(true);

// Autoload dependencies and include necessary files
require_once 'vendor/autoload.php';
require_once 'app/core.php';
require_once 'app/content.php';
require_once 'app/plugins.php';
require_once 'app/generate.php';
require_once 'app/api.php';

$config = include 'config.php';

$router = new AltoRouter();
$router->setBasePath($config['base_path']);

/* ============================================
   Plugins
============================================ */

load_plugins();

/* ============================================
   Subscription Feeds
============================================ */

// JSON Feed
$router->map('GET|HEAD', '/json/', function () use ($config) {
    header('Content-Type: application/json');
    $posts = get_posts(1, PHP_INT_MAX, null, null, 'all');
    if ($posts) {
        echo generate_json($posts);
    } else {
        echo json_encode(['error' => 'No posts found']);
    }
});

// RSS Feed
$router->map('GET|HEAD', '/rss/', function () use ($config) {
    header('Content-Type: application/xml');
    $posts = get_posts(1, PHP_INT_MAX, null, null, 'all');
    if ($posts) {
        echo generate_rss($posts);
    } else {
        echo '<error>No posts found</error>';
    }
});

/* ============================================
   API
============================================ */

// API Feed
$router->map('GET|HEAD', '/api/feed/', function () {
    header('Content-Type: application/json');
    $feed = api_feed();
    if ($feed) {
        echo generate_json($feed);
    } else {
        echo json_encode(['error' => 'No feed data available']);
    }
});

// API Single Post
$router->map('GET|HEAD', '/api/single/', function () {
    header('Content-Type: application/json');
    $single = api_single();
    if ($single) {
        echo generate_json($single);
    } else {
        echo json_encode(['error' => 'No post found']);
    }
});

/* ============================================
   Front-end
============================================ */

// Check if front-end is enabled in the configuration
if (!$config['use_frontend']) {
    $router->map('GET|HEAD', '/', function () {
        require 'views/default.php';
    });
} else {
    require_once 'app/frontend.php';
    require_once 'themes/' . $config['frontend_theme'] . '/functions.php';

    // Home Page (list of posts for the default post type)
    $router->map('GET|HEAD', '/[i:page]?/', function ($page = 1) use ($config) {
        $posts = get_posts($page, $config['posts_per_page'], null, null, 'posts');

        if ($posts) {
            include 'themes/' . $config['frontend_theme'] . '/home.php';
        } else {
            error_404();
        }
    });

    // Custom Post Type Listing Pages
    foreach ($config['post_types'] as $post_type => $settings) {
        $slug_prefix = $settings['slug_prefix'];
        if ($slug_prefix !== '') {
            // Listing Page for Post Type with Slug Prefix
            $router->map('GET|HEAD', '/' . $slug_prefix . '/[i:page]?/', function ($page = 1) use ($config, $post_type) {
                $posts = get_posts($page, $config['posts_per_page'], null, null, $post_type);

                if ($posts) {
                    include 'themes/' . $config['frontend_theme'] . '/post_type.php';
                } else {
                    error_404();
                }
            });
        }
    }

    // Taxonomy Term Pages
    $taxonomy_names = [];
    foreach ($config['post_types'] as $post_type => $settings) {
        $taxonomy = $settings['taxonomy'];
        if ($taxonomy !== null) {
            $taxonomy_names[$taxonomy] = true;
        }
    }

    foreach (array_keys($taxonomy_names) as $taxonomy_name) {
        // Taxonomy term route
        $router->map('GET|HEAD', '/' . $taxonomy_name . '/[:term]/[i:page]?/', function ($term, $page = 1) use ($config, $taxonomy_name) {
            $term = urldecode($term);
            $posts = get_posts($page, $config['posts_per_page'], $term, $taxonomy_name, 'all');

            if ($posts) {
                include 'themes/' . $config['frontend_theme'] . '/taxonomy.php';
            } else {
                error_404();
            }
        });
    }

    // Single Post or Page
    $router->map('GET|HEAD', '/[:slug]/', function ($slug) use ($config) {
        // Attempt to get content across all post types
        $post = get_single($slug);

        if ($post && isset($post->title)) {
            // Determine the template based on post type
            if ($post->post_type === 'pages') {
                include 'themes/' . $config['frontend_theme'] . '/page.php';
            } else {
                include 'themes/' . $config['frontend_theme'] . '/single.php';
            }
            return;
        }

        // If content not found, display 404
        error_404();
    });

    // Custom Post Types with Slug Prefix
    foreach ($config['post_types'] as $post_type => $settings) {
        $slug_prefix = $settings['slug_prefix'];
        if ($slug_prefix !== '') {
            // Single Post Route for Post Type with Slug Prefix
            $router->map('GET|HEAD', '/' . $slug_prefix . '/[:slug]/', function ($slug) use ($config, $post_type) {
                $post = get_single($slug);

                if ($post && $post->post_type === $post_type && isset($post->title)) {
                    include 'themes/' . $config['frontend_theme'] . '/single.php';
                    return;
                }

                error_404();
            });
        }
    }

    // Posts with date in URL (if post_base is active)
    if ($config['post_base']) {
        foreach ($config['post_types'] as $post_type => $settings) {
            $slug_prefix = $settings['slug_prefix'];
            if ($slug_prefix !== '') {
                $router->map('GET|HEAD', '/' . $slug_prefix . '/[:year]/[:month]/[:slug]/', function ($year, $month, $slug) use ($config, $post_type) {
                    $post = get_single($slug, $post_type, $year, $month);

                    if ($post && $post->post_type === $post_type && isset($post->title)) {
                        include 'themes/' . $config['frontend_theme'] . '/single.php';
                        return;
                    }

                    error_404();
                });
            }
        }
    }
}

/* ============================================
   Route Matching and Dispatch
============================================ */

$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    error_404();
}
