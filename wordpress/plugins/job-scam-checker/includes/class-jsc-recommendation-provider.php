<?php
/**
 * Result-specific safety action selection.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Recommendation_Provider {
    /**
     * Build a concise, deduplicated action plan from detected categories.
     *
     * @param array<int,array<string,mixed>> $detections Scored detections.
     * @param int                            $score Risk score.
     * @return array<int,array<string,string>>
     */
    public function for_result( array $detections, $score ) {
        $categories = array_unique( array_column( $detections, 'category' ) );
        $actions    = array();

        $this->add(
            $actions,
            'verify-employer',
            __( 'Verify the employer independently', 'job-scam-checker' ),
            __( 'Find the organization’s official website yourself and contact it using published details—not the phone number, email, or link in the message.', 'job-scam-checker' )
        );
        $this->add(
            $actions,
            'official-careers-page',
            __( 'Check the official careers page', 'job-scam-checker' ),
            __( 'Look for the same role on the employer’s real website and compare the title, location, recruiter, and application process.', 'job-scam-checker' )
        );
        $this->add( $actions, 'no-money', __( 'Do not send money', 'job-scam-checker' ), __( 'Do not pay a recruiter, deposit funds, buy gift cards, transfer cryptocurrency, or forward money from a check to obtain a job.', 'job-scam-checker' ) );
        $this->add( $actions, 'protect-codes', __( 'Protect passwords and verification codes', 'job-scam-checker' ), __( 'Do not provide passwords, OTPs, authentication codes, or banking login details to a recruiter.', 'job-scam-checker' ) );
        $this->add( $actions, 'protect-identity', __( 'Be cautious with sensitive personal information', 'job-scam-checker' ), __( 'Share identity documents or government numbers only after verifying the employer and its secure HR process.', 'job-scam-checker' ) );

        if ( array_intersect( $categories, array( 'payment', 'fake_check', 'task_scam' ) ) ) {
            $this->add( $actions, 'no-money', __( 'Do not send money', 'job-scam-checker' ), __( 'Do not pay fees, deposits, cryptocurrency, gift cards, or forward money from a check. A legitimate employer should not require payment to get a job.', 'job-scam-checker' ) );
        }
        if ( in_array( 'credentials', $categories, true ) ) {
            $this->add( $actions, 'protect-codes', __( 'Protect passwords and verification codes', 'job-scam-checker' ), __( 'Do not provide passwords, OTPs, authentication codes, or banking login details. Change exposed credentials and contact the affected provider.', 'job-scam-checker' ) );
        }
        if ( in_array( 'identity', $categories, true ) ) {
            $this->add( $actions, 'protect-identity', __( 'Limit sensitive personal information', 'job-scam-checker' ), __( 'Avoid sending identity documents or government numbers until the employer and its secure HR process are verified.', 'job-scam-checker' ) );
        }
        if ( array_intersect( $categories, array( 'links', 'channel', 'communication' ) ) ) {
            $this->add( $actions, 'avoid-links', __( 'Avoid supplied links and unknown software', 'job-scam-checker' ), __( 'Type the official website address manually. Do not install apps, remote-access tools, or files sent by an unverified recruiter.', 'job-scam-checker' ) );
        }
        if ( in_array( 'hiring', $categories, true ) ) {
            $this->add( $actions, 'request-interview', __( 'Expect a verifiable hiring process', 'job-scam-checker' ), __( 'Ask for a real interview, written job description, and recruiter contact on the employer’s official domain.', 'job-scam-checker' ) );
        }
        if ( in_array( 'job_type', $categories, true ) ) {
            $this->add( $actions, 'avoid-transfer', __( 'Do not move money or packages', 'job-scam-checker' ), __( 'Do not use your bank account to transfer funds or your address to receive and reship goods for an unverified employer.', 'job-scam-checker' ) );
        }
        if ( $score >= 50 ) {
            $this->add( $actions, 'pause-contact', __( 'Pause contact and preserve evidence', 'job-scam-checker' ), __( 'Do not respond under pressure. Save screenshots and report the account to the platform or appropriate authority if the approach remains suspicious.', 'job-scam-checker' ) );
        } else {
            $this->add( $actions, 'stay-cautious', __( 'Continue cautiously', 'job-scam-checker' ), __( 'A low score is not proof that an offer is legitimate. Confirm the people, company, and role before sharing information or taking action.', 'job-scam-checker' ) );
        }

        return array_values( $actions );
    }

    /**
     * @param array<string,array<string,string>> $actions Action map.
     */
    private function add( array &$actions, $id, $title, $description ) {
        $actions[ $id ] = array(
            'id'          => sanitize_key( $id ),
            'title'       => sanitize_text_field( $title ),
            'description' => sanitize_text_field( $description ),
        );
    }
}
