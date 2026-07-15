<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_create_email_logs_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_email_logs';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (

        id BIGINT(20) NOT NULL AUTO_INCREMENT,

        campaign_id BIGINT(20) NOT NULL,

        contact_id BIGINT(20) NOT NULL,

        email VARCHAR(255) NOT NULL,

        subject VARCHAR(255) NOT NULL,

        status VARCHAR(50) NOT NULL DEFAULT 'Pending',

        sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (id)

    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}