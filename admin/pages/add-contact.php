<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$table = $wpdb->prefix . 'pushpa_contacts';

$id = isset($_GET['id']) ? absint($_GET['id']) : 0;

$contact = null;

if ($id > 0) {

    $contact = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table WHERE id=%d",
            $id
        )
    );
}

?>

<div class="wrap">

<h1>

<?php echo $id ? 'Edit Contact' : 'Add New Contact'; ?>

</h1>

<?php if (isset($_GET['error'])) : ?>

<div class="notice notice-error is-dismissible">

<p><?php echo esc_html(urldecode($_GET['error'])); ?></p>

</div>

<?php endif; ?>

<form method="post" autocomplete="off">

<?php wp_nonce_field('pem_save_contact_nonce', 'pem_nonce'); ?>

<input
type="hidden"
name="contact_id"
value="<?php echo esc_attr($id); ?>">

<table class="form-table">

<tr>

<th>

Name <span style="color:red;">*</span>

</th>

<td>

<input
type="text"
name="name"
class="regular-text"
required
value="<?php echo esc_attr($contact->name ?? ''); ?>">

</td>

</tr>

<tr>

<th>Email</th>

<td>

<input
type="email"
name="email"
class="regular-text"
value="<?php echo esc_attr($contact->email ?? ''); ?>">

</td>

</tr>

<tr>

<th>Phone</th>

<td>

<input
type="text"
name="phone"
class="regular-text"
value="<?php echo esc_attr($contact->phone ?? ''); ?>">

</td>

</tr>

<tr>

<th>Company</th>

<td>

<input
type="text"
name="company"
class="regular-text"
value="<?php echo esc_attr($contact->company ?? ''); ?>">

</td>

</tr>

<tr>

<th>Group</th>

<td>

<select
name="contact_group"
class="regular-text">

<?php

$groups = array(
'General',
'Customers',
'Students',
'Employees',
'Clients',
'VIP'
);

foreach ($groups as $group) :

?>

<option
value="<?php echo esc_attr($group); ?>"
<?php selected($contact->contact_group ?? 'General', $group); ?>>

<?php echo esc_html($group); ?>

</option>

<?php endforeach; ?>

</select>

</td>

</tr>

<tr>

<th>Status</th>

<td>

<select
name="status"
class="regular-text">

<option
value="Active"
<?php selected($contact->status ?? 'Active', 'Active'); ?>>

Active

</option>

<option
value="Inactive"
<?php selected($contact->status ?? '', 'Inactive'); ?>>

Inactive

</option>

<option
value="Unsubscribed"
<?php selected($contact->status ?? '', 'Unsubscribed'); ?>>

Unsubscribed

</option>

</select>

</td>

</tr>

</table>

<p>

<input
type="submit"
name="pem_save_contact"
class="button button-primary"
value="<?php echo $id ? 'Update Contact' : 'Save Contact'; ?>">

<a
href="<?php echo admin_url('admin.php?page=pushpa-contacts'); ?>"
class="button">

Back

</a>

</p>

</form>

</div>