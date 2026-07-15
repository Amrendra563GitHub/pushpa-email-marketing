<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

if (isset($_GET['deleted'])) {

    echo '<div class="notice notice-success is-dismissible">
            <p>Campaign Deleted Successfully.</p>
          </div>';
}

$table = $wpdb->prefix . 'pushpa_campaigns';
$templates = $wpdb->prefix . 'pushpa_templates';

$campaigns = $wpdb->get_results("
SELECT
    c.*,
    t.template_name
FROM $table c
LEFT JOIN $templates t
ON c.template_id=t.id
ORDER BY c.id DESC
");

?>

<div class="wrap">

<h1 class="wp-heading-inline">📢 Campaigns</h1>

<a
href="<?php echo admin_url('admin.php?page=pushpa-add-campaign'); ?>"
class="page-title-action">

Add New

</a>

<hr class="wp-header-end">

<table class="wp-list-table widefat striped">

<thead>

<tr>

<th>ID</th>

<th>Campaign</th>

<th>Template</th>

<th>Recipients</th>

<th>Sent</th>

<th>Failed</th>

<th>Success %</th>

<th>Status</th>

<th>Created</th>

<th width="170">Actions</th>

</tr>

</thead>

<tbody>

<?php if ($campaigns) : ?>

<?php foreach ($campaigns as $campaign) :

$total = (int)$campaign->total_contacts;
$sent = (int)$campaign->sent_count;
$failed = (int)$campaign->failed_count;

$success = 0;

if ($total > 0) {
    $success = round(($sent / $total) * 100);
}

$status = $campaign->status;

$statusColor = '#666';

if ($status == 'Completed') {
    $statusColor = 'green';
}

if ($status == 'Running') {
    $statusColor = '#f39c12';
}

if ($status == 'Draft') {
    $statusColor = '#999';
}

?>

<tr>

<td><?php echo esc_html($campaign->id); ?></td>

<td>

<strong>

<?php echo esc_html($campaign->campaign_name); ?>

</strong>

<br>

<small>

<?php echo esc_html($campaign->subject); ?>

</small>

</td>

<td>

<?php echo esc_html($campaign->template_name); ?>

</td>

<td>

<?php echo esc_html($total); ?>

</td>

<td style="color:green;font-weight:bold;">

<?php echo esc_html($sent); ?>

</td>

<td style="color:red;font-weight:bold;">

<?php echo esc_html($failed); ?>

</td>

<td>

<div style="
background:#eee;
width:120px;
height:18px;
border-radius:20px;
overflow:hidden;">

<div style="
width:<?php echo esc_attr($success); ?>%;
background:#4CAF50;
height:18px;
text-align:center;
font-size:11px;
color:#fff;">

<?php echo esc_html($success); ?>%

</div>

</div>

</td>

<td>

<span style="
background:<?php echo esc_attr($statusColor); ?>;
color:#fff;
padding:5px 12px;
border-radius:20px;">

<?php echo esc_html($status); ?>

</span>

</td>

<td>

<?php echo esc_html($campaign->created_at); ?>

</td>

<td>

<a
href="<?php echo admin_url('admin.php?page=pushpa-add-campaign&id=' . $campaign->id); ?>"
class="button button-small button-primary">

Edit

</a>

<a
href="<?php echo wp_nonce_url(
admin_url(
'admin-post.php?action=pem_delete_campaign&id=' . $campaign->id
),
'pem_delete_campaign_' . $campaign->id
); ?>"
class="button button-small"
onclick="return confirm('Delete this campaign?');">

Delete

</a>

</td>

</tr>

<?php endforeach; ?>

<?php else : ?>

<tr>

<td colspan="10" style="text-align:center;">

No Campaign Found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>