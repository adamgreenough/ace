<?php
// Return list of all pages
function get_page_list($slug = '*') {
    $pageList = array_reverse(glob('pages/' . $slug . '.md'));
    return $pageList;
}

// Get the full contents of a single page
function get_page($page) {
    $frontMatter = new Webuni\FrontMatter\FrontMatter();
    $contentFiles = get_page_list($page);

    if ($contentFiles) { // Check page exists
        $pageObj = new stdClass;
        $content = $frontMatter->parse(file_get_contents($contentFiles[0]));
        
        // Get the contents and convert it to HTML
        $meta = $content->getData();
        $pageObj->title = $meta['title'] ?? 'No title';
        $pageObj->body = convert_markdown($content->getContent());
        
        return $pageObj;
    }
    return null;
}
