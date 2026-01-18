<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_2 extends CI_Controller
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
        $this->rename_customer_to_client();
        $this->remove_wrong_country();
        $this->add_excluded_language();
        $this->fix_notification_scheme_data();
        $this->update_contract_license();
        $this->grant_users_to_access_new_permissions();
        $this->fix_contract_renewals();
        //$this->keep_only_nontranslated_variables_to_russian();
        $this->write_log($this->log_path, 'done from migration script');
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
                    if(in_array('/time_tracking/edit/', $group_permission) && !in_array('/time_tracking/bulk_edit_time/', $group_permission) && !in_array('/cases/bulk_edit_time/', $group_permission)){
                        $new_permissions = $group_permissions;
                        array_push($new_permissions['core'], '/time_tracking/bulk_edit_time/');
                        array_push($new_permissions['core'], '/cases/bulk_edit_time/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/dashboard/management/', $group_permission) && !in_array('/dashboard/export_time_tracking_dashboard_pdf/', $group_permission) && !in_array('/dashboard/time_tracking_dashboard/', $group_permission)){
                        $new_permissions = $group_permissions;
                        array_push($new_permissions['core'], '/dashboard/export_time_tracking_dashboard_pdf/', '/dashboard/time_tracking_dashboard/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/companies/delete_company/', $group_permission) && !in_array('/companies/delete_signature_authority/', $group_permission)){
                        $new_permissions = $group_permissions;
                        array_push($new_permissions['core'], '/companies/delete_signature_authority/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if($module === 'contract'){
                    if($group_permission[0] == '/' || in_array('/contracts/', $group_permission) || in_array('/contracts/add/', $group_permission)){
                        $new_permissions = $group_permissions;
                        array_push($new_permissions['contract'], '/docusign_integration/request_to_sign/', '/docusign_integration/sign_contract/', '/contracts/activate_deactivate/', '/contracts/add_signature_signee_per_contract/', '/contracts/awaiting_signatures/', '/contracts/delete_signee_per_contract/', '/contracts/edit_signature_signee_per_contract/', '/contracts/load_signed_documents/', '/contracts/move_file_to_signed_tab/', '/contracts/related_contract_add/', '/contracts/related_contract_delete/', '/contracts/related_contract_edit/', '/contracts/related_contracts/', '/contracts/related_reminders/', '/contracts/related_tasks/', '/contracts/renew/', '/contracts/save_as_pdf/', '/contracts/sign_contract_doc/', '/contracts/signature_center/', '/signature_center/', '/signature_center/add/', '/signature_center/delete/', '/signature_center/delete_details/', '/signature_center/edit/', '/signature_center/index/', '/export/awaiting_signatures/', '/export/export_to_excel_approval_history/', '/export/export_to_excel_signature_history/', '/export/export_to_word_approval_history/', '/export/export_to_word_contract_details/', '/export/export_to_word_signature_history/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }
    
    private function update_contract_license()
    {
        $this->write_log($this->log_path, 'load installation type');
        $this->load->model('instance_data');
        $installation_type = $this->instance_data->get_value_by_key('installationType');
        $instanceID = $this->instance_data->get_value_by_key('instanceID');
        if ($installation_type['keyValue'] === 'on-cloud' && !in_array($instanceID['keyValue'], [6305, 6343, 6204, 6340, 6411]) ) {
            $this->write_log($this->log_path, 'get contract license content');
            $contract_license = FCPATH . 'modules' . DIRECTORY_SEPARATOR . 'contract/app/config/license.php';
            if(file_exists($contract_license)){
                $fibonacciStr = file_get_contents($contract_license);
                $this->write_log($this->log_path, 'decode license string');
                $b64_license = $this->license_decode($fibonacciStr);
                if ($b64_license) {
                    $this->write_log($this->log_path, 'unserialize decoded string');
                    @eval(@unserialize($b64_license));
                    if (isset($config)) {
                        if($config['App4Legal::Contract']['expiry'] >= date('Y-m-d')){
                            $this->write_log($this->log_path, 'license is not expired so proceed with adding the nbOfCollaborators');
                            $config['App4Legal::Contract']['nbOfCollaborators'] = '10';
                            $this->write_log($this->log_path, 'encode license');
                            $encoded = $this->license_encode($config['App4Legal::Contract'], 'App4Legal::Contract');
                            $this->write_log($this->log_path, 'write license file');
                            @file_put_contents($contract_license, $encoded);
                            $this->write_log($this->log_path, 'update license is done');
                        }else{
                            $this->write_log($this->log_path, 'no need to change an expired license');
                        }
                    }else{
                        $this->write_log($this->log_path, 'contract license is corrupted');
                    }
                }
            }else{
                $this->write_log($this->log_path, 'missing contract license file');
            }  
        }else{
            $this->write_log($this->log_path, 'no need to change contract license for self-hosted instances or new customers on cloud');
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
    
    public function add_excluded_language(){
        $this->write_log($this->log_path, 'exclude arabic interface if origin country of cloud instance is non-Arab');
        $this->load->model('instance_data');
        $installation_type = $this->instance_data->get_value_by_key('installationType');
        $instanceID = $this->instance_data->get_value_by_key('instanceID');
        if ($installation_type['keyValue'] === 'on-cloud' && $instanceID['keyValue'] > 0) {
           $this->write_log($this->log_path, 'send API call to CC to retrieve the origin country');
           //set POST variables
            $url = 'https://www.app4legal.com/app4legal-cc/public/api/loadInstanceCountry';
            $fields = ['instanceID' => $instanceID['keyValue'], 'token' => file_get_contents('https://www.app4legal.com/site/instancesToken')];
            //url-ify the data for the POST
            $fields_string = '';
            foreach ($fields as $key => $value) {
                $fields_string .= $key . '=' . $value . '&';
            }
            rtrim($fields_string, '&');

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

            //execute post
            $response = curl_exec($ch);

            //close connection
            curl_close($ch);
           $api_data = json_decode($response, true);
           $origin_country = $api_data['success']['data'] ?? '';
           if($origin_country){
                $this->write_log($this->log_path, "API response is {$origin_country}");
                $this->load->model('country', 'countryfactory');
                $this->country = $this->countryfactory->get_instance();
                $this->write_log($this->log_path, "fetch id in countries table");
                if($this->country->fetch($origin_country)){
                    $this->write_log($this->log_path, 'list Arab Countries in array');
                    $arab_countries = [247,224,213,207,197,194,188,183,172,151,137,136,127,123,119,113,107,65,62,58,23,2];
                    $this->write_log($this->log_path, 'check if origin country is in listed array');
                    if(!in_array($origin_country, $arab_countries)){
                        $this->write_log($this->log_path, 'set arabic value in excluded interfaces');
                        if ($this->db->query("update instance_data set keyValue = 'arabic' WHERE keyName = 'excluded_interfaces'")) {
                            $this->write_log($this->log_path, 'Done - arabic value has been added successfully');
                        } else {
                            $this->write_log($this->log_path, 'Error - Failed to add arabic value');
                        }
                    }else{
                        $this->write_log($this->log_path, 'no need to add arabic value');
                    }
                }else{
                    $this->write_log($this->log_path, 'origin country is not located in countries table');
                }
           }else{
               $this->write_log($this->log_path, 'origin country is null');
           }
        }
    }

    public function rename_customer_to_client(){
        $this->write_log($this->log_path, 'rename customer to client');
        $this->load->model('system_preference');
        $system = $this->system_preference->get_value_by_key('cpAppTitle');
        if(isset($system['keyValue']) && !empty($system['keyValue']) && $system['keyValue'] === 'App4Legal Customer Portal'){
            $this->system_preference->set_value_by_key('CustomerPortalConfig', 'cpAppTitle', 'App4Legal Client Portal');
        }
        $system = $this->system_preference->get_value_by_key('cpWelcomeMessage');
        if(isset($system['keyValue']) && !empty($system['keyValue']) && $system['keyValue'] === 'Welcome to App4Legal Customer Portal'){
            $this->system_preference->set_value_by_key('CustomerPortalConfig', 'cpWelcomeMessage', 'Welcome to App4Legal Client Portal');
        }

    }

    public function keep_only_nontranslated_variables_to_russian(){
        $this->load->model('translation_variable');
        $all_lang_vars = $this->translation_variable->load_all();
        $this->load->model('basetranslation');
        foreach($all_lang_vars as $key => $values){
            if($this->basetranslation->fetch(['key' => $values['key'], 'filePath' => $values['filePath']])){
                    $this->translation_variable->delete($values['id']);
            }
        }
    }
    
    public function remove_wrong_country(){
        $this->write_log($this->log_path, 'start removing country 187 from contacts table (country id)');
        if ($this->db->query("update contacts set country_id = NULL WHERE country_id = '187'")) {
            $this->write_log($this->log_path, 'Done - country 187 deleted successfully from contacts');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from contacts');
        }
        
        $this->write_log($this->log_path, 'start removing country 187 from contacts nationalities table');
        if ($this->db->query("delete from contact_nationalities WHERE nationality_id = '187'")) {
            $this->write_log($this->log_path, 'Done - country 187 deleted successfully from contact nationalities');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from contact nationalities');
        }
        
        $this->write_log($this->log_path, 'start removing country 187 from company table (nationality id)');
        if ($this->db->query("update companies set nationality_id = NULL WHERE nationality_id = '187'")) {
            $this->write_log($this->log_path, 'Done - country 187 deleted successfully from companies');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from companies');
        }
        
        $this->write_log($this->log_path, 'start removing country 187 from company addresses table (country)');
        if ($this->db->query("update company_addresses set country = NULL WHERE country = '187'")) {
            $this->write_log($this->log_path, 'Done - country 187 deleted successfully from company addresses');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from company addresses');
        }
        
        $this->write_log($this->log_path, 'start removing country 187 from IP table (country id)');
        if ($this->db->query("update ip_details set country_id = NULL WHERE country_id = '187'")) {
            $this->write_log($this->log_path, 'Done - country 187 deleted successfully from IP table');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from IP table');
        }
        
        $this->write_log($this->log_path, 'start removing country 187 from countries_languages table (country id)');
        if ($this->db->query("delete from countries_languages WHERE country_id = '187'")) {
            $this->write_log($this->log_path, 'Done - country 187 deleted successfully from countries_languages table');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from countries_languages table');
        }
        
        $this->write_log($this->log_path, 'start removing country 187 from countries table');
        if ($this->db->query("DELETE FROM countries WHERE id = '187'")) {
            $this->write_log($this->log_path, 'Done - country 187 deleted successfully');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete from countries table');
        }
    }
    public function fix_notification_scheme_data()
    {
        $this->write_log($this->log_path, 'Fix notification schema data started');
        $this->load->model('email_notification_scheme');
        $contract_rejected =  $this->email_notification_scheme->fetch(array('trigger_action' => 'contract_rejected'));
        if( $contract_rejected ){
            $notify_to_string = $this->email_notification_scheme->get_field('notify_to');
            $notify_to = $notify_to_array = array();
            $notify_to = explode(';', $notify_to_string);
            if (false !== $key = array_search('requester,', $notify_to)) {
                $notify_to[$key] = 'requester';
                $notify_to_array= implode(';', array_unique($notify_to));
                $query = "UPDATE {$this->email_notification_scheme->_table} SET notify_to = '{$notify_to_array}' WHERE trigger_action = 'contract_rejected'";
                if ($this->db->query($query)) {
                    $this->write_log($this->log_path, 'Done - Notification schema updated successfully');
                } else {
                    $this->write_log($this->log_path, 'Error - Failed to update the notification schema');
                }
            } else {
                $this->write_log($this->log_path, 'Notification schema has no \'request,\' value on the Trigger action');
            }
        }
    }

    public function fix_contract_renewals()
    {
        $this->write_log($this->log_path, 'Started fixing renewals on Contract module');
        $query = "SELECT * FROM contract WHERE renewal_id IS NOT NULL";
        $contracts = $this->db->query($query);

        foreach ($contracts->result_array() as $contract){
            $check_from_language_query = "SELECT name FROM contract_renewal_language WHERE renewal_id = " . $contract['renewal_id'] . " AND language_id = 1" ;
            $result =  $this->db->query($check_from_language_query);
            $result_array = $result->result_array();
            if ((strcasecmp($result_array[0]['name'], 'yearly') == 0) || (strcasecmp($result_array[0]['name'], 'year') == 0) || (strcasecmp($result_array[0]['name'], 'anniversary') == 0)) {
                $update_query = "UPDATE contract SET renewal_type = 'renewable_automatically' WHERE id = " .  $contract['id'];
                if ($this->db->query($update_query)) {
                    $this->write_log($this->log_path, 'Done - ' . $contract['name'] . ' renewal type updated to yearly');
                } else {
                    $this->write_log($this->log_path, 'Error - Failed to update contract renewal type');
                }
            } else {
                $update_query = "UPDATE contract SET renewal_type = 'other' WHERE id = " .  $contract['id'];
                if ($this->db->query($update_query)) {
                    $this->write_log($this->log_path, 'Done - ' . $contract['name'] . ' renewal type updated to others');
                } else {
                    $this->write_log($this->log_path, 'Error - Failed to update contract renewal type');
                }
            }
        } 
        $this->write_log($this->log_path, 'Deleting old renewal structure');

        if ($this->db->query("ALTER TABLE contract DROP COLUMN renewal_id")) {
            if ($this->db->dbdriver == 'sqlsrv') {
                $this->db->query("IF OBJECT_ID('dbo.contract_renewal_language', 'U') IS NOT NULL DROP TABLE dbo.contract_renewal_language");
                $this->db->query("IF OBJECT_ID('dbo.contract_renewal', 'U') IS NOT NULL DROP TABLE dbo.contract_renewal");
            } else {
                $this->db->query("DROP TABLE IF EXISTS `contract_renewal_language`");
                $this->db->query("DROP TABLE IF EXISTS `contract_renewal`");
            }
            $this->write_log($this->log_path, 'Done - Deleted successfully');
        } else {
            $this->write_log($this->log_path, 'Error - Failed to delete the structure');
        }

        $this->write_log($this->log_path, 'Finished fixing renewals on Contract module');
    }
}