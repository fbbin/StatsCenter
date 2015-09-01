<?php
namespace Apps;

require __DIR__.'/PHPMailer/PHPMailerAutoload.php';
class Mail
{
    private $host = "smtp.qq.com";
    private $username = 'report@chelun.com';
    private $password = '123qwe';
    private $secure = 'ssl';
    private $port = 465;

    static public $debug = 'off';

    public $mail;
    public function __construct()
    {
        $this->mail = new \PHPMailer;
        if (self::$debug == 'on')
            $this->mail->SMTPDebug = 3;
        $this->mail->isSMTP();
        $this->mail->Host = $this->host;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->username;
        $this->mail->Password = $this->password;
        $this->mail->SMTPSecure = $this->secure;
        $this->mail->Port = $this->port;
        $this->mail->CharSet = "utf-8";

        $this->mail->From = 'report@chelun.com';
        $this->mail->FromName = 'reporter';
        $this->mail->isHTML(true);
    }

    function mail($address, $subject, $body, $cc='', $attach='')
    {
        if (!empty($address))
        {
            if (is_array($address))
            {
                foreach ($address as $ad)
                {
                    $this->mail->addAddress($ad);
                }
            }
            else
            {
                $this->mail->addAddress($address);
            }
        }
        else
        {
            return 9000;
        }

        if (!empty($cc))
        {
            if (is_array($cc))
            {
                foreach ($cc as $c)
                {
                    $this->mail->addCC($c);
                }
            }
            else
            {
                $this->mail->addCC($cc);
            }
        }

        if (!empty($subject))
        {
            $this->mail->Subject = $subject;
        }
        else
        {
            return 9002;
        }
        if (!empty($body))
        {
            $this->mail->Body    = $body;
        }
        else
        {
            return 9003;
        }

        if (!empty($attach))
            $this->mail->addAttachment($attach);
        return $this->mail->send();
    }
}



