<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Scheduler
{
    /**
     * Event Name
     */
    const EVENT = 'pem_scheduler_event';

    /**
     * Activation
     */
    public static function activate()
    {
        if (!wp_next_scheduled(self::EVENT)) {

            wp_schedule_event(
                time(),
                'every_five_minutes',
                self::EVENT
            );

        }
    }

    /**
     * Deactivation
     */
    public static function deactivate()
    {
        $timestamp = wp_next_scheduled(self::EVENT);

        if ($timestamp) {

            wp_unschedule_event(
                $timestamp,
                self::EVENT
            );

        }
    }

    /**
     * Cron Callback
     */
    public static function run()
    {
        global $wpdb;

        $campaigns = $wpdb->get_results(

            $wpdb->prepare(

                "SELECT *
                FROM {$wpdb->prefix}pushpa_campaigns
                WHERE status=%s
                AND send_type=%s
                AND scheduled_at<=%s
                ORDER BY scheduled_at ASC",

                'Scheduled',
                'schedule',
                current_time('mysql')

            )

        );

        if (empty($campaigns)) {
            return;
        }

        foreach ($campaigns as $campaign) {

            PEM_Queue::processCampaign($campaign);

        }
    }
}

/**
 * Cron Hook
 */
add_action(
    PEM_Scheduler::EVENT,
    array(
        'PEM_Scheduler',
        'run'
    )
);