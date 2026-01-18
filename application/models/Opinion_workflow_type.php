<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_workflow_type extends My_Model_Factory
{
}
class mysql_Opinion_workflow_type extends My_Model
{
    protected $modelName = "opinion_workflow_type";
    protected $_table = "opinion_workflow_types";
    protected $_fieldsNames = ["id", "workflow_id", "type_id"];
    protected $builtInLogs = false;
    public function __construct()
    {
        parent::__construct();
    }
    public function load_workflow_type_names($workflow_id = 0)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["GROUP_CONCAT(DISTINCT types.name SEPARATOR ',' ) as name", false];
        $query["where"] = ["opinion_workflow_types.workflow_id", $workflow_id];
        $query["join"] = ["opinion_types_languages as types", "types.opinion_type_id = opinion_workflow_types.type_id AND types.language_id = " . $lang_id, "left"];
        $response = parent::load($query);
        return $response;
    }
}
class mysqli_Opinion_workflow_type extends mysql_Opinion_workflow_type
{
}
class sqlsrv_Opinion_workflow_type extends mysql_Opinion_workflow_type
{
    public function load_workflow_type_names($workflow_id = 0)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["name = STUFF((SELECT DISTINCT ', ' + types.name from opinion_workflow_types LEFT JOIN opinion_types_languages as types ON types.opinion_type_id = opinion_workflow_types.type_id AND types.language_id = " . $lang_id . " FOR XML PATH('')), 1, 1, '')", false];
        $query["where"] = ["opinion_workflow_types.workflow_id", $workflow_id];
        $response = parent::load($query);
        return $response;
    }
}

?>