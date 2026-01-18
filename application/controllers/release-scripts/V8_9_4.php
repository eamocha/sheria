<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_9_4 extends CI_Controller
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
        $this->delete_old_ms_office_calendar_access_token();
        $this->add_fields_to_invoice_templates();
        $this->grant_users_to_access_new_permissions();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function update_whats_new_flag()
    {
        // check if class's name contains 0 at the end, this means it is a major/minor release. Only major/minor releases will have new release notes
        if (substr(get_class($this), -1) == '0') {
            $this->write_log($this->log_path, 'Start updating whats new flag');
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $this->user->set_users_whats_new_flag();
            $this->write_log($this->log_path, 'End updating whats new flag');
        }
    }

    private function delete_old_ms_office_calendar_access_token() {
        $this->write_log($this->log_path, 'Start deleting old access token for ms office calendar');
        $query = $this->db->query("SELECT user_id, keyValue FROM user_preferences WHERE keyName = 'integration'");
        $integrations = $query->result_array();
        foreach($integrations as $integration) {
            if(strpos($integration['keyValue'], 'ms_cloud')) {
                $this->db->query("DELETE FROM user_preferences WHERE user_id = {$integration['user_id']} AND keyName = 'integration'");
            }
        }
        $this->write_log($this->log_path, 'End deleting old access token for ms office calendar');
    }

    public function add_fields_to_invoice_templates()
    {
        $this->write_log($this->log_path, 'Started adding Fields to invoice templates');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $value => $template) {
            $settings = unserialize($template['settings']);
            if (isset($settings['body'])) {
                if (!isset($settings['body']['show']['invoice-status-container'])) {
                    $settings['body']['show']['invoice-status-container'] = false;
                }
                if (!isset($settings['body']['show']['paid-amount-container'])) {
                    $settings['body']['show']['paid-amount-container'] = false;
                }
                if (!isset($settings['body']['show']['invoice-information-font-size'])) {
                    $settings['body']['css']['invoice-information-font-size'] = 10;
                }
                if (!isset($settings['body']['show']['invoice-tables-font-size'])) {
                    $settings['body']['css']['invoice-tables-font-size'] = 10;
                }
                if (!isset($settings['body']['show']['invoice-summation-font-size'])) {
                    $settings['body']['css']['invoice-summation-font-size'] = 10;
                }
                if (!isset($settings['body']['show']['invoice-notes-font-size'])) {
                    $settings['body']['css']['invoice-notes-font-size'] = 10;
                }
                if (!isset($settings['body']['css']['tables-borders'])) {
                    $settings['body']['css']['tables-borders'] = 'both';
                }
                if (!isset($settings['body']['css']['invoice-information-font-color'])) {
                    $settings['body']['css']['invoice-information-font-color'] = '#000000';
                }
                if (!isset($settings['body']['css']['invoice-tables-font-color'])) {
                    $settings['body']['css']['invoice-tables-font-color'] = '#000000';
                }
                if (!isset($settings['body']['css']['invoice-summation-font-color'])) {
                    $settings['body']['css']['invoice-summation-font-color'] = '#000000';
                }
                if (!isset($settings['body']['css']['invoice-notes-font-color'])) {
                    $settings['body']['css']['invoice-notes-font-color'] = '#000000';
                }
                if (!isset($settings['body']['css']['tables-headers-background-color'])) {
                    $settings['body']['css']['tables-headers-background-color'] = '#D3D3D3';
                }
                if (!isset($settings['properties']['page-size'])) {
                    $settings['properties']['page-size'] = 'letter';
                }
                if (!isset($settings['properties']['page-color'])) {
                    $settings['properties']['page-color'] = '#ffffff';
                }
                if (!isset($settings['properties']['page-font'])) {
                    $settings['properties']['page-font'] = 'Calibri';
                }
                if (!isset($settings['properties']['page-orientation'])) {
                    $settings['properties']['page-orientation'] = 'portrait';
                }
                if (!isset($settings['body']['show']['time-logs-rebuild-description'])) {
                    $settings['body']['show']['time-logs-rebuild-description'] = false;
                }
                $invoice_information_order = array('client_data' => 
                array('matter-id-container', 'matter-subject-container', 'matter-reference-nb',
                'bill-to-container', 'tax_number'),
                'invoice_data' => array('invoice-nb-container', 'invoice-ref-container', 'invoice-date-container',
                'due-date-container', 'terms-container', 'invoice-status-container', 'po-container', 'show-exchange-rate'));
                if (!isset($settings['body']['invoice_information_order'])) {
                    $settings['body']['invoice_information_order'] = $invoice_information_order;
                }
                $template['settings'] = serialize($settings);
            }
            $this->db->query("update organization_invoice_templates set settings = '{$template['settings']}' WHERE id = {$template['id']}");
        }
        $this->write_log($this->log_path, 'Fields added Succeddfully');
    }
    public function grant_users_to_access_new_permissions()
    {
        $this->write_log($this->log_path, 'add new actions to user permissions');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group) {
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach ($group_permissions as $module => $group_permission) {
                if ($module === 'money') {
                    $new_permissions = $group_permissions;
                    if (in_array('/vouchers/credit_notes/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/credit_notes_export_to_excel/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/set_invoice_as_draft/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/set_credit_note_as_draft/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/cancel_invoice/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/cancel_credit_note/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/convert_invoice_to_open/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/convert_credit_note_to_open/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }
}
