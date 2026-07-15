<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_Mailer
{
    /**
     * Replace Merge Tags
     */
    public static function parseTemplate($message, $contact = null)
    {
        if (!$contact) {
            return $message;
        }

        $replace = array(
            '{{name}}'    => $contact->name ?? '',
            '{{email}}'   => $contact->email ?? '',
            '{{phone}}'   => $contact->phone ?? '',
            '{{company}}' => $contact->company ?? '',
        );

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $message
        );
    }

    /**
     * Send Email
     */
    public static function send($to, $subject, $message, $contact = null)
{
    // Merge Tags Replace
    $message = self::parseTemplate($message, $contact);

    global $wpdb;

    $smtp = $wpdb->get_row(
        "SELECT * FROM {$wpdb->prefix}pushpa_smtp
        WHERE status='Active'
        LIMIT 1"
    );

    $headers = array(
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8'
    );

    if ($smtp) {

        $headers[] =
            'From: ' .
            $smtp->from_name .
            ' <' .
            $smtp->from_email .
            '>';

        if (!empty($smtp->reply_to)) {

            $headers[] =
                'Reply-To: ' .
                $smtp->reply_to;
        }
    }

    return wp_mail(
        $to,
        $subject,
        $message,
        $headers
    );
}
}