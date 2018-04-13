<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

class Mailer {

    /**
     * @var InstanceManager
     */
    private $im;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    private $addresses = [];
    private $vars = [];

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->config = $im->get('config');
        $this->view = $im->get('view');
        $this->logger = $im->get('logger');
    }
    
    public function init() {
        $this->addresses = [];
    }

    public function addAddress($email, $name = null) {
        $this->addresses[] = [
            'email' => $email,
            'name' => $name
        ];
    }

    public function set($name, $value) {
        $this->vars[$name] = $value;
    }

    public function send($subject, $templatePath) {
        $body = $this->view->fetch($templatePath, $this->vars);
        $result = true;
        if ($this->config->get('mailer.fake')) {
            $this->fakeSend($subject, $body);
        } else {
            $result = $this->realSend($subject, $body);
        }
        return $result;
    }

    private function fakeSend($subject, $body) {
        $to = [];
        foreach ($this->addresses as $address) {
            $to[] = $address['name'].' <'.$address['email'].'>';
        }        
        $message = "Fake mail sending\r\n";
        $message .= 'To: '.join('; ', $to)."\r\n";
        $message .= 'Subject: '.$subject."\r\n";
        $message .= "Message:\r\n".$body."\r\n";
        $this->logger->info($message);
        return true;
    }

    private function realSend($subject, $body) {
        $result = true;
        $mail = new PHPMailer(true);
        if (!$this->config->get('mailer.verify_ssl')) {
            $this->disableVerify($mail);
        }
        $this->setDefaults($mail);
        $this->addAddresses($mail);
        $mail->Subject = '=?utf-8?Q?'.quoted_printable_encode($subject).'?=';
        $mail->Body = $body;
        try {
            $mail->send();
        } catch (PHPMailerException $e) {
            $this->logger->error($e->getMessage());
            $result = false;
        }
        return $result;
    }

    private function disableVerify(PHPMailer $mail) {
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
    }

    private function setDefaults(PHPMailer $mail) {
        $mail->isHTML(true);
        //$mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = $this->config->get('mailer.host');
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->get('mailer.username');
        $mail->Password = $this->config->get('mailer.password');
        $mail->SMTPSecure = 'ssl';
        $mail->Port = $this->config->get('mailer.port');
        $mail->Encoding = 'quoted-printable';
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($this->config->get('mailer.from.email'), $this->config->get('mailer.from.name'));
    }

    private function addAddresses(PHPMailer $mail) {
        foreach ($this->addresses as $address) {
            $mail->addAddress($address['email'], $address['name']);
        }
    }

}