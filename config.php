<?php
return [

    /* ============================================
       Blog Settings
     ============================================ */

    // Posts per page
    'posts_per_page' => 4,

    // The direct URL to your blog without a trailing slash (e.g., https://adgr.dev or https://adgr.dev/blog)
    'blog_url' => 'https://example.com', 

    // If your CMS installation is not in the web root,
    // enter your folder name here with no preceding slash (e.g., 'blog') 
    'base_path' => '',

    // The name of your blog
    'blog_name' => 'Nicholas Demo',

    // A short description of your blog
    'blog_description' => 'Welcome to my amazing blog powered by Nicholas',

    /* ============================================
       Front-end Settings
     ============================================ */
     
    // Use the front-end (true) or API-only (false)?
    'use_frontend' => true,

    // Front-end theme
    'frontend_theme' => 'default',
     
    // Date format
    'date_format' => 'jS F Y',

    // Page title separator
    'title_separator' => '|',

    /* ============================================
       Advanced Settings
     ============================================ */

    // Prepend year and month to post URLs to help avoid slug conflicts?
    'post_base' => false

];
