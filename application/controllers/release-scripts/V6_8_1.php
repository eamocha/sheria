<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require(APPPATH . "controllers/Top_controller.php");


class V6_8_1 extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->hooks->enabled = false;
    }

    public function index()
    {
        $this->update_licenses_files();
    }
    /*
     * retrieve license file content then add at the end the encoded value of (WORD)
     */

    public function update_licenses_files()
    {
        $licenses = array('core', 'outlook', 'customer-portal');
        $root = substr(COREPATH, 0, -12);
        $a4l_encoded_key = base64_encode('WORD');
        foreach ($licenses as $license) {
            $license_file_path = ('core' == $license ? COREPATH : $root . "modules/{$license}/app/") . 'config/license.php';
            if (file_exists($license_file_path)) {
                $encoded_license = file_get_contents($license_file_path);
                if(!empty($encoded_license)){
                    $encoded_license.= $a4l_encoded_key;
                    file_put_contents($license_file_path, $encoded_license);
                }
            }
        }
    }

}