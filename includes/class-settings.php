<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Settings
{
    /**
     * Table Name
     */
    private static function table()
    {
        global $wpdb;

        return $wpdb->prefix . 'pushpa_settings';
    }

    /**
     * Get Setting
     */
    public static function get($key, $default = '')
    {
        global $wpdb;

        $value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT setting_value
                 FROM " . self::table() . "
                 WHERE setting_key = %s",
                $key
            )
        );

        if ($value === null) {
            return $default;
        }

        return $value;
    }

    /**
     * Save Setting
     */
    public static function set($key, $value)
    {
        global $wpdb;

        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id
                 FROM " . self::table() . "
                 WHERE setting_key = %s",
                $key
            )
        );

        if ($exists) {

            return $wpdb->update(
                self::table(),
                array(
                    'setting_value' => $value
                ),
                array(
                    'setting_key' => $key
                ),
                array('%s'),
                array('%s')
            );

        }

        return $wpdb->insert(
            self::table(),
            array(
                'setting_key'   => $key,
                'setting_value' => $value
            ),
            array(
                '%s',
                '%s'
            )
        );
    }

    /**
     * Get All Settings
     */
    public static function all()
    {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * FROM " . self::table() . " ORDER BY setting_key ASC"
        );
    }
}