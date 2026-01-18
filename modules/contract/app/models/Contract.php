<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract extends My_Model_Factory
{
}
class mysqli_Contract extends My_Model
{
    protected $modelName = "contract";
    protected $_table = "contract";
    protected $modelCode = "CT";
    protected $_listFieldName = "name";
    protected $_fieldsNames = [
        "id",
        "name",
        "description",
        "value",
        "type_id",
        "contract_date",
        "start_date",
        "end_date",
        "reference_number",
        "assigned_team_id",
        "assignee_id",
        "requester_id",
        "status_comments",
        "priority",
        "workflow_id",
        "status_id",
        "renewal_type",
        "currency_id",
        "private",
        "visible_to_cp",
        "channel",
        "createdOn",
        "createdBy",
        "modifiedOn",
        "modifiedBy",
        "modifiedByChannel",
        "status",
        "amendment_of",
        "authorized_signatory",
        "app_law_id",
        "country_id",
        "sub_type_id",
        "archived",
        "hideFromBoard",
        "category",
        "stage",
        "milestone_visible_to_cp",
        "contract_duration",
        "perf_security_commencement_date",
        "perf_security_expiry_date",
        "expected_completion_date",
        "actual_completion_date",
         "effective_date",
        "advance_payment_guarantee",
        "letter_of_credit_details",
          "department_id"
    ];

