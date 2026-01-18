<?php

class Ci_top_controller extends CI_Controller
{
    public $coreLicensePackage = ["core", "money", "api", "encoder-generator"];
    public $system_preferences;
    public $timezone;
    public $timezoneOffset;
    public $instance_data_array = [];
    public $cloud_installation_type = "";
    public $instance_client_type = "";
    public function __construct()
    {
        parent::__construct();
        $this->load->model("instance_data");
        $this->instance_data_array = $this->instance_data->get_values();
        $this->cloud_installation_type = $this->instance_data_array["installationType"] == "on-cloud";
        if ($this->cloud_installation_type) {
            $this->instance_client_type = isset($this->instance_data_array["clientType"]) ? $this->instance_data_array["clientType"] : NULL;
            $this->instance_subscription = isset($this->instance_data_array["subscription"]) ? $this->instance_data_array["subscription"] : NULL;
        }
        $this->system_preferences = $this->get_all_system_preferences_values();
        $systemDefaultValues = $this->system_preferences["SystemValues"];
        $this->timezone = isset($systemDefaultValues["systemTimezone"]) && $systemDefaultValues["systemTimezone"] ? $systemDefaultValues["systemTimezone"] : $this->config->item("default_timezone");
        date_default_timezone_set($this->timezone);
        $this->timezoneOffset = date("P", time());
        if ($this->db->dbdriver == "mysqli") {
            $this->db->query("SET time_zone = '" . $this->timezoneOffset . "';");
        }
        $loaded = $this->lang->is_loaded;
        $this->lang->is_loaded = [];
        foreach ($loaded as $file) {
            $file = str_replace("_lang.php", "", $file);
            $this->lang->load($file);
        }
    }
    public function get_all_system_preferences_values()
    {
        $this->load->model("system_preference");
        return $this->system_preference->get_key_groups();
    }
    public function getInstanceConfig($parameter)
    {
        $this->config->load("instance", true);
        $parameterValue = $this->config->item($parameter, "instance");
        return $parameterValue ? $parameterValue : "";
    }
    public function call_materialized_view_triggers($trigger, $record_id, $data = [])
    {
        if ($this->db->dbdriver != "mysqli") {
            return false;
        }
        $call_trigger = !empty($data) ? false : true;
        foreach ($data as $field => $value) {
            if ($value["old"] != $value["new"]) {
                $call_trigger = true;
            }
        }
        if ($call_trigger) {
            $cmd = $this->getInstanceConfig("PHP_executable_path") . " index.php Materialized_view_triggers " . $trigger . " " . $record_id;
            if (substr(php_uname(), 0, 7) == "Windows") {
                pclose(popen("start /B " . $cmd, "r"));
            } else {
                exec($cmd . " > /dev/null &");
            }
        }
    }
}

