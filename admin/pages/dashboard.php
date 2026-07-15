<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$total_contacts = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_contacts"
);

$total_campaigns = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_campaigns"
);

$total_templates = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_templates"
);

$total_logs = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_email_logs"
);

$success = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_email_logs
    WHERE status='Success'"
);

$failed = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_email_logs
    WHERE status='Failed'"
);

$smtp = $wpdb->get_row(
    "SELECT * FROM {$wpdb->prefix}pushpa_smtp
    WHERE status='Active'
    LIMIT 1"
);

$recent_campaigns = $wpdb->get_results(
    "SELECT campaign_name,status,sent_count,failed_count,created_at
    FROM {$wpdb->prefix}pushpa_campaigns
    ORDER BY id DESC
    LIMIT 5"
);

$recent_logs = $wpdb->get_results(
    "SELECT email,status,created_at
    FROM {$wpdb->prefix}pushpa_email_logs
    ORDER BY id DESC
    LIMIT 10"
);

?>

<div class="wrap">

<h1>🚀 Pushpa Email Marketing Dashboard</h1>

<p>

Welcome,

<strong><?php echo esc_html(wp_get_current_user()->display_name); ?></strong>

</p>

<hr>

<div style="display:flex;flex-wrap:wrap;gap:20px;">

<?php

$cards = array(

array("👥 Total Contacts",$total_contacts,"#2271b1"),
array("📧 Templates",$total_templates,"#2271b1"),
array("🚀 Campaigns",$total_campaigns,"#2271b1"),
array("📨 Email Logs",$total_logs,"#2271b1"),
array("✅ Emails Sent",$success,"green"),
array("❌ Failed",$failed,"red"),
array("📤 SMTP",$smtp ? "Connected" : "Not Configured",$smtp ? "green" : "red")

);

foreach($cards as $card){

?>

<div style="
width:220px;
background:#fff;
padding:20px;
border-left:5px solid <?php echo esc_attr($card[2]); ?>;
box-shadow:0 1px 3px rgba(0,0,0,.1);
">

<h2 style="margin:0;font-size:30px;">

<?php echo esc_html($card[1]); ?>

</h2>

<p style="margin-top:10px;color:#666;">

<?php echo esc_html($card[0]); ?>

</p>

</div>

<?php } ?>

</div>

<br>

<div class="postbox">

<h2 style="padding:15px;">📢 Recent Campaigns</h2>

<table class="widefat striped">

<thead>

<tr>

<th>Campaign</th>

<th>Status</th>

<th>Sent</th>

<th>Failed</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php if($recent_campaigns){ ?>

<?php foreach($recent_campaigns as $campaign){ ?>

<tr>

<td><?php echo esc_html($campaign->campaign_name); ?></td>

<td><?php echo esc_html($campaign->status); ?></td>

<td><?php echo esc_html($campaign->sent_count); ?></td>

<td><?php echo esc_html($campaign->failed_count); ?></td>

<td><?php echo esc_html($campaign->created_at); ?></td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="5">

No Campaign Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<br>

<div class="postbox">

<h2 style="padding:15px;">📨 Recent Email Logs</h2>

<table class="widefat striped">

<thead>

<tr>

<th>Email</th>

<th>Status</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php if($recent_logs){ ?>

<?php foreach($recent_logs as $log){ ?>

<tr>

<td><?php echo esc_html($log->email); ?></td>

<td>

<?php if($log->status=="Success"){ ?>

<span style="color:green;font-weight:bold;">

Success

</span>

<?php } else { ?>

<span style="color:red;font-weight:bold;">

Failed

</span>

<?php } ?>

</td>

<td><?php echo esc_html($log->created_at); ?></td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="3">

No Email Logs Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<hr>

<p>

<strong>Plugin Version :</strong>

<?php echo esc_html(PEM_VERSION); ?>

<br>

<strong>WordPress :</strong>

<?php echo esc_html(get_bloginfo('version')); ?>

<br>

<strong>PHP :</strong>

<?php echo esc_html(PHP_VERSION); ?>

</p>

</div>