<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_test_smtp()
{
    if (!isset($_POST['pem_test_smtp'])) {
        return;
    }

    if (
        !isset($_POST['pem_test_smtp_nonce']) ||
        !wp_verify_nonce(
            $_POST['pem_test_smtp_nonce'],
            'pem_test_smtp'
        )
    ) {
        wp_die('Security check failed.');
    }

    if (!current_user_can('manage_options')) {
        wp_die('Permission denied.');
    }

    $email = sanitize_email(
        $_POST['test_email']
    );

    if (!is_email($email)) {

        wp_safe_redirect(

            admin_url(
                'admin.php?page=pushpa-smtp&test=invalid'
            )

        );

        exit;
    }

    $subject = 'SMTP Test Email';

    $message = '
        <h2>Pushpa Email Marketing</h2>

        <p>Your SMTP configuration is working successfully.</p>

        <p>This is a test email.</p>
    ';

    $result = wp_mail(
        $email,
        $subject,
        $message
    );

    wp_safe_redirect(

        admin_url(

            'admin.php?page=pushpa-smtp&test=' .

            ($result ? 'success' : 'failed')

        )

    );

    exit;
}

add_action(
    'admin_init',
    'pem_test_smtp'
);