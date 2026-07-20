<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$table = $wpdb->prefix . 'pushpa_smtp';

$data = $wpdb->get_row("SELECT * FROM $table LIMIT 1");

?>

<div class="wrap">

    <h1>SMTP Settings</h1>
    <?php if (isset($_GET['saved'])) : ?>

<div class="notice notice-success is-dismissible">
    <p><strong>SMTP Settings Saved Successfully.</strong></p>
</div>

<?php endif; ?>


<?php if (isset($_GET['test'])) : ?>

    <?php if ($_GET['test'] === 'success') : ?>

        <div class="notice notice-success is-dismissible">
            <p><strong>✅ Test Email Sent Successfully.</strong></p>
        </div>

    <?php elseif ($_GET['test'] === 'failed') : ?>

        <div class="notice notice-error">
            <p><strong>❌ Failed to Send Test Email.</strong></p>
        </div>

    <?php elseif ($_GET['test'] === 'invalid') : ?>

        <div class="notice notice-warning">
            <p><strong>⚠ Please enter a valid email address.</strong></p>
        </div>

    <?php endif; ?>

<?php endif; ?>
    <form method="post">

        <?php wp_nonce_field('pem_save_smtp', 'pem_smtp_nonce'); ?>

        <table class="form-table">

            <tr>
                <th>SMTP Host</th>
                <td>
                    <input type="text" name="smtp_host" class="regular-text"
                        value="<?php echo esc_attr($data->smtp_host ?? ''); ?>">
                </td>
            </tr>

            <tr>
                <th>SMTP Port</th>
                <td>
                    <input type="number" name="smtp_port" class="regular-text"
                        value="<?php echo esc_attr($data->smtp_port ?? '587'); ?>">
                </td>
            </tr>

            <tr>
                <th>Encryption</th>
                <td>
                    <select name="smtp_encryption">
                        <option value="">None</option>
                        <option value="ssl" <?php selected($data->smtp_encryption ?? '', 'ssl'); ?>>SSL</option>
                        <option value="tls" <?php selected($data->smtp_encryption ?? '', 'tls'); ?>>TLS</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th>SMTP Username</th>
                <td>
                    <input type="text" name="smtp_username" class="regular-text"
                        value="<?php echo esc_attr($data->smtp_username ?? ''); ?>">
                </td>
            </tr>

            <tr>
                <th>SMTP Password</th>
                <td>
                    <input type="password" name="smtp_password" class="regular-text"
                        value="<?php echo esc_attr($data->smtp_password ?? ''); ?>">
                </td>
            </tr>

            <tr>
                <th>From Email</th>
                <td>
                    <input type="email" name="from_email" class="regular-text"
                        value="<?php echo esc_attr($data->from_email ?? ''); ?>">
                </td>
            </tr>

            <tr>
                <th>From Name</th>
                <td>
                    <input type="text" name="from_name" class="regular-text"
                        value="<?php echo esc_attr($data->from_name ?? ''); ?>">
                </td>
            </tr>

        </table>

        <p>
            <input type="submit" name="pem_save_smtp"
                class="button button-primary"
                value="Save SMTP Settings">
        </p>

    </form>
    <hr>

<h2>Send Test Email</h2>

<form method="post">

    <?php wp_nonce_field(
        'pem_test_smtp',
        'pem_test_smtp_nonce'
    ); ?>

    <table class="form-table">

        <tr>

            <th>Test Email</th>

            <td>

                <input
                    type="email"
                    name="test_email"
                    class="regular-text"
                    placeholder="example@gmail.com"
                    required>

            </td>

        </tr>

    </table>

    <p>

        <input
            type="submit"
            name="pem_test_smtp"
            class="button button-secondary"
            value="Send Test Email">

    </p>

</form>

</div>