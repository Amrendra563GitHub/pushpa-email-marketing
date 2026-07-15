<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Campaign
{
    /**
     * Table Name
     */
    private static function table()
    {
        global $wpdb;

        return $wpdb->prefix . 'pushpa_campaigns';
    }

    /**
     * Get All Campaigns
     */
    public static function get_all()
    {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * FROM " . self::table() . " ORDER BY id DESC"
        );
    }

    /**
     * Get Campaign By ID
     */
    public static function get($id)
    {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . self::table() . " WHERE id = %d",
                absint($id)
            )
        );
    }

    /**
     * Update Campaign Status
     */
    public static function updateStatus($id, $status)
    {
        global $wpdb;

        return $wpdb->update(
            self::table(),
            array(
                'status' => sanitize_text_field($status)
            ),
            array(
                'id' => absint($id)
            ),
            array('%s'),
            array('%d')
        );
    }

    /**
     * Update Campaign Statistics
     */
    public static function updateCounts($id, $total, $sent, $failed)
    {
        global $wpdb;

        return $wpdb->update(
            self::table(),
            array(
                'total_contacts' => absint($total),
                'sent_count'     => absint($sent),
                'failed_count'   => absint($failed)
            ),
            array(
                'id' => absint($id)
            ),
            array(
                '%d',
                '%d',
                '%d'
            ),
            array('%d')
        );
    }
    /**
 * Get Campaign Preview
 */
public static function getPreview($id)
{
    global $wpdb;

    return $wpdb->get_row(
        $wpdb->prepare(
            "SELECT
                c.*,
                t.template_name,
                t.subject AS template_subject
            FROM " . self::table() . " c
            LEFT JOIN {$wpdb->prefix}pushpa_templates t
                ON c.template_id = t.id
            WHERE c.id = %d",
            absint($id)
        )
    );
}
}