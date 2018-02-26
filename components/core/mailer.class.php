<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

class Mailer {

    private $config;

    public function __construct($im) {
        $this->config = $im->get('config');
    }

    public function send($toEmail = 'gopher.hu@gmail.com') {
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            $mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host = $this->config->get('mailer.host');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('mailer.username');
            $mail->Password = $this->config->get('mailer.password');
            //$mail->SMTPSecure = 'tls';
            $mail->Port = $this->config->get('mailer.port');
            $mail->setFrom($this->config->get('mailer.from.email'), $this->config->get('mailer.from.name'));
            $mail->addAddress($toEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
        } catch (PHPMailerException $e) {
            // TODO: logging
            return false;
        }
        return true;
    }

}