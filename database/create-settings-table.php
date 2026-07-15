<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_create_settings_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_settings';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (

        id BIGINT(20) NOT NULL AUTO_INCREMENT,

        setting_key VARCHAR(100) NOT NULL,

        setting_value LONGTEXT NULL,

        PRIMARY KEY (id),

        UNIQUE KEY setting_key (setting_key)

    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}