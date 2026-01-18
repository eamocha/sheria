<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Legal_case_outsource extends My_Model_Factory
{
}
class mysql_Legal_case_outsource extends My_Model
{
    protected $modelName = "legal_case_outsource";
    protected $_table = "legal_case_outsources";
    protected $_fieldsNames = ["id", "legal_case_id", "company_id", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["legal_case_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "company_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule"), "unique" => ["rule" => ["combinedUnique", ["legal_case_id", "company_id"]], "message" => $this->ci->lang->line("already_exists")]], "createdBy" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "modifiedBy" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function k_load_all_outsources($legal_case_id)
    {
        $query = ["select" => ["\r\n                legal_case_outsources.id AS id,\r\n                legal_case_outsources.legal_case_id AS case_id,\r\n                contacts_data.company_id AS company_id,\r\n                contacts_data.company_name AS company_name,\r\n                GROUP_CONCAT(CONCAT( contacts_data.name ,\r\n                CASE\r\n                    WHEN contacts_data.email is null THEN \"\"\r\n                    ELSE CONCAT(\" (\" , contacts_data.email ,\")\")\r\n                END \r\n                ) SEPARATOR \", \") AS contacts_names,\r\n                GROUP_CONCAT(contacts_data.id SEPARATOR \", \") AS contacts_ids,\r\n                GROUP_CONCAT(contacts_data.is_licensed_advisor SEPARATOR \", \") AS are_licensed_advisors\r\n                ", false], "join" => [["legal_cases", "legal_cases.id = legal_case_outsources.legal_case_id", "inner"], ["legal_case_outsource_contacts", "legal_case_outsource_contacts.legal_case_outsource_id = legal_case_outsources.id", "left"], ["\r\n                (SELECT contacts.id AS id,\r\n                        companies.id AS company_id,\r\n                        companies.name AS company_name,\r\n                        companies.status as  company_status,          \r\n                    (SELECT email\r\n                    FROM contact_emails\r\n                    WHERE contact_emails.contact_id = contacts.id limit 1) AS email,\r\n                        CASE\r\n                            WHEN contacts.father!='' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName)\r\n                            ELSE CONCAT(contacts.firstName, ' ', contacts.lastName)\r\n                        END AS name,\r\n                        CASE\r\n                            WHEN advisor_users.id IS NOT NULL THEN 1\r\n                            ELSE 0\r\n                        END AS is_licensed_advisor\r\n                        FROM contacts\r\n                        JOIN companies_contacts ON companies_contacts.contact_id = contacts.id\r\n                        INNER JOIN companies ON companies_contacts.company_id = companies.id\r\n                        LEFT JOIN advisor_users ON advisor_users.contact_id = contacts.id\r\n                        AND advisor_users.company_id = companies.id) AS contacts_data\r\n                                ", "contacts_data.id = legal_case_outsource_contacts.contact_id \r\n                                AND legal_case_outsources.company_id = contacts_data.company_id"]], "where" => [["legal_cases.id", $legal_case_id], ["contacts_data.company_status", "Active"]], "group_by" => ["contacts_data.company_id"], "order_by" => ["legal_case_outsources.id", "DESC"]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        $response["data"] = parent::load_all($query);
        return $response;
    }
    public function delete_outsource($outsource_id)
    {
        $table = $this->_table;
        $this->_table = "legal_case_outsource_contacts";
        $delet_related_contacts = $this->delete(["join" => [["legal_case_outsources", "legal_case_outsources.id = legal_case_outsource_contacts.legal_case_outsource_id"]], "where" => [["legal_case_outsource_contacts.legal_case_outsource_id", $outsource_id]]]);
        $this->_table = $table;
        if ($delet_related_contacts) {
            return $this->delete($outsource_id);
        }
        return false;
    }
    public function company_already_outsourced($legal_case_id, $company_id)
    {
        $query = ["where" => [["legal_case_outsources.legal_case_id", $legal_case_id], ["legal_case_outsources.company_id", $company_id]]];
        return $this->load($query);
    }
}
class mysqli_Legal_case_outsource extends mysql_Legal_case_outsource
{
}
class sqlsrv_Legal_case_outsource extends mysql_Legal_case_outsource
{
    public function k_load_all_outsources($legal_case_id)
    {
        $query = ["select" => ["\r\n            legal_case_outsources.id AS id,\r\n       legal_case_outsources.legal_case_id AS case_id,\r\n       companies.id AS company_id,\r\n       companies.name AS company_name,\r\n       STUFF(\r\n               (SELECT (', '+ CASE\r\n                                  WHEN n_contact.father!='' THEN n_contact.firstName + ' ' + n_contact.father + ' ' + n_contact.lastName\r\n                                  ELSE n_contact.firstName + ' ' + n_contact.lastName\r\n                              END), CASE\r\n                                        WHEN\r\n                                               (SELECT top(1) email\r\n                                                FROM contact_emails\r\n                                                WHERE contact_emails.contact_id = n_contact.id) IS NULL THEN ''\r\n                                        ELSE ' (' +\r\n                                               (SELECT top(1) email\r\n                                                FROM contact_emails\r\n                                                WHERE contact_emails.contact_id = n_contact.id) +')'\r\n                                    END\r\n                FROM contacts AS n_contact\r\n                INNER JOIN companies_contacts ON companies_contacts.company_id = companies.id\r\n                AND companies_contacts.contact_id = n_contact.id\r\n\t\t\t\tINNER JOIN legal_case_outsource_contacts  ON n_contact.id = legal_case_outsource_contacts.contact_id and legal_case_outsources.id = legal_case_outsource_contacts.legal_case_outsource_id\r\n                FOR XML PATH('')), 1, 1, '') AS contacts_names,\r\n       STUFF(\r\n               (SELECT ', '+ CAST(sub_contact.id AS nvarchar(MAX))\r\n                FROM contacts AS sub_contact\r\n                INNER JOIN companies_contacts ON companies_contacts.company_id = companies.id\r\n                AND companies_contacts.contact_id = sub_contact.id\r\n\t\t\t\tINNER JOIN legal_case_outsource_contacts  ON sub_contact.id = legal_case_outsource_contacts.contact_id and legal_case_outsources.id = legal_case_outsource_contacts.legal_case_outsource_id\r\n                FOR XML PATH('')), 1, 1, '') AS contacts_ids,\r\n       STUFF(\r\n               (SELECT ', '+ CASE\r\n                                 WHEN advisor_users.id IS NOT NULL THEN '1'\r\n                                 ELSE '0'\r\n                             END\r\n                FROM contacts AS li_contact\r\n                INNER JOIN companies_contacts ON companies_contacts.company_id = companies.id\r\n                AND companies_contacts.contact_id = li_contact.id\r\n                LEFT JOIN advisor_users ON advisor_users.contact_id = li_contact.id\r\n                AND advisor_users.company_id = companies.id\r\n\t\t\t\tINNER JOIN legal_case_outsource_contacts  ON li_contact.id = legal_case_outsource_contacts.contact_id and legal_case_outsources.id = legal_case_outsource_contacts.legal_case_outsource_id\r\n                FOR XML PATH('')), 1, 1, '') AS are_licensed_advisors\r\n                ", false], "join" => [["legal_cases", "legal_cases.id = legal_case_outsources.legal_case_id", "inner"], ["companies", "companies.id = legal_case_outsources\".\"company_id", "left"], ["legal_case_outsource_contacts", "legal_case_outsource_contacts.legal_case_outsource_id = legal_case_outsources.id", "left"], ["contacts", "contacts.id = legal_case_outsource_contacts.contact_id AND legal_case_outsources.company_id = legal_case_outsources.company_id", "inner"]], "where" => [["legal_cases.id", $legal_case_id], ["companies.status", "Active"]], "group_by" => ["\r\n                legal_case_outsources.id,\r\n                legal_case_outsources.legal_case_id,\r\n                companies.id,\r\n                companies.name,\r\n                legal_case_outsources.id\r\n                "]];
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        $response["data"] = parent::load_all($query);
        return $response;
    }
}

?>