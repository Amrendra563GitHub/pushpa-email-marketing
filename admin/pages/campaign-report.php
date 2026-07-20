<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$campaign_id = absint($_GET['id'] ?? 0);

if (!$campaign_id) {

    wp_die('Invalid Campaign.');

}

$campaign = PEM_Campaign::get($campaign_id);

if (!$campaign) {

    wp_die('Campaign Not Found.');

}

$logs_table = $wpdb->prefix . 'pushpa_email_logs';

$total = (int) $wpdb->get_var(

    $wpdb->prepare(

        "SELECT COUNT(*)
        FROM $logs_table
        WHERE campaign_id=%d",

        $campaign_id

    )

);

$success = (int) $wpdb->get_var(

    $wpdb->prepare(

        "SELECT COUNT(*)
        FROM $logs_table
        WHERE campaign_id=%d
        AND status='Success'",

        $campaign_id

    )

);

$failed = (int) $wpdb->get_var(

    $wpdb->prepare(

        "SELECT COUNT(*)
        FROM $logs_table
        WHERE campaign_id=%d
        AND status='Failed'",

        $campaign_id

    )

);

$pending = $total - ($success + $failed);

$logs = $wpdb->get_results(

    $wpdb->prepare(

        "SELECT *
        FROM $logs_table
        WHERE campaign_id=%d
        ORDER BY id DESC",

        $campaign_id

    )

);

?>

<div class="wrap">

<h1>📊 Campaign Report</h1>

<hr>

<h2>

<?php echo esc_html($campaign->campaign_name); ?>

</h2>

<table class="widefat striped" style="max-width:700px;">

<tbody>

<tr>

<th width="220">Campaign</th>

<td><?php echo esc_html($campaign->campaign_name); ?></td>

</tr>

<tr>

<th>Status</th>

<td><?php echo esc_html($campaign->status); ?></td>

</tr>

<tr>

<th>Total Recipients</th>

<td><?php echo esc_html($total); ?></td>

</tr>

<tr>

<th>Emails Sent</th>

<td style="color:green;font-weight:bold;">

<?php echo esc_html($success); ?>

</td>

</tr>

<tr>

<th>Failed</th>

<td style="color:red;font-weight:bold;">

<?php echo esc_html($failed); ?>

</td>

</tr>

<tr>

<th>Pending</th>

<td>

<?php echo esc_html($pending); ?>

</td>

</tr>

</tbody>

</table>

<br>

<h2>Recent Email Activity</h2>

<table class="widefat striped">

<thead>

<tr>

<th>ID</th>

<th>Email</th>

<th>Subject</th>

<th>Status</th>

<th>Sent At</th>

</tr>

</thead>

<tbody>

<?php if ($logs) : ?>

<?php foreach ($logs as $log) : ?>

<tr>

<td><?php echo esc_html($log->id); ?></td>

<td><?php echo esc_html($log->email); ?></td>

<td><?php echo esc_html($log->subject); ?></td>

<td>

<?php

if ($log->status == 'Success') {

    echo '<span style="color:green;font-weight:bold;">Success</span>';

} elseif ($log->status == 'Failed') {

    echo '<span style="color:red;font-weight:bold;">Failed</span>';

} else {

    echo '<span style="color:#dba617;font-weight:bold;">Pending</span>';

}

?>

</td>

<td><?php echo esc_html($log->sent_at); ?></td>

</tr>

<?php endforeach; ?>

<?php else : ?>

<tr>

<td colspan="5">

No Email Logs Found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>