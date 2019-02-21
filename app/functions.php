<?php

// Return list of all posts, newest first
function get_post_list() {
    static $_cache = array();

    if(empty($_cache)){
        $_cache = array_reverse(glob('posts/*.md'));
    }

    return $_cache;
}

// Return an array of posts
function get_posts($page = 1, $perpage = 10) {
    $frontMatter = new Webuni\FrontMatter\FrontMatter();
    $posts = get_post_list();

    // Extract a specific page with results
    $posts = array_slice($posts, ($page-1) * $perpage, $perpage);

    $tmp = array();

    foreach($posts as $k=>$v) {
        $post = new stdClass;
        $content = $frontMatter->parse(file_get_contents($v));
       
        // Split the date & slug from file name
        $arr = explode('_', $v);
        $post->date = strtotime(str_replace('posts/','',$arr[0]));
        $post->slug = basename($arr[1], '.md');
        
        // Get the contents and convert it to HTML
		$meta = $content->getData();
		$post->title = $meta['title'];
        $post->body = convert_markdown($content->getContent());

        $tmp[] = $post;
    }

    return $tmp;
}

// Convert markdown to HTML
function convert_markdown($post) {
	$md = new ParsedownExtra();	
	
	$post = $md->text($post);
	return $post;
}

// Convert array of posts into JSON
function generate_json($posts) {
    return json_encode($posts, JSON_PRETTY_PRINT);
}

// Load plugins from /plugins/ folder 
function load_plugins() {
	$plugins = array_filter(glob('plugins/*'), 'is_dir');
	foreach($plugins as $plugin) {
		$plugin = ltrim($plugin, 'plugins/');
		require 'plugins/' . $plugin . '/' . $plugin . '.php';
	}
}
