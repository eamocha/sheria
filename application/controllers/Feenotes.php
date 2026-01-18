<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'Top_controller.php';

class Feenotes extends Top_Controller {

    private $apiUrl = 'http://10.10.1.133:8001/api/vendor-invoices';

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url']);
        $this->load->library('session');
    }

    public function index() {
        $data['title'] = 'Fee Notes';
        $this->load->view('partial/header');
        $this->load->view('feenotes/index', $data);
        $this->load->view('partial/footer');
    }

    public function fetch() {
        header('Content-Type: application/json');

        $page = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 10;
        $search = trim($this->input->get('search')) ?: '';

        $json = @file_get_contents($this->apiUrl);
        if ($json === false) {
            echo json_encode(['error' => 'Failed to connect to the accounting system.']);
            return;
        }

        $invoices = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['error' => 'Invalid JSON received: ' . json_last_error_msg()]);
            return;
        }

        // Search filter
        if ($search !== '') {
            $invoices = array_filter($invoices, function ($inv) use ($search) {
                return stripos($inv['InvoiceNumber'], $search) !== false ||
                    stripos($inv['InvoiceDescription'], $search) !== false ||
                    stripos($inv['SageVendorNumber'], $search) !== false;
            });
        }

        $total = count($invoices);
        $offset = ($page - 1) * $limit;
        $paged = array_slice($invoices, $offset, $limit);

        echo json_encode([
            'data' => array_values($paged),
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }
}
