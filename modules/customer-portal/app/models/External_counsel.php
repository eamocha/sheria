<?php
class External_counsel extends CI_Model {
    protected $table = 'external_counsel';
    protected $primaryKey = 'id';

    public function __construct() {
        parent::__construct();
    }

    public function get_instance() {
        return $this;
    }

    public function fetch($id) {
        return $this->db->where($this->primaryKey, $id)->get($this->table)->row_array();
    }

    public function get_all() {
        return $this->db->order_by('firm_name', 'ASC')->get($this->table)->result_array();
    }

    public function search($term) {
        $this->db->like('firm_name', $term)
            ->or_like('contact_person', $term)
            ->or_like('email', $term);
        return $this->db->get($this->table)->result_array();
    }

    public function get_assigned_instruments($counsel_id) {
        $this->db->where('external_counsel_id', $counsel_id);
        return $this->db->get('conveyancing_instruments')->result_array();
    }
}