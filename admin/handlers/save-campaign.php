<?php

if (!defined('ABSPATH')) {
    exit;
}

function pem_save_campaign()
{
    if (!isset($_POST['pem_save_campaign'])) {
        return;
    }

    // Security
    if (
        !isset($_POST['pem_campaign_nonce']) ||
        !wp_verify_nonce($_POST['pem_campaign_nonce'], 'pem_campaign_nonce')
    ) {
        wp_die('Security check failed.');
    }

    if (!current_user_can('manage_options')) {
        wp_die('Permission denied.');
    }

    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_campaigns';

    $campaign_id   = absint($_POST['campaign_id'] ?? 0);
    $campaign_name = sanitize_text_field($_POST['campaign_name'] ?? '');
    $subject       = sanitize_text_field($_POST['subject'] ?? '');
    $template_id   = absint($_POST['template_id'] ?? 0);

    $send_type     = sanitize_text_field($_POST['send_type'] ?? 'now');
    $recipient_type = sanitize_text_field($_POST['recipient_type'] ?? 'all');

    $schedule_date = sanitize_text_field($_POST['schedule_date'] ?? '');
    $schedule_time = sanitize_text_field($_POST['schedule_time'] ?? '');

    $scheduled_at = null;
    $status = 'Draft';

    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    if ($send_type === 'schedule') {

        if (empty($schedule_date) || empty($schedule_time)) {

            wp_safe_redirect(
                add_query_arg(
                    array(
                        'page'  => 'pushpa-add-campaign',
                        'error' => urlencode('Schedule date and time are required.')
                    ),
                    admin_url('admin.php')
                )
            );
            exit;
        }

        $scheduled_at = $schedule_date . ' ' . $schedule_time . ':00';
        $status = 'Scheduled';

    } else {

        $status = 'Draft';
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        empty($campaign_name) ||
        empty($subject) ||
        empty($template_id)
    ) {

        wp_safe_redirect(
            add_query_arg(
                array(
                    'page'  => 'pushpa-add-campaign',
                    'error' => urlencode('All fields are required.')
                ),
                admin_url('admin.php')
            )
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate Name
    |--------------------------------------------------------------------------
    */

    $exists = $wpdb->get_var(

        $wpdb->prepare(

            "SELECT id
            FROM {$table}
            WHERE campaign_name=%s
            AND id!=%d",

            $campaign_name,
            $campaign_id

        )

    );

    if ($exists) {

        wp_safe_redirect(

            add_query_arg(

                array(

                    'page'  => 'pushpa-add-campaign',

                    'error' => urlencode(
                        'Campaign name already exists.'
                    )

                ),

                admin_url('admin.php')

            )

        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Save Data
    |--------------------------------------------------------------------------
    */

    $data = array(

        'campaign_name' => $campaign_name,

        'subject'       => $subject,

        'template_id'   => $template_id,

        'send_type'     => $send_type,

        'recipient_type'=> $recipient_type,

        'schedule_date' => $schedule_date,

        'schedule_time' => $schedule_time,

        'scheduled_at'  => $scheduled_at,

        'status'        => $status

    );

    $format = array(

        '%s',
        '%s',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s'

    );

    if ($campaign_id > 0) {

        $result = $wpdb->update(

            $table,

            $data,

            array(
                'id' => $campaign_id
            ),

            $format,

            array('%d')

        );

    } else {

        $data['total_contacts'] = 0;
        $data['sent_count'] = 0;
        $data['failed_count'] = 0;

        $result = $wpdb->insert(

            $table,

            $data,

            array(
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d'
            )

        );

    }

    if ($result === false) {

        wp_safe_redirect(

            add_query_arg(

                array(

                    'page'  => 'pushpa-add-campaign',

                    'error' => urlencode(
                        'Database Error : ' . $wpdb->last_error
                    )

                ),

                admin_url('admin.php')

            )

        );

        exit;
    }

    /*
|--------------------------------------------------------------------------
| Send Immediately
|--------------------------------------------------------------------------
*/

// if (
//     $result !== false &&
//     $send_type === 'now'
// ) {

//     $campaign = PEM_Campaign::get(
//         $campaign_id ?: $wpdb->insert_id
//     );

//     if ($campaign) {

//         PEM_Queue::processCampaign(
//             $campaign
//         );

//     }

// }

    wp_safe_redirect(

        admin_url(
            'admin.php?page=pushpa-campaigns&message=saved'
        )

    );

    exit;
}

add_action(
    'admin_init',
    'pem_save_campaign'
);