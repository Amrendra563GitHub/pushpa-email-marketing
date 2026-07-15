<?php

if (!defined('ABSPATH')) {
    exit;
}


?>

<div class="wrap">

    <h1>📥 Import Contacts</h1>

    <p>Select a CSV file to import contacts.</p>

    <form method="post" enctype="multipart/form-data">

        <?php wp_nonce_field('pem_import_csv', 'pem_import_nonce'); ?>

        <input type="file" name="csv_file" accept=".csv" required>

        <br><br>

        <input
            type="submit"
            name="pem_import_csv"
            class="button button-primary"
            value="Import CSV">

    </form>

</div>

<?php