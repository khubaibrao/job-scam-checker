<?php
/** Real-data trend calculation with minimum sample safeguards. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JSC_Trend_Provider {
    const MIN_PERIOD_CHECKS = 10;
    const MIN_PATTERN_COUNT = 5;

    private $repository;

    public function __construct( $repository ) { $this->repository = $repository; }

    public function get_trends( $today = null ) {
        if ( ! JSC_Statistics::enabled() ) { return array(); }
        $today         = $today ?: current_time( 'Y-m-d', true );
        $current_start = gmdate( 'Y-m-d', strtotime( $today . ' -13 days' ) );
        $prior_end     = gmdate( 'Y-m-d', strtotime( $current_start . ' -1 day' ) );
        $prior_start   = gmdate( 'Y-m-d', strtotime( $prior_end . ' -13 days' ) );
        $current_total = array_sum( $this->repository->counts( 'checks', $current_start, $today ) );
        $prior_total   = array_sum( $this->repository->counts( 'checks', $prior_start, $prior_end ) );
        if ( $current_total < self::MIN_PERIOD_CHECKS || $prior_total < self::MIN_PERIOD_CHECKS ) { return array(); }

        $labels = self::labels();
        $trends = array();
        foreach ( array( 'detection', 'channel', 'payment_purpose' ) as $metric ) {
            $current = $this->repository->counts( $metric, $current_start, $today );
            $prior   = $this->repository->counts( $metric, $prior_start, $prior_end );
            foreach ( $current as $key => $count ) {
                $before = $prior[ $key ] ?? 0;
                if ( $count >= self::MIN_PATTERN_COUNT && $before >= 3 && ( $count / $current_total ) > ( $before / $prior_total ) && isset( $labels[ $metric ][ $key ] ) ) {
                    $trends[] = array( 'label' => $labels[ $metric ][ $key ], 'current_count' => $count );
                }
            }
        }
        usort( $trends, static function ( $a, $b ) { return $b['current_count'] <=> $a['current_count']; } );
        return array_slice( $trends, 0, 5 );
    }

    private static function labels() {
        return array(
            'channel' => array( 'whatsapp' => __( 'WhatsApp job offers', 'job-scam-checker' ), 'telegram' => __( 'Telegram job offers', 'job-scam-checker' ), 'sms' => __( 'SMS job offers', 'job-scam-checker' ), 'email' => __( 'Email job offers', 'job-scam-checker' ), 'linkedin' => __( 'LinkedIn job offers', 'job-scam-checker' ), 'facebook' => __( 'Facebook job offers', 'job-scam-checker' ), 'job_board' => __( 'Job-board offers', 'job-scam-checker' ), 'other' => __( 'Other contact channels', 'job-scam-checker' ) ),
            'payment_purpose' => array( 'training' => __( 'Training-payment requests', 'job-scam-checker' ), 'equipment' => __( 'Equipment-payment requests', 'job-scam-checker' ), 'registration' => __( 'Registration-payment requests', 'job-scam-checker' ), 'task_deposit' => __( 'Task-deposit requests', 'job-scam-checker' ), 'cryptocurrency' => __( 'Cryptocurrency-payment requests', 'job-scam-checker' ), 'gift_cards' => __( 'Gift-card requests', 'job-scam-checker' ), 'other' => __( 'Other payment requests', 'job-scam-checker' ) ),
            'detection' => array( 'telegram-recruitment' => __( 'Telegram recruitment patterns', 'job-scam-checker' ), 'whatsapp-recruitment' => __( 'WhatsApp recruitment patterns', 'job-scam-checker' ), 'recharge-task' => __( 'Task or optimization job patterns', 'job-scam-checker' ), 'task-deposit' => __( 'Task-deposit patterns', 'job-scam-checker' ), 'equipment-payment' => __( 'Equipment-payment patterns', 'job-scam-checker' ), 'cryptocurrency-request' => __( 'Cryptocurrency requests', 'job-scam-checker' ) ),
        );
    }
}
