<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_5_1 extends CI_Controller
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
        $this->delete_voucher_related_cases_duplicates();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function delete_voucher_related_cases_duplicates()
    {
        $this->write_log($this->log_path, 'delete_voucher_related_cases_duplicates started', 'info');
        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query( "DELETE
                FROM voucher_related_cases
                WHERE id NOT IN
                (
                    SELECT MIN(id)
                    FROM voucher_related_cases
                    GROUP BY legal_case_id,voucher_header_id
                );");
        } else{
            $this->db->query("DELETE FROM voucher_related_cases
                WHERE voucher_related_cases.id NOT IN(
                    SELECT * FROM (
                    SELECT MIN(vrc.id)
                    FROM voucher_related_cases vrc
                    GROUP BY vrc.legal_case_id,vrc.voucher_header_id
                ) res
            );");
        }
        
        $this->write_log($this->log_path, 'delete_voucher_related_cases_duplicates is done', 'info');
    }
}
