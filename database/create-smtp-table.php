<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_create_smtp_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_smtp';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (

        id BIGINT(20) NOT NULL AUTO_INCREMENT,

        smtp_host VARCHAR(255) NOT NULL,

        smtp_port INT NOT NULL,

        smtp_encryption VARCHAR(20) DEFAULT 'tls',

        smtp_username VARCHAR(255) NOT NULL,

        smtp_password TEXT NOT NULL,

        from_email VARCHAR(255) NOT NULL,

        from_name VARCHAR(255) NOT NULL,

        reply_to VARCHAR(255) NULL,

        status VARCHAR(20) DEFAULT 'Active',

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY(id)

    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}