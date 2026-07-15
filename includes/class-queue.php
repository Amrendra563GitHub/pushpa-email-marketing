<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Queue
{
    /**
     * Get Active Contacts
     */
    public static function getContacts()
    {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT *
            FROM {$wpdb->prefix}pushpa_contacts
            WHERE status='Active'
            ORDER BY id ASC"
        );
    }
    /**
 * Get Contacts By Group
 */
public static function getContactsByGroup($group = 'all')
{
    global $wpdb;

    if (
    empty($group) ||
    strtolower($group) === 'all'
) {

        return self::getContacts();

    }

    return $wpdb->get_results(

        $wpdb->prepare(

            "SELECT *
            FROM {$wpdb->prefix}pushpa_contacts
            WHERE status='Active'
            AND contact_group=%s
            ORDER BY id ASC",

            $group

        )

    );
}

    /**
     * Total Contacts
     */
    public static function totalContacts()
    {
        global $wpdb;

        return (int) $wpdb->get_var(
            "SELECT COUNT(*)
            FROM {$wpdb->prefix}pushpa_contacts
            WHERE status='Active'"
        );
    }

    /**
     * Send One Email
     */
    public static function send($campaign, $template, $contact)
    {
        $result = PEM_Mailer::send(
            $contact->email,
            $campaign->subject,
            $template->email_body,
            $contact
        );

        PEM_Email_Log::save(
            $campaign->id,
            $contact->id,
            $contact->email,
            $campaign->subject,
            $result ? 'Success' : 'Failed'
        );

        return $result;
    }

    /**
     * Update Campaign Progress
     */
    public static function updateCampaign(
        $campaign_id,
        $total,
        $sent,
        $failed
    ) {
        PEM_Campaign::updateCounts(
            $campaign_id,
            $total,
            $sent,
            $failed
        );
    }

    /**
     * Complete Campaign
     */
    public static function complete($campaign_id)
{
    global $wpdb;

    $wpdb->update(
        $wpdb->prefix . 'pushpa_campaigns',
        array(
            'status'   => 'Completed',
            'last_run' => current_time('mysql')
        ),
        array(
            'id' => $campaign_id
        ),
        array(
            '%s',
            '%s'
        ),
        array('%d')
    );
}
    /**
 * Process Scheduled Campaign
 */
public static function processCampaign($campaign)
{
    $template = PEM_Template::get(
        $campaign->template_id
    );

    if (!$template) {
        return;
    }

    $group = !empty($campaign->recipient_type)
    ? $campaign->recipient_type
    : 'all';

$contacts = self::getContactsByGroup($group);

    $total  = count($contacts);

    $sent   = 0;

    $failed = 0;

    PEM_Campaign::updateStatus(
        $campaign->id,
        'Running'
    );

    foreach ($contacts as $contact) {

        if (!is_email($contact->email)) {

            $failed++;

            continue;
        }

        $result = self::send(
            $campaign,
            $template,
            $contact
        );

        if ($result) {

            $sent++;

        } else {

            $failed++;

        }

        self::updateCampaign(
            $campaign->id,
            $total,
            $sent,
            $failed
        );

    }

    self::complete(
        $campaign->id
    );
}
}