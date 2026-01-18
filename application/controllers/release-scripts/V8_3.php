<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_3 extends CI_Controller
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
        $this->update_system_accounts();
        $this->add_exchange_rate_invoice_templates();
        $this->grant_users_to_access_new_permissions();
        $this->update_themes_tabs();
        $this->write_log($this->log_path, 'done from migration script');
    }

    public function update_system_accounts(){
        $this->write_log($this->log_path, 'Updating some accounts to be system accounts');
        $query = "SELECT * FROM accounts WHERE name IN ('Legal Expenses', 'Partner Expenses', 'Exchange gain or loss')";
        $accounts = $this->db->query($query);
        $accounts_array = array_column($accounts->result_array(), 'id');
        $accounts_query = implode(",", $accounts_array);
        if ($this->db->query("UPDATE accounts SET systemAccount = 'yes' WHERE id IN ( $accounts_query )")) {
            $this->write_log($this->log_path, 'Done - System accounts updates successfully');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to update system accounts');
        }
        $this->write_log($this->log_path, 'Finished updating accounts');
    }

    public function add_exchange_rate_invoice_templates()
    {
        $this->write_log($this->log_path, 'started adding currncies to invoice templates');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $value => $template){
            $settings = unserialize($template['settings']);
            if(isset($settings['body'])){
                if(!isset($settings['body']['show']['show-exchange-rate'])){
                    $settings['body']['show']['show-exchange-rate'] = false;
                }
                $template['settings'] = serialize($settings);
            }
            $this->db->query("update organization_invoice_templates set settings = '{$template['settings']}' WHERE id = {$template['id']}");
        }
        $this->write_log($this->log_path, 'currncies added to invoice templates');
    }

    public function grant_users_to_access_new_permissions(){
        $this->write_log($this->log_path, 'add new actions to user permissions');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module === 'core'){
                    $new_permissions = $group_permissions;
                    if(in_array('/reports/hearings_pending_updates/', $group_permission)  && !in_array('/reports/hearings_pending_updates_settings/', $group_permission)){
                        array_push($new_permissions['core'], '/reports/hearings_pending_updates_settings/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/reports/hearing_roll_session_per_court/', $group_permission) && !in_array('/reports/hearing_roll_session_per_court_settings/', $group_permission)){    
                        array_push($new_permissions['core'], '/reports/hearing_roll_session_per_court_settings/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/reports/task_roll_session/', $group_permission) && !in_array('/reports/task_roll_session_settings/', $group_permission)){
                        array_push($new_permissions['core'], '/reports/task_roll_session_settings/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/contacts/edit/', $group_permission) && !in_array('/contacts/related_reminders/', $group_permission)){
                        array_push($new_permissions['core'], '/contacts/related_reminders/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }

    public function update_themes_tabs()
    {
        $themes_dir = "assets".DIRECTORY_SEPARATOR."app_themes";
        $dirs = scandir($themes_dir);
        if (is_array($dirs) && count($dirs) > 2) {
            $this->write_log($this->log_path, "Done - Get all Dirs");
            unset($dirs[0], $dirs[1]);
            foreach ($dirs as $index => $value) {
                if(!is_dir($themes_dir.DIRECTORY_SEPARATOR.$value)){
                    unset($dirs[$index]);
                }
            }
            $this->write_log($this->log_path, "Done - Uset .. - . folders and files from array dirs");
            foreach ($dirs as $key => $value) {
                $theme_path = $themes_dir.DIRECTORY_SEPARATOR. $value.DIRECTORY_SEPARATOR;
                $this->write_log($this->log_path, "Done - Get Json files from theme ".$value);
                $theme_file = file_get_contents($theme_path.$value.'.json', FILE_USE_INCLUDE_PATH);
                $theme_json = json_decode($theme_file, true);
                $theme_scss = file($theme_path.$value.'.scss');
                $scss_text = '';
                $hover_text = '
        &:hover {
            background-color: $tabs_header_active_background_hover_color !important;
            color: $tabs_header_active_text_hover_color !important;
        }
    }
                ';
                foreach ($theme_scss as $line_num => $line) {
                    if ($line_num == 93) {
                        count($theme_scss) > 306 ? $scss_text.= $line : $scss_text .= $hover_text;
                    } else
                    {
                        $scss_text.= $line;
                    }
                }
                $save_file_scss = file_put_contents($theme_path.$value.'.scss', $scss_text);
                if (@!$save_file_scss) {
                    $this->write_log($this->log_path, "Error -failed to put content to file ".$value.".scss");
                } else {
                    $this->write_log($this->log_path, "Done - save scss file ".$value.".scss theme ".$value);
                }
                $data['theme'] = $theme_json;
                $scss_variables = $this->load->view('look_feel/style', $data, true);
                $save_css_variables = file_put_contents($theme_path.'variables.scss', $scss_variables);
                if (@!$save_css_variables) {
                    $this->write_log($this->log_path, "Error -failed to put content to file ".$value.".scss  theme ".$value);
                } else {
                    $this->write_log($this->log_path, "Done - save scss file variables.scss theme ".$value);
                }
                $this->load->library('scss_compiler');
                $scss = new Scss_compiler();
                $compile =  $scss->compile($scss_variables.$scss_text);
                $save_css = file_put_contents($theme_path.$value.'.css', $compile);
                if (@!$save_css) {
                    $this->write_log($this->log_path, "Error - failed to put content to file .css");
                } else {
                    $this->write_log($this->log_path, "Done - save css file .css theme ".$value);
                }
            }
        }
    }
}