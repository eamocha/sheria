<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Image_ocr {

    protected $tesseractPath;

    public function __construct() {
        $CI =& get_instance();
        $CI->load->config('document_search');

        // Load tesseract path from config
        $this->tesseractPath = $CI->config->item('tesseract_path') ?? 'tesseract';

        // Wrap path in quotes (handles spaces in Windows paths like "Program Files")
        $this->tesseractPath = '"' . $this->tesseractPath . '"';
    }

    /**
     * Run OCR on an image file and return extracted text
     */
    public function extract_text($image_path) {
        try {
            $outputFile = tempnam(sys_get_temp_dir(), 'ocr_');

            // Tesseract command
            $cmd = $this->tesseractPath . ' "' . $image_path . '" "' . $outputFile . '" -l eng';
            exec($cmd . ' 2>&1', $output, $return_var);

            if ($return_var !== 0) {
                log_message('error', 'Tesseract OCR failed: ' . implode("\n", $output));
                return '';
            }

            // Tesseract writes to .txt
            $text = @file_get_contents($outputFile . '.txt');

            // Cleanup temp files
            @unlink($outputFile);
            @unlink($outputFile . '.txt');

            return $text ?: '';

        } catch (Exception $e) {
            log_message('error', 'Image OCR extraction failed: ' . $e->getMessage());
            return '';
        }
    }
}
