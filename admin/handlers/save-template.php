<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_save_template()
{
    if (!isset($_POST['pem_save_template'])) {
        return;
    }

    if (
        !isset($_POST['pem_template_nonce']) ||
        !wp_verify_nonce($_POST['pem_template_nonce'], 'pem_template_nonce')
    ) {
        wp_die('Security check failed.');
    }

    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_templates';

    $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : 0;

    $template_name = sanitize_text_field($_POST['template_name']);
    $subject       = sanitize_text_field($_POST['subject']);
    $email_body = wp_kses_post(
    wp_unslash($_POST['email_body'])
);

    if (empty($template_name) || empty($subject)) {

        echo '<div class="notice notice-error"><p>Template Name and Subject are required.</p></div>';
        return;
    }

    // UPDATE
    if ($template_id > 0) {

        $wpdb->update(
            $table,
            array(
                'template_name' => $template_name,
                'subject'       => $subject,
                'email_body'    => $email_body
            ),
            array(
                'id' => $template_id
            ),
            array(
                '%s',
                '%s',
                '%s'
            ),
            array('%d')
        );

        echo '<div class="notice notice-success">
                <p>Template Updated Successfully.</p>
              </div>';

    } else {

        // INSERT
        $wpdb->insert(
            $table,
            array(
                'template_name' => $template_name,
                'subject'       => $subject,
                'email_body'    => $email_body,
                'status'        => 'Active'
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );

        echo '<div class="notice notice-success">
                <p>Template Saved Successfully.</p>
              </div>';
    }
}

add_action('admin_init', 'pem_save_template');