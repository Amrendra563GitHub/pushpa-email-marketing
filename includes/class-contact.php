<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Contact
{
    /**
     * Table Name
     */
    private static function table()
    {
        global $wpdb;

        return $wpdb->prefix . 'pushpa_contacts';
    }

    /**
     * Get All Contacts
     */
    public static function getAll()
    {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * FROM " . self::table() . " ORDER BY id DESC"
        );
    }

    /**
     * Total Contacts
     */
    public static function count()
    {
        global $wpdb;

        return (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::table()
        );
    }

    /**
     * Get Contact By ID
     */
    public static function getById($id)
    {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . self::table() . " WHERE id=%d",
                absint($id)
            )
        );
    }

    /**
     * Check Duplicate Email
     */
    public static function emailExists($email, $exclude_id = 0)
    {
        global $wpdb;

        $email = sanitize_email($email);

        if (empty($email)) {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM " . self::table() . " WHERE email=%s";

        if ($exclude_id > 0) {

            $sql .= " AND id!=%d";

            return (int) $wpdb->get_var(
                $wpdb->prepare(
                    $sql,
                    $email,
                    absint($exclude_id)
                )
            ) > 0;
        }

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                $sql,
                $email
            )
        ) > 0;
    }

    /**
     * Create Contact
     */
    public static function create($data)
    {
        global $wpdb;

        $contact = array(

            'name'          => sanitize_text_field($data['name'] ?? ''),
            'email'         => sanitize_email($data['email'] ?? ''),
            'phone'         => sanitize_text_field($data['phone'] ?? ''),
            'company'       => sanitize_text_field($data['company'] ?? ''),
            'contact_group' => sanitize_text_field($data['contact_group'] ?? 'General'),
            'status'        => sanitize_text_field($data['status'] ?? 'Active')

        );

        if (empty($contact['name']) || empty($contact['email'])) {
            return new WP_Error(
                'required_fields',
                'Name and Email are required.'
            );
        }

        if (!is_email($contact['email'])) {
            return new WP_Error(
                'invalid_email',
                'Please enter a valid email.'
            );
        }

        if (self::emailExists($contact['email'])) {
            return new WP_Error(
                'duplicate_email',
                'This email already exists.'
            );
        }

        return $wpdb->insert(
            self::table(),
            $contact,
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
    }

    /**
     * Update Contact
     */
    public static function update($id, $data)
    {
        global $wpdb;

        $id = absint($id);

        $contact = array(

            'name'          => sanitize_text_field($data['name'] ?? ''),
            'email'         => sanitize_email($data['email'] ?? ''),
            'phone'         => sanitize_text_field($data['phone'] ?? ''),
            'company'       => sanitize_text_field($data['company'] ?? ''),
            'contact_group' => sanitize_text_field($data['contact_group'] ?? 'General'),
            'status'        => sanitize_text_field($data['status'] ?? 'Active')

        );

        if (empty($contact['name']) || empty($contact['email'])) {
            return new WP_Error(
                'required_fields',
                'Name and Email are required.'
            );
        }

        if (!is_email($contact['email'])) {
            return new WP_Error(
                'invalid_email',
                'Please enter a valid email.'
            );
        }

        if (self::emailExists($contact['email'], $id)) {
            return new WP_Error(
                'duplicate_email',
                'This email already exists.'
            );
        }

        return $wpdb->update(
            self::table(),
            $contact,
            array(
                'id' => $id
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            ),
            array('%d')
        );
    }

    /**
     * Delete Contact
     */
    public static function delete($id)
    {
        global $wpdb;

        return $wpdb->delete(
            self::table(),
            array(
                'id' => absint($id)
            ),
            array('%d')
        );
    }

    /**
     * Get Active Contacts By Group
     */
    public static function getActiveByGroup($group = '')
    {
        global $wpdb;

        if (empty($group) || $group === 'All') {

            return $wpdb->get_results(
                "SELECT *
                FROM " . self::table() . "
                WHERE status='Active'
                ORDER BY id ASC"
            );
        }

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT *
                FROM " . self::table() . "
                WHERE status='Active'
                AND contact_group=%s
                ORDER BY id ASC",
                sanitize_text_field($group)
            )
        );
    }

    /**
     * Get Contacts By Group
     */
    public static function getByGroup($group)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT *
                FROM " . self::table() . "
                WHERE contact_group=%s
                ORDER BY name ASC",
                sanitize_text_field($group)
            )
        );
    }

    /**
     * Count Contacts By Group
     */
    public static function countByGroup($group)
    {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                FROM " . self::table() . "
                WHERE contact_group=%s",
                sanitize_text_field($group)
            )
        );
    }

}