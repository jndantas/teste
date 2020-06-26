<?php
namespace Simcify;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer {}

class Mail {

    /**
     * Send an email
     * 
     * @param   string          $view_name
     * @param   string|array    $to
     * @param   string          $subject
     * @param   mixed           $from
     * @param   array           $attachments
     * @param   \PDO            $pdo
     * 
     * @return  void
     */
        public static function send($to, $subject, array $view_data, $view_name = "basic", $from = null, array $attachments = []) {
        $mail = new PHPMailer;
        $mail->SMTPDebug = 0;
        if (env("SMTP_AUTH")) {
            $mail->isSMTP();
        }
        $mail->Host = env('SMTP_HOST');
        $mail->SMTPAuth = env("SMTP_AUTH");
        $mail->Username = env('MAIL_USERNAME');
        $mail->Password = env('SMTP_PASSWORD');
        $mail->SMTPSecure = env('MAIL_ENCRYPTION');
        $mail->Port = env('SMTP_PORT');
        $view_data = array_merge($view_data, array(
                                "appurl" => env('APP_URL'),
                                "applogo" => env('APP_URL')."/uploads/app/".env('APP_LOGO'),
                                "copyright" => "&copy; ".date("Y")." ".env("APP_NAME")." | All Rights Reserved."
                            ));

        if (is_null($from)) {
            $from = config('mail.from');
        }
        list($from_name, $from_email) = array_merge(explode(' <', str_replace('>', '', $from)), [null]);
        $from_email = is_null($from_email) ? $from_name : $from_email;
        $mail->setFrom($from_email, $from_name);

        if (is_string($to)) {
            $to = array('To' => array($to));
        }
        if (!isset($to['Cc'])) {
            $to['Cc'] = array();
        }
        if (!isset($to['Bcc'])) {
            $to['Bcc'] = array();
        }
        foreach ($to['To'] as $recipient) {
            list($to_name, $to_email) = array_merge(explode(' <', str_replace('>', '', $recipient)), [null]);
            $to_email = is_null($to_email) ? $to_name : $to_email;
            $mail->addAddress($to_email, $to_name);
        }
        foreach ($to['Cc'] as $recipient) {
            list($cc_name, $cc_email) = array_merge(explode(' <', str_replace('>', '', $recipient)), [null]);
            $cc_email = is_null($cc_email) ? $cc_name : $cc_email;
            $mail->addCC($cc_email, $cc_name);
        }
        foreach ($to['Bcc'] as $recipient) {
            list($bcc_name, $bcc_email) = array_merge(explode(' <', str_replace('>', '', $recipient)), [null]);
            $bcc_email = is_null($bcc_email) ? $bcc_name : $bcc_email;
            $mail->addBCC($bcc_email, $bcc_name);
        }

        foreach ($attachments as $key => $value) {
            if (is_numeric($key)) {
                $mail->addAttachment($value); 
            } else {
                $mail->addAttachment($value, $key);
            }
        }
            
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = view("emails/html/{$view_name}", $view_data);

        return $mail->send(); 

    }
}
