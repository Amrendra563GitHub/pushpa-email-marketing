<?php

if (!defined('ABSPATH')) {
    exit;
}


    global $wpdb;

    $campaign_table = $wpdb->prefix . 'pushpa_campaigns';

    $campaigns = $wpdb->get_results(
        "SELECT * FROM $campaign_table ORDER BY id DESC"
    );
    $selected_campaign = null;
$template = null;

if (!empty($_POST['campaign_id'])) {

    $campaign_id = intval($_POST['campaign_id']);

    $selected_campaign = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}pushpa_campaigns WHERE id=%d",
            $campaign_id
        )
    );

    if ($selected_campaign) {

        $template = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}pushpa_templates WHERE id=%d",
                $selected_campaign->template_id
            )
        );

    }
    $total_contacts = 0;

if ($selected_campaign) {

    global $wpdb;

    $total_contacts = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}pushpa_contacts WHERE status='Active'"
    );

}
}

  
  
?>
<?php if ($selected_campaign) : ?>

<div class="notice notice-info">

    <p>

        <strong>Subject:</strong>

        <?php echo esc_html($selected_campaign->subject); ?>

        <br><br>

        <strong>Recipients:</strong>

        <?php echo esc_html($total_contacts); ?>

    </p>

</div>

<?php endif; ?>

<div class="wrap">

    <h1>Send Test Email</h1>

    <form method="post">

        <?php wp_nonce_field('pem_send_email_nonce', 'pem_send_email_nonce'); ?>

        <table class="form-table">

            <tr>

                <th>Campaign</th>

                <td>

                    <select
                        name="campaign_id"
                        class="regular-text"
                        required>

                        <option value="">
                            Select Campaign
                        </option>

                        <?php if (!empty($campaigns)) : ?>

                            <?php foreach ($campaigns as $campaign) : ?>

                                <option value="<?php echo esc_attr($campaign->id); ?>">

                                    <?php echo esc_html($campaign->campaign_name); ?>

                                </option>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </select>

                </td>

            </tr>

            <tr>

                <th>Email Address</th>

                <td>

                    <input
                        type="email"
                        name="test_email"
                        class="regular-text"
                        required>

                </td>

            </tr>

        </table>

        <p>

            <input
                type="submit"
                name="pem_send_test_email"
                class="button button-primary"
                value="Send Test Email">

        </p>

    </form>

</div>

<?php