<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_9 extends CI_Controller {

    use MigrationLogTrait;

    public $log_path = null;

    public function __construct() {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }


    public function index() {
        $this->update_database_file();
        $this->append_attachments_to_commment();
        $this->migrate_invoice_templates();
        $this->add_api_key_validity_time_to_instance_file();
        $this->migrate_users_api_keys();
        $this->write_log($this->log_path, 'End migration script');
    }
     /**
     * update database file maintenance dbdriver to mysqli
     *
     * @return void
     */
    public function update_database_file() {
        $this->write_log($this->log_path, 'Database update file starts');
        $database_file_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
        $new_content = str_replace("'mysql';", "'mysqli';", file_get_contents($database_file_path));
        if (@!file_put_contents($database_file_path, $new_content)) {
            $this->write_log($this->log_path, "failed to put content to file $database_file_path", 'error');
        } else {
            $this->write_log($this->log_path, "Database File is updated");
        }
    }
    /**
     * append attachments to commment function
     * this function must run in browser to get base_url
     * @return void
     */
    public function append_attachments_to_commment(){
        $this->load->model('task_comment', 'task_commentfactory');
        $this->task_comment = $this->task_commentfactory->get_instance();
        $sql = 'SELECT id , task_id FROM task_comments';
        $query_execution = $this->db->query($sql);
        $task_comments = $query_execution->result_array();
        $images = ['tif','jpg','png','gif','jpeg','bmp'];
        $result = [];
        $this->write_log($this->log_path, "Get All Comments");
        if(isset($task_comments) && !empty($task_comments)){
            foreach ($task_comments as $key=>$value) {
                $task_comments_attachments = $this->task_comment->load_all_attachments($value['task_id']);
                if(isset($task_comments_attachments ) && !empty($task_comments_attachments)){
                    foreach ($task_comments_attachments as $k=>$comment) {
                        if($value['id'] == $comment['comment_id']){
                            if(in_array($comment['extension'],$images)){
                                $link_or_image = "!{$comment['full_name']}|{$comment['document_id']}!";
                            }else{
                                $link_or_image = "[{$comment['full_name']}|{$comment['document_id']}]";
                            }
                            $comment_value = $this->db->query( 'SELECT comment FROM task_comments WHERE id='.$comment['comment_id'])->result_array();
                            $to_save_comment =  $comment_value[0]['comment']." ".$link_or_image;
                            $update = "UPDATE task_comments SET comment='".addslashes($to_save_comment)."' WHERE task_comments.id=".$comment['comment_id'];
                            $result[$k] = $this->db->query($update);    
                            if($result[$k]){
                                $this->write_log($this->log_path, "Update Comment Done ID". $comment['comment_id']);  
                            }else{
                                $this->write_log($this->log_path, "Error in Update");
                            }
                        }   
                    }
                }else{
                    $this->write_log($this->log_path, "There Is No Attachments Realted To Task");
                }
                
            }
            if(!in_array(false,$result)){
                // drop table
                $query_delete = 'DROP TABLE task_comment_attachments';
                $this->db->query($query_delete);
                $this->write_log($this->log_path, "DROP TABLE task_comment_attachments");
            }else{
                $this->write_log($this->log_path, "Error In DROP TABLE task_comment_attachments some attachments not merged with comments successfully"); 
            }
        }else{
            $this->write_log($this->log_path, "No Task Comments");
            $query_delete = 'DROP TABLE task_comment_attachments';
            $this->db->query($query_delete);
            $this->write_log($this->log_path, "DROP TABLE task_comment_attachments");
        }
    }
    public function migrate_invoice_templates()
    {
        $sql = 'select * from organization_invoice_templates';
        $query_execution = $this->db->query($sql);
        $templates = $query_execution->result_array();
        $this->write_log($this->log_path, 'Done - Return the templates data');
        $default_template = unserialize('a:3:{s:6:"header";a:2:{s:4:"show";a:2:{s:14:"logo-container";b:1;s:22:"company-info-container";b:1;}s:7:"general";a:2:{s:5:"notes";s:373:"<p style="margin: 0;">MyCompany Ltd</p><p style="margin: 0;">Registration #:</p><p style="margin: 0;">Riviera Building</p><p style="margin: 0;">1 3 Fox Street</p><p style="margin: 0;">London W1T 1JY</p><p style="margin: 0;">United Kingdom</p><p style="margin: 0;">Phone (678) 7890741</p><p style="margin: 0;">Fax (678) 7890741</p><p style="margin: 0;">www.mycompany.com</p>";s:4:"logo";s:15:"logo-sample.png";}}s:4:"body";a:3:{s:4:"show";a:14:{s:19:"matter-id-container";b:0;s:24:"matter-subject-container";b:0;s:17:"bill-to-container";b:1;s:20:"invoice-nb-container";b:1;s:22:"invoice-date-container";b:1;s:18:"due-date-container";b:1;s:15:"terms-container";b:1;s:12:"po-container";b:1;s:17:"amount_in_letters";b:0;s:27:"time-logs-summary-container";b:0;s:15:"notes-container";b:1;s:15:"title-container";b:1;s:19:"matter-reference-nb";b:0;s:10:"tax_number";i:1;}s:3:"css";a:1:{s:10:"margin-top";s:4:"0.38";}s:7:"general";a:1:{s:10:"line_items";a:3:{s:8:"expenses";s:1:"1";s:9:"time_logs";s:1:"2";s:5:"items";s:1:"3";}}}s:6:"footer";a:2:{s:4:"show";a:2:{s:16:"footer-container";b:0;s:14:"page-numbering";b:0;}s:7:"general";a:1:{s:5:"notes";s:39:"<p align="center">www.app4legal.com</p>";}}}');
        $settings = array();
        $result = true;
        foreach ($templates as $key => $template) {
            $settings = $default_template;
            $settings['body']['show']['sub-total-discount'] = false;
            $settings = serialize($settings);
            $update = "UPDATE organization_invoice_templates SET settings = '{$settings}' WHERE organization_invoice_templates.id = {$template['id']}";
            $result = $this->db->query($update);
            if ($result) {
                $this->write_log($this->log_path, 'Done - Migrate the template data of id = '.$template['id']);
            } else {
                $this->write_log($this->log_path, 'Error - Failed to update the template data of id = '.$template['id']);
            }
        }
    }
    
    public function add_api_key_validity_time_to_instance_file(){
        $this->write_log($this->log_path, 'Start - add_api_key_validity_time_to_instance_file()');
        
        $file = getcwd().DIRECTORY_SEPARATOR."application".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."instance.php";
        
        if(file_exists($file)){
            if(!$this->instance_option_already_exists('api_key_validity_time', $file)){
                $data = PHP_EOL . '$config[\'api_key_validity_time\'] = \'24\'; //time in-terms of hours' . PHP_EOL . PHP_EOL;
                if(!file_put_contents($file, $data, FILE_APPEND | LOCK_EX)){
                    $this->write_log($this->log_path, '    couldn\'t modify instance.php');
                }
            }
        } else{
            $this->write_log($this->log_path, 'instance.php doesn\'t exists');
        }
        
        $this->write_log($this->log_path, 'End - add_api_key_validity_time_to_instance_file()');
    }
    
    private function instance_option_already_exists($option, $instance_file){
        $this->write_log($this->log_path, '    Start - instance_option_already_exists()');
        $lines = file($instance_file, FILE_IGNORE_NEW_LINES);
        
        foreach($lines as $key => $line) {
            if(strpos($line, $option) > 0){
                return true;
            }
        }
        
        $this->write_log($this->log_path, '    End - instance_option_already_exists()');
        
        return false;
    }
    
    public function migrate_users_api_keys()
    {
        $this->write_log($this->log_path, 'Start - migrate_users_api_keys()');
        $sql = 'select users.id, users.api_key from users WHERE api_key IS NOT NULL';
        $query_execution = $this->db->query($sql);
        $users = $query_execution->result_array();
        $migration_done = true;
        
        if(!empty($users)){
            
            $this->load->model('user_api_key', 'user_api_keyfactory');
            $this->user_api_key = $this->user_api_keyfactory->get_instance();
            
            foreach($users as $user){
                if(!$this->user_api_key->fetch(['user_id' => $user['id'], 'api_key' => $user['api_key']])){
                    $data = [
                        'user_id' => $user['id'],
                        'api_key' => $user['api_key'],
                        'key_generated_on' => date('Y-m-d H:i:s')
                    ];

                    if(!$this->db->insert('user_api_keys', $data)){
                        $migration_done = false;
                        $this->write_log($this->log_path, '    user data didn\'t migrated correctly: '. implode(', ', $this->user_api_key->get('validationErrors')));
                    }
                }
            }
        } else{
            $this->write_log($this->log_path, '    no users found to be migrated!');
        }
        
        if($migration_done){
            $this->remove_api_key_from_table_users();
        }
        
        $this->write_log($this->log_path, 'End - migrate_users_api_keys()');
    }
    
    private function remove_api_key_from_table_users(){
        $this->write_log($this->log_path, '    Start - remove_api_key_from_table_users()');
        $query = 'ALTER TABLE users DROP COLUMN api_key';
        
        if($this->db->dbdriver === 'sqlsrv'){
            $this->remove_api_key_column_constraint();
        }
        
        if($this->db->query($query)){
            $this->write_log($this->log_path, '        column api_key dropped successfully from the users table');
        } else{
            $this->write_log($this->log_path, '        can\'t drop column api_key from the users table');
        }
        
        $this->write_log($this->log_path, '    End - remove_api_key_from_table_users()');
    }
    
    private function remove_api_key_column_constraint(){
        $this->write_log($this->log_path, '    Start - remove_api_key_column_constraint()');
        $query = 'DECLARE @sql NVARCHAR(MAX)
        WHILE 1=1
        BEGIN
            SELECT TOP 1 @sql = N\'alter table users drop constraint [\'+dc.NAME+N\']\'
            from sys.default_constraints dc
            JOIN sys.columns c
                ON c.default_object_id = dc.object_id
            WHERE 
                dc.parent_object_id = OBJECT_ID(\'users\')
            AND c.name = N\'api_key\'
            IF @@ROWCOUNT = 0 BREAK
            EXEC (@sql)
        END';
        
        if($this->db->query($query)){
            $this->write_log($this->log_path, '        column api_key dropped successfully from the users table');
        } else{
            $this->write_log($this->log_path, '        can\'t drop column api_key from the users table');
        }
        
        $this->write_log($this->log_path, '    End - remove_api_key_column_constraint()');
    }
}
