<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V6_4 extends Top_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->index();
    }

    public function index()
    {
        $this->remove_corrupted_files();
        $this->workflow_status_permissions_migration();
        $this->add_contact_categories_filters();
    }
    
    
    /*
     * remove corrupted files generated during upload failure and that by
     * deleting them from hard drive and deleting there related database records
     */

    public function remove_corrupted_files()
    {
        $files = $this->db->select("id, lineage")->where(array("name" => "FileRenamedTemporarly_forbiddenLetters", "type" => "file"))->get("webdav_tree_structure");
        foreach ($files->result() as $file) {
            $corepath = substr(COREPATH, 0, -12);
            @unlink("{$corepath}webdav/data{$file->lineage}");
            $this->db->delete('webdav_tree_structure', array('id' => $file->id));
        }
    }
    
    /*
     * retrieve user groups who have access to move status in the user group permissions, 
     * then set the transitions available in the workflow status transitions to those user groups so they have access to move status action
     */

    public function workflow_status_permissions_migration()
    {
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $user_groups = $this->user_group->load_all(array('select' => array('id, name')));
        $this->load->model('user_group_permission');
        $user_groups_allowed = array();
        foreach ($user_groups as $group) {
            $group_permissions = $this->user_group_permission->get_permissions($group['id'], false);
            if (isset($group_permissions['core']) && (in_array('/', $group_permissions['core']) || in_array('/cases/', $group_permissions['core']) || in_array('/cases/move_status/', $group_permissions['core']))) {
                $user_groups_allowed[] = $group['id'];
            }
        }
        $this->db->select('id');
        $query = $this->db->get('workflow_status_transition');
        foreach ($query->result_array() as $transition) {
            $data = array(
                'transition' => $transition['id'],
                'users' => '',
                'user_groups' => implode(',', $user_groups_allowed)
            );
            $this->db->insert('workflow_status_transition_permissions', $data);
        }
    }
    
    /*
     * inject all contact categories in sample data as saved filtes and visible for all users
     */

    public function add_contact_categories_filters()
    {
        $this->load->model('grid_saved_filter', 'grid_saved_filterfactory');
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->grid_saved_filter->disable_builtin_logs();
        $this->load->model('contact_company_category');
        $sys_lang = $this->system_preference->get_value_by_key('systemLanguage');
        $this->load->model('user', 'Userfactory');
        $this->user = $this->userfactory->get_instance();
        $is_admin_email = $this->user->get('isAdminUser');
        $this->user->fetch(['email' => $is_admin_email]);
        $admin_user = $this->user->get_field('id');
        $contact_categories = [
            'Lead' => 'Leads',
            'Partner' => 'Partners',
            'Other' => 'Others',
            'Provider' => 'Providers',
            'Client' => 'Clients'
        ];
        if ($sys_lang['keyValue'] === 'arabic') {
            $contact_categories = [
                'زبون محتمل' => 'الزبائن المحتملين',
                'شريك' => 'الشركاء',
                'آخر' => 'مختلف',
                'ممون' => 'الممومون',
                'زبون' => 'الزبائن'
            ];
        }
        foreach ($contact_categories as $sample_data_name => $filter_name) {
            $this->save_category_as_filter($sample_data_name, $filter_name, $admin_user);
        }
    }

    private function save_category_as_filter($sample_data_name, $filter_name, $admin_user)
    {
        $this->contact_category->reset_fields();
        $grid_filters = [
            'logic' => 'and',
            'filters' => []
        ];
        if ($this->contact_category->fetch(['name' => $sample_data_name])) {
            $contact_category_id = $this->contact_category->get_field('id');
            $this->grid_saved_filter->reset_fields();
            $grid_filters['filters'][0]['filters'][0] = [
                'field' => 'contacts.contact_category_id',
                'operator' => 'in',
                'value' => [$contact_category_id]
            ];
            $grid_filters = json_encode($grid_filters);
            $filter = [
                'gridFilters' => $grid_filters,
                'gridPageSize' => '20'
            ];
            $form_data = serialize($filter);
            $this->grid_saved_filter->set_fields([
                'filterName' => $filter_name,
                'model' => 'Contact',
                'formData' => $form_data,
                'isGlobalFilter' => 1,
                'user_id' => $admin_user,
                'createdOn' => date("Y-m-d H:i:s"),
                'createdBy' => $admin_user,
                'modifiedOn' => date("Y-m-d H:i:s"),
                'modifiedBy' => $admin_user
            ]);
            $this->grid_saved_filter->insert();
        }
    }
}
