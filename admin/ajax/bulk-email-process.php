<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_pem_bulk_email_process', 'pem_bulk_email_process');

function pem_bulk_email_process()
{
    check_ajax_referer('pem_bulk_email_nonce', 'security');

    if (!current_user_can('manage_options')) {

        wp_send_json_error(array(
            'message' => 'Permission denied.'
        ));

    }

    $campaign_id = absint($_POST['campaign_id'] ?? 0);
    $offset      = absint($_POST['offset'] ?? 0);
    $limit       = absint($_POST['limit'] ?? 25);

    if (!$campaign_id) {

        wp_send_json_error(array(
            'message' => 'Invalid Campaign.'
        ));

    }

    $campaign = PEM_Campaign::get($campaign_id);

    if (!$campaign) {

        wp_send_json_error(array(
            'message' => 'Campaign not found.'
        ));

    }

    $template = PEM_Template::get($campaign->template_id);

    if (!$template) {

        wp_send_json_error(array(
            'message' => 'Template not found.'
        ));

    }

    global $wpdb;

    $contacts = $wpdb->get_results(

        $wpdb->prepare(

            "SELECT *
            FROM {$wpdb->prefix}pushpa_contacts
            WHERE status='Active'
            LIMIT %d OFFSET %d",

            $limit,
            $offset

        )

    );

    $processed = 0;
    $sent      = 0;
    $failed    = 0;

    foreach ($contacts as $contact) {

        $processed++;

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

    }

    wp_send_json_success(array(

        'processed' => $processed,

        'sent'      => $sent,

        'failed'    => $failed

    ));
}