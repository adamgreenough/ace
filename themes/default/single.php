<?php get_header($post->title, $post->excerpt, $post->image); ?>

<article>
    <?php
        if (!empty($post->image)) {
            printf(
                '<img src="%s" alt="%s">',
                htmlspecialchars($post->image),
                htmlspecialchars($post->title)
            );
        }

        // Retrieve the taxonomy name for the current post type
        global $config;
        $post_types_config = $config['post_types'];
        $post_type_settings = $post_types_config[$post->post_type];
        $taxonomy_name = $post_type_settings['taxonomy'];
    ?>
    
    <h1><?= htmlspecialchars($post->title); ?></h1>   
    <p class="lead">
        Posted on <?= date($config['date_format'], $post->date); ?>
        <?php if (!empty($post->tags) && $taxonomy_name): ?>
            • Filed under <?= display_tag_list($post->tags, $taxonomy_name); ?>
        <?php endif; ?>
    </p>

    <?= $post->body; ?>
</article>

<?php get_footer(); ?>
