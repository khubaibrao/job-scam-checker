<?php
/**
 * Default page template.
 *
 * @package JobScamCheckerTheme
 */

get_header();
?>
<main id="main-content" class="site-main">
    <div class="site-container content-container">
        <?php while ( have_posts() ) : ?>
            <?php the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
                <header class="entry-header"><h1><?php the_title(); ?></h1></header>
                <div class="entry-content"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
