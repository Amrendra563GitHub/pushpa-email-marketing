<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_send_bulk_email()
{
    if (!isset($_POST['pem_send_bulk_email'])) {
        return;
    }

    // Security
    if (
        !isset($_POST['pem_bulk_email_nonce']) ||
        !wp_verify_nonce(
            $_POST['pem_bulk_email_nonce'],
            'pem_bulk_email_nonce'
        )
    ) {
        wp_die('Security check failed.');
    }

    // Permission
    if (!current_user_can('manage_options')) {
        wp_die('Permission denied.');
    }

    $campaign_id = absint($_POST['campaign_id']);

    $group = sanitize_text_field(
        $_POST['contact_group'] ?? 'All'
    );

    if (!$campaign_id) {

        wp_safe_redirect(
            admin_url(
                'admin.php?page=pushpa-bulk-email&error=campaign'
            )
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Campaign
    |--------------------------------------------------------------------------
    */

    $campaign = PEM_Campaign::get($campaign_id);

    if (!$campaign) {

        wp_safe_redirect(
            admin_url(
                'admin.php?page=pushpa-bulk-email&error=campaign'
            )
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Template
    |--------------------------------------------------------------------------
    */

    $template = PEM_Template::get(
        $campaign->template_id
    );

    if (!$template) {

        wp_safe_redirect(
            admin_url(
                'admin.php?page=pushpa-bulk-email&error=template'
            )
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Running
    |--------------------------------------------------------------------------
    */

    PEM_Campaign::updateStatus(
        $campaign_id,
        'Running'
    );

    /*
    |--------------------------------------------------------------------------
    | Get Contacts By Group
    |--------------------------------------------------------------------------
    */

    $contacts = PEM_Contact::getActiveByGroup(
        $group
    );

    $total  = count($contacts);

    $sent   = 0;

    $failed = 0;

    /*
    |--------------------------------------------------------------------------
    | Send Emails
    |--------------------------------------------------------------------------
    */

    foreach ($contacts as $contact) {

        if (!is_email($contact->email)) {

            $failed++;

            continue;
        }

        $result = PEM_Queue::send(
            $campaign,
            $template,
            $contact
        );

        if ($result) {

            $sent++;

        } else {

            $failed++;

        }

        PEM_Queue::updateCampaign(
            $campaign_id,
            $total,
            $sent,
            $failed
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Completed
    |--------------------------------------------------------------------------
    */

    PEM_Queue::complete(
        $campaign_id
    );

    wp_safe_redirect(

        admin_url(

            'admin.php?page=pushpa-bulk-email'
            . '&sent=' . $sent
            . '&failed=' . $failed

        )

    );

    exit;
}

add_action(
    'admin_init',
    'pem_send_bulk_email'
);