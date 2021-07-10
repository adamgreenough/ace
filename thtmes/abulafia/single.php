<?php get_header($post->title, $post->excerpt); ?>

<div id="article-container">
<article>
	<img src="<?= $post->image; ?>" alt="<?= $post->title; ?>">
	
	<h1><?= $post->title; ?></h1>	
	<p class="lead">Posted on <?= date($config['date_format'], $post->date); ?> • Filed under <?= display_tag_list($post->tags); ?>

	<?= $post->body; ?>
</article>
</div>

<?php get_footer(); ?>
