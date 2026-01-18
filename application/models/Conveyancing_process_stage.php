<?php
class Conveyancing_process_stage extends My_Model_Factory
{
}

class mysqli_Conveyancing_process_stage extends My_model
{
    protected $_table = 'conveyancing_process_stages';
    protected $modelName = "conveyancing_process_stage";

    protected $_fieldsNames = ['id', 'name', 'description', 'sequence_order', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();
        $validate=[
            "id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "name" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "sequence_order" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "is_active" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "description" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]];
    }

    public function load_list_stages()
    {
        $language = strtolower(substr($this->ci->session->userdata("AUTH_language"), 0, 2));
        $query = ["select" => "id, name,sequence_order,is_active "];
        return $this->load_all($query);

    }
}

class mysql_Conveyancing_process_stage extends mysqli_Conveyancing_process_stage
{

}

class sqlsrv_Conveyancing_process_stage extends mysql_Conveyancing_process_stage
{
    /*get all steps ordered*/
    public function get_ordered_stage_ids()
    {
        $query = $this->ci->db->query("SELECT id FROM conveyancing_process_stages ORDER BY sequence_order ASC");

        $stage_ids = [];
        foreach ($query->result() as $row) {
            $stage_ids[] = $row->id;
        }

        return $stage_ids;
    }

    public function get_next_stage_id($current_stage_id)
    {
        // Get current stage's sequence order
        $query = $this->ci->db->query(" SELECT TOP 1 sequence_order     FROM conveyancing_process_stages   WHERE id = ? ", [$current_stage_id]);
        $current = $query->row();

        if (!$current) {
            return null;
        }

        // Find the next stage with higher sequence order
        $query = $this->ci->db->query(" SELECT TOP 1 id  FROM conveyancing_process_stages WHERE sequence_order > ? 
        ORDER BY sequence_order ASC ", [$current->sequence_order]);
        $next = $query->row();
        return $next ? $next->id : null;
    }
}
