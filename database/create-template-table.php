<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_create_templates_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_templates';

    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (

        id BIGINT(20) NOT NULL AUTO_INCREMENT,

        template_name VARCHAR(255) NOT NULL,

        subject VARCHAR(255) NOT NULL,

        email_body LONGTEXT NOT NULL,

        status VARCHAR(20) DEFAULT 'Active',

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY(id)

    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}