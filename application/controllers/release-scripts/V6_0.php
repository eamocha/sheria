<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V6_0 extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ci = &get_instance();
        $this->index();
    }

    public function index()
    {
        $this->updatePermissionsScheme();
        $this->injectCloudInstanceIDs();
        $this->update_legal_cases();
        $this->update_legal_case_changes();
        $this->update_workflow_status_transition_history();
        $this->update_customer_portal_sla_cases();
        $this->update_webdav_tree_structure();
    }
    
    /*
     * Split ADD actions in Case form 
     * This update is required to give clients the access to add litigation and legal matter (new urls) if they already have access to add case (old url)
     */
    
    public function updatePermissionsScheme(){
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module === 'core'){
                    if(in_array('/cases/add/', $group_permission)){
                        $key = array_search('/cases/add/', $group_permission);
                        $new_permissions = $group_permissions;
                        unset($new_permissions['core'][$key]);
                        array_push($new_permissions['core'], '/cases/add_legal_matter/', '/cases/add_litigation/', '/intellectual_properties/add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
    }

    /*
     * Inject Instance ID in instance data table for cloud instances
     * to use this id as a reference in API calls with CC
     */
    public function injectCloudInstanceIDs()
    {
        $this->load->model('instance_data');
        $installation_type = $this->instance_data->get_value_by_key('installationType');
        if ($installation_type['keyValue'] === 'on-cloud') {
            $url_string = $this->config->site_url($this->uri->uri_string());
            $url_arr = explode("/", $url_string);
            $site_key = array_search("site", $url_arr);
            $instance_id = $url_arr[$site_key + 1];
            $this->instance_data->set_value_by_key('instanceID', $instance_id);
        }
    }

    public function update_legal_cases()
    {
        $this->update_record(array('modifiedByChannel' => 'A4L'), 'channel = \'A4L\'', 'legal_cases');
        $this->update_record(array('modifiedByChannel' => 'MOB'), 'channel = \'MOB\' and createdOn = modifiedOn', 'legal_cases');
        $this->update_record(array('modifiedByChannel' => 'A4L'), 'channel = \'MOB\' and createdOn != modifiedOn', 'legal_cases');
        $this->update_record(array('modifiedByChannel' => 'MSO'), 'channel = \'MSO\' and createdOn = modifiedOn', 'legal_cases');
        $this->update_record(array('modifiedByChannel' => 'A4L'), 'channel = \'MSO\' and createdOn != modifiedOn', 'legal_cases');
        $this->update_record(array('modifiedByChannel' => 'CP'), 'channel = \'CP\' and createdOn = modifiedOn', 'legal_cases');
        $this->update_record(array('modifiedBy' => null), 'channel = \'CP\' and createdOn != modifiedOn', 'legal_cases');
    }

    public function update_legal_case_changes()
    {
        $this->update_legal_case_related_data('legal_case_changes', 'legal_case_id', 'A4L', 'user_id');
        $this->update_legal_case_related_data('legal_case_changes', 'legal_case_id', 'MOB', 'user_id');
        $this->update_legal_case_related_data('legal_case_changes', 'legal_case_id', 'MSO', 'user_id');
        $this->update_legal_case_related_data('legal_case_changes', 'legal_case_id', 'CP', 'user_id');
    }

    public function update_workflow_status_transition_history()
    {
        $this->update_legal_case_related_data('workflow_status_transition_history', 'legal_case_id', 'A4L', 'user_id');
        $this->update_legal_case_related_data('workflow_status_transition_history', 'legal_case_id', 'MOB', 'user_id');
        $this->update_legal_case_related_data('workflow_status_transition_history', 'legal_case_id', 'MSO', 'user_id');
        $this->update_legal_case_related_data('workflow_status_transition_history', 'legal_case_id', 'CP', 'user_id');
    }

    public function update_customer_portal_sla_cases()
    {
        $this->update_legal_case_related_data('customer_portal_sla_cases', 'case_id', 'A4L', 'modifiedBy');
        $this->update_legal_case_related_data('customer_portal_sla_cases', 'case_id', 'MOB', 'modifiedBy');
        $this->update_legal_case_related_data('customer_portal_sla_cases', 'case_id', 'MSO', 'modifiedBy');
        $this->update_legal_case_related_data('customer_portal_sla_cases', 'case_id', 'CP', 'modifiedBy');
    }

    public function update_webdav_tree_structure()
    {
        $this->update_legal_case_related_document_data(true);
        $this->update_legal_case_related_document_data(false);
    }

    // execute the query in parameter and return the result in an associative array
    public function get_select_query_result($query)
    {
        $query_execution = $this->ci->db->query($query);
        $data = $query_execution->result_array();
        return $data;
    }

    // take an associative array that contain result of query wish return records ids only
    // and convert that associative array to simple one column array
    public function prep_ids($data)
    {
        $result = array();
        foreach ($data as $record) {
            array_push($result, $record['id']);
        }
        return $result;
    }

    // update a record in the data base
    // parameter
    // set : new values to set
    // where : condition of the records to update
    // table : name of the records table
    public function update_record($set, $where, $table)
    {
        $this->ci->db->set($set);
        $this->ci->db->where($where);
        return $this->ci->db->update($table);
    }

    // update data related to a legal_case that data come from creation and modification of a case
    // if the legal_case is not modified after creation we set the modification flag to the creation channel of the case
    // otherwise if the creation channle is portal the modification channel is not detectable we clear the id of the modifier (user)
    // if the creation channel is not portal then we set modification channel to web channel (A4L)
    // parameters
    // related_table: name of the table related to legal_case
    // case_reference_field_name: name of field containing case id
    // channel: creation channel of the case
    // modifier_user_field_name: name of field containing the if of the user ho made the change
    public function update_legal_case_related_data($related_table, $case_reference_field_name, $channel, $modifier_user_field_name)
    {
        $query = 'select ' . $related_table . '.id as id from ' . $related_table . ' inner join legal_cases on ' . $related_table . '.' . $case_reference_field_name . ' = legal_cases.id where channel = \'' . $channel . '\' and createdOn = modifiedOn';
        $data = $this->get_select_query_result($query);
        $records_ids = $this->prep_ids($data);
        if (!empty($records_ids)) {
            $this->update_record(array('modifiedByChannel' => $channel), $related_table . '.id in (' . implode(',', $records_ids) . ')', $related_table);
        }

        $query = 'select ' . $related_table . '.id as id from ' . $related_table . ' inner join legal_cases on ' . $related_table . '.' . $case_reference_field_name . ' = legal_cases.id where channel = \'' . $channel . '\' and createdOn != modifiedOn';
        $data = $this->get_select_query_result($query);
        $records_ids = $this->prep_ids($data);
        if (!empty($records_ids)) {
            $this->update_record($channel != 'CP' ? array('modifiedByChannel' => 'A4L') : array($modifier_user_field_name => null), $related_table . '.id in (' . implode(',', $records_ids) . ')', $related_table);
        }
    }

    // check if the user id belong to a user that belong to 'IS Group' sys admin group
    public function is_infosysta_user_group($id)
    {
        $query = 'select count(*) as is_infosysta_user_group from users inner join user_groups on users.user_group_id = user_groups.id where users.id = ' . $id . ' and user_groups.name = \'ISgroup\'';
        $data = $this->get_select_query_result($query);
        return $data[0]['is_infosysta_user_group'] > 0 ? true : false;
    }

    // get the id of the creator of the case
    public function get_case_creator($case_id)
    {
        $query = 'select createdBy from legal_cases where id = ' . $case_id;
        $data = $this->get_select_query_result($query);
        return $data[0]['createdBy'];
    }

    // update the attachment record in webdav table
    // if the record belong to a portal case we check the user group of the creator if it's Sys admin group
    // if so we set the creator to the case creator id and the creation flag to 'CP' and we do the same for the modifier and modification flag
    // if the record belong to a portal case but the user group of the creator if isn't Sys admin group then we set creation flag to 'A4L'
    // (it's a portal case but the attachment is created from web channel) and the
    // applie to modifier and this logic is appliable because befor this fix all attachment created from portal channel are created in the name of
    // Sys admin
    // for non portal cases we set the creation flag and the modification flag to 'A4L' cause the file can be uploded from web and api channels
    // we can't detecte the actual channel
    // parameters
    // case_id : the id of the case to wish the file belong
    // attachment : array that contain the attachement data
    // the channel of the case to wish the file belong
    public function update_attachment($case_id, $attachment, $channel)
    {
        $createdby_infosysta_user_group = $this->is_infosysta_user_group($attachment['createdBy']);
        $modifiedby_infosysta_user_group = $this->is_infosysta_user_group($attachment['modifiedBy']);
        $case_creator_id = $this->get_case_creator($case_id);
        if ($channel == 'CP' && $createdby_infosysta_user_group) {
            $update_set['createdBy'] = $case_creator_id;
            $update_set['createdByChannel'] = 'CP';
        } else {
            $update_set['createdByChannel'] = 'A4L';
        }
        if ($channel == 'CP' && $modifiedby_infosysta_user_group) {
            $update_set['modifiedBy'] = $case_creator_id;
            $update_set['modifiedByChannel'] = 'CP';
        } else {
            $update_set['modifiedByChannel'] = 'A4L';
        }
        $this->update_record($update_set, 'id = ' . $attachment['id'], 'webdav_tree_structure');
    }

    // it take an attachment and applie the update logic on it
    // the it check if this attachment is a folder if so it do same for the content of the folder
    // parameters
    // case_id : the id of the case to wish the file belong
    // attachment : array that contain the attachement data
    // the channel of the case to wish the file belong
    public function fix_attachment_data($case_id, $attachment, $channel)
    {
        $this->update_attachment($case_id, $attachment, $channel);
        if ($attachment['type'] == 'folder') {
            $query = 'select id,name,type,createdOn,createdBy,modifiedOn,modifiedBy from webdav_tree_structure where parentId = ' . $attachment['id'];
            $child_attachments = $this->get_select_query_result($query);
            foreach ($child_attachments as $child_attachment) {
                $this->fix_attachment_data($case_id, $child_attachment, $channel);
            }
        }
    }

    // organize data as an array in wish each row contain the case_id and the name of the case_folder (the id pre-fixed with 'case_')
    // parameter array containing case id's
    public function prep_cases_folders_data($data)
    {
        $cases_folders = array();
        foreach ($data as $record) {
            array_push($cases_folders, array('case_id' => ltrim($record['id'], '0'), 'case_folder_name' => 'case_' . ltrim($record['id'], '0')));
        }
        return $cases_folders;
    }

    // go through each case and fix it's attachment data
    // parameter
    // is_cp : flag to determine whether to go through portal or non portal created cases (cp for cutomer portal)
    public function update_legal_case_related_document_data($is_cp)
    {
        $query = 'select id from legal_cases where ' . ($is_cp ? 'channel = \'CP\'' : 'channel != \'CP\'');
        $data = $this->get_select_query_result($query);
        $cases_folders = $this->prep_cases_folders_data($data);
        // foreach case_folder check if the folder exists and fix it's data
        foreach ($cases_folders as $case_folder) {
            $query = 'select id,name,type,createdOn,createdBy,modifiedOn,modifiedBy from webdav_tree_structure where name = \'' . $case_folder['case_folder_name'] . '\'';
            $case_attachment_folder = $this->get_select_query_result($query);
            if (!empty($case_attachment_folder) > 0) {
                $this->fix_attachment_data($case_folder['case_id'], $case_attachment_folder[0], $is_cp ? 'CP' : '');
            }
        }
    }
}
