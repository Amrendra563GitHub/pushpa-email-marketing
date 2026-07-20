<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Queue
{
    public static function getContacts()
    {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}pushpa_contacts
             WHERE status='Active'
             ORDER BY id ASC"
        );
    }

    public static function getContactsByGroup($group='all')
    {
        global $wpdb;

        if (empty($group) || strtolower($group)==='all') {
            return self::getContacts();
        }

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}pushpa_contacts
                 WHERE status='Active'
                 AND contact_group=%s
                 ORDER BY id ASC",
                $group
            )
        );
    }

    public static function totalContacts()
    {
        global $wpdb;

        return (int)$wpdb->get_var(
            "SELECT COUNT(*)
             FROM {$wpdb->prefix}pushpa_contacts
             WHERE status='Active'"
        );
    }

    public static function send($campaign,$template,$contact)
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

    public static function updateCampaign($campaign_id,$total,$sent,$failed)
    {
        PEM_Campaign::updateCounts(
            $campaign_id,
            $total,
            $sent,
            $failed
        );
    }

    public static function complete($campaign_id)
    {
        global $wpdb;

        $wpdb->update(
            $wpdb->prefix.'pushpa_campaigns',
            array(
                'status'=>'Completed',
                'queue_status'=>'Completed',
                'completed_at'=>current_time('mysql'),
                'last_run'=>current_time('mysql')
            ),
            array('id'=>$campaign_id),
            array('%s','%s','%s','%s'),
            array('%d')
        );
    }

    public static function processCampaign($campaign)
    {
        $template = PEM_Template::get($campaign->template_id);

        if (!$template) {
            return;
        }

        $contacts = self::getContactsByGroup(
            !empty($campaign->recipient_type)
                ? $campaign->recipient_type
                : 'all'
        );

        $total = count($contacts);
        $sent = 0;
        $failed = 0;

        self::start($campaign->id);

        foreach ($contacts as $contact) {

            self::updateCurrentEmail(
                $campaign->id,
                $contact->email
            );

            if (!is_email($contact->email)) {
                $failed++;
                continue;
            }

            if (self::send($campaign,$template,$contact)) {
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

        self::complete($campaign->id);
    }

    public static function start($campaign_id)
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix.'pushpa_campaigns',
            array(
                'queue_status'=>'Running',
                'status'=>'Running',
                'started_at'=>current_time('mysql')
            ),
            array('id'=>$campaign_id),
            array('%s','%s','%s'),
            array('%d')
        );
    }

    public static function pause($campaign_id)
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix.'pushpa_campaigns',
            array(
                'queue_status'=>'Paused',
                'paused_at'=>current_time('mysql')
            ),
            array('id'=>$campaign_id),
            array('%s','%s'),
            array('%d')
        );
    }

    public static function resume($campaign_id)
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix.'pushpa_campaigns',
            array('queue_status'=>'Running'),
            array('id'=>$campaign_id),
            array('%s'),
            array('%d')
        );
    }

    public static function stop($campaign_id)
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix.'pushpa_campaigns',
            array(
                'queue_status'=>'Stopped',
                'status'=>'Stopped',
                'completed_at'=>current_time('mysql')
            ),
            array('id'=>$campaign_id),
            array('%s','%s','%s'),
            array('%d')
        );
    }

    public static function updateCurrentEmail($campaign_id,$email)
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix.'pushpa_campaigns',
            array(
                'current_email'=>sanitize_email($email)
            ),
            array('id'=>$campaign_id),
            array('%s'),
            array('%d')
        );
    }

    public static function getQueue($campaign_id)
    {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT *
                 FROM {$wpdb->prefix}pushpa_campaigns
                 WHERE id=%d",
                $campaign_id
            )
        );
    }
}
