<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class V7_2 extends CI_Controller
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
        $this->update_attachment_extensions_config_file();
        $this->migrate_invoice_templates();
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

    public function update_attachment_extensions_config_file()
    {
        $allowed_file_uploads_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php';
        $this->write_log($this->log_path, 'update quote extensions in config file');
        if (@$file_content = file_get_contents($allowed_file_uploads_path)) {
            if (!strpos($file_content, 'QOT')) {
                $file_content .= "\$config['QOT'] = 'doc|docx|xls|xlsx|pps|ppt|pptx|pdf|tif|jpg|png|gif|jpeg|bmp|html|htm|txt|msg|eml|vcf|zip|rar|mpg|mp3|mp4|flv|mov|wav|3gp|avi';";
            }
            if (@!file_put_contents($allowed_file_uploads_path, $file_content)) {
                $this->write_log($this->log_path, "failed to put content to file $allowed_file_uploads_path");
            }
        } else {
            $this->write_log($this->log_path, "failed to get content of file $allowed_file_uploads_path");
        }
        $this->write_log($this->log_path, 'Done - update quote extensions config file');
    }
    public function migrate_invoice_templates()
    {
        $sql = 'select * from organization_invoice_templates';
        $query_execution = $this->db->query($sql);
        $templates = $query_execution->result_array();
        $this->write_log($this->log_path, 'Done - Return the templates data');
        $default_template = unserialize('a:3:{s:6:"header";a:2:{s:4:"show";a:3:{s:14:"logo-container";b:0;s:15:"title-container";b:1;s:22:"company-info-container";b:1;}s:7:"general";a:1:{s:5:"notes";s:74:"Mountain Hills<br><div align="left">009613123456<br></div>Lebanon - Beirut";}}s:4:"body";a:3:{s:4:"show";a:10:{s:19:"matter-id-container";b:0;s:24:"matter-subject-container";b:0;s:17:"bill-to-container";b:1;s:20:"invoice-nb-container";b:1;s:22:"invoice-date-container";b:1;s:18:"due-date-container";b:1;s:15:"terms-container";b:1;s:12:"po-container";b:1;s:27:"time-logs-summary-container";b:0;s:15:"notes-container";b:1;}s:3:"css";a:1:{s:10:"margin-top";s:4:"0.38";}s:7:"general";a:1:{s:10:"line_items";a:3:{s:8:"expenses";s:1:"1";s:9:"time_logs";s:1:"2";s:5:"items";s:1:"3";}}}s:6:"footer";a:2:{s:4:"show";a:1:{s:16:"footer-container";b:0;}s:7:"general";a:1:{s:5:"notes";s:43:"<div align="center">App4legal.com<br></div>";}}}');
        $settings = array();
        $result = true;
        foreach ($templates as $key => $template) {
            $settings = $default_template;
            if ($template['logo']) {
                $settings['header']['show']['logo-container'] = true;
                $settings['header']['general']['logo'] = $template['logo'];
            }
            if ($template['headerNotes']) {
                $settings['header']['general']['notes'] = $template['headerNotes'];
            }
            $settings = serialize($settings);
            $update = "UPDATE organization_invoice_templates SET settings = '{$settings}' WHERE organization_invoice_templates.id = {$template['id']}";
            $result = $this->db->query($update);
            if ($result) {
                $this->write_log($this->log_path, 'Done - Migrate the template data of id = '.$template['id']);
            } else {
                $this->write_log($this->log_path, 'Error - Failed to update the template data of id = '.$template['id']);
            }
        }
        if ($result) {
            if (($this->db->dbdriver === 'sqlsrv')) {
                $this->db->query("DECLARE @constraint_name as NVARCHAR(255);
DECLARE @constraint_cursor as CURSOR;
DECLARE @columns_name TABLE (name varchar(1000));
DECLARE @table_name as NVARCHAR(255);
SET @table_name = 'organization_invoice_templates';
INSERT INTO @columns_name VALUES ('logo');
INSERT INTO @columns_name VALUES ('headerNotes');
INSERT INTO @columns_name VALUES ('footer');
INSERT INTO @columns_name VALUES ('footerNotes');
SET @constraint_cursor = CURSOR FOR
(SELECT fk.name AS constraint_name
FROM sys.foreign_keys fk
    INNER JOIN sys.foreign_key_columns fkcol on fkcol.constraint_object_id = fk.object_id
    INNER JOIN sys.columns col on col.column_id = fkcol.parent_column_id and fk.parent_object_id = col.object_id
WHERE fk.parent_object_id = OBJECT_ID(@table_name)
    AND col.name IN (SELECT name FROM @columns_name)
UNION
SELECT chk.name AS constraint_name
FROM sys.check_constraints chk
    INNER JOIN sys.columns col on col.column_id = chk.parent_column_id  and chk.parent_object_id = col.object_id
WHERE chk.parent_object_id = OBJECT_ID(@table_name)
    AND col.name IN (SELECT name FROM @columns_name)
UNION
SELECT dc.name AS constraint_name
FROM sys.default_constraints dc
    INNER JOIN sys.columns col ON col.default_object_id = dc.object_id and dc.parent_object_id = col.object_id
WHERE dc.parent_object_id = OBJECT_ID(@table_name)
    AND col.name IN (SELECT name FROM @columns_name));
OPEN @constraint_cursor;
FETCH NEXT FROM @constraint_cursor INTO @constraint_name;
WHILE @@FETCH_STATUS = 0
BEGIN
 EXEC(N'alter table ' + @table_name + ' drop constraint  [' + @constraint_name + N']');
 FETCH NEXT FROM @constraint_cursor INTO @constraint_name;
END
CLOSE @constraint_cursor;
DEALLOCATE @constraint_cursor;
ALTER TABLE organization_invoice_templates DROP COLUMN logo,headerNotes,footerNotes,footer;");
            } else {
                $this->db->query(' ALTER TABLE organization_invoice_templates DROP logo,DROP headerNotes, DROP footerNotes, DROP footer;');
            }
            $this->write_log($this->log_path, 'Done - DB columns are removed');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to remove the db columns', 'error');
        }
    }
}
