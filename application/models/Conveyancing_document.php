<?php
class Conveyancing_document extends CI_Model {
    protected $table = 'conveyancing_documents';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function get_instance() {
        return $this;
    }

    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

    public function get_documents_for_instrument($instrument_id) {
        $this->db->where('conveyancing_id', $instrument_id)
            ->order_by('upload_date', 'DESC');
        return $this->db->get($this->table)->result_array();
    }

    public function find($document_id) {
        return $this->db->where($this->primaryKey, $document_id)->get($this->table)->row_array();
    }

    public function delete($document_id) {
        $document = $this->find($document_id);
        if ($document) {
            $file_path = WRITEPATH . $document['file_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $this->db->where($this->primaryKey, $document_id)->delete($this->table);
            return $this->db->affected_rows() > 0;
        }
        return false;
    }
}