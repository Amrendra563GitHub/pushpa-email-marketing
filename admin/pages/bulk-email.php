<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$campaigns = PEM_Campaign::get_all();

$total_contacts = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_contacts
    WHERE status='Active'"
);

$groups = array(
    'All',
    'General',
    'Customers',
    'Students',
    'Employees',
    'Clients',
    'VIP'
);

?>

<div class="wrap">

<h1 class="wp-heading-inline">📧 Bulk Email</h1>

<hr class="wp-header-end">

<?php if (isset($_GET['sent'])) : ?>

<div class="notice notice-success is-dismissible">

<p>

<strong>Campaign Completed Successfully.</strong>

<br>

Sent :
<strong><?php echo esc_html($_GET['sent']); ?></strong>

|

Failed :
<strong><?php echo esc_html($_GET['failed']); ?></strong>

</p>

</div>

<?php endif; ?>

<div style="
background:#fff;
padding:20px;
border-left:4px solid #2271b1;
margin-bottom:20px;
box-shadow:0 1px 3px rgba(0,0,0,.1);
">

<h2 style="margin-top:0;">Campaign Details</h2>

<table class="form-table">

<tr>

<th>Total Active Contacts</th>

<td>

<strong
id="pem-total-count"
style="font-size:18px;color:#2271b1;">

<?php echo esc_html($total_contacts); ?>

</strong>

</td>

</tr>

</table>

</div>

<form method="post">

<?php
wp_nonce_field(
'pem_bulk_email_nonce',
'pem_bulk_email_nonce'
);
?>

<table class="form-table">

<tr>

<th width="180">Campaign</th>

<td>

<select
name="campaign_id"
class="regular-text"
required>

<option value="">Select Campaign</option>

<?php foreach ($campaigns as $campaign) : ?>

<option
value="<?php echo esc_attr($campaign->id); ?>">

<?php echo esc_html($campaign->campaign_name); ?>

</option>

<?php endforeach; ?>

</select>

</td>

</tr>

<tr>

<th>Recipient Group</th>

<td>

<select
name="contact_group"
class="regular-text">

<?php foreach ($groups as $group) : ?>

<option
value="<?php echo esc_attr($group); ?>">

<?php echo esc_html($group); ?>

</option>

<?php endforeach; ?>

</select>

<p class="description">

Choose which contact group will receive this campaign.

</p>

</td>

</tr>

<tr>

<th>Batch Size</th>

<td>

<select
name="batch_size"
class="regular-text">

<option value="25">25 Emails</option>
<option value="50" selected>50 Emails</option>
<option value="100">100 Emails</option>

</select>

</td>

</tr>

<tr>

<th>Delay</th>

<td>

<select
name="delay"
class="regular-text">

<option value="0">No Delay</option>
<option value="1" selected>1 Second</option>
<option value="2">2 Seconds</option>
<option value="5">5 Seconds</option>

</select>

</td>

</tr>

</table>

<p>

<input
type="submit"
name="pem_send_bulk_email"
class="button button-primary button-large"
value="🚀 Start Bulk Email">

</p>

</form>

<hr>

<h2>Progress</h2>

<progress
id="pem-progress"
value="0"
max="100"
style="width:100%;height:22px;">
</progress>

<table
class="widefat striped"
style="margin-top:20px;max-width:500px;">

<tbody>

<tr>

<th>Sent</th>

<td id="pem-sent-count">0</td>

</tr>

<tr>

<th>Failed</th>

<td id="pem-failed-count">0</td>

</tr>

<tr>

<th>Remaining</th>

<td id="pem-remaining-count">

<?php echo esc_html($total_contacts); ?>

</td>

</tr>

</tbody>

</table>

</div>