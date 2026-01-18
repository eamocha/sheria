<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['document_upload_path'] = FCPATH . 'files/tmp/';
$config['allowed_types'] = 'pdf|docx|xlsx|xls|jpg|jpeg|png|gif|txt|html|htm';
$config['max_size'] = 51200; // 50MB
//$config['tesseract_path'] = '/usr/bin/tesseract'; // Path to tesseract executable
// Windows Tesseract path - adjust based on your installation
$config['tesseract_path'] = 'C:/Program Files/Tesseract-OCR/tesseract.exe';

// Poppler (pdftoppm)
$config['pdftoppm_path'] = 'D:\progs\poppler-25.07.0\Library\bin\pdftoppm.exe';

// Optional XPDF path (if needed later)
$config['pdftotext_path'] = 'C:/Program Files/Glyph & Cog/XpdfReader-win64/xpdf.exe';


// OCR / Text extraction tools
$config['tesseract_path']  = 'tesseract';   // since it's in PATH, just call 'tesseract'
$config['pdftotext_path']  = 'pdftotext';   // from Xpdf or Poppler if installed
$config['pdftoppm_path']   = 'pdftoppm';    // for converting PDFs to images