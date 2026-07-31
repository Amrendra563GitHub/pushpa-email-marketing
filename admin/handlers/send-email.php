<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_send_test_email()
{
    if (!isset($_POST['pem_send_test_email'])) {
        return;
    }

    if (
        !isset($_POST['pem_send_email_nonce']) ||
        !wp_verify_nonce(
            $_POST['pem_send_email_nonce'],
            'pem_send_email_nonce'
        )
    ) {
        wp_die('Security check failed.');
    }

    $campaign_id = intval($_POST['campaign_id']);
    $email       = sanitize_email($_POST['test_email']);

    /*
    |--------------------------------------------------------------------------
    | Get Campaign
    |--------------------------------------------------------------------------
    */

    $campaign = PEM_Campaign::get($campaign_id);

    if (!$campaign) {

        echo '<div class="notice notice-error">
                <p>Campaign not found.</p>
              </div>';

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Template
    |--------------------------------------------------------------------------
    */

    $template = PEM_Template::get($campaign->template_id);

    if (!$template) {

        echo '<div class="notice notice-error">
                <p>Template not found.</p>
              </div>';

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Send Email
    |--------------------------------------------------------------------------
    */

// $contact = new stdClass();

// $contact->name = 'Amrendra';
// $contact->email = $email;
// $contact->phone = '';
// $contact->company = '';

// // echo "<pre>";
// // echo $template->email_body;
// // echo "</pre>";
// // exit;

// $sent = PEM_Mailer::send(
//     $email,
//     $campaign->subject,
//     $template->email_body,
//     $contact
// );

//     /*
//     |--------------------------------------------------------------------------
//     | Save Email Log
//     |--------------------------------------------------------------------------
//     */

//     $status = $sent ? 'Success' : 'Failed';

//     $log_saved = PEM_Email_Log::save(
//         $campaign->id,
//         0,
//         $email,
//         $campaign->subject,
//         $status
//     );


/*
|--------------------------------------------------------------------------
| Send Email
|--------------------------------------------------------------------------
*/

$contact = new stdClass();

$contact->name = 'Amrendra';
$contact->email = $email;
$contact->phone = '';
$contact->company = '';

/*
|--------------------------------------------------------------------------
| Create Email Log First
|--------------------------------------------------------------------------
*/

$log_id = PEM_Email_Log::save(
    $campaign->id,
    0,
    $email,
    $campaign->subject,
    'Pending'
);

/*
|--------------------------------------------------------------------------
| Send Email
|--------------------------------------------------------------------------
*/

$sent = PEM_Mailer::send(
    $email,
    $campaign->subject,
    $template->email_body,
    $contact,
    $log_id
);

/*
|--------------------------------------------------------------------------
| Update Email Log Status
|--------------------------------------------------------------------------
*/

global $wpdb;

$wpdb->update(
    $wpdb->prefix . 'pushpa_email_logs',
    array(
        'status' => $sent ? 'Success' : 'Failed'
    ),
    array(
        'id' => $log_id
    ),
    array('%s'),
    array('%d')
);

    /*
    |--------------------------------------------------------------------------
    | Success Message
    |--------------------------------------------------------------------------
    */

    if ($sent) {

        echo '<div class="notice notice-success is-dismissible">
                <p>Test Email Sent Successfully.</p>
              </div>';

    } else {

        echo '<div class="notice notice-error">
                <p>Email Sending Failed.</p>
              </div>';
    }

    /*
    |--------------------------------------------------------------------------
    | Debug (Temporary)
    |--------------------------------------------------------------------------
    */

    // if (!$log_saved) {

    //     global $wpdb;

    //     echo '<div class="notice notice-warning">
    //             <p><strong>Email Log Error:</strong> ' .
    //             esc_html($wpdb->last_error) .
    //             '</p>
    //           </div>';
    // }
}

add_action('admin_init', 'pem_send_test_email');