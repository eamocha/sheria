<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class V7_3 extends CI_Controller
{
    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->write_log($this->log_path, 'start migration script');
    }
    public function index()
    {
        $this->migrate_invoice_templates();
        $this->update_attachment_extensions_config_file();
    }
    public function write_log($file_path, $message, $type = 'info')
    {
        $log_path = FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $file_path . '.log';
        $pr = fopen($log_path, 'a');
        fwrite($pr, date('Y-m-d H:i:s') . " [$type] - $message \n");
        fclose($pr);
        if ($type == 'error') {
            echo $type.': '.$message .". Please check the log file '$log_path' for more details and to fix the error";
            exit;
        }
    }
    public function migrate_invoice_templates()
    {
        $sql = 'select id,settings from organization_invoice_templates';
        $query_execution = $this->db->query($sql);
        $templates = $query_execution->result_array();
        $this->write_log($this->log_path, 'Done - Return the templates data');
        $settings = array();
        $result = true;
        foreach ($templates as $key => $template) {
            if ($template['settings']) {
                $settings = unserialize($template['settings']);
                $settings['body']['show']['title-container'] = true;
                if (isset($settings['header']['show']['title-container'])) {
                    unset($settings['header']['show']['title-container']);
                }
                $settings['body']['show']['matter-reference-nb'] = false;
                $settings['footer']['show']['page-numbering'] = false;
                $updated_settings = serialize($settings);
                $update = "UPDATE organization_invoice_templates SET settings = '{$updated_settings}' WHERE organization_invoice_templates.id = {$template['id']}";
                $result = $this->db->query($update);
                if ($result) {
                    $this->write_log($this->log_path, 'Done - Migrate the template data of id = '.$template['id']);
                } else {
                    $this->write_log($this->log_path, 'Error - Failed to update the template data of id = '.$template['id']);
                }
            }
        }
    }
    public function update_attachment_extensions_config_file()
    {
        $allowed_file_uploads_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php';
        $this->write_log($this->log_path, 'update system logo extensions in config file');
        if (@$file_content = file_get_contents($allowed_file_uploads_path)) {
            if (!strpos($file_content, 'system_logo')) {
                $file_content .= "\$config['system_logo'] = 'gif|jpg|jpeg|png|ico';";
            }
            if (@!file_put_contents($allowed_file_uploads_path, $file_content)) {
                $this->write_log($this->log_path, "failed to put content to file $allowed_file_uploads_path");
            }
        } else {
            $this->write_log($this->log_path, "failed to get content of file $allowed_file_uploads_path");
        }
        $this->write_log($this->log_path, 'Done - update system logo extensions config file');
    }
}
