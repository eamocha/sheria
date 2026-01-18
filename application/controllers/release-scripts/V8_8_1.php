<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V8_8_1 extends CI_Controller
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
        $this->discount_after_tax();
        $this->update_invoice_templates();
        $this->add_credit_note_prefix();
        $this->grant_users_to_access_new_permissions();
        $this->add_israel_country();
        $this->contact_company_category_fix();
        $this->change_days_off_in_uae();
        $this->write_log($this->log_path, 'End migration script');
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
                    if (in_array('/money_preferences/', $group_permission)) {
                        array_push($new_permissions['money'], '/setup/credit_note_number_prefix/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/invoice_export_to_word/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/credit_note_export_to_word/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/invoices_list/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/credit_notes/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/invoice_add/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/save_credit_note/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/delete_invoice/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/delete_credit_note/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/invoice_edit/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/edit_credit_note/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }

    public function discount_after_tax()
    {
        $this->write_log($this->log_path, 'Add Discount After Tax');

        $discount_set = $this->db->query("SELECT keyValue FROM system_preferences WHERE groupName = 'ActivateDiscountinInvoices'");
        $discount_setting = $discount_set->row_array();
        $discount_account_per_organization = [];
        if (!empty($discount_setting)) {
            $discounts = unserialize($discount_setting['keyValue']);
            foreach ($discounts as $organization_key => $organization) {
                $new_val = $organization['enabled'] == "invoice_level" ? "invoice_level_before_tax" : $organization['enabled'];
                $discount_account_per_organization[$organization_key] = ['enabled' => $new_val, 'account_id' => $organization['account_id']];
            }
            $res = serialize($discount_account_per_organization);
            $this->db->query("UPDATE system_preferences SET keyValue = '{$res}' WHERE groupName = 'ActivateDiscountinInvoices'");
        }
        $this->write_log($this->log_path, 'done from discount after tax');
    }

    public function update_invoice_templates()
    {
        $this->write_log($this->log_path, 'Start Updating Invoice Templates');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $template) {
            $settings = unserialize($template['settings']);
            if (isset($settings['body'])) {
                if (!isset($settings['body']['show']['qr-code'])) {
                    $settings['body']['show']['qr-code'] = false;
                }
                $template['settings'] = serialize($settings);
            }
            $this->db->query("update organization_invoice_templates set settings = '{$template['settings']}' WHERE id = {$template['id']}");
        }
        $this->write_log($this->log_path, 'End Updating Invoice Templates');
    }
    public function add_credit_note_prefix()
    {
        $this->write_log($this->log_path, 'Adding credit note prefix');
        $organizations = $this->db->query("SELECT id FROM organizations");
        $all_organizations = $organizations->result_array();
        $prefixes = [];
        foreach ($all_organizations as $organization) {
            $prefixes[$organization['id']] = "CN-";
        }
        $res = serialize($prefixes);
        $this->db->query("INSERT INTO system_preferences VALUES ('CreditNoteValues','creditNoteNumberPrefix','{$res}');");
       
        $this->write_log($this->log_path, 'Done from adding credit note prefix');
    }


    public function contact_company_category_fix(){
        $this->write_log($this->log_path, 'executing contact company category fix');

        if ($this->db->dbdriver === 'sqlsrv') {
            $query ="IF OBJECT_ID('dbo.companies_full_details', 'V') IS NOT NULL DROP VIEW dbo.companies_full_details;";
            $this->db->query($query);
            $query = "  CREATE VIEW companies_full_details AS SELECT
            TOP(9223372036854775800)
            companies.id, ('COM' + CAST( companies.id AS nvarchar )) AS companyID, companies.legalName, companies.name, companies.shortName,
            companies.foreignName, companies.status, companies.category, companies.company_category_id, contact_company_categories.name AS company_category,
            contact_company_categories.keyName  AS company_category_keyName,
            companies.company_sub_category_id, contact_company_sub_categories.name AS company_sub_category, companies.private,
            companies.company_id, companies.nationality_id, companies.company_legal_type_id,
            companies.object, companies.capital, companies.capitalCurrency, companies.nominalShares,
            companies.bearerShares, companies.shareParValue, companies.shareParValueCurrency,
            companies.qualifyingShares, companies.registrationNb, companies.registrationDate,
            companies.registrationCity, companies.registrationTaxNb, companies.registrationYearsNb,
            companies.registrationByLawNotaryPublic, companies.registrationByLawRef, companies.registrationByLawDate,
            companies.registrationByLawCity, companies.sharesLocation,
            companies.ownedByGroup, companies.sheerLebanese, companies.contributionRatio, companies.notes,
            companies.otherNotes, companies.createdOn, companies.createdBy, companies.modifiedOn, companies.modifiedBy,
            (modified.firstName + ' ' + modified.lastName) AS modifiedByName, (created.firstName + ' ' + created.lastName) AS createdByName,
            clt.name as legalType, cp.name as majorParent, lawyer=STUFF( (SELECT '; '+ (CASE WHEN cont.father!='' THEN cont.firstName + ' '+ cont.father + ' ' + cont.lastName ELSE cont.firstName+' '+cont.lastName END) from company_lawyers LEFT JOIN contacts cont ON cont.id = company_lawyers.lawyer_id where company_lawyers.company_id=companies.id FOR XML PATH('')), 1, 1, '') ,
            companyRegistrationAuthority.name as registrationAuthorityName, companies.internalReference, companies.crNumber, companies.crReleasedOn, companies.crExpiresOn,company_addresses.email FROM companies
                LEFT JOIN company_legal_types clt ON clt.id = companies.company_legal_type_id
                LEFT JOIN companies cp ON companies.company_id = cp.id
                LEFT JOIN contact_company_categories ON contact_company_categories.id = companies.company_category_id
                LEFT JOIN contact_company_sub_categories ON contact_company_sub_categories.id = companies.company_sub_category_id
                LEFT JOIN companies companyRegistrationAuthority ON companyRegistrationAuthority.id = companies.registrationAuthority AND companyRegistrationAuthority.category = 'Internal'
                LEFT JOIN company_addresses ON company_addresses.company = companies.id AND company_addresses.email IS NOT NULL
                LEFT JOIN user_profiles created ON created.user_id = companies.createdBy
                LEFT JOIN user_profiles modified ON modified.user_id = companies.modifiedBy;";
            $this->db->query($query);
            $query = "IF OBJECT_ID('dbo.contacts_grid', 'V') IS NOT NULL DROP VIEW dbo.contacts_grid;";
            $this->db->query($query);
            $query = " CREATE VIEW contacts_grid AS SELECT TOP(9223372036854775800) contacts.id, contacts.status, contacts.gender, contacts.title_id, contacts.firstName, contacts.lastName, CASE WHEN contacts.father!='' THEN contacts.firstName + ' '+ contacts.father + ' ' + contacts.lastName ELSE contacts.firstName+' '+contacts.lastName END AS fullName,
            contacts.foreignFirstName, contacts.foreignLastName, ISNULL(contacts.foreignFirstName, '') +  ' ' +  ISNULL(contacts.foreignLastName, '') AS foreignFullName, contacts.father, contacts.mother,
            contact_company_categories.keyName AS category_keyName,
            contacts.dateOfBirth, contacts.contact_category_id, contacts.contact_sub_category_id, contacts.jobTitle, contacts.private, contacts.isLawyer, contacts.lawyerForCompany, contacts.website,
            contacts.phone, contacts.fax, contacts.mobile, contacts.address1, contacts.address2, contacts.city, contacts.state, contacts.zip, contacts.country_id, contacts.comments, contacts.createdOn,
            (created.firstName + ' ' + created.lastName) AS createdByName, contacts.createdBy, contacts.modifiedOn,(modified.firstName + ' ' + modified.lastName) AS modifiedByName, contacts.modifiedBy, CAST(contacts.createdOn AS DATE) AS createdOnDate ,  'PER' +  CAST(contacts.id AS nvarchar) as contactID,
            contact_company_categories.name AS category, contact_company_sub_categories.name AS subCategory, contacts.internalReference AS internalReference, contacts.id_nb as id_nb,
            email=STUFF( (SELECT '; '+ contact_emails.email FROM contact_emails INNER JOIN contacts ct ON ct.id = contact_emails.contact_id  where contacts.id = contact_emails.contact_id  FOR XML PATH('')), 1, 1, ''),
            company=STUFF( (SELECT '; '+ companies.name FROM companies_contacts INNER JOIN companies ON companies.id = companies_contacts.company_id WHERE companies_contacts.contact_id = contacts.id FOR XML PATH('')), 1, 1, '')
            FROM contacts
                LEFT JOIN user_profiles created ON created.user_id = contacts.createdBy
                LEFT JOIN user_profiles modified ON modified.user_id = contacts.modifiedBy
                LEFT JOIN contact_company_categories ON contact_company_categories.id = contacts.contact_category_id
                LEFT JOIN contact_company_sub_categories ON contact_company_sub_categories.id = contacts.contact_sub_category_id;";
            $this->db->query($query);
        } else {
            $query = "CREATE OR REPLACE ALGORITHM = TEMPTABLE SQL SECURITY DEFINER VIEW `contacts_grid` AS SELECT `contacts`.`id`, `contacts`.`status`, `contacts`.`gender`, `contacts`.`title_id`, `contacts`.`firstName`, `contacts`.`lastName`, `contacts`.`father`, `contacts`.`foreignFirstName`, `contacts`.`foreignLastName`, `contacts`.`mother`, `contacts`.`dateOfBirth`, `contacts`.`contact_category_id`, `contacts`.`contact_sub_category_id`, `contacts`.`jobTitle`, `contacts`.`private`, `contacts`.`isLawyer`, `contacts`.`lawyerForCompany`, GROUP_CONCAT( `contact_emails`.`email` SEPARATOR '; ' ) AS email, `contacts`.`website`, `contacts`.`phone`, `contacts`.`fax`, `contacts`.`mobile`, `contacts`.`address1`, `contacts`.`address2`, `contacts`.`city`, `contacts`.`state`, `contacts`.`zip`, `contacts`.`country_id`, `contacts`.`comments`, `contacts`.`createdOn`, CONCAT(`created`.`firstName`, ' ', `created`.`lastName`) AS `createdByName`, `contacts`.`createdBy`, `contacts`.`modifiedOn`, CONCAT(`modified`.`firstName`, ' ', `modified`.`lastName`) AS `modifiedByName`, `contacts`.`modifiedBy`, `contacts`.`id_nb`, CASE WHEN contacts.father != '' THEN CONCAT(contacts.firstName, ' ', contacts.father, ' ', contacts.lastName) ELSE CONCAT(contacts.firstName, ' ', contacts.lastName) END AS `fullName`, CONCAT(IFNULL(`contacts`.`foreignFirstName`, ' '), ' ', IFNULL(`contacts`.`foreignLastName`, ' ')) AS `foreignFullName`,CONCAT('PER', `contacts`.`id`) as `contactID`, GROUP_CONCAT(`companies`.`name` SEPARATOR '; ') AS `company`, `contact_company_categories`.`name` AS `category`,`contact_company_categories`.`keyName` AS `category_keyName`, `contact_company_sub_categories`.`name` AS `subCategory`, `contacts`.`createdOn` AS `createdOnDate`, `contacts`.`internalReference` AS `internalReference` FROM `contacts` LEFT JOIN `user_profiles` `created` ON `created`.`user_id` = `contacts`.`createdBy` LEFT JOIN `user_profiles` `modified` ON `modified`.`user_id` = `contacts`.`modifiedBy` LEFT JOIN `companies_contacts` ON `companies_contacts`.`contact_id` = `contacts`.`id` LEFT JOIN `companies` ON `companies`.`id` = `companies_contacts`.`company_id` LEFT JOIN `contact_company_categories` ON `contact_company_categories`.`id` = `contacts`.`contact_category_id` LEFT JOIN `contact_company_sub_categories` ON `contact_company_sub_categories`.`id` = `contacts`.`contact_sub_category_id` LEFT JOIN `contact_emails` ON `contact_emails`.`contact_id` = `contacts`.`id` GROUP BY `contacts`.`id`;";
            $this->db->query($query);
            $query = "CREATE OR REPLACE ALGORITHM = TEMPTABLE SQL SECURITY DEFINER VIEW `companies_full_details` AS SELECT `companies`.`id`, CONCAT('COM', `companies`.`id`) as companyID, `companies`.`legalName`, `companies`.`name`, `companies`.`shortName`, `companies`.`foreignName`, `companies`.`status`, `companies`.`category`, companies.company_category_id, contact_company_categories.name  AS company_category,contact_company_categories.keyName  AS company_category_keyName, companies.company_sub_category_id, `contact_company_sub_categories`.`name` AS `company_sub_category`, `companies`.`private`, `companies`.`company_id`, `companies`.`nationality_id`, `companies`.`company_legal_type_id`, `companies`.`object`, `companies`.`capital`, `companies`.`capitalCurrency`, `companies`.`nominalShares`, `companies`.`bearerShares`, `companies`.`shareParValue`, `companies`.`shareParValueCurrency`, `companies`.`qualifyingShares`, `companies`.`registrationNb`, `companies`.`registrationDate`, `companies`.`registrationCity`, `companies`.`registrationTaxNb`, `companies`.`registrationYearsNb`, `companies`.`registrationByLawNotaryPublic`, `companies`.`registrationByLawRef`, `companies`.`registrationByLawDate`, `companies`.`registrationByLawCity`, `companies`.`sharesLocation`, `companies`.`ownedByGroup`, `companies`.`sheerLebanese`, `companies`.`contributionRatio`, `companies`.`notes`, `companies`.`otherNotes`, `companies`.`createdOn`, `companies`.`createdBy`, `companies`.`modifiedOn`, `companies`.`modifiedBy`, CONCAT(`created`.`firstName`, ' ', `created`.`lastName`)   AS `createdByName`, CONCAT(`modified`.`firstName`, ' ', `modified`.`lastName`) AS `modifiedByName`, `clt`.`name` as `legalType`, `cp`.`name` as `majorParent`, `companyRegistrationAuthority`.`name` as `registrationAuthorityName`, `companies`.`internalReference`, `companies`.`crNumber`, `companies`.`crReleasedOn`, `companies`.`crExpiresOn`, company_addresses.email, (select GROUP_CONCAT(CASE WHEN cont.father != '' THEN CONCAT(cont.firstName, ' ', cont.father, ' ', cont.lastName) ELSE CONCAT(cont.firstName, ' ', cont.lastName) END SEPARATOR ',') from company_lawyers LEFT JOIN `contacts` `cont` ON `cont`.`id` = `company_lawyers`.`lawyer_id` where company_lawyers.company_id = `companies`.`id`)  AS `lawyer` FROM `companies` LEFT JOIN `company_legal_types` `clt` ON `clt`.`id` = `companies`.`company_legal_type_id` LEFT JOIN `companies` `cp` ON `companies`.`company_id` = `cp`.`id` LEFT JOIN contact_company_categories ON contact_company_categories.id = companies.company_category_id LEFT JOIN `contact_company_sub_categories` ON `contact_company_sub_categories`.`id` = `companies`.`company_sub_category_id` LEFT JOIN `companies` `companyRegistrationAuthority` ON `companyRegistrationAuthority`.`id` = `companies`.`RegistrationAuthority` AND `companyRegistrationAuthority`.`category` = 'Internal' LEFT JOIN `company_addresses` ON `company_addresses`.`company` = `companies`.`id` AND company_addresses.email IS NOT NULL LEFT JOIN `user_profiles` `created` ON `created`.`user_id` = `companies`.`createdBy` LEFT JOIN `user_profiles` `modified` ON `modified`.`user_id` = `companies`.`modifiedBy`;";
            $this->db->query($query);
        }
    }
    public function add_israel_country(){
        $this->write_log($this->log_path, 'started add isreal country scripts');

        if ($this->db->dbdriver === 'sqlsrv') {
            $query1 = "INSERT INTO countries (countryCode, currencyCode, currencyName, isoNumeric, languages) VALUES ('IL', 'ILS', 'Israeli New Shekel', '092', 'en');";
            $query2 = " INSERT INTO countries_languages (country_id, language_id, name) VALUES (250, 1, 'Israel'),(250, 2, 'اسرائيل'),(250, 3, 'Israël'),(250, 4, 'Israel');";
        } else {
            $query2 = "INSERT INTO `countries_languages` (`id`, `country_id`, `language_id`, `name`) VALUES (1001, 252, 1, 'Israel'),(1002, 252, 2, 'اسرائيل'),(1003, 252, 3, 'Israël'),(1004, 252, 4, 'Israel');";
            $query1 = "INSERT INTO `countries` (`id`, `countryCode`, `currencyCode`, `currencyName`, `isoNumeric`, `languages`) VALUES (252, 'IL', 'ILS', 'Israeli New Shekel', '092', 'en-ILS,sn,nr,nd'); ";
        }
        $timezone = $this->db->query("SELECT keyValue FROM system_preferences WHERE keyName = 'systemTimezone'");
        $timezone_val = $timezone->row_array();
        if(!empty($timezone_val) && isset($timezone_val['keyValue']) && !empty($timezone_val['keyValue']) && $timezone_val['keyValue'] != 'Asia/Beirut'){
            $this->db->query($query1);
            $this->db->query($query2);
            $this->write_log($this->log_path, 'added israel country for different than asia/beirut time zone');
        }
    }
    public function change_days_off_in_uae()
    {
        $this->write_log($this->log_path, 'change days off in UAE');
        $timezone = $this->db->query("SELECT keyValue FROM system_preferences WHERE keyName = 'systemTimezone'");
        $timezone_val = $timezone->row_array();
        if(!empty($timezone_val) && isset($timezone_val['keyValue']) && !empty($timezone_val['keyValue']) && $timezone_val['keyValue'] == 'Asia/Dubai'){
            // change days off for UAE clients based on timezone Asia/Dubai
            $this->write_log($this->log_path, 'start changing days off');
            $this->db->query("UPDATE system_preferences  SET keyValue = 'Sun, Sat' WHERE keyName = 'sysDaysOff'");
        }
        $this->write_log($this->log_path, 'done from days off');
    }
}
