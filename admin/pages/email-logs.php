<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$log_table      = $wpdb->prefix . 'pushpa_email_logs';
$campaign_table = $wpdb->prefix . 'pushpa_campaigns';

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

$search   = sanitize_text_field($_GET['s'] ?? '');
$status   = sanitize_text_field($_GET['status'] ?? '');
$campaign = absint($_GET['campaign'] ?? 0);

$where = " WHERE 1=1 ";
$args  = array();

if (!empty($search)) {

    $where .= " AND logs.email LIKE %s";

    $args[] = '%' . $wpdb->esc_like($search) . '%';
}

if (!empty($status)) {

    $where .= " AND logs.status=%s";

    $args[] = $status;
}

if ($campaign > 0) {

    $where .= " AND logs.campaign_id=%d";

    $args[] = $campaign;
}

/*
|--------------------------------------------------------------------------
| Campaign List
|--------------------------------------------------------------------------
*/

$campaigns = $wpdb->get_results(
    "SELECT id, campaign_name
    FROM {$campaign_table}
    ORDER BY campaign_name ASC"
);

/*
|--------------------------------------------------------------------------
| Logs
|--------------------------------------------------------------------------
*/

$sql = "

SELECT
logs.*,
campaigns.campaign_name

FROM {$log_table} AS logs

LEFT JOIN {$campaign_table} AS campaigns

ON logs.campaign_id = campaigns.id

{$where}

ORDER BY logs.id DESC

";

if (!empty($args)) {

    $logs = $wpdb->get_results(
        $wpdb->prepare($sql, ...$args)
    );

} else {

    $logs = $wpdb->get_results($sql);
}

?>

<div class="wrap">

<h1 class="wp-heading-inline">📊 Email Logs</h1>

<hr class="wp-header-end">

<form method="get" style="margin:20px 0;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">

<input
type="hidden"
name="page"
value="pushpa-email-logs">

<input
type="text"
name="s"
class="regular-text"
placeholder="Search Email..."
value="<?php echo esc_attr($search); ?>">

<select name="campaign">

<option value="">All Campaigns</option>

<?php foreach ($campaigns as $item) : ?>

<option
value="<?php echo esc_attr($item->id); ?>"
<?php selected($campaign, $item->id); ?>>

<?php echo esc_html($item->campaign_name); ?>

</option>

<?php endforeach; ?>

</select>

<select name="status">

<option value="">All Status</option>

<option
value="Success"
<?php selected($status, 'Success'); ?>>

Success

</option>

<option
value="Failed"
<?php selected($status, 'Failed'); ?>>

Failed

</option>

<option
value="Pending"
<?php selected($status, 'Pending'); ?>>

Pending

</option>

</select>

<input
type="submit"
class="button button-primary"
value="Filter">

<a
href="<?php echo esc_url(admin_url('admin.php?page=pushpa-email-logs')); ?>"
class="button">

Reset

</a>

</form>

<table class="widefat fixed striped">

<thead>

<tr>

<th width="60">ID</th>

<th>Campaign</th>

<th>Email</th>

<th>Subject</th>

<th width="120">Status</th>

<th width="180">Sent At</th>

</tr>

</thead>

<tbody>

<?php if (!empty($logs)) : ?>

<?php foreach ($logs as $log) : ?>

<tr>

<td><?php echo esc_html($log->id); ?></td>

<td>

<?php
echo !empty($log->campaign_name)
    ? esc_html($log->campaign_name)
    : '-';
?>

</td>

<td><?php echo esc_html($log->email); ?></td>

<td><?php echo esc_html($log->subject); ?></td>

<td>

<?php

if ($log->status === 'Success') {

    echo '<span style="
    background:#46b450;
    color:#fff;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
    ">✓ Success</span>';

} elseif ($log->status === 'Failed') {

    echo '<span style="
    background:#dc3232;
    color:#fff;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
    ">✗ Failed</span>';

} else {

    echo '<span style="
    background:#ffb900;
    color:#fff;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
    ">Pending</span>';

}

?>

</td>

<td><?php echo esc_html($log->sent_at); ?></td>

</tr>

<?php endforeach; ?>

<?php else : ?>

<tr>

<td colspan="6" style="text-align:center;">

No Email Logs Found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>