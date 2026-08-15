<?php
/**
 * Default page template.
 *
 * @package JobScamCheckerTheme
 */

get_header();
?>
<main id="main-content" class="site-main">
    <?php $jsc_content_type = get_post_meta( get_queried_object_id(), '_jsc_content_type', true ); ?>
    <div class="site-container content-container <?php echo in_array( $jsc_content_type, array( 'hub', 'tool' ), true ) ? 'content-container--wide' : ''; ?>">
        <?php while ( have_posts() ) : ?>
            <?php the_post(); ?>
            <?php jsc_theme_breadcrumbs(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
                <header class="entry-header"><h1><?php the_title(); ?></h1></header>
                <div class="entry-content"><?php the_content(); ?></div>
            </article>
            <?php jsc_theme_related_content(); ?>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
