<?php
/**
 * Homepage template.
 *
 * @package JobScamCheckerTheme
 */

get_header();
?>
<main id="main-content">
    <section class="hero">
        <div class="site-container hero__inner">
            <div class="hero__copy">
                <p class="section-kicker"><?php esc_html_e( 'Job offer safety check', 'job-scam-checker-theme' ); ?></p>
                <h1><?php esc_html_e( 'Is That Job Offer Legit?', 'job-scam-checker-theme' ); ?></h1>
                <p class="hero__lead"><?php esc_html_e( 'Paste a suspicious job offer, recruiter message, email, WhatsApp, Telegram or SMS message and check it for common scam warning signs.', 'job-scam-checker-theme' ); ?></p>
                <ul class="trust-list" aria-label="<?php esc_attr_e( 'Service benefits', 'job-scam-checker-theme' ); ?>">
                    <li><span aria-hidden="true">&#10003;</span><?php esc_html_e( 'Free', 'job-scam-checker-theme' ); ?></li>
                    <li><span aria-hidden="true">&#10003;</span><?php esc_html_e( 'No signup', 'job-scam-checker-theme' ); ?></li>
                    <li><span aria-hidden="true">&#10003;</span><?php esc_html_e( 'Instant result', 'job-scam-checker-theme' ); ?></li>
                    <li><span aria-hidden="true">&#10003;</span><?php esc_html_e( 'Privacy-focused', 'job-scam-checker-theme' ); ?></li>
                </ul>
            </div>
            <div class="hero__checker">
                <?php if ( shortcode_exists( 'job_scam_checker' ) ) : ?>
                    <?php echo do_shortcode( '[job_scam_checker]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted shortcode output. ?>
                <?php else : ?>
                    <div class="plugin-notice" role="status">
                        <h2><?php esc_html_e( 'Checker setup required', 'job-scam-checker-theme' ); ?></h2>
                        <p><?php esc_html_e( 'Activate the Job Scam Checker plugin to display the free checking tool.', 'job-scam-checker-theme' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="how-it-helps" aria-labelledby="how-it-helps-title">
        <div class="site-container narrow-container">
            <p class="section-kicker"><?php esc_html_e( 'A careful second look', 'job-scam-checker-theme' ); ?></p>
            <h2 id="how-it-helps-title"><?php esc_html_e( 'Pause before you pay, click or share', 'job-scam-checker-theme' ); ?></h2>
            <p><?php esc_html_e( 'Job scams often create urgency, promise unusually easy income, move conversations away from trusted platforms, or request money and sensitive information. This service is being built to explain those warning signs in plain language.', 'job-scam-checker-theme' ); ?></p>
            <div class="safety-cards">
                <article>
                    <span class="safety-card__number" aria-hidden="true">01</span>
                    <h3><?php esc_html_e( 'Paste the message', 'job-scam-checker-theme' ); ?></h3>
                    <p><?php esc_html_e( 'Remove sensitive information first, then add the suspicious communication.', 'job-scam-checker-theme' ); ?></p>
                </article>
                <article>
                    <span class="safety-card__number" aria-hidden="true">02</span>
                    <h3><?php esc_html_e( 'Review warning signs', 'job-scam-checker-theme' ); ?></h3>
                    <p><?php esc_html_e( 'The completed checker will identify patterns and explain why each one matters.', 'job-scam-checker-theme' ); ?></p>
                </article>
                <article>
                    <span class="safety-card__number" aria-hidden="true">03</span>
                    <h3><?php esc_html_e( 'Verify independently', 'job-scam-checker-theme' ); ?></h3>
                    <p><?php esc_html_e( 'Use the employer’s official website and verified contact details before proceeding.', 'job-scam-checker-theme' ); ?></p>
                </article>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
