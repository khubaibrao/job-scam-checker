<?php
/**
 * Idempotent page installer framework.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Content_Installer {
    /**
     * Return content definitions for this release.
     *
     * Later phases can append definitions without duplicating existing pages.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function get_page_definitions() {
        $foundation = array(
            'home' => array(
                'title'   => __( 'Home', 'job-scam-checker' ),
                'slug'    => 'home',
                'content' => '<!-- wp:paragraph --><p>' . esc_html__( 'Check suspicious job offers and recruiter messages for common warning signs.', 'job-scam-checker' ) . '</p><!-- /wp:paragraph -->',
                'seo_title' => __( 'Job Scam Checker: Check a Suspicious Job Offer', 'job-scam-checker' ),
                'description' => __( 'Check suspicious job offers and recruiter messages for common scam warning signs. Free, private and no signup required.', 'job-scam-checker' ),
                'content_type' => 'core',
                'upgrade_from' => array( '<!-- wp:paragraph --><p>' . esc_html__( 'Check suspicious job offers and recruiter messages for common warning signs.', 'job-scam-checker' ) . '</p><!-- /wp:paragraph -->' ),
            ),
        );

        return array_merge( $foundation, require JSC_PLUGIN_DIR . 'data/content-pages.php' );
    }

    /**
     * Install a new content release after plugin updates.
     */
    public static function maybe_install() {
        if ( JSC_CONTENT_VERSION !== get_option( 'jsc_content_version' ) ) {
            self::install();
        }
    }

    /**
     * Create missing pages and configure a static homepage.
     *
     * @return array<string,int> Installed or existing page IDs.
     */
    public static function install() {
        $page_ids    = array();
        $definitions = self::get_page_definitions();

        foreach ( $definitions as $key => $definition ) {
            $parent_id = ! empty( $definition['parent'] ) && ! empty( $page_ids[ $definition['parent'] ] ) ? $page_ids[ $definition['parent'] ] : 0;
            $path      = $definition['slug'];
            if ( ! empty( $definition['parent'] ) ) {
                $parent_definition = $definitions[ $definition['parent'] ];
                $path              = $parent_definition['slug'] . '/' . $definition['slug'];
            }
            $existing = get_page_by_path( $path, OBJECT, 'page' );

            if ( $existing instanceof WP_Post ) {
                $page_ids[ $key ] = (int) $existing->ID;
                self::upgrade_foundation_content( $existing, $definition );
                continue;
            }

            $page_id = wp_insert_post(
                array(
                    'post_title'     => $definition['title'],
                    'post_name'      => $definition['slug'],
                    'post_content'   => $definition['content'],
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'post_parent'    => $parent_id,
                    'comment_status' => 'closed',
                ),
                true
            );

            if ( ! is_wp_error( $page_id ) ) {
                $page_ids[ $key ] = (int) $page_id;
                self::save_page_metadata( $page_id, $definition );
            }
        }

        if ( ! empty( $page_ids['home'] ) ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $page_ids['home'] );
        }

        update_option( 'jsc_installed_pages', $page_ids, false );
        update_option( 'jsc_content_version', JSC_CONTENT_VERSION, false );

        return $page_ids;
    }

    /**
     * Upgrade only the exact untouched Phase 1 checker shell.
     */
    private static function upgrade_foundation_content( $page, array $definition ) {
        if ( empty( $definition['upgrade_from'] ) || ! isset( $page->post_content ) || ! in_array( $page->post_content, $definition['upgrade_from'], true ) ) {
            return;
        }

        wp_update_post( array( 'ID' => $page->ID, 'post_content' => $definition['content'] ) );
        self::save_page_metadata( $page->ID, $definition );
    }

    /**
     * Store page-specific SEO and relationship data for theme presentation.
     */
    private static function save_page_metadata( $page_id, array $definition ) {
        $metadata = array(
            '_jsc_seo_title'       => $definition['seo_title'] ?? $definition['title'],
            '_jsc_seo_description' => $definition['description'] ?? '',
            '_jsc_content_type'    => $definition['content_type'] ?? 'core',
            '_jsc_related_pages'   => $definition['related'] ?? array(),
            '_jsc_managed_content_version' => JSC_CONTENT_VERSION,
        );

        foreach ( $metadata as $key => $value ) {
            update_post_meta( $page_id, $key, $value );
        }
    }
}
