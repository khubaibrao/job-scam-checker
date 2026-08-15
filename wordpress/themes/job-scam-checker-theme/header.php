<?php
/**
 * Site header.
 *
 * @package JobScamCheckerTheme
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'job-scam-checker-theme' ); ?></a>
<header class="site-header">
    <div class="site-container site-header__inner">
        <div class="site-branding">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                    <span class="site-brand__mark" aria-hidden="true">&#10003;</span>
                    <span><?php bloginfo( 'name' ); ?></span>
                </a>
            <?php endif; ?>
        </div>

        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" data-menu-toggle>
            <span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'job-scam-checker-theme' ); ?></span>
            <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
        </button>

        <nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'job-scam-checker-theme' ); ?>" data-primary-nav>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => 'jsc_theme_primary_menu_fallback',
                )
            );
            ?>
        </nav>
        <details class="header-search"><summary><?php esc_html_e( 'Search', 'job-scam-checker-theme' ); ?></summary><div><?php get_search_form(); ?></div></details>
    </div>
</header>
<?php
/**
 * Minimal useful navigation before an administrator assigns a menu.
 *
 * @param array<string,mixed> $args Menu arguments.
 */
function jsc_theme_primary_menu_fallback( $args ) {
    ?>
    <ul id="<?php echo esc_attr( $args['menu_id'] ); ?>" class="menu">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'job-scam-checker-theme' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/job-scam-checker/' ) ); ?>"><?php esc_html_e( 'Check a Message', 'job-scam-checker-theme' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/job-scams/' ) ); ?>"><?php esc_html_e( 'Scam Types', 'job-scam-checker-theme' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/guides/' ) ); ?>"><?php esc_html_e( 'Guides', 'job-scam-checker-theme' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'job-scam-checker-theme' ); ?></a></li>
    </ul>
    <?php
}
