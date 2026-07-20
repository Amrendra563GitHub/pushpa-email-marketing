<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="wrap">

    <h1>📥 Import Contacts</h1>

    <?php if (isset($_GET['imported'])) : ?>

        <?php
        $file      = sanitize_file_name($_GET['file'] ?? '');
        $total     = absint($_GET['total'] ?? 0);
        $imported  = absint($_GET['imported'] ?? 0);
        $skipped   = absint($_GET['skipped'] ?? 0);
        $time      = floatval($_GET['time'] ?? 0);
        ?>

        <div class="notice notice-success is-dismissible">

            <p>

                <strong>✅ Import Completed Successfully.</strong>

                <br><br>

                <strong>📄 File :</strong>
                <?php echo esc_html($file); ?>

                <br>

                <strong>📊 Total Rows :</strong>
                <?php echo esc_html($total); ?>

                <br>

                <strong>✅ Imported :</strong>
                <?php echo esc_html($imported); ?>

                <br>

                <strong>⏭ Skipped :</strong>
                <?php echo esc_html($skipped); ?>

                <br>

                <strong>⏱ Time :</strong>
                <?php echo esc_html($time); ?> Seconds

            </p>

        </div>

    <?php endif; ?>

    <p>Select a CSV file to import contacts.</p>

    <form method="post" enctype="multipart/form-data">

        <?php wp_nonce_field('pem_import_csv', 'pem_import_nonce'); ?>

        <input
            type="file"
            name="csv_file"
            accept=".csv"
            required>

        <br><br>

        <input
            type="submit"
            name="pem_import_csv"
            class="button button-primary"
            value="Import CSV">

    </form>

</div>