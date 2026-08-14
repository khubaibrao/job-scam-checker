<?php
/**
 * Not-found template.
 *
 * @package JobScamCheckerTheme
 */

get_header();
?>
<main id="main-content" class="site-main">
    <div class="site-container content-container empty-state">
        <p class="section-kicker">404</p>
        <h1><?php esc_html_e( 'That page could not be found', 'job-scam-checker-theme' ); ?></h1>
        <p><?php esc_html_e( 'The page may have moved. You can return home or check a suspicious job message.', 'job-scam-checker-theme' ); ?></p>
        <a class="button-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'job-scam-checker-theme' ); ?></a>
    </div>
</main>
<?php get_footer(); ?>
