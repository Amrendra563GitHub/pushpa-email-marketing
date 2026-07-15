<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_save_smtp_settings()
{
    error_log('SMTP SAVE FUNCTION CALLED');
    if (!isset($_POST['pem_save_smtp'])) {
        return;
    }

    if (
        !isset($_POST['pem_smtp_nonce']) ||
        !wp_verify_nonce($_POST['pem_smtp_nonce'], 'pem_save_smtp')
    ) {
        wp_die('Security Check Failed');
    }

    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_smtp';

    $data = array(
        'smtp_host'       => sanitize_text_field($_POST['smtp_host']),
        'smtp_port'       => intval($_POST['smtp_port']),
        'smtp_encryption' => sanitize_text_field($_POST['smtp_encryption']),
        'smtp_username'   => sanitize_text_field($_POST['smtp_username']),
        'smtp_password'   => sanitize_text_field($_POST['smtp_password']),
        'from_email'      => sanitize_email($_POST['from_email']),
        'from_name'       => sanitize_text_field($_POST['from_name']),
        'reply_to'        => sanitize_email($_POST['from_email']),
        'status'          => 'Active'
    );

    $exists = $wpdb->get_var("SELECT id FROM $table LIMIT 1");

    if ($exists) {

        $wpdb->update(
            $table,
            $data,
            array('id' => $exists)
        );

    } else {

        $wpdb->insert(
            $table,
            $data
        );
    }

    wp_safe_redirect(
    admin_url('admin.php?page=pushpa-smtp&saved=1')
);

    exit;
}

add_action('admin_init', 'pem_save_smtp_settings');