<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Case_investigation_log extends My_Model_Factory
{
}

class mysqli_Case_investigation_log extends My_Model {
    protected $_table = 'case_investigation_log';
    protected $primaryKey = 'id';
    protected $modelName = 'case_investigation_log';
    protected $_fieldsNames = [
        'id', 'case_id', 'log_date', 'details', 'action_taken',
        'createdBy', 'createdOn', 'modifiedBy', 'modifiedOn'
    ];

    public function get_investigation_log_by_case_id($case_id) {
        $query = [];
        $query['select'] = [
            "case_investigation_log.*,i_doc.document as doc_id, CONCAT(creator.firstName, ' ', creator.lastName) as creator_name, CONCAT(modifier.firstName, ' ', modifier.lastName) as modifier_name"
        ];
        $query['join'] = [
            ["user_profiles creator", "creator.user_id = case_investigation_log.createdBy", "left"],
            ["user_profiles modifier", "modifier.user_id = case_investigation_log.modifiedBy", "left"],
            ["case_investigation_log_document i_doc", "i_doc.investigation_id = case_investigation_log.id", "left"]
        ];

        $query['where'][] = ["case_investigation_log.case_id", $case_id];
        $query['order_by'] = ["case_investigation_log.log_date DESC"];

        return $this->load_all($query);
    }
}

class mysql_Case_investigation_log extends mysqli_Case_investigation_log {}
class sqlsrv_Case_investigation_log extends mysqli_Case_investigation_log {}
