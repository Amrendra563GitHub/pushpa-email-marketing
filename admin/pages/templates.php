<?php

if (!defined('ABSPATH')) {
    exit;
}


    global $wpdb;
    if (get_transient('pem_template_deleted')) {

    delete_transient('pem_template_deleted');

    echo '<div class="notice notice-success is-dismissible">
            <p>Template Deleted Successfully.</p>
          </div>';
}
    $table = $wpdb->prefix . 'pushpa_templates';

    $templates = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    ?>

    <div class="wrap">

        <h1 class="wp-heading-inline">📧 Email Templates</h1>

        <a href="<?php echo admin_url('admin.php?page=pushpa-add-template'); ?>"
           class="page-title-action">
            Add New
        </a>

        <hr class="wp-header-end">

        <table class="wp-list-table widefat fixed striped">

            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th>Template Name</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($templates)) : ?>

                <?php foreach ($templates as $template) : ?>

                    <tr>

                        <td><?php echo esc_html($template->id); ?></td>

                        <td><?php echo esc_html($template->template_name); ?></td>

                        <td><?php echo esc_html($template->subject); ?></td>

                        <td><?php echo esc_html($template->status); ?></td>

                        <td><?php echo esc_html($template->created_at); ?></td>
                        <td>

    <a
        href="<?php echo admin_url('admin.php?page=pushpa-add-template&id=' . $template->id); ?>"
        class="button button-small button-primary">

        Edit

    </a>

    <a
        href="<?php echo wp_nonce_url(
            admin_url('admin.php?page=pushpa-templates&delete=' . $template->id),
            'pem_delete_template_' . $template->id
        ); ?>"
        class="button button-small button-secondary"
        onclick="return confirm('Are you sure you want to delete this template?');">

        Delete

    </a>

</td>

                    </tr>

                <?php endforeach; ?>

            <?php else : ?>

                <tr>
                    <td colspan="6" style="text-align:center;">
                        No Templates Found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <?php