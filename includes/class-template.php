<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Template
{
    /**
     * Table Name
     */
    private static function table()
    {
        global $wpdb;

        return $wpdb->prefix . 'pushpa_templates';
    }

    /**
     * Get All Templates
     */
    public static function get_all()
    {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * FROM " . self::table() . " ORDER BY id DESC"
        );
    }

    /**
     * Get Template By ID
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
     * Count Templates
     */
    public static function count()
    {
        global $wpdb;

        return (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::table()
        );
    }
}