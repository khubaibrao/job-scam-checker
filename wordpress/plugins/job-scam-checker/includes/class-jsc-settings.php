<?php
/** WordPress administration settings for anonymous aggregates. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JSC_Settings {
    public function register_hooks() {
        add_action( 'admin_menu', array( $this, 'add_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'jsc_daily_cleanup', array( $this, 'cleanup' ) );
    }

    public function add_page() {
        add_options_page( __( 'Job Scam Checker Privacy', 'job-scam-checker' ), __( 'Job Scam Checker', 'job-scam-checker' ), 'manage_options', 'job-scam-checker', array( $this, 'render_page' ) );
    }

    public function register_settings() {
        register_setting( 'jsc_privacy', 'jsc_statistics_enabled', array( 'type' => 'string', 'sanitize_callback' => array( $this, 'checkbox' ), 'default' => '0' ) );
        register_setting( 'jsc_privacy', 'jsc_follow_up_enabled', array( 'type' => 'string', 'sanitize_callback' => array( $this, 'checkbox' ), 'default' => '1' ) );
        register_setting( 'jsc_privacy', 'jsc_statistics_retention_days', array( 'type' => 'integer', 'sanitize_callback' => array( $this, 'retention' ), 'default' => 365 ) );
    }

    public function checkbox( $value ) { return '1' === (string) $value ? '1' : '0'; }
    public function retention( $value ) { return min( 730, max( 30, (int) $value ) ); }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) { return; }
        ?>
        <div class="wrap"><h1><?php esc_html_e( 'Job Scam Checker privacy settings', 'job-scam-checker' ); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'jsc_privacy' ); ?>
            <table class="form-table" role="presentation"><tbody>
            <tr><th scope="row"><?php esc_html_e( 'Anonymous statistics', 'job-scam-checker' ); ?></th><td><label><input type="hidden" name="jsc_statistics_enabled" value="0"><input type="checkbox" name="jsc_statistics_enabled" value="1" <?php checked( JSC_Statistics::enabled() ); ?>> <?php esc_html_e( 'Collect daily aggregate counters from checker results', 'job-scam-checker' ); ?></label><p class="description"><?php esc_html_e( 'Pasted messages, visitor profiles and contact details are never stored in these statistics.', 'job-scam-checker' ); ?></p></td></tr>
            <tr><th scope="row"><?php esc_html_e( 'Optional follow-up questions', 'job-scam-checker' ); ?></th><td><label><input type="hidden" name="jsc_follow_up_enabled" value="0"><input type="checkbox" name="jsc_follow_up_enabled" value="1" <?php checked( JSC_Statistics::follow_up_enabled() ); ?>> <?php esc_html_e( 'Ask channel and payment questions after a result', 'job-scam-checker' ); ?></label></td></tr>
            <tr><th scope="row"><label for="jsc-retention"><?php esc_html_e( 'Aggregate retention', 'job-scam-checker' ); ?></label></th><td><input id="jsc-retention" type="number" name="jsc_statistics_retention_days" min="30" max="730" value="<?php echo esc_attr( JSC_Statistics::retention_days() ); ?>"> <?php esc_html_e( 'days (30–730)', 'job-scam-checker' ); ?></td></tr>
            </tbody></table><?php submit_button(); ?>
        </form></div>
        <?php
    }

    public function cleanup() {
        global $wpdb;
        $cutoff = gmdate( 'Y-m-d', time() - DAY_IN_SECONDS * JSC_Statistics::retention_days() );
        ( new JSC_Statistics_Repository( $wpdb ) )->delete_before( $cutoff );
    }
}
