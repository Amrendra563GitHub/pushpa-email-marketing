<?php

if (!defined('ABSPATH')) {
    exit;
}


?>

<div class="wrap">

    <h1>⚙ Pushpa Email Settings</h1>

    <form method="post">

        <?php wp_nonce_field('pem_save_settings', 'pem_settings_nonce'); ?>

        <table class="form-table">

            <tr>

                <th>Company Name</th>

                <td>

                    <input
                        type="text"
                        name="company_name"
                        class="regular-text"
                        value="<?php echo esc_attr(PEM_Settings::get('company_name')); ?>">

                </td>

            </tr>

            <tr>

                <th>Sender Name</th>

                <td>

                    <input
                        type="text"
                        name="sender_name"
                        class="regular-text"
                        value="<?php echo esc_attr(PEM_Settings::get('sender_name')); ?>">

                </td>

            </tr>

            <tr>

                <th>Sender Email</th>

                <td>

                    <input
                        type="email"
                        name="sender_email"
                        class="regular-text"
                        value="<?php echo esc_attr(PEM_Settings::get('sender_email')); ?>">

                </td>

            </tr>

            <tr>

                <th>Reply-To Email</th>

                <td>

                    <input
                        type="email"
                        name="reply_to_email"
                        class="regular-text"
                        value="<?php echo esc_attr(PEM_Settings::get('reply_to_email')); ?>">

                </td>

            </tr>

        </table>

        <p>

            <input
                type="submit"
                name="pem_save_settings"
                class="button button-primary button-large"
                value="Save Settings">

        </p>

    </form>

</div>

<?php