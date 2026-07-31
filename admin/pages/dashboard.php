<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// $stats = PEM_Dashboard::stats();

// $total_contacts     = $stats['contacts'];
// $total_campaigns    = $stats['campaigns'];
// $total_templates    = $stats['templates'];
// $total_logs         = $stats['logs'];
// $total_opened       = $stats['opened'];
// $total_clicked      = $stats['clicked'];
// $total_unsubscribed = $stats['unsubscribed'];

$stats = PEM_Dashboard::stats();

$total_contacts      = $stats['contacts'];
$total_campaigns     = $stats['campaigns'];
$total_templates     = $stats['templates'];
$total_logs          = $stats['logs'];
$total_opened        = $stats['opened'];
$total_clicked       = $stats['clicked'];
$total_unsubscribed  = $stats['unsubscribed'];

// $total_contacts = (int) $wpdb->get_var(
//     "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_contacts"
// );

// $total_campaigns = (int) $wpdb->get_var(
//     "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_campaigns"
// );

// $total_templates = (int) $wpdb->get_var(
//     "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_templates"
// );

// $total_logs = (int) $wpdb->get_var(
//     "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_email_logs"
// );

$success = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_email_logs
    WHERE status='Success'"
);

$failed = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_email_logs
    WHERE status='Failed'"
);
$running = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_campaigns
    WHERE status='Running'"
);

$completed = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_campaigns
    WHERE status='Completed'"
);

$draft = (int) $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_campaigns
    WHERE status='Draft'"
);

$success_rate = 0;

if ($total_logs > 0) {

    $success_rate = round(
        ($success / $total_logs) * 100,
        2
    );

}

$open_rate = 0;
$click_rate = 0;

if ($total_logs > 0) {

    $open_rate = round(
        ($total_opened / $total_logs) * 100,
        2
    );

    $click_rate = round(
        ($total_clicked / $total_logs) * 100,
        2
    );
}
// $smtp = $wpdb->get_row(
//     "SELECT * FROM {$wpdb->prefix}pushpa_smtp
//     WHERE status='Active'
//     LIMIT 1"
// );

// $recent_campaigns = $wpdb->get_results(
//     "SELECT campaign_name,status,sent_count,failed_count,created_at
//     FROM {$wpdb->prefix}pushpa_campaigns
//     ORDER BY id DESC
//     LIMIT 5"
// );

// $recent_logs = $wpdb->get_results(
//     "SELECT email,status,sent_at
//     FROM {$wpdb->prefix}pushpa_email_logs
//     ORDER BY id DESC
//     LIMIT 10"
// );

$smtp = PEM_Dashboard::smtp();

$recent_campaigns = PEM_Dashboard::recentCampaigns();

$recent_logs = PEM_Dashboard::recentLogs();

$campaign_reports = PEM_Dashboard::campaignReports();


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
    

array("👥 Total Contacts", $total_contacts, "#2271b1"),
array("📧 Templates", $total_templates, "#673ab7"),
array("🚀 Campaigns", $total_campaigns, "#ff9800"),
array("📨 Email Logs", $total_logs, "#009688"),

array("✅ Emails Sent", $success, "#4CAF50"),
array("❌ Failed", $failed, "#f44336"),
array("📊 Success Rate", $success_rate . "%", "#3f51b5"),

array("🟢 Running", $running, "#ff9800"),
array("🏁 Completed", $completed, "#4CAF50"),
array("📝 Draft", $draft, "#9e9e9e"),

array(
    "📤 SMTP",
    $smtp ? "Connected" : "Not Configured",
    $smtp ? "#4CAF50" : "#f44336"
),
array("👁 Total Opens", $total_opened, "#2196F3"),

array("🖱 Total Clicks", $total_clicked, "#9C27B0"),

array("🚫 Unsubscribed", $total_unsubscribed, "#F44336"),
array("📈 Open Rate", $open_rate . "%", "#03A9F4"),

array("📊 Click Rate", $click_rate . "%", "#8BC34A"),
);



foreach($cards as $card){

?>

<div style="
width:240px;
min-height:120px;
border-radius:10px;
background:#fff;
padding:20px;
border-left:5px solid <?php echo esc_attr($card[2]); ?>;
box-shadow:0 1px 3px rgba(0,0,0,.1);
">

<h2 style="margin:0;font-size:34px;
font-weight:bold;">

<?php echo esc_html($card[1]); ?>

</h2>

<p style="margin-top:10px;color:#666;">

<?php echo esc_html($card[0]); ?>

</p>

</div>

<?php } ?>

<div class="postbox" style="padding:20px;margin-bottom:20px;width:100%;">

    <h2>Email Analytics</h2>

    <div style="height:350px;">
        <canvas id="pemAnalyticsChart"></canvas>
    </div>

</div>

<div class="postbox" style="padding:20px;margin-top:20px;width:100%;">

    <h2>Campaign Status</h2>

    <div style="height:350px;">
        <canvas id="pemCampaignChart"></canvas>
    </div>

</div>
<div class="postbox" style="padding:20px;margin-top:20px;width:100%;">

    <h2>Last 7 Days Email Activity</h2>

    <div style="height:350px;">
        <canvas id="pemActivityChart"></canvas>
    </div>

</div>
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

<td><?php echo esc_html($log->sent_at); ?></td>

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

<div class="postbox" style="margin-top:20px;">

    <h2 style="padding:15px;">📊 Campaign Reports</h2>

    <table class="widefat striped">

        <thead>
            <tr>
                <th>Campaign</th>
                <th>Sent</th>
                <th>Failed</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($campaign_reports as $report) : ?>

            <tr>

                <td>
    <a href="<?php echo admin_url('admin.php?page=pushpa-campaign-details&id=' . absint($report->id)); ?>">
        <?php echo esc_html($report->campaign_name); ?>
    </a>
</td>

                <td><?php echo esc_html($report->sent_count); ?></td>

                <td><?php echo esc_html($report->failed_count); ?></td>

                <td><?php echo esc_html($report->status); ?></td>

                <td><?php echo esc_html($report->created_at); ?></td>

            </tr>

        <?php endforeach; ?>

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