<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Case_investigation_log_document extends My_Model_Factory
{
}
class mysqli_Case_investigation_log_document extends My_Model {
    protected $_table = 'case_investigation_log_document';
    protected $primaryKey = 'id';
    protected $modelName = 'case_investigation_log_document';
    protected $_fieldsNames = ['id', 'investigation_id', 'document'];
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Get documents for a specific investigation.
     */
    public function get_by_investigation($investigation_id)
    {
        return $this->db->where('investigation_id', $investigation_id)
            ->get($this->table)
            ->result_array();
    }

    /**
     * Delete a document link.
     */
    public function delete_by_document($document_id)
    {
        return $this->db->where('document', $document_id)
            ->delete($this->table);
    }

    /**
     * Delete all documents under an investigation log (e.g., on case delete).
     */
    public function delete_by_investigation($investigation_id)
    {
        return $this->db->where('investigation_id', $investigation_id)
            ->delete($this->table);
    }
}

class mysql_Case_investigation_log_document extends mysqli_Case_investigation_log_document {}
class sqlsrv_Case_investigation_log_document extends mysqli_Case_investigation_log_document {}
