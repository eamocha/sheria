<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Conveyancing_stage_progress extends My_Model_Factory
{
}

class mysql_Conveyancing_stage_progress extends My_Model
{
    protected $modelName = "conveyancing_stage_progress";
    protected $_table = "conveyancing_stage_progress";
    protected $_listFieldName = "instrument_id";
    protected $_fieldsNames = ["id", "instrument_id", "stage_id", "status", "start_date", "completion_date", "updated_by", "updated_on", "comments"];

    protected $builtInLogs = true;

    public function __construct()
    {
        parent::__construct();
        $this->validate = [
            "instrument_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "stage_id" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "status" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => ["minLength", 1],
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ],
            "updated_by" => [
                "required" => true,
                "allowEmpty" => false,
                "rule" => "numeric",
                "message" => $this->ci->lang->line("cannot_be_blank_rule")
            ]
        ];
    }

    public function load_progress($instrument_id, $show_all = true)
    {
        if (!$show_all) {
            $this->pagination_config_set("inPage", 6);
        }

        $query = [];
        $query["select"] = [
            "SQL_CALC_FOUND_ROWS conveyancing_stage_progress.id, 
            conveyancing_stage_progress.instrument_id, 
            conveyancing_stage_progress.stage_id, 
            conveyancing_stage_progress.status, 
            conveyancing_stage_progress.start_date, 
            conveyancing_stage_progress.completion_date, 
            conveyancing_stage_progress.updated_by, 
            conveyancing_stage_progress.updated_on, 
            conveyancing_stage_progress.comments,
            conveyancing_process_stages.name as stage_name,
            CONCAT(creator.firstName, ' ', creator.lastName) as updated_by_name",
            false
        ];

        $query["join"] = [
            ["conveyancing_process_stages", "conveyancing_process_stages.id = conveyancing_stage_progress.stage_id", "left"],
            ["user_profiles creator", "creator.user_id = conveyancing_stage_progress.updated_by", "left"]
        ];

        $query["where"] = ["conveyancing_stage_progress.instrument_id", $instrument_id];
        $query["order_by"] = !$show_all ? ["conveyancing_stage_progress.updated_on desc"] : ["conveyancing_stage_progress.start_date asc"];

        $response["records"] = $show_all ? parent::load_all($query) : parent::paginate($query, ["urlPrefix" => ""]);

        if (!$show_all) {
            asort($response["records"]);
        }

        $response["count"] = $this->ci->db->query("SELECT FOUND_ROWS() AS `count`")->row()->count;
        return $response;
    }

    public function load_stage_progress($id)
    {
        $query = [];
        $query["select"] = [
            "conveyancing_stage_progress.id, 
            conveyancing_stage_progress.instrument_id, 
            conveyancing_stage_progress.stage_id, 
            conveyancing_stage_progress.status, 
            conveyancing_stage_progress.start_date, 
            conveyancing_stage_progress.completion_date, 
            conveyancing_stage_progress.updated_by, 
            conveyancing_stage_progress.updated_on, 
            conveyancing_stage_progress.comments,
            conveyancing_process_stages.name as stage_name,
            CONCAT(creator.firstName, ' ', creator.lastName) as updated_by_name",
            false
        ];

        $query["join"] = [
            ["conveyancing_process_stages", "conveyancing_process_stages.id = conveyancing_stage_progress.stage_id", "left"],
            ["user_profiles creator", "creator.user_id = conveyancing_stage_progress.updated_by", "left"]
        ];

        $query["where"] = ["conveyancing_stage_progress.id", $id];
        $response = parent::load($query);
        return $response;
    }

    public function load_all_attachments($progress_id)
    {
        $query = [];
        $query["select"] = [
            "attachments.id, 
            attachments.progress_id, 
            attachments.document_id, 
            documents.type, 
            documents.name, 
            documents.extension, 
            (case when (documents.type = 'file') then concat(documents.name,'.',documents.extension) else name end) AS full_name",
            false
        ];

        $query["join"][] = ["conveyancing_progress_attachments as attachments", "attachments.progress_id = conveyancing_stage_progress.id", "left"];
        $query["join"][] = ["documents_management_system as documents", "attachments.document_id = documents.id", "inner"];
        $query["where"][] = ["conveyancing_stage_progress.id", $progress_id];
        $response = $this->load_all($query);
        return $response;
    }
}

