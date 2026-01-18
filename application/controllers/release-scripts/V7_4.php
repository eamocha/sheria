<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class V7_4 extends CI_Controller
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
        $this->update_themes_files_remove_money_section();
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
    /**
     * update theme file and remove an money section color
     *
     * @return void
     */
    public function update_themes_files_remove_money_section()
    {
        // path to themes
        $themes_dir = "assets".DIRECTORY_SEPARATOR."app_themes";
        // get all dirs in theme path
        $dirs = scandir($themes_dir);
        // check length folders and conditional if > 2  ( .. - . )
        if (is_array($dirs) && count($dirs) > 2) {
            $this->write_log($this->log_path, "Done - Get all Dirs");
            // remove .. / . from array
            unset($dirs[0], $dirs[1]);
            $this->write_log($this->log_path, "Done - Uset .. - . folders from array dirs");
            // foreach paths and update json - scss - and complile scss to json
            foreach ($dirs as $key => $value) {
                // path to folder theme
                $theme_path = $themes_dir.DIRECTORY_SEPARATOR. $value.DIRECTORY_SEPARATOR;
                // get json file and remove an money menu
                $theme_file = file_get_contents($theme_path.$value.'.json', FILE_USE_INCLUDE_PATH);
                $this->write_log($this->log_path, "Done - Get Json files from theme ".$value);
                // convert and json to array to path to view and build an html
                $theme_json = json_decode($theme_file, true);
                // check if have section money aleardy in json file
                if (isset($theme_json['money_menu'])) {
                // unset money_menu from array
                unset($theme_json['money_menu']);
                $this->write_log($this->log_path, "Done - Unset money menu section from theme ".$value);
                // save json file theme
                $save_file_json = file_put_contents($theme_path.$value.'.json', json_encode($theme_json));
                if (@!$save_file_json) {
                    $this->write_log($this->log_path, "Error -failed to put content to file ".$value.".json theme ".$value);
                } else {
                    $this->write_log($this->log_path, "Done - save json file theme ".$value);
                }
                // get scss file and remove money lines line
                $theme_scss = file($theme_path.$value.'.scss');
                // Loop through our array line by line and append scss
                $scss_text = "";
                foreach ($theme_scss as $line_num => $line) {
                    // append an text before line 25
                    if ($line_num < 25) {
                        $scss_text .= $line;
                    }
                    // append an text after line 69
                    if ($line_num > 69) {
                        $scss_text .= $line;
                    }
                }
                // save file after remove lines
                $save_file_scss = file_put_contents($theme_path.$value.'.scss', $scss_text);
                // check file saved successfully
                if (@!$save_file_scss) {
                    $this->write_log($this->log_path, "Error -failed to put content to file ".$value.".scss");
                } else {
                    $this->write_log($this->log_path, "Done - save scss file ".$value.".scss theme ".$value);
                }
                /**
                * Afer Update Json File And Remove realated scss from and themename.scss will make variables.scss
                *  and complile with themename.scss
                */
                // path json variables from file to make variables files
                $data['theme'] = $theme_json;
                // load variables.scss file created by php
                $scss_variables = $this->load->view('look_feel/style', $data, true);
                // put scss variables created by php into scss file
                $save_css_variables = file_put_contents($theme_path.'variables.scss', $scss_variables);
                if (@!$save_css_variables) {
                    $this->write_log($this->log_path, "Error -failed to put content to file ".$value.".scss  theme ".$value);
                } else {
                    $this->write_log($this->log_path, "Done - save scss file variables.scss theme ".$value);
                }
                //load scss compiler
                $this->load->library('scss_compiler');
                //Scss_compiler init file
                $scss = new Scss_compiler();
                //compile an scss files (variables + main) and return css
                $compile =  $scss->compile($scss_variables.$scss_text);
                //save  css file after compile
                $save_css = file_put_contents($theme_path.'default.css', $compile);
                if (@!$save_css) {
                    $this->write_log($this->log_path, "Error - failed to put content to file default.css");
                } else {
                    $this->write_log($this->log_path, "Done - save css file default.css theme ".$value);
                }
                //remove an variables scss file
                $remove_variables = unlink($theme_path.'variables.scss');
                // log and remove file
                $this->write_log($this->log_path, "Done - unlink variables.css theme ".$value);
                // set new version to for theme if theme is active
                $this->load->model('instance_data');
                // update app_theme_version
                $data_save['app_theme_version'] = rand();
                // update in database
                $saving = $this->instance_data->set_values($data_save);
                // update database to set new version
                $this->write_log($this->log_path, "Done - update database to set new version theme ".$value);
                }else{
                    // theme already updated 
                    $this->write_log($this->log_path, "Done - already updated theme ".$value);
                }

            }
        }
    }
}