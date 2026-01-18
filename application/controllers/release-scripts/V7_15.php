<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_15 extends CI_Controller
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
        $this->link_customers_to_contacts();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function link_customers_to_contacts()
    {
        $this->write_log($this->log_path, 'link_customers_to_contacts started', 'info');
        $query = $this->db->query("SELECT * FROM customer_portal_users");
        $result = $query->result_array();
        $this->load->model('customer_portal_users', 'customer_portal_usersfactory');
        $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
        $this->load->model('contact', 'contactfactory');
        $this->contact = $this->contactfactory->get_instance();
        foreach($result as $row){
            // link customer to contact
            $contact_id = $this->add_cp_user_as_contact($row["id"]);
            $this->db->set("contact_id", $contact_id);
            $this->db->where("id", $row["id"]);
            $this->db->update("customer_portal_users");
        }
        $this->write_log($this->log_path, 'link_customers_to_contacts is done', 'info');
    }
    
    private function add_cp_user_as_contact($customerId)
    {
        $this->customer_portal_users->reset_fields();
        $this->contact->reset_fields();
        $this->customer_portal_users->fetch($customerId);
        $contact_fetch = $this->contact->fetch([
            'firstName' => $this->customer_portal_users->get_field('firstName'),
            'lastName' => $this->customer_portal_users->get_field('lastName'),
            'email' => $this->customer_portal_users->get_field('email')
        ]);
        $contact_id = "";
        if (!$contact_fetch) {
            // get default category
            $this->load->model('contact_company_category');
            $this->contact_company_category->fetch(['name' => 'Other']);
            $contact_category_id = $this->contact_company_category->get_field('id');
            $validate = $this->contact->get('validate');
            unset($validate['lastName']['unique']);
            $validate = $this->contact->set('validate', $validate);
            //contact does not exist so create
            $this->contact->set_field('firstName', $this->customer_portal_users->get_field('firstName'));
            $this->contact->set_field('lastName', $this->customer_portal_users->get_field('lastName'));
            $this->contact->set_field('email', $this->customer_portal_users->get_field('email'));
            $this->contact->set_field('phone', $this->customer_portal_users->get_field('phone'));
            $this->contact->set_field('mobile', $this->customer_portal_users->get_field('mobile'));
            $this->contact->set_field('address1', $this->customer_portal_users->get_field('address'));
            $this->contact->set_field('jobTitle', $this->customer_portal_users->get_field('jobTitle'));
            $this->contact->set_field('status', 'Active');
            $this->contact->set_field('contact_category_id', $contact_category_id);
            $this->contact->set_field('isLawyer', 'no');
            $this->contact->set_field('lawyerForCompany', 'no');
            $this->contact->set_field('father', '');
            $this->contact->set_field('mother', '');
            $this->contact->set_field('jobTitle', '');
            $this->contact->set_field('website', '');
            $this->contact->set_field('fax', '');
            $this->contact->set_field('address2', '');
            $this->contact->set_field('city', '');
            $this->contact->set_field('state', '');
            $this->contact->set_field('zip', '');
            $this->contact->set_field('comments', '');
            $this->contact->disable_builtin_logs();
            $this->contact->set_field('createdOn', date("Y-m-d H:i:s", time()));
            $this->contact->set_field('modifiedOn', date("Y-m-d H:i:s", time()));
            $this->contact->set_field('createdBy', $this->customer_portal_users->get_field('createdBy'));
            $this->contact->set_field('modifiedBy', $this->customer_portal_users->get_field('createdBy'));
            if ($this->contact->insert()) {
                $contact_id = $this->contact->get_field('id');
            }
        } else {
            $contact_id = $this->contact->get_field('id');
        }
        return $contact_id;
    }
}
