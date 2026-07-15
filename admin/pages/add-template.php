<?php

if (!defined('ABSPATH')) {
    exit;
}


    global $wpdb;

    $table = $wpdb->prefix . 'pushpa_templates';

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $template = null;

    if ($id > 0) {

        $template = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE id=%d",
                $id
            )
        );
    }

?>

<div class="wrap">

    <h1>
        <?php echo $id ? 'Edit Email Template' : 'Create Email Template'; ?>
    </h1>

    <form method="post">

        <?php wp_nonce_field('pem_template_nonce', 'pem_template_nonce'); ?>

        <input
            type="hidden"
            name="template_id"
            value="<?php echo esc_attr($id); ?>">

        <table class="form-table">

            <tr>
                <th>Template Name</th>
                <td>
                    <input
                        type="text"
                        name="template_name"
                        class="regular-text"
                        value="<?php echo esc_attr($template->template_name ?? ''); ?>"
                        required>
                </td>
            </tr>

            <tr>
                <th>Email Subject</th>
                <td>
                    <input
                        type="text"
                        name="subject"
                        class="regular-text"
                        value="<?php echo esc_attr($template->subject ?? ''); ?>"
                        required>
                </td>
            </tr>

            <tr>
                <th>Email Body</th>
                <td>

                    <?php

                    wp_editor(
    html_entity_decode($template->email_body ?? ''),
    'email_body',
    array(
        'textarea_name' => 'email_body',
        'media_buttons' => true,
        'textarea_rows' => 18,
        'teeny' => false,
        'tinymce' => true,
        'quicktags' => true,
    )
);

                    ?>

                </td>
            </tr>

        </table>

        <p>

            <input
                type="submit"
                name="pem_save_template"
                class="button button-primary button-large"
                value="<?php echo $id ? 'Update Template' : 'Save Template'; ?>">

        </p>

    </form>

</div>

<?php