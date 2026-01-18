<?php

include_once COREPATH.'libraries/Mailer_Interface.php';
include_once BASEPATH.'libraries/Email.php';

class Ci_Mailer_Implementation implements Mailer_Interface
{

    private $mailer;
    public $ci = null;
    public $log_path = null;

    public function __construct($system_preferences) {
        $this->ci = &get_instance();
        $this->log_path = 'email' . DIRECTORY_SEPARATOR . date('Y-m-d');

        // Server settings
        $config = array();
        $config['protocol'] = 'smtp';
        $config['smtp_crypto'] = $system_preferences['outgoingMailSmtpEncryption'];
        $config['smtp_host'] = $system_preferences['outgoingMailSmtpHost'];
        if(!empty($system_preferences['outgoingMailSmtpNameUser'])){
            $config['smtp_user'] = $system_preferences['outgoingMailSmtpNameUser'];
        }

        if(!empty($system_preferences['outgoingMailSmtpPass'])){
            $this->ci->load->library('encryption');
            $config['smtp_pass'] = $this->ci->encryption->decrypt($system_preferences['outgoingMailSmtpPass']);
        }
        $config['smtp_port'] = $system_preferences['outgoingMailSmtpPort'];
        $config['smtp_timeout'] = $system_preferences['outgoingMailTimeout'];
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $config['newline'] = "\r\n";
        $this->mailer = new CI_Email($config);
        $this->mailer->set_crlf("\r\n");
    }

    public function set_from($address, $name){
        $this->mailer->from($address, $name);
    }
    public function set_address($to){
        $this->mailer->to($to);
    }
    public function add_cc($cc){
        $this->mailer->cc($cc);
    }
    public function set_subject($subject){
        $this->mailer->subject(htmlentities($subject));
    }
    public function set_message($content){
        $this->mailer->message($content);
    }
    public function add_attachment($attachment_path, $file_name){
        $this->ci->load->helper('file');
        $mime = get_mime_by_extension($attachment_path);
        $this->mailer->attach($attachment_path, array('mime' => $mime), $file_name);
    }
    public function add_reply_to($email, $name)
    {
        $this->mailer->reply_to($email, $name);
    }
    public function send_mail(){
        if (!@$this->mailer->send()) {
            $this->write_email_log($this->log_path, $this->mailer->print_debugger([]));
            return false;
        }
        return true;
    }
    private function write_email_log($file_path, $message, $type = 'error')
    {
      $log_path = $this->ci->config->item('files_path') . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $file_path . '.log';
      $pr = fopen($log_path, 'a');
      fwrite($pr, date('Y-m-d H:i:s') . " [$type] - $message \n");
      fclose($pr);
    }
}