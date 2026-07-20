<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_create_campaigns_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_campaigns';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (

        id BIGINT(20) NOT NULL AUTO_INCREMENT,

        campaign_name VARCHAR(255) NOT NULL,

        subject VARCHAR(255) NOT NULL,

        template_id BIGINT(20) NOT NULL,
        
        recipient_type VARCHAR(30) DEFAULT 'all',

        send_type VARCHAR(20) DEFAULT 'now',

        schedule_date DATE NULL,

        schedule_time TIME NULL,

        scheduled_at DATETIME NULL,

        last_run DATETIME NULL,

        started_at DATETIME NULL,

paused_at DATETIME NULL,

completed_at DATETIME NULL,

current_email VARCHAR(255) NULL,

queue_status VARCHAR(20) DEFAULT 'Idle',

        total_contacts INT DEFAULT 0,

        sent_count INT DEFAULT 0,

        failed_count INT DEFAULT 0,

        status VARCHAR(30) DEFAULT 'Draft',

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (id),

        KEY status (status),

        KEY send_type (send_type),

        KEY scheduled_at (scheduled_at),
        
        KEY scheduler (status, send_type, scheduled_at)

    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);

    $columns = array(

    'started_at'    => "ALTER TABLE $table ADD started_at DATETIME NULL",

    'paused_at'     => "ALTER TABLE $table ADD paused_at DATETIME NULL",

    'completed_at'  => "ALTER TABLE $table ADD completed_at DATETIME NULL",

    'current_email' => "ALTER TABLE $table ADD current_email VARCHAR(255) NULL",

    'queue_status'  => "ALTER TABLE $table ADD queue_status VARCHAR(20) DEFAULT 'Idle'"

);

foreach ($columns as $column => $query) {

    $exists = $wpdb->get_results(
        "SHOW COLUMNS FROM $table LIKE '$column'"
    );

    if (empty($exists)) {

        $wpdb->query($query);

    }

}
}