<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_export_contacts()
{
    if (
        !isset($_GET['action']) ||
        $_GET['action'] !== 'export'
    ) {
        return;
    }

    if (
        !isset($_GET['_wpnonce']) ||
        !wp_verify_nonce(
            $_GET['_wpnonce'],
            'pem_export_contacts'
        )
    ) {
        wp_die('Security check failed.');
    }

    if (!current_user_can('manage_options')) {
        wp_die('Permission denied.');
    }

    global $wpdb;

    $contacts = $wpdb->get_results(
        "SELECT
            name,
            email,
            phone,
            company,
            contact_group,
            status,
            created_at
        FROM {$wpdb->prefix}pushpa_contacts
        ORDER BY id DESC"
    );

    header('Content-Type: text/csv; charset=UTF-8');
    header(
        'Content-Disposition: attachment; filename=pushpa-contacts-' .
        date('Y-m-d') .
        '.csv'
    );

    $output = fopen('php://output', 'w');

    // UTF-8 BOM
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, array(
        'Name',
        'Email',
        'Phone',
        'Company',
        'Group',
        'Status',
        'Created At'
    ));

    foreach ($contacts as $contact) {

        fputcsv($output, array(
            $contact->name,
            $contact->email,
            $contact->phone,
            $contact->company,
            $contact->contact_group,
            $contact->status,
            $contact->created_at
        ));
    }

    fclose($output);

    exit;
}

add_action(
    'admin_init',
    'pem_export_contacts'
);