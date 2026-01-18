<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Smalot\PdfParser\Parser;
Require "Abstract_parser.php";
class Pdf_parser extends Abstract_parser {

    /**
     * Extracts plain text from a PDF file.
     *
     * @param string $file_path
     * @return string
     */
    public function extract_text($file_path) {
        try {
            if (!class_exists(Parser::class)) {
                $this->log_error('PDF parsing', 'Smalot\\PdfParser not available');
                return '';
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($file_path);

            $text = $pdf->getText() ?? '';

            // (Optional) include metadata in text for searching
            $details = $pdf->getDetails();
            if (is_array($details)) {
                foreach ($details as $key => $value) {
                    if (is_string($value) && trim($value) !== '') {
                        $text .= ' ' . $value;
                    }
                }
            }

            return $this->clean_text($text);

        } catch (\Exception $e) {
            $this->log_error('PDF parsing', $e->getMessage());
            return '';
        }
    }
}