class mysqli_Conveyancing_stage_progress extends mysql_Conveyancing_stage_progress
{
}

class sqlsrv_Conveyancing_stage_progress extends mysql_Conveyancing_stage_progress
{
    public function load_progress($instrument_id, $show_all = true)
    {
        if (!$show_all) {
            $this->pagination_config_set("inPage", 6);
        }

        $query = [];
        $query["select"] = [
            "COUNT(*) OVER() AS total_rows, 
            conveyancing_stage_progress.id, 
            conveyancing_stage_progress.instrument_id, 
            conveyancing_stage_progress.stage_id, 
            conveyancing_stage_progress.status, 
            conveyancing_stage_progress.start_date, 
            conveyancing_stage_progress.completion_date, 
            conveyancing_stage_progress.updated_by, 
            conveyancing_stage_progress.updated_on, 
            conveyancing_stage_progress.comments,
            conveyancing_process_stages.name as stage_name,
            (creator.firstName + ' ' + creator.lastName) as updated_by_name",
            false
        ];

        $query["join"] = [
            ["conveyancing_process_stages", "conveyancing_process_stages.id = conveyancing_stage_progress.stage_id", "left"],
            ["user_profiles creator", "creator.user_id = conveyancing_stage_progress.updated_by", "left"]
        ];

        $query["where"] = ["conveyancing_stage_progress.instrument_id", $instrument_id];
        $query["order_by"] = !$show_all ? ["conveyancing_stage_progress.updated_on desc"] : ["conveyancing_stage_progress.start_date asc"];

        $response["records"] = $show_all ? parent::load_all($query) : parent::paginate($query, ["urlPrefix" => ""]);

        if (!$show_all) {
            asort($response["records"]);
        }

        $response["count"] = $response["records"][0]["total_rows"] ?? false;
        return $response;
    }

    public function load_stage_progress($id)
    {
        $query = [];
        $query["select"] = [
            "conveyancing_stage_progress.id, 
            conveyancing_stage_progress.instrument_id, 
            conveyancing_stage_progress.stage_id, 
            conveyancing_stage_progress.status, 
            conveyancing_stage_progress.start_date, 
            conveyancing_stage_progress.completion_date, 
            conveyancing_stage_progress.updated_by, 
            conveyancing_stage_progress.updated_on, 
            conveyancing_stage_progress.comments,
            conveyancing_process_stages.name as stage_name,
            (creator.firstName + ' ' + creator.lastName) as updated_by_name",
            false
        ];

        $query["join"] = [
            ["conveyancing_process_stages", "conveyancing_process_stages.id = conveyancing_stage_progress.stage_id", "left"],
            ["user_profiles creator", "creator.user_id = conveyancing_stage_progress.updated_by", "left"]
        ];

        $query["where"] = ["conveyancing_stage_progress.id", $id];
        $response = parent::load($query);
        return $response;
    }

    public function load_all_attachments($progress_id)
    {
        $query = [];
        $query["select"] = [
            "attachments.id, 
            attachments.progress_id, 
            attachments.document_id, 
            documents.type, 
            documents.name, 
            documents.extension, 
            (case when (documents.type = 'file') then (documents.name + '.' + documents.extension) else name end) AS full_name",
            false
        ];

        $query["join"][] = ["conveyancing_progress_attachments as attachments", "attachments.progress_id = conveyancing_stage_progress.id", "left"];
        $query["join"][] = ["documents_management_system as documents", "attachments.document_id = documents.id", "left"];
        $query["where"][] = ["conveyancing_stage_progress.id", $progress_id];
        $response = $this->load_all($query);
        return $response;
    }


}