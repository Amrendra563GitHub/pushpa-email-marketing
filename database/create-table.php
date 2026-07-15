<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_create_contacts_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'pushpa_contacts';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (

        id BIGINT(20) NOT NULL AUTO_INCREMENT,

        name VARCHAR(255) NOT NULL,

        email VARCHAR(255) NOT NULL,

        phone VARCHAR(30) DEFAULT '',

        company VARCHAR(255) DEFAULT '',

        contact_group VARCHAR(100) DEFAULT 'General',

status VARCHAR(20) DEFAULT 'Active',

created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
ON UPDATE CURRENT_TIMESTAMP,

PRIMARY KEY (id),

KEY email (email),

KEY status (status),

KEY contact_group (contact_group)

    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}