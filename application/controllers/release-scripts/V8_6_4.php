<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_6_4 extends CI_Controller
{
    use MigrationLogTrait;

    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }

    public function index()
    {
        $this->update_case_comments_emails();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function update_case_comments_emails()
    {
        $this->write_log($this->log_path, 'Started update case comments emails to change type of email_to to text instead of varchar 255');
        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query("ALTER TABLE case_comments_emails ALTER COLUMN email_to text NOT NULL;");
        } else {
            $this->db->query("ALTER TABLE `case_comments_emails` MODIFY `email_to` text NOT NULL;");
        }
        $this->write_log($this->log_path, 'Done update case comments emails to change type of email_to to text instead of varchar 255');
    }
}
