<?php 
    $title = 'Posts tagged with "' . htmlspecialchars($term) . '"';
    get_header($title); 
?>

<h1><?= $title; ?></h1>

<?php foreach ($posts as $post): ?>      
    <article class="blog-preview">
        <a href="<?= get_post_link($post); ?>">
            <h2><?= htmlspecialchars($post->title); ?></h2>
        </a>
        <p><?= $post->excerpt; ?></p>
        <p class="small">
            <?= date($config['date_format'], $post->date); ?>
            <?php
                // Retrieve the taxonomy name for the current post type
                $post_types_config = $config['post_types'];
                $post_type_settings = $post_types_config[$post->post_type];
                $taxonomy_name = $post_type_settings['taxonomy'];

                if (!empty($post->tags) && $taxonomy_name):
            ?>
                • Filed under <?= display_tag_list($post->tags, $taxonomy_name); ?>
            <?php endif; ?>
        </p>
    </article>
<?php endforeach; ?>

<div class="pagination">
    <div class="prev">
        <?php 
            $pagination = get_pagination_link($page, $term, $taxonomy_name, 'all');
            $prevLink = $pagination['prev'];
            if($prevLink): ?>
                <a href="<?= $prevLink; ?>" title="Previous Page">&laquo; Newer Posts</a>
        <?php endif; ?>
    </div>
    <div class="next">
        <?php 
            $nextLink = $pagination['next'];
            if($nextLink): ?>
                <a href="<?= $nextLink; ?>" title="Next Page">Older Posts &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
