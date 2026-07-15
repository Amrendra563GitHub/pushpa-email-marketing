<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_save_contact()
{
    if (!isset($_POST['pem_save_contact'])) {
        return;
    }

    // Security Check
    if (
        !isset($_POST['pem_nonce']) ||
        !wp_verify_nonce($_POST['pem_nonce'], 'pem_save_contact_nonce')
    ) {
        wp_die('Security check failed.');
    }

    // Permission Check
    if (!current_user_can('manage_options')) {
        wp_die('You are not allowed to perform this action.');
    }

    $contact_id = isset($_POST['contact_id'])
        ? absint($_POST['contact_id'])
        : 0;

    $data = array(

        'name'       => sanitize_text_field($_POST['name'] ?? ''),

        'email'      => sanitize_email($_POST['email'] ?? ''),

        'phone'      => sanitize_text_field($_POST['phone'] ?? ''),

        'company'    => sanitize_text_field($_POST['company'] ?? ''),

        'contact_group' => sanitize_text_field($_POST['contact_group'] ?? 'General'),

        'status'     => sanitize_text_field($_POST['status'] ?? 'Active')

    );

    if ($contact_id > 0) {

        $result = PEM_Contact::update(
            $contact_id,
            $data
        );

    } else {

        $result = PEM_Contact::create(
            $data
        );
    }

    if (is_wp_error($result)) {

        wp_safe_redirect(

            add_query_arg(

                array(

                    'page'  => 'pushpa-add-contact',

                    'error' => urlencode(
                        $result->get_error_message()
                    )

                ),

                admin_url('admin.php')

            )

        );

        exit;

    }

    wp_safe_redirect(

        add_query_arg(

            array(

                'page'    => 'pushpa-contacts',

                'message' => $contact_id > 0
                    ? 'updated'
                    : 'saved'

            ),

            admin_url('admin.php')

        )

    );

    exit;
}

add_action(
    'admin_init',
    'pem_save_contact'
);