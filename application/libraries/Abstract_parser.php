<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abstract base parser class.
 * Provides common functionality for all document parsers.
 */
abstract class Abstract_parser {

    /**
     * Extract text from a file.
     * Must be implemented by child classes.
     *
     * @param string $file_path
     * @return string
     */
    abstract public function extract_text($file_path);

    /**
     * Clean up whitespace and normalize text.
     *
     * @param string $text
     * @return string
     */
    protected function clean_text($text) {
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Log errors in a consistent way.
     *
     * @param string $context
     * @param string $message
     * @return void
     */
    protected function log_error($context, $message) {
        log_message('error', sprintf('%s: %s', $context, $message));
    }
}
