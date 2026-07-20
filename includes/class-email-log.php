<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Email_Log
{
    /**
     * Get Table Name
     */
    private static function table()
    {
        global $wpdb;

        return $wpdb->prefix . 'pushpa_email_logs';
    }

    /**
     * Save Email Log
     */
    public static function save(
        $campaign_id,
        $contact_id,
        $email,
        $subject,
        $status
    ) {
        global $wpdb;

        return $wpdb->insert(
            self::table(),
            array(
                'campaign_id' => absint($campaign_id),
                'contact_id'  => absint($contact_id),
                'email'       => sanitize_email($email),
                'subject'     => sanitize_text_field($subject),
                'status'      => sanitize_text_field($status),
                'sent_at'     => current_time('mysql'),
                'opened_at'   => null,
                'open_count'  => 0
            ),
            array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d'
            )
        );
    }

    /**
     * Mark Email Opened
     */
    public static function markOpened($id)
    {
        global $wpdb;

        $log = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . self::table() . " WHERE id=%d",
                absint($id)
            )
        );

        if (!$log) {
            return;
        }

        $wpdb->update(
            self::table(),
            array(
                'opened_at' => current_time('mysql'),
                'open_count' => ((int)$log->open_count + 1)
            ),
            array(
                'id' => absint($id)
            ),
            array(
                '%s',
                '%d'
            ),
            array(
                '%d'
            )
        );
    }

    /**
     * Total Opens
     */
    public static function totalOpened()
    {
        global $wpdb;

        return (int)$wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::table() . "
            WHERE open_count > 0"
        );
    }

    /**
     * Get All Logs
     */
    public static function getAll()
    {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * FROM " . self::table() . " ORDER BY id DESC"
        );
    }

    /**
     * Get Logs By Campaign
     */
    public static function getByCampaign($campaign_id)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . self::table() . "
                WHERE campaign_id=%d
                ORDER BY id DESC",
                absint($campaign_id)
            )
        );
    }

    /**
     * Total Email Logs
     */
    public static function count()
    {
        global $wpdb;

        return (int)$wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::table()
        );
    }
}