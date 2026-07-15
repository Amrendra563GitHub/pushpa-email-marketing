<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_delete_contact()
{
    if (
        !isset($_GET['action']) ||
        $_GET['action'] !== 'delete'
    ) {
        return;
    }

    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to perform this action.');
    }

    $id = isset($_GET['id']) ? absint($_GET['id']) : 0;

    if (!$id) {
        return;
    }

    if (
        !isset($_GET['_wpnonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_GET['_wpnonce'])),
            'pem_delete_contact_' . $id
        )
    ) {
        wp_die('Security check failed.');
    }

    $contact = PEM_Contact::getById($id);

    if (!$contact) {

        wp_safe_redirect(
            admin_url(
                'admin.php?page=pushpa-contacts&error=notfound'
            )
        );

        exit;
    }

    $deleted = PEM_Contact::delete($id);

    if ($deleted === false) {

        wp_safe_redirect(
            admin_url(
                'admin.php?page=pushpa-contacts&error=deletefailed'
            )
        );

        exit;
    }

    wp_safe_redirect(
        admin_url(
            'admin.php?page=pushpa-contacts&deleted=1'
        )
    );

    exit;
}

add_action('admin_init', 'pem_delete_contact');