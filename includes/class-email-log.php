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
    $status = 'Pending'
) {
    global $wpdb;

    $wpdb->insert(
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
    error_log('INSERT ID = ' . $wpdb->insert_id);
error_log('LAST ERROR = ' . $wpdb->last_error);

    return (int) $wpdb->insert_id;
}

    /**
     * Mark Email Opened
     */
    public static function markOpened($id)
{
    global $wpdb;

    $id = absint($id);

    if ($id <= 0) {
        return false;
    }

    $log = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM " . self::table() . " WHERE id = %d",
            $id
        )
    );

    if (!$log) {

        error_log('PEM: Email log not found. ID = ' . $id);

        return false;
    }

    $result = $wpdb->update(
        self::table(),
        array(
            'opened_at' => current_time('mysql'),
            'open_count' => ((int) $log->open_count + 1)
        ),
        array(
            'id' => $id
        ),
        array(
            '%s',
            '%d'
        ),
        array(
            '%d'
        )
    );

    if ($result === false) {

        error_log('PEM Open Tracking Error: ' . $wpdb->last_error);

        return false;
    }

    error_log('PEM Open Tracking Success. Log ID = ' . $id);

    return true;
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

    /**
 * Mark Email Clicked
 */
public static function markClicked($id)
{
    global $wpdb;

    error_log('markClicked() CALLED. ID = ' . $id);

    $log = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM " . self::table() . " WHERE id=%d",
            absint($id)
        )
    );

    if (!$log) {
        error_log('LOG NOT FOUND');
        return;
    }

    $result = $wpdb->update(
        self::table(),
        array(
            'clicked_at' => current_time('mysql'),
            'click_count' => ((int)$log->click_count + 1)
        ),
        array(
            'id' => absint($id)
        ),
        array('%s', '%d'),
        array('%d')
    );

    error_log('UPDATE RESULT = ' . print_r($result, true));
    error_log('LAST ERROR = ' . $wpdb->last_error);
}
/**
 * Unsubscribe Email
 */
public static function unsubscribe($id)
{
    global $wpdb;

    return $wpdb->update(
        self::table(),
        array(
            'is_unsubscribed' => 1
        ),
        array(
            'id' => absint($id)
        ),
        array('%d'),
        array('%d')
    );
}

/**
 * Get Open Rate
 */
public static function getOpenRate($campaign_id)
{
    global $wpdb;

    $total = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*)
            FROM " . self::table() . "
            WHERE campaign_id=%d",
            absint($campaign_id)
        )
    );

    if ($total == 0) {
        return 0;
    }

    $opened = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*)
            FROM " . self::table() . "
            WHERE campaign_id=%d
            AND open_count>0",
            absint($campaign_id)
        )
    );

    return round(($opened / $total) * 100, 2);
}

/**
 * Get Click Rate
 */
public static function getClickRate($campaign_id)
{
    global $wpdb;

    $total = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*)
            FROM " . self::table() . "
            WHERE campaign_id=%d",
            absint($campaign_id)
        )
    );

    if ($total == 0) {
        return 0;
    }

    $clicked = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*)
            FROM " . self::table() . "
            WHERE campaign_id=%d
            AND click_count>0",
            absint($campaign_id)
        )
    );

    return round(($clicked / $total) * 100, 2);
}

/**
 * Total Clicked Emails
 */
public static function totalClicked()
{
    global $wpdb;

    return (int) $wpdb->get_var(
        "SELECT COUNT(*)
        FROM " . self::table() . "
        WHERE click_count > 0"
    );
}

/**
 * Total Unsubscribed
 */
public static function totalUnsubscribed()
{
    global $wpdb;

    return (int) $wpdb->get_var(
        "SELECT COUNT(*)
        FROM " . self::table() . "
        WHERE is_unsubscribed = 1"
    );
}
}