<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Licensor
{
    public $licenseDetails = [];
    public $license_msg = [];
    public $expiration_days = 0;
    public $extendExpiredInstance = 15;
    public $license_package;
    private $licenseFilePath;
    private $ci;
    private $isOutputJSON;
    private $currentModule;
    public function __construct($params = [])
    {
        $this->ci =& get_instance();
        $this->currentModule = in_array(MODULE, $this->ci->coreLicensePackage) ? "core" : MODULE;
        $this->isOutputJSON = !empty($params) && isset($params["isOutputJSON"]) ? $params["isOutputJSON"] : false;
        $this->ci->load->library("inflector");
        $this->license_package = $this->check_license_package();
        $this->licenseDetails = $this->check_license_validity($this->currentModule);
        $this->ci->load->model("instance_data");
        $installation_type = $this->ci->instance_data->get_value_by_key("installationType");
        $this->cloud_installation_type = $installation_type["keyValue"] == "on-cloud";
    }
    private function check_license_package()
    {
        $license_file = DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "license.php";
        $core_license_file_path = INSTANCE_PATH . DIRECTORY_SEPARATOR . "application" . $license_file;
        $contract_license_file_path = INSTANCE_PATH . "modules" . DIRECTORY_SEPARATOR . "contract" . DIRECTORY_SEPARATOR . "app" . $license_file;
        $error_msg = "";
        $license_package = "";
        if (is_file($core_license_file_path)) {
            $fibonacci_str = empty($encoded_license) ? file_get_contents($core_license_file_path) : $encoded_license;
            $b64_license = $this->license_decode($fibonacci_str);
            if ($b64_license) {
                eval(@unserialize($b64_license));
                $key_prefix = APPNAME;
                if (isset($config) && isset($config[$key_prefix]) && $config[$key_prefix]["maxActiveUsers"]) {
                    $core_license = true;
                    $license_package = "core";
                    $this->ci->load->model("user", "userfactory");
                    $this->ci->user = $this->ci->userfactory->get_instance();
                    if ($config[$key_prefix]["maxActiveUsers"] < $this->ci->user->count_active_users()["totalCount"]) {
                        $error_msg = sprintf($this->ci->lang->line("license_msg_invalid_max_users_exceeded_for"), $this->ci->lang->line("core"));
                    }
                } else {
                    $core_license = false;
                }
            } else {
                $core_license = false;
            }
        } else {
            $core_license = false;
        }
        if (is_file($contract_license_file_path)) {
            $fibonacci_str = file_get_contents($contract_license_file_path);
            $b64_license = $this->license_decode($fibonacci_str);
            if ($b64_license) {
                eval(@unserialize($b64_license));
                $key_prefix = APPNAME . "::" . $this->ci->inflector->humanize("contract");
                if (isset($config) && isset($config[$key_prefix])) {
                    if (isset($config[$key_prefix]["maxActiveUsers"])) {
                        $contract_license = true;
                        $license_package = "contract";
                        $this->ci->load->model("user", "userfactory");
                        $this->ci->user = $this->ci->userfactory->get_instance();
                        if ($config[$key_prefix]["maxActiveUsers"] < $this->ci->user->count_active_users("contract")["totalCount"]) {
                            $error_msg = sprintf($this->ci->lang->line("license_msg_invalid_max_users_exceeded_for"), $this->ci->lang->line("contract"));
                        }
                    } else {
                        $contract_license = false;
                    }
                } else {
                    $contract_license = false;
                }
            } else {
                $contract_license = false;
            }
        } else {
            $contract_license = false;
        }
        if ($core_license && $contract_license) {
            $license_package = "core_contract";
        }
        if (!$core_license && !$contract_license) {
            $this->ci->session->set_userdata("license_message_error", $this->ci->lang->line("no_available_license"));
            redirect("base/license_error/");
        }
        if ($error_msg && ($this->currentModule == "core" || $this->currentModule == "contract")) {
            $this->ci->session->set_userdata("license_message_error", $error_msg);
            redirect("base/license_error/");
        }
        return $license_package;
    }
    private function license_decode($str)
    {
        $a4l_encoded = base64_encode("INFOSYSTA-LICENSE-KEYWORD");
        $key_length = strlen($a4l_encoded);
        if (empty($str) || !empty($str) && substr($str, -1 * $key_length) !== $a4l_encoded) {
            return false;
        }
        $str = substr($str, 0, -1 * $key_length);
        $length = strlen($str);
        $newStr = "";
        $fibonacciPositions = [];
        $i = 0;
        $Un2 = -1;
        for ($Un1 = 1; $i < $length; $i++) {
            $Un = $Un1 + $Un2;
            $Un2 = $Un1;
            $Un1 = $Un;
            $fibonacciPositions[] = $Un + $i;
            if (!in_array($i, $fibonacciPositions)) {
                $newStr .= $str[$i];
            }
        }
        return base64_decode($newStr);
    }
    public function check_license_validity($module = "core", $encoded_license = "")
    {
        $config = false;
        $error_msg = "";
        if ($module === "customer-portal") {
            $contract_error_msg = "";
            $cp_error_msg = "";
            $license_file = DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "license.php";
            $contract_license_file_path = INSTANCE_PATH . "modules" . DIRECTORY_SEPARATOR . "contract" . $license_file;
            $cp_license_file_path = INSTANCE_PATH . "modules" . DIRECTORY_SEPARATOR . "customer-portal" . $license_file;
            if (is_file($contract_license_file_path)) {
                $fibonacci_str = file_get_contents($contract_license_file_path);
                $b64_license = $this->license_decode($fibonacci_str);
                if ($b64_license) {
                    eval(@unserialize($b64_license));
                    $key_prefix = APPNAME . "::" . $this->ci->inflector->humanize("contract");
                    if (isset($config) && isset($config[$key_prefix])) {
                        $this->ci->load->model("customer_portal_users", "customer_portal_usersfactory");
                        $this->ci->customer_portal_users = $this->ci->customer_portal_usersfactory->get_instance();
                        if (isset($config[$key_prefix]["nbOfCollaborators"])) {
                            if ($config[$key_prefix]["nbOfCollaborators"] < $this->ci->customer_portal_users->count_active_collaborators()["totalCount"]) {
                                $contract_error_msg = $this->ci->lang->line("license_msg_invalid_max_collaborators_exceeded");
                            }
                        } else {
                            $contract_error_msg = $this->ci->lang->line("invalid_license");
                        }
                    } else {
                        $contract_error_msg = sprintf($this->ci->lang->line("invalid_license_for"), strtoupper($module));
                    }
                } else {
                    $contract_error_msg = sprintf($this->ci->lang->line("invalid_license_for"), strtoupper($module));
                }
            } else {
                $contract_error_msg = sprintf($this->ci->lang->line("invalid_license_for"), strtoupper($module));
            }
            if (is_file($cp_license_file_path)) {
                $fibonacci_str = file_get_contents($cp_license_file_path);
                $b64_license = $this->license_decode($fibonacci_str);
                if ($b64_license) {
                    eval(@unserialize($b64_license));
                    $key_prefix = APPNAME . "::" . $this->ci->inflector->humanize($module);
                    if (!isset($config) || !isset($config[$key_prefix])) {
                        $cp_error_msg = $this->ci->lang->line("invalid_license");
                    }
                } else {
                    $cp_error_msg = $this->ci->lang->line("invalid_license");
                }
            } else {
                $cp_error_msg = $this->ci->lang->line("license_file_missing");
            }
            if ($contract_error_msg != "" && $cp_error_msg !== "") {
                $error_msg = $contract_error_msg ?: $cp_error_msg;
            } else {
                switch ($this->license_package) {
                    case "core_contract":
                        $config["license_valid"]["core"] = true;
                        $config["license_valid"]["contract"] = true;
                        break;
                    case "core":
                        $config["license_valid"]["core"] = true;
                        $config["license_valid"]["contract"] = false;
                        break;
                    case "contract":
                        $config["license_valid"]["core"] = false;
                        $config["license_valid"]["contract"] = true;
                        break;
                    default:
                        if ($contract_error_msg === "") {
                            $config["license_valid"]["contract"] = true;
                        } else {
                            $config["license_valid"]["contract"] = false;
                        }
                        if ($cp_error_msg === "") {
                            $config["license_valid"]["customer-portal"] = true;
                        } else {
                            $config["license_valid"]["customer-portal"] = false;
                        }
                }
            }
        } else {
            $core_license_file_path = INSTANCE_PATH . "application" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "license.php";
            if (is_file($core_license_file_path)) {
                $fibonacci_str = empty($encoded_license) ? file_get_contents($core_license_file_path) : $encoded_license;
                $b64_license = $this->license_decode($fibonacci_str);
                if ($b64_license) {
                    eval(@unserialize($b64_license));
                    if (isset($config)) {
                        return $config;
                    }
                }
            } else {
                $error_msg = $this->ci->lang->line("license_file_missing");
            }
        }
        if (!empty($error_msg)) {
            if ($module === "core" && $this->currentModule !== "core") {
                $error_msg = sprintf($this->ci->lang->line("invalid_license_for"), strtoupper("core"));
            }
            if ($this->isOutputJSON) {
                $output = $this->ci->output->set_content_type("application/json")->set_header("Access-Control-Allow-Origin: *")->set_output(json_encode(["status" => "not-valid", "error" => $error_msg]));
                echo $output->get_output();
                exit;
            }
            if ($this->currentModule == "contract") {
                if ($this->ci->input->is_ajax_request()) {
                    $response["display_message"] = $error_msg;
                    $response["result"] = false;
                    $output = $this->ci->output->set_content_type("application/json")->set_output(json_encode($response));
                    echo $output->get_output();
                    exit;
                }
                $this->ci->set_flashmessage("warning", $error_msg);
                $base_url = substr(base_url(), 0, -1 * strlen(MODULE) - 9);
                redirect($base_url . "dashboard");
            } else {
                $this->ci->session->set_userdata("license_message_error", $error_msg);
                redirect("base/license_error/");
            }
        }
        return $config;
    }
    public function get($key = false)
    {
        $keyPrefix = "core" != $this->currentModule ? APPNAME . "::" . $this->ci->inflector->humanize($this->currentModule) : APPNAME;
        if ($key === false) {
            return $this->licenseDetails[$keyPrefix];
        }
        return isset($this->licenseDetails[$keyPrefix][$key]) ? $this->licenseDetails[$keyPrefix][$key] : NULL;
    }
    public function test_license($tempString, $module = false)
    {
        if (!$module) {
            $module = $this->currentModule;
        }
        return $this->check_license_file_validity($module, $tempString);
    }
    public function get_all_licenses()
    {
        $licenses = ["core" => $this->read_license_file("core"), "outlook" => $this->read_license_file("outlook"), "A4G" => $this->read_license_file("A4G"), "customer-portal" => $this->read_license_file("customer-portal"), "contract" => $this->read_license_file("contract"), "advisor-portal" => $this->read_license_file("advisor-portal")];
        foreach ($this->ci->activeInstalledModules as $module => $Module) {
            $licenses[$module] = $this->read_license_file($module);
        }
        return $licenses;
    }
    public function read_license_file($module)
    {
        $licenseFilePath = ("core" == $module ? INSTANCE_PATH . "application/" : INSTANCE_PATH . "modules/" . $module . "/app/") . "config/license.php";
        $fibonacciStr = file_get_contents($licenseFilePath);
        $b64_license = $this->license_decode($fibonacciStr);
        if ($b64_license) {
            eval(@unserialize($b64_license));
            $key_prefix = "core" == $module ? APPNAME : APPNAME . "::" . $this->ci->inflector->humanize($module);
            if (isset($config) && isset($config[$key_prefix])) {
                return $config;
            }
            return false;
        }
        return false;
    }
    public function write_license($encoded = "", $module)
    {
        $licenseFilePath = ("core" == $module ? INSTANCE_PATH . "application/" : INSTANCE_PATH . "modules/" . $module . "/app/") . "config/license.php";
        @file_put_contents($licenseFilePath, $encoded);
    }
    public function check_license_date($module = "")
    { 
        $module = in_array($module, $this->ci->coreLicensePackage) ? "core" : $module;
        $this->license_msg[$module] = "";
        $this->module = $module;
        $this->expiry_day[$module] = "";
        $this->installation_type = "";
        if (strcmp($module, "")) {
            $all_modules = $this->get_installed_modules();
            $all_modules["core"] = "Core";
            if (!array_key_exists($module, $all_modules)) {
                $this->license_msg[$module] = "invalid_license_for";
                return false;
            }
            $keyPrefix = "core" != $module ? APPNAME . "::" . $this->ci->inflector->humanize($module) : APPNAME;
            $licenses[$module] = $this->read_license_file($module);
            $license_expiry_date = $licenses[$module][$keyPrefix]["expiry"];
        } else {
            $license_expiry_date = $this->ci->licensor->get("expiry");
        }
        $cur_date = date_create(date("Y-m-d"));
        $expiry_date = date_create($license_expiry_date); 
        $diff = date_diff($cur_date, $expiry_date);
        $expiration_days = intval($diff->format("%R%a"));
        $this->expiration_days = $expiration_days;
        $this->installation_type = $this->cloud_installation_type && strcmp($this->ci->instance_client_type, "") ? "cloud_" . $this->ci->instance_client_type : "server";
        $return = true;
        if ($license_expiry_date) {
            if ($expiration_days >= 0) { //changed >
                if ($expiration_days >= 1) {//changed >
                    if ($expiration_days >= 10 && 0 < $expiration_days) { //changed >
                    } else {
                        $this->license_msg[$module] = $this->installation_type . "_expires_soon_license";
                        $this->expiry_day[$module] = $expiration_days;
                    }
                } else {
                    $this->license_msg[$module] = $this->installation_type . "_expires_tomorrow_license";
                    $this->expiry_day[$module] = date("d M, Y", strtotime($license_expiry_date));
                }
            } else { 
                $this->license_msg[$module] = $this->installation_type . "_expired_license";
                $this->expiry_day[$module] = $license_expiry_date ? date("d M, Y", strtotime($license_expiry_date)) : 0;
                $return = false;
                if ($license_expiry_date && $this->cloud_installation_type && $module == "core") {
                    $cur_date = date_create(date("Y-m-d"));
                    $expiry_date = date_create($license_expiry_date);
                    $extended_expiry_date = date_create(date("Y-m-d", strtotime($license_expiry_date . " + " . $this->extendExpiredInstance . " days")));
                    $diff = date_diff($cur_date, $extended_expiry_date);
                    $extended_expiration_days = intval($diff->format("%R%a"));
                    if ($extended_expiration_days <= 0) {
                        if ($extended_expiration_days == 1) {
                            if (0 >= $extended_expiration_days) {
                                $this->license_msg[$module] = $this->installation_type . "_expires_soon_license";
                                $this->expiry_day[$module] = $extended_expiration_days;
                                $return = true;
                            }
                        } else {
                            $this->license_msg[$module] = $this->installation_type . "_expires_tomorrow_license";
                            $return = true;
                        }
                    }
                }
            }
        } else {
            $this->license_msg[$module] = "invalid_license_for";
            $return = false;
        }
        return $return;
    }
    public function get_installed_modules()
    {
        $modulesPath = INSTANCE_PATH . "modules/";
        $_installedModules = $this->ci->session->userdata("_license_installed_modules");
        if (empty($_installedModules)) {
            $_installedModules = [];
            $ignore = [".", ".."];
            $modules = opendir($modulesPath);
            while ($module = readdir($modules)) {
                if (!in_array($module, $ignore) && is_dir($modulesPath . $module)) {
                    $_installedModules[] = $module;
                }
            }
            $this->ci->session->set_userdata("_license_installed_modules", serialize($_installedModules));
        } else {
            $_installedModules = (array) unserialize($_installedModules);
        }
        $installed = [];
        foreach ($_installedModules as $installedModule) {
            $installed[$installedModule] = $this->ci->inflector->humanize($installedModule);
        }
        foreach ($this->ci->coreLicensePackage as $coreMod) {
            unset($installed[$coreMod]);
        }
        return $installed;
    }
    public function get_license_message($module = "core")
    {
        if (isset($this->expiry_day[$module])) {
            if (strcmp($this->expiry_day[$module], "")) {
                return sprintf($this->ci->lang->line($this->license_msg[$module]), $this->ci->lang->line($module), $this->expiry_day[$module]);
            }
        } else {
            return "";
        }
    }
    private function check_license_file_validity($module = "core", $encoded_license = "")
    {
        $error_msg = "";
        $license_file_path = ($module == "core" ? str_replace("/", DIRECTORY_SEPARATOR, COREPATH) : str_replace("/", DIRECTORY_SEPARATOR, APPPATH)) . "config" . DIRECTORY_SEPARATOR . "license.php";
        if (is_file($license_file_path)) {
            $fibonacci_str = empty($encoded_license) ? file_get_contents($license_file_path) : $encoded_license;
            $b64_license = $this->license_decode($fibonacci_str);
            if ($b64_license) {
                eval(@unserialize($b64_license));
                $key_prefix = "core" == $module ? APPNAME : APPNAME . "::" . $this->ci->inflector->humanize($module);
                if (isset($config) && isset($config[$key_prefix])) {
                    $this->ci->load->model("user", "userfactory");
                    $this->ci->user = $this->ci->userfactory->get_instance();
                    if ($module == "contract" && $config[$key_prefix]["maxActiveUsers"] < $this->ci->user->count_active_users("contract")["totalCount"]) {
                        $error_msg = sprintf($this->ci->lang->line("license_msg_invalid_max_users_exceeded_for"), $this->ci->lang->line("contract"));
                    } else {
                        if ($module !== "customer-portal" && $config[$key_prefix]["maxActiveUsers"] < $this->ci->user->count_active_users()["totalCount"]) {
                            $error_msg = sprintf($this->ci->lang->line("license_msg_invalid_max_users_exceeded_for"), $this->ci->lang->line("core"));
                        }
                    }
                } else {
                    $error_msg = $this->ci->lang->line("invalid_license");
                }
            } else {
                $error_msg = $this->ci->lang->line("invalid_license");
            }
        } else {
            $error_msg = $this->ci->lang->line("license_file_missing");
        }
        if (!empty($error_msg)) {
            if ($module === "core" && $this->currentModule !== "core") {
                $error_msg = sprintf($this->ci->lang->line("invalid_license_for"), strtoupper("core"));
            }
            $this->ci->set_flashmessage("warning", $error_msg);
        }
        return $config;
    }
}

?>