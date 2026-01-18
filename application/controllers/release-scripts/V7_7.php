<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class V7_7 extends CI_Controller {

    public $log_path = null;
    private $fake_themes_images_dir = 'compressed_asset/images/instance/themes/';

    public function __construct() {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->write_log($this->log_path, 'start migration script');
        $this->load->database();
        $this->load->model('instance_data');
    }

    public function index() {
        $this->generate_themes_assets();
        $this->remove_instance_login_second_logo();
        $this->restructure_timers();
        $this->remove_case_type_from_screen_fields();
        $this->migrate_invoice_templates();
        $this->updateDeleteNoteInPermissionsScheme();
        $this->updateListNotesInPermissionsScheme();
    }
    
    /**
     * get the instance login second logo value which is hard-coded declared in the config/instance.php
     * and insert it into the DB instance_data table
     * 
     */
    private function migrate_instance_login_second_logo($theme_images_dir, $clean_theme_name){
        $this->write_log($this->log_path, 'START :: migrate_instance_login_second_logo ...');
        $app_login_second_logo = "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . $this->getInstanceConfig('login_second_logo');
        
        $ext = pathinfo($app_login_second_logo, PATHINFO_EXTENSION);
        
        $this->write_log($this->log_path, 'LINE 41 :: check if client has app login second logo ...');
        
        if(file_exists($app_login_second_logo) && !empty($this->getInstanceConfig('login_second_logo'))){
            $this->write_log($this->log_path, ' :: the client has app login second logo ...');
            $app_login_second_logo_new = "app4legal-login-second-logo." . $ext;
            $cp_logo_new = "customer-portal-logo." . $ext;
            
            $this->write_log($this->log_path, 'LINE 48 :: trying to copy app login second logo to theme dir as app_login_second_logo ...');
            
            if(!copy($app_login_second_logo, $theme_images_dir . $app_login_second_logo_new)){
                $this->write_log($this->log_path, ' :: copy failed!');
            } else{
                $this->write_log($this->log_path, ' :: copy success! update the field value in the DB...');
                $this->instance_data->set_value_by_key('app_login_second_logo',$this->fake_themes_images_dir . $clean_theme_name . "/" . $app_login_second_logo_new);
            }
            
            $this->write_log($this->log_path, 'LINE 57 :: trying to copy app login second logo to theme dir as customer_portal_logo ...');
            
            if(!copy($app_login_second_logo, $theme_images_dir . $cp_logo_new)){
                $this->write_log($this->log_path, ' :: copy failed!');
            } else{
                $this->write_log($this->log_path, ' :: copy success! update the field value in the DB...');
                $this->instance_data->set_value_by_key('customer_portal_logo',$this->fake_themes_images_dir . $clean_theme_name . "/" . $cp_logo_new);
            }
        }
        
        $this->write_log($this->log_path, 'DONE :: migrate_instance_login_second_logo ...');
    }
    
    /**
     * Remove the "on-server" login second logo config option from the application/config/instance.php
     * 
     */
    private function remove_instance_login_second_logo(){
        $this->write_log($this->log_path, 'START :: remove_instance_login_second_logo ...');
        $file = getcwd().DIRECTORY_SEPARATOR."application".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."instance.php";
        $msg = "Unable to remove line from {$file}";
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        
        $this->write_log($this->log_path, 'LINE 80 :: looping over the instance.php lines to find the login_second_logo ...');
        
        foreach($lines as $key => $line) {
            if(strpos($line, 'login_second_logo') > 0){
                unset($lines[$key]);
                $msg = "The {$file} has been changed successfully!";
                break;
            } else{
                $msg = "Unable to remove line from {$file}. Line not found!";
            }
        }
        
        $data = implode(PHP_EOL, $lines);
        file_put_contents($file, $data);
        
        $this->write_log($this->log_path, $msg);
        $this->write_log($this->log_path, 'DONE :: remove_instance_login_second_logo ...');
    }
    
    /**
     * generate the themes images dirs
     * and do the needed assets (css, & json) migrations
     * 
     */
    public function generate_themes_assets(){
        $this->write_log($this->log_path, 'START :: generate_themes_assets ...');
        // path of themes
        $themes_path = "assets" . DIRECTORY_SEPARATOR . "app_themes";
        
        // path of themes images
        $images_path = "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "themes" . DIRECTORY_SEPARATOR;
        
        $this->write_log($this->log_path, 'LINE 112 :: check if the themes images directory exists or not ...');
        
        if(!is_dir($images_path)){
            
            $this->write_log($this->log_path, 'LINE 116 :: The themes images directory does not exists, trying to create it ...');
            
            if(!mkdir($images_path)){
                $this->write_log($this->log_path, ' :: failed to create the themes images directory with this path: ' . $images_path . ' ...');
            }
        }
        
        // remove the main_default
        $themes_names = $this->scan_filename_recursivly($themes_path);
        
        if(in_array('main_default', $themes_names)){
            unset($themes_names['main_default']);
        }
        
        $themes_names = array_values($themes_names);
        //EOL remove the main_default
        
        $this->write_log($this->log_path, 'LINE 137 :: searching the images/instance directory for the uploaded-logo file ...');
        
        $app_logo = '';
        
        $uploaded_app_logo = glob("assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "uploaded-logo" . ".{jpg,png,gif,jpeg}", GLOB_BRACE);
        
        foreach($uploaded_app_logo as $logo){
            if(file_exists($logo)){
                $this->write_log($this->log_path, ' :: the uploaded-logo file has been found, trying to rename it...');
                $ext = pathinfo($logo, PATHINFO_EXTENSION);
                
                $new_logo_name = "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "app4legal-logo." . $ext;
                
                rename($logo, $new_logo_name);
                
                $app_logo =  "app4legal-logo." . $ext;
            }
        }
        
        // remove the default favicon to avoid naming conflicts
        $app_favicon_default = glob("assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "favicon" . ".{jpg,png,gif,jpeg,ico,icon}", GLOB_BRACE);
        
        foreach($app_favicon_default as $logo){
            unlink($logo);
        }
        
        $this->write_log($this->log_path, 'LINE 159 :: searching the images/instance directory for the uploaded-favicon file ...');
        
        $app_favicon = '';
        
        $uploaded_app_favicon = glob("assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "uploaded-favicon" . ".{jpg,png,gif,jpeg,ico,icon}", GLOB_BRACE);
        
        foreach($uploaded_app_favicon as $logo){
            if(file_exists($logo)){
                $this->write_log($this->log_path, ' :: the uploaded-favicon file has been found, trying to rename it...');
                $ext = pathinfo($logo, PATHINFO_EXTENSION);
                
                $new_logo_name = "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "favicon." . $ext;
                
                rename($logo, $new_logo_name);
                
                $app_favicon = "favicon." . $ext;
            }
        }
        
        $theme_counter = 0;
        $nb_of_themes = count($themes_names);
        
        $this->write_log($this->log_path, 'LINE 181 :: looping over the themes directories in the app_themes folder ...');
        
        // create the themes images dirs & clean the themes names to not include any unwanted characters
        foreach($themes_names as $theme){
            $this->write_log($this->log_path, ' :: working on the theme '.$theme);
            $clean_theme_name = preg_replace('/[^a-z0-9\-\_]+/', '', strtolower($theme));
            
            $theme_images_dir = $images_path . $clean_theme_name . DIRECTORY_SEPARATOR;
            $this->write_log($this->log_path, ' LINE 190 :: working on the theme of name = '.$theme.' and clean name = '.$clean_theme_name);
            $this->write_log($this->log_path, ' :: check if theme images dir exists or not ...');
            
            if(!is_dir($theme_images_dir)){
                $this->write_log($this->log_path, ' the images dir of theme '.$theme.' does not exist, trying to make it...');
                if(mkdir($theme_images_dir)){
                    $this->copy_main_default_theme_images($theme_images_dir, $clean_theme_name, $app_logo, $app_favicon, $theme_counter == ($nb_of_themes - 1));
                } else{
                    $this->write_log($this->log_path, " :: This is not a valid directory: ".$theme_images_dir);
                }
            } else{
                $this->copy_main_default_theme_images($theme_images_dir, $clean_theme_name, $app_logo, $app_favicon, $theme_counter == ($nb_of_themes - 1));
            }
            
            $theme_dir = $themes_path . DIRECTORY_SEPARATOR . $theme;
            
            $this->write_log($this->log_path, 'LINE 201 :: check if theme dir exists or not ...');
            
            if(is_dir($theme_dir)){
                $this->copy_theme_css_json_assets($theme, $theme_dir, $clean_theme_name, $themes_path);
            } else{
                $this->write_log($this->log_path, ' :: theme dir does not exists: ' . $theme_dir);
            }
            
            $this->write_log($this->log_path, 'LINE 209 :: trying to change the name of the current active theme (in DB) to the clean name ...');
            
            // change the name of current active theme to the clean one
            if(isset($this->instance_data->get_value_by_key('app_theme')['keyValue']) && $this->instance_data->get_value_by_key('app_theme')['keyValue'] == $theme){
                $this->write_log($this->log_path, ' :: theme name has been updated successfully!');
                $this->instance_data->set_value_by_key('app_theme', $clean_theme_name);
            }

            $theme_counter++;
        }
        
        $this->write_log($this->log_path, 'DONE :: generate_themes_assets ...');
    }
    
    /**
     * Copy the main_default_theme images to the parameterized theme dir
     * 
     * @param type $theme_images_dir
     */
    private function copy_main_default_theme_images($theme_images_dir, $clean_theme_name, $app_logo = '', $app_favicon = '', $remove_old_uploaded_images = false){
        $this->write_log($this->log_path, 'START :: copy_main_default_theme_images ...');
        $main_default_theme_images_path =  "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . "main_default_theme" . DIRECTORY_SEPARATOR;
        
        $main_default_theme_images = [
            'app4legal-logo.png' => 'app4legal-logo.png',
            'customer-portal-login-logo.png' => 'customer-portal-login-logo.png',
            'favicon.ico' => 'favicon.ico'
        ];
        
        if(is_dir($main_default_theme_images_path)){
            if(is_dir($theme_images_dir)){
                if($app_logo !== ''){
                    if(copy("assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . $app_logo, $theme_images_dir . $app_logo)){
                        unset($main_default_theme_images['app4legal-logo.png']);

                        if($remove_old_uploaded_images){
                            unlink("assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . $app_logo);
                        }
                    }
                }

                if($app_favicon !== ''){
                    if(copy("assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . $app_favicon, $theme_images_dir . $app_favicon)){

                        unset($main_default_theme_images['favicon.ico']);

                        if($remove_old_uploaded_images){
                            unlink("assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "instance" . DIRECTORY_SEPARATOR . $app_favicon);
                        }
                    }
                }

                foreach($main_default_theme_images as $key => $image){
                    $image_name = $main_default_theme_images_path . $image;

                    if(file_exists($image_name)){
                        copy($image_name, $theme_images_dir . $image);
                    }
                }

                $this->migrate_instance_login_second_logo($theme_images_dir, $clean_theme_name);
            } else{
                $this->write_log($this->log_path, ' :: wrong path: ' . $theme_images_dir);
            }
        } else{
            $this->write_log($this->log_path, ' :: wrong path: ' . $main_default_theme_images_path);
        }
        
        $this->write_log($this->log_path, 'DONE :: copy_main_default_theme_images ...');
    }
    
    /**
     * copy the css & json files to the theme dir
     * 
     * @param type $theme
     * @param type $theme_dir
     * @param type $clean_theme_name
     * @param type $themes_path
     */
    private function copy_theme_css_json_assets($theme, $theme_dir, $clean_theme_name, $themes_path){
        $this->write_log($this->log_path, 'START :: copy_theme_css_json_assets ...');
        // The default.css actulay is the $theme.css! so fix it's name!
        $default_css = $theme_dir . DIRECTORY_SEPARATOR . "default.css";

        if(file_exists($default_css) && $clean_theme_name !== 'default'){
            rename($default_css, $theme_dir . DIRECTORY_SEPARATOR . $clean_theme_name . ".css");
        }
        
        // Rename the $theme.scss with the clean name
        $theme_scss = $theme_dir . DIRECTORY_SEPARATOR . $theme . ".scss";
        $clean_theme_scss = $theme_dir . DIRECTORY_SEPARATOR . $clean_theme_name . ".scss";
        
        if(file_exists($theme_scss)){
            rename($theme_scss, $clean_theme_scss);
        }
        
        $this->modify_theme_json($theme, $theme_dir, $clean_theme_name, $themes_path);
        $this->add_customer_portal_scss_to_theme($theme_dir, $clean_theme_name, $themes_path);
        
        /**
         * after finishing all migrations of theme files
         * rename the theme dir name by the clean name
         */
        rename($theme_dir, $themes_path . DIRECTORY_SEPARATOR . $clean_theme_name);
        
        /**
         * and finally compile the scss!
         */
        $this->compilescsstocss($clean_theme_name);
        $this->write_log($this->log_path, 'DONE :: copy_theme_css_json_assets ...');
    }
    
    /**
     * MODIFY $theme.json
     * rename it to be valid with the new theme clean name, 
     * then add the new part of Customer_Portal
     * or copy it from the default theme dir if it doesn't exists at all
     * 
     * @param type $theme
     * @param type $theme_dir
     * @param type $clean_theme_name
     * @param type $themes_path
     */
    private function modify_theme_json($theme, $theme_dir, $clean_theme_name, $themes_path){
        $this->write_log($this->log_path, 'START :: modify_theme_json ...');
        $theme_json_file = $theme_dir . DIRECTORY_SEPARATOR . $theme . ".json";
        $clean_theme_json_file = $theme_dir . DIRECTORY_SEPARATOR . $clean_theme_name . ".json";
        $default_json = $themes_path . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "main_default.json";
        
        if(file_exists($theme_json_file)){
            $theme_json = json_decode(file_get_contents($theme_json_file), true);
            
            $json = json_decode('{
                "customer_portal": {
                    "customer_portal_menu": {
                        "menu_background_color": "#ffffff",
                        "menu_button_background_color": "transparent",
                        "menu_background_hover_color": "#e7e7e7",
                        "menu_text_color": "#777777",
                        "menu_text_hover_color": "#333333"
                    },
                    "customer_portal_buttons": {
                        "buttons_background_color": "#3B7FC4",
                        "buttons_hover_background_color": "#4796e6",
                        "buttons_text_color": "#ffffff",
                        "buttons_hover_text_color": "#ffffff"
                    },
                    "customer_portal_links": {
                        "links_text_color": "#428bca",
                        "links_text_hover_color": "#2a6496"
                    }
                }
            }
            ', true);
            
            $theme_json['customer_portal'] = $json['customer_portal'];
            
            unlink($theme_json_file);
            file_put_contents($clean_theme_json_file, json_encode($theme_json));
        } else{
            copy($default_json, $clean_theme_json_file);
        }
        
        $this->write_log($this->log_path, 'DONE :: modify_theme_json ...');
    }
    
    /**
     * Add the Customer Portal scss to the $theme
     * 
     * @param type $theme
     * @param type $theme_dir
     * @param type $clean_theme_name
     * @param type $themes_path
     */
    private function add_customer_portal_scss_to_theme($theme_dir, $clean_theme_name, $themes_path){
        $this->write_log($this->log_path, 'START :: add_customer_portal_scss_to_theme ...');
        $theme_cp_scss = $theme_dir . DIRECTORY_SEPARATOR . $clean_theme_name . "_customer_portal.scss";
        $default_cp_scss = $themes_path . DIRECTORY_SEPARATOR . "default" . DIRECTORY_SEPARATOR . "default_customer_portal.scss";
        
        if(file_exists($default_cp_scss)){
            copy($default_cp_scss, $theme_cp_scss);
        } else{
            $cp_scss = '.navbar.navbar-default{
    
    background-color: $customer_portal_menu_background_color !important;
    background: $customer_portal_menu_background_color !important;
    
    ul.navbar-nav{
        
        > li{
            
            > a{
                
                color: $customer_portal_menu_text_color !important;
                background-color: $customer_portal_menu_button_background_color !important;
                background: $customer_portal_menu_button_background_color !important;
                
                &:hover{
                    
                    color: $customer_portal_menu_text_hover_color !important;
                }
            }
            
            &.active{
                
                > a{
                    
                    color: $customer_portal_menu_text_hover_color !important;
                    background-color: $customer_portal_menu_background_hover_color !important;
                    background: $customer_portal_menu_background_hover_color !important;
                }
            }
        }
    }
    
    .btn:not(.btn-link){
        
        color: $customer_portal_menu_text_color !important;
        background-color: $customer_portal_menu_background_hover_color !important;
        background: $customer_portal_menu_background_hover_color !important;
        
        &:hover{
                    
            color: $customer_portal_menu_text_color !important;
            background-color: $customer_portal_menu_background_hover_color !important;
            background: $customer_portal_menu_background_hover_color !important;
        }
    }
}

.btn:not(.btn-link):not(.btn-sm):not(.cp-customizable-link), 
.btn.btn-default:not(#addMoreAttachment):not(.btn-sm):not(.cp-customizable-link), 
.dataTables_paginate ul.pagination li.active a:not(.cp-customizable-link){
    
    background-color: $customer_portal_buttons_background_color !important;
    background: $customer_portal_buttons_background_color !important;
    color: $customer_portal_buttons_text_color !important;
    
    &:hover{
        
        background-color: $customer_portal_buttons_hover_background_color !important;
        background: $customer_portal_buttons_hover_background_color !important;
        color: $customer_portal_buttons_hover_text_color !important;
    }
}

#files_attachment button.btn.btn-default.btn-sm{
    background-color: inherit;
    background: inherit;
}

.cp-customizable-link{
    
    color: $customer_portal_links_text_color !important;
    
    &:hover,
    &:active{
        
        color: $customer_portal_links_text_hover_color !important;
    }
}
            ';
            
            file_put_contents($theme_cp_scss, $cp_scss);
        }
        
        $this->write_log($this->log_path, 'DONE :: add_customer_portal_scss_to_theme ...');
    }
    
    /**
     * compilescsstocss function
     *
     * @param [type] $themename
     * @return void
     */
    private function compilescsstocss($themename){
        $this->write_log($this->log_path, 'START :: compilescsstocss ...');
        // load scss compiler
        $this->load->library('scss_compiler');
        // path of themes
        $path = "assets".DIRECTORY_SEPARATOR."app_themes";
        // generate customer portal css files
        $this->generate_css($path,$themename,'_customer_portal', true);
        $this->write_log($this->log_path, 'DONE :: compilescsstocss ...');
    }
    
    private function generate_css($path,$themename,$file_name_suffix = '', $remove_variables = false){
        $this->write_log($this->log_path, 'START :: generate_css ...');
        $cp_variables_file = $path.DIRECTORY_SEPARATOR.$themename.DIRECTORY_SEPARATOR.'cp_variables.scss';
        $cp_variables = '
            $customer_portal_menu_background_color: #ffffff;
            $customer_portal_menu_background_hover_color: #e7e7e7;
            $customer_portal_menu_text_color: #777777;
            $customer_portal_menu_text_hover_color: #333333;
            $customer_portal_menu_button_background_color: transparent;
            $customer_portal_buttons_background_color: #3B7FC4;
            $customer_portal_buttons_hover_background_color: #4796e6;
            $customer_portal_buttons_text_color: #ffffff;
            $customer_portal_buttons_hover_text_color: #ffffff;
            $customer_portal_links_text_color: #428bca;
            $customer_portal_links_text_hover_color: #2a6496;
        ';
        
        file_put_contents($cp_variables_file, $cp_variables);
        
        $theme_variables_file = file_get_contents($cp_variables_file,FILE_USE_INCLUDE_PATH);
        // get theme main file
        $filepath_theme_main = $path.DIRECTORY_SEPARATOR.$themename.DIRECTORY_SEPARATOR.$themename.$file_name_suffix.'.scss';
        $theme_main_file = file_get_contents($filepath_theme_main,FILE_USE_INCLUDE_PATH);
        // Scss_compiler init file
        $scss = new Scss_compiler();
        // compile an scss files (variables + main) and return css
        $compile =  $scss->compile($theme_variables_file.$theme_main_file);
        // make an final css file
        $filepath_css = $path.DIRECTORY_SEPARATOR.$themename.DIRECTORY_SEPARATOR.$themename.$file_name_suffix.'.css';
        file_put_contents($filepath_css,$compile);
        // remove the variables scss file after finishing generating all the css files
        if($remove_variables){
            unlink($cp_variables_file);
        }
        
        $this->write_log($this->log_path, 'DONE :: generate_css ...');
    }
    
    /**
     * To get the config/instance.php values
     * 
     * @param type $parameter
     * @return type
     */
    private function getInstanceConfig($parameter){
        $this->write_log($this->log_path, 'START :: getInstanceConfig ...');
        $this->config->load('instance', true);
        $parameterValue = $this->config->item($parameter, 'instance');
        
        $this->write_log($this->log_path, 'DONE :: getInstanceConfig ...');
        
        return $parameterValue ? $parameterValue : "";
    }
    
    /**
     * Select All Json Files From Path Themes
     *
     * @param string $path
     * @param array $name
     * @return $name
     */
    private function scan_filename_recursivly($path = '', &$name = []){
        $this->write_log($this->log_path, 'START :: scan_filename_recursivly ...');
        $path = $path == ''? dirname(__FILE__) : $path;
        $lists = @scandir($path);
        
        if(!empty($lists)){
            foreach($lists as $f){ 
                if(is_dir($path.DIRECTORY_SEPARATOR.$f) && $f != ".." && $f != "."){
                    $this->scan_filename_recursivly($path.DIRECTORY_SEPARATOR.$f, $name); 
                } else{
                    if (!in_array($f,array(".","..")) AND pathinfo($path.DIRECTORY_SEPARATOR.$f, PATHINFO_EXTENSION) == 'json'){ 
                        $themename = str_replace(".json","",$f);
                        $name[str_replace(".json","",$f)] = $themename;
                    }
                }
            }
        }
        
        $this->write_log($this->log_path, 'DONE :: scan_filename_recursivly ...');
        
        return $name;
    }
    
    private function write_log($file_path, $message, $type = 'info') {
        $log_path = FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $file_path . '.log';
        $pr = fopen($log_path, 'a');
        fwrite($pr, date('Y-m-d H:i:s') . " [$type] - $message \n");
        fclose($pr);
        if ($type == 'error') {
            echo $type . ': ' . $message . ". Please check the log file '$log_path' for more details and to fix the error";
            exit;
        }
    }
    
    /**
     * restructure_timers function
     * @example timer - get all timers in table user_preferences and restructure to work with new logic
     * @return void
     */
    public function restructure_timers(){
        $get_all_active_timers = $this->db->query(
            "SELECT * FROM user_preferences WHERE keyName='activityLogTimer'"
            )->result_array();
        // check if data not empty
        if(isset($get_all_active_timers) && !empty($get_all_active_timers)){
            foreach ($get_all_active_timers as $key => $value) {
            if ($this->db->dbdriver === 'sqlsrv') {
                $value['id'] = $value['user_id'];
            }
             $this->write_log($this->log_path, " get old timer data id=".$value['id']);
            if($value['keyValue']){
                    // get old timer data
              $oldtimer = unserialize($value['keyValue']);
              // check if this old structure
              if(isset($oldtimer['startedOn'])){
                // init new timer structure
                    $timer['id'] = rand();
                    $timer['status'] = 'active';
                    $timer['logs'] = array(
                                        array('start_date'=>$oldtimer['startedOn'],'end_date'=>null)
                                );
                    $timer['description'] = '' ; 
                    $timer['object'] = [];
                    $task_id  = $oldtimer['task_id'];
                    $legalcase_id  = $oldtimer['legal_case_id'];
                    if(isset($task_id) && !empty($task_id)){
                        array_push($timer['object'],['task'=>$task_id]);
                    }
                    if(isset($legalcase_id) && !empty($legalcase_id)){
                        array_push($timer['object'],['matter'=>$legalcase_id]);
                    }
                    $timers[] = $timer;
                    // update query to set new structure
                    $update = $this->db->query("UPDATE user_preferences SET keyValue ='".serialize($timers)."' WHERE id=".$value['id']);
                    if($update){
                        $msg = "update timer structure id=".$value['id'];
                        $this->write_log($this->log_path, $msg);
                    }else{
                        $msg = "fail update timer structure id=".$value['id'];
                        $this->write_log($this->log_path, $msg);
                    }
                }else{
                        $msg = "timer already in new structure";
                        $this->write_log($this->log_path,$msg ); 
                }
              }
            }

            
        }
    }
    
    /**
     * remove the case type field from the screen field list
     *
     * @return void
     */
    public function remove_case_type_from_screen_fields()
    {
        $this->write_log($this->log_path, 'Load the screen fields');
        $sql = 'select id,data from workflow_status_transition_screen_fields';
        $query_execution = $this->db->query($sql);
        $results = $query_execution->result_array();
        if (!empty($results)) {
            foreach ($results as $screen) {
                $data = unserialize($screen['data']);
                if (isset($data['type'])) {
                    unset($data['type']);

                    $new_data = serialize($data);

                    $update = "UPDATE workflow_status_transition_screen_fields SET data = '{$new_data}' WHERE workflow_status_transition_screen_fields.id = {$screen['id']}";
                    $result = $this->db->query($update);
                    if ($result) {
                        $this->write_log($this->log_path, 'Done - Updated the data of id = ' . $screen['id']);
                    } else {
                        $this->write_log($this->log_path, 'Error - Failed to update the data of id = ' . $screen['id']);
                    }
                }
            }
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
                $settings['body']['show']['tax_number'] = true;
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
    
    /*
     * This update is required to give clients the access to delete email note if they already have access to delete a note
     */
    
    public function updateDeleteNoteInPermissionsScheme(){
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module === 'core'){
                    // grant access to delete email note if the user has access to delete a comment
                    if(in_array('/cases/delete_comment/', $group_permission)){
                        $key = array_search('/cases/delete_comment/', $group_permission);
                        $new_permissions = $group_permissions;
                        unset($new_permissions['core'][$key]);
                        array_push($new_permissions['core'], '/cases/delete_comment/', '/cases/delete_email_comment/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
    }
    /*
     * This update is required to give clients the access to list all notes and emails if they already have access to list all threads
     */
    
    public function updateListNotesInPermissionsScheme(){
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module === 'core'){
                    // grant access to Notes and Emails tabs if the user has access to All Threads
                    if(in_array('/cases/get_all_comments/', $group_permission)){
                        $key = array_search('/cases/get_all_comments/', $group_permission);
                        $new_permissions = $group_permissions;
                        unset($new_permissions['core'][$key]);
                        array_push($new_permissions['core'], '/cases/get_all_comments/', '/cases/get_all_core_and_cp_comments/', '/cases/get_all_email_comments/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
    }
}
