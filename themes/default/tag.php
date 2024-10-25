<?php 
	$title = 'Posts tagged ' . $tag;
	get_header($title); 
?>

<h1>Posts tagged "<?= $tag ?>"</h1>
		
<?php foreach($posts as $post) { ?>		
	<article class="blog-preview">
		<a href="<?= $config['base_path'] . '/' . $post->slug; ?>/">
			<h2><?= $post->title; ?></h2>
		</a>
		<p><?= $post->excerpt; ?></p>
		<p class="small"><?= date($config['date_format'], $post->date); ?> • Filed under <?= display_tag_list($post->tags); ?></p>
	</article>
<?php } ?>

<div class="pagination">
	<div class="prev">
		<?php 
			$prevLink = get_pagination_link($page, $posts, $tag)['prev'];
			if($prevLink) { echo '<a href="' . $prevLink . '" title="Previous Page">&laquo; Newer Posts </a>'; }
		?>	
	</div>
	<div class="next">
		<?php 
			$nextLink = get_pagination_link($page, $posts, $tag)['next'];
			if($nextLink) { echo '<a href="' . $nextLink . '" title="Next Page">Older Posts &raquo;</a>'; }
		?>	
	</div>
</div>

<?php get_footer(); ?>