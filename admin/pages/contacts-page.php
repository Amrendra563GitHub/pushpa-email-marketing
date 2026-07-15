<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$table = $wpdb->prefix . 'pushpa_contacts';

$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$group  = isset($_GET['group']) ? sanitize_text_field($_GET['group']) : '';

$where = " WHERE 1=1 ";
$args = array();

if (!empty($search)) {

    $where .= " AND (
        name LIKE %s
        OR email LIKE %s
        OR phone LIKE %s
        OR company LIKE %s
    )";

    $like = "%{$search}%";

    $args[] = $like;
    $args[] = $like;
    $args[] = $like;
    $args[] = $like;
}

if (!empty($group)) {

    $where .= " AND contact_group=%s";

    $args[] = $group;
}

$sql = "SELECT * FROM $table $where ORDER BY id DESC";

if (!empty($args)) {

    $contacts = $wpdb->get_results(
        $wpdb->prepare($sql, ...$args)
    );

} else {

    $contacts = $wpdb->get_results($sql);

}

$total_contacts = PEM_Contact::count();

$groups = array(
    'General',
    'Customers',
    'Students',
    'Employees',
    'Clients',
    'VIP'
);

?>

<div class="wrap">

<h1 class="wp-heading-inline">📒 Contacts</h1>

<a
href="<?php echo admin_url('admin.php?page=pushpa-add-contact'); ?>"
class="page-title-action">

Add New

</a>

<hr class="wp-header-end">

<div style="
margin:20px 0;
padding:18px;
background:#fff;
border-left:4px solid #2271b1;
max-width:250px;
">

<h2 style="margin:0;font-size:30px;">

<?php echo esc_html($total_contacts); ?>

</h2>

<p>Total Contacts</p>

</div>

<form method="get" style="margin-bottom:20px;">

<input
type="hidden"
name="page"
value="pushpa-contacts">

<input
type="text"
name="s"
class="regular-text"
placeholder="Search..."
value="<?php echo esc_attr($search); ?>">

<select
name="group"
class="regular-text">

<option value="">All Groups</option>

<?php foreach ($groups as $g) : ?>

<option
value="<?php echo esc_attr($g); ?>"
<?php selected($group, $g); ?>>

<?php echo esc_html($g); ?>

</option>

<?php endforeach; ?>

</select>

<input
type="submit"
class="button"
value="Filter">

</form>

<table class="wp-list-table widefat striped">

<thead>

<tr>

<th>ID</th>

<th>Name</th>

<th>Email</th>

<th>Phone</th>

<th>Company</th>

<th>Group</th>

<th>Status</th>

<th>Created</th>

<th width="170">Actions</th>

</tr>

</thead>

<tbody>

<?php if ($contacts) : ?>

<?php foreach ($contacts as $contact) : ?>

<tr>

<td><?php echo esc_html($contact->id); ?></td>

<td><?php echo esc_html($contact->name); ?></td>

<td><?php echo esc_html($contact->email); ?></td>

<td><?php echo esc_html($contact->phone); ?></td>

<td><?php echo esc_html($contact->company); ?></td>

<td>

<strong>

<?php echo esc_html($contact->contact_group ?? 'General'); ?>

</strong>

</td>

<td>

<?php

$status = $contact->status;

$color = '#999';

if ($status == 'Active') {
    $color = 'green';
}

if ($status == 'Inactive') {
    $color = '#f39c12';
}

if ($status == 'Unsubscribed') {
    $color = '#e74c3c';
}

?>

<span style="
background:<?php echo esc_attr($color); ?>;
color:#fff;
padding:4px 10px;
border-radius:20px;
font-size:12px;">

<?php echo esc_html($status); ?>

</span>

</td>

<td><?php echo esc_html($contact->created_at); ?></td>

<td>

<a
href="<?php echo admin_url('admin.php?page=pushpa-add-contact&id=' . $contact->id); ?>"
class="button button-small">

Edit

</a>

<a
href="<?php echo wp_nonce_url(
admin_url('admin.php?page=pushpa-contacts&action=delete&id=' . $contact->id),
'pem_delete_contact_' . $contact->id
); ?>"
class="button button-small button-link-delete"
onclick="return confirm('Delete this contact?');">

Delete

</a>

</td>

</tr>

<?php endforeach; ?>

<?php else : ?>

<tr>

<td colspan="9" style="text-align:center;">

No Contacts Found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>