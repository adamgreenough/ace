<?php

function display_tag_list($tags, $taxonomy_name) {
    $output = [];
    foreach ($tags as $tag) {
        $output[] = '<a href="' . get_taxonomy_link($taxonomy_name, $tag) . '">' . htmlspecialchars($tag) . '</a>';
    }
    return implode(', ', $output);
}
