<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_save_settings()
{
    if (!isset($_POST['pem_save_settings'])) {
        return;
    }

    if (
        !isset($_POST['pem_settings_nonce']) ||
        !wp_verify_nonce($_POST['pem_settings_nonce'], 'pem_save_settings')
    ) {
        wp_die('Security check failed.');
    }

    if (!current_user_can('manage_options')) {
        wp_die('Permission denied.');
    }

    PEM_Settings::set(
        'company_name',
        sanitize_text_field($_POST['company_name'])
    );

    PEM_Settings::set(
        'sender_name',
        sanitize_text_field($_POST['sender_name'])
    );

    PEM_Settings::set(
        'sender_email',
        sanitize_email($_POST['sender_email'])
    );

    PEM_Settings::set(
        'reply_to_email',
        sanitize_email($_POST['reply_to_email'])
    );

    echo '<div class="notice notice-success is-dismissible">
            <p>Settings Saved Successfully.</p>
          </div>';
}

add_action('admin_init', 'pem_save_settings');