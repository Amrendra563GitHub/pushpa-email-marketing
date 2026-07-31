<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Dashboard
{

    /**
     * Dashboard Statistics
     */
    public static function stats()
    {
        return array(

            'contacts'       => PEM_Contact::count(),
            'campaigns'      => PEM_Campaign::count(),
            'templates'      => PEM_Template::count(),
            'logs'           => PEM_Email_Log::count(),
            'opened'         => PEM_Email_Log::totalOpened(),
            'clicked'        => PEM_Email_Log::totalClicked(),
            'unsubscribed'   => PEM_Email_Log::totalUnsubscribed()

        );
    }

    /**
 * Recent Campaigns
 */
public static function recentCampaigns($limit = 5)
{
    global $wpdb;

    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                campaign_name,
                status,
                sent_count,
                failed_count,
                created_at
            FROM {$wpdb->prefix}pushpa_campaigns
            ORDER BY id DESC
            LIMIT %d",
            absint($limit)
        )
    );
}

/**
 * Recent Email Logs
 */
public static function recentLogs($limit = 10)
{
    global $wpdb;

    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT
                email,
                status,
                sent_at
            FROM {$wpdb->prefix}pushpa_email_logs
            ORDER BY id DESC
            LIMIT %d",
            absint($limit)
        )
    );
}

/**
 * Active SMTP
 */
public static function smtp()
{
    global $wpdb;

    return $wpdb->get_row(
        "SELECT *
        FROM {$wpdb->prefix}pushpa_smtp
        WHERE status='Active'
        LIMIT 1"
    );
}
/**
 * Campaign Status Statistics
 */
public static function campaignStatus()
{
    global $wpdb;

    return array(

        'running' => (int) $wpdb->get_var(
            "SELECT COUNT(*)
            FROM {$wpdb->prefix}pushpa_campaigns
            WHERE status='Running'"
        ),

        'completed' => (int) $wpdb->get_var(
            "SELECT COUNT(*)
            FROM {$wpdb->prefix}pushpa_campaigns
            WHERE status='Completed'"
        ),

        'draft' => (int) $wpdb->get_var(
            "SELECT COUNT(*)
            FROM {$wpdb->prefix}pushpa_campaigns
            WHERE status='Draft'"
        )

    );
}
/**
 * Last 7 Days Email Activity
 */
public static function emailActivity()
{
    global $wpdb;

    return $wpdb->get_results(
        "
        SELECT
            DATE(sent_at) AS day,
            COUNT(*) AS total
        FROM {$wpdb->prefix}pushpa_email_logs
        WHERE sent_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(sent_at)
        ORDER BY day ASC
        ",
        ARRAY_A
    );
}

/**
 * Campaign Reports
 */
public static function campaignReports($limit = 10)
{
    global $wpdb;

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                id,
                campaign_name,
                sent_count,
                failed_count,
                status,
                created_at
            FROM {$wpdb->prefix}pushpa_campaigns
            ORDER BY id DESC
            LIMIT %d
            ",
            absint($limit)
        )
    );
}

/**
 * Get Single Campaign
 */
public static function getCampaign($id)
{
    global $wpdb;

    return $wpdb->get_row(
        $wpdb->prepare(
            "
            SELECT *
            FROM {$wpdb->prefix}pushpa_campaigns
            WHERE id = %d
            LIMIT 1
            ",
            absint($id)
        )
    );
}

}