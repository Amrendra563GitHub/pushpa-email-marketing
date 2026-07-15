<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_delete_template()
{
    if (!isset($_GET['delete'])) {
        return;
    }

    if (!isset($_GET['page']) || $_GET['page'] !== 'pushpa-templates') {
        return;
    }

    $id = intval($_GET['delete']);

    if (
        !isset($_GET['_wpnonce']) ||
        !wp_verify_nonce($_GET['_wpnonce'], 'pem_delete_template_' . $id)
    ) {
        wp_die('Security check failed.');
    }

    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_templates';

    $wpdb->delete(
        $table,
        array(
            'id' => $id
        ),
        array('%d')
    );
    set_transient('pem_template_deleted', true, 30);

    wp_redirect(
        admin_url('admin.php?page=pushpa-templates&deleted=1')
    );
    exit;
}

add_action('admin_init', 'pem_delete_template');