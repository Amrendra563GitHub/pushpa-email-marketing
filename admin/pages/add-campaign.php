<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$campaigns_table = $wpdb->prefix . 'pushpa_campaigns';
$templates_table = $wpdb->prefix . 'pushpa_templates';

$id = isset($_GET['id']) ? absint($_GET['id']) : 0;

$campaign = null;

if ($id > 0) {

    $campaign = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $campaigns_table WHERE id=%d",
            $id
        )
    );
}

$templates = $wpdb->get_results(
    "SELECT id, template_name
    FROM $templates_table
    WHERE status='Active'
    ORDER BY template_name ASC"
);

?>

<div class="wrap">

<h1>

<?php echo $id ? 'Edit Campaign' : 'Create Campaign'; ?>

</h1>

<form method="post">

<?php wp_nonce_field('pem_campaign_nonce', 'pem_campaign_nonce'); ?>

<input
type="hidden"
name="campaign_id"
value="<?php echo esc_attr($id); ?>">

<table class="form-table">

<tr>

<th>Campaign Name</th>

<td>

<input
type="text"
name="campaign_name"
class="regular-text"
required
value="<?php echo esc_attr($campaign->campaign_name ?? ''); ?>">

</td>

</tr>

<tr>

<th>Email Subject</th>

<td>

<input
type="text"
name="subject"
class="regular-text"
required
value="<?php echo esc_attr($campaign->subject ?? ''); ?>">

</td>

</tr>

<tr>

<th>Email Template</th>

<td>

<select
name="template_id"
class="regular-text"
required>

<option value="">Select Template</option>

<?php foreach ($templates as $template) : ?>

<option
value="<?php echo esc_attr($template->id); ?>"
<?php selected($campaign->template_id ?? '', $template->id); ?>>

<?php echo esc_html($template->template_name); ?>

</option>

<?php endforeach; ?>

</select>

</td>

</tr>

<tr>

<th>Recipients</th>

<td>

<select
name="recipient_type"
class="regular-text">

<option
value="all"
<?php selected($campaign->recipient_type ?? 'all', 'all'); ?>>

All Contacts

</option>

<option
value="active"
<?php selected($campaign->recipient_type ?? '', 'active'); ?>>

Active Contacts

</option>

</select>

</td>

</tr>

<tr>

<th>Send Option</th>

<td>

<label>

<input
type="radio"
name="send_type"
value="now"
checked>

Send Now

</label>

&nbsp;&nbsp;&nbsp;

<label>

<input
type="radio"
name="send_type"
value="schedule"
<?php checked($campaign->send_type ?? '', 'schedule'); ?>>

Schedule

</label>

</td>

</tr>

<tbody id="pem-schedule-box" style="display:none;">

<tr>

<th>Schedule Date</th>

<td>

<input
type="date"
name="schedule_date"
value="<?php echo esc_attr($campaign->schedule_date ?? ''); ?>">

</td>

</tr>

<tr>

<th>Schedule Time</th>

<td>

<input
type="time"
name="schedule_time"
value="<?php echo esc_attr($campaign->schedule_time ?? ''); ?>">

</td>

</tr>

</tbody>

</table>

<p>

<input
type="submit"
name="pem_save_campaign"
class="button button-primary button-large"
value="<?php echo $id ? 'Update Campaign' : 'Save Campaign'; ?>">

</p>

</form>

</div>

<script>

document.addEventListener("DOMContentLoaded", function(){

const radios = document.querySelectorAll("input[name='send_type']");
const box = document.getElementById("pem-schedule-box");

function toggleSchedule(){

const value = document.querySelector("input[name='send_type']:checked").value;

box.style.display = value === "schedule"
? "table-row-group"
: "none";

}

radios.forEach(function(r){

r.addEventListener("change", toggleSchedule);

});

toggleSchedule();

});

</script>