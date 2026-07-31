<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Report
{

    /**
     * Campaign Analytics
     */
    public static function campaignAnalytics($campaign_id)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'pushpa_email_logs';

        $campaign_id = absint($campaign_id);

        $sent = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                 FROM $table
                 WHERE campaign_id = %d",
                $campaign_id
            )
        );

        $opened = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                 FROM $table
                 WHERE campaign_id = %d
                 AND open_count > 0",
                $campaign_id
            )
        );

        $clicked = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                 FROM $table
                 WHERE campaign_id = %d
                 AND click_count > 0",
                $campaign_id
            )
        );

        $unsubscribed = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                 FROM $table
                 WHERE campaign_id = %d
                 AND is_unsubscribed = 1",
                $campaign_id
            )
        );

        $bounced = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                 FROM $table
                 WHERE campaign_id = %d
                 AND bounce_reason IS NOT NULL
                 AND bounce_reason <> ''",
                $campaign_id
            )
        );

        $failed = (int) $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*)
         FROM $table
         WHERE campaign_id = %d
         AND status = 'Failed'",
        $campaign_id
    )
);

        return array(

            'sent' => $sent,

            'failed' => $failed,

            'opened' => $opened,

            'clicked' => $clicked,

            'unsubscribed' => $unsubscribed,

            'bounced' => $bounced,

            'open_rate' => $sent > 0 ? round(($opened / $sent) * 100, 2) : 0,

            'click_rate' => $sent > 0 ? round(($clicked / $sent) * 100, 2) : 0,

        );
    }

    /**
 * Campaign Recipients
 */
public static function campaignRecipients($campaign_id, $search = '', $limit = 10, $offset = 0)
{
    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_email_logs';

    $sql = "
        SELECT
            contact_id,
            email,
            status,
            sent_at,
            opened_at,
            clicked_at,
            is_unsubscribed
        FROM $table
        WHERE campaign_id = %d
    ";

    $params = [absint($campaign_id)];

    if (!empty($search)) {

        $sql .= " AND email LIKE %s";

        $params[] = '%' . $wpdb->esc_like($search) . '%';
    }

    $sql .= " ORDER BY sent_at DESC LIMIT %d OFFSET %d";

$params[] = absint($limit);
$params[] = absint($offset);

    return $wpdb->get_results(
        $wpdb->prepare($sql, ...$params)
    );
}

/**
 * Total Campaign Recipients
 */
public static function totalRecipients($campaign_id, $search = '')
{
    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_email_logs';

    $sql = "SELECT COUNT(*) FROM $table WHERE campaign_id = %d";

    $params = [absint($campaign_id)];

    if (!empty($search)) {

        $sql .= " AND email LIKE %s";

        $params[] = '%' . $wpdb->esc_like($search) . '%';
    }

    return (int) $wpdb->get_var(
        $wpdb->prepare($sql, ...$params)
    );
}
/**
 * Export Campaign CSV
 */
public static function exportCampaignCSV()
{
    global $wpdb;

    if (
        !current_user_can('manage_options') ||
        !isset($_GET['_wpnonce']) ||
        !wp_verify_nonce($_GET['_wpnonce'], 'pem_export_campaign_csv')
    ) {
        wp_die('Unauthorized');
    }

    $campaign_id = isset($_GET['id']) ? absint($_GET['id']) : 0;

    if (!$campaign_id) {
        wp_die('Invalid Campaign');
    }

    $logs_table = $wpdb->prefix . 'pushpa_email_logs';
$contacts_table = $wpdb->prefix . 'pushpa_contacts';

$rows = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT
            c.name,
            l.email,
            c.phone,
            c.company,
            l.status,
            l.opened_at,
            l.clicked_at,
            l.is_unsubscribed,
            l.sent_at
        FROM $logs_table l
        LEFT JOIN $contacts_table c
            ON l.contact_id = c.id
        WHERE l.campaign_id = %d
        ORDER BY l.sent_at DESC",
        $campaign_id
    ),
    ARRAY_A
);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="campaign-' . $campaign_id . '.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, [
    'Name',
    'Email',
    'Phone',
    'Company',
    'Status',
    'Opened',
    'Clicked',
    'Unsubscribed',
    'Sent At'
]);

    foreach ($rows as $row) {

        fputcsv($output, [
    $row['name'],
    $row['email'],
    $row['phone'],
    $row['company'],
    $row['status'],
    !empty($row['opened_at']) ? 'Yes' : 'No',
    !empty($row['clicked_at']) ? 'Yes' : 'No',
    $row['is_unsubscribed'] ? 'Yes' : 'No',
    $row['sent_at']
]);
    }

    fclose($output);
    exit;
}

}