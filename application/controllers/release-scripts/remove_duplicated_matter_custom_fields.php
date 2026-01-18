<?php
// custom script for DIB only
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class Remove_duplicated_matter_custom_fields extends CI_Controller
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
        $this->remove_duplicated_matter_custom_field_values();
    }
    
    public function remove_duplicated_matter_custom_field_values()
    {
        if ($this->db->dbdriver === 'sqlsrv') {
            $this->write_log($this->log_path, 'start removing duplicated matter custom field values');
            $recordIds = ['2665', '899', '971', '1606', '1776', '1788', '1827', '1834', '2000', '2085', '2260', '2665'];
            foreach($recordIds as $recordId){
                $query = $this->db->query("SELECT custom_field_id, count(*) occurrences FROM custom_field_values where recordId  = '{$recordId}' group by custom_field_id having COUNT(*) > 1");
                $duplicated_values = $query->result_array();
                foreach ($duplicated_values as $value){
                    for($i=2; $i<= $value['occurrences']; $i++){
                        $this->db->query("delete from custom_field_values where id = (select top 1 id from custom_field_values where recordId = '{$recordId}' and custom_field_id = '{$value['custom_field_id']}' order by id desc)");
                    }
                }
            }
            $this->write_log($this->log_path, 'done from custom fields');
        }
    }
}
