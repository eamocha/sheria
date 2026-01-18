<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_20 extends CI_Controller
{

    use MigrationLogTrait;

    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }

    public function index()
    {
        $this->replace_new_logos();
        $this->remove_wrong_country();
        $this->update_saved_invoice_template();
        $this->add_file_extensions();
        $this->remove_litigation_transition_data();
        $this->write_log($this->log_path, 'done from migration script');
    }

    private function replace_new_logos()
    {
        $this->write_log($this->log_path, 'start copying default logos');
        $main_default_theme_path =  "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "main_default_theme" . DIRECTORY_SEPARATOR;
        $default_path =  "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR;
        $instance_path =  "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR;
        $copy = [
            $main_default_theme_path . "app4legal-logo.png" => $default_path . "app4legal-logo.png",
            $main_default_theme_path . "customer-portal-login-logo.png" => $default_path . "a4l_logo_sign_in.png",
            $main_default_theme_path . "favicon.ico" => $default_path . "favicon.ico",
            $instance_path . "app4legal-logo.png" => $default_path . "app4legal-logo.png",
            $instance_path . "a4l_logo_sign_in.png" => $default_path . "a4l_logo_sign_in.png",
            $instance_path . "favicon.ico" => $default_path . "favicon.ico"
        ];
        foreach($copy as $destination => $source){
            $this->write_log($this->log_path, 'copy ' . $source . ' to ' . $destination);
            if(file_exists($source) && file_exists($destination)){
                if(copy($source , $destination)){
                    $this->write_log($this->log_path, 'copied successfully');
                }else{
                   $this->write_log($this->log_path, 'failed to copy');
                }
            }else{
                $this->write_log($this->log_path, 'source of destination is not found');
            }
        }
    }
    
    public function remove_wrong_country(){
        $this->write_log($this->log_path, 'start removing country 103 from contacts table (country id)');
        if ($this->db->query("update contacts set country_id = NULL WHERE country_id = '103'")) {
            $this->write_log($this->log_path, 'Done - country 103 deleted successfully from contacts');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from contacts');
        }
        
        $this->write_log($this->log_path, 'start removing country 103 from contacts nationalities table');
        if ($this->db->query("delete from contact_nationalities WHERE nationality_id = '103'")) {
            $this->write_log($this->log_path, 'Done - country 103 deleted successfully from contact nationalities');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from contact nationalities');
        }
        
        $this->write_log($this->log_path, 'start removing country 103 from company table (nationality id)');
        if ($this->db->query("update companies set nationality_id = NULL WHERE nationality_id = '103'")) {
            $this->write_log($this->log_path, 'Done - country 103 deleted successfully from companies');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from companies');
        }
        
        $this->write_log($this->log_path, 'start removing country 103 from company addresses table (country)');
        if ($this->db->query("update company_addresses set country = NULL WHERE country = '103'")) {
            $this->write_log($this->log_path, 'Done - country 103 deleted successfully from company addresses');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from company addresses');
        }
        
        $this->write_log($this->log_path, 'start removing country 103 from IP table (country id)');
        if ($this->db->query("update ip_details set country_id = NULL WHERE country_id = '103'")) {
            $this->write_log($this->log_path, 'Done - country 103 deleted successfully from IP table');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from IP table');
        }
        
        $this->write_log($this->log_path, 'start removing country 103 from countries table');
        if ($this->db->query("DELETE FROM countries WHERE id = '103'")) {
            $this->write_log($this->log_path, 'Done - country 103 deleted successfully');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from countries table');
        }
    }

    public function update_saved_invoice_template()
    {
        $this->write_log($this->log_path, 'start update invoice template to be compatible with new summary options');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $value => &$template){
            $unserialize_tempalte = unserialize($template['settings']);
            if(isset($unserialize_tempalte['body']['show']['time-logs-summary-container'])){
                if($unserialize_tempalte['body']['show']['time-logs-summary-container'] == true) {
                    $unserialize_tempalte['body']['show']['show-user-code'] = true;
                } else {
                    $unserialize_tempalte['body']['show']['show-user-code'] = false;
                }
                if($unserialize_tempalte['body']['show']['time-logs-summary-container'] == true){
                    $unserialize_tempalte['body']['show']['time-logs-summary-container'] = '1';
                } else {
                    $unserialize_tempalte['body']['show']['time-logs-summary-container'] = '0';
                }
                $template['settings'] = serialize($unserialize_tempalte);
            }
            $template['settings'] = addslashes($template['settings']);
            $this->db->query("update organization_invoice_templates set settings = '{$template['settings']}' WHERE id = {$template['id']}");
        }
        unset($template);
        $this->write_log($this->log_path, 'End update invoice template to be compatible with new summary options');
    }
    public function add_file_extensions()
    {
        $extensions = ['ogg|wmv', 'aiff|aif', 'm4a|m4v|au'];
        foreach($extensions as $extension){
            $this->add_extensions($extension);
        }
    }
    private function add_extensions($extension)
    {
        $this->write_log($this->log_path, 'Changes on allowed files upload started', 'info');
        $allowed_file_uploads_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php';

        if(!file_exists($allowed_file_uploads_path)){
            $this->write_log($this->log_path, 'target file not found (' . $allowed_file_uploads_path . ')', 'error');
            return;
        }

        $target_keys = [
            'company', 'contact', 'case', 'doc', 'BI', 'BI-PY', 'EXP', 'INV', 'QOT', 'INV-PY', 'task', 'caseContainer'
        ];
        $handle = fopen($allowed_file_uploads_path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // process the line read.
                foreach($target_keys as $target_key){
                    $str_pos = strpos($line, '$config[\'' . $target_key . '\']');
                    if($str_pos !== false){
                        $this->write_log($this->log_path, 'replacing config variable: ' . '$config[\'' . $target_key . '\']', 'info');
                        $new_line = substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")+2);
                        file_put_contents($allowed_file_uploads_path, str_replace($new_line, substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")) . "|" . $extension ."';", file_get_contents($allowed_file_uploads_path)));
                    }
                }
            }
            fclose($handle);
        } else {
            $this->write_log($this->log_path, 'error opening the file (' . $allowed_file_uploads_path . ')', 'error');
        }

        $this->write_log($this->log_path, 'Changes on allowed files upload is done', 'info');
    }
    public function remove_litigation_transition_data()
    {
        $this->write_log($this->log_path, 'Removing Litigation fields from corporate matter transitions started', 'info');
        $this->load->model('workflow', 'workflowfactory');
        $this->workflow = $this->workflowfactory->get_instance();
        $corporate_workflows = $this->workflow->load_all(array('where' => array('category', 'matter')));
        $this->write_log($this->log_path, 'Fetched all Workflows applicable on corporate matters');
        if(!empty($corporate_workflows)){
            $this->load->model('workflow_status_transition', 'workflow_status_transitionfactory');
            $this->workflow_status_transition = $this->workflow_status_transitionfactory->get_instance();
            $this->load->model('workflow_status_transition_screen_field', 'workflow_status_transition_screen_fieldfactory');
            $this->workflow_status_transition_screen_field = $this->workflow_status_transition_screen_fieldfactory->get_instance();
            foreach($corporate_workflows as $workflow){
                $workflow_transitions = $this->workflow_status_transition->load_all(array('where' => array('workflow_id', $workflow['id'])));
                $this->write_log($this->log_path, 'Fetched all Transitions related to workflow of id: ' . $workflow['id']);
                if(!empty($workflow_transitions)){
                    foreach($workflow_transitions as $transition){
                        if ($this->workflow_status_transition_screen_field->fetch(array('transition' => $transition['id']))) {
                            $transition_data = unserialize($this->workflow_status_transition_screen_field->get_field('data'));
                            $this->write_log($this->log_path, 'Fetched all Fields related to transition of id: ' . $transition['id']);
                            foreach($transition_data as $key => $data){
                                if($key == 'judgmentValue' || $key == 'recoveredValue' || $key == 'legal_case_client_position_id' || $key == 'legal_case_success_probability_id' || $key == 'opponent'){
                                    unset($transition_data[$key]);
                                }
                            }
                            $this->workflow_status_transition_screen_field->set_field('data', serialize($transition_data));
                            $this->workflow_status_transition_screen_field->update();
                        }
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'Removing Litigation fields from corporate matters transitions is done');
    }
}
