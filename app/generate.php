<?php
use Michelf\MarkdownExtra;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Item;

function convert_markdown($content) {
    return MarkdownExtra::defaultTransform($content);
}

function generate_json($posts) {
    return json_encode($posts, JSON_PRETTY_PRINT);
}

function generate_rss($posts) {
    global $config;

    $feed = new Feed();
    $channel = new Channel();

    $channel
        ->title($config['blog_name'])
        ->description($config['blog_description'])
        ->appendTo($feed);

    foreach ($posts as $p) {
        $item = new Item();
        $url = get_post_link($p);

        $item
            ->title($p->title)
            ->description($p->body)
            ->url($url)
            ->appendTo($channel);
    }

    return $feed;
}
