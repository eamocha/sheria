<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Documents_search extends Top_controller {

    public function __construct() {
        parent::__construct();
        // Manually include the Composer autoloader
       require_once FCPATH . 'vendor/autoload.php';

        $this->load->model('document_search');
        $this->load->library(['Pdf_parser', 'Docx_parser', 'Excel_parser', 'Image_ocr']);
        $this->load->config('document_search');
    }

    public function index() {
        $data['title'] = 'Document Text Search';

        $this->load->view('partial/header');
        $this->load->view('document_search/index', $data);
        $this->load->view('partial/footer');
    }

    public function upload() {

        if (!$this->input->is_ajax_request()) {
            show_error('Direct access not allowed');
        }

        $config['upload_path'] = $this->config->item('document_upload_path');
        $config['allowed_types'] = $this->config->item('allowed_types');
        $config['max_size'] = $this->config->item('max_size');
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('document')) {
            echo json_encode([
                'status' => 'error',
                'message' => $this->upload->display_errors()
            ]);
            return;
        }

        $upload_data = $this->upload->data();
        echo json_encode([
            'status' => 'success',
            'file' => $upload_data['file_name']
        ]);
    }

    public function search() {
        if (!$this->input->is_ajax_request()) {
            show_error('Direct access not allowed');
        }

        $search_term = $this->input->post('search_term');
        $file_type = $this->input->post('file_type');

        $results = $this->document_search->search_documents($search_term, $file_type);

        echo json_encode([
            'status' => 'success',
            'results' => $results
        ]);
    }

    public function get_file_content() {
        if (!$this->input->is_ajax_request()) {
            show_error('Direct access not allowed');
        }

        $filename = $this->input->post('filename');
        $search_term = $this->input->post('search_term');

        $content = $this->document_search->get_file_content_with_highlights(
            $filename,
            $search_term
        );

        echo json_encode([
            'status' => 'success',
            'content' => $content
        ]);
    }
}