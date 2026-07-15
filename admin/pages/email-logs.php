<?php

if (!defined('ABSPATH')) {
    exit;
}


    global $wpdb;

    $log_table = $wpdb->prefix . 'pushpa_email_logs';
    $campaign_table = $wpdb->prefix . 'pushpa_campaigns';

    $logs = $wpdb->get_results("
        SELECT logs.*, campaigns.campaign_name
        FROM {$log_table} AS logs
        LEFT JOIN {$campaign_table} AS campaigns
        ON logs.campaign_id = campaigns.id
        ORDER BY logs.id DESC
    ");
?>

<div class="wrap">

    <h1 class="wp-heading-inline">📊 Email Logs</h1>

    <hr class="wp-header-end">

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

                        <?php if ($log->status === 'Success') : ?>

                            <span style="background:#46b450;color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:bold;">
                                ✓ Success
                            </span>

                        <?php elseif ($log->status === 'Failed') : ?>

                            <span style="background:#dc3232;color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:bold;">
                                ✗ Failed
                            </span>

                        <?php else : ?>

                            <span style="background:#ffb900;color:#fff;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:bold;">
                                Pending
                            </span>

                        <?php endif; ?>

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

<?php