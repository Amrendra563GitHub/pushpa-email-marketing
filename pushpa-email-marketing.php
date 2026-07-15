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
// require_once PEM_PATH . 'admin/pages/smtp-settings.php';
// require_once PEM_PATH . 'admin/pages/settings.php';
require_once PEM_PATH . 'admin/handlers/save-settings.php';
require_once PEM_PATH . 'admin/handlers/save-smtp.php';
require_once PEM_PATH . 'admin/ajax/bulk-email-process.php';

require_once PEM_PATH . 'database/create-table.php';
require_once PEM_PATH . 'database/create-template-table.php';
require_once PEM_PATH . 'database/create-campaign-table.php';
require_once PEM_PATH . 'database/create-email-log-table.php';
require_once PEM_PATH . 'database/create-settings-table.php';
require_once PEM_PATH . 'database/create-smtp-table.php';

register_activation_hook(__FILE__, function () {

    pem_create_contacts_table();

    pem_create_templates_table();

    pem_create_campaigns_table();

    pem_create_email_logs_table();

    pem_create_settings_table();

    pem_create_smtp_table();

    PEM_Scheduler::activate();

});

register_deactivation_hook(__FILE__, function () {

    PEM_Scheduler::deactivate();

});
