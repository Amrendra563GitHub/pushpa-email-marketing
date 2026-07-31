<?php
/*
Plugin Name: Pushpa Email Marketing
Description: Email Marketing Plugin by Pushpa Tech
Version: 1.0.0
Author: Amrendra Kumar
*/

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Plugin Constants
|--------------------------------------------------------------------------
*/

define('PEM_PATH', plugin_dir_path(__FILE__));
define('PEM_URL', plugin_dir_url(__FILE__));
define('PEM_VERSION', '1.0.0');
/*
|--------------------------------------------------------------------------
| Include Files
|--------------------------------------------------------------------------
*/

require_once PEM_PATH . 'includes/class-contact.php';
require_once PEM_PATH . 'includes/class-template.php';
require_once PEM_PATH . 'includes/class-campaign.php';
require_once PEM_PATH . 'includes/class-dashboard.php';
require_once PEM_PATH . 'includes/class-report.php';
require_once PEM_PATH . 'includes/class-database.php';
require_once PEM_PATH . 'includes/class-helper.php';
require_once PEM_PATH . 'includes/class-settings.php';
require_once PEM_PATH . 'includes/class-mailer.php';
require_once PEM_PATH . 'includes/class-queue.php';
require_once PEM_PATH . 'includes/class-scheduler.php';
require_once PEM_PATH . 'includes/class-email-log.php';
require_once PEM_PATH . 'includes/class-smtp.php';


require_once PEM_PATH . 'admin/admin-menu.php';

// require_once PEM_PATH . 'admin/pages/contacts-page.php';
// require_once PEM_PATH . 'admin/pages/add-contact.php';
// require_once PEM_PATH . 'admin/pages/import-csv.php';
// require_once PEM_PATH . 'admin/pages/templates.php';
// require_once PEM_PATH . 'admin/pages/add-template.php';
// require_once PEM_PATH . 'admin/pages/campaigns.php';
// require_once PEM_PATH . 'admin/pages/add-campaign.php';
// require_once PEM_PATH . 'admin/pages/send-email.php';
// require_once PEM_PATH . 'admin/pages/email-logs.php';
// require_once PEM_PATH . 'admin/pages/bulk-email.php';



require_once PEM_PATH . 'admin/handlers/delete-template.php';

require_once PEM_PATH . 'admin/handlers/save-contact.php';
require_once PEM_PATH . 'admin/handlers/delete-contact.php';
require_once PEM_PATH . 'admin/handlers/import-csv.php';
require_once PEM_PATH . 'admin/handlers/save-template.php';
require_once PEM_PATH . 'admin/handlers/save-campaign.php';
require_once PEM_PATH . 'admin/handlers/delete-campaign.php';
require_once PEM_PATH . 'admin/handlers/send-email.php';
require_once PEM_PATH . 'admin/handlers/send-bulk-email.php';
require_once PEM_PATH . 'admin/handlers/export-contacts.php';
require_once PEM_PATH . 'admin/handlers/test-smtp.php';
// require_once PEM_PATH . 'admin/handlers/open-tracking.php';



// require_once PEM_PATH . 'admin/pages/smtp-settings.php';
// require_once PEM_PATH . 'admin/pages/settings.php';
require_once PEM_PATH . 'admin/handlers/save-settings.php';
require_once PEM_PATH . 'admin/handlers/save-smtp.php';
require_once PEM_PATH . 'admin/ajax/bulk-email-process.php';
require_once PEM_PATH . 'admin/ajax/send-bulk-email.php';

require_once PEM_PATH . 'database/create-table.php';
require_once PEM_PATH . 'database/create-template-table.php';
require_once PEM_PATH . 'database/create-campaign-table.php';
require_once PEM_PATH . 'database/create-email-log-table.php';
require_once PEM_PATH . 'database/create-settings-table.php';
require_once PEM_PATH . 'database/create-smtp-table.php';

// register_activation_hook(__FILE__, function () {

//     pem_create_contacts_table();

//     pem_create_templates_table();

