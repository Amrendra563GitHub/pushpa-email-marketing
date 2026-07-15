<?php

if (!defined('ABSPATH')) {
    exit;
}

class PEM_SMTP
{
    /**
     * Configure PHPMailer
     */
    public static function init($phpmailer)
    {
        global $wpdb;

        $smtp = $wpdb->get_row(
            "SELECT * FROM {$wpdb->prefix}pushpa_smtp
            WHERE status='Active'
            LIMIT 1"
        );

        if (!$smtp) {
            return;
        }

        $phpmailer->isSMTP();

        $phpmailer->Host       = $smtp->smtp_host;
        $phpmailer->Port       = (int) $smtp->smtp_port;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Username   = $smtp->smtp_username;
        $phpmailer->Password   = $smtp->smtp_password;

        if ($smtp->smtp_encryption == 'ssl') {

            $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;

        } elseif ($smtp->smtp_encryption == 'tls') {

            $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

        }

        $phpmailer->From     = $smtp->from_email;
        $phpmailer->FromName = $smtp->from_name;

        if (!empty($smtp->reply_to)) {

            $phpmailer->addReplyTo(
                $smtp->reply_to,
                $smtp->from_name
            );
        }

        $phpmailer->CharSet = 'UTF-8';

        $phpmailer->isHTML(true);
    }
}

add_action('phpmailer_init', array('PEM_SMTP', 'init'));