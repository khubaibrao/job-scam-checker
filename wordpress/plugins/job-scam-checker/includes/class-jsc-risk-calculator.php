<?php
/**
 * Deterministic risk scoring.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Risk_Calculator {
    /** @var array<string,int> */
    private $category_caps = array(
        'payment'       => 38,
        'credentials'   => 55,
        'task_scam'     => 35,
        'compensation'  => 25,
        'hiring'        => 24,
        'channel'       => 20,
        'pressure'      => 18,
        'links'         => 25,
        'identity'      => 24,
        'fake_check'    => 30,
        'job_type'      => 24,
        'communication' => 18,
    );

    /**
     * Deduplicate scoring groups, cap noisy categories and produce a 0–100 score.
     *
     * @param array<int,array<string,mixed>> $matches Raw matches.
     * @return array<string,mixed>
     */
    public function calculate( array $matches ) {
        $group_winners = array();

        foreach ( $matches as $match ) {
            $group = $match['score_group'];
            if ( ! isset( $group_winners[ $group ] ) || (int) $match['weight'] > (int) $group_winners[ $group ]['weight'] ) {
                $group_winners[ $group ] = $match;
            }
        }

        $category_totals = array();
        $detections      = array_values( $group_winners );

        usort(
            $detections,
            static function ( $a, $b ) {
                return (int) $b['weight'] <=> (int) $a['weight'];
            }
        );

        $score = 0;
        foreach ( $detections as &$detection ) {
            $category = $detection['category'];
            $current  = $category_totals[ $category ] ?? 0;
            $cap      = $this->category_caps[ $category ] ?? 30;
            $applied  = max( 0, min( (int) $detection['weight'], $cap - $current ) );

            $detection['applied_weight']     = $applied;
            $category_totals[ $category ]    = $current + $applied;
            $score                          += $applied;
        }
        unset( $detection );

        $score = min( 100, max( 0, $score ) );

        return array(
            'score'      => $score,
            'level'      => self::level_for_score( $score ),
            'detections' => $detections,
        );
    }

    /**
     * @return array<string,string>
     */
    public static function level_for_score( $score ) {
        if ( $score >= 75 ) {
            return array( 'key' => 'very_high', 'label' => __( 'Very High Risk', 'job-scam-checker' ), 'message' => __( 'Very high-risk scam indicators detected.', 'job-scam-checker' ) );
        }
        if ( $score >= 50 ) {
            return array( 'key' => 'high', 'label' => __( 'High Risk', 'job-scam-checker' ), 'message' => __( 'High-risk scam indicators detected.', 'job-scam-checker' ) );
        }
        if ( $score >= 25 ) {
            return array( 'key' => 'caution', 'label' => __( 'Caution', 'job-scam-checker' ), 'message' => __( 'Potential warning signs detected.', 'job-scam-checker' ) );
        }
        return array( 'key' => 'low', 'label' => __( 'Low Risk Indicators', 'job-scam-checker' ), 'message' => __( 'Few common scam indicators were detected. Verify independently before proceeding.', 'job-scam-checker' ) );
    }
}
