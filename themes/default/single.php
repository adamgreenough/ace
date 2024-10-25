<?php get_header($post->title, $post->excerpt, $post->image); ?>

<article>
	<?php
		if (!empty($post->image)) {
			printf('<img src="%s" alt="%s">',
				$post->image,
				$post->title
			);
		}
	?>
	
	<h1><?= $post->title; ?></h1>	
	<p class="lead">Posted on <?= date($config['date_format'], $post->date); ?> • Filed under <?= display_tag_list($post->tags); ?>

	<?= $post->body; ?>
</article>

<?php get_footer(); ?>