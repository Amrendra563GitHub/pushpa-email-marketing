<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Admin Menu
 */
function pem_admin_menu()
{
    add_menu_page(
        'Pushpa Email Marketing',
        'Pushpa Email',
        'manage_options',
        'pushpa-email',
        'pem_dashboard_page',
        'dashicons-email',
        26
    );

    add_submenu_page(
        'pushpa-email',
        'Contacts',
        'Contacts',
        'manage_options',
        'pushpa-contacts',
        'pem_contacts_page'
    );

    add_submenu_page(
        'pushpa-email',
        'Add Contact',
        'Add Contact',
        'manage_options',
        'pushpa-add-contact',
        'pem_add_contact_page'
    );

    add_submenu_page(
        'pushpa-email',
        'Import CSV',
        'Import CSV',
        'manage_options',
        'pushpa-import-csv',
        'pem_import_csv_page'
    );

    add_submenu_page(
        'pushpa-email',
        'Email Templates',
        'Email Templates',
        'manage_options',
        'pushpa-templates',
        'pem_templates_page'
    );

    add_submenu_page(
        null,
        'Add Template',
        'Add Template',
        'manage_options',
        'pushpa-add-template',
        'pem_add_template_page'
    );

    add_submenu_page(
        'pushpa-email',
        'Campaigns',
        'Campaigns',
        'manage_options',
        'pushpa-campaigns',
        'pem_campaigns_page'
    );

    add_submenu_page(
        null,
        'Add Campaign',
        'Add Campaign',
        'manage_options',
        'pushpa-add-campaign',
        'pem_add_campaign_page'
    );

    add_submenu_page(
    null,
    'Campaign Report',
    'Campaign Report',
    'manage_options',
    'pushpa-campaign-report',
    'pem_campaign_report_page'
);

    add_submenu_page(
        'pushpa-email',
        'Send Test Email',
        'Send Test Email',
        'manage_options',
        'pushpa-send-email',
        'pem_send_email_page'
    );

    add_submenu_page(
        'pushpa-email',
        'Email Logs',
        'Email Logs',
        'manage_options',
        'pushpa-email-logs',
        'pem_email_logs_page'
    );

    add_submenu_page(
        'pushpa-email',
        'Bulk Email',
        'Bulk Email',
        'manage_options',
        'pushpa-bulk-email',
        'pem_bulk_email_page'
    );

    add_submenu_page(
        'pushpa-email',
        'SMTP Settings',
        'SMTP Settings',
        'manage_options',
        'pushpa-smtp',
        'pem_smtp_page'
    );

    add_submenu_page(
        'pushpa-email',
        'Settings',
        'Settings',
        'manage_options',
        'pushpa-settings',
        'pem_settings_page'
    );
    add_submenu_page(

    null,

    'Campaign Details',

    'Campaign Details',

    'manage_options',

    'pushpa-campaign-details',

    function () {

        require_once PEM_PATH . 'admin/pages/campaign-details.php';

    }

);
}

add_action('admin_menu', 'pem_admin_menu');


/*
|--------------------------------------------------------------------------
| Callback Functions
|--------------------------------------------------------------------------
*/

function pem_dashboard_page()
{
    require_once PEM_PATH . 'admin/pages/dashboard.php';
}

function pem_contacts_page()
{
    require_once PEM_PATH . 'admin/pages/contacts-page.php';
}

function pem_add_contact_page()
{
    require_once PEM_PATH . 'admin/pages/add-contact.php';
}

function pem_import_csv_page()
{
    require_once PEM_PATH . 'admin/pages/import-csv.php';
}

function pem_templates_page()
{
    require_once PEM_PATH . 'admin/pages/templates.php';
}

function pem_add_template_page()
{
    require_once PEM_PATH . 'admin/pages/add-template.php';
}

function pem_campaigns_page()
{
    require_once PEM_PATH . 'admin/pages/campaigns.php';
}

function pem_add_campaign_page()
{
    require_once PEM_PATH . 'admin/pages/add-campaign.php';
}

function pem_send_email_page()
{
    require_once PEM_PATH . 'admin/pages/send-email.php';
}

function pem_email_logs_page()
{
    require_once PEM_PATH . 'admin/pages/email-logs.php';
}

function pem_bulk_email_page()
{
    require_once PEM_PATH . 'admin/pages/bulk-email.php';
}

// function pem_smtp_page()
// {
//     require_once PEM_PATH . 'admin/pages/smtp-page.php';
// }

function pem_settings_page()
{
    require_once PEM_PATH . 'admin/pages/settings.php';
}
function pem_smtp_page()
{
    require_once PEM_PATH . 'admin/pages/smtp-settings.php';
}
function pem_campaign_report_page()
{
    require_once PEM_PATH . 'admin/pages/campaign-report.php';
}
/*
|--------------------------------------------------------------------------
| Admin Scripts
|--------------------------------------------------------------------------
*/

function pem_admin_scripts($hook)
{
    if ($hook !== 'pushpa-email_page_pushpa-bulk-email') {
        return;
    }

    wp_enqueue_script(
        'pem-bulk-email',
        PEM_URL . 'assets/js/bulk-email.js',
        array('jquery'),
        PEM_VERSION,
        true
    );

    wp_localize_script(
        'pem-bulk-email',
        'pemBulk',
        array(
            'nonce' => wp_create_nonce('pem_bulk_email_nonce')
        )
    );
}

add_action('admin_enqueue_scripts', 'pem_admin_scripts');
