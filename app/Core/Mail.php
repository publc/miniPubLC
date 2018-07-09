<?php

namespace App\Core;

use App\Core\Config;
use App\Core\Container;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    protected $container;

    protected $mail;

    public function __construct()
    {
        $this->container = new Container([
            'config' => function () {
                return new Config;
            }
        ]);

        $this->mail = new PHPMailer;
        $this->config();
    }

    public function send($mail, $subject, $template = 'default', $attachment = null)
    {
        $this->mail->addAddress($mail);
        $this->mail->Subject = $subject;
        $message = $this->getMessage($template);
        $this->mail->msgHTML($message);
        $this->mail->AltBody = $message;
        if (!is_null($attachment)) {
            $this->mail->addAttachment($attachment);
        }
        return $this->mail->send();
    }

    protected function config()
    {
        $config = $this->container->config->get('mail');
        $this->mail->IsSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->Host = $config->host;
        $this->mail->Port = $config->port;
        $this->mail->SMTPSecure = $config->secure;
        $this->mail->Username = $config->user;
        $this->mail->Password = $config->pass;
        $this->mail->SMTPDebug = 0;
        $this->mail->setFrom($config->from);
    }

    protected function getMessage($template)
    {
        ob_start();
        $config = $this->container->config->get('app');
        require '../source/views/mail/' . $template . '.template.php';
        $data = ob_get_clean();
        return $data;
    }
}
