<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class Cloud_script extends CI_Controller
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
        $this->fix_customer_licenses();
        $this->write_log($this->log_path, 'done from migration script');
    }
    
    public function fix_customer_licenses(){
        $this->write_log($this->log_path, 'fix customer licenses');
        $this->load->model('instance_data');
        $instance_data_array = $this->instance_data->get_values();
        $cloud_installation_type = $instance_data_array['installationType'] == "on-cloud";
        if ($cloud_installation_type) { //cloud installation
            $this->write_log($this->log_path, 'get core license content');
            $fibonacciStr = file_get_contents(COREPATH. 'config/license.php');
            $this->write_log($this->log_path, 'decode license string');
            $b64_license = $this->license_decode($fibonacciStr);
            if ($b64_license) {
                $this->write_log($this->log_path, 'unserialize decoded string');
                @eval(@unserialize($b64_license));
                if (isset($config)) {
                    $this->write_log($this->log_path, 'load installation type');
                    $instanceId = $instance_data_array['instanceID'];
                    if ($instance_data_array['clientType'] === 'customer' && !in_array($instanceId, ['5890', '3687'])) { // exclude enterprise customers
                        $this->write_log($this->log_path, 'update cloud plan');
                        $config['App4Legal']['plan'] = 'cloud-business';
                        $config['App4Legal']['plan_excluded_features'] = 'In-Document-Search,Advanced-Workflows-&-Approvals,LDAP-User-Management-Integration,Azure-User-Management-Integration,Third-Party-Integration-(APIs),Service-Level-Agreement';
                        $this->write_log($this->log_path, 'encode license');
                        $encoded = $this->license_encode($config['App4Legal'], 'App4Legal');
                        $this->write_log($this->log_path, 'write license file');
                        @file_put_contents(COREPATH . 'config/license.php', $encoded);
                        $this->write_log($this->log_path, 'update license is done');
                    }
                }
            }
        }
    }
    
    private function license_encode($licenseVars, $product)
    {
        $license = "";
        if (!empty($licenseVars)) {
            foreach ($licenseVars as $licenseVar => $licenseVal) {
                $license .= " \$config['{$product}']['{$licenseVar}'] = '{$licenseVal}';";
            }
            $slicense = serialize($license);
            $str = base64_encode($slicense);
            if (empty($str)) {
                return '';
            }
            $lastPos = strlen($str) - 1;
            $newStr = '';
            if ($lastPos == 0) {
                $newStr = $str . $str;
            } elseif ($lastPos == 1) {
                $newStr = $str[1] . $str[0] . $str[0] . $str[1] . $str[1];
            } elseif ($lastPos == 2) {
                $newStr = $str[1] . $str[0] . $str[2] . $str[0] . $str[1] . $str[0] . $str[2];
            } elseif ($lastPos == 3) {
                $newStr = $str[1] . $str[0] . $str[2] . $str[0] . $str[1] . $str[0] . $str[2] . $str[2] . $str[3];
            } elseif ($lastPos == 4) {
                $newStr = $str[2] . $str[0] . $str[1] . $str[3] . $str[1] . $str[0] . $str[2] . $str[2] . $str[3] . $str[4];
            }
            if ($lastPos < 5) {
                die($newStr);
            }
            $newStr = $str[2] . $str[0] . $str[1] . $str[3] . $str[1] . $str[0] . $str[2] . $str[2] . $str[3] . $str[4];
            $lastStopPos = 5;
            $n = 5;
            $Un2 = 2;
            $Un1 = 3;
            $Un = $Un1 + $Un2;
            do {
                $Un2 = $Un1;
                $Un1 = $Un;
                while ($Un > $lastStopPos) {
                    $newStr .= $str[$lastStopPos];
                    $lastStopPos++;
                }
                $newStr .= $str[rand(1, $lastPos)];
            } while (($Un = $Un + $Un2) <= $lastPos);
            while ($lastStopPos <= $lastPos) {
                $newStr .= $str[$lastStopPos];
                $lastStopPos++;
            }
            $a4l_encoded = base64_encode('INFOSYSTA-LICENSE-KEYWORD');
            return $newStr.$a4l_encoded;
        }
    }

    private function license_decode($str)
    {
        $a4l_encoded = base64_encode('INFOSYSTA-LICENSE-KEYWORD');
        $key_length = strlen($a4l_encoded);
        if (empty($str) || (!empty($str) && substr($str, -$key_length) !== $a4l_encoded)) {
            return false;
        } else {
            $str = substr($str, 0, -$key_length);
        }
        $length = strlen($str);
        $newStr = '';
        $fibonacciPositions = array();
        $i = 0;
        $Un2 = -1;
        $Un1 = 1;
        while ($i < $length) {
            $Un = $Un1 + $Un2;
            $Un2 = $Un1;
            $Un1 = $Un;
            $fibonacciPositions[] = $Un + $i;
            if (!in_array($i, $fibonacciPositions)) {
                $newStr .= $str[$i];
            }
            $i++;
        }
        return base64_decode($newStr);
    }
}