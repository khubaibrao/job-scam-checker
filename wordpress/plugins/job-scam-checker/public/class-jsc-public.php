<?php
/**
 * Public checker shell.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Public {
    /**
     * Register public hooks.
     */
    public function register_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
        add_shortcode( 'job_scam_checker', array( $this, 'render_checker' ) );
        add_shortcode( 'jsc_ad_slot', array( $this, 'render_ad_slot' ) );
        add_shortcode( 'jsc_trends', array( $this, 'render_trends' ) );
    }

    /**
     * Register assets without loading them on pages that do not use the checker.
     */
    public function register_assets() {
        wp_register_style(
            'jsc-checker',
            JSC_PLUGIN_URL . 'assets/css/checker.css',
            array(),
            JSC_VERSION
        );
        wp_register_script(
            'jsc-checker',
            JSC_PLUGIN_URL . 'assets/js/checker.js',
            array(),
            JSC_VERSION,
            true
        );
    }

    /**
     * Render the checker interface and same-origin API configuration.
     *
     * @return string
     */
    public function render_checker() {
        static $instance = 0;
        ++$instance;
        $instance_id = 'jsc-checker-' . $instance;

        wp_enqueue_style( 'jsc-checker' );
        wp_enqueue_script( 'jsc-checker' );
        wp_localize_script(
            'jsc-checker',
            'JSCCheckerConfig',
            array(
                'endpoint' => esc_url_raw( rest_url( 'job-scam-checker/v1/analyze' ) ),
                'followUpEndpoint' => esc_url_raw( rest_url( 'job-scam-checker/v1/follow-up' ) ),
                'followUpEnabled'  => JSC_Statistics::follow_up_enabled(),
                'nonce'    => wp_create_nonce( 'jsc_analyze_message' ),
                'labels'   => array(
                    'checking'       => __( 'Checking message…', 'job-scam-checker' ),
                    'error'          => __( 'The message could not be checked. Please try again.', 'job-scam-checker' ),
                    'warningSigns'   => __( 'Warning signs detected', 'job-scam-checker' ),
                    'whyItMatters'   => __( 'Why it matters', 'job-scam-checker' ),
                    'whatToDo'       => __( 'What to do', 'job-scam-checker' ),
                    'recommended'    => __( 'Recommended next steps', 'job-scam-checker' ),
                    'domains'        => __( 'Links and domains to examine carefully', 'job-scam-checker' ),
                    'noWarnings'     => __( 'No common warning signs were detected by the current rules.', 'job-scam-checker' ),
                    'verify'         => __( 'Always verify the recruiter and company independently before proceeding.', 'job-scam-checker' ),
                    'warningSingular'=> __( 'warning sign detected', 'job-scam-checker' ),
                    'warningPlural'  => __( 'warning signs detected', 'job-scam-checker' ),
                    'resultReady'    => __( 'Analysis complete.', 'job-scam-checker' ),
                    'configuration'  => __( 'Checker configuration is unavailable. Refresh the page and try again.', 'job-scam-checker' ),
                    'followUpThanks' => __( 'Thank you. Your anonymous answers were added to aggregate totals.', 'job-scam-checker' ),
                    'followUpError'  => __( 'The optional answers could not be submitted. Your checker result is unaffected.', 'job-scam-checker' ),
                ),
            )
        );

        ob_start();
        require JSC_PLUGIN_DIR . 'public/views/checker-form.php';
        return (string) ob_get_clean();
    }

    /**
     * Render an inert integration target for a future real advertising setup.
     *
     * @param array<string,string> $attributes Shortcode attributes.
     * @return string
     */
    public function render_ad_slot( $attributes = array() ) {
        $attributes = shortcode_atts( array( 'position' => 'article' ), $attributes, 'jsc_ad_slot' );
        $position   = sanitize_key( $attributes['position'] );

        return '<aside class="jsc-content-ad-slot" data-jsc-ad-position="' . esc_attr( $position ) . '" aria-label="' . esc_attr__( 'Advertisement', 'job-scam-checker' ) . '" hidden></aside>';
    }

    /** Render honest trends only after both comparison periods meet thresholds. */
    public function render_trends() {
        global $wpdb;
        $cached = get_transient( 'jsc_public_trends' );
        if ( ! is_array( $cached ) || ! array_key_exists( 'items', $cached ) ) {
            $cached = array( 'items' => ( new JSC_Trend_Provider( new JSC_Statistics_Repository( $wpdb ) ) )->get_trends() );
            set_transient( 'jsc_public_trends', $cached, 15 * MINUTE_IN_SECONDS );
        }
        $trends = $cached['items'];
        ob_start();
        ?>
        <section class="jsc-trends" aria-labelledby="jsc-trends-title">
            <p class="jsc-eyebrow"><?php esc_html_e( 'Based on anonymous aggregate checks', 'job-scam-checker' ); ?></p>
            <h2 id="jsc-trends-title"><?php esc_html_e( 'Trending Job Scam Patterns', 'job-scam-checker' ); ?></h2>
            <?php if ( empty( $trends ) ) : ?>
                <p class="jsc-trends__empty"><?php esc_html_e( 'Not enough real data yet to show a trend.', 'job-scam-checker' ); ?></p>
            <?php else : ?>
                <p><?php esc_html_e( 'These patterns appeared more often relative to all checks in the latest 14 days than in the preceding 14 days.', 'job-scam-checker' ); ?></p>
                <ul><?php foreach ( $trends as $trend ) : ?><li><?php echo esc_html( $trend['label'] ); ?></li><?php endforeach; ?></ul>
            <?php endif; ?>
        </section>
        <?php
        return (string) ob_get_clean();
    }
}