//     pem_create_campaigns_table();

//     pem_create_email_logs_table();

//     pem_create_settings_table();

//     pem_create_smtp_table();

//     PEM_Scheduler::activate();

// });

register_activation_hook(__FILE__, function () {

    ob_start();

    pem_create_contacts_table();

    $output = ob_get_clean();

    if ($output !== '') {

        file_put_contents(
            WP_CONTENT_DIR . '/pem-activation-output.txt',
            $output
        );
    }

});

register_deactivation_hook(__FILE__, function () {

    PEM_Scheduler::deactivate();

});

/*
|--------------------------------------------------------------------------
| Admin Assets
|--------------------------------------------------------------------------
*/

// function pem_admin_assets($hook)
// {
//     // Sirf Pushpa Email Dashboard par load hoga
//     if ($hook !== 'toplevel_page_pushpa-email') {
//         return;
//     }

//     wp_enqueue_script(
//         'chart-js',
//         'https://cdn.jsdelivr.net/npm/chart.js',
//         array(),
//         '4.5.0',
//         true
//     );

//     wp_enqueue_script(
//         'pem-dashboard',
//         PEM_URL . 'assets/js/dashboard.js',
//         array('chart-js'),
//         PEM_VERSION,
//         true
//     );
// }

// add_action('admin_enqueue_scripts', 'pem_admin_assets');


/*
|--------------------------------------------------------------------------
| Dashboard Assets
|--------------------------------------------------------------------------
*/

function pem_dashboard_assets($hook)
{
    if ($hook !== 'toplevel_page_pushpa-email') {
        return;
    }

    wp_enqueue_script(
        'chart-js',
        'https://cdn.jsdelivr.net/npm/chart.js',
        array(),
        '4.5.0',
        true
    );

    wp_enqueue_script(
        'pem-dashboard',
        PEM_URL . 'assets/js/dashboard.js',
        array('chart-js'),
        PEM_VERSION,
        true
    );

    $status = PEM_Dashboard::campaignStatus();
    $activity = PEM_Dashboard::emailActivity();

    wp_localize_script(
        'pem-dashboard',
        'pemDashboard',
        array(

        'opens' => PEM_Email_Log::totalOpened(),
        'clicks' => PEM_Email_Log::totalClicked(),
        'unsubscribed' => PEM_Email_Log::totalUnsubscribed(),

        'running' => $status['running'],
        'completed' => $status['completed'],
        'draft' => $status['draft'],
        'activity' => $activity

    )
    );
    
    
}

add_action('admin_enqueue_scripts', 'pem_dashboard_assets');
/*
|--------------------------------------------------------------------------
| Email Open Tracking
|--------------------------------------------------------------------------
*/

add_action('init', function () {

    if (!isset($_GET['pem_open'])) {
        return;
    }

    $log_id = absint($_GET['pem_open']);

    if ($log_id <= 0) {
        return;
    }

    PEM_Email_Log::markOpened($log_id);

    // Transparent 1x1 GIF
    header('Content-Type: image/gif');

    echo base64_decode(
        'R0lGODlhAQABAPAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=='
    );

    exit;

    
});

/*
|--------------------------------------------------------------------------
| Email Click Tracking
|--------------------------------------------------------------------------
*/

add_action('init', function () {

    if (!isset($_GET['pem_click'])) {
        return;
    }

    $log_id = absint($_GET['pem_click']);

    if ($log_id <= 0) {
        return;
    }

    PEM_Email_Log::markClicked($log_id);

    $url = isset($_GET['url'])
        ? rawurldecode($_GET['url'])
        : home_url('/');

    wp_redirect(esc_url_raw($url));
    exit;

});
/*
|--------------------------------------------------------------------------
| Export Campaign CSV
|--------------------------------------------------------------------------
*/

add_action(
    'admin_post_pem_export_campaign_csv',
    ['PEM_Report', 'exportCampaignCSV']
);
error_reporting(E_ALL);
ini_set('display_errors', 1);
