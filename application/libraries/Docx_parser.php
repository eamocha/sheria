<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpWord\IOFactory;

class Docx_parser extends Abstract_parser {

    public function extract_text($file_path) {
        // First try the ZIP method (most reliable)
        $text = $this->extract_text_using_zip($file_path);

        if (!empty(trim($text))) {
            return $this->clean_text($text);
        }

        // Fallback to PHPWord
        if (class_exists(IOFactory::class)) {
            try {
                $phpWord = IOFactory::load($file_path);
                $text = '';

                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                            foreach ($element->getElements() as $textElement) {
                                if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                    $text .= $textElement->getText() . ' ';
                                }
                            }
                        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                            $text .= $element->getText() . ' ';
                        }
                    }
                }

                return $this->clean_text($text);
            } catch (\Exception $e) {
                $this->log_error('DOCX parsing', $e->getMessage());
            }
        }

        return '';
    }

    private function extract_text_using_zip($file_path) {
        if (!extension_loaded('zip')) {
            $this->log_error('DOCX parsing', 'ZIP extension not loaded');
            return '';
        }

        $zip = new \ZipArchive;
        $text = '';

        if ($zip->open($file_path) === TRUE) {
            if (($index = $zip->locateName('word/document.xml')) !== FALSE) {
                $data = $zip->getFromIndex($index);
                $text = preg_replace('/<[^>]*>/', ' ', $data);
                $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
            }
            $zip->close();
        }

        return $text;
    }
}
