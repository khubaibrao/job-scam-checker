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
<section class="jsc-checker" aria-labelledby="jsc-checker-title">
    <div class="jsc-checker__heading">
        <p class="jsc-eyebrow"><?php esc_html_e( 'Free job offer safety check', 'job-scam-checker' ); ?></p>
        <h2 id="jsc-checker-title"><?php esc_html_e( 'Check a suspicious message', 'job-scam-checker' ); ?></h2>
        <p><?php esc_html_e( 'Paste the message exactly as you received it. The checker will look for common job scam warning signs.', 'job-scam-checker' ); ?></p>
    </div>

    <form class="jsc-checker__form" data-jsc-checker-form method="post" action="">
        <label for="jsc-message"><?php esc_html_e( 'Suspicious job or recruiter message', 'job-scam-checker' ); ?></label>
        <textarea id="jsc-message" name="jsc_message" rows="9" maxlength="12000" required placeholder="<?php echo esc_attr__( 'Paste the suspicious message here...', 'job-scam-checker' ); ?>" aria-describedby="jsc-privacy-warning jsc-character-count"></textarea>

        <div class="jsc-checker__meta">
            <p id="jsc-privacy-warning" class="jsc-privacy-warning">
                <span aria-hidden="true">&#128274;</span>
                <?php esc_html_e( 'Avoid pasting passwords, banking details, government ID numbers, OTP codes, or other sensitive personal information.', 'job-scam-checker' ); ?>
            </p>
            <p id="jsc-character-count" class="jsc-character-count" aria-live="polite">
                <span data-jsc-character-count>0</span>/12,000
            </p>
        </div>

        <button type="button" class="jsc-button" data-jsc-check-button>
            <?php esc_html_e( 'CHECK NOW', 'job-scam-checker' ); ?>
        </button>

        <p class="jsc-checker-status" data-jsc-status role="status" aria-live="polite"></p>
        <noscript><p class="jsc-phase-notice"><?php esc_html_e( 'JavaScript is required to run the checker. Your message has not been sent or stored.', 'job-scam-checker' ); ?></p></noscript>
    </form>

    <section class="jsc-basic-result" data-jsc-result hidden tabindex="-1" aria-labelledby="jsc-result-title">
        <div class="jsc-basic-result__summary">
            <div class="jsc-score" aria-label="<?php esc_attr_e( 'Risk score out of 100', 'job-scam-checker' ); ?>">
                <strong data-jsc-score>0</strong><span>/100</span>
            </div>
            <div>
                <p class="jsc-basic-result__level" data-jsc-level></p>
                <h3 id="jsc-result-title" data-jsc-message></h3>
                <p data-jsc-count></p>
            </div>
        </div>
        <div data-jsc-detections></div>
        <div data-jsc-domains></div>
        <p class="jsc-result-disclaimer" data-jsc-disclaimer></p>
    </section>
</section>
