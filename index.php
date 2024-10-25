<?php
// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$start_time = microtime(true);

// Autoload dependencies and include necessary files
require_once 'vendor/autoload.php';
require_once 'app/core.php';
require_once 'app/posts.php';
require_once 'app/pages.php';
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
$router->map('GET|HEAD', '/json/', function () {
    header('Content-Type: application/json');
    $posts = get_posts();
    if ($posts) {
        echo generate_json($posts);
    } else {
        echo json_encode(['error' => 'No posts found']);
    }
});

// RSS Feed
$router->map('GET|HEAD', '/rss/', function () {
    header('Content-Type: application/xml');
    $posts = get_posts();
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

    // Tag Page
    $router->map('GET|HEAD', '/tag/[:tag]/[i:page]?/', function ($tag, $page = 1) use ($config) {
        $tag = urldecode($tag);
        $posts = get_posts($page, $config['posts_per_page'], $tag);

        if ($posts) {
            include 'themes/' . $config['frontend_theme'] . '/tag.php';
        } else {
            error_404();
        }
    });

    // Home Page
    $router->map('GET|HEAD', '/[i:page]?/', function ($page = 1) use ($config) {
        $posts = get_posts($page);

        if ($posts) {
            include 'themes/' . $config['frontend_theme'] . '/home.php';
        } else {
            error_404();
        }
    });

    // Single Post or Page
    $router->map('GET|HEAD', '/[:slug]/', function ($slug) use ($config) {
        // Attempt to get post
        $post = get_single($slug);

        if ($post && isset($post->title)) {
            include 'themes/' . $config['frontend_theme'] . '/single.php';
            return;
        }

        // Attempt to get page
        $page = get_page($slug);
        if ($page && isset($page->title)) {
            include 'themes/' . $config['frontend_theme'] . '/page.php';
            return;
        }

        // If neither post nor page is found, display 404
        error_404();
    });

    // Posts with date in URL (if post_base is active)
    if ($config['post_base']) {
        $router->map('GET|HEAD', '/[:year]/[:month]/[:slug]/', function ($year, $month, $slug) use ($config) {
            $post = get_single($slug, $year, $month);
            if ($post && isset($post->title)) {
                include 'themes/' . $config['frontend_theme'] . '/single.php';
            } else {
                error_404();
            }
        });
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
