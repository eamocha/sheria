<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_approval_submission extends My_Model_Factory
{
}
class mysqli_Contract_approval_submission extends My_Model
{
    protected $modelName = "contract_approval_submission";
    protected $_table = "contract_approval_submission";
    protected $_fieldsNames = ["id", "contract_id", "status"];
    protected $status_list = ["drafting", "awaiting_approval", "awaiting_revision", "approved"];
    protected $modelCode = "CT";
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["contract_id" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "unique" => ["rule" => ["combinedUnique", ["approval_id"]], "message" => sprintf($this->ci->lang->line("is_unique_rule"), $this->ci->lang->line("approval"))]], "status" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->status_list], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->status_list))]];
        $this->logged_user_id = $this->ci->is_auth->get_user_id();
        $this->override_privacy = $this->ci->is_auth->get_override_privacy();
    }
    public function load_awaiting_approvals($filter, $sortable)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $this->ci->db->query("set optimizer_switch = 'block_nested_loop=off'");
        $query["select"] = ["SQL_CALC_FOUND_ROWS `contract`.`id`    AS `id`,\r\n                                   `contract`.`name`  AS `name`,\r\n                                   `assigned_team`.`name`    AS `assigned_team`,\r\n                                   `contract`.`description` AS `description`,\r\n                                   `contract`.`priority`    AS `priority`,\r\n                                   `contract`.`contract_date` AS `contract_date`,\r\n                                   `contract`.`start_date`      AS `start_date`,\r\n                                   `contract`.`end_date`     AS `end_date`,\r\n                                   `contract`.`status_comments`       AS `status_comments`,\r\n                                   `contract`.`value`   AS `value`,\r\n                                   `contract`.`reference_number`    AS `reference_number`,\r\n                                   `contract`.`private`     AS `private`,\r\n                                   `contract`.`archived`     AS `archived`,\r\n                                   concat('" . $this->get("modelCode") . "', `contract`.`id`)      AS `contract_id`,\r\n                                   `status`.`name`    AS `status`,\r\n                                   `requester`.`status`    AS `requester_status`,\r\n                                   `assignee`.`status`    AS `assignee_status`,\r\n                                   `type`.`name`   AS `type`,\r\n                                   (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) as requester,\r\n                                   manager.id as manager_id,\r\n                                    CONCAT(assignee.firstName,' ',assignee.lastName) as assignee,\r\n                                    assigned_team.name as assigned_team,\r\n                                     workflow.name as workflow_name, status.name as status_name, iso_currencies.code as currency, \r\n                                     status_category.color as status_color,                             \r\n                                     CONCAT_WS(', ' ,\r\n                                        (SELECT GROUP_CONCAT( DISTINCT CONCAT( approval_users.firstName, ' ', approval_users.lastName , ' (" . $this->ci->lang->line("user") . ")',\r\n                                                  IF( approval_users.status='Inactive', ' (Inactive)', '') ) SEPARATOR ', ') FROM contract_approval_users as users \r\n                                                  LEFT JOIN user_profiles as approval_users ON approval_users.user_id = users.user_id \r\n                                                  LEFT JOIN `contract_approval_status` ON `users`.`contract_approval_status_id` = `contract_approval_status`.`id`\r\n                                                  WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                                  AND contract_approval_status.contract_id = contract_approval_submission.contract_id ),\r\n                                        (SELECT GROUP_CONCAT( DISTINCT CONCAT( approval_collaborators.firstName, ' ', approval_collaborators.lastName , ' (" . $this->ci->lang->line("collaborator") . ")',\r\n                                                  IF( approval_collaborators.status='Inactive', ' (Inactive)', '') ) SEPARATOR ', ') FROM contract_approval_collaborators as collaborators \r\n                                                  LEFT JOIN customer_portal_users as approval_collaborators ON approval_collaborators.id = collaborators.user_id \r\n                                                  LEFT JOIN `contract_approval_status` ON `collaborators`.`contract_approval_status_id` = `contract_approval_status`.`id`\r\n                                                  WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                                  AND contract_approval_status.contract_id = contract_approval_submission.contract_id ),\r\n                                        (SELECT GROUP_CONCAT( DISTINCT CONCAT( approval_contacts.firstName, ' ', approval_contacts.lastName , ' (" . $this->ci->lang->line("contact") . ")',\r\n                                                  IF( approval_contacts.status='Inactive', ' (Inactive)', '') ) SEPARATOR ', ') FROM contract_approval_contacts as contacts \r\n                                                  LEFT JOIN contacts as approval_contacts ON approval_contacts.id = contacts.contact_id \r\n                                                  LEFT JOIN `contract_approval_status` ON `contacts`.`contract_approval_status_id` = `contract_approval_status`.`id`\r\n                                                  WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                                  AND contract_approval_status.contract_id = contract_approval_submission.contract_id ),\r\n                                        (SELECT GROUP_CONCAT( DISTINCT CONCAT(approval_user_groups.name, ' (" . $this->ci->lang->line("user_group") . ")') SEPARATOR ', ') FROM contract_approval_user_groups as user_groups \r\n                                                  LEFT JOIN user_groups as approval_user_groups ON approval_user_groups.id = user_groups.user_group_id \r\n                                                  LEFT JOIN `contract_approval_status` ON `user_groups`.`contract_approval_status_id` = `contract_approval_status`.`id`\r\n                                                  WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                                  AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                                  ),\r\n                                                  CONCAT(manager.firstName,' ',manager.lastName, ' (" . $this->ci->lang->line("is_manager_requester") . ")')                                        )                                     as approvers , contract_approval_users.user_id, contract_approval_user_groups.user_group_id,                                        (SELECT GROUP_CONCAT(CASE WHEN `contract_party`.`party_member_type` IS NULL THEN NULL                                                        ELSE (CASE WHEN `party_category_language`.`name` != '' THEN (CASE WHEN `contract_party`.`party_member_type` = 'company'                                                                                                                     THEN CONCAT(`party_company`.`name`, ' - ', `party_category_language`.`name`)                                                              ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT( `party_contact`.`firstName`, ' ', `party_contact`.`father`, ' ', `party_contact`.`lastName`,  ' - ', `party_category_language`.`name`)                                                                    ELSE CONCAT(  `party_contact`.`firstName`,  ' ', `party_contact`.`lastName`, ' - ', `party_category_language`.`name`)                                                                    END)                                                             END)                                                       ELSE (CASE WHEN `contract_party`.`party_member_type` = 'company' THEN `party_company`.`name`                                                             ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT(`party_contact`.`firstName`,   ' ', `party_contact`.`father`,  ' ',  `party_contact`.`lastName`)                                                                   ELSE CONCAT( `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`)                                                                   END)                                                             END)                                                       END)                                                   END                                    order by `contract_party`.`contract_id` ASC SEPARATOR ', ')                                   FROM `contract_party`                                              LEFT JOIN `party` ON `party`.`id` = `contract_party`.`party_id`                                              LEFT JOIN `companies` AS `party_company`                                                        ON `party_company`.`id` = `party`.`company_id` AND                                                           `contract_party`.`party_member_type` = 'company'                                              LEFT JOIN `contacts` AS `party_contact`                                                        ON `party_contact`.`id` = `party`.`contact_id` AND                                                           `contract_party`.`party_member_type` = 'contact'                                              LEFT JOIN `party_category_language`                                                        ON `party_category_language`.`category_id` =                                                           `contract_party`.`party_category_id` and                                                           `party_category_language`.`language_id` = '" . $lang_id . "'\r\n                                   WHERE `contract_party`.`contract_id` = `contract`.`id`)   AS `parties`", false];
        $query["where"][] = ["(contract_approval_submission.status = 'awaiting_approval' || contract_approval_submission.status = 'awaiting_revision')", NULL, false];
        $query["where"][] = ["contract_approval_status.status = 'awaiting_approval'", NULL, false];
        $where = [];
        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (isset($_filter["filters"][0]) && in_array($_filter["filters"][0]["field"], ["contract_approval_users.user_id", "contract_approval_user_groups.user_group_id", "manager.id"])) {
                        $ids = "'" . implode("','", $_filter["filters"][0]["value"]) . "'";
                        $where[] = $_filter["filters"][0]["field"] . " " . $_filter["filters"][0]["operator"] . " (" . $ids . ")";
                    } else {
                        $this->prep_k_filter($_filter, $query, $filter["logic"]);
                    }
                }
                unset($_filter);
            }
            if (!empty($where)) {
                $exceptional_cond = "(";
                foreach ($where as $i => $cond) {
                    $exceptional_cond .= $cond . " " . ($i + 1 < count($where) ? "OR " : ")");
                }
                $query["where"][] = [$exceptional_cond, NULL, false];
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["join"] = [["contract ", "contract.id = contract_approval_submission.contract_id", "left"], ["contract_approval_status ", "contract_approval_status.contract_id = contract_approval_submission.contract_id", "left"], ["contract_approval_users ", "contract_approval_users.contract_approval_status_id = contract_approval_status.id", "left"], ["contract_approval_user_groups ", "contract_approval_user_groups.contract_approval_status_id = contract_approval_status.id", "left"], ["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["user_profiles manager", "manager.user_id = requester.manager_id", "left"], ["provider_groups assigned_team", "assigned_team.id = contract.assigned_team_id", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_workflow as workflow", "workflow.id = contract.workflow_id", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"]];
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["contract.id desc"];
        }
        $query["group_by"] = ["contract_approval_status.contract_id"];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $this->ci->db->query("SELECT FOUND_ROWS() AS `count`")->row()->count;
        return $response;
    }
    public function assignee_field_value()
    {
        return "concat(`assignee`.firstName, ' ', `assignee`.`lastName`)";
    }
    public function parties_field_value()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        return "(SELECT GROUP_CONCAT(CASE WHEN `contract_party`.`party_member_type` IS NULL THEN NULL                                                        ELSE (CASE WHEN `party_category_language`.`name` != '' THEN (CASE WHEN `contract_party`.`party_member_type` = 'company'                                                                                                                     THEN CONCAT(`party_company`.`name`, ' - ', `party_category_language`.`name`)                                                              ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT( `party_contact`.`firstName`, ' ', `party_contact`.`father`, ' ', `party_contact`.`lastName`,  ' - ', `party_category_language`.`name`)                                                                    ELSE CONCAT(  `party_contact`.`firstName`,  ' ', `party_contact`.`lastName`, ' - ', `party_category_language`.`name`)                                                                    END)                                                             END)                                                       ELSE (CASE WHEN `contract_party`.`party_member_type` = 'company' THEN `party_company`.`name`                                                             ELSE (CASE WHEN `party_contact`.`father` != '' THEN CONCAT(`party_contact`.`firstName`,   ' ', `party_contact`.`father`,  ' ',  `party_contact`.`lastName`)                                                                   ELSE CONCAT( `party_contact`.`firstName`,  ' ',  `party_contact`.`lastName`)                                                                   END)                                                             END)                                                       END)                                                   END                                    order by `contract_party`.`contract_id` ASC SEPARATOR ', ')                                   FROM `contract_party`                                              LEFT JOIN `party` ON `party`.`id` = `contract_party`.`party_id`                                              LEFT JOIN `companies` AS `party_company`                                                        ON `party_company`.`id` = `party`.`company_id` AND                                                           `contract_party`.`party_member_type` = 'company'                                              LEFT JOIN `contacts` AS `party_contact`                                                        ON `party_contact`.`id` = `party`.`contact_id` AND                                                           `contract_party`.`party_member_type` = 'contact'                                              LEFT JOIN `party_category_language`                                                        ON `party_category_language`.`category_id` =                                                           `contract_party`.`party_category_id` and                                                           `party_category_language`.`language_id` = '" . $lang_id . "'\r\n                                   WHERE `contract_party`.`contract_id` = `contract`.`id`)";
    }
    public function load_cp_awaiting_approvals($logged_in_user, $filter = "all")
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang() ?? 1;
        $query["select"] = ["`contract`.`id`    AS `id`,\r\n                                   `contract`.`name`  AS `name`,\r\n                                   `contract`.`createdBy`  AS `createdBy`,\r\n                                   `contract`.`contract_date` AS `contract_date`,\r\n                                   `contract`.`start_date`      AS `start_date`,\r\n                                   `contract`.`end_date`     AS `end_date`,\r\n                                   `contract`.`value`   AS `value`,\r\n                                   concat('" . $this->get("modelCode") . "', `contract`.`id`)      AS `contract_id`,\r\n                                   `status`.`name`    AS `status`,\r\n                                   `requester`.`status`    AS `requester_status`,\r\n                                   `assignee`.`status`    AS `assignee_status`,\r\n                                   `type`.`name`   AS `type`,\r\n                                   (CASE WHEN requester.father!= '' THEN CONCAT(requester.firstName, ' ', requester.father,' ',  requester.lastName) ELSE CONCAT(requester.firstName, ' ', requester.lastName) END) as requester,\r\n                                   manager.id as manager_id, CONCAT(manager.firstName,' ',manager.lastName) as manager,\r\n                                    CONCAT(assignee.firstName,' ',assignee.lastName) as assignee,\r\n                                  status.name as status_name, iso_currencies.code as currency,\r\n                                      (SELECT GROUP_CONCAT( DISTINCT CONCAT( approval_users.firstName, ' ', approval_users.lastName ,\r\n                                      IF( approval_users.status='Inactive', ' (Inactive)', '') ) SEPARATOR ',') FROM contract_approval_users as users\r\n                                      LEFT JOIN user_profiles as approval_users ON approval_users.user_id = users.user_id\r\n                                      LEFT JOIN `contract_approval_status` ON `users`.`contract_approval_status_id` = `contract_approval_status`.`id`\r\n                                      WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                      AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                      )\r\n                                      as approver_users,\r\n                                     (SELECT GROUP_CONCAT( DISTINCT CONCAT( approval_collaborators.firstName, ' ', approval_collaborators.lastName ,\r\n                                      IF( approval_collaborators.status='Inactive', ' (Inactive)', '') ) SEPARATOR ',') FROM contract_approval_collaborators as collaborators\r\n                                      LEFT JOIN customer_portal_users as approval_collaborators ON approval_collaborators.id = collaborators.user_id\r\n                                      LEFT JOIN `contract_approval_status` ON `collaborators`.`contract_approval_status_id` = `contract_approval_status`.`id`\r\n                                      WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                      AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                      )\r\n                                      as approver_collaborators,\r\n                                       (SELECT GROUP_CONCAT( DISTINCT CONCAT( collaborators_users.firstName, ' ', collaborators_users.lastName ,\r\n                                      IF( collaborators_users.status='Inactive', ' (Inactive)', '') ) SEPARATOR ',') FROM contract_collaborators\r\n                                      LEFT JOIN customer_portal_users as collaborators_users ON collaborators_users.id = contract_collaborators.user_id\r\n                                      WHERE contract_collaborators.contract_id = contract_approval_submission.contract_id\r\n                                      )\r\n                                      as collaborators,\r\n                                      (SELECT GROUP_CONCAT( DISTINCT approval_user_groups.name SEPARATOR ',') FROM contract_approval_user_groups as user_groups\r\n                                      LEFT JOIN user_groups as approval_user_groups ON approval_user_groups.id = user_groups.user_group_id\r\n                                      LEFT JOIN `contract_approval_status` ON `user_groups`.`contract_approval_status_id` = `contract_approval_status`.`id`\r\n                                      WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                      AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                      )\r\n                                      as approver_user_groups,\r\n                                      (SELECT GROUP_CONCAT( DISTINCT CONCAT(approval_contacts.firstName,' ', approval_contacts.lastName) SEPARATOR ',') FROM contract_approval_contacts as contacts\r\n                                      LEFT JOIN contacts as approval_contacts ON approval_contacts.id = contacts.contact_id\r\n                                      LEFT JOIN `contract_approval_status` ON `contacts`.`contract_approval_status_id` = `contract_approval_status`.`id`\r\n                                      WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                      AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                      )\r\n                                      as approver_contacts", false];
        $query["where"][] = ["(contract_approval_submission.status = 'awaiting_approval' || contract_approval_submission.status = 'awaiting_revision')", NULL, false];
        $query["where"][] = ["contract_approval_status.status = 'awaiting_approval'", NULL, false];
        $query["join"] = [["contract ", "contract.id = contract_approval_submission.contract_id", "left"], ["contract_approval_status ", "contract_approval_status.contract_id = contract_approval_submission.contract_id", "left"], ["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["user_profiles manager", "manager.user_id = requester.manager_id", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["customer_portal_users cp_requester", "cp_requester.contact_id = contract.requester_id", "left"]];
        $this->ci->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->ci->customer_portal_users = $this->ci->customer_portal_usersfactory->get_instance();
        $this->ci->customer_portal_users->fetch($logged_in_user);
        $requested_contact = $this->ci->customer_portal_users->get_field("contact_id");
        $query["where"][] = ["(contract.createdBy = " . $logged_in_user . " and contract.channel = '" . $this->ci->contract->get("cp_channel") . "}'\r\n        OR (contract.visible_to_cp = '1' and cp_requester.contact_id = " . $requested_contact . ")\r\n        OR (contract.id in (select contract_id from contract_collaborators where contract_collaborators.user_id = " . $logged_in_user . "))\r\n        OR (contract.id in (select contract_id from customer_portal_contract_watchers where customer_portal_user_id = " . $logged_in_user . ")))"];
        if ($filter == "my_approvals") {
            $query["join"][] = ["contract_approval_collaborators ", "contract_approval_collaborators.contract_approval_status_id = contract_approval_status.id AND contract_approval_collaborators.user_id = '" . $logged_in_user . "'", "inner"];
        }
        $query["group_by"] = ["contract_approval_status.contract_id"];
        $query["order_by"] = ["contract.id DESC"];
        return parent::load_all($query);
    }
}
class mysql_Contract_approval_submission extends mysqli_Contract_approval_submission
{
}
class sqlsrv_Contract_approval_submission extends mysqli_Contract_approval_submission
{
    public function load_awaiting_approvals($filter, $sortable)
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query["select"] = [" COUNT(*) OVER() AS total_rows, MAX(contract.id) as id,\r\n                                MAX(contract.name)  AS name,\r\n                                MAX(assigned_team.name)    AS assigned_team,\r\n                                MAX(cast(contract.description as varchar(255))) as description,\r\n                                MAX(contract.priority)    AS priority,\r\n                                MAX(contract.contract_date) AS contract_date,\r\n                                MAX(contract.start_date)      AS start_date,\r\n                                MAX(contract.end_date)     AS end_date,\r\n                                MAX(cast(contract.status_comments as varchar(255)))       AS status_comments,\r\n                                MAX(contract.value)   AS value,\r\n                                MAX(contract.reference_number)    AS reference_number,\r\n                                MAX(contract.private)     AS private,\r\n                                MAX(contract.archived) AS archived,\r\n                                MAX(contract.channel),\r\n                                MAX(contract.visible_to_cp),\r\n                                ('" . $this->get("modelCode") . "' + MAX(CAST( contract.id AS nvarchar )))      AS contract_id,\r\n                                MAX(status.name)    AS status,\r\n                                MAX(requester.status)    AS requester_status,\r\n                                MAX(assignee.status)    AS assignee_status,\r\n                                MAX(type.name)   AS type,\r\n                                (CASE WHEN MAX(requester.father) != '' THEN MAX(requester.firstName + ' ' + requester.father + ' ' + requester.lastName) ELSE MAX(requester.firstName + ' ' + requester.lastName) END) AS requester,\r\n                                MAX(assignee.firstName + ' ' + assignee.lastName) AS assignee,\r\n                                MAX(manager.id) as manager_id,\r\n                                MAX(assigned_team.name) as assigned_team,\r\n                                MAX(workflow.name)    AS workflow_name,\r\n                                MAX(status.name)    AS status_name,\r\n                                MAX(iso_currencies.code)    AS currency,\r\n                                MAX(status_category.color) as status_color,\r\n                                approvers =\r\n                                        COALESCE(STUFF((SELECT DISTINCT ' '  + ( approval_users.firstName + ' ' + approval_users.lastName +(CASE WHEN approval_users.status='Inactive' THEN ('Inactive') ELSE '' END ) + ' (" . $this->ci->lang->line("user") . ")')\r\n                                        FROM contract_approval_users as users\r\n                                            LEFT JOIN user_profiles as approval_users ON approval_users.user_id = users.user_id\r\n                                            LEFT JOIN contract_approval_status ON users.contract_approval_status_id = contract_approval_status.id\r\n                                        WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                            AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                        FOR XML PATH('')\r\n                                            ), 1, 1, ''), '') \r\n                                        + ' ' +\r\n                                        COALESCE(STUFF((SELECT DISTINCT ' '  + ( approval_collaborators.firstName + ' ' + approval_collaborators.lastName +(CASE WHEN approval_collaborators.status='Inactive' THEN ('Inactive') ELSE '' END ) + ' (" . $this->ci->lang->line("collaborator") . ")')\r\n                                        FROM contract_approval_collaborators as collaborators\r\n                                            LEFT JOIN customer_portal_users as approval_collaborators ON approval_collaborators.id = collaborators.user_id\r\n                                            LEFT JOIN contract_approval_status ON collaborators.contract_approval_status_id = contract_approval_status.id\r\n                                        WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                            AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                        FOR XML PATH('')\r\n                                            ), 1, 1, ''), '') \r\n                                        + ' ' +\r\n                                        COALESCE(STUFF\r\n                                        ((SELECT DISTINCT ' '  + ( approval_contacts.firstName + ' ' + approval_contacts.lastName + ' (" . $this->ci->lang->line("contact") . ")')\r\n                                        FROM contract_approval_contacts as users\r\n                                            LEFT JOIN contacts as approval_contacts ON approval_contacts.id = users.contact_id\r\n                                            LEFT JOIN contract_approval_status ON users.contract_approval_status_id = contract_approval_status.id\r\n                                        WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                            AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                        FOR XML PATH('')\r\n                                            ), 1, 1, ''), '') \r\n                                        + ' ' +\r\n                                        COALESCE(STUFF((SELECT DISTINCT '  ' + ( approval_user_groups.name + ' (" . $this->ci->lang->line("user_group") . ")')\r\n                                        FROM contract_approval_user_groups as user_groups\r\n                                            LEFT JOIN user_groups as approval_user_groups ON approval_user_groups.id = user_groups.user_group_id\r\n                                            LEFT JOIN contract_approval_status ON user_groups.contract_approval_status_id = contract_approval_status.id\r\n                                        WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                            AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                        FOR XML PATH('')\r\n                                            ), 1, 2, ''), '')\r\n                                            + ' ' + COALESCE( MAX(manager.firstName) + ' ' + MAX(manager.lastName) + ' (" . $this->ci->lang->line("is_manager_requester") . ")', ''),\r\n                                MAX(contract_approval_users.user_id), MAX(contract_approval_user_groups.user_group_id),\r\n                                parties = STUFF(\r\n\t\t\t(SELECT ', ' +(CASE WHEN contract_party.party_member_type IS NULL THEN NULL\r\n                                                        ELSE (CASE WHEN party_category_language.name != '' THEN (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                                                                                 THEN (party_company.name + ' - ' + party_category_language.name)\r\n                                                              ELSE (CASE WHEN party_contact.father != '' THEN ( party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    ELSE (  party_contact.firstName + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    END)\r\n                                                             END)\r\n                                                       ELSE (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                        THEN party_company.name\r\n                                                             ELSE (CASE WHEN party_contact.father != '' THEN (party_contact.firstName + ' ' + party_contact.father + ' ' +  party_contact.lastName)\r\n                                                                   ELSE ( party_contact.firstName + ' ' +  party_contact.lastName)\r\n                                                                   END)\r\n                                                             END)\r\n                                                       END)\r\n                                                   END\r\n\t\t\t)\r\n\t\t\t FROM contract_party\r\n                                              LEFT JOIN party ON party.id = contract_party.party_id\r\n                                              LEFT JOIN companies AS party_company\r\n                                                        ON party_company.id = party.company_id AND\r\n                                                           contract_party.party_member_type = 'company'\r\n                                              LEFT JOIN contacts AS party_contact\r\n                                                        ON party_contact.id = party.contact_id AND\r\n                                                           contract_party.party_member_type = 'contact'\r\n                                              LEFT JOIN party_category_language\r\n                                                        ON party_category_language.category_id =\r\n                                                           contract_party.party_category_id and\r\n                                                           party_category_language.language_id = '" . $lang_id . "'\r\n                                   WHERE contract_party.contract_id = contract_approval_status.contract_id\r\n\t\t\tFOR XML PATH('')), 1, 1, '')\r\n                                   ", false];
        $query["where"][] = ["(contract_approval_submission.status = 'awaiting_approval' OR contract_approval_submission.status = 'awaiting_revision')", NULL, false];
        $query["where"][] = ["contract_approval_status.status = 'awaiting_approval'", NULL, false];
        $where = [];
        if (isset($filter) && is_array($filter)) {
            if (isset($filter["filters"])) {
                foreach ($filter["filters"] as $_filter) {
                    if (isset($_filter["filters"][0]) && in_array($_filter["filters"][0]["field"], ["contract_approval_users.user_id", "contract_approval_user_groups.user_group_id", "manager.id"])) {
                        $ids = "'" . implode("','", $_filter["filters"][0]["value"]) . "'";
                        $where[] = $_filter["filters"][0]["field"] . " " . $_filter["filters"][0]["operator"] . " (" . $ids . ")";
                    } else {
                        $this->prep_k_filter($_filter, $query, $filter["logic"]);
                    }
                }
                unset($_filter);
            }
            if (!empty($where)) {
                $exceptional_cond = "(";
                foreach ($where as $i => $cond) {
                    $exceptional_cond .= $cond . " " . ($i + 1 < count($where) ? "OR " : ")");
                }
                $query["where"][] = [$exceptional_cond, NULL, false];
            }
            if (isset($filter["customFields"])) {
                $this->ci->load->model("custom_field", "custom_fieldfactory");
                $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance("custom_fieldfactory");
                $this->ci->custom_field->prep_custom_field_filters($this->modelName, $filter["customFields"], $query);
            }
        }
        $query["join"] = [["contract ", "contract.id = contract_approval_submission.contract_id", "left"], ["contract_approval_status ", "contract_approval_status.contract_id = contract_approval_submission.contract_id", "left"], ["contract_approval_users ", "contract_approval_users.contract_approval_status_id = contract_approval_status.id", "left"], ["contract_approval_user_groups ", "contract_approval_user_groups.contract_approval_status_id = contract_approval_status.id", "left"], ["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["user_profiles manager", "manager.user_id = requester.manager_id", "left"], ["provider_groups assigned_team", "assigned_team.id = contract.assigned_team_id", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["status_category", "status_category.id = contract_status.category_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["contract_workflow as workflow", "workflow.id = contract.workflow_id", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"]];
        $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' or contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["id desc"];
        }
        $query["group_by"] = ["contract_approval_status.contract_id, contract_approval_submission.contract_id"];
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = parent::load_all($query);
        $response["totalRows"] = $response["data"][0]["total_rows"] ?? false;
        return $response;
    }
    public function assignee_field_value()
    {
        return "(assignee.firstName + ' ' + assignee.lastName)";
    }
    public function parties_field_value()
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        return "  STUFF((SELECT ', ' +(CASE WHEN contract_party.party_member_type IS NULL THEN NULL\r\n                                                        ELSE (CASE WHEN party_category_language.name != '' THEN (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                                                                                 THEN (party_company.name + ' - ' + party_category_language.name)\r\n                                                              ELSE (CASE WHEN party_contact.father != '' THEN ( party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    ELSE (  party_contact.firstName + ' ' + party_contact.lastName + ' - ' + party_category_language.name)\r\n                                                                    END)\r\n                                                             END)\r\n                                                       ELSE (CASE WHEN contract_party.party_member_type = 'company'\r\n                                                        THEN party_company.name\r\n                                                             ELSE (CASE WHEN party_contact.father != '' THEN (party_contact.firstName + ' ' + party_contact.father + ' ' +  party_contact.lastName)\r\n                                                                   ELSE ( party_contact.firstName + ' ' +  party_contact.lastName)\r\n                                                                   END)\r\n                                                             END)\r\n                                                       END)\r\n                                                   END\r\n\t\t\t)FROM contract_party\r\n              LEFT JOIN party ON party.id = contract_party.party_id\r\n              LEFT JOIN companies AS party_company\r\n                        ON party_company.id = party.company_id AND\r\n                           contract_party.party_member_type = 'company'\r\n              LEFT JOIN contacts AS party_contact\r\n                        ON party_contact.id = party.contact_id AND\r\n                           contract_party.party_member_type = 'contact'\r\n              LEFT JOIN party_category_language\r\n                        ON party_category_language.category_id =\r\n                           contract_party.party_category_id and\r\n                           party_category_language.language_id = '" . $lang_id . "'\r\n              WHERE contract_party.contract_id = contract.id\r\n\t\t\tFOR XML PATH('')), 1, 1, '')";
    }
    public function load_cp_awaiting_approvals($logged_in_user, $filter = "all")
    {
        $query = [];
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang() ?? 1;
        $query["select"] = ["MAX(contract.id) as id,\r\n                                MAX(contract.name)  AS name,\r\n                                MAX(contract.contract_date) AS contract_date,\r\n                                MAX(contract.start_date)      AS start_date,\r\n                                MAX(contract.end_date)     AS end_date,\r\n                                MAX(contract.value)   AS value,\r\n                                ('" . $this->get("modelCode") . "' + MAX(CAST( contract.id AS nvarchar )))      AS contract_id,\r\n                                MAX(status.name)    AS status,\r\n                                MAX(requester.status)    AS requester_status,\r\n                                MAX(assignee.status)    AS assignee_status,\r\n                                MAX(type.name)   AS type,\r\n                                (CASE WHEN MAX(requester.father) != '' THEN MAX(requester.firstName + ' ' + requester.father + ' ' + requester.lastName) ELSE MAX(requester.firstName + ' ' + requester.lastName) END) AS requester,\r\n                                MAX(assignee.firstName + ' ' + assignee.lastName) AS assignee,\r\n                                MAX(manager.id) as manager_id, MAX(manager.firstName + ' ' + manager.lastName) as manager,\r\n                                MAX(status.name)    AS status_name,\r\n                                MAX(iso_currencies.code)    AS currency,\r\n                                approver_users= STUFF(\r\n                                (SELECT DISTINCT ', ' + ( approval_users.firstName + ' ' + approval_users.lastName +(CASE WHEN approval_users.status='Inactive' THEN ('Inactive') ELSE '' END )) FROM contract_approval_users as users\r\n                                      LEFT JOIN user_profiles as approval_users ON approval_users.user_id = users.user_id\r\n                                      LEFT JOIN contract_approval_status ON users.contract_approval_status_id = contract_approval_status.id\r\n                                      WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                      AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                     FOR XML PATH('')), 1, 1, ''),\r\n                               approver_collaborators= STUFF(\r\n                                (SELECT DISTINCT ', ' + ( approval_collaborators.firstName + ' ' + approval_collaborators.lastName +(CASE WHEN approval_collaborators.status='Inactive' THEN ('Inactive') ELSE '' END )) FROM contract_approval_collaborators as collaborators\r\n                                      LEFT JOIN customer_portal_users as approval_collaborators ON approval_collaborators.id = collaborators.user_id\r\n                                      LEFT JOIN contract_approval_status ON collaborators.contract_approval_status_id = contract_approval_status.id\r\n                                      WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                      AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                     FOR XML PATH('')), 1, 1, ''),\r\n                               collaborators= STUFF(\r\n                                (SELECT DISTINCT ', ' + ( collaborators_users.firstName + ' ' + collaborators_users.lastName +(CASE WHEN collaborators_users.status='Inactive' THEN ('Inactive') ELSE '' END )) FROM contract_collaborators\r\n                                      LEFT JOIN customer_portal_users as collaborators_users ON collaborators_users.id = contract_collaborators.user_id\r\n                                      WHERE contract_collaborators.contract_id = contract_approval_submission.contract_id\r\n                                     FOR XML PATH('')), 1, 1, ''),\r\n                                approver_user_groups = STUFF(\r\n                                (SELECT DISTINCT ', ' + ( approval_user_groups.name) FROM contract_approval_user_groups as user_groups\r\n                                      LEFT JOIN user_groups as approval_user_groups ON approval_user_groups.id = user_groups.user_group_id\r\n                                      LEFT JOIN contract_approval_status ON user_groups.contract_approval_status_id = contract_approval_status.id\r\n                                      WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                      AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                      FOR XML PATH('')), 1, 1, ''),\r\n                                approver_contacts = STUFF(\r\n                                (SELECT DISTINCT ', ' + ( approval_contacts.firstName + ' ' + approval_contacts.lastName) FROM contract_approval_contacts as contacts\r\n                                      LEFT JOIN contacts as approval_contacts ON approval_contacts.id = contacts.contact_id\r\n                                      LEFT JOIN contract_approval_status ON contacts.contract_approval_status_id = contract_approval_status.id\r\n                                      WHERE contract_approval_status.status = 'awaiting_approval'\r\n                                      AND contract_approval_status.contract_id = contract_approval_submission.contract_id\r\n                                      FOR XML PATH('')), 1, 1, '')\r\n                               ", false];
        $query["where"][] = ["(contract_approval_submission.status = 'awaiting_approval' OR contract_approval_submission.status = 'awaiting_revision')", NULL, false];
        $query["where"][] = ["contract_approval_status.status = 'awaiting_approval'", NULL, false];
        $query["join"] = [["contract ", "contract.id = contract_approval_submission.contract_id", "left"], ["contract_approval_status ", "contract_approval_status.contract_id = contract_approval_submission.contract_id", "left"], ["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"], ["contacts requester", "requester.id = contract.requester_id", "left"], ["user_profiles manager", "manager.user_id = requester.manager_id", "left"], ["contract_type_language as type", "type.type_id = contract.type_id and type.language_id = '" . $lang_id . "'", "left"], ["contract_status", "contract_status.id = contract.status_id", "left"], ["contract_status_language as status", "status.status_id = contract_status.id and status.language_id = '" . $lang_id . "'", "left"], ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"], ["customer_portal_users cp_requester", "cp_requester.contact_id = contract.requester_id", "left"]];
        $this->ci->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->ci->customer_portal_users = $this->ci->customer_portal_usersfactory->get_instance();
        $this->ci->customer_portal_users->fetch($logged_in_user);
        $requested_contact = $this->ci->customer_portal_users->get_field("contact_id");
        $query["where"][] = ["(contract.createdBy = " . $logged_in_user . " and contract.channel = '" . $this->ci->contract->get("cp_channel") . "}'\r\n        OR (contract.visible_to_cp = '1' and cp_requester.contact_id = " . $requested_contact . ")\r\n        OR (contract.id in (select contract_id from contract_collaborators where contract_collaborators.user_id = " . $logged_in_user . "))\r\n        OR (contract.id in (select contract_id from customer_portal_contract_watchers where customer_portal_user_id = " . $logged_in_user . ")))"];
        if ($filter == "my_approvals") {
            $query["join"][] = ["contract_approval_collaborators ", "contract_approval_collaborators.contract_approval_status_id = contract_approval_status.id AND contract_approval_collaborators.user_id = '" . $logged_in_user . "'", "inner"];
        }
        $query["group_by"] = ["contract_approval_status.contract_id, contract_approval_submission.contract_id"];
        $query["order_by"] = ["contract_approval_status.contract_id DESC"];
        return parent::load_all($query);
    }
    
public function api_load_awaiting_approvals($logged_in_user, $override_privacy = "no", $lang_id = 1)
{
    $this->logged_user_id = $logged_in_user;
    $this->override_privacy = $override_privacy;
    $query = [];

    // SQL Server compliant SELECT
    $query["select"] = ["
        MAX(contract.id) AS id,
        MAX(contract.name) AS name,
        MAX(assigned_team.name) AS assigned_team,
        MAX(CAST(contract.description AS varchar(255))) AS description,
        MAX(contract.priority) AS priority,
        MAX(contract.contract_date) AS contract_date,
        MAX(contract.start_date) AS start_date,
        MAX(contract.end_date) AS end_date,
        MAX(CAST(contract.status_comments AS varchar(255))) AS status_comments,
        MAX(contract.value) AS value,
        MAX(contract.reference_number) AS reference_number,
        MAX(contract.private) AS private,
        MAX(contract.archived) AS archived,
        ('" . $this->get("modelCode") . "' + CAST(MAX(contract.id) AS nvarchar)) AS contract_id,
        MAX(status.name) AS status,
        MAX(requester.status) AS requester_status,
        MAX(assignee.status) AS assignee_status,
        MAX(type.name) AS type,

        -- requester full name
        MAX(
            CASE 
                WHEN requester.father != '' 
                THEN requester.firstName + ' ' + requester.father + ' ' + requester.lastName 
                ELSE requester.firstName + ' ' + requester.lastName 
            END
        ) AS requester,

        MAX(manager.id) AS manager_id,
        CONCAT(MAX(manager.firstName), ' ', MAX(manager.lastName)) AS assignee,
        MAX(workflow.name) AS workflow_name,
        MAX(status.name) AS status_name,
        MAX(iso_currencies.code) AS currency,
        MAX(status_category.color) AS status_color,

        -- approvers aggregation
        (
            COALESCE(
                STUFF((
                    SELECT DISTINCT ' ' + approval_users.firstName + ' ' + approval_users.lastName
                        + CASE WHEN approval_users.status = 'Inactive' THEN ' (Inactive)' ELSE '' END
                        + ' (" . $this->ci->lang->line("user") . ")'
                    FROM contract_approval_users AS users
                    LEFT JOIN user_profiles AS approval_users ON approval_users.user_id = users.user_id
                    LEFT JOIN contract_approval_status ON users.contract_approval_status_id = contract_approval_status.id
                    WHERE contract_approval_status.status = 'awaiting_approval'
                    AND contract_approval_status.contract_id = contract_approval_submission.contract_id
                    FOR XML PATH('')
                ), 1, 1, ''), '')
            + ' ' +
            COALESCE(
                STUFF((
                    SELECT DISTINCT ' ' + approval_collaborators.firstName + ' ' + approval_collaborators.lastName
                        + CASE WHEN approval_collaborators.status = 'Inactive' THEN ' (Inactive)' ELSE '' END
                        + ' (" . $this->ci->lang->line("collaborator") . ")'
                    FROM contract_approval_collaborators AS collaborators
                    LEFT JOIN customer_portal_users AS approval_collaborators ON approval_collaborators.id = collaborators.user_id
                    LEFT JOIN contract_approval_status ON collaborators.contract_approval_status_id = contract_approval_status.id
                    WHERE contract_approval_status.status = 'awaiting_approval'
                    AND contract_approval_status.contract_id = contract_approval_submission.contract_id
                    FOR XML PATH('')
                ), 1, 1, ''), '')
            + ' ' +
            COALESCE(
                STUFF((
                    SELECT DISTINCT ' ' + approval_contacts.firstName + ' ' + approval_contacts.lastName 
                        + ' (" . $this->ci->lang->line("contact") . ")'
                    FROM contract_approval_contacts AS users
                    LEFT JOIN contacts AS approval_contacts ON approval_contacts.id = users.contact_id
                    LEFT JOIN contract_approval_status ON users.contract_approval_status_id = contract_approval_status.id
                    WHERE contract_approval_status.status = 'awaiting_approval'
                    AND contract_approval_status.contract_id = contract_approval_submission.contract_id
                    FOR XML PATH('')
                ), 1, 1, ''), '')
            + ' ' +
            COALESCE(
                STUFF((
                    SELECT DISTINCT ' ' + approval_user_groups.name 
                        + ' (" . $this->ci->lang->line("user_group") . ")'
                    FROM contract_approval_user_groups AS user_groups
                    LEFT JOIN user_groups AS approval_user_groups ON approval_user_groups.id = user_groups.user_group_id
                    LEFT JOIN contract_approval_status ON user_groups.contract_approval_status_id = contract_approval_status.id
                    WHERE contract_approval_status.status = 'awaiting_approval'
                    AND contract_approval_status.contract_id = contract_approval_submission.contract_id
                    FOR XML PATH('')
                ), 1, 1, ''), '')
            + ' ' +
            COALESCE(CONCAT(MAX(manager.firstName), ' ', MAX(manager.lastName), ' (" . $this->ci->lang->line("is_manager_requester") . ")'), '')
        ) AS approvers,

        MAX(contract_approval_users.user_id) AS user_id,
        MAX(contract_approval_user_groups.user_group_id) AS user_group_id,

        -- parties aggregation
        STUFF((
            SELECT ', ' +
                CASE 
                    WHEN contract_party.party_member_type IS NULL THEN NULL
                    WHEN party_category_language.name != '' THEN 
                        CASE 
                            WHEN contract_party.party_member_type = 'company'
                                THEN party_company.name + ' - ' + party_category_language.name
                            WHEN party_contact.father != '' 
                                THEN party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName + ' - ' + party_category_language.name
                            ELSE party_contact.firstName + ' ' + party_contact.lastName + ' - ' + party_category_language.name
                        END
                    ELSE 
                        CASE 
                            WHEN contract_party.party_member_type = 'company'
                                THEN party_company.name
                            WHEN party_contact.father != '' 
                                THEN party_contact.firstName + ' ' + party_contact.father + ' ' + party_contact.lastName
                            ELSE party_contact.firstName + ' ' + party_contact.lastName
                        END
                END
            FROM contract_party
            LEFT JOIN party ON party.id = contract_party.party_id
            LEFT JOIN companies AS party_company ON party_company.id = party.company_id AND contract_party.party_member_type = 'company'
            LEFT JOIN contacts AS party_contact ON party_contact.id = party.contact_id AND contract_party.party_member_type = 'contact'
            LEFT JOIN party_category_language 
                ON party_category_language.category_id = contract_party.party_category_id 
                AND party_category_language.language_id = '" . $lang_id . "'
            WHERE contract_party.contract_id = contract_approval_status.contract_id
            FOR XML PATH('')
        ), 1, 2, '') AS parties
    ", false];

    // WHERE conditions
    $query["where"][] = ["(contract_approval_submission.status = 'awaiting_approval' OR contract_approval_submission.status = 'awaiting_revision')", NULL, false];
    $query["where"][] = ["contract_approval_status.status = 'awaiting_approval'", NULL, false];
    $query["where"][] = ["('" . $this->override_privacy . "' = 'yes' OR contract.private IS NULL OR contract.private = '0' OR (contract.private = 1 AND (contract.createdBy = '" . $this->logged_user_id . "' OR contract.assignee_id = '" . $this->logged_user_id . "' OR contract.id IN (SELECT contract_id FROM contract_users WHERE user_id = '" . $this->logged_user_id . "'))))", NULL, false];

    // Joins
    $query["join"] = [
        ["contract", "contract.id = contract_approval_submission.contract_id", "left"],
        ["contract_approval_status", "contract_approval_status.contract_id = contract_approval_submission.contract_id", "left"],
        ["contract_approval_users", "contract_approval_users.contract_approval_status_id = contract_approval_status.id", "left"],
        ["contract_approval_user_groups", "contract_approval_user_groups.contract_approval_status_id = contract_approval_status.id", "left"],
        ["user_profiles assignee", "assignee.user_id = contract.assignee_id", "left"],
        ["contacts requester", "requester.id = contract.requester_id", "left"],
        ["user_profiles manager", "manager.user_id = requester.manager_id", "left"],
        ["provider_groups assigned_team", "assigned_team.id = contract.assigned_team_id", "left"],
        ["contract_type_language AS type", "type.type_id = contract.type_id AND type.language_id = '" . $lang_id . "'", "left"],
        ["contract_status", "contract_status.id = contract.status_id", "left"],
        ["status_category", "status_category.id = contract_status.category_id", "left"],
        ["contract_status_language AS status", "status.status_id = contract_status.id AND status.language_id = '" . $lang_id . "'", "left"],
        ["contract_workflow AS workflow", "workflow.id = contract.workflow_id", "left"],
        ["iso_currencies", "iso_currencies.id = contract.currency_id", "left"]
    ];

    // Sorting and grouping
    $query["group_by"] = ["contract_approval_status.contract_id, contract_approval_submission.contract_id"];
    if (is_array($sortable) && !empty($sortable)) {
        foreach ($sortable as $_sort) {
            $query["order_by"][] = [$_sort["field"], $_sort["dir"]]; 
        }
    } else {
        $query["order_by"] = ["id DESC"];
    }

    // Pagination
    if ($limit = $this->ci->input->post("take", true)) {
        $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
    }

    $response["data"] = parent::load_all($query);
    return $response;
}

}

?>