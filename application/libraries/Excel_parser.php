<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel_parser extends Abstract_parser {

    public function extract_text($file_path) {
        $text = '';

        try {
            if (!class_exists(IOFactory::class)) {
                $this->log_error('Excel parsing', 'PhpSpreadsheet not available');
                return '';
            }

            libxml_use_internal_errors(true);
            $spreadsheet = IOFactory::load($file_path);
            libxml_clear_errors();

            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $text .= $sheet->getTitle() . ' ';

                foreach ($sheet->toArray() as $row) {
                    if (is_array($row)) {
                        $row_text = implode(' ', array_map(
                            fn($cell) => strip_tags((string)$cell),
                            $row
                        ));
                        if (trim($row_text) !== '') {
                            $text .= $row_text . ' ';
                        }
                    } elseif (trim((string)$row) !== '') {
                        $text .= strip_tags((string)$row) . ' ';
                    }
                }
            }

            return $this->clean_text($text);

        } catch (\Exception $e) {
            $this->log_error('Excel parsing', $e->getMessage());
            return '';
        }
    }
}
