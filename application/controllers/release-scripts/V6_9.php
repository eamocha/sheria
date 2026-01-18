<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V6_9 extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->index();
    }

    public function index()
    {
        $this->migrate_father_column_with_full_name_in_contacts_grid();
        $this->fix_notification_scheme_data();
        $this->migrate_intellectual_properties_grid_saved_column();
    }

    public function migrate_father_column_with_full_name_in_contacts_grid()
    {
        $this->log('migrate father name with full name in contacts grid started', 'info');
        $this->load->model('grid_saved_column');
        $contact_grid_saved_columns = $this->grid_saved_column->load_all(array('select' => array('id, grid_details'), 'where' => array('model', 'contact')));
        $this->log('contact grid saved columns are loaded', 'info');
        foreach ($contact_grid_saved_columns as $saved_column) {
            $grid_details = unserialize($saved_column['grid_details']);
            $columns = $grid_details['selected_columns'];
            if (!empty($columns) && in_array('father', $columns)) {
                unset($grid_details['selected_columns'][array_search('father', $columns)]);
                $grid_details['selected_columns'] = array_values($grid_details['selected_columns']);
                $details = serialize($grid_details);
                $query = "UPDATE {$this->grid_saved_column->_table} SET grid_details = '{$details}' WHERE id = {$saved_column['id']}";
                $this->db->query($query);
            }
        }
        $this->log('migrate father name with full name in contacts grid finished', 'info');
    }
    public function fix_notification_scheme_data()
    {
        $this->log('fix notification schema data started', 'info');
        $this->load->model('email_notification_scheme');
        $editable_notify_to_values =  $this->email_notification_scheme->get('userToEditableValues');
        $uneditable_notify_cc_values =  $this->email_notification_scheme->get('userCcUnEditableValues');
        $notifications =  $this->email_notification_scheme->load_all();
        $this->log('notifications schema data are loaded', 'info');
        $notify_to = $notify_to_array = $notify_cc=$notify_cc_array=array();
        foreach ($notifications as $notification) {
            $notify_to = explode(';', $notification['notify_to']);
            if (in_array($notification['trigger_action'], $editable_notify_to_values)) {
                $notify_to_array= implode(';', array_unique($notify_to));
                $query = "UPDATE {$this->email_notification_scheme->_table} SET notify_to = '{$notify_to_array}' WHERE id = {$notification['id']}";
                $this->db->query($query);
            }
            if (!in_array($notification['trigger_action'], $uneditable_notify_cc_values)) {
                $notify_cc =array_unique(explode(';', $notification['notify_cc']));
                foreach ($notify_cc as $key=> $cc) {
                    if (in_array($cc, $notify_to)) {
                        unset($notify_cc[$key]);
                    }
                }
                $notify_cc_array= implode(';', $notify_cc);
                $query = "UPDATE {$this->email_notification_scheme->_table} SET notify_cc = '{$notify_cc_array}' WHERE id = {$notification['id']}";
                $this->db->query($query);
            }
        }
        $this->log('fix notification schema data finished', 'info');
    }
    public function migrate_intellectual_properties_grid_saved_column()
    {
        $this->log('migrate ip grid saved column started', 'info');
        $this->load->model('grid_saved_column');
        $grid_saved_columns = $this->grid_saved_column->load_all();
        $this->log('saved columns are loaded', 'info');
        foreach ($grid_saved_columns as $grid_saved_column) {
            if (!empty($grid_saved_column['grid_details'])) {
                $grid_details = unserialize($grid_saved_column['grid_details']);
                if (!empty($grid_details['selected_columns'])) {
                    $column_key = array_search('renewalUserToRemind', $grid_details['selected_columns']);
                    if ($column_key) {
                        $grid_details['selected_columns'][$column_key] = 'renewalUsersToRemind';
                        $grid_saved_column['grid_details'] = serialize($grid_details);
                        $query = "UPDATE {$this->grid_saved_column->_table} SET grid_details = '{$grid_saved_column['grid_details']}' WHERE id = {$grid_saved_column['id']};";
                        $this->db->query($query);
                    }
                }
            }
        }
        $this->log('migrate ip grid saved column finished', 'info');
    }
    public function log($message, $type = 'error')
    {
        $pr = fopen(FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'v6_9_release_scripts_log.log', 'a');
        fwrite($pr, "[" . date('Y-m-d H:i:s') . "] - {$message} \n");
        fclose($pr);
        if ($type=='error') {
            echo $type.': '.$message .' check log file ' . FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "v6_9_release_scripts_log.log </br>";
            exit;
        }
    }
}
