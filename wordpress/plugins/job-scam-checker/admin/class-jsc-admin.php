<?php
/** Native WordPress administration for Job Scam Checker. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JSC_Admin {
    const CAPABILITY = 'manage_options';
    private $repository;
    private $rule_labels;

    public function __construct( JSC_Rule_Repository $repository = null ) {
        if ( $repository ) { $this->repository = $repository; }
    }

    private function repository() {
        if ( ! $this->repository ) { global $wpdb; $this->repository = new JSC_Rule_Repository( $wpdb ); }
        return $this->repository;
    }

    public function register_hooks() {
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
        add_action( 'admin_post_jsc_save_rule', array( $this, 'save_rule' ) );
        add_action( 'admin_post_jsc_rule_action', array( $this, 'rule_action' ) );
        add_action( 'admin_post_jsc_reset_statistics', array( $this, 'reset_statistics' ) );
    }

    public function menu() {
        add_menu_page( __( 'Job Scam Checker', 'job-scam-checker' ), __( 'Job Scam Checker', 'job-scam-checker' ), self::CAPABILITY, 'jsc-dashboard', array( $this, 'dashboard' ), 'dashicons-shield-alt', 58 );
        add_submenu_page( 'jsc-dashboard', __( 'Overview', 'job-scam-checker' ), __( 'Overview', 'job-scam-checker' ), self::CAPABILITY, 'jsc-dashboard', array( $this, 'dashboard' ) );
        add_submenu_page( 'jsc-dashboard', __( 'Rules', 'job-scam-checker' ), __( 'Rules', 'job-scam-checker' ), self::CAPABILITY, 'jsc-rules', array( $this, 'rules' ) );
        add_submenu_page( 'jsc-dashboard', __( 'Edit rule', 'job-scam-checker' ), __( 'Add rule', 'job-scam-checker' ), self::CAPABILITY, 'jsc-rule-edit', array( $this, 'edit_rule' ) );
        add_submenu_page( 'jsc-dashboard', __( 'Settings', 'job-scam-checker' ), __( 'Settings', 'job-scam-checker' ), self::CAPABILITY, 'jsc-settings', array( $this, 'settings' ) );
        add_submenu_page( 'jsc-dashboard', __( 'Statistics', 'job-scam-checker' ), __( 'Statistics', 'job-scam-checker' ), self::CAPABILITY, 'jsc-statistics', array( $this, 'statistics' ) );
        add_submenu_page( 'jsc-dashboard', __( 'Content', 'job-scam-checker' ), __( 'Content', 'job-scam-checker' ), self::CAPABILITY, 'jsc-content', array( $this, 'content' ) );
    }

    public function assets( $hook ) {
        if ( false === strpos( (string) $hook, 'jsc-' ) ) { return; }
        wp_enqueue_style( 'jsc-admin', JSC_PLUGIN_URL . 'admin/css/admin.css', array(), JSC_VERSION );
    }

    private function authorize( $nonce_action, $nonce_name = '_wpnonce' ) {
        if ( ! current_user_can( self::CAPABILITY ) ) { wp_die( esc_html__( 'You are not allowed to manage Job Scam Checker.', 'job-scam-checker' ), '', array( 'response' => 403 ) ); }
        check_admin_referer( $nonce_action, $nonce_name );
    }

    private function page_start( $title ) {
        if ( ! current_user_can( self::CAPABILITY ) ) { wp_die( esc_html__( 'You are not allowed to view this page.', 'job-scam-checker' ), '', array( 'response' => 403 ) ); }
        echo '<div class="wrap jsc-admin"><h1>' . esc_html( $title ) . '</h1>';
        if ( isset( $_GET['jsc_notice'] ) ) { echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( sanitize_text_field( wp_unslash( $_GET['jsc_notice'] ) ) ) . '</p></div>'; } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only notice.
        if ( isset( $_GET['jsc_error'] ) ) { echo '<div class="notice notice-error"><p>' . esc_html( sanitize_text_field( wp_unslash( $_GET['jsc_error'] ) ) ) . '</p></div>'; } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only notice.
    }

    private function stats_repository() { global $wpdb; return new JSC_Statistics_Repository( $wpdb ); }

    public function dashboard() {
        $this->page_start( __( 'Job Scam Checker overview', 'job-scam-checker' ) );
        $stats = $this->stats_repository();
        $total = array_sum( $stats->totals( 'checks' ) );
        $this->summary_cards( $total, $stats->totals( 'risk_level' ) );
        $today = current_time( 'Y-m-d', true );
        $recent = array_sum( $stats->counts( 'checks', gmdate( 'Y-m-d', strtotime( $today . ' -13 days' ) ), $today ) );
        echo '<div class="jsc-panel"><h2>' . esc_html__( 'Recent anonymous totals', 'job-scam-checker' ) . '</h2><p><strong>' . esc_html( number_format_i18n( $recent ) ) . '</strong> ' . esc_html__( 'checks in the latest 14-day period.', 'job-scam-checker' ) . '</p>' . ( 0 === $recent ? '<p>' . esc_html__( 'No aggregate checks were recorded in this period.', 'job-scam-checker' ) . '</p>' : '' ) . '</div>';
        echo '<div class="jsc-admin-grid">';
        $this->metric_table( __( 'Most frequent warning signs', 'job-scam-checker' ), $stats->totals( 'detection' ), 'detection' );
        $this->metric_table( __( 'Recruitment channels', 'job-scam-checker' ), $stats->totals( 'channel' ), 'channel' );
        $this->metric_table( __( 'Money requests', 'job-scam-checker' ), $stats->totals( 'money_request' ), 'money_request' );
        $this->metric_table( __( 'Payment purposes', 'job-scam-checker' ), $stats->totals( 'payment_purpose' ), 'payment_purpose' );
        echo '</div><div class="jsc-panel"><h2>' . esc_html__( 'System status', 'job-scam-checker' ) . '</h2><ul>';
        $status = array( __( 'Checker', 'job-scam-checker' ) => '0' !== get_option( 'jsc_checker_enabled', '1' ), __( 'Anonymous statistics', 'job-scam-checker' ) => JSC_Statistics::enabled(), __( 'Follow-up questions', 'job-scam-checker' ) => JSC_Statistics::follow_up_enabled(), __( 'Public trends', 'job-scam-checker' ) => '0' !== get_option( 'jsc_trends_visible', '1' ) );
        foreach ( $status as $label => $enabled ) { echo '<li><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $enabled ? __( 'Enabled', 'job-scam-checker' ) : __( 'Disabled', 'job-scam-checker' ) ) . '</li>'; }
        echo '<li><strong>' . esc_html__( 'Aggregate retention:', 'job-scam-checker' ) . '</strong> ' . esc_html( JSC_Statistics::retention_days() ) . ' ' . esc_html__( 'days', 'job-scam-checker' ) . '</li></ul></div></div>';
    }

    private function summary_cards( $total, array $risks ) {
        $cards = array( __( 'Total checks', 'job-scam-checker' ) => $total, __( 'Low risk', 'job-scam-checker' ) => $risks['low'] ?? 0, __( 'Caution', 'job-scam-checker' ) => $risks['caution'] ?? 0, __( 'High risk', 'job-scam-checker' ) => $risks['high'] ?? 0, __( 'Very high risk', 'job-scam-checker' ) => $risks['very_high'] ?? 0 );
        echo '<div class="jsc-summary">'; foreach ( $cards as $label => $count ) { echo '<div class="jsc-card"><span>' . esc_html( $label ) . '</span><strong>' . esc_html( number_format_i18n( $count ) ) . '</strong></div>'; } echo '</div>';
        if ( 0 === $total ) { echo '<div class="notice notice-info inline"><p>' . esc_html__( 'No anonymous aggregate checks have been recorded. Statistics are never estimated or fabricated.', 'job-scam-checker' ) . '</p></div>'; }
    }

    private function metric_table( $title, array $counts, $metric ) {
        echo '<section class="jsc-panel"><h2>' . esc_html( $title ) . '</h2>';
        if ( empty( $counts ) ) { echo '<p>' . esc_html__( 'No aggregate data yet.', 'job-scam-checker' ) . '</p></section>'; return; }
        echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Item', 'job-scam-checker' ) . '</th><th>' . esc_html__( 'Count', 'job-scam-checker' ) . '</th></tr></thead><tbody>';
        foreach ( $counts as $key => $count ) { echo '<tr><td>' . esc_html( $this->stat_label( $metric, $key ) ) . '</td><td>' . esc_html( number_format_i18n( $count ) ) . '</td></tr>'; }
        echo '</tbody></table></section>';
    }

    private function stat_label( $metric, $key ) {
        if ( 'detection' === $metric ) {
            if ( null === $this->rule_labels ) {
                $this->rule_labels = array();
                foreach ( $this->repository()->get_rules() as $rule ) { $this->rule_labels[ $rule['slug'] ] = $rule['name']; }
            }
            if ( isset( $this->rule_labels[ $key ] ) ) { return $this->rule_labels[ $key ]; }
        }
        return ucwords( str_replace( '_', ' ', $key ) );
    }

    public function rules() {
        $this->page_start( __( 'Detection rules', 'job-scam-checker' ) );
        $search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $category = isset( $_GET['category'] ) ? sanitize_key( wp_unslash( $_GET['category'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $status = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        echo '<a class="page-title-action" href="' . esc_url( admin_url( 'admin.php?page=jsc-rule-edit' ) ) . '">' . esc_html__( 'Add new rule', 'job-scam-checker' ) . '</a>';
        echo '<form method="get" class="jsc-filters"><input type="hidden" name="page" value="jsc-rules"><input type="search" name="s" value="' . esc_attr( $search ) . '" placeholder="' . esc_attr__( 'Search rules', 'job-scam-checker' ) . '"><select name="category"><option value="">' . esc_html__( 'All categories', 'job-scam-checker' ) . '</option>';
        foreach ( JSC_Rule_Validator::categories() as $item ) { echo '<option value="' . esc_attr( $item ) . '" ' . selected( $category, $item, false ) . '>' . esc_html( ucwords( str_replace( '_', ' ', $item ) ) ) . '</option>'; }
        echo '</select><select name="status"><option value="">' . esc_html__( 'Any status', 'job-scam-checker' ) . '</option><option value="enabled" ' . selected( $status, 'enabled', false ) . '>' . esc_html__( 'Enabled', 'job-scam-checker' ) . '</option><option value="disabled" ' . selected( $status, 'disabled', false ) . '>' . esc_html__( 'Disabled', 'job-scam-checker' ) . '</option></select>'; submit_button( __( 'Filter', 'job-scam-checker' ), 'secondary', '', false ); echo '</form>';
        $rules = $this->repository()->get_rules( $search, $category, $status );
        if ( empty( $rules ) ) { echo '<p>' . esc_html__( 'No rules match these filters.', 'job-scam-checker' ) . '</p></div>'; return; }
        echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Rule', 'job-scam-checker' ) . '</th><th>' . esc_html__( 'Category', 'job-scam-checker' ) . '</th><th>' . esc_html__( 'Type', 'job-scam-checker' ) . '</th><th>' . esc_html__( 'Weight', 'job-scam-checker' ) . '</th><th>' . esc_html__( 'Status', 'job-scam-checker' ) . '</th><th>' . esc_html__( 'Actions', 'job-scam-checker' ) . '</th></tr></thead><tbody>';
        foreach ( $rules as $rule ) { $edit = admin_url( 'admin.php?page=jsc-rule-edit&rule_id=' . (int) $rule['id'] ); echo '<tr><td><strong><a href="' . esc_url( $edit ) . '">' . esc_html( $rule['name'] ) . '</a></strong><br><code>' . esc_html( $rule['slug'] ) . '</code>' . ( ! empty( $rule['is_default'] ) ? '<br><span class="jsc-protected">' . esc_html__( 'Protected default', 'job-scam-checker' ) . '</span>' : '' ) . '</td><td>' . esc_html( ucwords( str_replace( '_', ' ', $rule['category'] ) ) ) . '</td><td>' . esc_html( $rule['match_type'] ) . '</td><td>' . esc_html( $rule['weight'] ) . '</td><td>' . esc_html( $rule['enabled'] ? __( 'Enabled', 'job-scam-checker' ) : __( 'Disabled', 'job-scam-checker' ) ) . '</td><td><a href="' . esc_url( $edit ) . '">' . esc_html__( 'Edit', 'job-scam-checker' ) . '</a> | ' . $this->rule_action_link( $rule['enabled'] ? 'disable' : 'enable', $rule['id'], $rule['enabled'] ? __( 'Disable', 'job-scam-checker' ) : __( 'Enable', 'job-scam-checker' ) ) . ' | ' . $this->rule_action_link( 'duplicate', $rule['id'], __( 'Duplicate', 'job-scam-checker' ) ); if ( empty( $rule['is_default'] ) ) { echo ' | ' . $this->rule_action_link( 'delete', $rule['id'], __( 'Delete', 'job-scam-checker' ), true ); } echo '</td></tr>'; }
        echo '</tbody></table></div>';
    }

    private function rule_action_link( $action, $id, $label, $destructive = false ) {
        $url = wp_nonce_url( admin_url( 'admin-post.php?action=jsc_rule_action&rule_action=' . $action . '&rule_id=' . (int) $id ), 'jsc_rule_action_' . (int) $id );
        return '<a' . ( $destructive ? ' class="jsc-delete" onclick="return confirm(\'' . esc_js( __( 'Delete this custom rule permanently?', 'job-scam-checker' ) ) . '\');"' : '' ) . ' href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
    }

    public function edit_rule() {
        $this->page_start( __( 'Rule editor', 'job-scam-checker' ) );
        $id = isset( $_GET['rule_id'] ) ? absint( $_GET['rule_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $rule = $id ? $this->repository()->get_rule( $id ) : null;
        if ( $id && ! $rule ) { echo '<p>' . esc_html__( 'Rule not found.', 'job-scam-checker' ) . '</p></div>'; return; }
        $rule = $rule ?: array( 'name'=>'', 'slug'=>'', 'match_type'=>'phrase', 'pattern'=>'', 'category'=>'payment', 'score_group'=>'custom', 'weight'=>10, 'explanation'=>'', 'recommendation'=>'', 'enabled'=>1, 'priority'=>100 );
        echo '<form action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" method="post"><input type="hidden" name="action" value="jsc_save_rule"><input type="hidden" name="rule_id" value="' . esc_attr( $id ) . '">'; wp_nonce_field( 'jsc_save_rule_' . $id );
        echo '<table class="form-table" role="presentation"><tbody>';
        $this->field( 'name', __( 'Rule name', 'job-scam-checker' ), $rule['name'], true ); $this->field( 'slug', __( 'Stable slug', 'job-scam-checker' ), $rule['slug'], true, ! empty( $rule['is_default'] ) );
        echo '<tr><th><label for="match_type">' . esc_html__( 'Detection type', 'job-scam-checker' ) . '</label></th><td><select id="match_type" name="match_type">'; foreach ( JSC_Rule_Validator::match_types() as $type ) { echo '<option value="' . esc_attr( $type ) . '" ' . selected( $rule['match_type'], $type, false ) . '>' . esc_html( $type ) . '</option>'; } echo '</select><p class="description">' . esc_html__( 'Phrase and contextual types accept text/JSON; regex is validated and never executes PHP.', 'job-scam-checker' ) . '</p></td></tr>';
        echo '<tr><th><label for="pattern">' . esc_html__( 'Detection configuration', 'job-scam-checker' ) . '</label></th><td><textarea class="large-text code" rows="6" id="pattern" name="pattern">' . esc_textarea( $rule['pattern'] ) . '</textarea></td></tr>';
        echo '<tr><th><label for="category">' . esc_html__( 'Category', 'job-scam-checker' ) . '</label></th><td><select id="category" name="category">'; foreach ( JSC_Rule_Validator::categories() as $category ) { echo '<option value="' . esc_attr( $category ) . '" ' . selected( $rule['category'], $category, false ) . '>' . esc_html( ucwords( str_replace( '_', ' ', $category ) ) ) . '</option>'; } echo '</select></td></tr>';
        $this->field( 'score_group', __( 'Scoring group', 'job-scam-checker' ), $rule['score_group'], true ); $this->field( 'weight', __( 'Risk weight (0–100)', 'job-scam-checker' ), $rule['weight'], true, false, 'number' ); $this->field( 'priority', __( 'Priority (lower runs first)', 'job-scam-checker' ), $rule['priority'], true, false, 'number' );
        echo '<tr><th><label for="explanation">' . esc_html__( 'Explanation', 'job-scam-checker' ) . '</label></th><td><textarea class="large-text" rows="3" id="explanation" name="explanation" required>' . esc_textarea( $rule['explanation'] ) . '</textarea></td></tr><tr><th><label for="recommendation">' . esc_html__( 'Recommended action', 'job-scam-checker' ) . '</label></th><td><textarea class="large-text" rows="3" id="recommendation" name="recommendation" required>' . esc_textarea( $rule['recommendation'] ) . '</textarea></td></tr><tr><th>' . esc_html__( 'Status', 'job-scam-checker' ) . '</th><td><label><input type="checkbox" name="enabled" value="1" ' . checked( $rule['enabled'], 1, false ) . '> ' . esc_html__( 'Rule enabled', 'job-scam-checker' ) . '</label></td></tr></tbody></table>'; submit_button( $id ? __( 'Update rule', 'job-scam-checker' ) : __( 'Create rule', 'job-scam-checker' ) ); echo '</form></div>';
    }

    private function field( $name, $label, $value, $required = false, $readonly = false, $type = 'text' ) { echo '<tr><th><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label></th><td><input class="regular-text" type="' . esc_attr( $type ) . '" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" ' . ( $required ? 'required ' : '' ) . ( $readonly ? 'readonly ' : '' ) . '></td></tr>'; }

    public function save_rule() {
        $id = isset( $_POST['rule_id'] ) ? absint( $_POST['rule_id'] ) : 0;
        $this->authorize( 'jsc_save_rule_' . $id );
        $input = wp_unslash( $_POST );
        $existing = $id ? $this->repository()->get_rule( $id ) : null;
        if ( $id && ! $existing ) { $this->redirect_error( __( 'Rule not found.', 'job-scam-checker' ) ); }
        if ( $existing && ! empty( $existing['is_default'] ) ) { $input['slug'] = $existing['slug']; }
        $validated = ( new JSC_Rule_Validator() )->validate( $input, $id );
        if ( is_wp_error( $validated ) ) { $this->redirect_error( $validated->get_error_message(), $id ); }
        if ( $this->repository()->slug_exists( $validated['slug'], $id ) ) { $this->redirect_error( __( 'That rule slug is already in use.', 'job-scam-checker' ), $id ); }
        $saved = $id ? $this->repository()->update( $id, $validated ) : $this->repository()->create( $validated );
        if ( ! $saved ) { $this->redirect_error( __( 'The rule could not be saved.', 'job-scam-checker' ), $id ); }
        wp_safe_redirect( add_query_arg( 'jsc_notice', rawurlencode( __( 'Rule saved.', 'job-scam-checker' ) ), admin_url( 'admin.php?page=jsc-rules' ) ) ); exit;
    }

    public function rule_action() {
        $id = isset( $_GET['rule_id'] ) ? absint( $_GET['rule_id'] ) : 0;
        $this->authorize( 'jsc_rule_action_' . $id );
        $rule = $this->repository()->get_rule( $id ); $action = isset( $_GET['rule_action'] ) ? sanitize_key( wp_unslash( $_GET['rule_action'] ) ) : '';
        if ( ! $rule ) { $this->redirect_error( __( 'Rule not found.', 'job-scam-checker' ) ); }
        if ( 'enable' === $action || 'disable' === $action ) { $this->repository()->set_enabled( $id, 'enable' === $action ); }
        elseif ( 'duplicate' === $action ) { unset( $rule['id'], $rule['created_at'], $rule['updated_at'], $rule['is_default'] ); $rule['name'] .= ' ' . __( '(Copy)', 'job-scam-checker' ); $rule['slug'] = $this->unique_slug( $rule['slug'] . '-copy' ); $this->repository()->create( $rule ); }
        elseif ( 'delete' === $action ) { if ( ! empty( $rule['is_default'] ) ) { $this->redirect_error( __( 'Default rules cannot be deleted. Disable one instead.', 'job-scam-checker' ) ); } $this->repository()->delete_custom( $id ); }
        else { $this->redirect_error( __( 'Invalid rule action.', 'job-scam-checker' ) ); }
        wp_safe_redirect( admin_url( 'admin.php?page=jsc-rules&jsc_notice=' . rawurlencode( __( 'Rule updated.', 'job-scam-checker' ) ) ) ); exit;
    }

    private function unique_slug( $base ) { $slug = sanitize_key( $base ); $number = 2; while ( $this->repository()->slug_exists( $slug ) ) { $slug = sanitize_key( $base . '-' . $number ); ++$number; } return $slug; }
    private function redirect_error( $message, $id = 0 ) { $url = admin_url( 'admin.php?page=' . ( $id ? 'jsc-rule-edit&rule_id=' . $id : 'jsc-rules' ) ); wp_safe_redirect( add_query_arg( 'jsc_error', rawurlencode( $message ), $url ) ); exit; }

    public function settings() {
        $this->page_start( __( 'Job Scam Checker settings', 'job-scam-checker' ) );
        echo '<form action="options.php" method="post">'; settings_fields( 'jsc_management' );
        $sections = array(
            __( 'Checker', 'job-scam-checker' ) => array( 'jsc_checker_enabled' => array( __( 'Enable checker', 'job-scam-checker' ), __( 'Show and accept checks on checker components.', 'job-scam-checker' ), '1' ), 'jsc_follow_up_enabled' => array( __( 'Optional follow-up questions', 'job-scam-checker' ), __( 'Ask channel and payment questions after a result when statistics are enabled.', 'job-scam-checker' ), '1' ), 'jsc_result_focus_enabled' => array( __( 'Move focus to results', 'job-scam-checker' ), __( 'After a successful check, move keyboard focus and the viewport to the result.', 'job-scam-checker' ), '1' ) ),
            __( 'Statistics', 'job-scam-checker' ) => array( 'jsc_statistics_enabled' => array( __( 'Anonymous aggregate statistics', 'job-scam-checker' ), __( 'Count only date, risk level, warning-rule slug and allow-listed follow-up selections. Pasted text and visitor identities are never stored.', 'job-scam-checker' ), '0' ) ),
            __( 'Search and content', 'job-scam-checker' ) => array( 'jsc_search_filters_enabled' => array( __( 'Search content-type filters', 'job-scam-checker' ), __( 'Allow visitors to narrow native site search by curated content type.', 'job-scam-checker' ), '1' ), 'jsc_related_content_enabled' => array( __( 'Related content', 'job-scam-checker' ), __( 'Show curated related-reading links on content pages.', 'job-scam-checker' ), '1' ) ),
            __( 'Display', 'job-scam-checker' ) => array( 'jsc_trends_visible' => array( __( 'Public trend component', 'job-scam-checker' ), __( 'Show the trend component; it still uses minimum sample safeguards and honest empty states.', 'job-scam-checker' ), '1' ) ),
        );
        foreach ( $sections as $heading => $fields ) { echo '<section class="jsc-panel"><h2>' . esc_html( $heading ) . '</h2><table class="form-table" role="presentation">'; foreach ( $fields as $name => $field ) { echo '<tr><th>' . esc_html( $field[0] ) . '</th><td><label><input type="hidden" name="' . esc_attr( $name ) . '" value="0"><input type="checkbox" name="' . esc_attr( $name ) . '" value="1" ' . checked( get_option( $name, $field[2] ), '1', false ) . '> ' . esc_html( $field[1] ) . '</label></td></tr>'; } echo '</table></section>'; }
        echo '<section class="jsc-panel"><h2>' . esc_html__( 'Rule categories', 'job-scam-checker' ) . '</h2><p>' . esc_html__( 'Add optional categories as comma-separated identifiers. Built-in categories remain available for engine compatibility.', 'job-scam-checker' ) . '</p><input class="large-text" name="jsc_rule_categories" value="' . esc_attr( implode( ', ', (array) get_option( 'jsc_rule_categories', array() ) ) ) . '"></section>';
        echo '<section class="jsc-panel"><h2>' . esc_html__( 'Privacy and retention', 'job-scam-checker' ) . '</h2><p>' . esc_html__( 'Collected: daily aggregate counts for checks, risk levels, detected rule slugs, and optional channel/payment selections. Never collected by this system: pasted messages, names, contact details, IP addresses, full URLs, credentials, or per-visitor histories.', 'job-scam-checker' ) . '</p><label for="jsc_statistics_retention_days"><strong>' . esc_html__( 'Aggregate retention', 'job-scam-checker' ) . '</strong></label> <input id="jsc_statistics_retention_days" type="number" min="30" max="730" name="jsc_statistics_retention_days" value="' . esc_attr( JSC_Statistics::retention_days() ) . '"> ' . esc_html__( 'days (30–730)', 'job-scam-checker' ) . '</section>'; submit_button(); echo '</form></div>';
    }

    public function statistics() {
        $this->page_start( __( 'Anonymous aggregate statistics', 'job-scam-checker' ) ); $stats = $this->stats_repository(); $this->summary_cards( array_sum( $stats->totals( 'checks' ) ), $stats->totals( 'risk_level' ) );
        echo '<div class="jsc-admin-grid">'; foreach ( array( 'detection'=>__( 'Warning signs', 'job-scam-checker' ), 'channel'=>__( 'Recruitment channels', 'job-scam-checker' ), 'money_request'=>__( 'Money requests', 'job-scam-checker' ), 'payment_purpose'=>__( 'Payment purposes', 'job-scam-checker' ) ) as $metric => $title ) { $this->metric_table( $title, $stats->totals( $metric ), $metric ); } echo '</div>';
        $daily = $stats->daily_totals(); echo '<section class="jsc-panel"><h2>' . esc_html__( 'Daily checks (latest 30 days with data)', 'job-scam-checker' ) . '</h2>'; if ( ! $daily ) { echo '<p>' . esc_html__( 'No daily totals yet.', 'job-scam-checker' ) . '</p>'; } else { echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'UTC date', 'job-scam-checker' ) . '</th><th>' . esc_html__( 'Checks', 'job-scam-checker' ) . '</th></tr></thead><tbody>'; foreach ( $daily as $row ) { echo '<tr><td>' . esc_html( $row['stat_date'] ) . '</td><td>' . esc_html( number_format_i18n( $row['total'] ) ) . '</td></tr>'; } echo '</tbody></table>'; } echo '</section>';
        echo '<section class="jsc-panel jsc-danger"><h2>' . esc_html__( 'Reset aggregate statistics', 'job-scam-checker' ) . '</h2><p>' . esc_html__( 'This permanently deletes only anonymous aggregate counter rows. It does not affect rules, pages, posts, users, settings, or other WordPress content.', 'job-scam-checker' ) . '</p><form action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" method="post" onsubmit="return confirm(\'' . esc_js( __( 'Permanently reset all anonymous aggregate statistics?', 'job-scam-checker' ) ) . '\');"><input type="hidden" name="action" value="jsc_reset_statistics"><label><input type="checkbox" name="confirm_reset" value="yes" required> ' . esc_html__( 'I understand that aggregate statistics will be permanently deleted.', 'job-scam-checker' ) . '</label>'; wp_nonce_field( 'jsc_reset_statistics' ); submit_button( __( 'Reset aggregate statistics', 'job-scam-checker' ), 'delete' ); echo '</form></section></div>';
    }

    public function reset_statistics() { $this->authorize( 'jsc_reset_statistics' ); if ( 'yes' !== ( $_POST['confirm_reset'] ?? '' ) ) { wp_die( esc_html__( 'Explicit confirmation is required.', 'job-scam-checker' ), '', array( 'response' => 400 ) ); } if ( ! $this->stats_repository()->reset() ) { wp_die( esc_html__( 'Statistics could not be reset.', 'job-scam-checker' ), '', array( 'response' => 500 ) ); } wp_safe_redirect( admin_url( 'admin.php?page=jsc-statistics&jsc_notice=' . rawurlencode( __( 'Anonymous aggregate statistics reset.', 'job-scam-checker' ) ) ) ); exit; }

    public function content() {
        $this->page_start( __( 'Content management', 'job-scam-checker' ) ); echo '<p>' . esc_html__( 'Use normal WordPress page editing. These shortcuts group the installed Job Scam Checker content without replacing the editor.', 'job-scam-checker' ) . '</p><div class="jsc-admin-grid">';
        $groups = array( 'scam_article'=>__( 'Scam types', 'job-scam-checker' ), 'guide'=>__( 'Guides', 'job-scam-checker' ), 'tool'=>__( 'Checker pages', 'job-scam-checker' ), 'trust'=>__( 'Trust pages', 'job-scam-checker' ), 'legal'=>__( 'Legal pages', 'job-scam-checker' ) );
        $installed = get_option( 'jsc_installed_pages', array() ); foreach ( $groups as $type => $label ) { echo '<section class="jsc-panel"><h2>' . esc_html( $label ) . '</h2><ul>'; $found = false; foreach ( (array) $installed as $id ) { if ( $type === get_post_meta( (int) $id, '_jsc_content_type', true ) ) { $found = true; echo '<li><a href="' . esc_url( get_edit_post_link( (int) $id ) ) . '">' . esc_html( get_the_title( (int) $id ) ) . '</a></li>'; } } if ( ! $found ) { echo '<li>' . esc_html__( 'No installed pages in this group.', 'job-scam-checker' ) . '</li>'; } echo '</ul><p><a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '">' . esc_html__( 'View all pages', 'job-scam-checker' ) . '</a></p></section>'; } echo '</div></div>';
    }
}
