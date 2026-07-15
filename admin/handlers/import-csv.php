<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_import_csv_handler()
{
    if (!isset($_POST['pem_import_csv'])) {
        return;
    }

    if (
        !isset($_POST['pem_import_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['pem_import_nonce'])),
            'pem_import_csv'
        )
    ) {
        wp_die('Security check failed.');
    }

    if (!current_user_can('manage_options')) {
        wp_die('You are not allowed to import contacts.');
    }

    if (
        empty($_FILES['csv_file']) ||
        empty($_FILES['csv_file']['tmp_name'])
    ) {
        wp_safe_redirect(
            admin_url(
                'admin.php?page=pushpa-import-csv&error=nofile'
            )
        );
        exit;
    }

    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');

    if (!$file) {

        wp_safe_redirect(
            admin_url(
                'admin.php?page=pushpa-import-csv&error=file'
            )
        );

        exit;
    }

    // Skip CSV Header
    fgetcsv($file);

    $imported = 0;
    $skipped = 0;

    while (($row = fgetcsv($file)) !== false) {

        $data = array(
            'name'    => sanitize_text_field($row[0] ?? ''),
            'email'   => sanitize_email($row[1] ?? ''),
            'phone'   => sanitize_text_field($row[2] ?? ''),
            'company' => sanitize_text_field($row[3] ?? '')
        );

        if (
            empty($data['name']) ||
            empty($data['email']) ||
            !is_email($data['email'])
        ) {
            $skipped++;
            continue;
        }

        $result = PEM_Contact::create($data);

        if (is_wp_error($result)) {
            $skipped++;
            continue;
        }

        $imported++;
    }

    fclose($file);

    wp_safe_redirect(

        add_query_arg(

            array(
                'page' => 'pushpa-import-csv',
                'imported' => $imported,
                'skipped' => $skipped
            ),

            admin_url('admin.php')

        )

    );

    exit;
}

add_action('admin_init', 'pem_import_csv_handler');