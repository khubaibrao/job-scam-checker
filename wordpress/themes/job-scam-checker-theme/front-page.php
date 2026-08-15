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
            <p><?php esc_html_e( 'Job scams often create urgency, promise unusually easy income, move conversations away from trusted platforms, or request money and sensitive information. The checker explains detected warning signs in plain language and points to independent verification steps.', 'job-scam-checker-theme' ); ?></p>
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

    <?php if ( shortcode_exists( 'jsc_trends' ) ) : ?>
        <section class="homepage-trends"><div class="site-container narrow-container"><?php echo do_shortcode( '[jsc_trends]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted shortcode output. ?></div></section>
    <?php endif; ?>

    <section class="explore-content" aria-labelledby="explore-content-title">
        <div class="site-container narrow-container">
            <p class="section-kicker"><?php esc_html_e( 'Learn the patterns', 'job-scam-checker-theme' ); ?></p>
            <h2 id="explore-content-title"><?php esc_html_e( 'Verify the offer beyond the message', 'job-scam-checker-theme' ); ?></h2>
            <p><?php esc_html_e( 'Use practical guides and scam-pattern explanations to check the recruiter, employer, interview and any request for money or personal information.', 'job-scam-checker-theme' ); ?></p>
            <div class="homepage-content-grid">
                <article><h3><a href="<?php echo esc_url( home_url( '/job-scams/' ) ); ?>"><?php esc_html_e( 'Explore job scam types', 'job-scam-checker-theme' ); ?></a></h3><p><?php esc_html_e( 'Compare the approach with remote-work, task, fake-check, impersonation and fee scam patterns.', 'job-scam-checker-theme' ); ?></p></article>
                <article><h3><a href="<?php echo esc_url( home_url( '/guides/how-to-check-a-job-offer/' ) ); ?>"><?php esc_html_e( 'Check a job offer', 'job-scam-checker-theme' ); ?></a></h3><p><?php esc_html_e( 'Confirm the role, recruiter, interview, compensation and onboarding through independent sources.', 'job-scam-checker-theme' ); ?></p></article>
                <article><h3><a href="<?php echo esc_url( home_url( '/guides/what-to-do-after-a-job-scam/' ) ); ?>"><?php esc_html_e( 'Respond to a suspected scam', 'job-scam-checker-theme' ); ?></a></h3><p><?php esc_html_e( 'Prioritize payments, accounts, identity records, device access, evidence and reporting.', 'job-scam-checker-theme' ); ?></p></article>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
