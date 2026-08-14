<?php
/**
 * Posts fallback template.
 *
 * @package JobScamCheckerTheme
 */

get_header();
?>
<main id="main-content" class="site-main">
    <div class="site-container content-container">
        <header class="archive-header"><h1><?php bloginfo( 'name' ); ?></h1></header>
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : ?>
                <?php the_post(); ?>
                <article <?php post_class( 'entry-summary' ); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
            <?php the_posts_navigation(); ?>
        <?php else : ?>
            <p><?php esc_html_e( 'No content was found.', 'job-scam-checker-theme' ); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
