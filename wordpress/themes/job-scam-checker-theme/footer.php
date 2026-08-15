<?php
/**
 * Site footer.
 *
 * @package JobScamCheckerTheme
 */
?>
<footer class="site-footer">
    <div class="site-container site-footer__grid">
        <div>
            <a class="site-brand site-brand--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <span class="site-brand__mark" aria-hidden="true">&#10003;</span>
                <span><?php bloginfo( 'name' ); ?></span>
            </a>
            <p><?php esc_html_e( 'A free, privacy-focused utility for spotting common warning signs in suspicious job communications.', 'job-scam-checker-theme' ); ?></p>
        </div>
        <nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer navigation', 'job-scam-checker-theme' ); ?>">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'footer',
                    'container'      => false,
                    'fallback_cb'    => 'jsc_theme_footer_menu_fallback',
                    'depth'          => 1,
                )
            );
            ?>
        </nav>
    </div>
    <div class="site-container site-footer__bottom">
        <p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.</p>
        <p><?php esc_html_e( 'This tool provides general guidance, not a guarantee that an offer is legitimate or fraudulent.', 'job-scam-checker-theme' ); ?></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
<?php
/**
 * Provide trust and legal navigation before a custom footer menu is assigned.
 */
function jsc_theme_footer_menu_fallback() {
    ?>
    <ul class="menu">
        <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'job-scam-checker-theme' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'job-scam-checker-theme' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'job-scam-checker-theme' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/terms-of-use/' ) ); ?>"><?php esc_html_e( 'Terms', 'job-scam-checker-theme' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>"><?php esc_html_e( 'Disclaimer', 'job-scam-checker-theme' ); ?></a></li>
    </ul>
    <?php
}
