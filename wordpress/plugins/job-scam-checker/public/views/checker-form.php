<?php
/**
 * Checker form shell.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<section class="jsc-checker" aria-labelledby="<?php echo esc_attr( $instance_id ); ?>-title">
    <div class="jsc-checker__heading">
        <p class="jsc-eyebrow"><?php esc_html_e( 'Free job offer safety check', 'job-scam-checker' ); ?></p>
        <h2 id="<?php echo esc_attr( $instance_id ); ?>-title"><?php esc_html_e( 'Check a suspicious message', 'job-scam-checker' ); ?></h2>
        <p><?php esc_html_e( 'Paste the message exactly as you received it. The checker will look for common job scam warning signs.', 'job-scam-checker' ); ?></p>
    </div>

    <form class="jsc-checker__form" data-jsc-checker-form method="post" action="">
        <label for="<?php echo esc_attr( $instance_id ); ?>-message"><?php esc_html_e( 'Suspicious job or recruiter message', 'job-scam-checker' ); ?></label>
        <textarea id="<?php echo esc_attr( $instance_id ); ?>-message" name="jsc_message" rows="9" minlength="10" maxlength="12000" required placeholder="<?php echo esc_attr__( 'Paste the suspicious message here...', 'job-scam-checker' ); ?>" aria-describedby="<?php echo esc_attr( $instance_id ); ?>-privacy <?php echo esc_attr( $instance_id ); ?>-count"></textarea>

        <div class="jsc-checker__meta">
            <p id="<?php echo esc_attr( $instance_id ); ?>-privacy" class="jsc-privacy-warning">
                <span aria-hidden="true">&#128274;</span>
                <?php esc_html_e( 'Avoid pasting passwords, banking details, government ID numbers, OTP codes, or other sensitive personal information.', 'job-scam-checker' ); ?>
            </p>
            <p id="<?php echo esc_attr( $instance_id ); ?>-count" class="jsc-character-count">
                <span data-jsc-character-count>0</span>/12,000
            </p>
        </div>

        <button type="button" class="jsc-button" data-jsc-check-button>
            <?php esc_html_e( 'CHECK NOW', 'job-scam-checker' ); ?>
        </button>

        <p class="jsc-checker-status" data-jsc-status role="status" aria-live="polite" aria-atomic="true"></p>
        <noscript>
            <div class="jsc-no-script" role="note">
                <h3><?php esc_html_e( 'JavaScript is needed to run this private check', 'job-scam-checker' ); ?></h3>
                <p><?php esc_html_e( 'Your message has not been sent or stored. Enable JavaScript and reload this page, or verify the employer through contact details found independently on its official website.', 'job-scam-checker' ); ?></p>
            </div>
        </noscript>
    </form>

    <section class="jsc-result" data-jsc-result hidden tabindex="-1" aria-labelledby="<?php echo esc_attr( $instance_id ); ?>-result-title" aria-describedby="<?php echo esc_attr( $instance_id ); ?>-result-summary">
        <header class="jsc-result__header">
            <div class="jsc-score" data-jsc-score-visual>
                <span class="jsc-score__label"><?php esc_html_e( 'Risk score', 'job-scam-checker' ); ?></span>
                <span><strong data-jsc-score>0</strong><span aria-hidden="true">/100</span></span>
                <progress data-jsc-progress max="100" value="0" aria-label="<?php esc_attr_e( 'Risk score', 'job-scam-checker' ); ?>"><?php esc_html_e( '0 out of 100', 'job-scam-checker' ); ?></progress>
            </div>
            <div class="jsc-result__headline">
                <p class="jsc-risk-badge" data-jsc-level></p>
                <h3 id="<?php echo esc_attr( $instance_id ); ?>-result-title" data-jsc-message></h3>
                <p id="<?php echo esc_attr( $instance_id ); ?>-result-summary" data-jsc-count></p>
            </div>
        </header>

        <div class="jsc-risk-scale" aria-hidden="true">
            <span><?php esc_html_e( 'Low', 'job-scam-checker' ); ?></span>
            <span><?php esc_html_e( 'Caution', 'job-scam-checker' ); ?></span>
            <span><?php esc_html_e( 'High', 'job-scam-checker' ); ?></span>
            <span><?php esc_html_e( 'Very high', 'job-scam-checker' ); ?></span>
        </div>

        <div class="jsc-result__section" data-jsc-detections></div>
        <div class="jsc-result__section jsc-domain-section" data-jsc-domains hidden></div>
        <div class="jsc-result__section jsc-actions" data-jsc-actions></div>

        <aside class="jsc-result__notice">
            <strong><?php esc_html_e( 'Important', 'job-scam-checker' ); ?></strong>
            <p class="jsc-result-disclaimer" data-jsc-disclaimer></p>
        </aside>

        <footer class="jsc-result__footer">
            <button type="button" class="jsc-secondary-button" data-jsc-reset><?php esc_html_e( 'Check another message', 'job-scam-checker' ); ?></button>
            <button type="button" class="jsc-print-button" data-jsc-print><?php esc_html_e( 'Print or save result', 'job-scam-checker' ); ?></button>
        </footer>
    </section>

    <section class="jsc-error" data-jsc-error hidden role="alert" tabindex="-1" aria-labelledby="<?php echo esc_attr( $instance_id ); ?>-error-title">
        <h3 id="<?php echo esc_attr( $instance_id ); ?>-error-title"><?php esc_html_e( 'We could not complete the check', 'job-scam-checker' ); ?></h3>
        <p data-jsc-error-message></p>
        <button type="button" class="jsc-secondary-button" data-jsc-retry><?php esc_html_e( 'Try again', 'job-scam-checker' ); ?></button>
    </section>

    <aside class="jsc-ad-slot" data-jsc-ad-slot hidden aria-label="<?php esc_attr_e( 'Advertisement', 'job-scam-checker' ); ?>"></aside>
</section>
