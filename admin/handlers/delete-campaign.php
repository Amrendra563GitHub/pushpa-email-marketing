<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_delete_campaign()
{
    if (!current_user_can('manage_options')) {
        wp_die('Permission denied.');
    }

    if (!isset($_GET['id'])) {
        wp_die('Campaign ID missing.');
    }

    $id = intval($_GET['id']);

    if (
        !isset($_GET['_wpnonce']) ||
        !wp_verify_nonce($_GET['_wpnonce'], 'pem_delete_campaign_' . $id)
    ) {
        wp_die('Security check failed.');
    }

    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_campaigns';

    $wpdb->delete(
        $table,
        array(
            'id' => $id
        ),
        array('%d')
    );

    wp_safe_redirect(
        admin_url('admin.php?page=pushpa-campaigns&deleted=1')
    );
    exit;
}

add_action('admin_post_pem_delete_campaign', 'pem_delete_campaign');