    protected $allowedNulls = [
        "description",
        "value",
        "start_date",
        "end_date",
        "reference_number",
        "assignee_id",
        "status_comments",
        "currency_id",
        "private",
        "visible_to_cp",
        "renewal_type",
        "amendment_of",
        "authorized_signatory",
        "app_law_id",
        "country_id",
        "sub_type_id",
        "stage",
        "milestone_visible_to_cp",
        "contract_duration",
        "perf_security_commencement_date",
        "perf_security_expiry_date",
        "expected_completion_date",
        "actual_completion_date",
        "effective_date",
        "advance_payment_guarantee",
        "letter_of_credit_details"
    ];
    protected $priorityValues = ["critical", "high", "medium", "low"];
    protected $renewal_values = ["one_time", "renewable_automatically", "renewable_with_notice", "unlimited_period", "other"];
    protected $lookupInputsToValidate = [["input_name" => "assignee_id", "error_field" => "assignee_id", "message" => ["main_var" => "not_exists", "lookup_for" => "user"]], ["input_name" => "contract_lookup[]", "error_field" => "contract_member_id[]", "message" => ["main_var" => "not_exists3"]]];
    protected $builtInLogs = true;
    protected $cp_channel = "CP";
    protected $outlook_channel = "MSO";
    protected $apiGmailChannel = "A4G";
    protected $web_channel = "A4L";
    protected $statusValues = ["","Active", "Inactive","Expired","Suspended"];
    protected $archivedValues = ["", "yes", "no"];
    protected $categoryValues = ["contract", "mou"];
    protected $stageValues = ["","Development", "Implementation"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]], "type_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "requester_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "assigned_team_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "priority" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->priorityValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->priorityValues))], "workflow_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("required_rule"), $this->ci->lang->line("workflow"))], "status_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("required_rule"), $this->ci->lang->line("status"))], "currency_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("currency"))], 
        "contract_date" => ["isRequired" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("contract_date"))]], "start_date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("start_date"))], "end_date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("end_date"))], "status" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->statusValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->statusValues))], "app_law_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("applicable_law"))], "country_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("country"))], "sub_type_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("sub_type"))], "value" => ["maxLength" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 13], "message" => sprintf($this->ci->lang->line("max_characters"), 13)], "numeric" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("value"))]], "archived" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->archivedValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->archivedValues))], "hideFromBoard" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 3], "message" => sprintf($this->ci->lang->line("max_characters"), 3)]
    ,
            //"stage" => ["required" => false, "allowEmpty" => true, "rule" => ["inList", $this->stageValues], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->stageValues))],
        "contract_duration" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 50], "message" => sprintf($this->ci->lang->line("max_characters"), 50)], "perf_security_commencement_date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("perf_security_commencement_date"))], "perf_security_expiry_date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("perf_security_expiry_date"))], "expected_completion_date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("expected_completion_date"))], "actual_completion_date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("actual_completion_date"))], "effective_date" => ["required" => false, "allowEmpty" => true, "rule" => $this->custom_validation_rules["date"], "message" => sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("effective_date"))], "advance_payment_guarantee" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)], "letter_of_credit_details" => ["required" => false, "allowEmpty" => true, "rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]];
        $this->logged_user_id = $this->ci->is_auth->get_user_id();
        $this->override_privacy = $this->ci->is_auth->get_override_privacy();
        $this->ci->load->library("dms");
    }
    public function load_data($contract_id)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"][] = ["contract.*, type.name as type, sub_type.name as sub_type, department.name as department_name , (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) as requester,CONCAT(assignee.firstName,' ',assignee.lastName) as assignee, CONCAT(authorizedSignatory.firstName,' ',authorizedSignatory.lastName) as authorized_signatory_name,\r\n        CONCAT(assignee.firstName,' ',assignee.lastName) as assignee, assigned_team.name as assigned_team,\r\n         workflow.name as workflow_name, status.name as status_name, iso_currencies.code as currency, status_category.color as status_color,  contract.renewal_type as renewal,\r\n          amended.name as amendment_of_name, countries_languages.name as country, applicable_law.name as applicable_law,\r\n         (CASE WHEN contract.channel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles\r\n         WHERE user_profiles.user_id = contract.createdBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')\r\n         FROM customer_portal_users WHERE customer_portal_users.id = contract.createdBy) END) as creator,\r\n         (CASE WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles\r\n         WHERE user_profiles.user_id = contract.modifiedBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')\r\n         FROM customer_portal_users WHERE customer_portal_users.id = contract.modifiedBy) END) as modifier", false];
        $query["where"][] = ["contract.id", $contract_id];
        $query["join"] = [["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"],
            ["user_profiles authorizedSignatory", "authorizedSignatory.user_id = contract.authorized_signatory", "left"],
            ["contacts requester", "requester.id = contract.requester_id", "left"],
            ["provider_groups assigned_team", "assigned_team.id = contract.assigned_team_id", "left"],
            ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"],
            ["sub_contract_type_language as sub_type", "sub_type.sub_type_id = contract.sub_type_id and sub_type.language_id = '" . $lang_id . "'", "left"],
            ["contract_status", "contract_status.id = contract.status_id", "left"],
            ["status_category", "status_category.id = contract_status.category_id", "left"],
            ["status_category", "status_category.id = contract_status.category_id", "left"],
            ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_workflow as workflow", "workflow.id = contract.workflow_id", "left"],
            ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"],
            ["contract as amended", "amended.id = contract.amendment_of", "left"],
            ["departments as department", "department.id = contract.department_id", "left"],
            ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"],
            ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"]];
        return $this->load($query);
    }
    public function api_load_data($contract_id)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->get_lang_id(true);
        $query["select"][] = ["contract.*, type.name as type, sub_type.name as sub_type, department.name as department_name (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) as requester,CONCAT(assignee.firstName,' ',assignee.lastName) as assignee, CONCAT(authorizedSignatory.firstName,' ',authorizedSignatory.lastName) as authorized_signatory_name,\r\n        CONCAT(assignee.firstName,' ',assignee.lastName) as assignee, assigned_team.name as assigned_team,\r\n         workflow.name as workflow_name, status.name as status_name, iso_currencies.code as currency, status_category.color as status_color,  contract.renewal_type as renewal,\r\n          amended.name as amendment_of_name, countries_languages.name as country, applicable_law.name as applicable_law,\r\n         (CASE WHEN contract.channel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles\r\n         WHERE user_profiles.user_id = contract.createdBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')\r\n         FROM customer_portal_users WHERE customer_portal_users.id = contract.createdBy) END) as creator,\r\n         (CASE WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles\r\n         WHERE user_profiles.user_id = contract.modifiedBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')\r\n         FROM customer_portal_users WHERE customer_portal_users.id = contract.modifiedBy) END) as modifier", false];
        $query["where"][] = ["contract.id", $contract_id];
        $query["join"] = [["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"], ["departments as department", "department.id = contract.department_id", "left"], ["user_profiles authorizedSignatory", "authorizedSignatory.user_id = contract.authorized_signatory", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["provider_groups assigned_team", "assigned_team.id = contract.assigned_team_id", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["sub_contract_type_language as sub_type", "sub_type.sub_type_id = contract.sub_type_id and sub_type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_workflow as workflow", "workflow.id = contract.workflow_id", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contract as amended", "amended.id = contract.amendment_of", "left"], ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"], ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"]];
        return $this->load($query);
    }
    public function get_lang_id($is_api = false)
    {
        $this->ci->load->model("language");
        $lang = NULL;
        if ($is_api) {
            $logged_user_id = $this->user_logged_in_data["user_id"];
            $this->ci->load->model("user_preference");
            $this->ci->user_preference->fetch(["user_id" => $logged_user_id, "keyName" => "language"]);
            $lang = $this->ci->user_preference->get_field("keyValue");
        }
        return $this->ci->language->get_id_by_session_lang($lang);
    }
    public function load_contract_details($contract_id)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.name, status.name as status, type.name as type, contract.description,    
                                      CONCAT(up.firstName, ' ', up.lastName) as assignee, contract.start_date, contract.end_date,                                  contract.contract_date, contract.value, contract.reference_number, contract.priority,                                  contract.createdOn, contract.modifiedOn, contract.archived as archived,                                  iso_currencies.code as currency,                                  (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) as requester, contract.renewal_type as renewal,                                  (SELECT GROUP_CONCAT(CASE WHEN `contract_party`.`party_member_type` IS NULL THEN NULL                                                       ELSE (CASE WHEN `party_category_language`.`name` != '' THEN (CASE WHEN `contract_party`.`party_member_type` = 'company'                                                                                                                    THEN CONCAT(`party_company`.`name`, ' - ', `party_category_language`.`name`)                                                             ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT( `party_contact`.`firstName`, ' ', `party_contact`.`father`, ' ', `party_contact`.`lastName`,  ' - ', `party_category_language`.`name`)                                                                   ELSE CONCAT(  `party_contact`.`firstName`,  ' ', `party_contact`.`lastName`, ' - ', `party_category_language`.`name`)                                                                   END)                                                            END)                                                      ELSE (CASE WHEN `contract_party`.`party_member_type` = 'company' THEN `party_company`.`name`                                                            ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT(`party_contact`.`firstName`,   ' ', `party_contact`.`father`,  ' ',  `party_contact`.`lastName`)                                                                  ELSE CONCAT( `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`)                                                                  END)                                                            END)                                                      END)                                                  END                                   order by `contract_party`.`contract_id` ASC SEPARATOR ', ')                                  FROM `contract_party`                                             LEFT JOIN `party` ON `party`.`id` = `contract_party`.`party_id`                                             LEFT JOIN `companies` AS `party_company`                                                       ON `party_company`.`id` = `party`.`company_id` AND                                                          `contract_party`.`party_member_type` = 'company'                                             LEFT JOIN `contacts` AS `party_contact`                                                       ON `party_contact`.`id` = `party`.`contact_id` AND                                                          `contract_party`.`party_member_type` = 'contact'                                             LEFT JOIN `party_category_language`                                                       ON `party_category_language`.`category_id` =                                                          `contract_party`.`party_category_id` and                                                          `party_category_language`.`language_id` = '" . $lang_id . "'\r\n                                  WHERE `contract_party`.`contract_id` = `contract`.`id`)   AS `parties`, CONCAT(authorizedSignatory.firstName,' ',authorizedSignatory.lastName) as authorized_signatory_name", false];
        $query["join"] = [["contract_party", "contract_party.contract_id = contract.id", "left"], ["user_profiles authorizedSignatory", "authorizedSignatory.user_id = contract.authorized_signatory", "left"], ["party", "party.id = contract_party.party_id", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["user_profiles as up", "up.user_id = contract.assignee_id", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"]];
        $query["where"] = ["contract.id", $contract_id];
        $result = $this->load($query);
        return $result;
    }
    public function k_load_all20_04_2025($filter, $sortable, $return_query = false)
    {
        /*$query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $this->ci->db->query("set optimizer_switch = 'block_nested_loop=off'");
        $query["select"][] = [" SQL_CALC_FOUND_ROWS `contract`.`id`    AS `id`, `contract`.`name`  AS `name`,\r\n                                   `provider_groups`.`name`    AS `assigned_team`,\r\n                                   `contract`.`description` AS `description`, `contract`.`priority`    AS `priority`, `contract`.`status`    AS `contract_status`, `contract`.`contract_date` AS `contract_date`, `contract`.`start_date`      AS `start_date`, `contract`.`end_date`     AS `end_date`, `contract`.`status_comments`       AS `status_comments`, `contract`.`value`   AS `value`, contract.amendment_of as amendment_of, `contract`.`archived`  AS `archived`, amended.name as amendment_of_name, `contract`.`reference_number`    AS `reference_number`, `contract`.`private`     AS `private`, `contract`.`channel`, `contract`.`visible_to_cp`, countries_languages.name as country, applicable_law.name as applicable_law, concat('" . $this->get("modelCode") . "', `contract`.`id`)      AS `contract_id`,                                   `status`.`name`    AS `status`, contract.renewal_type as renewal,                                   (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) AS `requester`,                                   `requester`.`status`    AS `requester_status`,                                   concat(`up`.`firstName`, ' ', `up`.`lastName`) AS `assignee`,                                   `type`.`name`   AS `type`,                                   `up`.`status`    AS `userStatus`,                                   `contract`.`createdOn`   AS `createdOn`,    `contract`.`createdBy`   AS `createdBy`,                                   (CASE WHEN contract.channel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles\r\n                             WHERE user_profiles.user_id = contract.createdBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')\r\n                             FROM customer_portal_users WHERE customer_portal_users.id = contract.createdBy) END) as createdByName, `contract`.`modifiedOn`  AS `modifiedOn`,`contract`.`modifiedBy`  AS `modifiedBy`,\r\n (CASE WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles                             WHERE user_profiles.user_id = contract.modifiedBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')                             FROM customer_portal_users WHERE customer_portal_users.id = contract.modifiedBy) END) as modifiedByName,                                   iso_currencies.code as currency,                                   (SELECT GROUP_CONCAT(CASE WHEN `contract_party`.`party_member_type` IS NULL THEN NULL                                                        ELSE (CASE WHEN `party_category_language`.`name` != '' THEN (CASE WHEN `contract_party`.`party_member_type` = 'company'                                                                                                                     THEN CONCAT(`party_company`.`name`, ' - ', `party_category_language`.`name`)                                                              ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT( `party_contact`.`firstName`, ' ', `party_contact`.`father`, ' ', `party_contact`.`lastName`,  ' - ', `party_category_language`.`name`)                                                                    ELSE CONCAT(  `party_contact`.`firstName`,  ' ', `party_contact`.`lastName`, ' - ', `party_category_language`.`name`)                                                                    END)                                                             END)                                                       ELSE (CASE WHEN `contract_party`.`party_member_type` = 'company' THEN `party_company`.`name`                                                             ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT(`party_contact`.`firstName`,   ' ', `party_contact`.`father`,  ' ',  `party_contact`.`lastName`)                                                                   ELSE CONCAT( `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`)                                                                   END)                                                             END)                                                       END)                                                   END                                    order by `contract_party`.`contract_id` ASC SEPARATOR ', ')                                   FROM `contract_party`                                              LEFT JOIN `party` ON `party`.`id` = `contract_party`.`party_id`                                              LEFT JOIN `companies` AS `party_company`                                                        ON `party_company`.`id` = `party`.`company_id` AND                                                           `contract_party`.`party_member_type` = 'company'                                              LEFT JOIN `contacts` AS `party_contact`                                                        ON `party_contact`.`id` = `party`.`contact_id` AND                                                           `contract_party`.`party_member_type` = 'contact'                                              LEFT JOIN `party_category_language`                                                        ON `party_category_language`.`category_id` =                                                           `contract_party`.`party_category_id` and                                                           `party_category_language`.`language_id` = '" . $lang_id . "' WHERE `contract_party`.`contract_id` = `contract`.`id`)   AS `parties` ", false];
        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["join"] = [["`user_profiles` `up`", "`up`.`user_id` = `contract`.`assignee_id`", "left"], ["`contacts` `requester`", "`requester`.`id` = `contract`.`requester_id`", "left"], ["`provider_groups`", "`provider_groups`.`id` = `contract`.`assigned_team_id`", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status_language as status", "status.status_id = contract.status_id and status.language_id = '" . $lang_id . "'", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contract as amended", "amended.id = contract.amendment_of", "left"], ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"], ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"]];
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["contract.id desc"];
        }
        if ($return_query) {
            return $query;
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $query["select"][] = [$this->ci->custom_field->load_grid_custom_fields($this->modelName, $this->_table)];
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $this->ci->db->query("SELECT FOUND_ROWS() AS `count`")->row()->count;
        return $response;
    }
    //modified for getting totals paid
    public function k_load_all($filter, $sortable, $return_query = false)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $this->ci->db->query("set optimizer_switch = 'block_nested_loop=off'");
        $query["select"][] = [" SQL_CALC_FOUND_ROWS `contract`.`id`    AS `id`,\r\n                                   `contract`.`name`  AS `name`,\r\n                                   `provider_groups`.`name`    AS `assigned_team`,\r\n                                   `contract`.`description` AS `description`,\r\n                                   `contract`.`priority`    AS `priority`,\r\n                                   `contract`.`status`    AS `contract_status`,\r\n                                   `contract`.`contract_date` AS `contract_date`,\r\n                                   `contract`.`start_date`      AS `start_date`,\r\n                                   `contract`.`end_date`     AS `end_date`,\r\n                                   `contract`.`status_comments`       AS `status_comments`,\r\n                                   `contract`.`value`   AS `value`,\r\n                                   contract.amendment_of as amendment_of,\r\n                                   `contract`.`archived`  AS `archived`,\r\n                                   amended.name as amendment_of_name,\r\n                                   `contract`.`reference_number`    AS `reference_number`,\r\n                                   `contract`.`private`     AS `private`,\r\n                                   `contract`.`channel`,\r\n                                   `contract`.`visible_to_cp`, countries_languages.name as country, applicable_law.name as applicable_law,\r\n                                   concat('" . $this->get("modelCode") . "', `contract`.`id`)      AS `contract_id`,                                   `status`.`name`    AS `status`, contract.renewal_type as renewal,\r\n                                   (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) AS `requester`,\r\n                                   `requester`.`status`    AS `requester_status`,\r\n                                   concat(`up`.`firstName`, ' ', `up`.`lastName`) AS `assignee`,\r\n                                   `type`.`name`   AS `type`,\r\n                                   `up`.`status`    AS `userStatus`,\r\n                                   `contract`.`createdOn`   AS `createdOn`,    `contract`.`createdBy`   AS `createdBy`,\r\n                                   (CASE WHEN contract.channel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles\r\n                             WHERE user_profiles.user_id = contract.createdBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')\r\n                             FROM customer_portal_users WHERE customer_portal_users.id = contract.createdBy) END) as createdByName,\r\n                                   `contract`.`modifiedOn`  AS `modifiedOn`,`contract`.`modifiedBy`  AS `modifiedBy`,\r\n\r\n                                   (CASE WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles\r\n                             WHERE user_profiles.user_id = contract.modifiedBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')\r\n                             FROM customer_portal_users WHERE customer_portal_users.id = contract.modifiedBy) END) as modifiedByName,\r\n                                   iso_currencies.code as currency,\r\n                                   COALESCE(SUM(CASE WHEN `contract_milestone`.`financial_status` IN ('paid', 'partially_paid') THEN `contract_milestone`.`amount` ELSE 0 END), 0) AS amount_paid_so_far,\r\n                                   (`contract`.`value` - COALESCE(SUM(CASE WHEN `contract_milestone`.`financial_status` IN ('paid', 'partially_paid') THEN `contract_milestone`.`amount` ELSE 0 END), 0)) AS  balance_due,\r\n                                   (SELECT GROUP_CONCAT(CASE WHEN `contract_party`.`party_member_type` IS NULL THEN NULL\r\n                                                        ELSE (CASE WHEN `party_category_language`.`name` != '' THEN (CASE WHEN `contract_party`.`party_member_type` = 'company'\r\n                                                                                                                     THEN CONCAT(`party_company`.`name`, ' - ', `party_category_language`.`name`)\r\n                                                              ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT( `party_contact`.`firstName`, ' ', `party_contact`.`father`, ' ', `party_contact`.`lastName`,  ' - ', `party_category_language`.`name`)\r\n                                                                    ELSE CONCAT(  `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`, ' - ', `party_category_language`.`name`)\r\n                                                                    END)\r\n                                                             END)\r\n                                                       ELSE (CASE WHEN `contract_party`.`party_member_type` = 'company' THEN `party_company`.`name`\r\n                                                             ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT(`party_contact`.`firstName`,   ' ', `party_contact`.`father`,  ' ',  `party_contact`.`lastName`)\r\n                                                                   ELSE CONCAT( `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`)\r\n                                                                   END)\r\n                                                             END)\r\n                                                       END)\r\n                                                   END\r\n                                    order by `contract_party`.`contract_id` ASC SEPARATOR ', ')   AS `parties`\r\n                                   ", false];
        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["join"] = [
            ["`user_profiles` `up`", "`up`.`user_id` = `contract`.`assignee_id`", "left"],
            ["`contacts` `requester`", "`requester`.`id` = `contract`.`requester_id`", "left"],
            ["`provider_groups`", "`provider_groups`.`id` = `contract`.`assigned_team_id`", "left"],
            ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"],
            ["contract_status_language as status", "status.status_id = contract.status_id and status.language_id = '" . $lang_id . "'", "left"],
            ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"],
            ["contract as amended", "amended.id = contract.amendment_of", "left"],
            ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"],
            ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"],
            ["`contract_milestone` ", "`contract_milestone`.`contract_id` = `contract`.`id`", "left"]
        ];
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["contract.id desc"];
        }
        $query["group_by"] = "`contract`.`id`";
        if ($return_query) {
            return $query;
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $query["select"][] = [$this->ci->custom_field->load_grid_custom_fields($this->modelName, $this->_table)];
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $this->ci->db->query("SELECT FOUND_ROWS() AS `count`")->row()->count;
        exit(json_decode($response));//return $response;
   */ }
    public function universal_search($q, $paging_on = true)
    {
        $q = addslashes(trim((string) $q));
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"][] = ["contract.*,  concat('" . $this->get("modelCode") . "', contract.id) AS contract_id, type.name as type, (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) as requester,         CONCAT(assignee.firstName,' ',assignee.lastName) as assignee,    (SELECT GROUP_CONCAT(CASE WHEN `contract_party`.`party_member_type` IS NULL THEN NULL                                                        ELSE (CASE WHEN `party_category_language`.`name` != '' THEN (CASE WHEN `contract_party`.`party_member_type` = 'company'                                                                                                                     THEN CONCAT(`party_company`.`name`, ' - ', `party_category_language`.`name`)                                                              ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT( `party_contact`.`firstName`, ' ', `party_contact`.`father`, ' ', `party_contact`.`lastName`,  ' - ', `party_category_language`.`name`)                                                                    ELSE CONCAT(  `party_contact`.`firstName`,  ' ', `party_contact`.`lastName`, ' - ', `party_category_language`.`name`)                                                                    END)                                                             END)                                                       ELSE (CASE WHEN `contract_party`.`party_member_type` = 'company' THEN `party_company`.`name`                                                             ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT(`party_contact`.`firstName`,   ' ', `party_contact`.`father`,  ' ',  `party_contact`.`lastName`)                                                                   ELSE CONCAT( `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`)                                                                   END)                                                             END)                                                       END)                                                   END                                    order by `contract_party`.`contract_id` ASC SEPARATOR ', ')                                   FROM `contract_party`                                              LEFT JOIN `party` ON `party`.`id` = `contract_party`.`party_id`                                              LEFT JOIN `companies` AS `party_company`                                                        ON `party_company`.`id` = `party`.`company_id` AND                                                           `contract_party`.`party_member_type` = 'company'                                              LEFT JOIN `contacts` AS `party_contact`                                                        ON `party_contact`.`id` = `party`.`contact_id` AND                                                           `contract_party`.`party_member_type` = 'contact'                                              LEFT JOIN `party_category_language`                                                        ON `party_category_language`.`category_id` =                                                           `contract_party`.`party_category_id` and                                                           `party_category_language`.`language_id` = '" . $lang_id . "' WHERE `contract_party`.`contract_id` = `contract`.`id`)   AS `parties`,\r\n         status.name as status_name, iso_currencies.code as currency, status_category.color as status_color, contract.renewal_type as renewal", false];
        $query["join"] = [["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["provider_groups assigned_team", "assigned_team.id = contract.assigned_team_id", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_workflow as workflow", "workflow.id = contract.workflow_id", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contract_party", "contract_party.contract_id = contract.id", "left"], ["party", "contract_party.party_id = party.id", "left"], ["contacts", "party.contact_id = contacts.id", "left"], ["companies", "party.company_id = companies.id", "left"]];
        $query["where"][] = ["(contract.name LIKE '%" . $q . "%' \r\n            OR contract.id = if(SUBSTRING('" . $q . "', 1, 2) = '" . $this->modelCode . "', SUBSTRING('" . $q . "', 3), '" . $q . "'))\r\n            OR contacts.firstName LIKE '%" . $q . "%'\r\n            OR contacts.lastName LIKE '%" . $q . "%'\r\n            OR concat(contacts.firstName,' ',contacts.lastName) LIKE '%" . $q . "%'\r\n            OR companies.name LIKE '%" . $q . "%'"];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["contract.id desc"];
        return $paging_on ? parent::paginate($query, ["urlPrefix" => ""]) : parent::load_all($query);
    }
    public function count_all_contracts()
    {
        $user_id = $this->ci->user_logged_in_data["user_id"];
        $this->ci->user_profile->fetch(["user_id" => $user_id]);
        $override_privacy = $this->ci->user_profile->get_field("overridePrivacy");
        $query["select"] = ["COUNT(0) as contracts", false];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.createdBy = '" . $user_id . "' OR contract.assignee_id = '" . $user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $user_id . "') OR '" . $override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        return $this->load($query)["contracts"];
    }
    public function load_all_contract_docs($contract_id, $ext = "", $parent_id = "")
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $_table = $this->_table;
        $this->_table = "documents_management_system AS documents";
        $query["select"] = ["documents.id as document_id, documents.type, documents.name, documents.extension, parent.lineage as parent_lineage, concat(documents.name,'.',documents.extension) AS full_name, documents.module_record_id as contract_id, status.name as status_name"];
        $query["join"][] = ["documents_management_system parent", "parent.id = documents.parent", "left"];
        $query["join"][] = ["contract_document_status_language status", "status.status_id = documents.document_status_id AND status.language_id = " . $lang_id, "left"];
        $query["where"][] = ["documents.module_record_id", $contract_id];
        $query["where"][] = ["documents.module", $this->modelName];
        $query["where"][] = ["documents.type", "file"];
        $query["where"][] = ["documents.visible", "1"];
        if ($ext) {
            $query["where"][] = ["documents.extension", $ext];
        }
        if ($parent_id) {
            $query["where"][] = ["documents.parent", $parent_id];
        }
        $response = $this->load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_approval_signature_documents($contract_id, $ext = "", $type = "to_be_signed")
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system AS documents";
        $query["select"] = ["documents.id as document_id, documents.type, documents.name, documents.extension, parent.lineage as parent_lineage,\r\n         concat(documents.name,'.',documents.extension) AS full_name, documents.module_record_id as contract_id, documents.comment, documents.modifiedOn,\r\n          signed_doc.signed_on,\r\n           CASE WHEN signed_doc.signed_by_type = 'user'\r\n           THEN (SELECT concat(user_profiles.firstName, ' ', user_profiles.lastName) from user_profiles WHERE user_profiles.user_id = signed_doc.signed_by)\r\n           ELSE (SELECT concat(contacts.firstName, ' ', contacts.lastName) from contacts WHERE contacts.id = signed_doc.signed_by) END as signed_by, signed_doc.signed_by_type"];
        $query["join"][] = ["documents_management_system parent", "parent.id = documents.parent", "left"];
        $query["join"][] = ["contract_signed_document signed_doc", "signed_doc.document_id = documents.id", "left"];
        $query["join"][] = ["approval_signature_documents", "approval_signature_documents.document_id = documents.id", "left"];
        $query["where"][] = ["documents.module_record_id", $contract_id];
        $query["where"][] = ["documents.module", "contract"];
        $query["where"][] = ["approval_signature_documents." . $type, 1];
        $query["where"][] = ["documents.module", $this->modelName];
        $query["where"][] = ["documents.type", "file"];
        $query["where"][] = ["documents.visible", "1"];
        if ($ext) {
            $query["where"][] = ["documents.extension", $ext];
        }
        $response = $this->load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_contract_docs_to_approve($contract_id)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system AS documents";
        $query["select"] = ["documents.id as document_id, concat(documents.name,'.',documents.extension) AS full_name, documents.comment as keyword"];
        $query["where"][] = ["documents.module_record_id", $contract_id];
        $query["where"][] = ["documents.module", $this->modelName];
        $query["where"][] = ["documents.type", "file"];
        $query["where"][] = ["documents.visible", "1"];
        $response["docs"] = $this->load_all($query);
        if (!empty($response)) {
            $query["select"] = ["Max(documents.createdOn) as max_created_on"];
            $created_on = $this->load($query);
            $query["select"] = ["documents.id as document_id"];
            $query["where"][] = ["documents.createdOn", $created_on["max_created_on"]];
            $response["latest_document_id"] = $this->load($query)["document_id"];
        }
        $this->_table = $_table;
        return $response;
    }
    public function load_contract_docs_list($contract_id, $lineage, $visible_in_cp = false)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system AS documents";
        $query["select"] = ["documents.id as document_id, CONCAT(documents.name,'.',documents.extension) AS full_name"];
        $query["where"][] = ["documents.module_record_id", $contract_id];
        $query["where"][] = ["documents.module", $this->modelName];
        $query["where"][] = ["documents.type", "file"];
        $query["where"][] = ["documents.visible", "1"];
        if (empty($lineage)) {
            if ($lineage === "") {
                $parent = $this->ci->dms->get_container("contract", $contract_id);
                $query["where"][] = ["documents.parent", $parent["id"]];
            }
        } else {
            $lineage_arr = explode(DIRECTORY_SEPARATOR, $lineage);
            $parent_id = count($lineage_arr) - 1;
            $query["where"][] = ["documents.parent", $lineage_arr[$parent_id]];
        }
        if ($visible_in_cp) {
            $query["where"][] = ["documents.visible_in_cp", "1"];
        }
        $response = $this->load_list($query, ["key" => "document_id", "value" => "full_name", "firstLine" => ["" => $this->ci->lang->line("none")]]);
        $this->_table = $_table;
        return $response;
    }
    public function assignee_field_value()
    {
        return "concat(`up`.firstName, ' ', `up`.`lastName`)";
    }
    public function requester_field_value()
    {
        return "concat(`requester`.`firstName`, ' ', `requester`.`lastName`)";
    }
    public function amended_field_value()
    {
        return "amended.name";
    }
    public function parties_field_value()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        return "(SELECT GROUP_CONCAT(CASE WHEN `contract_party`.`party_member_type` IS NULL THEN NULL                                                        ELSE (CASE WHEN `party_category_language`.`name` != '' THEN (CASE WHEN `contract_party`.`party_member_type` = 'company'                                                                                                                     THEN CONCAT(`party_company`.`name`, ' - ', `party_category_language`.`name`)                                                              ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT( `party_contact`.`firstName`, ' ', `party_contact`.`father`, ' ', `party_contact`.`lastName`,  ' - ', `party_category_language`.`name`)                                                                    ELSE CONCAT(  `party_contact`.`firstName`,  ' ', `party_contact`.`lastName`, ' - ', `party_category_language`.`name`)                                                                    END)                                                             END)                                                       ELSE (CASE WHEN `contract_party`.`party_member_type` = 'company' THEN `party_company`.`name`                                                             ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT(`party_contact`.`firstName`,   ' ', `party_contact`.`father`,  ' ',  `party_contact`.`lastName`)                                                                   ELSE CONCAT( `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`)                                                                   END)                                                             END)                                                       END)                                                   END                                    order by `contract_party`.`contract_id` ASC SEPARATOR ', ')                                   FROM `contract_party`                                              LEFT JOIN `party` ON `party`.`id` = `contract_party`.`party_id`                                              LEFT JOIN `companies` AS `party_company`                                                        ON `party_company`.`id` = `party`.`company_id` AND                                                           `contract_party`.`party_member_type` = 'company'                                              LEFT JOIN `contacts` AS `party_contact`                                                        ON `party_contact`.`id` = `party`.`contact_id` AND                                                           `contract_party`.`party_member_type` = 'contact'                                              LEFT JOIN `party_category_language`                                                        ON `party_category_language`.`category_id` =                                                           `contract_party`.`party_category_id` and                                                           `party_category_language`.`language_id` = '" . $lang_id . "' WHERE `contract_party`.`contract_id` = `contract`.`id`)";
    }
    public function load_requester_manager($requester_id)
    {
        $_table = $this->_table;
        $this->_table = "contacts";
        $query["select"] = ["manager.id, CONCAT(manager.firstName, ' ', manager.lastName) as name"];
        $query["join"][] = ["user_profiles manager", "manager.user_id = contacts.manager_id", "left"];
        $query["where"][] = ["contacts.id", $requester_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_expiring_contracts_this_month()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.priority, contract.name, concat( '" . $this->modelCode . "', contract.id ) as contract_id, type.name as type, contract.end_date", false];
        $query["join"] = [["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["MONTH(contract.end_date) = MONTH(CURRENT_DATE())", NULL, false], ["YEAR(contract.end_date) = YEAR(CURRENT_DATE())", NULL, false], ["contract.archived = 'no' "]];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function load_expiring_contracts_this_quarter()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.priority, contract.name, concat( '" . $this->modelCode . "', contract.id ) as contract_id, type.name as type, contract.end_date", false];
        $query["join"] = [["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["QUARTER(contract.end_date) = QUARTER(CURRENT_DATE())", NULL, false], ["YEAR(contract.end_date) = YEAR(CURRENT_DATE())", NULL, false], ["contract.archived = 'no' "]];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '1' OR(contract.private = '0' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function load_expiring_contracts_next_quarter()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.priority, contract.name, concat( '" . $this->modelCode . "', contract.id ) as contract_id, type.name as type, contract.end_date", false];
        $query["join"] = [["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["QUARTER(contract.end_date) = QUARTER(DATE_ADD(CURRENT_DATE(), INTERVAL 3 MONTH))", NULL, false], ["YEAR(contract.end_date) = YEAR(DATE_ADD(CURRENT_DATE(), INTERVAL 3 MONTH))", NULL, false], ["contract.archived = 'no' "]];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '1' OR(contract.private = '0' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function load_received_contracts_this_month()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.priority, contract.name, concat( '" . $this->modelCode . "', contract.id ) as contract_id, type.name as type, status.name as status, status_category.type as status_category", false];
        $query["join"] = [["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["MONTH(contract.contract_date) = MONTH(CURRENT_DATE())", NULL, false], ["YEAR(contract.contract_date) = YEAR(CURRENT_DATE())", NULL, false], ["contract.archived = 'no' "]];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '1' OR(contract.private = '0' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function load_contracts_per_status($filters)
    {
        $this->ci->load->model("contract_workflow", "contract_workflowfactory");
        $this->ci->contract_workflow = $this->ci->contract_workflowfactory->get_instance();
        if (0 < $filters["type"]) {
            $workflow = $this->ci->contract_workflow->load_contract_workflow_per_types([$filters["type"]]);
            $workflow_statuses = $this->ci->contract_workflow->load_all_statuses_per_workflow($workflow["workflow_id"] ?? 1);
        } else {
            $this->ci->load->model("contract_status_language", "contract_status_languagefactory");
            $this->ci->contract_status_language = $this->ci->contract_status_languagefactory->get_instance();
            $workflow_statuses = $this->ci->contract_status_language->load_all_per_language();
        }
        $response["statuses"] = [];
        $response["values"] = [];
        foreach ($workflow_statuses as $status) {
            $query = [];
            $query["select"] = ["COUNT(contract.id) as contracts", false];
            $query["where"] = [["contract.archived", "no"]];
            $query["where"][] = ["contract.status_id", $status["id"]];
            if (0 < $filters["type"]) {
                $query["where"][] = ["contract.type_id", $filters["type"]];
                $query["where"][] = ["contract.workflow_id", $workflow["workflow_id"] ?? 1];
            }
            if (0 < $filters["year"]) {
                $query["where"][] = ["YEAR(contract.createdOn)", $filters["year"]];
            }
            $data = $this->load($query);
            if (0 < $data["contracts"]) {
                $response["statuses"][] = $status["name"];
                $response["values"][] = (int) $data["contracts"];
            }
        }
        return $response;
    }
    public function load_contracts_per_department($filters)
    {
        $query = [];
        $query["select"] = [
            "COUNT(contract.id) as contracts,departments.id as department_id,  departments.name as department_name",
            false
        ];

        $query["join"] = [
            ["departments", "departments.id = contract.department_id", "left"]
        ];

        $query["where"] = [["contract.archived", "no"]];
        $query["group_by"] = ["departments.id"];

        // Apply filters
        if (!empty($filters["year"]) && $filters["year"] > 0) {
            $query["where"][] = ["YEAR(contract.createdOn)", $filters["year"]];
        }

        if (!empty($filters["type"]) && $filters["type"] > 0) {
            $query["where"][] = ["contract.type_id", $filters["type"]];
        }

        $data = $this->load_all($query);

        $response["indexes"] = [];
        $response["values"] = [];

        foreach ($data as $val) {
            $response["indexes"][] = $val["department_name"] ?? 'Unassigned';
            $response["values"][] = (int) $val["contracts"];
        }

        return $response;
    }
    public function load_contracts_per_party($filters)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["COUNT(contract.id) as contracts, contract_party.party_id, contract_party.party_member_type AS party_member_type, contract_party.party_category_id, party_category_language.name as party_category_name, (CASE WHEN contract_party.party_member_type = 'company' THEN comp.name ELSE CASE WHEN con.father!=' ' THEN concat_ws(' ',con.firstName,con.father,con.lastName) ELSE concat_ws(' ',con.firstName,con.lastName) END END ) AS party_name,(CASE WHEN contract_party.party_member_type = 'company' THEN party.company_id ELSE party.contact_id END ) AS party_member_id,", false];
        $query["join"] = [["contract_party", "contract_party.id = (SELECT dd.id FROM contract_party dd  WHERE contract.id = dd.contract_id LIMIT 1)", "inner"], ["party", "party.id = contract_party.party_id", "left"], ["companies comp", "comp.id = party.company_id and contract_party.party_member_type = 'company'", "left"], ["contacts con", "con.id = party.contact_id and contract_party.party_member_type = 'contact'", "left"], ["party_category_language", "party_category_language.category_id = contract_party.party_category_id and party_category_language.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["contract.archived", "no"]];
        $query["group_by"] = ["contract_party.party_id"];
        if (0 < $filters["year"]) {
            $query["where"][] = ["YEAR(contract.createdOn)", $filters["year"]];
        }
        if (0 < $filters["type"]) {
            $query["where"][] = ["contract.type_id", $filters["type"]];
        }
        $data = $this->load_all($query);
        $response["indexes"] = [];
        $response["values"] = [];
        foreach ($data as $val) {
            $response["indexes"][] = $val["party_name"];
            $response["values"][] = (int) $val["contracts"];
        }
        return $response;
    }
    public function load_contracts_per_value()
    {
        $response = [];
        $query = [];
        $query["select"] = ["SUM(contract.value) as value, iso_currencies.code", false];
        $query["join"] = ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"];
        $query["group_by"] = ["contract.currency_id"];
        $query["where"] = [["contract.archived", "no"]];
        $query["where"][] = ["YEAR(contract.createdOn) = YEAR(CURRENT_DATE())", NULL, false];
        $query["where"][] = ["contract.currency_id IS NOT NULL", NULL, false];
        $data = $this->load_all($query);
        $response["x_axis"] = [];
        $response["y_axis"] = [];
        if (!empty($data)) {
            foreach ($data as $val) {
                $response["x_axis"][] = $val["code"];
                $response["y_axis"][] = (int) $val["value"];
            }
        }
        return $response;
    }

    public function load_contracts_per_month()
    {
        $response = [];
        $query = [];
        $query["select"] = ["SUM(contract.value) as value, MONTH(contract.createdOn) AS month", false];
        $query["group_by"] = ["MONTH(contract.createdOn)"];
        $query["order_by"] = ["MONTH(contract.createdOn) ASC"];
        $query["where"] = [["contract.archived", "no"]];
        $query["where"][] = ["YEAR(contract.createdOn) = YEAR(CURRENT_DATE())", NULL, false];
        $data = $this->load_all($query);

        // Initialize arrays for all 12 months
        $monthlyValues = array_fill(1, 12, 0);
        $monthLabels = [
            1 => $this->ci->lang->line('january'),
            2 => $this->ci->lang->line('february'),
            3 => $this->ci->lang->line('march'),
            4 => $this->ci->lang->line('april'),
            5 => $this->ci->lang->line('may'),
            6 => $this->ci->lang->line('june'),
            7 => $this->ci->lang->line('july'),
            8 => $this->ci->lang->line('august'),
            9 => $this->ci->lang->line('september'),
            10 => $this->ci->lang->line('october'),
            11 => $this->ci->lang->line('november'),
            12 => $this->ci->lang->line('december'),
        ];

        $response["x_axis"] = [];
        $response["y_axis"] = [];

        if (!empty($data)) {
            foreach ($data as $val) {
                $month = (int) $val["month"];
                $monthlyValues[$month] = (int) $val["value"];

            }
        }

        // Populate the response array with month labels and values
        foreach ($monthLabels as $month => $label) {
            $response["x_axis"][] = $label;
            $response["y_axis"][] = $monthlyValues[$month];

        }

        return $response;
    }
    public function lookup($term)
    {
        $query["select"][] = ["contract.*, CONCAT('" . $this->get("modelCode") . "', contract.id) as contract_id", false];
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];
        if (!empty($term)) {
            $modelCode = substr($term, 0, 2);
            $contract_id = substr($term, 2);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($contract_id)) {
                $query["where"][] = ["contract.id = " . $contract_id, NULL, false];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $query["where"][] = ["contract.name LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($more_filters = $this->ci->input->get("more_filters")) {
            foreach ($more_filters as $_field => $_term) {
                $query["where"][] = [$_field, $_term];
            }
        }
        return $this->load_all($query);
    }
    public function lookup_approvers_signees($term, $lookup_field)
    {
        $table = $this->_table;
        $this->_table = "users";
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $query = ["select" => ["users.id, us.firstName, us.lastName, 'User' as type", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->ci->user->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->ci->user->superAdminInfosystaUserGroupId], ["us.status", "Active"], ["CONCAT(us.firstName, ' ', us.lastName) LIKE '%" . $term . "%'", NULL, false]]];
        $users_list = $this->load_all($query);
        $this->_table = "customer_portal_users";
        $query = ["select" => ["customer_portal_users.id, customer_portal_users.firstName, customer_portal_users.lastName, 'Collaborator' as type", false], "where" => [["customer_portal_users.status", "Active"], ["(customer_portal_users.type = 'collaborator' OR customer_portal_users.type = 'both')", NULL, false], ["CONCAT(customer_portal_users.firstName, ' ', customer_portal_users.lastName) LIKE '%" . $term . "%'", NULL, false]]];
        $collaborators_list = $this->load_all($query);
        $data = array_merge($users_list, $collaborators_list);
        if ($lookup_field == "approver") {
            $this->_table = "contacts";
            $query = ["select" => ["id, contacts.firstName, contacts.lastName, 'Contact' as type", false], "where" => ["(contacts.private IS NULL OR contacts.private = 'no' OR (contacts.private = 'yes' AND (contacts.createdBy = '" . $this->logged_user_id . "' OR contacts.id IN (SELECT contact_id FROM contact_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'))) AND CONCAT(contacts.firstName, ' ', contacts.lastName) LIKE '%" . $term . "%'", NULL, false]];
            $contacts_list = $this->load_all($query);
            $data = array_merge($data, $contacts_list);
        }
        $this->_table = $table;
        return $data;
    }
    public function api_lookup($term, $user_id, $override_privacy)
    {
        $query["select"][] = ["contract.id, CONCAT('" . $this->get("modelCode") . "', contract.id) as contract_id, contract.name", false];
        $query["where"][] = ["('" . $override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $user_id . "' OR contract.assignee_id = '" . $user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $user_id . "'))))", NULL, false];
        if (!empty($term)) {
            $modelCode = substr($term, 0, 2);
            $contract_id = substr($term, 2);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($contract_id)) {
                $query["where"][] = ["contract.id = " . $contract_id, NULL, false];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $query["where"][] = ["contract.name LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($more_filters = $this->ci->input->get("more_filters")) {
            foreach ($more_filters as $_field => $_term) {
                $query["where"][] = [$_field, $_term];
            }
        }
        return $this->load_all($query);
    }
    public function load_watchers_users($contract_id)
    {
        $users = [];
        $data = [];
        $status = [];
        $results = [];
        if ($contract_id < 1) {
            return $users;
        }
        $results = $this->ci->db->select(["UP.user_id as id, CONCAT( UP.firstName, ' ', UP.lastName ) as name,UP.status as status", false])->join("user_profiles UP", "UP.user_id = contract_users.user_id", "inner")->where("contract_users.contract_id", $contract_id)->get("contract_users");
        if (!$results->num_rows()) {
            return [];
        }
        foreach ($results->result() as $user) {
            $users[$user->id] = $user->name;
            $status[$user->id] = $user->status;
        }
        $data[0] = $users;
        $data[1] = $status;
        return $data;
    }
    public function send_notifications($trigger, $users = [], $extra_data = [], $channel = NULL)
    {
        $channel = $channel ?: $this->web_channel;
        $contributors = $users["contributors"];
        $contract_id = $extra_data["id"];
        $this->fetch($contract_id);
        $contract_data = ["priority" => $this->get_field("priority"), "contract_date" => $this->get_field("contract_date"), "end_date" => $this->get_field("end_date"), "description" => nl2br($this->get_field("description")), "name" => $this->get_field("name"), "type_id" => $this->get_field("type_id"), "assignee_id" => $this->get_field("assignee_id")];
        $contract_data = array_merge($contract_data, $extra_data);
        unset($contract_data["id"]);
        $this->fetch($contract_id);
        $assignee = $this->get_field("assignee_id");
        $to_ids = [];
        if ($assignee != NULL) {
            $assignee_id = str_pad($assignee, 10, "0", STR_PAD_LEFT);
            $to_ids = [$assignee_id];
        }
        if (!empty($contributors) && $trigger != "add_contract_inform_assignee") {
            $to_ids = array_merge($to_ids, $contributors);
        }
        $this->ci->load->library("system_notification");
        $this->ci->load->library("email_notifications");
        $this->ci->load->model("email_notification_scheme");
        $assignee_profile_name = $this->ci->email_notification_scheme->get_user_full_name($assignee);
        $notificationsData = ["toIds" => array_unique($to_ids), "object" => $trigger, "object_id" => (int) $contract_id, "objectModelCode" => $this->get("modelCode"), "targetUser" => $assignee, "contract_data" => $contract_data];
        $model = $this->get("_table");
        $model_data["id"] = $contract_id;
        $model_data["contributors_ids"] = $contributors;
        $notifications_emails = $this->ci->email_notification_scheme->get_emails($trigger, $model, $model_data);
        extract($notifications_emails);
        $this->ci->load->model("contract_type_language", "contract_type_languagefactory");
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $this->ci->contract_type_language = $this->ci->contract_type_languagefactory->get_instance();
        $this->ci->contract_type_language->fetch(["type_id" => $this->get_field("type_id"), "language_id" => $lang_id]);
        $notificationsData["to"] = array_filter($to_emails);
        $notificationsData["cc"] = array_filter($cc_emails);
        $notificationsData["fromLoggedUser"] = $users["logged_in_user"];
        $notificationsData["contract_data"]["modifiedOn"] = $this->get_field("modifiedOn");
        $notificationsData["contract_data"]["createdOn"] = $this->get_field("createdOn");
        $notificationsData["contract_data"]["name"] = $this->get_field("name");
        $notificationsData["contract_data"]["type"] = $this->ci->contract_type_language->get_field("name");
        $notificationsData["contract_data"]["contract_date"] = $this->get_field("contract_date");
        $notificationsData["contract_data"]["end_date"] = $this->get_field("end_date");
        $notificationsData["contract_data"]["priority"] = $this->get_field("priority");
        $notificationsData["contract_data"]["assignee"] = $assignee_profile_name;
        $this->ci->system_notification->notification_add($notificationsData);
        $this->ci->email_notifications->notify($notificationsData);
    }
    public function get_document_generator_data($id)
    {
        return $this->load_contract_details($id);
    }
    public function save_contract_from_template($channel = "", $logged_user = "",$cp_contact_id=0)
    {
        $channel = $channel ?: $this->web_channel;
        $logged_user = $logged_user ?: $this->ci->session->userdata("AUTH_user_id");
        $post_data = $this->ci->input->post("template", true);
        $post_data["category"]=$this->ci->input->post("category");
        $template_id = $post_data["id"] ?? false;
        $file_name = $post_data["name"];
        $this->ci->load->model("document_management_system", "document_management_systemfactory");
        $this->ci->document_management_system = $this->ci->document_management_systemfactory->get_instance();
        $this->ci->load->model("contract_template", "contract_templatefactory");
        $this->ci->contract_template = $this->ci->contract_templatefactory->get_instance();
        $response["result"] = false;
        if ($template_id && $this->ci->contract_template->fetch($template_id)) {
            $document_id = $this->ci->contract_template->get_field("document_id");
            $template_details = $this->ci->contract_template->get_fields();
            if ($document_id && $this->ci->document_management_system->fetch(["id" => $document_id]) && isset($post_data["variable_id"]) && !empty($post_data["variable_id"])) {
                $result = $this->insert_contract_from_template($template_details, $post_data, $channel, $logged_user,$cp_contact_id);
                if ($result["result"]) {
                    $template_record = $this->ci->document_management_system->get_fields();
                    $response = $this->ci->dmsnew->generate_document_from_questionnaire($template_record, $result["variables_details"], $result["contract_details"]);
                    $response["id"] = $result["contract_details"]["id"];
                    $response["model_code"] = "CT";
                    $response["result"] = true;
                    $this->inject_folder_templates($response["id"], "contract", $this->get_field("type_id"));
                    return $response;
                }
                return $result;
            }
        }
        return $response;
    }
    private function insert_contract_from_template($template_details, $post_data, $channel, $logged_user,$cp_contact_id=0)
    {
        $response = [];
        $this->ci->load->model("contract_template_variable", "contract_template_variablefactory");
        $this->ci->contract_template_variable = $this->ci->contract_template_variablefactory->get_instance();
        $contract_fields = $this->ci->contract_template->contract_fields;
        $post_data["category"]=$post_data["category"];
        foreach ($post_data["variable_id"] as $id) {
            $variable = $post_data["variable_value"][$id] ?? "";
            if (is_array($variable)) {
                $variable = implode(" & ", $variable);
            }
            $this->ci->contract_template_variable->reset_fields();
            if ($this->ci->contract_template_variable->fetch($id)) {
                $variable_data = $this->ci->contract_template_variable->get_fields();
                if ($variable_data["variable_property"] == "contract_field") {
                    $field_details = $contract_fields[$variable_data["property_details"]];
                    if ($variable) {
                        $extra_data = $post_data["extra_data"] ?? false;
                        switch ($field_details["group"]) {
                            case "main":
                                $this->ci->contract->set_field($contract_fields[$variable_data["property_details"]]["db_key"], $variable);
                                switch ($field_details["type"]) {
                                    case "list":
                                        $list_values = $this->ci->contract_template->{$variable_data["property_details"] . "_load_list"}();
                                        $variables_details[$variable_data["name"]] = $variable ? $list_values[$variable] : "";
                                        break;
                                    case "single_lookup":
                                        if (isset($extra_data[$id]["name"])) {
                                            $variables_details[$variable_data["name"]] = $extra_data[$id]["name"];
                                        }
                                        break;
                                    case "multiple_fields_per_type":
                                        $list_values = $this->ci->contract_template->{$variable_data["property_details"] . "_load_list"}();
                                        $variables_details[$variable_data["name"]] = $variable ? $list_values[$variable] : "";
                                        break;
                                    default:
                                        $variables_details[$variable_data["name"]] = $variable;
                                }
                                break;
                            case "multiple_records":
                                if ($extra_data[$id]) {
                                    $multiple_records["types"][] = $extra_data[$id]["type"];
                                    $multiple_records["ids"][] = $variable;
                                    $variables_details[$variable_data["name"]] = $extra_data[$id]["name"];
                                }
                                break;
                        }
                    } else {
                        $variables_details[$variable_data["name"]] = "";
                    }
                } else {
                    $variables_details[$variable_data["name"]] = $variable;
                }
            }
        }
        $this->ci->contract->set_field("type_id", $template_details["type_id"]);
        $workflow_applicable = $this->ci->contract_status->load_workflow_status_per_type($template_details["type_id"]);
        if (empty($workflow_applicable)) {
            $workflow_applicable = $this->ci->contract_status->load_system_workflow_status();
        }
        $this->ci->contract->set_field("status_id", $workflow_applicable["status_id"] ?? "1");
        $this->ci->contract->set_field("workflow_id", $workflow_applicable["workflow_id"] ?? "1");
        $this->ci->load->model(["provider_group"]);
        $this->ci->provider_group->fetch(["allUsers" => 1]);
        $this->ci->contract->set_field("assigned_team_id", $this->ci->provider_group->get_field("id"));
        $required_fields = $this->ci->input->post("required_fields", true);
        $this->ci->contract->set_field("name", $required_fields["name"] ?? $this->ci->contract->get_field("name"));
        $this->ci->contract->set_field("contract_date", $this->ci->contract->get_field("contract_date") ?? date("Y-m-d"));
        $this->ci->contract->set_field("end_date", $required_fields["end_date"] ?? $this->ci->contract->get_field("end_date"));
        $this->ci->contract->set_field("priority", $this->ci->contract->get_field("priority") ?: "medium");
        $this->ci->contract->set_field("channel", $channel);
        if ($channel == $this->cp_channel) {
            $this->ci->contract->set_field("visible_to_cp", 1);
            $this->ci->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->ci->customer_portal_users = $this->ci->customer_portal_usersfactory->get_instance();
            //check if requester id is set
           $requester_id= $required_fields["requester_id"]??$cp_contact_id;
           if ($requester_id<1) { //if requester_id is 0
               $this->ci->customer_portal_users->fetch($required_fields["requester_id"] ?? $this->ci->contract->get_field("requester_id"));
              $requester_id = $this->ci->customer_portal_users->add_cp_user_as_contact(true, false, $this->ci->customer_portal_users->get_field("email"));
           }
            $this->ci->contract->set_field("requester_id",$requester_id);
        } else {
            $this->ci->contract->set_field("requester_id", $required_fields["requester_id"] ?? $this->ci->contract->get_field("requester_id"));
        }
        $this->ci->contract->set_field("modifiedByChannel", $channel);
        $this->ci->contract->set_field("createdOn", date("Y-m-d H:i:s", time()));
        $this->ci->contract->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
        $this->ci->contract->set_field("createdBy", $logged_user);
        $this->ci->contract->set_field("modifiedBy", $logged_user);
        $this->ci->contract->set_field("status", "Active");
        $this->ci->contract->set_field("archived", "no");
        $this->ci->contract->set_field("category",$post_data["category"]);
        $this->ci->contract->set_field("stage",$post_data["stage"]??"Development");
        $this->ci->contract->disable_builtin_logs();
        if ($this->ci->contract->validate()) {
            $notify_before = $this->ci->input->post("notify_me_before");
            $end_date = $this->ci->input->post("required_fields[end_date]");
            if ($notify_before && $end_date && (!$notify_before["time"] || !$notify_before["time_type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                if ($is_not_nb) {
                    $response["result"] = false;
                    $response["validationErrors"]["notify_before"] = sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("notify_before"));
                } else {
                    $response["result"] = false;
                    $response["validationErrors"]["notify_before"] = $this->ci->lang->line("cannot_be_blank_rule");
                }
            }
            $this->ci->contract->insert();
            $contract_data = $this->ci->contract->get_fields();
            $contract_id = $this->ci->contract->get_field("id");
            if (isset($multiple_records) && !empty($multiple_records)) {
                $this->ci->load->model("party");
                $parties_data = $this->ci->party->return_parties($multiple_records["types"], $multiple_records["ids"]);
                if (!empty($parties_data)) {
                    foreach ($parties_data as $key => $value) {
                        $parties_data[$key]["contract_id"] = $contract_id;
                    }
                    $this->ci->contract_party->insert_contract_parties($contract_id, $parties_data);
                }
                $this->feed_related_contracts_to_parties($multiple_records["types"], $multiple_records["ids"], $contract_id);
                $contract_data["party_member_type"] = $multiple_records["types"];
                $contract_data["party_member_id"] = $multiple_records["ids"];
            }
            $this->feed_related_contracts_to_requester($this->ci->contract->get_field("requester_id"), $contract_id);
            $this->ci->load->model("approval", "approvalfactory");
            $this->ci->approval = $this->ci->approvalfactory->get_instance();
            $this->ci->load->model("signature", "signaturefactory");
            $this->ci->signature = $this->ci->signaturefactory->get_instance();
            $this->ci->load->model("contract_sla_management", "contract_sla_managementfactory");
            $this->ci->contract_sla_management = $this->ci->contract_sla_managementfactory->get_instance();
            $this->ci->contract_sla_management->contract_sla($contract_id, $logged_user, $channel);
            if (!$this->ci->contract_approval_submission->fetch(["contract_id" => $contract_id])) {
                $data["approval_center"] = $this->ci->approval->update_approval_contract($contract_data);
            }
            if (!$this->ci->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                $data["signature_center"] = $this->ci->signature->update_signature_contract($contract_data);
            }
            if ($this->ci->system_preference->get_values()["webhooks_enabled"] == 1) {
                $data = $this->load_contract_details($contract_id);
                $this->trigger_web_hook("contract_created", $data);
            }
            if (isset($response["validationErrors"]) && !empty($response["validationErrors"])) {
                $response["result"] = false;
            } else {
                $response["result"] = true;
            }
            $response["variables_details"] = $variables_details;
            $this->ci->contract->fetch($contract_id);
            $response["contract_details"] = $this->ci->contract->get_fields();
        } else {
            $response["result"] = false;
            $response["validationErrors"] = $this->ci->contract->get("validationErrors");
        }
        return $response;
    }
    public function feed_related_contracts_to_requester($requester, $contract_id)
    {
        if (!$requester) {
            return true;
        }
        $this->ci->load->model("contacts_related_contract");
        $data = ["contact_id" => $requester, "contract_id" => $contract_id];
        $is_requester_relation_exists = $this->ci->contacts_related_contract->fetch($data);
        if ($is_requester_relation_exists) {
            return true;
        }
        $this->ci->contacts_related_contract->reset_fields();
        $this->ci->contacts_related_contract->set_fields($data);
        $this->ci->contacts_related_contract->insert();
    }
    public function feed_related_contracts_to_parties($parties_member_types, $parties_member_ids, $contract_id)
    {
        $this->ci->load->model("contacts_related_contract");
        $this->ci->load->model("companies_related_contract");
        for ($opp = 0; $opp < count($parties_member_types); $opp++) {
            $party_id = $parties_member_ids[$opp];
            if ($party_id) {
                if (!strcmp($parties_member_types[$opp], "contact")) {
                    $data = ["contact_id" => $party_id, "contract_id" => $contract_id];
                    $party_contact_relation_exists = $this->ci->contacts_related_contract->fetch($data);
                    if (!$party_contact_relation_exists) {
                        $this->ci->contacts_related_contract->reset_fields();
                        $this->ci->contacts_related_contract->set_fields($data);
                        $this->ci->contacts_related_contract->insert();
                        $this->ci->contacts_related_contract->reset_fields();
                    }
                }
                if (!strcmp($parties_member_types[$opp], "company")) {
                    $data = ["company_id" => $party_id, "contract_id" => $contract_id];
                    $party_company_relation_exists = $this->ci->companies_related_contract->fetch($data);
                    if (!$party_company_relation_exists) {
                        $this->ci->companies_related_contract->reset_fields();
                        $this->ci->companies_related_contract->set_fields($data);
                        $this->ci->companies_related_contract->insert();
                        $this->ci->companies_related_contract->reset_fields();
                    }
                }
            }
        }
    }
    public function dashboard_recent_contracts($category = "contracts", $api_params = [])
    {
        $logged_user_id = $api_params["user_id"] ?? $this->logged_user_id;
        $this->ci->load->model("user_preference");
        $recent_contracts = unserialize($this->ci->user_preference->get_value_by_user("recent_cases", $logged_user_id));
        $response = [];
        if (isset($recent_contracts[$category])) {
            $recent_contracts = $recent_contracts[$category];
            foreach ($recent_contracts as $key => $val) {
                if ($val == 0) {
                    unset($recent_contracts[$key]);
                }
            }
            $recent_contracts = implode(",", array_map("intval", $recent_contracts));
            if (!empty($recent_contracts)) {
                $query["select"][] = ["contract.*,  concat('" . $this->get("modelCode") . "', contract.id) AS contract_id, '" . $category . "' AS module", false];
                $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.modifiedBy = '" . $logged_user_id . "' OR contract.assignee_id = '" . $logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
                $query["where"][] = ["contract.id IN (" . $recent_contracts . ")", NULL, false];
                $query["order_by"] = ["FIELD(contract.id, " . $recent_contracts . ")"];
                $response = $this->load_all($query);
            }
        }
        return $response;
    }
    public function archived_contracts_total_number($contract_status_ids = false, $filter = false, $update = false, $hide = false)
    {
        $system_preferences = $this->ci->session->userdata("systemPreferences");
        $ids = $contract_status_ids ? $contract_status_ids : $system_preferences["archiveContractStatus"];
        $query = [];
        $query["select"] = ["contract.id"];
        $where_condition = $hide ? "contract.hideFromBoard IS NULL" : "contract.archived = 'no'";
        $query["where"] = [["contract.status_id IN ( " . $ids . ")"], [$where_condition]];
        if (isset($filter["filter"]) && is_array($filter["filter"]) && isset($filter["filter"]["filters"])) {
            foreach ($filter["filter"]["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["filter"]["logic"]);
            }
            unset($_filter);
        }
        $this->prep_query($query);
        if (!$update) {
            $total_rows = $this->ci->db->get($this->_table)->num_rows();
        } else {
            $update_condition = $filter["archiving_type"] === "archive" ? "archived" : "hideFromBoard";
            $total_rows = $this->ci->db->update($this->_table, [$update_condition => "yes"]);
        }
        return $total_rows;
    }
    public function get_contract_grid_query_web($filter, $sortable, $return_query = false)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $response = $this->k_load_all($filter, $sortable, $return_query);
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    public function assignee_user_id_field_value()
    {
        return "`up`.`user_id`";
    }
    public function api_load_all_contracts($logged_in_user, $override_privacy, $lang_id)
    {
        $query = [];
        $this->ci->load->model("language");
        $this->ci->db->query("set optimizer_switch = 'block_nested_loop=off'");
        $query["select"][] = [" SQL_CALC_FOUND_ROWS `contract`.`id`    AS `id`,\r\n                                 
          `contract`.`name`  AS `name`,\r\n                      
                       `provider_groups`.`name`    AS `assigned_team`,\r\n   `contract`.`description` AS `description`,\r\n      
                                                `contract`.`priority`    AS `priority`,\r\n                                   `contract`.`status`    AS `contract_status`,\r\n                                   `contract`.`contract_date` AS `contract_date`,\r\n                                   `contract`.`start_date`      AS `start_date`,\r\n                                   `contract`.`end_date`     AS `end_date`,\r\n                                   `contract`.`status_comments`       AS `status_comments`,\r\n                                   `contract`.`value`   AS `value`,\r\n                                   contract.amendment_of as amendment_of,\r\n                                   `contract`.`archived`  AS `archived`,\r\n                                   amended.name as amendment_of_name,\r\n                                   `contract`.`reference_number`    AS `reference_number`,\r\n                                   `contract`.`private`     AS `private`,\r\n                                   `contract`.`channel`,\r\n                                   `contract`.`visible_to_cp`, countries_languages.name as country, applicable_law.name as applicable_law,\r\n                                   concat('" . $this->get("modelCode") . "', `contract`.`id`)      AS `contract_id`,                                   `status`.`name`    AS `status`, contract.renewal_type as renewal,                                   (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) AS `requester`,                                   `requester`.`status`    AS `requester_status`,                                   concat(`up`.`firstName`, ' ', `up`.`lastName`) AS `assignee`,                                   `type`.`name`   AS `type`,                                   `up`.`status`    AS `userStatus`,                                   `contract`.`createdOn`   AS `createdOn`,    `contract`.`createdBy`   AS `createdBy`,                                   (CASE WHEN contract.channel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles\r\n                             WHERE user_profiles.user_id = contract.createdBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')\r\n                             FROM customer_portal_users WHERE customer_portal_users.id = contract.createdBy) END) as createdByName,\r\n                                   `contract`.`modifiedOn`  AS `modifiedOn`,`contract`.`modifiedBy`  AS `modifiedBy`,\r\n\r\n                                   (CASE WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' THEN (SELECT CONCAT(user_profiles.firstName,' ',user_profiles.lastName) FROM user_profiles                             WHERE user_profiles.user_id = contract.modifiedBy) ELSE  (SELECT CONCAT(customer_portal_users.firstName,' ',customer_portal_users.lastName, ' (Portal User)')                             FROM customer_portal_users WHERE customer_portal_users.id = contract.modifiedBy) END) as modifiedByName,                                   iso_currencies.code as currency,                            
               (SELECT GROUP_CONCAT(CASE WHEN `contract_party`.`party_member_type` IS NULL THEN NULL                                                        ELSE (CASE WHEN `party_category_language`.`name` != '' THEN (CASE WHEN `contract_party`.`party_member_type` = 'company'                                                                                                                     THEN CONCAT(`party_company`.`name`, ' - ', `party_category_language`.`name`)                                                              ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT( `party_contact`.`firstName`, ' ', `party_contact`.`father`, ' ', `party_contact`.`lastName`,  ' - ', `party_category_language`.`name`)                                                                    ELSE CONCAT(  `party_contact`.`firstName`,  ' ', `party_contact`.`lastName`, ' - ', `party_category_language`.`name`)                                                                    END)                                                             END)                                                       ELSE (CASE WHEN `contract_party`.`party_member_type` = 'company' THEN `party_company`.`name`                                                             ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT(`party_contact`.`firstName`,   ' ', `party_contact`.`father`,  ' ',  `party_contact`.`lastName`)                                                                   ELSE CONCAT( `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`)                                                                   END)                                                             END)                                                       END)                                                   END                                    order by `contract_party`.`contract_id` ASC SEPARATOR ', ')                                   FROM `contract_party`                                              LEFT JOIN `party` ON `party`.`id` = `contract_party`.`party_id`                                              LEFT JOIN `companies` AS `party_company`                                                        ON `party_company`.`id` = `party`.`company_id` AND                                                           `contract_party`.`party_member_type` = 'company'                                              LEFT JOIN `contacts` AS `party_contact`                                                        ON `party_contact`.`id` = `party`.`contact_id` AND                                                           `contract_party`.`party_member_type` = 'contact'                                              LEFT JOIN `party_category_language`                                                        ON `party_category_language`.`category_id` =                                                           `contract_party`.`party_category_id` and                                                           `party_category_language`.`language_id` = '" . $lang_id . "'\r\n                                   WHERE `contract_party`.`contract_id` = `contract`.`id`)   AS `parties`\r\n                                   ", false];
        $query["join"] = [["`user_profiles` `up`", "`up`.`user_id` = `contract`.`assignee_id`", "left"], ["`contacts` `requester`", "`requester`.`id` = `contract`.`requester_id`", "left"], ["`provider_groups`", "`provider_groups`.`id` = `contract`.`assigned_team_id`", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status_language as status", "status.status_id = contract.status_id and status.language_id = '" . $lang_id . "'", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contract as amended", "amended.id = contract.amendment_of", "left"], ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"], ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"]];
        $query["where"][] = ["contract.archived = 'no' ", NULL, false];
        $query["where"][] = ["('" . $override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $logged_in_user . "' OR contract.assignee_id = '" . $logged_in_user . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $logged_in_user . "'))))", NULL, false];
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $this->ci->db->query("SELECT FOUND_ROWS() AS `count`")->row()->count;
        return $response;
    }
    public function inject_folder_templates($contract_id, $category, $contract_type)
    {
        $category = strtolower($category);
        $this->ci->load->model("folder_template", "folder_templatefactory");
        $this->ci->folder_template = $this->ci->folder_templatefactory->get_instance();
        $folders = $this->ci->folder_template->get_contract_folders($category, $contract_type);
        $this->ci->load->library("dms");
        $this->ci->load->model("document_management_system", "document_management_systemfactory");
        $this->ci->document_management_system = $this->ci->document_management_systemfactory->get_instance();
        foreach ($folders as $folder) {
            $this->create_folders_recursively($contract_id, $folder);
        }
    }
    public function create_folders_recursively($contract_id, $folder, $lineage = "")
    {
        if (!empty($folder)) {
            $response = $this->ci->dms->create_folder(["module" => "contract", "module_record_id" => $contract_id, "lineage" => $lineage, "name" => $folder["text"]]);
            if ($response["status"] && isset($response["id"]) && isset($folder["children"]) && !empty($folder["children"])) {
                foreach ($folder["children"] as $node) {
                    $this->ci->document_management_system->fetch($response["id"]);
                    $this->create_folders_recursively($contract_id, $node, $this->ci->document_management_system->get_field("lineage"));
                }
            }
        }
    }
    public function get_contract_privacy_conditions($logged_user_id, $override_privacy, $return_array = true)
    {
        $this->ci->load->model("system_preference");
        $condition = "((" . "'" . $override_privacy . "' = 'yes'" . " OR (" . " contract.private IS NULL" . " OR contract.private = 0" . " OR (" . " contract.private = 1" . " AND  (" . " contract.assignee_id = '" . $logged_user_id . "'" . " )" . " )" . " )" . "))";
        return $return_array ? [$condition, NULL, false] : $condition;
    }
    public function load_visible_contracts_ids($return = "array", $where = [])
    {
        $where_condition = $this->get_contract_privacy_conditions($this->logged_user_id, $this->override_privacy, false);
        $_table = $this->_table;
        $this->_table = "contract";
        $this->ci->db->select("contract.id");
        $this->ci->db->where($where_condition);
        if ($where) {
            $this->ci->db->where($where["column"] . " = " . $where["value"]);
        }
        $query = $this->ci->db->get($this->_table);
        $visible_ids = [];
        if ($query && $query->num_rows()) {
            foreach ($query->result() as $row) {
                $visible_ids[] = $row->id;
            }
        }
        $this->_table = $_table;
        return $return == "array" ? $visible_ids : "'" . implode("','", $visible_ids) . "'";
    }
    public function load_visible_contracts_per_sla($workflow_id, $logged_user_id = 0, $override_privacy = 0)
    {
        !$override_privacy ? $override_privacy = $this->override_privacy : "";
        !$logged_user_id ? $logged_user_id = $this->logged_user_id : "";
        $query = "select DISTINCT contract.id, contracts_sla.sla_management_id\r\n        from contract\r\n        inner join contracts_sla on contracts_sla.contract_id=contract.id\r\n        where (('" . $override_privacy . "' = 'yes' \r\n        OR ( contract.private IS NULL OR contract.private = 0 OR \r\n        (contract.private = 1 AND  (contract.assignee_id = '" . $logged_user_id . "'))\r\n            )))\r\n            AND contract.workflow_id ='" . $workflow_id . "'";
        return $this->ci->db->query($query)->result_array();
    }
}
class mysql_Contract extends mysqli_Contract
{
}
class sqlsrv_Contract extends mysqli_Contract
{


    public function load_data($contract_id)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"][] = ["contract.*, type.name as type, sub_type.name as sub_type,department.name as department_name,(CASE WHEN requester.father!= '' THEN (requester.firstName + ' ' + requester.father + ' ' + requester.lastName) ELSE (requester.firstName + ' ' + requester.lastName) END) as requester, (assignee.firstName + ' ' + assignee.lastName) as assignee, (authorizedSignatory.firstName + ' ' + authorizedSignatory.lastName) as authorized_signatory_name, assigned_team.name as assigned_team,\r\n         workflow.name as workflow_name, status.name as status_name, iso_currencies.code as currency, status_category.color as status_color, contract.renewal_type as renewal,\r\n         amended.name as amendment_of_name, countries_languages.name as country, applicable_law.name as applicable_law,\r\n         (CASE WHEN contract.channel != '" . $this->cp_channel . "' THEN (SELECT (user_profiles.firstName + ' ' + user_profiles.lastName) FROM user_profiles\r\n         WHERE user_profiles.user_id = contract.createdBy) ELSE  (SELECT (customer_portal_users.firstName + ' ' + customer_portal_users.lastName + ' (Portal User)')\r\n         FROM customer_portal_users WHERE customer_portal_users.id = contract.createdBy) END) as creator,\r\n         (CASE WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' THEN (SELECT (user_profiles.firstName + ' ' + user_profiles.lastName) FROM user_profiles\r\n          WHERE user_profiles.user_id = contract.modifiedBy) ELSE  (SELECT (customer_portal_users.firstName + ' ' + customer_portal_users.lastName + ' (Portal User)')\r\n          FROM customer_portal_users WHERE customer_portal_users.id = contract.modifiedBy) END) as modifier", false];
        $query["where"][] = ["contract.id", $contract_id];
        $query["join"] = [["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"], ["departments as department", "department.id = contract.department_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["user_profiles authorizedSignatory", "authorizedSignatory.user_id = contract.authorized_signatory", "left"], ["provider_groups assigned_team", "assigned_team.id = contract.assigned_team_id", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["sub_contract_type_language as sub_type", "sub_type.sub_type_id = contract.sub_type_id and sub_type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_workflow as workflow", "workflow.id = contract.workflow_id", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contract as amended", "amended.id = contract.amendment_of", "left"], ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"], ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"]];
        return $this->load($query);
    }
    public function load_contract_details($contract_id)
    {
        $table = $this->_table;
        $this->_table = "contract";
        $this->ci->load->model("language");
        $this->ci->load->model("contract", "contractfactory");
        $this->ci->contract = $this->ci->contractfactory->get_instance();
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id,\r\n                                  contract.name, status.name as status, type.name as type, contract.description,
                                          department.name as department_name,(up.firstName + ' ' + up.lastName) as assignee, contract.start_date, contract.end_date,\r\n                                  contract.contract_date, contract.value, contract.reference_number, contract.priority,\r\n                                  contract.createdOn, contract.modifiedOn, contract.archived as archived,\r\n                                  iso_currencies.code as currency,\r\n                                  (CASE WHEN requester.father!= '' THEN (requester.firstName + ' ' + requester.father + ' ' + requester.lastName) ELSE (requester.firstName + ' ' + requester.lastName) END) as requester, contract.renewal_type as renewal,\r\n                                   parties = STUFF(\r\n\t\t\t(SELECT ', ' +(CASE WHEN contract_party.party_member_type IS NULL THEN NULL\r\n                                                        ELSE (CASE WHEN party_category_language.name != '' THEN (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                                                                                 THEN (party_company.name + ' - ' + party_category_language.name)\r\n                                                              ELSE (CASE WHEN party_contact.father != '' THEN ( party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    ELSE (  party_contact.firstName + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    END)\r\n                                                             END)\r\n                                                       ELSE (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                        THEN party_company.name\r\n                                                             ELSE (CASE WHEN party_contact.father != '' THEN (party_contact.firstName + ' ' + party_contact.father + ' ' +  party_contact.lastName)\r\n                                                                   ELSE ( party_contact.firstName + ' ' +  party_contact.lastName)\r\n                                                                   END)\r\n                                                             END)\r\n                                                       END)\r\n                                                   END\r\n\t\t\t)\r\n\t\t\t FROM contract_party\r\n                                              LEFT JOIN party ON party.id = contract_party.party_id\r\n                                              LEFT JOIN companies AS party_company\r\n                                                        ON party_company.id = party.company_id AND\r\n                                                           contract_party.party_member_type = 'company'\r\n                                              LEFT JOIN contacts AS party_contact\r\n                                                        ON party_contact.id = party.contact_id AND\r\n                                                           contract_party.party_member_type = 'contact'\r\n                                              LEFT JOIN party_category_language\r\n                                                        ON party_category_language.category_id =\r\n                                                           contract_party.party_category_id and\r\n                                                           party_category_language.language_id = '" . $lang_id . "'\r\n                                   WHERE contract_party.contract_id = contract.id\r\n\t\t\tFOR XML PATH('')), 1, 1, ''), (authorizedSignatory.firstName + ' ' + authorizedSignatory.lastName) as authorized_signatory_name", false];
        $query["join"] = [["contract_party", "contract_party.contract_id = contract.id", "left"],  ["departments as department", "department.id = contract.department_id", "left"],["user_profiles authorizedSignatory", "authorizedSignatory.user_id = contract.authorized_signatory", "left"], ["party", "party.id = contract_party.party_id", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["user_profiles as up", "up.user_id = contract.assignee_id", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"]];
        $query["where"] = ["contract.id", $contract_id];
        $result = $this->load($query);
        $this->_table = $table;
        return $result;
    }
    public function k_load_all20_04_2025($filter, $sortable, $return_query = false)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"][] = [" COUNT(*) OVER() AS total_rows, contract.id,\r\n                                   contract.name  AS name,\r\n                                   provider_groups.name    AS assigned_team,\r\n                                   contract.description AS description,\r\n                                   contract.priority    AS priority,\r\n                                   contract.status    AS contract_status,\r\n                                   contract.contract_date AS contract_date,\r\n                                   contract.start_date      AS start_date,\r\n                                   contract.end_date     AS end_date,\r\n                                   contract.status_comments       AS status_comments,\r\n                                   contract.value   AS value,\r\n                                   contract.amendment_of as amendment_of,\r\n                                   amended.name as amendment_of_name,\r\n                                   contract.reference_number    AS reference_number,\r\n                                   contract.archived AS archived,\r\n                                   contract.private     AS private,\r\n                                   contract.channel,\r\n                                   contract.visible_to_cp, countries_languages.name as country, applicable_law.name as applicable_law,\r\n                                   ('" . $this->get("modelCode") . "' + CAST( contract.id AS nvarchar ))      AS contract_id,\r\n                                   status.name    AS status, contract.renewal_type as renewal,\r\n                                   (CASE WHEN requester.father!= '' THEN (requester.firstName + ' ' + requester.father + ' ' + requester.lastName) ELSE (requester.firstName + ' ' + requester.lastName) END) AS requester,\r\n                                   requester.status    AS requester_status,\r\n                                   (up.firstName + ' ' + up.lastName) AS assignee,\r\n                                   type.name   AS type,\r\n                                   up.status    AS userStatus,\r\n                                   contract.createdOn   AS createdOn,\r\n                                   contract.createdBy   AS createdBy,\r\n                                  (CASE WHEN contract.channel != '" . $this->cp_channel . "' THEN (SELECT (user_profiles.firstName + ' ' + user_profiles.lastName) FROM user_profiles\r\n                                 WHERE user_profiles.user_id = contract.createdBy) ELSE  (SELECT (customer_portal_users.firstName + ' ' + customer_portal_users.lastName + ' (Portal User)')\r\n                                 FROM customer_portal_users WHERE customer_portal_users.id = contract.createdBy) END) as createdByName,\r\n                                   contract.modifiedOn  AS modifiedOn,\r\n                                   contract.modifiedBy  AS modifiedBy,\r\n                                 (CASE WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' THEN (SELECT (user_profiles.firstName + ' ' + user_profiles.lastName) FROM user_profiles\r\n                                  WHERE user_profiles.user_id = contract.modifiedBy) ELSE  (SELECT (customer_portal_users.firstName + ' ' + customer_portal_users.lastName + ' (Portal User)')\r\n                                  FROM customer_portal_users WHERE customer_portal_users.id = contract.modifiedBy) END) as modifiedByName,\r\n                                   iso_currencies.code as currency,\r\n                                   parties = STUFF(\r\n\t\t\t(SELECT ', ' +(CASE WHEN contract_party.party_member_type IS NULL THEN NULL\r\n                                                        ELSE (CASE WHEN party_category_language.name != '' THEN (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                                                                                 THEN (party_company.name + ' - ' + party_category_language.name)\r\n                                                              ELSE (CASE WHEN party_contact.father != '' THEN ( party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    ELSE (  party_contact.firstName + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    END)\r\n                                                             END)\r\n                                                       ELSE (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                        THEN party_company.name\r\n                                                             ELSE (CASE WHEN party_contact.father != '' THEN (party_contact.firstName + ' ' + party_contact.father + ' ' +  party_contact.lastName)\r\n                                                                   ELSE ( party_contact.firstName + ' ' +  party_contact.lastName)\r\n                                                                   END)\r\n                                                             END)\r\n                                                       END)\r\n                                                   END\r\n\t\t\t)\r\n\t\t\t FROM contract_party\r\n                                              LEFT JOIN party ON party.id = contract_party.party_id\r\n                                              LEFT JOIN companies AS party_company\r\n                                                        ON party_company.id = party.company_id AND\r\n                                                           contract_party.party_member_type = 'company'\r\n                                              LEFT JOIN contacts AS party_contact\r\n                                                        ON party_contact.id = party.contact_id AND\r\n                                                           contract_party.party_member_type = 'contact'\r\n                                              LEFT JOIN party_category_language\r\n                                                        ON party_category_language.category_id =\r\n                                                           contract_party.party_category_id and\r\n                                                           party_category_language.language_id = '" . $lang_id . "'\r\n                                   WHERE contract_party.contract_id = contract.id\r\n\t\t\tFOR XML PATH('')), 1, 1, '')\r\n                                   ", false];
        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["join"] = [["user_profiles up", "up.user_id = contract.assignee_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["provider_groups", "provider_groups.id = contract.assigned_team_id", "left"], ["user_profiles created_users", "created_users.user_id = contract.createdBy", "left"], ["user_profiles modified_users", "modified_users.user_id = contract.modifiedBy", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status_language as status", "status.status_id = contract.status_id and status.language_id = '" . $lang_id . "'", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contract as amended", "amended.id = contract.amendment_of", "left"], ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"], ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"]];
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["contract.id desc"];
        }
        if ($return_query) {
            return $query;
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $query["select"][] = [$this->ci->custom_field->load_grid_custom_fields($this->modelName, $this->_table)];
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $response["data"][0]["total_rows"] ?? false;
        return $response;
    }
    public function k_load_all($filter, $sortable, $return_query = false)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = [
            "COUNT(*) OVER() AS total_rows, 
        contract.id,
        contract.name AS name,
        provider_groups.name AS assigned_team,
        contract.description AS description,
        contract.priority AS priority, 
        contract.status AS contract_status,
        contract.contract_date AS contract_date,
        contract.start_date AS start_date,
        contract.end_date AS end_date,
        contract.status_comments AS status_comments,
        contract.value AS value,
        contract.amendment_of as amendment_of,
        amended.name as amendment_of_name,
        contract.reference_number AS reference_number,
        contract.archived AS archived,
        contract.private AS private,
        contract.channel,
        contract.visible_to_cp, 
        countries_languages.name as country, 
        applicable_law.name as applicable_law,
        CONCAT('" . $this->get("modelCode") . "', CAST(contract.id AS nvarchar)) AS contract_id,
        status.name AS status, 
        contract.renewal_type as renewal,
        (CASE 
            WHEN requester.father != '' 
            THEN CONCAT(requester.firstName, ' ', requester.father, ' ', requester.lastName) 
            ELSE CONCAT(requester.firstName, ' ', requester.lastName) 
         END) AS requester,
        requester.status AS requester_status,
        CONCAT(up.firstName, ' ', up.lastName) AS assignee,
        type.name AS type,
        up.status AS userStatus,
        contract.createdOn AS createdOn,
        contract.createdBy AS createdBy,
        (CASE 
            WHEN contract.channel != '" . $this->cp_channel . "' 
            THEN (SELECT CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) 
                  FROM user_profiles
                  WHERE user_profiles.user_id = contract.createdBy) 
            ELSE (SELECT CONCAT(customer_portal_users.firstName, ' ', customer_portal_users.lastName, ' (Portal User)')
                  FROM customer_portal_users 
                  WHERE customer_portal_users.id = contract.createdBy) 
         END) as createdByName,
        contract.modifiedOn AS modifiedOn,
        contract.modifiedBy AS modifiedBy,
        (CASE 
            WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' 
            THEN (SELECT CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) 
                  FROM user_profiles
                  WHERE user_profiles.user_id = contract.modifiedBy) 
            ELSE (SELECT CONCAT(customer_portal_users.firstName, ' ', customer_portal_users.lastName, ' (Portal User)')
                  FROM customer_portal_users 
                  WHERE customer_portal_users.id = contract.modifiedBy) 
         END) as modifiedByName,
        iso_currencies.code as currency,
        ISNULL(cpv.amount_paid_so_far, 0) AS amount_paid_so_far,
        ISNULL(cpv.balance_due, 0) AS balance_due,
        parties = STUFF(
            (SELECT ', ' + (CASE 
                WHEN contract_party.party_member_type IS NULL THEN NULL
                ELSE (CASE 
                    WHEN party_category_language.name != '' THEN 
                        (CASE 
                            WHEN contract_party.party_member_type = 'company'
                            THEN CONCAT(party_company.name, ' - ', party_category_language.name)
                            ELSE (CASE 
                                WHEN party_contact.father != '' 
                                THEN CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName, ' - ', party_category_language.name)
                                ELSE CONCAT(party_contact.firstName, ' ', party_contact.lastName, ' - ', party_category_language.name)
                                END)
                            END)
                    ELSE (CASE 
                        WHEN contract_party.party_member_type = 'company'
                        THEN party_company.name
                        ELSE (CASE 
                            WHEN party_contact.father != '' 
                            THEN CONCAT(party_contact.firstName, ' ', party_contact.father, ' ', party_contact.lastName)
                            ELSE CONCAT(party_contact.firstName, ' ', party_contact.lastName)
                            END)
                        END)
                    END)
                END)
            FROM contract_party
            LEFT JOIN party ON party.id = contract_party.party_id
            LEFT JOIN companies AS party_company
                ON party_company.id = party.company_id 
                AND contract_party.party_member_type = 'company'
            LEFT JOIN contacts AS party_contact
                ON party_contact.id = party.contact_id 
                AND contract_party.party_member_type = 'contact'
            LEFT JOIN party_category_language
                ON party_category_language.category_id = contract_party.party_category_id 
                AND party_category_language.language_id = '" . $lang_id . "'
            WHERE contract_party.contract_id = contract.id
            FOR XML PATH('')), 1, 1, '')",
            false
        ];

        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    $this->prep_k_filter($_filter, $query, $filter["logic"]);
                }
                unset($_filter);
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }

        $query["join"] = [
            ["user_profiles up", "up.user_id = contract.assignee_id", "left"],
            ["contacts requester", "requester.id = contract.requester_id", "left"],
            ["provider_groups", "provider_groups.id = contract.assigned_team_id", "left"],
            ["user_profiles created_users", "created_users.user_id = contract.createdBy", "left"],
            ["user_profiles modified_users", "modified_users.user_id = contract.modifiedBy", "left"],
            ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"],
            ["contract_status_language as status", "status.status_id = contract.status_id and status.language_id = '" . $lang_id . "'", "left"],
            ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"],
            ["contract as amended", "amended.id = contract.amendment_of", "left"],
            ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"],
            ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"],
            ["contract_payments_view AS cpv", "cpv.contract_id = contract.id", "left"]
        ];

        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];

        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["contract.id desc"];
        }

        if ($return_query) {
            return $query;
        }

        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }

        $query["select"][] = [$this->ci->custom_field->load_grid_custom_fields($this->modelName, $this->_table)];
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $response["data"][0]["total_rows"] ?? false;
        return $response;
    }

    public function load_requester_manager($requester_id)
    {
        $_table = $this->_table;
        $this->_table = "contacts";
        $query["select"] = ["manager.id, (manager.firstName + ' ' + manager.lastName) as name"];
        $query["join"][] = ["user_profiles manager", "manager.user_id = contacts.manager_id", "left"];
        $query["where"][] = ["contacts.id", $requester_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_expiring_contracts_this_month()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.priority, contract.name, concat( '" . $this->modelCode . "', contract.id ) as contract_id, type.name as type, contract.end_date", false];
        $query["join"] = [["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["MONTH(contract.end_date) = MONTH(GETDATE())", NULL, false], ["YEAR(contract.end_date) = YEAR(GETDATE())", NULL, false], ["contract.archived = 'no' "]];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '1' OR(contract.private = '0' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function load_expiring_contracts_this_quarter()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.priority, contract.name, concat( '" . $this->modelCode . "', contract.id ) as contract_id, type.name as type, contract.end_date", false];
        $query["join"] = [["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["DATEPART(q, contract.end_date) =  DATEPART(q, GETDATE())", NULL, false], ["YEAR(contract.end_date) = YEAR(GETDATE())", NULL, false], ["contract.archived = 'no' "]];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function load_expiring_contracts_next_quarter()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.priority, contract.name, concat( '" . $this->modelCode . "', contract.id ) as contract_id, type.name as type, contract.end_date", false];
        $query["join"] = [["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["DATEPART(q, contract.end_date) = DATEPART(q, DATEADD(qq, 1, GETDATE()))", NULL, false], ["YEAR(contract.end_date) = YEAR(DATEADD(qq, 1, GETDATE()))", NULL, false], ["contract.archived = 'no' "]];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function load_received_contracts_this_month()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = ["contract.id, contract.priority, contract.name, concat( '" . $this->modelCode . "', contract.id ) as contract_id, type.name as type, status.name as status, status_category.type as status_category", false];
        $query["join"] = [["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["MONTH(contract.contract_date) = MONTH(GETDATE())", NULL, false], ["YEAR(contract.contract_date) = YEAR(GETDATE())", NULL, false], ["contract.archived = 'no' "]];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["id desc"];
        return parent::load_all($query);
    }
    public function load_contracts_per_status($filters)
    {
        $this->ci->load->model("contract_workflow", "contract_workflowfactory");
        $this->ci->contract_workflow = $this->ci->contract_workflowfactory->get_instance();
        if (0 < $filters["type"]) {
            $workflow = $this->ci->contract_workflow->load_contract_workflow_per_types([$filters["type"]]);
            $workflow_statuses = $this->ci->contract_workflow->load_all_statuses_per_workflow($workflow["workflow_id"] ?? 1);
        } else {
            $this->ci->load->model("contract_status_language", "contract_status_languagefactory");
            $this->ci->contract_status_language = $this->ci->contract_status_languagefactory->get_instance();
            $workflow_statuses = $this->ci->contract_status_language->load_all_per_language();
        }
        $response["statuses"] = [];
        $response["values"] = [];
        foreach ($workflow_statuses as $status) {
            $query = [];
            $query["select"] = ["COUNT(contract.id) as contracts", false];
            $query["where"] = [["contract.archived", "no"]];
            $query["where"][] = ["contract.status_id", $status["id"]];
            if (0 < $filters["type"]) {
                $query["where"][] = ["contract.type_id", $filters["type"]];
                $query["where"][] = ["contract.workflow_id", $workflow["workflow_id"] ?? 1];
            }
            if (0 < $filters["year"]) {
                $query["where"][] = ["YEAR(contract.createdOn)", $filters["year"]];
            }
            $data = $this->load($query);
            if (0 < $data["contracts"]) {
                $response["statuses"][] = $status["name"];
                $response["values"][] = (int) $data["contracts"];
            }
        }
        return $response;
    }
    public function load_contracts_per_department($filters)
    {
        $query = [];

        $query["select"] = [
            "COUNT(contract.id) as contracts, 
        MAX(departments.id) as department_id, 
        MAX(departments.name) as department_name",
            false
        ];

        $query["join"] = [
            ["departments", "departments.id = contract.department_id", "left"],
              ];

        $query["where"] = [["contract.archived", "no"]];
        $query["group_by"] = ["departments.id,departments.name"];

        if (0 < $filters["year"]) {
            $query["where"][] = ["YEAR(contract.createdOn)", $filters["year"]];
        }

        if (0 < $filters["type"]) {
            $query["where"][] = ["contract.type_id", $filters["type"]];
        }

        $data = $this->load_all($query);

        $response["indexes"] = [];
        $response["values"] = [];

        foreach ($data as $val) {
            $response["indexes"][] = $val["department_name"] ?? 'Unassigned';
            $response["values"][] = (int) $val["contracts"];
        }

        return $response;
    }
    public function load_contracts_per_party($filters)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = [];
        $query["select"] = ["COUNT(contract.id) as contracts, MAX(contract_party.party_id), MAX(contract_party.party_member_type) AS party_member_type, MAX(contract_party.party_category_id),\r\n        MAX(party_category_language.name) as party_category_name,\r\n            (CASE WHEN contract_party.party_member_type = 'company'\r\n            THEN MAX(party_company.name)\r\n                 ELSE (CASE WHEN MAX(party_contact.father) != '' THEN MAX(party_contact.firstName + ' ' + party_contact.father + ' ' +  party_contact.lastName)\r\n                       ELSE MAX( party_contact.firstName + ' ' +  party_contact.lastName)\r\n                       END)\r\n                 END) AS party_name,\r\n                 (CASE WHEN contract_party.party_member_type = 'company' THEN MAX(party.company_id) ELSE MAX(party.contact_id) END ) AS party_member_id", false];
        $query["join"] = [["contract_party", "contract_party.id = (SELECT TOP 1 dd.id FROM contract_party dd  WHERE contract.id = dd.contract_id)", "inner"], ["party", "party.id = contract_party.party_id", "left"], ["companies party_company", "party_company.id = party.company_id and contract_party.party_member_type = 'company'", "left"], ["contacts party_contact", "party_contact.id = party.contact_id and contract_party.party_member_type = 'contact'", "left"], ["party_category_language", "party_category_language.category_id = contract_party.party_category_id and party_category_language.language_id = '" . $lang_id . "'", "left"]];
        $query["where"] = [["contract.archived", "no"]];
        $query["group_by"] = ["contract_party.party_id, contract_party.party_member_type"];
        if (0 < $filters["year"]) {
            $query["where"][] = ["YEAR(contract.createdOn)", $filters["year"]];
        }
        if (0 < $filters["type"]) {
            $query["where"][] = ["contract.type_id", $filters["type"]];
        }
        $data = $this->load_all($query);
        $response["indexes"] = [];
        $response["values"] = [];
        foreach ($data as $val) {
            $response["indexes"][] = $val["party_name"];
            $response["values"][] = (int) $val["contracts"];
        }
        return $response;
    }
    public function load_contracts_per_value()
    {
        $response = [];
        $query = [];
        $query["select"] = ["SUM(contract.value) as value, MAX(iso_currencies.code) AS code", false];
        $query["join"] = ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"];
        $query["group_by"] = ["contract.currency_id"];
        $query["where"] = [["contract.archived", "no"]];
        $query["where"][] = ["YEAR(contract.createdOn) = YEAR(GETDATE())", NULL, false];
        $query["where"][] = ["contract.currency_id IS NOT NULL", NULL, false];
        $data = $this->load_all($query);
        if (!empty($data)) {
            foreach ($data as $val) {
                $response["x_axis"][] = $val["code"];
                $response["y_axis"][] = (int) $val["value"];
            }
        }
        return $response;
    }

    public function load_contracts_per_month()
    {
        $response = [];
        $query = [];
        $query["select"] = ["SUM(contract.value) as value, MONTH(contract.createdOn) AS month", false];
        $query["group_by"] = ["MONTH(contract.createdOn)"];
        $query["order_by"] = ["MONTH(contract.createdOn) ASC"];
        $query["where"] = [["contract.archived", "no"]];
        $query["where"][] = ["YEAR(contract.createdOn) = YEAR(GETDATE())", NULL, false];
        $data = $this->load_all($query);

        // Initialize arrays for all 12 months
        $monthlyValues = array_fill(1, 12, 0);
        $monthLabels = [
            1 => $this->ci->lang->line('january'),
            2 => $this->ci->lang->line('february'),
            3 => $this->ci->lang->line('march'),
            4 => $this->ci->lang->line('april'),
            5 => $this->ci->lang->line('may'),
            6 => $this->ci->lang->line('june'),
            7 => $this->ci->lang->line('july'),
            8 => $this->ci->lang->line('august'),
            9 => $this->ci->lang->line('september'),
            10 => $this->ci->lang->line('october'),
            11 => $this->ci->lang->line('november'),
            12 => $this->ci->lang->line('december'),
        ];

        if (!empty($data)) {
            foreach ($data as $val) {
                $month = (int) $val["month"];
                $monthlyValues[$month] = (int) $val["value"];
            }
        }

        // Populate the response array with month labels and values
        foreach ($monthLabels as $month => $label) {
            $response["x_axis"][] = $label;
            $response["y_axis"][] = $monthlyValues[$month];
        }

        return $response;
    }
    public function lookup_approvers_signees($term, $lookup_field)
    {
        $table = $this->_table;
        $this->_table = "users";
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $query = ["select" => ["users.id, us.firstName, us.lastName, 'User' as type", false], "join" => [["user_profiles us", "us.user_id = users.id", "left"]], "where" => [["user_group_id NOT IN (" . $this->ci->user->systemAdministrationGroupId . ")", NULL, false], ["user_group_id != ", $this->ci->user->superAdminInfosystaUserGroupId], ["us.status", "Active"], ["(us.firstName + ' ' + us.lastName) LIKE '%" . $term . "%'", NULL, false]]];
        $users_list = $this->load_all($query);
        $this->_table = "customer_portal_users";
        $query = ["select" => ["customer_portal_users.id, customer_portal_users.firstName, customer_portal_users.lastName, 'Collaborator' as type", false], "where" => [["customer_portal_users.status", "Active"], ["(customer_portal_users.type = 'collaborator' OR customer_portal_users.type = 'both')", NULL, false], ["(customer_portal_users.firstName + ' ' + customer_portal_users.lastName) LIKE '%" . $term . "%'", NULL, false]]];
        $collaborators_list = $this->load_all($query);
        $data = array_merge($users_list, $collaborators_list);
        if ($lookup_field == "approver") {
            $this->_table = "contacts";
            $query = ["select" => ["id, contacts.firstName, contacts.lastName, 'Contact' as type", false], "where" => ["(contacts.private IS NULL OR contacts.private = 'no' OR (contacts.private = 'yes' AND (contacts.createdBy = '" . $this->logged_user_id . "' OR contacts.id IN (SELECT contact_id FROM contact_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'))) AND (contacts.firstName + ' ' + contacts.lastName) LIKE '%" . $term . "%'", NULL, false]];
            $contacts_list = $this->load_all($query);
            $data = array_merge($data, $contacts_list);
        }
        $this->_table = $table;
        return $data;
    }
    public function universal_search($q, $paging_on = true)
    {
        $q2 = $this->escape_universal_search_keyword($q);
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"][] = ["contract.*,  concat('" . $this->get("modelCode") . "', CAST( contract.id AS nvarchar )) AS contract_id,\r\n         type.name as type, (CASE WHEN requester.father!= '' THEN (requester.firstName + ' ' + requester.father + ' ' + requester.lastName) ELSE (requester.firstName + ' ' + requester.lastName) END) as requester,\r\n         (assignee.firstName + ' ' + assignee.lastName) as assignee,\r\n          parties = STUFF((SELECT ', ' +(CASE WHEN contract_party.party_member_type IS NULL THEN NULL\r\n                                                        ELSE (CASE WHEN party_category_language.name != '' THEN (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                                                                                 THEN (party_company.name + ' - ' + party_category_language.name)\r\n                                                              ELSE (CASE WHEN party_contact.father != '' THEN ( party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    ELSE (  party_contact.firstName + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    END)\r\n                                                             END)\r\n                                                       ELSE (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                        THEN party_company.name\r\n                                                             ELSE (CASE WHEN party_contact.father != '' THEN (party_contact.firstName + ' ' + party_contact.father + ' ' +  party_contact.lastName)\r\n                                                                   ELSE ( party_contact.firstName + ' ' +  party_contact.lastName)\r\n                                                                   END)\r\n                                                             END)\r\n                                                       END)\r\n                                                   END\r\n\t\t\t)FROM contract_party\r\n              LEFT JOIN party ON party.id = contract_party.party_id\r\n              LEFT JOIN companies AS party_company\r\n                        ON party_company.id = party.company_id AND\r\n                           contract_party.party_member_type = 'company'\r\n              LEFT JOIN contacts AS party_contact\r\n                        ON party_contact.id = party.contact_id AND\r\n                           contract_party.party_member_type = 'contact'\r\n              LEFT JOIN party_category_language\r\n                        ON party_category_language.category_id =\r\n                           contract_party.party_category_id and\r\n                           party_category_language.language_id = '" . $lang_id . "'\r\n              WHERE contract_party.contract_id = contract.id\r\n\t\t\tFOR XML PATH('')), 1, 1, ''),\r\n         status.name as status_name, iso_currencies.code as currency, status_category.color as status_color, contract.renewal_type as renewal", false];
        $query["join"] = [["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["provider_groups assigned_team", "assigned_team.id = contract.assigned_team_id", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_workflow as workflow", "workflow.id = contract.workflow_id", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contract_party", "contract_party.contract_id = contract.id", "left"], ["party", "contract_party.party_id = party.id", "left"], ["contacts", "party.contact_id = contacts.id", "left"], ["companies", "party.company_id = companies.id", "left"]];
        $query["where"][] = ["(contract.name LIKE '%" . $q2 . "%' escape '\\' OR contract.id = CASE WHEN SUBSTRING('" . $q2 . "', 1, 2) = '" . $this->modelCode . "'THEN (CASE WHEN ISNUMERIC(SUBSTRING('" . $q2 . "', 3, 9)) = 1 THEN SUBSTRING('" . $q2 . "', 3, 9) ELSE 0 END) ELSE (CASE WHEN ISNUMERIC('" . $q2 . "') = 1 THEN CAST('" . $q2 . "' as bigint) ELSE 0 END) END)\r\n        OR contacts.firstName LIKE '%" . $q2 . "%' escape '\\'\r\n        OR contacts.lastName LIKE '%" . $q2 . "%' escape '\\'\r\n        OR concat(contacts.firstName,' ',contacts.lastName) LIKE '%" . $q2 . "%' escape '\\'\r\n        OR companies.name LIKE '%" . $q2 . "%' escape '\\' "];
        $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
        $query["order_by"] = ["contract.id desc"];
        return $paging_on ? parent::paginate($query, ["urlPrefix" => ""]) : parent::load_all($query);
    }
    public function assignee_field_value()
    {
        return "(up.firstName + ' ' + up.lastName)";
    }
    public function parties_field_value()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        return " STUFF((SELECT ', ' +(CASE WHEN contract_party.party_member_type IS NULL THEN NULL\r\n                                                        ELSE (CASE WHEN party_category_language.name != '' THEN (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                                                                                 THEN (party_company.name + ' - ' + party_category_language.name)\r\n                                                              ELSE (CASE WHEN party_contact.father != '' THEN ( party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    ELSE (  party_contact.firstName + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    END)\r\n                                                             END)\r\n                                                       ELSE (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                        THEN party_company.name\r\n                                                             ELSE (CASE WHEN party_contact.father != '' THEN (party_contact.firstName + ' ' + party_contact.father + ' ' +  party_contact.lastName)\r\n                                                                   ELSE ( party_contact.firstName + ' ' +  party_contact.lastName)\r\n                                                                   END)\r\n                                                             END)\r\n                                                       END)\r\n                                                   END\r\n\t\t\t)FROM contract_party\r\n              LEFT JOIN party ON party.id = contract_party.party_id\r\n              LEFT JOIN companies AS party_company\r\n                        ON party_company.id = party.company_id AND\r\n                           contract_party.party_member_type = 'company'\r\n              LEFT JOIN contacts AS party_contact\r\n                        ON party_contact.id = party.contact_id AND\r\n                           contract_party.party_member_type = 'contact'\r\n              LEFT JOIN party_category_language\r\n                        ON party_category_language.category_id =\r\n                           contract_party.party_category_id and\r\n                           party_category_language.language_id = '" . $lang_id . "'\r\n              WHERE contract_party.contract_id = contract.id\r\n\t\t\tFOR XML PATH('')), 1, 1, '')";
    }
    public function load_all_contract_docs($contract_id, $ext = "", $parent_id = "")
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $_table = $this->_table;
        $this->_table = "documents_management_system AS documents";
        $query["select"] = ["documents.id as document_id, documents.type, documents.name, documents.extension, parent.lineage as parent_lineage, (documents.name + '.' + documents.extension) AS full_name, documents.module_record_id as contract_id, status.name as status_name"];
        $query["join"][] = ["documents_management_system parent", "parent.id = documents.parent", "left"];
        $query["join"][] = ["contract_document_status_language status", "status.status_id = documents.document_status_id AND status.language_id = " . $lang_id, "left"];
        $query["where"][] = ["documents.module_record_id", $contract_id];
        $query["where"][] = ["documents.module", $this->modelName];
        $query["where"][] = ["documents.type", "file"];
        $query["where"][] = ["documents.visible", "1"];
        if ($ext) {
            $query["where"][] = ["documents.extension", $ext];
        }
        if ($parent_id) {
            $query["where"][] = ["documents.parent", $parent_id];
        }
        $response = $this->load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_approval_signature_documents($contract_id, $ext = "", $type = "to_be_signed")
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system AS documents";
        $query["select"] = ["documents.id as document_id, documents.type, documents.name, documents.extension, parent.lineage as parent_lineage,\r\n         (documents.name + '.' + documents.extension) AS full_name, documents.module_record_id as contract_id, documents.comment,\r\n          signed_doc.signed_on,\r\n           CASE WHEN signed_doc.signed_by_type = 'user'\r\n           THEN (SELECT ( user_profiles.firstName + ' ' + user_profiles.lastName ) from user_profiles WHERE user_profiles.user_id = signed_doc.signed_by)\r\n           ELSE (SELECT ( contacts.firstName + ' ' + contacts.lastName ) from contacts WHERE contacts.id = signed_doc.signed_by) END as signed_by, signed_doc.signed_by_type"];
        $query["join"][] = ["documents_management_system parent", "parent.id = documents.parent", "left"];
        $query["join"][] = ["contract_signed_document signed_doc", "signed_doc.document_id = documents.id", "left"];
        $query["join"][] = ["approval_signature_documents", "approval_signature_documents.document_id = documents.id", "left"];
        $query["where"][] = ["documents.module_record_id", $contract_id];
        $query["where"][] = ["documents.module", "contract"];
        $query["where"][] = ["approval_signature_documents." . $type, 1];
        $query["where"][] = ["documents.module", $this->modelName];
        $query["where"][] = ["documents.type", "file"];
        $query["where"][] = ["documents.visible", "1"];
        if ($ext) {
            $query["where"][] = ["documents.extension", $ext];
        }
        $response = $this->load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_contract_docs_to_approve($contract_id)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system AS documents";
        $query["select"] = ["documents.id as document_id, (documents.name + '.' + documents.extension) AS full_name, documents.comment as keyword"];
        $query["where"][] = ["documents.module_record_id", $contract_id];
        $query["where"][] = ["documents.module", $this->modelName];
        $query["where"][] = ["documents.type", "file"];
        $query["where"][] = ["documents.visible", "1"];
        $response["docs"] = $this->load_all($query);
        if (!empty($response)) {
            $query["select"] = ["Max(documents.createdOn) as max_created_on"];
            $created_on = $this->load($query);
            $query["select"] = ["documents.id as document_id"];
            $query["where"][] = ["documents.createdOn", $created_on["max_created_on"]];
            $response["latest_document_id"] = $this->load($query)["document_id"];
        }
        $this->_table = $_table;
        return $response;
    }
    public function load_contract_docs_list($contract_id, $lineage, $visible_in_cp = false)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system AS documents";
        $query["select"] = ["documents.id as document_id, (documents.name + '.' + documents.extension) AS full_name"];
        $query["where"][] = ["documents.module_record_id", $contract_id];
        $query["where"][] = ["documents.module", $this->modelName];
        $query["where"][] = ["documents.type", "file"];
        $query["where"][] = ["documents.visible", "1"];
        if (empty($lineage)) {
            if ($lineage === "") {
                $parent = $this->ci->dms->get_container("contract", $contract_id);
                $query["where"][] = ["documents.parent", $parent["id"]];
            }
        } else {
            $lineage_arr = explode(DIRECTORY_SEPARATOR, $lineage);
            $parent_id = count($lineage_arr) - 1;
            $query["where"][] = ["documents.parent", $lineage_arr[$parent_id]];
        }
        if ($visible_in_cp) {
            $query["where"][] = ["documents.visible_in_cp", "1"];
        }
        $response = $this->load_list($query, ["key" => "document_id", "value" => "full_name", "firstLine" => ["" => $this->ci->lang->line("none")]]);
        $this->_table = $_table;
        return $response;
    }
    public function lookup($term)
    {
        $query["select"][] = ["contract.*, ('" . $this->get("modelCode") . "' + CAST( contract.id AS nvarchar )) as contract_id", false];
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];
        if (!empty($term)) {
            $modelCode = substr($term, 0, 2);
            $contract_id = substr($term, 2);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($contract_id)) {
                $query["where"][] = ["contract.id = " . $contract_id, NULL, false];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $query["where"][] = ["contract.name LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($more_filters = $this->ci->input->get("more_filters")) {
            foreach ($more_filters as $_field => $_term) {
                $query["where"][] = [$_field, $_term];
            }
        }
        return $this->load_all($query);
    }
    public function api_lookup($term, $user_id, $override_privacy)
    {
        $query["select"][] = ["contract.id, ('" . $this->get("modelCode") . "' + CAST( contract.id AS nvarchar )) as contract_id, contract.name", false];
        $query["where"][] = ["('" . $override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $user_id . "' OR contract.assignee_id = '" . $user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $user_id . "'))))", NULL, false];
        if (!empty($term)) {
            $modelCode = substr($term, 0, 2);
            $contract_id = substr($term, 2);
            if (strcasecmp($modelCode, $this->get("modelCode")) === 0 && is_numeric($contract_id)) {
                $query["where"][] = ["contract.id = " . $contract_id, NULL, false];
            } else {
                $term = $this->ci->db->escape_like_str($term);
                $query["where"][] = ["contract.name LIKE '%" . $term . "%'", NULL, false];
            }
        }
        if ($more_filters = $this->ci->input->get("more_filters")) {
            foreach ($more_filters as $_field => $_term) {
                $query["where"][] = [$_field, $_term];
            }
        }
        return $this->load_all($query);
    }
    public function load_watchers_users($contract_id)
    {
        $users = [];
        $data = [];
        $status = [];
        if ($contract_id < 1) {
            return $users;
        }
        $results = $this->ci->db->select(["UP.user_id as id, ( UP.firstName + ' ' + UP.lastName ) as name, UP.status as status", false])->join("user_profiles UP", "UP.user_id = contract_users.user_id", "inner")->where("contract_users.contract_id", $contract_id)->get("contract_users");
        if (!$results->num_rows()) {
            return $data;
        }
        foreach ($results->result() as $user) {
            $users[(string) $user->id] = $user->name;
            $status[(string) $user->id] = $user->status;
        }
        $data[0] = $users;
        $data[1] = $status;
        return $data;
    }
    public function dashboard_recent_contracts($category = "contracts", $api_params = [])
    {
        $logged_user_id = $api_params["user_id"] ?? $this->logged_user_id;
        $this->ci->load->model("user_preference");
        $recent_contracts = unserialize($this->ci->user_preference->get_value_by_user("recent_cases", $logged_user_id));
        $response = [];
        if (isset($recent_contracts[$category])) {
            $recent_contracts = $recent_contracts[$category];
            $order_by = "CASE contract.id";
            foreach ($recent_contracts as $key => $val) {
                if ($val == 0) {
                    unset($recent_contracts[$key]);
                } else {
                    $order_by .= " when '" . $val . "' then " . $key;
                }
            }
            $order_by .= " end";
            $recent_contracts = implode(",", array_map("intval", $recent_contracts));
            if (!empty($recent_contracts)) {
                $query["select"][] = ["contract.*,  concat('" . $this->get("modelCode") . "', contract.id) AS contract_id, '" . $category . "' AS module", false];
                $query["where"][] = ["(contract.private IS NULL OR contract.private = '0' OR(contract.private = '1' AND (" . "contract.modifiedBy = '" . $logged_user_id . "' OR contract.assignee_id = '" . $logged_user_id . "' OR " . "contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $logged_user_id . "') OR '" . $this->override_privacy . "' = 'yes'" . ")" . ")" . ")"];
                $query["where"][] = ["contract.id IN (" . $recent_contracts . ")", NULL, false];
                $query["order_by"] = [$order_by];
                $response = $this->load_all($query);
            }
        }
        return $response;
    }
    public function get_contract_grid_query_web($filter, $sortable, $return_query = false)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $response = $this->k_load_all($filter, $sortable, $return_query);
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $response;
    }
    public function assignee_user_id_field_value()
    {
        return "up.user_id";
    }
    public function api_load_all_contracts($logged_in_user, $override_privacy, $lang_id)
    {
        $query = [];
        $this->ci->load->model("language");
        $query["select"][] = ["contract.id,\r\n                                    contract.name  AS name,\r\n                                    provider_groups.name    AS assigned_team,\r\n                                    contract.description AS description,\r\n                                    contract.priority    AS priority,\r\n                                    contract.status    AS contract_status,\r\n                                    contract.contract_date AS contract_date,\r\n                                    contract.start_date      AS start_date,\r\n                                    contract.end_date     AS end_date,\r\n                                    contract.status_comments       AS status_comments,\r\n                                    contract.value   AS value,\r\n                                    contract.amendment_of as amendment_of,\r\n                                    amended.name as amendment_of_name,\r\n                                    contract.reference_number    AS reference_number,\r\n                                    contract.archived AS archived,\r\n                                    contract.private     AS private,\r\n                                    contract.channel,\r\n                                    contract.visible_to_cp, countries_languages.name as country, applicable_law.name as applicable_law,\r\n                                    ('" . $this->get("modelCode") . "' + CAST( contract.id AS nvarchar ))      AS contract_id,\r\n                                    status.name    AS status, contract.renewal_type as renewal,\r\n                                    (CASE WHEN requester.father!= '' THEN (requester.firstName + ' ' + requester.father + ' ' + requester.lastName) ELSE (requester.firstName + ' ' + requester.lastName) END) AS requester,\r\n                                    requester.status    AS requester_status,\r\n                                    (up.firstName + ' ' + up.lastName) AS assignee,\r\n                                    type.name   AS type,\r\n                                    up.status    AS userStatus,\r\n                                    contract.createdOn   AS createdOn,\r\n                                    contract.createdBy   AS createdBy,\r\n                                   (CASE WHEN contract.channel != '" . $this->cp_channel . "' THEN (SELECT (user_profiles.firstName + ' ' + user_profiles.lastName) FROM user_profiles\r\n                                  WHERE user_profiles.user_id = contract.createdBy) ELSE  (SELECT (customer_portal_users.firstName + ' ' + customer_portal_users.lastName + ' (Portal User)')\r\n                                  FROM customer_portal_users WHERE customer_portal_users.id = contract.createdBy) END) as createdByName,\r\n                                    contract.modifiedOn  AS modifiedOn,\r\n                                    contract.modifiedBy  AS modifiedBy,\r\n                                  (CASE WHEN contract.modifiedByChannel != '" . $this->cp_channel . "' THEN (SELECT (user_profiles.firstName + ' ' + user_profiles.lastName) FROM user_profiles\r\n                                   WHERE user_profiles.user_id = contract.modifiedBy) ELSE  (SELECT (customer_portal_users.firstName + ' ' + customer_portal_users.lastName + ' (Portal User)')\r\n                                   FROM customer_portal_users WHERE customer_portal_users.id = contract.modifiedBy) END) as modifiedByName,\r\n                                    iso_currencies.code as currency,\r\n                                    parties = STUFF(\r\n                                        (SELECT ', ' +(CASE WHEN contract_party.party_member_type IS NULL THEN NULL\r\n                                             ELSE (CASE WHEN party_category_language.name != '' THEN (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                                                                      THEN (party_company.name + ' - ' + party_category_language.name)\r\n                                                   ELSE (CASE WHEN party_contact.father != '' THEN ( party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                         ELSE (  party_contact.firstName + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                         END)\r\n                                                  END)\r\n                                            ELSE (CASE WHEN contract_party.party_member_type = 'company'\r\n                                             THEN party_company.name\r\n                                                  ELSE (CASE WHEN party_contact.father != '' THEN (party_contact.firstName + ' ' + party_contact.father + ' ' +  party_contact.lastName)\r\n                                                        ELSE ( party_contact.firstName + ' ' +  party_contact.lastName)\r\n                                                        END)\r\n                                                  END)\r\n                                            END)\r\n                                        END\r\n                                        )\r\n                                    FROM contract_party\r\n                                    LEFT JOIN party ON party.id = contract_party.party_id\r\n                                    LEFT JOIN companies AS party_company\r\n                                              ON party_company.id = party.company_id AND\r\n                                                 contract_party.party_member_type = 'company'\r\n                                    LEFT JOIN contacts AS party_contact\r\n                                              ON party_contact.id = party.contact_id AND\r\n                                                 contract_party.party_member_type = 'contact'\r\n                                    LEFT JOIN party_category_language\r\n                                              ON party_category_language.category_id =\r\n                                                 contract_party.party_category_id and\r\n                                                 party_category_language.language_id = '" . $lang_id . "'\r\n                WHERE contract_party.contract_id = contract.id\r\n                FOR XML PATH('')), 1, 1, '') ", false];
        $query["join"] = [["user_profiles up", "up.user_id = contract.assignee_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["provider_groups", "provider_groups.id = contract.assigned_team_id", "left"], ["user_profiles created_users", "created_users.user_id = contract.createdBy", "left"], ["user_profiles modified_users", "modified_users.user_id = contract.modifiedBy", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status_language as status", "status.status_id = contract.status_id and status.language_id = '" . $lang_id . "'", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["contract as amended", "amended.id = contract.amendment_of", "left"], ["countries_languages", "countries_languages.country_id = contract.country_id AND countries_languages.language_id = " . $lang_id, "left"], ["applicable_law_language as applicable_law", "applicable_law.app_law_id = contract.app_law_id and applicable_law.language_id = '" . $lang_id . "'", "left"]];
        $query["where"][] = ["contract.archived = 'no' ", NULL, false];
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = count($response["data"]) ?? false;
        return $response;
    }
    public function requester_field_value()
    {
        return "(requester.firstName + ' ' +requester.lastName)";
    }

    /**
     * Generate the next contract reference number in the format:
     * CA/SCM/070/0xx/CurrentMonth/CurrentYear
     * 0xx is the count of contracts started this year, zero-padded to 3 digits.
     * @return string
     */
    public function get_new_ref_numberold()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');

        $this->ci->db->from($this->_table);
        $this->ci->db->where("YEAR(createdOn)", $currentYear);
        $count = $this->ci->db->count_all_results();

        // Next contract number (increment by 1 for the new contract)
        $nextNumber = $count + 1;
        $numberPadded = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

       return "CA/SCM/070/{$numberPadded}/{$currentMonth}/{$currentYear}";

    }
    /**
     * on ssms run the following to get ref number
     * DECLARE @ref NVARCHAR(200);
     * EXEC sp_get_new_contract_ref_number @deptCode = 'FIN', @newRefNumber = @ref OUTPUT;
     * SELECT @ref AS NewReference;
 */
    public function get_new_ref_number($deptCode = null)
    {
        $sql = "DECLARE @ref NVARCHAR(200);
            EXEC sp_get_new_contract_ref_number @deptCode = ?, @newRefNumber = @ref OUTPUT;
            SELECT @ref AS ref;";
        $query = $this->ci->db->query($sql, [$deptCode]);
        $row = $query->row_array();
        return $row['ref'];
    }
  


}

?>