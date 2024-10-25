<?php
	
function display_tag_list($tags) {
	global $config;
	$output = '';
	
	foreach ($tags as $tag) {
		$output .= 	'<a href="' . $config['base_path'] . '/tag/' . strtolower($tag) . '/" title="Posts tagged ' . $tag . '">' . $tag . '</a>, ';
	}
	
	$output = rtrim($output, ', ');
	return $output;
}