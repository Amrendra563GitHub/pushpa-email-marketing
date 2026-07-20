<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'wp_ajax_pem_send_bulk_email',
    'pem_send_bulk_email_ajax'
);

function pem_send_bulk_email_ajax()
{
    check_ajax_referer(
        'pem_bulk_email_nonce',
        'nonce'
    );

    if (!current_user_can('manage_options')) {

        wp_send_json_error(
            'Permission denied.'
        );

    }

    $campaign_id = absint(
        $_POST['campaign_id'] ?? 0
    );

    $group = sanitize_text_field(
        $_POST['contact_group'] ?? 'All'
    );

    $offset = absint(
        $_POST['offset'] ?? 0
    );

    $batch = absint(
        $_POST['batch_size'] ?? 50
    );

    if (!$campaign_id) {

        wp_send_json_error(
            'Invalid Campaign.'
        );

    }

    $campaign = PEM_Campaign::get($campaign_id);

    $template = PEM_Template::get(
        $campaign->template_id
    );

    $contacts = PEM_Contact::getActiveByGroup(
        $group
    );

    $total = count($contacts);

    $sent = 0;

    $failed = 0;

    $slice = array_slice(
        $contacts,
        $offset,
        $batch
    );

    foreach ($slice as $contact) {

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

    $completed = $offset + count($slice);

    PEM_Queue::updateCampaign(
    $campaign_id,
    $total,
    $completed,
    $failed
);

if ($completed >= $total) {

    PEM_Queue::complete($campaign_id);

}

    wp_send_json_success(

    array(

        'processed' => count($slice),

        'sent'      => $sent,

        'failed'    => $failed,

        'completed' => $completed,

        'total'     => $total,

        'finished'  => ($completed >= $total)

    )

);
}