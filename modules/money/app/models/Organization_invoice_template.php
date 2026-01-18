<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Organization_invoice_template extends My_Model_Factory
{
}
class mysql_Organization_invoice_template extends My_Model
{
    protected $modelName = "organization_invoice_template";
    protected $modelCode = "";
    protected $_table = "organization_invoice_templates";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "organization_id", "name", "settings", "type"];
    protected $allowedNulls = ["settings"];
    protected $builtInLogs = false;
    protected $default_template_settings = "a:4:{s:10:\"properties\";a:4:{s:9:\"page-size\";s:6:\"letter\";s:10:\"page-color\";s:7:\"#ffffff\";s:9:\"page-font\";s:7:\"Calibri\";s:16:\"page-orientation\";s:8:\"portrait\";}s:6:\"header\";a:2:{s:4:\"show\";a:5:{s:14:\"logo-container\";b:1;s:22:\"company-info-container\";b:1;s:11:\"center-logo\";b:0;s:16:\"logo-system-size\";b:1;s:16:\"image_full_width\";b:0;}s:7:\"general\";a:2:{s:5:\"notes\";s:290:\"<p style=\"margin: 0;\">&nbsp;</p><p style=\"margin: 0;\">Your Business Name</p><p style=\"margin: 0;\">Your Registration Number</p><p style=\"margin: 0;\">Your Street</p><p style=\"margin: 0;\">City, State, Country</p><p style=\"margin: 0;\">Your Phone Number</p><p style=\"margin: 0;\">Your Website</p>\";s:4:\"logo\";s:11:\"logo_54.png\";}}s:4:\"body\";a:4:{s:4:\"show\";a:26:{s:19:\"matter-id-container\";b:1;s:24:\"matter-subject-container\";b:1;s:17:\"bill-to-container\";b:1;s:20:\"invoice-nb-container\";b:1;s:22:\"invoice-date-container\";b:1;s:18:\"due-date-container\";b:1;s:15:\"terms-container\";b:1;s:24:\"invoice-status-container\";b:1;s:21:\"paid-amount-container\";b:0;s:12:\"po-container\";b:1;s:17:\"amount_in_letters\";b:0;s:27:\"time-logs-summary-container\";b:0;s:15:\"notes-container\";b:1;s:15:\"title-container\";b:1;s:19:\"matter-reference-nb\";b:1;s:10:\"tax_number\";i:1;s:18:\"sub-total-discount\";b:0;s:14:\"show-user-code\";b:0;s:18:\"show-exchange-rate\";b:0;s:21:\"invoice-ref-container\";b:1;s:25:\"invoice-description-table\";b:0;s:26:\"invoice-description-column\";b:1;s:20:\"show-entity-currency\";b:0;s:17:\"full_width_layout\";b:1;s:7:\"qr-code\";b:0;s:29:\"time-logs-rebuild-description\";b:0;}s:3:\"css\";a:11:{s:10:\"margin-top\";s:3:\"0.5\";s:29:\"invoice-information-font-size\";s:2:\"10\";s:24:\"invoice-tables-font-size\";s:2:\"10\";s:27:\"invoice-summation-font-size\";s:2:\"10\";s:23:\"invoice-notes-font-size\";s:2:\"10\";s:14:\"tables-borders\";s:4:\"both\";s:30:\"invoice-information-font-color\";s:7:\"#000000\";s:25:\"invoice-tables-font-color\";s:7:\"#000000\";s:28:\"invoice-summation-font-color\";s:7:\"#000000\";s:24:\"invoice-notes-font-color\";s:7:\"#000000\";s:31:\"tables-headers-background-color\";s:7:\"#87CEEB\";}s:7:\"general\";a:1:{s:10:\"line_items\";a:3:{s:8:\"expenses\";s:1:\"1\";s:9:\"time_logs\";s:1:\"2\";s:5:\"items\";s:1:\"3\";}}s:25:\"invoice_information_order\";a:2:{s:11:\"client_data\";a:5:{i:0;s:19:\"matter-id-container\";i:1;s:24:\"matter-subject-container\";i:2;s:19:\"matter-reference-nb\";i:3;s:17:\"bill-to-container\";i:4;s:10:\"tax_number\";}s:12:\"invoice_data\";a:8:{i:0;s:20:\"invoice-nb-container\";i:1;s:21:\"invoice-ref-container\";i:2;s:24:\"invoice-status-container\";i:3;s:22:\"invoice-date-container\";i:4;s:18:\"due-date-container\";i:5;s:15:\"terms-container\";i:6;s:12:\"po-container\";i:7;s:18:\"show-exchange-rate\";}}}s:6:\"footer\";a:2:{s:4:\"show\";a:2:{s:16:\"footer-container\";b:0;s:14:\"page-numbering\";b:0;}s:7:\"general\";a:1:{s:5:\"notes\";s:58:\"<p style=\"margin: 0;\" align=\"center\">www.sheria360.com</p>\";}}}";
    protected $default_bill_template_settings = "a:3:{s:6:\"header\";a:2:{s:4:\"show\";a:6:{s:14:\"logo-container\";b:1;s:22:\"company-info-container\";b:1;s:16:\"logo-system-size\";b:1;s:11:\"center-logo\";b:0;s:16:\"image_full_width\";b:0;s:17:\"full_width_layout\";b:0;}s:7:\"general\";a:2:{s:5:\"notes\";s:373:\"<p style=\"margin: 0;\">MyCompany Ltd</p><p style=\"margin: 0;\">Registration #:</p><p style=\"margin: 0;\">Riviera Building</p><p style=\"margin: 0;\">1 3 Fox Street</p><p style=\"margin: 0;\">London W1T 1JY</p><p style=\"margin: 0;\">United Kingdom</p><p style=\"margin: 0;\">Phone (678) 7890741</p><p style=\"margin: 0;\">Fax (678) 7890741</p><p style=\"margin: 0;\">www.mycompany.com</p>\";s:4:\"logo\";s:11:\"logo_54.png\";}}s:4:\"body\";a:3:{s:4:\"show\";a:10:{s:13:\"bill-supplier\";b:1;s:15:\"bill-tax-number\";b:1;s:11:\"bill-number\";b:1;s:19:\"bill-related-matter\";b:1;s:11:\"bill-client\";b:1;s:9:\"bill-date\";b:1;s:13:\"bill-due-date\";b:1;s:22:\"bill-amount-in-letters\";b:1;s:10:\"bill-notes\";b:1;s:10:\"bill-title\";b:1;}s:3:\"css\";a:1:{s:10:\"margin-top\";s:4:\"0.38\";}s:7:\"general\";a:1:{s:5:\"title\";a:3:{s:1:\"0\";s:4:\"Bill\";s:3:\"fl1\";s:7:\"Facture\";s:3:\"fl2\";s:12:\"فاتورة\";}}}s:6:\"footer\";a:2:{s:4:\"show\";a:2:{s:16:\"footer-container\";b:0;s:14:\"page-numbering\";b:0;}s:7:\"general\";a:1:{s:5:\"notes\";s:58:\"<p style=\"margin: 0;\" align=\"center\">www.sheria360.com</p>\";}}}";
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["organization_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "name" => ["required" => true, "allowEmpty" => false, "rule" => ["maxLength", 255], "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function load_organization_invoice_template_data($pagingOn = true, $type = "invoice")
    {
        $this->ci->load->model("organization", "organizationfactory");
        $this->ci->organization = $this->ci->organizationfactory->get_instance();
        $query = [];
        $query["select"] = ["organization_invoice_templates.id,organization_invoice_templates.name,organization_invoice_templates.type,organization_invoice_templates.is_default, concat( '" . $this->ci->organization->get("modelCode") . "', organization_invoice_templates.organization_id ) as organization_id,organizations.id as organizationId,organizations.name as organization_name"];
        $query["join"] = ["organizations", "organizations.id = organization_invoice_templates.organization_id", "inner"];
        $query["where"] = ["type", $type];
        $query["order_by"] = ["id asc"];
        $paginationConf = ["urlPrefix" => ""];
        $paginationConf["inPage"] = 20;
        return $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
    }
    public function prohibited_organizations()
    {
        $query = [];
        $query["select"] = ["DISTINCT organization_id,name", false];
        $query["order_by"] = ["organization_id asc"];
        $result = [];
        foreach ($this->load_all($query) as $value) {
            $result[(int) $value["organization_id"]] = $value["name"];
        }
        return $result;
    }
    public function load_invoice_templates_by_entity($entity)
    {
        $query = [];
        $query["select"] = ["organization_invoice_templates.*"];
        $query["where"] = ["organization_invoice_templates.organization_id", $entity];
        $query["order_by"] = ["id asc"];
        return parent::load_all($query);
    }
    public function fix_empty_templates($settings)
    {
        $settings["body"]["general"]["line_items"]["expenses"] = 1;
        $settings["body"]["general"]["line_items"]["time_logs"] = 2;
        $settings["body"]["general"]["line_items"]["items"] = 3;
        $this->set_field("settings", serialize($settings));
        $this->update();
        return $settings;
    }
    public function get_templates_list($type, $organization_id)
    {
        $query = [];
        $query["select"] = ["id, name", false];
        $query["where"] = [["organization_id", $organization_id], ["type", $type]];
        $data = $this->load_all($query);
        return $data;
    }
    public function get_entity_default_invoice_template($type, $organization_id)
    {
        $query = [];
        $query["select"] = ["organization_invoice_templates.*"];
        $query["where"] = [["organization_invoice_templates.organization_id", $organization_id], ["organization_invoice_templates.type", $type], ["organization_invoice_templates.is_default", "1"]];
        return parent::load($query);
    }
    public function set_entity_default_invoice_template($id)
    {
        if (!$id) {
            return false;
        }
        $query = [];
        $query["select"] = ["*"];
        $query["where"] = [["id", $id]];
        $template_row = parent::load($query);
        if (empty($template_row)) {
            return false;
        }
        $response = $this->ci->db->where("id", $template_row["id"])->update($this->_table, ["is_default" => 1]);
        if (!$response) {
            return false;
        }
        $this->ci->db->where(["id !=" => $template_row["id"], "organization_id" => $template_row["organization_id"], "type" => $template_row["type"]])->update($this->_table, ["is_default" => 0]);
        return true;
    }
    public function unset_default_invoice_template($id)
    {
        if (!$id) {
            return false;
        }
        $response = $this->ci->db->where("id", $id)->update($this->_table, ["is_default" => 0]);
        if (!$response) {
            return false;
        }
        return true;
    }
}
class mysqli_Organization_invoice_template extends mysql_Organization_invoice_template
{
}
class sqlsrv_Organization_invoice_template extends mysql_Organization_invoice_template
{
    public function load_organization_invoice_template_data($pagingOn = true, $type = "invoice")
    {
        $this->ci->load->model("organization", "organizationfactory");
        $this->ci->organization = $this->ci->organizationfactory->get_instance();
        $query = [];
        $query["select"] = ["organization_invoice_templates.id,organization_invoice_templates.name,organization_invoice_templates.type,organization_invoice_templates.is_default, ( '" . $this->ci->organization->get("modelCode") . "' + CAST( organization_invoice_templates.organization_id AS nvarchar ) ) as organization_id,organizations.id as organizationId,organizations.name as organization_name"];
        $query["join"] = ["organizations", "organizations.id = organization_invoice_templates.organization_id", "inner"];
        $query["where"] = ["type", $type];
        $query["order_by"] = ["id asc"];
        $paginationConf = ["urlPrefix" => ""];
        $paginationConf["inPage"] = 20;
        return $pagingOn ? parent::paginate($query, $paginationConf) : parent::load_all($query);
    }
}

?>