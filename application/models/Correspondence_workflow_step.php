<?php
class Correspondence_workflow_step extends My_Model_Factory
{
}
class mysqli_Correspondence_workflow_step extends My_model
{
    protected $_table = 'correspondence_workflow_steps';
    protected $modelName = "correspondence_workflow_step";
    protected $_fieldsNames = ['id', 'name', 'correspondence_type_id', 'sequence_order', 'comment', 'createdOn', 'modifiedOn', 'createdBy', 'modifiedBy'];

    protected $primaryKey = 'id';
    public function __construct()
    {
        $this->ci =& get_instance();
        parent::__construct();


        $validate = [
            "id" => [
                "required" => false, // ID is auto-incremented on insert
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
            "correspondence_type_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["numeric"], // Ensure it's a numeric ID
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "sequence_order" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["numeric"], // Ensure it's a numeric order
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "comment" => [
                "required" => false,
                "allowEmpty" => true,
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],

        ];
    }

    /**
     * Example method to load all workflow steps.
     * @return array An array of all workflow steps.
     */
    public function load_all_steps()
    {
        $query = ["select" => "id, name as step_name, correspondence_type_id, sequence_order, comment"];
        return $this->load_all($query); // Assumes load_all is available from My_model
    }



}


class mysql_Correspondence_workflow_step extends mysqli_Correspondence_workflow_step
{
}

class sqlsrv_Correspondence_workflow_step extends mysql_Correspondence_workflow_step
{
    public function get_next_stage_id($current_stage_id,$correspondence_type_id)
    {
        // Get current stage's sequence order
        $query = $this->ci->db->query(" SELECT TOP 1 sequence_order     FROM correspondence_workflow_steps   WHERE id = ? ", [$current_stage_id]);
        $current = $query->row();

        if (!$current) {
            return null;
        }

        // Find the next stage with higher sequence order
        $query = $this->ci->db->query(" SELECT TOP 1 id  FROM correspondence_workflow_steps WHERE  sequence_order > ? 
       and correspondence_type_id=?  ORDER BY sequence_order ASC ", [$current->sequence_order,$correspondence_type_id]);
        $next = $query->row();
        return $next ? $next->id : null;
    }

    public function load_steps_by_type($correspondence_type_id)
    {
        $query = [];
         $query = ["select" => "correspondence_workflow_steps.id as id, correspondence_workflow_steps.name as name, correspondence_type_id,ct.name as type, sequence_order, comment"];

        $query["join"] = [
            ["correspondence_types ct", "ct.id = correspondence_workflow_steps.correspondence_type_id", "left"]
        ];

        $query["where"] = [
            "correspondence_workflow_steps.correspondence_type_id" , $correspondence_type_id
        ];
       return $this->load_all($query);
    }
}
