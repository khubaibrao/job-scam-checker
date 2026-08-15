<?php
/**
 * Curated Phase 4 page library.
 *
 * All examples are fictional composites written for education. No page makes an
 * accusation about an identifiable person or organization.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$page = static function ( $title, $slug, $seo_title, $description, $type, $content, $related = array(), $parent = '' ) {
    return array(
        'title'        => $title,
        'slug'         => $slug,
        'seo_title'    => $seo_title,
        'description'  => $description,
        'content_type' => $type,
        'content'      => $content,
        'related'      => $related,
        'parent'       => $parent,
    );
};

$list = static function ( array $items ) {
    return '<ul><li>' . implode( '</li><li>', $items ) . '</li></ul>';
};

$faq = static function ( array $items ) {
    $html = '<section class="jsc-faq"><h2>Frequently asked questions</h2>';
    foreach ( $items as $question => $answer ) {
        $html .= '<h3>' . $question . '</h3><p>' . $answer . '</p>';
    }
    return $html . '</section>';
};

$scam_article = static function ( array $data ) use ( $list, $faq ) {
    return '<p class="jsc-lead">' . $data['intro'] . '</p>'
        . '<div class="jsc-safety-callout"><strong>Quick guidance:</strong> ' . $data['quick'] . '</div>'
        . '<h2>How this scam works</h2>' . $data['how']
        . '<h2>Warning signs to look for</h2>' . $list( $data['signs'] )
        . '<h2>A realistic example</h2><div class="jsc-example"><p><strong>Fictional example:</strong> “' . $data['example'] . '”</p><p>' . $data['example_note'] . '</p></div>'
        . '[jsc_ad_slot position="mid_article"]'
        . '<h2>What the sender may ask you to do</h2>' . $data['asks']
        . '<h2>How legitimate employers normally behave</h2>' . $data['legitimate']
        . '<h2>How to verify the opportunity</h2>' . $list( $data['verify'] )
        . '<h2>What to do if you have already been contacted</h2>' . $data['contacted']
        . '<div class="jsc-checker-cta"><h2>Check the message for warning signs</h2><p>Paste the communication into our free checker. It provides a risk assessment, not a guarantee, and does not permanently store the message.</p><a class="button-link" href="/job-scam-checker/">Open the Job Scam Checker</a></div>'
        . $faq( $data['faq'] )
        . '[jsc_ad_slot position="lower_article"]';
};

$guide = static function ( array $data ) use ( $list, $faq ) {
    $steps = '';
    foreach ( $data['steps'] as $heading => $body ) {
        $steps .= '<h3>' . $heading . '</h3><p>' . $body . '</p>';
    }
    return '<p class="jsc-lead">' . $data['intro'] . '</p>'
        . '<div class="jsc-safety-callout"><strong>Use this guide before you act:</strong> ' . $data['summary'] . '</div>'
        . '<h2>A practical verification process</h2>' . $steps
        . '[jsc_ad_slot position="mid_article"]'
        . '<h2>Checklist</h2>' . $list( $data['checklist'] )
        . '<h2>Example situation</h2><div class="jsc-example"><p><strong>Fictional example:</strong> ' . $data['example'] . '</p><p>' . $data['lesson'] . '</p></div>'
        . '<h2>Common mistakes to avoid</h2>' . $list( $data['mistakes'] )
        . '<h2>When to stop communicating</h2><p>' . $data['stop'] . '</p>'
        . '<div class="jsc-checker-cta"><h2>Assess the message before proceeding</h2><p>The checker can highlight common patterns while you complete the independent checks in this guide.</p><a class="button-link" href="/job-scam-checker/">Check a suspicious message</a></div>'
        . $faq( $data['faq'] )
        . '[jsc_ad_slot position="lower_article"]';
};

$pages = array();

// Checker and tool pages.
$pages['job_scam_checker'] = $page(
    'Job Scam Checker', 'job-scam-checker', 'Free Job Scam Checker: Review a Suspicious Offer',
    'Paste a suspicious job offer or recruiter message to check for common scam warning signs. Free, private and no account required.', 'tool',
    '[job_scam_checker]<section><h2>What this checker looks for</h2><p>The checker reviews wording, links and combinations commonly associated with recruitment fees, task deposits, fake checks, credential theft, unrealistic earnings, messaging-only hiring and other employment scams. It analyzes text on this website using local rules; it does not contact the sender or verify a company database.</p><h2>How to use the result</h2><p>Treat the score as a prompt for further verification. A low score cannot prove that an offer is real, and a high score is not a legal determination. Find the employer’s website independently, confirm the opening on its careers page and contact the organization through published details.</p><h2>Before you paste</h2><p>Remove passwords, security codes, bank details, government identification numbers and other sensitive information. The submitted text is processed for the immediate result and is not permanently stored by the checker.</p></section>',
    array( 'recruiter_checker', 'email_scam_checker', 'whatsapp_job_checker', 'guide_check_offer' )
);
$pages['job_scam_checker']['upgrade_from'] = array( '<!-- wp:shortcode -->[job_scam_checker]<!-- /wp:shortcode -->' );

$pages['recruiter_checker'] = $page(
    'Recruiter Checker', 'recruiter-checker', 'Recruiter Checker: Review a Suspicious Recruiter Message',
    'Check a recruiter message for common impersonation, fee, urgency and credential-request warning signs, then verify the recruiter independently.', 'tool',
    '<p class="jsc-lead">A real name, company logo or polished profile does not establish that a recruiter represents the employer. Review the message below, then confirm the person through sources the sender does not control.</p>[job_scam_checker]<h2>What to examine in a recruiter approach</h2><p>Look for a role that matches the company’s careers page, an email domain controlled by the employer or established agency, a coherent explanation of how the recruiter found you, and a normal interview process. Be cautious when the sender moves immediately to WhatsApp or Telegram, refuses a call, guarantees hiring, or asks for money, codes or identity documents.</p><h2>Verification beyond the checker</h2><ol><li>Navigate to the company website manually.</li><li>Find its switchboard, careers team or published recruiting contact.</li><li>Ask whether the person, role and requisition number are genuine.</li><li>Check whether the sender’s email domain exactly matches the official domain.</li></ol><p>See <a href="/guides/how-to-verify-a-recruiter/">how to verify a recruiter</a> for a complete process.</p>',
    array( 'guide_verify_recruiter', 'fake_recruiter_scams', 'company_impersonation_scams' )
);

$pages['email_scam_checker'] = $page(
    'Email Scam Checker', 'email-scam-checker', 'Job Offer Email Scam Checker: Review Suspicious Email Text',
    'Review the text and links in a suspicious job email for common employment scam indicators without opening its links or attachments.', 'tool',
    '<p class="jsc-lead">Job scam emails may copy real branding, names and job descriptions. Paste the text—not attachments, passwords or personal records—to review it for common warning signs.</p>[job_scam_checker]<h2>Check the email safely</h2><p>Do not click a link merely to investigate it. Compare the visible sender address and reply-to address, inspect the spelling after the @ symbol, and locate the employer’s site through a separate search or a known address. A free mailbox is not automatic proof of fraud, but a corporate recruiter using one deserves careful confirmation.</p><h2>Attachments and forms</h2><p>Do not enable macros, install interview software from an attachment, or enter a password after following an email link. Legitimate employers may collect applications and onboarding details, but they normally do so through a recognizable careers or HR system after a documented process.</p><p>For a step-by-step response, read <a href="/guides/how-to-handle-a-suspicious-job-message/">how to handle a suspicious job message</a>.</p>',
    array( 'guide_suspicious_message', 'company_impersonation_scams', 'fake_check_scams' )
);

$pages['whatsapp_job_checker'] = $page(
    'WhatsApp Job Scam Checker', 'whatsapp-job-scam-checker', 'WhatsApp Job Scam Checker: Review a Recruitment Message',
    'Paste a WhatsApp recruitment message to check for task deposits, fees, unrealistic income and other common job scam warning signs.', 'tool',
    '<p class="jsc-lead">Some legitimate recruiters use WhatsApp, especially where it is a normal business channel. The risk rises when the entire process stays in chat, the sender cannot be verified, or money and sensitive data enter the conversation.</p>[job_scam_checker]<h2>Common WhatsApp patterns</h2><p>Watch for unsolicited “online work,” daily commission promises, product-rating tasks, requests to recharge an account, group-chat testimonials and pressure to continue after a small introductory payment. A profile photo, business account label or international number does not prove employer authorization.</p><h2>Verify without relying on the chat</h2><p>Ask for the public job posting and recruiter’s corporate email, then contact the employer using its independently located website. Do not use the registration link or telephone number supplied in the message as your only verification source.</p><p>Learn more about <a href="/job-scams/whatsapp-telegram-job-scams/">WhatsApp and Telegram job scams</a>.</p>',
    array( 'messaging_job_scams', 'task_scams', 'guide_suspicious_message' )
);

$pages['telegram_job_checker'] = $page(
    'Telegram Job Scam Checker', 'telegram-job-scam-checker', 'Telegram Job Scam Checker: Review a Suspicious Offer',
    'Review a Telegram job message for common task scam, cryptocurrency, impersonation and pressure tactics before you respond.', 'tool',
    '<p class="jsc-lead">Telegram usernames, channels and copied company graphics are easy to create. Use the checker for the message text, then verify the opportunity outside Telegram.</p>[job_scam_checker]<h2>Why Telegram job approaches need verification</h2><p>Frequent patterns include a “receptionist” who hands the conversation to a task manager, screenshots of supposed earnings, cryptocurrency deposits, escalating combination orders and claims that a final payment will unlock withdrawals. The displayed account balance may be controlled entirely by the operator.</p><h2>Safer next steps</h2><p>Do not join a work group, install an unknown app or deposit funds to test the offer. Search for the role on the official employer website and contact its recruiting team through published details. Report impersonating accounts through Telegram’s own reporting controls.</p>',
    array( 'messaging_job_scams', 'task_scams', 'advance_fee_scams' )
);

// Hubs.
$pages['scam_categories'] = $page(
    'Job Scam Categories', 'job-scams', 'Job Scam Types: Patterns, Warning Signs and Safety Steps',
    'Explore major job scam patterns, understand how they work and learn how to verify suspicious employment opportunities safely.', 'hub',
    '<p class="jsc-lead">Job scams do not all follow one script. Some steal an upfront fee, some obtain identity or account access, and others use victims to move money or goods. Start with the behavior you observed rather than relying on the job title alone.</p><div class="jsc-content-grid">'
    . '<article><h2><a href="/job-scams/remote-job-scams/">Remote job scams</a></h2><p>Learn how fake flexibility, equipment purchases and text-only interviews are used.</p></article>'
    . '<article><h2><a href="/job-scams/work-from-home-scams/">Work-from-home scams</a></h2><p>Examine guaranteed earnings, starter charges and vague home-based duties.</p></article>'
    . '<article><h2><a href="/job-scams/whatsapp-telegram-job-scams/">WhatsApp and Telegram scams</a></h2><p>Recognize changing contacts, task groups, deposits and controlled balances.</p></article>'
    . '<article><h2><a href="/job-scams/task-job-optimization-scams/">Task and optimization schemes</a></h2><p>Understand deposits, recharges, commissions and controlled account balances.</p></article>'
    . '<article><h2><a href="/job-scams/fake-recruiter-scams/">Fake recruiter scams</a></h2><p>Check names, profiles, domains, roles and authorization using independent sources.</p></article>'
    . '<article><h2><a href="/job-scams/company-impersonation-job-scams/">Company impersonation scams</a></h2><p>Compare copied branding and lookalike portals with the real employer’s presence.</p></article>'
    . '<article><h2><a href="/job-scams/advance-fee-job-scams/">Fees and irreversible payments</a></h2><p>Recognize training, registration, equipment, crypto and gift-card demands.</p></article>'
    . '<article><h2><a href="/job-scams/fake-check-equipment-scams/">Fake checks and equipment</a></h2><p>Avoid forwarding money before a deposited check is discovered to be invalid.</p></article>'
    . '<article><h2><a href="/job-scams/reshipping-package-forwarding-scams/">Reshipping and package forwarding</a></h2><p>See why forwarding goods through a personal address can be dangerous.</p></article>'
    . '<article><h2><a href="/job-scams/data-entry-job-scams/">Data entry job scams</a></h2><p>Review vague duties, unusual pay, chat interviews and software fees.</p></article></div>'
    . '<h2>Use behavior, not branding, as evidence</h2><p>A message can mention a real employer and still be fraudulent. Logos, offer letters and employee names may be copied. Verification means reaching the organization through a path the sender did not provide.</p><div class="jsc-checker-cta"><h2>Have a message to check?</h2><p>Use the free checker to identify common warning signs, then follow the verification steps in the relevant category.</p><a class="button-link" href="/job-scam-checker/">Check the message</a></div>',
    array( 'guides', 'job_scam_checker', 'guide_check_offer' )
);

$pages['guides'] = $page(
    'Job Scam Safety Guides', 'guides', 'Job Offer Verification and Job Scam Safety Guides',
    'Practical guides for checking recruiters, companies, interviews and suspicious job messages while protecting personal information.', 'hub',
    '<p class="jsc-lead">Verification works best as a repeatable process. These guides help you separate information controlled by the sender from evidence found through independent official sources.</p><div class="jsc-content-grid">'
    . '<article><h2><a href="/guides/how-to-check-a-job-offer/">Check a job offer</a></h2><p>Compare the role, process, pay and documents before accepting.</p></article>'
    . '<article><h2><a href="/guides/how-to-verify-a-recruiter/">Verify a recruiter</a></h2><p>Confirm identity and authorization without relying on a profile or signature.</p></article>'
    . '<article><h2><a href="/guides/how-to-handle-a-suspicious-job-message/">Handle a suspicious message</a></h2><p>Preserve evidence, avoid unsafe links and verify the claim through another route.</p></article>'
    . '<article><h2><a href="/guides/how-to-check-if-a-company-is-real/">Check a company</a></h2><p>Distinguish a real business from a person impersonating one.</p></article>'
    . '<article><h2><a href="/guides/job-interview-red-flags/">Review interview red flags</a></h2><p>Assess text interviews, instant offers and requests unrelated to qualifications.</p></article>'
    . '<article><h2><a href="/guides/protect-personal-information-during-a-job-search/">Protect personal information</a></h2><p>Know what information belongs at each stage of legitimate hiring.</p></article>'
    . '<article><h2><a href="/guides/what-to-do-after-a-job-scam/">Respond after a suspected scam</a></h2><p>Prioritize accounts, payments, evidence and reporting based on what was shared.</p></article></div>',
    array( 'scam_categories', 'job_scam_checker', 'guide_suspicious_message' )
);

// Scam articles.
$scam_data = array(
    'remote_job_scams' => array(
        'title' => 'Remote Job Scams', 'slug' => 'remote-job-scams', 'seo' => 'Remote Job Scams: Warning Signs and Verification Steps', 'desc' => 'Learn how fake remote job offers work, including text-only hiring, equipment payments, fake checks and identity requests.',
        'intro' => 'Remote hiring is normal, but distance gives an impersonator room to avoid an office, video call or verifiable company contact. The important question is not whether the job is remote; it is whether the employer and process can be independently confirmed.',
        'quick' => 'Do not buy equipment from a recruiter’s vendor, forward check proceeds or share sensitive documents before verifying the employer.',
        'how' => '<p>The sender advertises flexibility and may copy a genuine employer’s role. After a short text exchange, the applicant receives an offer and instructions to buy equipment, deposit a check or complete onboarding through an unfamiliar portal. The scammer benefits from the payment, check transfer, credentials or identity data—not from any work.</p>',
        'signs' => array( 'An offer arrives before a video or telephone interview with identifiable staff.', 'The recruiter will communicate only by text, email, WhatsApp or Telegram.', 'A check is sent for equipment and part of the money must be forwarded to a vendor.', 'The sender asks for banking or identity details through an ordinary form or email.', 'The listed role is missing from the company’s official careers page.' ),
        'example' => 'Your remote administrative interview is complete. Deposit our emailed check, purchase the laptop from our approved supplier today and keep the balance as your signing bonus.',
        'example_note' => 'The fast offer and check-funded vendor purchase are the important signals. Banks may initially show funds before later reversing a fraudulent check.',
        'asks' => '<p>The applicant may be told to purchase office equipment, install remote-access software, receive payments, open a new bank account or send identity records immediately. Each request should be evaluated separately from the appealing remote-work promise.</p>',
        'legitimate' => '<p>Real remote employers still interview candidates, identify the legal employer, explain supervision and payroll, use established HR systems and provide equipment directly or reimburse through documented policy after hiring. They do not need a candidate to forward money from a check.</p>',
        'verify' => array( 'Find the company website without using the message link.', 'Locate the exact job and compare its location, reference number and qualifications.', 'Call a published company number and confirm the recruiter and equipment policy.', 'Search the sender’s domain carefully for added letters or substituted characters.' ),
        'contacted' => '<p>Pause before completing onboarding. Preserve the message and offer documents, but do not open further attachments. If you deposited a check or installed software, contact your bank or device support promptly and explain exactly what occurred.</p>',
        'faq' => array( 'Are remote job offers usually scams?' => 'No. Remote work is common. Risk comes from unverifiable identity, abnormal hiring and requests for money, access or sensitive information.', 'Should a remote employer send a check for equipment?' => 'A check combined with instructions to pay a particular vendor or return excess funds is a serious warning sign. Verify through official company contacts before using any funds.' ),
        'related' => array( 'work_from_home_scams', 'fake_check_scams', 'guide_check_offer' ),
    ),
    'work_from_home_scams' => array(
        'title' => 'Work-from-Home Scams', 'slug' => 'work-from-home-scams', 'seo' => 'Work-from-Home Scams: Easy-Income Claims and Red Flags', 'desc' => 'Recognize work-from-home scams involving guaranteed earnings, starter fees, vague duties and personal account use.',
        'intro' => '“Work from home” describes a location, not a business model. Suspicious offers focus on effortless income and urgency while remaining vague about the employer, customers, supervision and actual deliverables.',
        'quick' => 'Ask what work creates value, who pays the employer, how performance is measured and why any candidate payment is necessary.',
        'how' => '<p>An advertisement promises high daily earnings for minimal time. The applicant is directed to a chat, webinar or registration page and then asked to pay for access, leads, training, a starter kit or higher-paying tasks. Some schemes are disguised product sales or account-recharge systems rather than employment.</p>',
        'signs' => array( 'Guaranteed income without clear hours, rate or performance conditions.', 'The main description is lifestyle freedom rather than concrete responsibilities.', 'Payment is required for a starter kit, certification, directory or access level.', 'The company cannot explain its product, customers or legal employer.', 'Recruitment relies on group testimonials and earnings screenshots instead of a contract.' ),
        'example' => 'Earn $500 every day from your phone in 60 minutes. No experience or interview. Register now and pay the refundable activation charge to reserve your place.',
        'example_note' => 'The combination of minimal effort, guaranteed daily pay, no screening and a refundable fee is more informative than the “home-based” label.',
        'asks' => '<p>Common requests include buying materials, recruiting other participants, using a personal account for payments, completing rating tasks or paying to move to a more profitable tier. A refund promise does not remove the risk of sending money.</p>',
        'legitimate' => '<p>Legitimate home-based roles identify the employer, responsibilities, manager, schedule, employment status and compensation formula. They assess relevant skills and provide written policies. Business expenses are handled through normal company processes.</p>',
        'verify' => array( 'Read the full job description on the official site.', 'Ask for the legal entity that will appear on payroll and the name of the manager.', 'Separate salary from commissions, bonuses and hypothetical maximum earnings.', 'Decline any payment whose purpose cannot be independently justified.' ),
        'contacted' => '<p>Do not pay to “hold” the opening. If you joined a group, avoid posting identity documents or financial screenshots. Leave the group, retain evidence and report the advertisement to the site where it appeared.</p>',
        'faq' => array( 'Is paying for training always a scam?' => 'Not every course is fraudulent, but an employer-controlled mandatory fee during recruitment is a major warning sign. Verify whether the role exists independently.', 'What if the registration fee is refundable?' => 'A refund claim is only a promise from the recipient. It does not make an unverified payment safe.' ),
        'related' => array( 'remote_job_scams', 'data_entry_scams', 'advance_fee_scams' ),
    ),
    'messaging_job_scams' => array(
        'title' => 'WhatsApp and Telegram Job Scams', 'slug' => 'whatsapp-telegram-job-scams', 'seo' => 'WhatsApp and Telegram Job Scams: How to Verify Messages', 'desc' => 'Understand WhatsApp and Telegram recruitment scams, including task groups, fake managers, deposits and impersonation.',
        'intro' => 'Messaging apps are useful communication tools, but an account name, logo or business label is not proof of identity. Scam operations use quick account creation, private groups and international payments to separate the approach from the organization being impersonated.',
        'quick' => 'Verify the role and recruiter outside the app. Do not deposit money, install an app or trust group earnings screenshots.',
        'how' => '<p>A friendly contact offers simple online work and transfers the applicant to a “receptionist,” “mentor” or task group. Early tasks may produce a small payment. Later tasks require deposits, cryptocurrency or a growing account recharge. The displayed balance and group testimonials are controlled by the operator.</p>',
        'signs' => array( 'Unsolicited contact from a personal or international number.', 'A conversation moves to a different account for training or payment.', 'Work consists of ratings, clicks, orders or product optimization.', 'Withdrawals require tax, verification, negative-balance or combination-task payments.', 'The company’s real website does not mention the chat account or role.' ),
        'example' => 'I am the hiring receptionist. Join our Telegram training group, complete three hotel-rating orders, then recharge 200 USDT to unlock the premium commission set.',
        'example_note' => 'The changing contacts, rating work and cryptocurrency recharge form a recognizable task-scam sequence.',
        'asks' => '<p>The sender may ask for screenshots, a crypto wallet, an account on an unfamiliar platform, identity verification or invitations to friends. A small early payment can be used to build confidence before larger demands.</p>',
        'legitimate' => '<p>A legitimate recruiter who uses messaging should still provide a verifiable corporate identity, public job posting and conventional interview route. Employment earnings do not require deposits to release them.</p>',
        'verify' => array( 'Search the company’s own careers page for the role.', 'Contact the company using its published domain or telephone number.', 'Check whether the messaging username is listed by the company itself.', 'Treat screenshots and group comments as claims, not independent evidence.' ),
        'contacted' => '<p>Stop before making another deposit, even if a dashboard says one final task will release everything. Record usernames, wallet addresses and transaction details, report the account in the app and contact the payment provider promptly.</p>',
        'faq' => array( 'Do legitimate recruiters use WhatsApp?' => 'Some do. The channel alone is not decisive; independent identity, normal screening and absence of payment demands matter.', 'Can a task platform balance prove I earned money?' => 'No. The operator can control the displayed figures. A balance that requires new payments to withdraw is especially concerning.' ),
        'related' => array( 'task_scams', 'advance_fee_scams', 'whatsapp_job_checker' ),
    ),
    'fake_recruiter_scams' => array(
        'title' => 'Fake Recruiter Scams', 'slug' => 'fake-recruiter-scams', 'seo' => 'Fake Recruiter Scams: Verify Identity and Job Authority', 'desc' => 'Learn how fake recruiters copy profiles and employer details, and verify whether a recruiter is authorized to offer a real role.',
        'intro' => 'A fake recruiter may use the name of a real employee, agency or employer. Finding that name online confirms only that the person exists—not that the account contacting you belongs to them.',
        'quick' => 'Confirm identity and authorization through the employer or agency using contact details you locate independently.',
        'how' => '<p>The impersonator copies a professional profile, logo and job description, then contacts candidates from a lookalike domain, free mailbox or messaging account. The apparent opportunity creates a reason to request documents, fees, account access or a fake-check transfer.</p>',
        'signs' => array( 'The display name is professional but the underlying address is unrelated.', 'The sender refuses a scheduled voice or video conversation.', 'The offer arrives before questions about experience or availability.', 'A recruiter claims confidentiality prevents contact with the company.', 'The domain differs from the official one by a letter, hyphen or added word.' ),
        'example' => 'I am the senior recruiter listed on the company website. For security, do not call head office. Continue your interview on Telegram and send your passport to reserve onboarding.',
        'example_note' => 'Referencing a real staff member can make the approach convincing, while the instruction not to verify removes the strongest check.',
        'asks' => '<p>Fake recruiters may request a résumé with excessive identity data, copies of documents, an employment screening fee, an OTP or login to a supposed applicant portal. Each request advances identity theft or payment fraud.</p>',
        'legitimate' => '<p>Real recruiters can explain who employs them, identify the client when permitted, describe the role and arrange normal screening. They do not object to reasonable verification through their agency or the employer.</p>',
        'verify' => array( 'Use the official company or agency site to locate a switchboard.', 'Contact the published address and ask whether the recruiter and requisition are valid.', 'Compare the complete email domain, not just the display name.', 'Ask for a calendar invitation and participants whose identities can also be confirmed.' ),
        'contacted' => '<p>Do not confront the sender with extra personal information. Preserve profile URLs and headers, notify the person or organization being impersonated through official channels, and report the account to the platform.</p>',
        'faq' => array( 'Does a LinkedIn profile prove a recruiter is real?' => 'No. Profiles can be copied or compromised. Confirm the account controls a legitimate work address and is authorized for the role.', 'Can an agency recruiter use a different domain?' => 'Yes, but the agency should have its own independently verifiable business presence and be able to confirm the recruiter.' ),
        'related' => array( 'company_impersonation_scams', 'guide_verify_recruiter', 'recruiter_checker' ),
    ),
    'task_scams' => array(
        'title' => 'Task and Job Optimization Scams', 'slug' => 'task-job-optimization-scams', 'seo' => 'Task Scams and Job Optimization Scams Explained', 'desc' => 'Learn how optimization, rating and order task scams use fake commissions, deposits, recharges and blocked withdrawals.',
        'intro' => 'Task scams disguise deposits as work. Clicking, rating or “optimizing” creates a visible commission balance, but the platform’s real purpose is to persuade the participant to add increasingly large amounts of money.',
        'quick' => 'Never pay to complete a work task or withdraw earnings. A displayed balance is not proof that funds exist.',
        'how' => '<p>After an unsolicited offer, the participant receives simple tasks and may get a small payment. A later “combination order” pushes the balance negative. The mentor says a recharge is required and promises the deposit plus commission will return after the set. New taxes or verification payments appear when withdrawal is attempted.</p>',
        'signs' => array( 'The role involves clicking, liking, rating or product optimization with no real client deliverable.', 'Tasks require a deposit, recharge, top-up or cryptocurrency balance.', 'A mentor controls the sequence and discourages stopping mid-set.', 'The account shows negative balances or unusually high commissions.', 'Withdrawal depends on completing another task or paying another charge.' ),
        'example' => 'Your lucky combination order is worth triple commission. Recharge 750 USDT to clear the negative amount; all principal and profit will unlock after task 40.',
        'example_note' => 'The platform turns a supposed job into a sequence where the worker finances the work. The increasing balance creates pressure to chase earlier deposits.',
        'asks' => '<p>Participants may be told to open a cryptocurrency account, borrow money, share transfer screenshots or ask customer service for a temporary extension. None of these steps creates a legitimate employment relationship.</p>',
        'legitimate' => '<p>Employers pay workers for completed services. They do not require workers to stake personal funds against customer orders or pay a negative task balance. Real commission structures are documented and tied to actual sales or services.</p>',
        'verify' => array( 'Ask what customer receives value from each task.', 'Check whether the named brand publishes the role on its official site.', 'Read who legally owns the platform and where disputes can be raised.', 'Try no further deposit; a real employer can explain earnings without one.' ),
        'contacted' => '<p>Stop adding funds. Do not accept the claim that one last payment is needed. Save chats, domain names, wallet addresses and transaction records, then contact the exchange, bank or payment provider and make a report.</p>',
        'faq' => array( 'Why do task scams sometimes pay at first?' => 'A small payment can build confidence and encourage much larger deposits later.', 'Should I pay a withdrawal tax to the platform?' => 'Taxes are not normally paid to an anonymous task mentor to release wages. Verify any tax obligation independently.' ),
        'related' => array( 'messaging_job_scams', 'advance_fee_scams', 'guide_after_scam' ),
    ),
    'advance_fee_scams' => array(
        'title' => 'Advance-Fee Job Scams', 'slug' => 'advance-fee-job-scams', 'seo' => 'Advance-Fee Job Scams: Training, Registration and Crypto Fees', 'desc' => 'Recognize job scams that demand training, registration, equipment, visa, task or cryptocurrency payments before work begins.',
        'intro' => 'Advance-fee job scams make the opportunity conditional on a candidate payment. The label changes—training, registration, equipment, background check, visa or account activation—but the pressure is the same.',
        'quick' => 'Do not pay a recruiter to secure a job. Confirm the role and every expense with the employer through official contacts.',
        'how' => '<p>The sender offers or strongly implies employment, then introduces a charge that is described as refundable, temporary or required by policy. Payment may go to a personal account, gift card, crypto wallet or supposed third-party vendor. After payment, another requirement appears or contact ends.</p>',
        'signs' => array( 'Payment is required before an interview, contract or start date.', 'The fee must be paid urgently to a personal account or irreversible method.', 'A refund is promised without enforceable written terms.', 'The recruiter controls the training, screening or equipment vendor.', 'The employer cannot confirm the charge through published channels.' ),
        'example' => 'Your application is approved. Pay the refundable $185 background and registration fee in Bitcoin within two hours so HR can issue your employee number.',
        'example_note' => 'Approval before screening, urgency and cryptocurrency payment make independent verification essential.',
        'asks' => '<p>Fees may be divided into smaller steps so each seems manageable. The sender may also ask for a screenshot of the transaction, which confirms payment and can reveal account information.</p>',
        'legitimate' => '<p>Employers may require qualifications, checks or documents, but ordinary recruitment costs are generally handled by the employer or through transparent processes. Candidates should be able to verify the provider and policy without relying on the recruiter’s link.</p>',
        'verify' => array( 'Ask the employer’s verified HR contact whether the fee exists.', 'Look for the exact opening on the official careers site.', 'Identify who receives the money and why that entity is authorized.', 'Do not treat “refundable” as evidence that recovery is possible.' ),
        'contacted' => '<p>If you paid, contact the payment provider immediately and ask what reversal or fraud-report options exist. Preserve receipts and conversations. Do not pay a recovery agent who promises guaranteed retrieval for another upfront fee.</p>',
        'faq' => array( 'Can a company charge for a background check?' => 'Practices vary, but a recruiter-directed payment before verification deserves caution. Confirm policy using the employer’s independently found HR contact.', 'Is cryptocurrency ever normal for a job fee?' => 'Cryptocurrency is unusual for candidate expenses and difficult to reverse. Treat the request as a strong warning sign.' ),
        'related' => array( 'task_scams', 'fake_check_scams', 'guide_check_offer' ),
    ),
    'fake_check_scams' => array(
        'title' => 'Fake Check and Equipment Purchase Job Scams', 'slug' => 'fake-check-equipment-scams', 'seo' => 'Fake Check Job Scams and Equipment Purchase Fraud', 'desc' => 'Understand fake job checks, equipment vendor instructions and why available bank funds do not mean a check has cleared.',
        'intro' => 'A fake-check job scam exploits the time between a bank making funds available and discovering that the check is invalid. The victim sends real money while the apparent deposit is later reversed.',
        'quick' => 'Do not spend, transfer or return money from an unexpected employer check until the employer and payment are independently verified.',
        'how' => '<p>A newly hired remote worker receives an electronic or mailed check supposedly for equipment. The sender directs a purchase from a chosen vendor, asks for excess money back or requests gift cards. The bank later removes the check amount, leaving the account holder responsible for outgoing payments.</p>',
        'signs' => array( 'A check arrives before verified employment or completed payroll setup.', 'The check amount exceeds the expected expense.', 'The recruiter chooses a vendor paid by transfer, payment app, crypto or gift card.', 'The sender says visible funds mean the check has cleared.', 'There is pressure to purchase before the bank can investigate.' ),
        'example' => 'Deposit the $4,800 check by mobile banking. Keep $600 as your first pay and send $4,200 to our laptop vendor today; the funds already show available.',
        'example_note' => 'Available funds can still be reversed. The instruction converts an unverified check into an irreversible outgoing payment.',
        'asks' => '<p>Victims may be asked to send a deposit receipt, buy equipment, pay a shipping agent, purchase gift cards or return an “accidental” overpayment. The destination is often controlled by the scammer.</p>',
        'legitimate' => '<p>Remote employers commonly ship equipment directly or use established procurement and reimbursement policies. Payroll payments follow verified onboarding. They do not need a new worker to redistribute funds from a check.</p>',
        'verify' => array( 'Contact the supposed employer through its official website.', 'Tell your bank the full story, not only that you want to verify a check.', 'Confirm the vendor independently and look for a real procurement relationship.', 'Wait for reliable bank guidance rather than the recruiter’s deadline.' ),
        'contacted' => '<p>If a check was deposited, contact the bank’s fraud department promptly. If money was sent, also contact the receiving service. Do not spend remaining displayed funds and retain the check, envelope, messages and receipts.</p>',
        'faq' => array( 'If the bank shows the money, is the check safe?' => 'No. Availability is not the same as final validity, and a fraudulent check may be reversed later.', 'Can I keep part of the check as pay?' => 'Keeping part does not protect you from liability for money transferred elsewhere when the deposit is reversed.' ),
        'related' => array( 'remote_job_scams', 'advance_fee_scams', 'guide_after_scam' ),
    ),
    'reshipping_scams' => array(
        'title' => 'Reshipping and Package Forwarding Job Scams', 'slug' => 'reshipping-package-forwarding-scams', 'seo' => 'Reshipping Job Scams and Package Forwarding Risks', 'desc' => 'Learn why package inspector and reshipping jobs may involve stolen goods, unpaid wages and exposure of your home address.',
        'intro' => 'Reshipping scams recruit people to receive goods at home, remove packaging and forward parcels elsewhere. The job title may sound logistical, but the goods can originate from compromised payment accounts.',
        'quick' => 'Do not receive or forward packages until the legal business, customers and shipping relationships are independently verified.',
        'how' => '<p>The applicant becomes a “package quality inspector” or “shipping coordinator.” Parcels arrive under different names and new labels redirect them domestically or overseas. A dashboard tracks supposed wages, but payment may never arrive. The participant’s address and identity create distance from the original fraud.</p>',
        'signs' => array( 'A home address is used as an intermediate warehouse without an inspection or business agreement.', 'Packages arrive under unrelated customer names.', 'Labels are replaced to conceal the original destination.', 'Compensation is delayed until a month of forwarding is complete.', 'The company has no verifiable warehouse, clients or carrier contracts.' ),
        'example' => 'Receive electronics at your home, photograph each box, remove the retailer invoice and reship with our prepaid international label. Salary is released after 30 days.',
        'example_note' => 'Removing invoices and rerouting consumer goods through a private residence are not ordinary remote logistics duties.',
        'asks' => '<p>The worker may provide identification, home address, utility bill and bank details, then handle goods or postage. Some variations ask the worker to buy items first and promise reimbursement.</p>',
        'legitimate' => '<p>Real logistics operations use documented facilities, commercial accounts, inventory controls and known customers. A genuine employer can explain the supply chain, insurance, legal entity and why a residence would be involved.</p>',
        'verify' => array( 'Confirm business registration and operating address through authoritative local sources.', 'Contact named retailers or carriers using their published channels.', 'Ask for written insurance, customer and inventory procedures.', 'Search for the role on an official company domain, not only a dashboard.' ),
        'contacted' => '<p>Stop forwarding new parcels and do not dispose of records. Contact the carrier or retailer through official channels for instructions; if stolen goods may be involved, seek guidance from appropriate local law enforcement or legal support.</p>',
        'faq' => array( 'Are all package forwarding jobs fraudulent?' => 'No, but using a private home as an unexplained relay is high risk. Verify the full business operation before accepting goods.', 'What if shipping labels are prepaid?' => 'A prepaid label does not establish that the goods or transaction are legitimate.' ),
        'related' => array( 'remote_job_scams', 'company_impersonation_scams', 'guide_check_company' ),
    ),
    'data_entry_scams' => array(
        'title' => 'Data Entry Job Scams', 'slug' => 'data-entry-job-scams', 'seo' => 'Data Entry Job Scams: Pay, Fees and Fake Hiring Signs', 'desc' => 'Identify fake data entry jobs that use vague duties, unrealistic pay, fees, fake checks and identity collection.',
        'intro' => 'Data entry is familiar and can be done remotely, making it an effective label for vague offers. The title itself does not explain who owns the data, what system is used or why the advertised pay is unusually high.',
        'quick' => 'Ask for concrete duties, realistic productivity expectations, a verifiable employer and a normal interview before sharing records or paying anything.',
        'how' => '<p>A broad advertisement promises high hourly or daily pay with no experience. Screening takes place by text and quickly becomes an offer. The sender then introduces software, training, equipment, a background fee or a check-funded purchase. Another version collects identity information through a fake onboarding form.</p>',
        'signs' => array( 'Pay is far above comparable clerical work without specialized requirements.', 'The employer cannot describe the records, software, accuracy targets or supervisor.', 'The interview is a short questionnaire in chat.', 'Training or software must be purchased from a named source.', 'Tax, banking and identity details are requested before the organization is verified.' ),
        'example' => 'Earn $65 per hour entering customer records from home. No interview is needed. Pay $95 for our licensed database software and begin today.',
        'example_note' => 'The combination of unusually high clerical pay, no assessment and a software payment deserves verification.',
        'asks' => '<p>The applicant may be asked to buy software, cash a check, install remote access, provide a credit report or type personal data into an imitation HR portal. These steps have little connection to evaluating data-entry ability.</p>',
        'legitimate' => '<p>Real data-entry employers explain the data, confidentiality obligations, tools, accuracy standards, schedule and compensation. They assess availability and relevant skills and use established onboarding after an offer.</p>',
        'verify' => array( 'Compare pay with similar roles in the same market.', 'Ask who owns the data and what work product is expected.', 'Confirm the opening and recruiter on the employer’s official site.', 'Do not buy required software through a recruiter-controlled payment route.' ),
        'contacted' => '<p>Do not complete forms that request identity or bank data merely to schedule an interview. Report the listing to the job board and notify the impersonated company if its name was used.</p>',
        'faq' => array( 'Why are data entry jobs used in scams?' => 'The work is easy to describe vaguely and appeals to many remote applicants, which creates a large target pool.', 'Is a text interview enough for data entry?' => 'Text may be one step, but an instant offer without verifiable people or meaningful screening is a warning sign.' ),
        'related' => array( 'work_from_home_scams', 'remote_job_scams', 'guide_interview_flags' ),
    ),
    'company_impersonation_scams' => array(
        'title' => 'Company Impersonation Job Scams', 'slug' => 'company-impersonation-job-scams', 'seo' => 'Fake Company Job Scams and Employer Impersonation', 'desc' => 'Learn how scammers impersonate real employers with copied branding, lookalike domains, fake portals and fabricated offer letters.',
        'intro' => 'A real company name can be used in a fake job offer. Scammers copy logos, job descriptions, executive names and public addresses so that a quick search appears to confirm their story.',
        'quick' => 'Verify that the real organization—not merely a person using its name—controls the email, role, interview and onboarding route.',
        'how' => '<p>The sender selects a recognizable or plausible business and creates a lookalike domain, copied careers page or branded documents. Candidates may be directed to a fake HR portal that captures passwords and identity data, or to fees and equipment payments presented as company policy.</p>',
        'signs' => array( 'The email domain is similar to, but not exactly, the official domain.', 'The supplied website is new, thin or hosted on a free site builder.', 'The job exists elsewhere but the contact method and requisition do not match.', 'Offer letters contain real addresses alongside unusual payment instructions.', 'Official company staff cannot confirm the recruiter or opening.' ),
        'example' => 'Welcome to Example Global Careers. Sign in at examp1e-careers.top with your email password and upload your ID so our executive team can issue the offer today.',
        'example_note' => 'Copied branding does not overcome a lookalike domain, password request and rushed onboarding.',
        'asks' => '<p>Impersonation can support credential theft, identity theft, fees, fake checks or malware delivery. The company name is a credibility tool; the underlying request reveals the objective.</p>',
        'legitimate' => '<p>Established employers use domains and hiring systems they control, provide consistent contact information and can confirm recruiters through normal internal channels. They do not ask applicants for an existing email password.</p>',
        'verify' => array( 'Type the known company domain manually rather than following a message link.', 'Use the official site’s careers search and contact details.', 'Compare domain spelling character by character and inspect redirects.', 'Ask the company to confirm the requisition number and interviewer names.' ),
        'contacted' => '<p>If credentials were entered into a suspicious portal, change the password from the real service, sign out other sessions and enable strong multifactor authentication. Notify the impersonated employer and report the domain to the hosting or platform provider where appropriate.</p>',
        'faq' => array( 'If the job is on the real company site, is my offer real?' => 'Not necessarily. A scammer can copy a genuine vacancy. Confirm that your specific recruiter and interview route are authorized.', 'Does HTTPS prove a careers site is legitimate?' => 'No. HTTPS protects a connection to a domain; it does not prove who operates that domain.' ),
        'related' => array( 'fake_recruiter_scams', 'guide_check_company', 'email_scam_checker' ),
    ),
);

foreach ( $scam_data as $key => $data ) {
    $pages[ $key ] = $page( $data['title'], $data['slug'], $data['seo'], $data['desc'], 'scam_article', $scam_article( $data ), $data['related'], 'scam_categories' );
}

// Seven guides.
$guide_data = array(
    'guide_check_offer' => array(
        'title' => 'How to Check a Job Offer', 'slug' => 'how-to-check-a-job-offer', 'seo' => 'How to Check Whether a Job Offer Is Legitimate', 'desc' => 'Use a practical process to verify a job offer, employer, role, interview, compensation, documents and payment requests.',
        'intro' => 'A polished offer letter is evidence of what the sender claims, not proof of who sent it. Verify the opportunity by rebuilding the connection between the employer, role, recruiter and hiring process from independent sources.',
        'summary' => 'Pause deadlines, separate sender-controlled claims from independent evidence and verify before signing, paying or sharing sensitive records.',
        'steps' => array( '1. Confirm the role independently' => 'Open the employer’s official careers page without using the offer link. Match the title, location, department and requisition number. A missing listing is not conclusive, but it requires direct confirmation.', '2. Confirm the people' => 'Call or email the organization through published contact details. Ask whether the recruiter, interviewers and hiring manager are associated with the opening.', '3. Reconstruct the hiring process' => 'List each interview and who attended. An offer that appears before meaningful evaluation or only after a chat questionnaire needs more scrutiny.', '4. Review money and information requests' => 'Separate normal payroll onboarding after a verified offer from fees, checks, gift cards, crypto, passwords or OTPs. Do not proceed with the latter.', '5. Read the offer as a contract' => 'Check the legal employer, job duties, compensation, conditions, start date and signatures. Resolve contradictions through verified HR contacts.' ),
        'checklist' => array( 'The role appears on an official source or HR confirms it.', 'The recruiter and interviewers are independently verified.', 'Email and portal domains exactly match official domains.', 'No payment or forwarding of check proceeds is required.', 'Sensitive onboarding occurs only after verification through a secure process.' ),
        'example' => 'An applicant receives a branded offer after one text interview and is asked to buy a laptop from a vendor using an emailed check.',
        'lesson' => 'The letter’s appearance matters less than the abnormal interview and check-funded purchase. Official HR confirmation should happen before any transaction.',
        'mistakes' => array( 'Calling only the number printed in the offer.', 'Assuming a real vacancy proves the person contacting you is authorized.', 'Letting a same-day deadline prevent verification.', 'Treating visible check funds as final payment.' ),
        'stop' => 'Stop if the sender objects to independent verification, asks for passwords or codes, demands money, threatens a penalty or instructs you to mislead a bank or platform.',
        'faq' => array( 'Can a genuine offer arrive quickly?' => 'Yes, but speed does not remove the need to verify identity, role and terms.', 'What if the role is confidential?' => 'Some searches are confidential, but the recruiter or agency should still have a verifiable identity and explain a safe confirmation process.' ),
        'related' => array( 'job_scam_checker', 'guide_verify_recruiter', 'remote_job_scams' ),
    ),
    'guide_verify_recruiter' => array(
        'title' => 'How to Verify a Recruiter', 'slug' => 'how-to-verify-a-recruiter', 'seo' => 'How to Verify a Recruiter and Confirm Their Identity', 'desc' => 'Confirm a recruiter’s identity, employer or agency authorization, email domain and connection to a real job opening.',
        'intro' => 'Recruiter verification has two parts: confirming the person or agency exists and confirming that the account contacting you is controlled by that person and authorized for the role.',
        'summary' => 'Use a contact route that you found independently; do not ask the suspicious account to verify itself.',
        'steps' => array( '1. Identify the claimed relationship' => 'Ask whether the recruiter works for the employer or an outside agency, and request the job title, client where permitted and requisition number.', '2. Inspect the actual address' => 'Ignore the display name temporarily. Compare the complete email domain with the company or agency website, including spelling and hyphens.', '3. Find an independent contact' => 'Use the official website, switchboard or established agency office—not a message link—to ask whether the recruiter works there.', '4. Confirm authorization for the role' => 'A real employee name can be impersonated. Ask verified HR whether that person is handling the specific opening.', '5. Verify meeting participants' => 'Review calendar domains and attendee identities. A normal interview should involve people whose organizational connection can be confirmed.' ),
        'checklist' => array( 'Recruiter explains how they found you and what role they represent.', 'Agency or employer confirms the recruiter independently.', 'The domain and meeting invitations are consistent.', 'The recruiter accepts reasonable verification questions.', 'No fee, password, OTP or personal-account transfer is requested.' ),
        'example' => 'A message copies a real recruiter’s profile photo but comes from a newly created free mailbox and asks to continue on Telegram.',
        'lesson' => 'Finding the recruiter’s genuine profile does not validate the contacting account. A reply through a verified agency address can expose the impersonation.',
        'mistakes' => array( 'Relying on profile age, connections or endorsements alone.', 'Calling a number included in the same suspicious signature.', 'Assuming the email display name is the sending address.', 'Sending an ID document merely to receive a job description.' ),
        'stop' => 'Stop when the recruiter cannot state who employs them, will not permit verification, avoids all real-time conversation or introduces payments and sensitive credentials.',
        'faq' => array( 'Can recruiters contact people who did not apply?' => 'Yes. Unsolicited sourcing is normal in some fields, but the recruiter and role must still be verifiable.', 'Is a free email address always fraudulent?' => 'No, but it provides less organizational evidence and should trigger independent confirmation.' ),
        'related' => array( 'recruiter_checker', 'fake_recruiter_scams', 'guide_check_company' ),
    ),
    'guide_suspicious_message' => array(
        'title' => 'How to Handle a Suspicious Job Message', 'slug' => 'how-to-handle-a-suspicious-job-message', 'seo' => 'What to Do With a Suspicious Job or Recruiter Message', 'desc' => 'Safely review a suspicious employment email, SMS, WhatsApp or Telegram message without clicking links or exposing personal data.',
        'intro' => 'You do not need to reply immediately to investigate a job message. A safe review preserves evidence, avoids sender-controlled links and verifies the claim through a separate route.',
        'summary' => 'Do not click, pay or share information while you establish who sent the message and whether the role exists.',
        'steps' => array( '1. Pause and preserve the message' => 'Take screenshots or save headers where practical. Do not forward sensitive content broadly or publish personal accusations.', '2. Remove sensitive details before analysis' => 'If using the checker, omit passwords, codes, bank details and identification numbers.', '3. Examine the request' => 'Identify the action the sender wants: opening a link, moving platforms, paying, installing software, sharing data or completing a task.', '4. Verify outside the conversation' => 'Find the employer’s official site and contact it through published information.', '5. Report through the right channel' => 'Use the messaging service, email provider or job board reporting feature, and contact payment or identity providers if exposure occurred.' ),
        'checklist' => array( 'No links or attachments were opened during verification.', 'The sender’s actual address and domain were examined.', 'The role and recruiter were checked independently.', 'Sensitive data was removed before sharing the text with anyone.', 'Evidence is retained privately if a report becomes necessary.' ),
        'example' => 'An SMS says a résumé was approved and provides a shortened link for an immediate WhatsApp interview.',
        'lesson' => 'The safe path is to avoid the short link, search the claimed employer independently and confirm whether it uses that recruiting route.',
        'mistakes' => array( 'Replying “stop” before preserving evidence when a report may be needed.', 'Opening a link to discover where it goes.', 'Posting the sender’s personal details publicly without verification.', 'Assuming silence or account deletion proves the identity behind it.' ),
        'stop' => 'End the conversation when the sender demands secrecy, money, codes, remote device access or continued contact through unverifiable accounts.',
        'faq' => array( 'Can I paste an email header into the checker?' => 'The checker is designed for message text and URLs. Remove personal identifiers and credentials first.', 'Should I confront the sender?' => 'Usually, quiet verification and platform reporting are safer than providing more information or revealing how you identified the warning signs.' ),
        'related' => array( 'email_scam_checker', 'messaging_job_scams', 'guide_after_scam' ),
    ),
    'guide_interview_flags' => array(
        'title' => 'Job Interview Red Flags', 'slug' => 'job-interview-red-flags', 'seo' => 'Job Interview Red Flags: Text Interviews and Instant Offers', 'desc' => 'Recognize interview warning signs including text-only screening, instant offers, financial tasks, secrecy and requests unrelated to qualifications.',
        'intro' => 'Interview formats vary, but a legitimate process should help both sides evaluate fit and clarify the work. A conversation focused on payments or account setup rather than qualifications may be serving another purpose.',
        'summary' => 'Evaluate who attends, what they ask, how the meeting is hosted and whether the process connects to a verified employer.',
        'steps' => array( '1. Confirm participants before the meeting' => 'Check names, roles and invitation domains. Contact verified HR if a last-minute account replaces the expected interviewer.', '2. Assess the substance' => 'Real interviews usually discuss experience, duties, scenarios, availability and questions from the candidate.', '3. Notice channel restrictions' => 'A text screen can be an initial step, but refusal of any voice, video or verifiable contact deserves caution.', '4. Separate interviewing from onboarding' => 'Banking, tax and identification generally belong after a verified conditional offer, not in an anonymous chat interview.', '5. Review the offer timing' => 'An instant offer is not automatically false, but it should match the role’s complexity and documented process.' ),
        'checklist' => array( 'Interviewers have verifiable organizational identities.', 'Questions relate meaningfully to the role.', 'The candidate can ask questions and receives coherent answers.', 'No financial transaction or password is part of the interview.', 'Next steps arrive through a consistent official channel.' ),
        'example' => 'A “hiring manager” sends twenty yes/no questions in chat, declares a perfect score and immediately emails a check for equipment.',
        'lesson' => 'The questionnaire creates the appearance of screening, but the rapid check transaction reveals the likely objective.',
        'mistakes' => array( 'Assuming a long questionnaire equals a meaningful assessment.', 'Installing unfamiliar video software from a direct attachment.', 'Sharing identity documents because an interviewer says HR is waiting.', 'Ignoring inconsistencies because the offer is attractive.' ),
        'stop' => 'Leave or pause an interview that becomes coercive, asks for money or account access, requires secrecy, or cannot establish who the interviewers are.',
        'faq' => array( 'Are chat interviews legitimate?' => 'They can be one stage, but chat-only hiring plus instant offers or financial requests is concerning.', 'Should an interviewer ask for my Social Security number?' => 'Sensitive tax or identity data generally belongs in secure onboarding after employer verification, not routine interviewing.' ),
        'related' => array( 'data_entry_scams', 'remote_job_scams', 'guide_check_offer' ),
    ),
    'guide_check_company' => array(
        'title' => 'How to Check If a Company Is Real', 'slug' => 'how-to-check-if-a-company-is-real', 'seo' => 'How to Check If a Company and Job Opportunity Are Real', 'desc' => 'Verify a company website, legal identity, contact information and hiring presence while distinguishing a real business from impersonation.',
        'intro' => 'A company can be real while the offer using its name is fake. Company verification must connect the legal organization, official digital presence and specific person contacting you.',
        'summary' => 'Use authoritative records and contact details found outside the message, then confirm the specific opening and recruiter.',
        'steps' => array( '1. Locate the official website independently' => 'Use a known address, reputable directory or careful search. Do not assume the first advertisement or supplied link is official.', '2. Examine the domain and site history' => 'Look for consistent contact information, substantive business details and an established careers section. HTTPS alone proves little.', '3. Check appropriate public records' => 'Depending on the location, business registries or professional regulators can confirm an entity. A registration still does not validate every person using its name.', '4. Contact the organization' => 'Use a published switchboard or domain-based contact to ask about the role and recruiter.', '5. Compare the whole story' => 'Addresses, legal names, email domains, job details and interview participants should align.' ),
        'checklist' => array( 'Official domain was found without using the recruiter’s link.', 'Legal name and operating details are consistent.', 'Published contact confirms the role and recruiter.', 'The careers page or HR team recognizes the opening.', 'No lookalike domain or unexplained payment destination is involved.' ),
        'example' => 'A real manufacturer’s name appears on an offer, but replies go to a hyphenated domain registered for a separate “careers” site.',
        'lesson' => 'Proving the manufacturer exists does not prove it controls the lookalike domain. Contact through the manufacturer’s established site.',
        'mistakes' => array( 'Treating incorporation as proof of the job offer.', 'Using only reviews, which may be unrelated or manipulated.', 'Calling the number on the suspicious website.', 'Assuming a logo and postal address establish control.' ),
        'stop' => 'Stop when the real organization denies the role, the domain cannot be connected to it, or the sender prevents contact with published company channels.',
        'faq' => array( 'Does a business registry prove the recruiter is genuine?' => 'No. It helps confirm an entity, but you still need to connect the contacting person and role to it.', 'Is a new company website always suspicious?' => 'No. New businesses exist, but limited history means you need stronger evidence from registration, people, customers and direct contact.' ),
        'related' => array( 'company_impersonation_scams', 'guide_verify_recruiter', 'guide_check_offer' ),
    ),
    'guide_protect_info' => array(
        'title' => 'Protect Personal Information During a Job Search', 'slug' => 'protect-personal-information-during-a-job-search', 'seo' => 'Protect Personal Information During Job Applications', 'desc' => 'Learn what information recruiters need at each hiring stage and how to protect identity, banking, password and verification-code data.',
        'intro' => 'Job applications legitimately require personal information, but timing, purpose and collection method matter. Share the minimum needed for the current stage through a verified system.',
        'summary' => 'A résumé is not onboarding, and an interview is not payroll. Delay high-risk information until the employer and process are confirmed.',
        'steps' => array( '1. Minimize public résumé details' => 'Use professional contact information and general location where appropriate. Avoid government numbers, birth date, full address, account data and document images.', '2. Match information to the hiring stage' => 'Early applications need qualifications and contact details. Tax, right-to-work and payroll data usually follow a verified conditional offer.', '3. Verify the collection system' => 'Reach the HR portal through the official employer site. Do not log in through shortened or lookalike links.', '4. Protect account secrets' => 'No recruiter needs an existing password, OTP or authentication code. These cannot be made safe by a confidentiality claim.', '5. Keep records' => 'Note what you shared, with whom and through which verified system so you can respond quickly if identity concerns emerge.' ),
        'checklist' => array( 'Résumé excludes high-risk identity numbers.', 'Employer is verified before documents are uploaded.', 'Portal domain matches the official organization or disclosed HR provider.', 'Passwords and one-time codes are never shared.', 'Document copies are limited to what is legally and operationally necessary.' ),
        'example' => 'Before scheduling any interview, a recruiter asks for a passport photo, bank statement and the code sent to the applicant’s phone.',
        'lesson' => 'The combination exceeds what is needed to arrange an interview and includes an account-security code that should never be shared.',
        'mistakes' => array( 'Sending a full ID because a message includes a privacy notice.', 'Reusing a password on an applicant portal.', 'Including government numbers on a résumé.', 'Assuming a cloud-storage upload link belongs to the employer.' ),
        'stop' => 'Stop when the purpose is unclear, the sender requests authentication secrets, or the collection method cannot be connected to a verified employer.',
        'faq' => array( 'When is bank information normal?' => 'Usually during verified payroll onboarding after an accepted offer, through a secure HR process.', 'Should I watermark identity documents?' => 'Where lawful and accepted, a purpose-specific watermark may reduce reuse, but verification and secure transfer remain essential.' ),
        'related' => array( 'guide_after_scam', 'fake_recruiter_scams', 'company_impersonation_scams' ),
    ),
    'guide_after_scam' => array(
        'title' => 'What to Do After a Suspected Job Scam', 'slug' => 'what-to-do-after-a-job-scam', 'seo' => 'What to Do After a Job Scam: Money, Accounts and Identity', 'desc' => 'Prioritize urgent steps after a suspected job scam based on money sent, passwords shared, documents exposed or software installed.',
        'intro' => 'The right response depends on what happened. Prioritize actions that can limit ongoing loss—payments, account access and device control—before documenting and reporting.',
        'summary' => 'Stop contact, do not send a “final” payment, and respond according to the money, credentials, identity data or device access involved.',
        'steps' => array( '1. Stop further loss' => 'Do not pay withdrawal taxes, recovery charges or penalties. Contact banks, card issuers, payment apps or crypto exchanges through official support as soon as possible.', '2. Secure accounts' => 'Change exposed passwords from a trusted device, sign out other sessions, review recovery details and enable strong multifactor authentication. Never share another OTP.', '3. Address device access' => 'If remote-access tools or unknown software were installed, disconnect the device from sensitive accounts and seek qualified technical help.', '4. Document privately' => 'Save messages, headers, usernames, domains, wallet addresses, receipts and dates. Avoid publishing unverified personal accusations.', '5. Report appropriately' => 'Report the account or listing to the platform and use the relevant consumer protection, cybercrime or law-enforcement channel for your location.' ),
        'checklist' => array( 'No additional payment was made to unlock or recover funds.', 'Payment providers were contacted through official channels.', 'Passwords, sessions and recovery settings were secured.', 'Evidence was retained without exposing more personal data.', 'The listing, account or domain was reported to the relevant service.' ),
        'example' => 'After two task deposits, a platform demands a tax payment to release the balance while the mentor warns that stopping will forfeit everything.',
        'lesson' => 'A further payment increases exposure. Preserve the records, contact the payment provider and report rather than chasing the displayed balance.',
        'mistakes' => array( 'Paying a recovery service that guarantees success for an upfront fee.', 'Deleting all messages before recording payment details.', 'Continuing contact to trap or threaten the sender.', 'Changing a password from a device still under remote control.' ),
        'stop' => 'Do not re-engage because a different account claims to be support, law enforcement or a recovery specialist. Verify every helper independently.',
        'faq' => array( 'Can money always be recovered?' => 'No. Recovery depends on method, timing and provider. Anyone guaranteeing recovery for another fee should be treated cautiously.', 'Should I report even if I did not pay?' => 'Reporting the listing or account can still help the platform investigate and may protect other users.' ),
        'related' => array( 'guide_suspicious_message', 'fake_check_scams', 'task_scams' ),
    ),
);

foreach ( $guide_data as $key => $data ) {
    $pages[ $key ] = $page( $data['title'], $data['slug'], $data['seo'], $data['desc'], 'guide', $guide( $data ), $data['related'], 'guides' );
}

// Trust and legal pages accurately describing the current service.
$pages['about'] = $page( 'About', 'about', 'About Job Scam Checker', 'Learn how Job Scam Checker provides a free, rule-based employment message risk assessment without AI or visitor accounts.', 'trust',
    '<p class="jsc-lead">Job Scam Checker is a free public educational utility designed to help people pause and examine suspicious employment communications.</p><h2>What the service does</h2><p>The checker analyzes pasted text on this WordPress website using a local library of weighted rules. It looks for common patterns such as recruitment fees, fake checks, task deposits, credential requests, unrealistic income and suspicious link characteristics. It then explains detected signals and suggests independent verification steps.</p><h2>What it cannot do</h2><p>The result is not a guarantee, background check, legal determination or accusation. A low score does not prove an offer is legitimate, and a high score does not establish fraud. The service does not contact recruiters, confirm corporate records in real time or use a paid reputation database.</p><h2>How we approach trust</h2><p>Visitors do not need an account. The checker does not permanently store pasted messages. We do not claim partnerships, certifications, official authority or success statistics. Educational examples are fictional composites rather than allegations about named people.</p><p>Start with the <a href="/job-scam-checker/">free checker</a>, browse <a href="/job-scams/">job scam categories</a> or follow the <a href="/guides/how-to-check-a-job-offer/">job-offer verification guide</a>.</p>', array( 'privacy', 'disclaimer', 'job_scam_checker' ) );

$pages['contact'] = $page( 'Contact', 'contact', 'Contact Job Scam Checker', 'Contact information and safe reporting guidance for questions about the Job Scam Checker website.', 'trust',
    '<p class="jsc-lead">Use this page for questions about the website, content corrections or accessibility concerns. This site is not an emergency service and cannot investigate individuals or recover money.</p><div class="jsc-safety-callout"><strong>Do not send sensitive information.</strong> Do not include passwords, OTPs, banking details, government identification numbers or complete scam-message records in a contact request.</div><h2>Website contact</h2><p>The site administrator should configure a public contact method in WordPress before publishing this page. Until then, no email address, postal address or organization details are invented here.</p><h2>Reporting a suspicious job</h2><p>Report the account or listing to the platform where it appeared. If money, account access or identity documents are involved, contact the relevant bank, provider or authority using official channels in your location. See <a href="/guides/what-to-do-after-a-job-scam/">what to do after a suspected job scam</a>.</p><h2>Content corrections</h2><p>When a contact channel is configured, describe the page URL and the specific wording that may need correction. Do not submit accusations against identifiable people for automatic publication; this website does not operate a public allegation directory.</p>', array( 'about', 'guide_after_scam', 'privacy' ) );

$pages['privacy'] = $page( 'Privacy Policy', 'privacy-policy', 'Privacy Policy | Job Scam Checker', 'How Job Scam Checker processes pasted text, rate-limit identifiers, WordPress data and optional third-party integrations.', 'legal',
    '<p><strong>Last reviewed:</strong> Phase 4 release.</p><p>This policy describes the custom Job Scam Checker functionality. The hosting provider and WordPress installation may maintain standard security or server logs outside the custom checker code.</p><h2>Pasted messages</h2><p>The checker sends pasted text to this website’s own WordPress endpoint for immediate rule-based analysis. The custom code does not permanently store the message, add it to statistics, place it in a URL, return it in the result or send it to an AI provider. Remove sensitive information before submitting.</p><h2>Rate limiting</h2><p>To reduce abuse, the checker temporarily stores a request counter under a salted one-way identifier derived from the connecting network address. The raw address and message are not stored in that transient by the custom code. The counter expires automatically.</p><h2>Pages and administration</h2><p>WordPress stores site pages, editable detection rules and normal administrator settings. Visitors do not need an account to use the checker.</p><h2>Analytics and advertising</h2><p>The current custom implementation does not require Google Analytics or AdSense. Empty integration placeholders do not load advertisements or trackers. If analytics or advertising is enabled later, this policy and any consent controls should be updated before deployment to describe the actual providers and cookies.</p><h2>Contact and retention</h2><p>No public contact channel is fabricated. The site operator should add accurate contact details and jurisdiction-specific privacy information before production publication. Removing the plugin deletes its rule table and options but preserves administrator-edited pages.</p>', array( 'terms', 'disclaimer', 'about' ) );

$pages['terms'] = $page( 'Terms of Use', 'terms-of-use', 'Terms of Use | Job Scam Checker', 'Terms governing use of the free Job Scam Checker educational website and automated risk assessment.', 'legal',
    '<p><strong>Last reviewed:</strong> Phase 4 release.</p><h2>Educational service</h2><p>Job Scam Checker provides automated, rule-based information about common employment scam indicators. It is not legal, financial, cybersecurity or law-enforcement advice and does not replace independent verification.</p><h2>No guarantee</h2><p>Messages may be misunderstood, warning signs may be missed and legitimate communications may contain matched wording. You remain responsible for decisions, payments, communications and information sharing.</p><h2>Acceptable use</h2><p>Do not submit passwords, OTPs, banking credentials, government identification numbers, unlawful content or material you are not permitted to process. Do not attempt to overwhelm, bypass, scrape abusively or interfere with the checker or WordPress site.</p><h2>Identifiable allegations</h2><p>Do not treat a result as proof for publishing accusations about a person or organization. The service does not automatically publish user reports and does not verify the identity behind a message.</p><h2>Availability and changes</h2><p>The site may change rules, content, limits or availability to improve safety and reliability. There is no promise of uninterrupted access. The site operator should adapt these terms to applicable law and add accurate operator contact details before public launch.</p>', array( 'privacy', 'disclaimer', 'about' ) );

$pages['disclaimer'] = $page( 'Disclaimer', 'disclaimer', 'Disclaimer | Job Scam Checker', 'Important limitations of the Job Scam Checker risk score, educational content and suspicious-domain findings.', 'legal',
    '<p class="jsc-lead">Job Scam Checker is a risk-assessment and education tool. It cannot determine with certainty whether a message, recruiter, company, website or job is legitimate or fraudulent.</p><h2>Automated limitations</h2><p>The score is produced from weighted text and link rules. Scammers change language, legitimate employers sometimes use unusual processes, and the checker does not investigate external facts in real time. Results can contain false positives and false negatives.</p><h2>No professional relationship</h2><p>Using the site does not create a lawyer-client, financial adviser, investigator or cybersecurity professional relationship. Seek qualified local help for legal rights, financial loss, identity theft or device compromise.</p><h2>Links and names</h2><p>Suspicious domains are displayed as non-clickable text based on technical characteristics. Their appearance is not an accusation about an identifiable owner. External sites and platforms control their own content and policies.</p><h2>Independent action</h2><p>Verify employers through official sources you locate yourself. Contact banks, account providers, job platforms and relevant authorities through their genuine channels when necessary. Do not delay urgent protective action because of this website’s result.</p>', array( 'about', 'terms', 'guide_check_offer' ) );

return $pages;
