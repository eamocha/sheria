<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document_search extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->config('document_search');
        $this->load->library(['Pdf_parser', 'Docx_parser', 'Excel_parser', 'Image_ocr']);
    }

    public function search_documents($search_term, $file_type = 'all') {
        $documents_path = $this->config->item('document_upload_path');
        $files = $this->get_files_by_type($documents_path, $file_type);
        $results = [];

        foreach ($files as $file) {
            $file_path = $documents_path . $file;
            $text = $this->extract_text($file_path);

            if (!empty($text) && stripos($text, $search_term) !== false) {
                $results[] = [
                    'filename' => $file,
                    'path' => $file_path,
                    'size' => filesize($file_path),
                    'modified' => date('Y-m-d H:i:s', filemtime($file_path)),
                    'matches' => substr_count(strtolower($text), strtolower($search_term)),
                    'type' => pathinfo($file_path, PATHINFO_EXTENSION)
                ];
            }
        }

        return $results;
    }

    public function get_file_content_with_highlights($filename, $search_term) {
        $file_path = $this->config->item('document_upload_path') . $filename;
        $text = $this->extract_text($file_path);

        if (empty($text)) {
            return 'No text content available.';
        }

        // Highlight search term
        $highlighted = preg_replace(
            "/(" . preg_quote($search_term, '/') . ")/i",
            '<mark>$1</mark>',
            $text
        );

        return nl2br($highlighted);
    }

    private function extract_text($file_path) {
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'pdf':
                return $this->pdf_parser->extract_text($file_path);
            case 'docx':
                return $this->docx_parser->extract_text($file_path);
            case 'xlsx':
            case 'xls':
                return $this->excel_parser->extract_text($file_path);
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return $this->image_ocr->extract_text($file_path);
            case 'txt':
            case 'html':
            case 'htm':
                return file_get_contents($file_path);
            default:
                return '';
        }
    }

    private function get_files_by_type($path, $type) {
        $all_files = array_diff(scandir($path), array('.', '..', 'index.html'));
        $filtered_files = [];

        foreach ($all_files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            switch ($type) {
                case 'pdf':
                    if ($ext === 'pdf') $filtered_files[] = $file;
                    break;
                case 'docx':
                    if (in_array($ext, ['docx', 'doc'])) $filtered_files[] = $file;
                    break;
                case 'xlsx':
                    if (in_array($ext, ['xlsx', 'xls'])) $filtered_files[] = $file;
                    break;
                case 'image':
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) $filtered_files[] = $file;
                    break;
                case 'text':
                    if (in_array($ext, ['txt', 'html', 'htm'])) $filtered_files[] = $file;
                    break;
                default: // 'all'
                    if (in_array($ext, ['pdf', 'docx', 'doc', 'xlsx', 'xls', 'jpg', 'jpeg', 'png', 'gif', 'txt', 'html', 'htm'])) {
                        $filtered_files[] = $file;
                    }
                    break;
            }
        }

        return $filtered_files;
    }
}