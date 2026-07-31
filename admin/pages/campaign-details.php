<?php

if (!defined('ABSPATH')) {
    exit;
}

$campaign_id = isset($_GET['id'])
    ? absint($_GET['id'])
    : 0;

$campaign = PEM_Dashboard::getCampaign($campaign_id);

if (!$campaign) {
    wp_die('Campaign not found.');
}

$analytics = PEM_Report::campaignAnalytics($campaign_id);

$search = isset($_GET['search'])
    ? sanitize_text_field($_GET['search'])
    : '';

$paged = isset($_GET['paged'])
    ? max(1, absint($_GET['paged']))
    : 1;

$limit = 10;

$offset = ($paged - 1) * $limit;

$total = PEM_Report::totalRecipients(
    $campaign_id,
    $search
);

$total_pages = ceil($total / $limit);

$recipients = PEM_Report::campaignRecipients(
    $campaign_id,
    $search,
    $limit,
    $offset
);
?>

<div class="wrap">

    

    <h1>Campaign Details</h1>

    <table class="widefat striped" style="max-width:900px; margin-top:20px;">

        <tbody>

            <tr>
    <th>Campaign Name</th>
    <td><?php echo esc_html($campaign->campaign_name); ?></td>
</tr>

<tr>
    <th>Status</th>
    <td><?php echo esc_html($campaign->status); ?></td>
</tr>

<tr>
    <th>Sent</th>
    <td><?php echo esc_html($analytics['sent']); ?></td>
</tr>

<tr>
    <th>Failed</th>
    <td><?php echo esc_html($analytics['failed']); ?></td>
</tr>

<tr>
    <th>Opened</th>
    <td><?php echo esc_html($analytics['opened']); ?></td>
</tr>

<tr>
    <th>Clicked</th>
    <td><?php echo esc_html($analytics['clicked']); ?></td>
</tr>

<tr>
    <th>Created</th>
    <td><?php echo esc_html($campaign->created_at); ?></td>
</tr>
        </tbody>

    </table>

    <div class="postbox" style="max-width:900px;margin-top:20px;">

    <h2 style="padding:15px;">📊 Campaign Analytics</h2>

    <div style="display:grid;
                grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
                gap:15px;
                padding:20px;">

        <?php

        $cards = [

            'Total Sent'      => $analytics['sent'],
            'Failed'          => $analytics['failed'],
            'Opened'          => $analytics['opened'],
            'Clicked'         => $analytics['clicked'],
            'Open Rate'       => $analytics['open_rate'] . '%',
            'Click Rate'      => $analytics['click_rate'] . '%',
            'Unsubscribed'    => $analytics['unsubscribed'],
            'Bounced'         => $analytics['bounced'],

        ];

        foreach ($cards as $title => $value) :
        ?>

            <div style="
                background:#fff;
                border:1px solid #dcdcde;
                border-radius:8px;
                padding:20px;
                text-align:center;
                box-shadow:0 1px 3px rgba(0,0,0,.08);
            ">

                <div style="
                    font-size:14px;
                    color:#646970;
                    margin-bottom:10px;
                ">
                    <?php echo esc_html($title); ?>
                </div>

                <div style="
                    font-size:30px;
                    font-weight:700;
                    color:#2271b1;
                ">
                    <?php echo esc_html($value); ?>
                </div>

            </div>

        <?php endforeach; ?>

    </div>
    

</div>

<div class="postbox" style="width:100%;margin-top:20px;">

    <h2 style="padding:15px;">👥 Campaign Recipients</h2>

    <form method="get" style="margin:15px;">

    <input type="hidden" name="page" value="pushpa-campaign-details">
    <input type="hidden" name="id" value="<?php echo absint($campaign_id); ?>">

    <input
        type="search"
        name="search"
        value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>"
        placeholder="Search by email..."
        style="width:300px;">

    <input
        type="submit"
        class="button button-primary"
        value="Search">
        <a
    href="<?php echo wp_nonce_url(
        admin_url(
            'admin-post.php?action=pem_export_campaign_csv&id=' . absint($campaign_id)
        ),
        'pem_export_campaign_csv'
    ); ?>"
    class="button"
    style="margin-left:10px;">
    📥 Export CSV
</a>
<a
    href="<?php echo wp_nonce_url(
        admin_url(
            'admin-post.php?action=pem_resend_failed&id=' . absint($campaign_id)
        ),
        'pem_resend_failed'
    ); ?>"
    class="button button-secondary"
    style="margin-left:10px;">
    🔄 Resend Failed
</a>

</form>

    <table class="widefat striped">

        <thead>

            <tr>
                <th>#</th>
                <th>Email</th>
                <th>Status</th>
                <th>Opened</th>
                <th>Clicked</th>
                <th>Unsubscribed</th>
                <th>Sent At</th>
            </tr>

        </thead>

        <tbody>

        <?php if (!empty($recipients)) : ?>

            <?php $i = 1; ?>

            <?php foreach ($recipients as $row) : ?>

                <tr>

                    <td><?php echo esc_html($i++); ?></td>

                    <td>
    <a href="mailto:<?php echo esc_attr($row->email); ?>">
        <?php echo esc_html($row->email); ?>
    </a>
</td>

                    <td>

<?php

if ($row->status === 'Success') {

    echo '<span style="
        background:#d1fae5;
        color:#065f46;
        padding:4px 10px;
        border-radius:20px;
        font-size:12px;
        font-weight:600;
    ">Success</span>';

} elseif ($row->status === 'Failed') {

    echo '<span style="
        background:#fee2e2;
        color:#991b1b;
        padding:4px 10px;
        border-radius:20px;
        font-size:12px;
        font-weight:600;
    ">Failed</span>';

} else {

    echo esc_html($row->status);

}

?>

</td>

                    <td>
                        <?php echo !empty($row->opened_at) ? '✅ Yes' : '❌ No'; ?>
                    </td>

                    <td>
                        <?php echo !empty($row->clicked_at) ? '✅ Yes' : '❌ No'; ?>
                    </td>

                    <td>
                        <?php echo $row->is_unsubscribed ? '✅ Yes' : '❌ No'; ?>
                    </td>

                    <td>
    <?php
    echo esc_html(
        wp_date(
            'd M Y h:i A',
            strtotime($row->sent_at)
        )
    );
    ?>
</td>
                </tr>

            <?php endforeach; ?>

        <?php else : ?>

            <tr>

                <td colspan="7" style="text-align:center;">
                    No recipients found.
                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

    <?php

if ($total_pages > 1) {

    echo '<div style="padding:15px;">';

    echo paginate_links([
        'base' => add_query_arg('paged', '%#%'),
        'format' => '',
        'current' => $paged,
        'total' => $total_pages,
        'prev_text' => '&laquo; Previous',
        'next_text' => 'Next &raquo;',
    ]);

    echo '</div>';
}
?>

</div>

</div>


