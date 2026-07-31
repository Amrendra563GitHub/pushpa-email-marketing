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
 * Replace Links with Click Tracking Links
 */
public static function parseLinks($message, $log_id = 0)
{
    error_log('============= PARSE LINKS =============');
    error_log('LOG ID = ' . $log_id);
    error_log($message);

    if ($log_id <= 0) {
        return $message;
    }

    $message = preg_replace_callback(
        '/<a\s+[^>]*href=["\']([^"\']+)["\']/i',
        function ($matches) use ($log_id) {

            $original = $matches[1];

            error_log('FOUND LINK = ' . $original);

            $tracking = add_query_arg(
                array(
                    'pem_click' => $log_id,
                    'url'       => rawurlencode($original)
                ),
                home_url('/')
            );

            error_log('TRACKING LINK = ' . $tracking);

            return str_replace(
                $original,
                esc_url($tracking),
                $matches[0]
            );

        },
        $message
    );

    error_log('FINAL HTML = ');
    error_log($message);

    return $message;
}

    /**
     * Send Email
     */
    public static function send(
        $to,
        $subject,
        $message,
        $contact = null,
        $log_id = 0
    ) {

        // Replace Merge Tags
        $message = self::parseTemplate($message, $contact);
        // Replace Links
        $message = self::parseLinks($message, $log_id);

        global $wpdb;

        $smtp = $wpdb->get_row(
            "SELECT *
            FROM {$wpdb->prefix}pushpa_smtp
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
        error_log('MAILER LOG ID = ' . $log_id);

        /*
        |--------------------------------------------------------------------------
        | Email Open Tracking Pixel
        |--------------------------------------------------------------------------
        */

        error_log('PEM LOG ID = ' . $log_id);

        if ($log_id > 0) {

            $pixel = add_query_arg(
                array(
                    'pem_open' => $log_id
                ),
                home_url('/')
            );

            $message .=
                '<img src="' .
                esc_url($pixel) .
                '" width="1" height="1" style="display:none;" alt="">';

        }

        return wp_mail(
            $to,
            $subject,
            $message,
            $headers
        );
    }
}