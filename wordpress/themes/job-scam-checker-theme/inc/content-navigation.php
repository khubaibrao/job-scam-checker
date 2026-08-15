<?php
/**
 * Breadcrumbs and contextual related content.
 *
 * @package JobScamCheckerTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return breadcrumb items for the current singular page.
 *
 * @return array<int,array<string,string>>
 */
function jsc_theme_breadcrumb_items() {
    if ( ! is_singular() || is_front_page() ) {
        return array();
    }

    $items = array( array( 'label' => __( 'Home', 'job-scam-checker-theme' ), 'url' => home_url( '/' ) ) );
    $post  = get_queried_object();

    foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor_id ) {
        $items[] = array( 'label' => get_the_title( $ancestor_id ), 'url' => get_permalink( $ancestor_id ) );
    }
    $items[] = array( 'label' => get_the_title( $post ), 'url' => get_permalink( $post ) );

    return $items;
}

/**
 * Print visible breadcrumb navigation.
 */
function jsc_theme_breadcrumbs() {
    $items = jsc_theme_breadcrumb_items();
    if ( empty( $items ) ) {
        return;
    }
    ?>
    <nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'job-scam-checker-theme' ); ?>">
        <ol>
            <?php foreach ( $items as $index => $item ) : ?>
                <li>
                    <?php if ( $index < count( $items ) - 1 ) : ?>
                        <a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
                    <?php else : ?>
                        <span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}

/**
 * Print curated related links saved by the content installer.
 */
function jsc_theme_related_content() {
    $related_keys = get_post_meta( get_the_ID(), '_jsc_related_pages', true );
    $installed    = get_option( 'jsc_installed_pages', array() );
    if ( ! is_array( $related_keys ) || ! is_array( $installed ) ) {
        return;
    }

    $related = array();
    foreach ( $related_keys as $key ) {
        if ( empty( $installed[ $key ] ) || (int) $installed[ $key ] === get_the_ID() ) {
            continue;
        }
        $page = get_post( (int) $installed[ $key ] );
        if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
            $related[] = $page;
        }
    }
    if ( empty( $related ) ) {
        return;
    }
    ?>
    <aside class="related-content" aria-labelledby="related-content-title">
        <h2 id="related-content-title"><?php esc_html_e( 'Related information', 'job-scam-checker-theme' ); ?></h2>
        <div class="related-content__grid">
            <?php foreach ( $related as $page ) : ?>
                <article>
                    <h3><a href="<?php echo esc_url( get_permalink( $page ) ); ?>"><?php echo esc_html( get_the_title( $page ) ); ?></a></h3>
                    <?php $description = get_post_meta( $page->ID, '_jsc_seo_description', true ); ?>
                    <?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </aside>
    <?php
}
