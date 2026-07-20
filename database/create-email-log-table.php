<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_create_email_logs_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_email_logs';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (

        id BIGINT(20) NOT NULL AUTO_INCREMENT,

        campaign_id BIGINT(20) NOT NULL,

        contact_id BIGINT(20) NOT NULL,

        email VARCHAR(255) NOT NULL,

        subject VARCHAR(255) NOT NULL,

        status VARCHAR(50) NOT NULL DEFAULT 'Pending',

        sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        opened_at DATETIME NULL,

        open_count INT(11) NOT NULL DEFAULT 0,

        clicked_at DATETIME NULL,

click_count INT(11) NOT NULL DEFAULT 0,

is_unsubscribed TINYINT(1) NOT NULL DEFAULT 0,

bounce_reason TEXT NULL,

        PRIMARY KEY (id)

    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);

    // Add opened_at column if missing
    $opened_exists = $wpdb->get_results(
        "SHOW COLUMNS FROM $table LIKE 'opened_at'"
    );

    if (empty($opened_exists)) {

        $wpdb->query(
            "ALTER TABLE $table
            ADD opened_at DATETIME NULL
            AFTER sent_at"
        );

    }

    // Add open_count column if missing
    $count_exists = $wpdb->get_results(
        "SHOW COLUMNS FROM $table LIKE 'open_count'"
    );

    if (empty($count_exists)) {

        $wpdb->query(
            "ALTER TABLE $table
            ADD open_count INT(11) NOT NULL DEFAULT 0
            AFTER opened_at"
        );

    }

    // Add clicked_at column
$clicked_exists = $wpdb->get_results(
    "SHOW COLUMNS FROM $table LIKE 'clicked_at'"
);

if (empty($clicked_exists)) {

    $wpdb->query(
        "ALTER TABLE $table
        ADD clicked_at DATETIME NULL
        AFTER open_count"
    );

}

// Add click_count column
$click_count_exists = $wpdb->get_results(
    "SHOW COLUMNS FROM $table LIKE 'click_count'"
);

if (empty($click_count_exists)) {

    $wpdb->query(
        "ALTER TABLE $table
        ADD click_count INT(11) NOT NULL DEFAULT 0
        AFTER clicked_at"
    );

}

// Add unsubscribe column
$unsubscribe_exists = $wpdb->get_results(
    "SHOW COLUMNS FROM $table LIKE 'is_unsubscribed'"
);

if (empty($unsubscribe_exists)) {

    $wpdb->query(
        "ALTER TABLE $table
        ADD is_unsubscribed TINYINT(1) NOT NULL DEFAULT 0
        AFTER click_count"
    );

}

// Add bounce reason column
$bounce_exists = $wpdb->get_results(
    "SHOW COLUMNS FROM $table LIKE 'bounce_reason'"
);

if (empty($bounce_exists)) {

    $wpdb->query(
        "ALTER TABLE $table
        ADD bounce_reason TEXT NULL
        AFTER is_unsubscribed"
    );

}
}