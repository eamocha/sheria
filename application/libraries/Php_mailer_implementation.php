<?php

include_once COREPATH.'libraries/Mailer_Interface.php';
require COREPATH.'libraries/PHPMailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Php_Mailer_Implementation implements Mailer_Interface 
{
    private $mailer;
    public $ci = null;

    public function __construct($system_preferences) {

        $this->ci = &get_instance();
        $this->mailer = new PHPMailer();

        //Server settings
        $this->mailer->isSMTP();
        $this->mailer->SMTPDebug = 2;
        $this->mailer->Debugoutput = "error_log";
        $this->mailer->Host = $system_preferences['outgoingMailSmtpHost'];                    
        $this->mailer->SMTPAuth = true;
        if(!empty($system_preferences['outgoingMailSmtpNameUser'])){
            $this->mailer->Username = $system_preferences['outgoingMailSmtpNameUser'];
        }
        if(!empty($system_preferences['outgoingMailSmtpPass'])){
            $this->ci->load->library('encryption');
            $this->mailer->Password = $this->ci->encryption->decrypt($system_preferences['outgoingMailSmtpPass']);
        }
        $this->mailer->SMTPSecure = $system_preferences['outgoingMailSmtpEncryption'];
        $this->mailer->Port       = $system_preferences['outgoingMailSmtpPort'];
        $this->mailer->Timeout = $system_preferences['outgoingMailTimeout'];
        $this->mailer->ContentType = PHPMailer::CONTENT_TYPE_TEXT_HTML;
        $this->mailer->CharSet = PHPMailer::CHARSET_UTF8;
    }

    public function set_from($address, $name){
        $this->mailer->setFrom($address, $name);
    }
    public function set_address($to){
        $to = is_array($to)? $to: array($to);
        $this->mailer->clearAddresses();
        foreach($to as $address){
            $this->mailer->addAddress($address);
        }
    }
    public function add_cc($cc){
        $cc = is_array($cc)? $cc: array($cc);
        $this->mailer->clearCCs();
        foreach($cc as $address){
            $this->mailer->addCC($address);
        }
    }
    public function set_subject($subject){
        $this->mailer->Subject = $subject;
    }
    public function set_message($content){
        $this->mailer->Body = $content;
    }
    public function add_attachment($attachment_path, $file_name){
        $this->mailer->addAttachment($attachment_path, $file_name);
    }
    public function add_reply_to($email, $name){
        $this->mailer->addReplyTo($email, $name);
    }
    public function send_mail(){
        if (!@$this->mailer->send()) {
            return false;
        }
        return true;
    }
}