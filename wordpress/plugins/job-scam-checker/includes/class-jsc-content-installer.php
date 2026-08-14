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
     * @return array<string,array<string,string>>
     */
    public static function get_page_definitions() {
        return array(
            'home' => array(
                'title'   => __( 'Home', 'job-scam-checker' ),
                'slug'    => 'home',
                'content' => '<!-- wp:paragraph --><p>' . esc_html__( 'Check suspicious job offers and recruiter messages for common warning signs.', 'job-scam-checker' ) . '</p><!-- /wp:paragraph -->',
            ),
            'job_scam_checker' => array(
                'title'   => __( 'Job Scam Checker', 'job-scam-checker' ),
                'slug'    => 'job-scam-checker',
                'content' => '<!-- wp:shortcode -->[job_scam_checker]<!-- /wp:shortcode -->',
            ),
        );
    }

    /**
     * Create missing pages and configure a static homepage.
     *
     * @return array<string,int> Installed or existing page IDs.
     */
    public static function install() {
        $page_ids = array();

        foreach ( self::get_page_definitions() as $key => $definition ) {
            $existing = get_page_by_path( $definition['slug'], OBJECT, 'page' );

            if ( $existing instanceof WP_Post ) {
                $page_ids[ $key ] = (int) $existing->ID;
                continue;
            }

            $page_id = wp_insert_post(
                array(
                    'post_title'     => $definition['title'],
                    'post_name'      => $definition['slug'],
                    'post_content'   => $definition['content'],
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'comment_status' => 'closed',
                ),
                true
            );

            if ( ! is_wp_error( $page_id ) ) {
                $page_ids[ $key ] = (int) $page_id;
            }
        }

        if ( ! empty( $page_ids['home'] ) ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $page_ids['home'] );
        }

        update_option( 'jsc_installed_pages', $page_ids, false );

        return $page_ids;
    }
}
