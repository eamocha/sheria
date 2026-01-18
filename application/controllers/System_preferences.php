<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class System_preferences extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("system_preference");
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
    }
    private function decode($value)
    {
        $ci =& get_instance();
        $ci->load->library("encryption");
        return $ci->encryption->decrypt($value);
    }
    public function index()
    {
        if ($this->input->post(NULL)) {
            $data = $this->input->post("systemValues");
            if (empty($data)) {
                redirect("system_preferences");
            }
            $this->load->model("saml_configuration");
            $saml_configuration = $this->saml_configuration->get_values();
            $enabled_idp = $saml_configuration["idp"] == "none" ? 0 : 1;
            if ($data["adEnabled"]["keyValue"] == 1 && $enabled_idp == 1) {
                $this->set_flashmessage("error", sprintf($this->lang->line("idp_active_at_same_time"), $this->lang->line($saml_configuration["idp"])));
                redirect("system_preferences");
            } else {
                $this->load->model("instance_data");
                $this->instance_data->set_value_by_key("idp_enabled", $enabled_idp);
                $this->instance_data_array["idp_enabled"] = $enabled_idp;
                $plan_execluded_features = $this->plan_excluded_features ? explode(",", $this->plan_excluded_features) : [];
                foreach ($data as $data_key => $data_value) {
                    if (in_array($data_key, ["adEnabled"]) && $data_value["keyValue"] == 1 && !empty($plan_execluded_features) && in_array("LDAP-User-Management-Integration", $plan_execluded_features)) {
                        $data[$data_key]["keyValue"] = 0;
                    }
                    if (in_array($data_key, ["menu_url_1", "menu_url_2", "menu_url_3", "menu_url_4", "menu_url_5", "menu_url_6", "menu_url_7", "menu_url_8", "menu_url_9", "menu_url_10"])) {
                        $menu_url = $data_key;
                        $systemPreferences = $this->session->userdata("systemPreferences");
                        if (isset($systemPreferences[$menu_url])) {
                            $menu_links = @unserialize($systemPreferences[$menu_url]);
                        }
                        $menu_links = $data[$menu_url]["keyValue"];
                        $serialized_url = serialize($menu_links);
                        $data[$menu_url]["keyValue"] = $serialized_url;
                    }
                }
                $result = $this->system_preference->set_values_by_group_key($data, true);
            }
            if ($result) {
                $this->session->set_userdata("systemPreferences", $this->system_preference->get_values());
                $this->set_flashmessage("success", $this->lang->line("records_updated"));
            } else {
                $this->set_flashmessage("error", $this->lang->line("records_not_updated"));
            }
            redirect("system_preferences");
        } else {
            $data = [];
            $data["sysPreferences"] = $this->system_preference->get_key_groups();
            foreach ($data["sysPreferences"] as $groupName => $keyValues) {
                $groupNameHTMLOptions = form_input(["name" => "systemValues[%s][groupName]", "id" => "groupNameOf%s", "type" => "hidden", "value" => $groupName]);
                foreach ($keyValues as $keyName => $val) {
                    $data["formHTML"][$keyName] = sprintf($groupNameHTMLOptions, $keyName, $keyName) . form_input(["name" => "systemValues[" . $keyName . "][keyName]", "id" => $keyName, "type" => "hidden", "value" => $keyName]);
                    $data["formHTML"][$keyName] .= $this->get_from_html($keyName, $val);
                }
            }
            unset($keyName);
            unset($val);
            unset($data["sysPreferences"]["AdvisorConfig"]["SharedDocumentsLegalCases"]);
            $this->load->model("user_changes_authorization", "user_changes_authorizationfactory");
            $this->user_changes_authorization = $this->user_changes_authorizationfactory->get_instance();
            $data["hasPendingChangesInUsersGroupsData"] = $this->user_changes_authorization->has_pending_changes_in_users_groups_data();
            $this->includes("jquery/filterTable", "js");
            $this->includes("scripts/system_preferences", "js");
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("system_preferences"));
            $this->load->view("partial/header");
            $this->load->view("system_preferences/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function email_test_configurations()
    {
        if ($this->input->is_ajax_request()) {
            $ci =& get_instance();
            $config = [];
            $config["protocol"] = "smtp";
            $config["smtp_port"] = $this->input->post("smtpPort");
            $config["smtp_host"] = $this->input->post("smtpHost");
            $config["smtp_timeout"] = $this->input->post("timeOut");
            if ($this->input->post("smtpUser")) {
                $config["smtp_user"] = $this->input->post("smtpUser");
            }
            if ($this->input->post("smtpRequiresAuth") != "no") {
                if ($this->input->post("smtpPass")) {
                    $config["smtp_pass"] = $this->input->post("smtpPass");
                } else {
                    $systemPreferences = $this->session->userdata("systemPreferences");
                    if (!empty($systemPreferences["outgoingMailSmtpPass"])) {
                        $this->load->library("encryption");
                        $config["smtp_pass"] = $this->encryption->decrypt($systemPreferences["outgoingMailSmtpPass"]);
                    }
                }
            }
            $config["smtp_crypto"] = $this->input->post("smtpEncryption");
            $config["mailtype"] = "html";
            $config["charset"] = "utf-8";
            $config["newline"] = "\r\n";
            $subject = $this->lang->line("outgoing_mail_configuration_subject");
            $content = $this->lang->line("outgoing_mail_configuration_content");
            $this->load->library("email_notifications");
            $result = $this->email_notifications->send_email([$this->input->post("smtpUser")], $subject, $content);
            $response["status"] = $result ? true : false;
            $response["message"] = $result ? "Success" : "Failed";
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->set_flashmessage("error", $this->lang->line("invalid"));
            redirect("system_preferences");
        }
    }
    private function get_from_html($keyName, $keyValue)
    {
        if (method_exists($this, $keyName)) {
            return call_user_func([$this, $keyName], $keyValue);
        }
        if ($keyName === "docusign_template_message") {
            $form_html = form_textarea(["name" => "systemValues[" . $keyName . "][keyValue]", "id" => "value" . $keyName, "value" => $keyValue, "class" => "form-control"]);
        } else {
            $form_html = form_input(["name" => "systemValues[" . $keyName . "][keyValue]", "id" => "value" . $keyName, "type" => "text", "value" => $keyValue, "class" => "form-control"]);
        }
        return $form_html;
    }
    private function reminderIntervalDate($default)
    {
        return form_input(["name" => "systemValues[reminderIntervalDate][keyValue]", "id" => "valuereminderIntervalDate", "type" => "text", "value" => $default, "class" => "form-control"]) . " " . $this->lang->line("[D]");
    }
    private function businessDayEquals($default)
    {
        return form_input(["name" => "systemValues[businessDayEquals][keyValue]", "id" => "valuebusinessDayEquals", "type" => "text", "value" => $default, "class" => "form-control"]) . " " . $this->lang->line("[H]");
    }
    private function businessWeekEquals($default)
    {
        return form_input(["name" => "systemValues[businessWeekEquals][keyValue]", "id" => "valuebusinessWeekEquals", "type" => "text", "value" => $default, "class" => "form-control"]) . " " . $this->lang->line("[D]");
    }
    private function contactCountryId($default)
    {
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        return form_dropdown("systemValues[contactCountryId][keyValue]", $this->country->load_countries_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecontactCountryId\"");
    }

    private function contactNationalityId($default)
    {
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        return form_dropdown("systemValues[contactNationalityId][keyValue]", $this->country->load_countries_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecontactNationalityId\"");
    }
    private function defaultCompany($default)
    {
        $this->load->model("company", "companyfactory");
        $this->company = $this->companyfactory->get_instance();
        return form_dropdown("systemValues[defaultCompany][keyValue]", $this->company->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuedefaultCompany\"");

    }
    private function companyLegalTypeId($default)
    {
        $this->load->model("company_legal_type");
        return form_dropdown("systemValues[companyLegalTypeId][keyValue]", $this->company_legal_type->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecompanyLegalTypeId\"");
    }
    private function boardMemberRoleId($default)
    {
        $this->load->model("board_member_role");
        return form_dropdown("systemValues[boardMemberRoleId][keyValue]", $this->board_member_role->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valueboardMemberRoleId\"");
    }
    private function caseTypeLitigationId($default)
    {
        $this->load->model("case_type");
        return form_dropdown("systemValues[caseTypeLitigationId][keyValue]", $this->case_type->load_list(["where" => [["litigation", "yes"], ["isDeleted", 0]]], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecaseTypeLitigationId\"");
    }
    private function caseTypeProjectId($default)
    {
        $this->load->model("case_type");
        return form_dropdown("systemValues[caseTypeProjectId][keyValue]", $this->case_type->load_list(["where" => [["corporate", "yes"], ["isDeleted", 0]]], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecaseTypeProjectId\"");
    }
    private function taskTypeId($default)
    {
        $this->load->model("task_type", "task_typefactory");
        $this->task_type = $this->task_typefactory->get_instance();
        return form_dropdown("systemValues[taskTypeId][keyValue]", $this->task_type->load_list_per_language(), $default, "id=\"valuetaskTypeId\"");
    }
    private function taskTypeIdOnNewContract($default)
    {
        $this->load->model("task_type", "task_typefactory");
        $this->task_type = $this->task_typefactory->get_instance();
        return form_dropdown("systemValues[taskTypeIdOnNewContract][keyValue]", $this->task_type->load_list_per_language(), $default, "id=\"valuetaskTypeIdOnNewContract\"");
    }
    private function caseDocumentTypeId($default)
    {
        $this->load->model("case_document_type");
        return form_dropdown("systemValues[caseDocumentTypeId][keyValue]", $this->case_document_type->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecaseDocumentTypeId\"");
    }
    private function caseDocumentStatusId($default)
    {
        $this->load->model("case_document_status");
        return form_dropdown("systemValues[caseDocumentStatusId][keyValue]", $this->case_document_status->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecaseDocumentStatusId\"");
    }
    private function companyDocumentTypeId($default)
    {
        $this->load->model("company_document_type");
        return form_dropdown("systemValues[companyDocumentTypeId][keyValue]", $this->company_document_type->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecompanyDocumentTypeId\"");
    }
    private function companyDocumentStatusId($default)
    {
        $this->load->model("company_document_status");
        return form_dropdown("systemValues[companyDocumentStatusId][keyValue]", $this->company_document_status->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecompanyDocumentStatusId\"");
    }
    private function contactDocumentTypeId($default)
    {
        $this->load->model("contact_document_type");
        return form_dropdown("systemValues[contactDocumentTypeId][keyValue]", $this->contact_document_type->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecontactDocumentTypeId\"");
    }
    private function contactDocumentStatusId($default)
    {
        $this->load->model("contact_document_status");
        return form_dropdown("systemValues[contactDocumentStatusId][keyValue]", $this->contact_document_status->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecontactDocumentStatusId\"");
    }
    private function systemAdministrationGroupId($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[systemAdministrationGroupId][keyValue][]", $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]), $default, "id=\"valuesystemAdministrationGroupId\" multiple=\"multiple\"");
    }
    private function systemUserRateViewerGroupId($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[systemUserRateViewerGroupId][keyValue][]", $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]), $default, "id=\"valuesystemUserRateViewerGroupId\" multiple=\"multiple\"");
    }
    private function caseValueCurrency($default)
    {
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $selectedValues = $this->country->load_currency_list("currencyCode", "currency_country");
        return form_dropdown("systemValues[caseValueCurrency][keyValue]", $selectedValues, $default, "id=\"valuecaseValueCurrency\"");
    }
    private function archiveTaskStatus($default)
    {
        $this->load->model("task_status");
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[archiveTaskStatus][keyValue][]", $this->task_status->load_list(), $default, "id=\"valuearchiveTaskStatus\" multiple=\"multiple\"");
    }
    private function adEnabled($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[adEnabled][keyValue]", $array, $default, "id=\"valueadEnabled\"");
    }
    private function user_group_sync($default)
    {
        $array = ["no" => $this->lang->line("no"), "yes" => $this->lang->line("yes")];
        return form_dropdown("systemValues[user_group_sync][keyValue]", $array, $default, "id=\"valueuser_group_sync\"");
    }
    private function user_multiple_groups($default)
    {
        $array = ["no" => $this->lang->line("deny_access"), "yes" => $this->lang->line("first_matched_group")];
        return form_dropdown("systemValues[user_multiple_groups][keyValue]", $array, $default, "id=\"valueuser_multiple_groups\"");
    }
    private function loginWithoutDomain($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[loginWithoutDomain][keyValue]", $array, $default, "id=\"valueloginWithoutDomain\" onChange=\"checkUsernamesCompatibility();\"");
    }
    private function ssosheria360($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[ssosheria360][keyValue]", $array, $default, "id=\"valuessosheria360\"");
    }
    private function ssosheria360CustomerPortal($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[ssosheria360CustomerPortal][keyValue]", $array, $default, "id=\"valuessosheria360CustomerPortal\"");
    }
    private function JEnabled($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[JEnabled][keyValue]", $array, $default, "id=\"valueJEnabled\"");
    }
    private function archiveCaseStatus($default)
    {
        $this->load->model("workflow_status", "workflow_statusfactory");
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $statusesList = $this->workflow_status->loadStatusesUniqueList();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[archiveCaseStatus][keyValue][]", $statusesList, $default, "id=\"valuearchiveCaseStatus\" multiple=\"multiple\"");
    }
    private function matterCapAfterInvoicing($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[matterCapAfterInvoicing][keyValue]", $array, $default, "id=\"valuematterCapAfterInvoicing\"");
    }
    private function seniorityLevel($default)
    {
        $this->load->model("seniority_level");
        $seniority_Levels = $this->seniority_level->load_list([]);
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[seniorityLevel][keyValue][]", $seniority_Levels, $default, "id=\"valueseniorityLevel\"");
    }
    private function providerGroupId($default)
    {
        $this->load->model("provider_group");
        return form_dropdown("systemValues[providerGroupId][keyValue]", $this->provider_group->load_list([]), $default, "id=\"valueproviderGroupId\"");
    }
    private function outgoingMailSmtpHost($default)
    {
        return form_input(["name" => "systemValues[outgoingMailSmtpHost][keyValue]", "id" => "valueoutgoingMailSmtpHost", "type" => "text", "autocomplete" => "Off", "value" => $default, "class" => "form-control"]);
    }
    private function outgoingMailSubjectPrefix($default)
    {
        return form_input(["name" => "systemValues[outgoingMailSubjectPrefix][keyValue]", "id" => "valueoutgoingMailSubjectPrefix", "type" => "text", "autocomplete" => "Off", "value" => $default, "class" => "form-control"]);
    }
    private function outgoingMailSmtpPass($default)
    {
        return form_input(["name" => "systemValues[outgoingMailSmtpPass][keyValue]", "id" => "valueoutgoingMailSmtpPass", "type" => "password", "autocomplete" => "Off", "value" => "", "class" => "form-control"]);
    }
    private function password($default)
    {
        return form_input(["name" => "systemValues[password][keyValue]", "id" => "valuepassword", "type" => "password", "autocomplete" => "Off", "value" => "", "class" => "form-control"]);
    }
    private function passwordStrongComplexity($default)
    {
        return form_dropdown("systemValues[passwordStrongComplexity][keyValue]", ["yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valuepasswordStrongComplexity\"");
    }
    private function reminderType($default)
    {
        $this->load->model("reminder_type", "reminder_typefactory");
        $this->reminder_type = $this->reminder_typefactory->get_instance();
        return form_dropdown("systemValues[reminderType][keyValue]", $this->reminder_type->load_list_per_language(), $default, "id=\"valuereminderType\"");
    }
    private function hearingReminderType($default)
    {
        $this->load->model("reminder_type", "reminder_typefactory");
        $this->reminder_type = $this->reminder_typefactory->get_instance();
        return form_dropdown("systemValues[hearingReminderType][keyValue]", $this->reminder_type->load_list_per_language(), $default, "id=\"valuehearingReminderType\"");
    }
    private function socialSecurityReminderType($default)
    {
        $this->load->model("reminder_type", "reminder_typefactory");
        $this->reminder_type = $this->reminder_typefactory->get_instance();
        return form_dropdown("systemValues[socialSecurityReminderType][keyValue]", $this->reminder_type->load_list_per_language(), $default, "id=\"valuesocialSecurityReminderType\"");
    }
    private function notificationIntervalDate($default)
    {
        return form_input(["name" => "systemValues[notificationIntervalDate][keyValue]", "id" => "valuenotificationIntervalDate", "type" => "text", "value" => $default, "class" => "form-control"]) . " [D]";
    }
    private function sysDaysOff($default)
    {
        $days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[sysDaysOff][keyValue][]", array_combine($days, $days), $default, "id=\"valuesysDaysOff\" multiple=\"multiple\" required=\"\"");
    }
    private function sysDayStartOn($default)
    {
        return form_input(["name" => "systemValues[sysDayStartOn][keyValue]", "id" => "valuesysDayStartOn", "class" => "form-control", "type" => "text", "autocomplete" => "Off", "value" => $default, "class" => "form-control"]);
    }
    private function sysDayEndOn($default)
    {
        return form_input(["name" => "systemValues[sysDayEndOn][keyValue]", "id" => "valuesysDayEndOn", "class" => "form-control", "type" => "text", "autocomplete" => "Off", "value" => $default, "class" => "form-control"]);
    }
    private function systemLanguage($default)
    {
        $this->load->model("language");
        $languages = $this->language->loadAvailableLanguages();
        $translations = [];
        foreach ($languages as $value) {
            $translations[] = $this->lang->line($value);
        }
        array_unshift($languages, "");
        array_unshift($translations, $this->lang->line("not_set"));
        return form_dropdown("systemValues[systemLanguage][keyValue]", array_combine($languages, $translations), $default, "id=\"valuesystemLanguage\"");
    }
    private function ipStatus($default)
    {
        $this->load->model("ip_status");
        return form_dropdown("systemValues[ipStatus][keyValue]", $this->ip_status->load_list(), $default, "id=\"valueipStatus\"");
    }
    private function ipRight($default)
    {
        $this->load->model("intellectual_property_right");
        return form_dropdown("systemValues[ipRight][keyValue]", $this->intellectual_property_right->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valueipRight\"");
    }
    private function contactCompanyCategoryId($default)
    {
        $this->load->model("contact_company_category");
        return form_dropdown("systemValues[contactCompanyCategoryId][keyValue]", $this->contact_company_category->load_categories_per_lang("not_set"), $default, "id=\"valuecontactCompanyCategoryId\"");
    }
    private function contactCompanySubCategoryId($default)
    {
        $this->load->model("contact_company_sub_category");
        return form_dropdown("systemValues[contactCompanySubCategoryId][keyValue]", $this->contact_company_sub_category->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecontactCompanySubCategoryId\"");
    }
    private function caseContainerStatusId($default)
    {
        $this->load->model("legal_case_container_status");
        return form_dropdown("systemValues[caseContainerStatusId][keyValue]", $this->legal_case_container_status->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecaseContainerStatusId\"");
    }
    private function outgoingMailSmtpEncryption($default)
    {
        return form_dropdown("systemValues[outgoingMailSmtpEncryption][keyValue]", array_combine(["none", "tls", "ssl"], ["none", "tls", "ssl"]), $default, "id=\"valueoutgoingMailSmtpEncryption\"");
    }
    private function outgoingMailMailer($default)
    {
        return form_dropdown("systemValues[outgoingMailMailer][keyValue]", array_combine(["php_mailer_implementation", "ci_mailer_implementation"], ["PHP Mailer", "CI Mailer"]), $default, "id=\"valueoutgoingMailMailer\"");
    }
    private function use_a4l_smtp($default)
    {
        return form_dropdown("systemValues[use_a4l_smtp][keyValue]", ["yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueuse_a4l_smtp\"");
    }
    public function save_system_value()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("system_preferences");
        }
        $response = [];
        $system_value = $this->input->post("systemValue");
        $key_values = $system_value["keyValue"];
        $ad_enabled = $this->system_preference->get_specified_system_preferences("adEnabled");
        $this->load->model("saml_configuration");
        $saml_configuration = $this->saml_configuration->get_values();
        $enabled_idp = $saml_configuration["idp"] == "none" ? 0 : 1;
        $response["message"] = "";
        if ($system_value["keyName"] == "adEnabled" && $system_value["keyValue"] == 1 && $enabled_idp == 1) {
            $response["message"] = sprintf($this->lang->line("idp_active_at_same_time"), $this->lang->line($saml_configuration["idp"]));
            $response["status"] = 101;
        } else {
            $save_value = true;
            $plan_execluded_features = $this->plan_excluded_features ? explode(",", $this->plan_excluded_features) : [];
            if (in_array($system_value["keyName"], ["adEnabled"]) && $key_values == 1 && !empty($plan_execluded_features) && in_array("LDAP-User-Management-Integration", $plan_execluded_features)) {
                $response["message"] = $this->plan_feature_warning_msgs["LDAP-User-Management-Integration"] ?? $this->lang->line("you_do_not_have_enough_previlages_to_access_the_requested_feature");
                $response["status"] = 101;
                $save_value = false;
            }
            if (in_array($system_value["keyName"], ["menu_url_1", "menu_url_2", "menu_url_3", "menu_url_4", "menu_url_5", "menu_url_6", "menu_url_7", "menu_url_8", "menu_url_9", "menu_url_10"])) {
                $menu_url = $system_value["keyName"];
                $systemPreferences = $this->session->userdata("systemPreferences");
                if (isset($systemPreferences[$menu_url])) {
                    $menu_links = @unserialize($systemPreferences[$menu_url]);
                }
                $menu_links = $system_value["keyValue"];
                $response["status"] = $this->system_preference->set_value_by_key($system_value["groupName"], $system_value["keyName"], serialize($menu_links)) ? 202 : 101;
                $save_value = false;
            }
            if ($save_value) {
                $response["status"] = $this->system_preference->set_value_by_key($system_value["groupName"], $system_value["keyName"], $key_values) ? 202 : 101;
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function reset_expense_notification_system_params()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("system_preferences");
        }
        $response = [];
        $response["result"] = true;
        $status_group = $this->system_preference->set_value_by_key("ExpensesValues", "notifyUserGroupExpense", NULL) ? true : false;
        $status_users = $this->system_preference->set_value_by_key("ExpensesValues", "notifyUsersExpense", NULL) ? true : false;
        if ($status_group && $status_users) {
            $response["result"] = true;
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function systemTimezone($default)
    {
        $this->load->helper("timezone");
        $timezoneList = get_timezone();
        $timezoneList[""] = $this->lang->line("not_set");
        return form_dropdown("systemValues[systemTimezone][keyValue]", $timezoneList, $default, "id=\"valuesystemTimezone\"");
    }
    private function warningMessageOnLoginPage($default)
    {
        return "<textarea name=\"systemValues[warningMessageOnLoginPage][keyValue]\" id=\"valuewarningMessageOnLoginPage\" class=\"form-control\">" . $default . "</textarea>";
    }
    private function providerGroupIdCaseAssignee($default)
    {
        $systemPreferences = $this->session->userdata("systemPreferences");
        $usersList = [];
        if (isset($systemPreferences["providerGroupId"]) && !empty($systemPreferences["providerGroupId"])) {
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $usersList = $this->user->load_users_list($systemPreferences["providerGroupId"], ["key" => "id", "value" => "name", "firstLine" => ["" => $this->lang->line("not_set")]]);
        }
        return form_dropdown("systemValues[providerGroupIdCaseAssignee][keyValue]", $usersList, $default, "id=\"valueproviderGroupIdCaseAssignee\"");
    }
    private function staySignedIn($default)
    {
        return form_dropdown("systemValues[staySignedIn][keyValue]", ["yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valuestaySignedIn\"");
    }
    private function APIEnableStatus($default)
    {
        return form_dropdown("systemValues[APIEnableStatus][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueAPIEnableStatus\"");
    }
    private function makerCheckerFeatureStatus($default)
    {
        return form_dropdown("systemValues[makerCheckerFeatureStatus][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valuemakerCheckerFeatureStatus\"");
    }
    private function userMakerGroups($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[userMakerGroups][keyValue][]", $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]), $default, "id=\"valueuserMakerGroups\" multiple=\"multiple\"");
    }
    private function userCheckerGroups($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[userCheckerGroups][keyValue][]", $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]), $default, "id=\"valueuserCheckerGroups\" multiple=\"multiple\"");
    }
    private function companyCapitalVisualizeDecimals($default)
    {
        return form_dropdown("systemValues[companyCapitalVisualizeDecimals][keyValue]", [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")], $default, "id=\"valuecompanyCapitalVisualizeDecimals\"");
    }
    private function companySharesVisualizePreferredShares($default)
    {
        return form_dropdown("systemValues[companySharesVisualizePreferredShares][keyValue]", [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")], $default, "id=\"valuecompanySharesVisualizePreferredShares\"");
    }
    private function roundUpTimeLogs($default)
    {
        return form_dropdown("systemValues[roundUpTimeLogs][keyValue]", [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")], $default, "id=\"valueroundUpTimeLogs\"");
    }
    private function kpiUserGroups($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[kpiUserGroups][keyValue][]", $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]), $default, "id=\"valuekpiUserGroups\" multiple=\"multiple\"");
    }
    private function gridsAdminUserGroups($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[gridsAdminUserGroups][keyValue][]", $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]), $default, "id=\"valuegridsAdminUserGroups\" multiple=\"multiple\"");
    }
    private function AllowFeatureCustomerPortal($default)
    {
        return form_dropdown("systemValues[AllowFeatureCustomerPortal][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueAllowFeatureCustomerPortal\"");
    }
    private function AllowAddContacts($default)
    {
        return form_dropdown("systemValues[AllowAddContacts][keyValue]", ["yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueAllowAddContacts\"");
    }
    private function AllowFeatureSLAManagement($default)
    {
        return form_dropdown("systemValues[AllowFeatureSLAManagement][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueAllowFeatureSLAManagement\"");
    }
    private function AllowContractSLAManagement($default)
    {
        return form_dropdown("systemValues[AllowContractSLAManagement][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueAllowContractSLAManagement\"");
    }
    private function EnableContractRenewalFeature($default)
    {
        return form_dropdown("systemValues[EnableContractRenewalFeature][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableContractRenewalFeature\"");
    }
    private function AutoCreateTaskOnNewContract($default)
    {
        return form_dropdown("systemValues[AutoCreateTaskOnNewContract][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueAutoCreateTaskOnNewContract\"");
    }
    private function EnableCorrespondenceModule($default) {
        return form_dropdown("systemValues[EnableCorrespondenceModule][keyValue]", ["" => $this->lang->line("not_set"),"yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableCorrespondenceModule\"");
    }

    private function EnableTimeFeature($default) {
        return form_dropdown("systemValues[EnableTimeFeature][keyValue]", ["" => $this->lang->line("not_set"),"yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableTimeFeature\"");
    }

    private function EnableOpinionsModule($default) {
        return form_dropdown("systemValues[EnableOpinionsModule][keyValue]", ["" => $this->lang->line("not_set"),"yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableOpinionsModule\"");
    }

    private function EnableConveyancingModule($default) {
        return form_dropdown("systemValues[EnableConveyancingModule][keyValue]", ["" => $this->lang->line("not_set"),"yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableConveyancingModule\"");
    }
    private function EnableIntellectualPropertyModule($default) {
        return form_dropdown("systemValues[EnableIntellectualPropertyModule][keyValue]", ["" => $this->lang->line("not_set"),"yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableIntellectualPropertyModule\"");
    }

    private function EnableProsecutionModule($default) {
        return form_dropdown("systemValues[EnableProsecutionModule][keyValue]", ["" => $this->lang->line("not_set"),"yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableProsecutionModule\"");
    }

	private function EnableOtherAgreementsModule($default) {
		return form_dropdown("systemValues[EnableOtherAgreementsModule][keyValue]", ["" => $this->lang->line("not_set"),"yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableOtherAgreementsModule\"");
	}
	private function EnableLegislativeDraftingModule($default) {
		return form_dropdown("systemValues[EnableLegislativeDraftingModule][keyValue]", ["" => $this->lang->line("not_set"),"yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueEnableLegislativeDraftingModule\"");
	}
    private function hijriCalendarConverter($default)
    {
        return form_dropdown("systemValues[hijriCalendarConverter][keyValue]", ["" => $this->lang->line("not_set"), $this->lang->line("yes"), 0 => $this->lang->line("no")], $default, "id=\"valuehijriCalendarConverter\"");
    }
    private function hijriCalendarFeature($default)
    {
        return form_dropdown("systemValues[hijriCalendarFeature][keyValue]", ["" => $this->lang->line("not_set"), $this->lang->line("yes"), 0 => $this->lang->line("no")], $default, "id=\"valuehijriCalendarFeature\"");
    }
    private function AllowFeatureAdvisor($default)
    {
        return form_dropdown("systemValues[AllowFeatureAdvisor][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueAllowFeatureAdvisor\"");
    }
    private function copySummaryAndCommentsToPostponedHearing($default)
    {
        return form_dropdown("systemValues[copySummaryAndCommentsToPostponedHearing][keyValue]", [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")], $default, "id=\"valuecopySummaryAndCommentsToPostponedHearing\"");
    }
    public function email_notification_scheme()
    {
        $this->load->model("email_notification_scheme");
        $this->load->model("customer_portal_screen", "customer_portal_screenfactory");
        $this->customer_portal_screen = $this->customer_portal_screenfactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("notification_scheme"));
        if (!$this->input->is_ajax_request()) {
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $this->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
            $data["notifications_scheme"] = $this->email_notification_scheme->load_notifications_scheme();
            $users_emails = $this->user->load_active_emails();
            $data["users_emails"] = array_values($users_emails);
            $cp_emails = $this->customer_portal_users->load_active_emails();
            $data["cp_emails"] = array_values($cp_emails);
            foreach ($data["notifications_scheme"] as $index => $notify) {
                if ($this->license_package == "core_contract" || in_array($notify["trigger_action"], $this->email_notification_scheme->get($this->license_package . "_actions"))) {
                    if (mb_substr($notify["trigger_action"], 0, 13) === "request_type_") {
                        $request_type_id = $this->get_object_id_from_action("request_type_", $notify["trigger_action"]);
                        $this->customer_portal_screen->fetch($request_type_id);
                        $data["notifications_scheme"][$index]["trigger_name"] = $this->lang->line("customer_portal_screen") . ": <a href=\"" . app_url("customer_portal/portal_screen_edit/" . $request_type_id) . "\">" . $this->customer_portal_screen->get_field("name") . "</a>";
                    }
                    if (mb_substr($notify["trigger_action"], 0, 11) === "transition_") {
                        $transition_id = $this->get_object_id_from_action("transition_", $notify["trigger_action"]);
                        $transition_details = $this->email_notification_scheme->load_workflow_transition_notification_details($transition_id);
                        $url = app_url("manage_workflows/edit_status_transition/" . $transition_details["id"]);
                        $data["notifications_scheme"][$index]["trigger_name"] = $this->lang->line("transition") . ": <a href=\"javascript:;\" onclick=\"redirectToTab('" . $url . "','notifications')\">" . $transition_details["name"] . "</a><br/>" . $this->lang->line("workflow") . ": " . $transition_details["workflow_name"];
                    }
                    if (mb_substr($notify["trigger_action"], 0, 20) === "contract_transition_") {
                        $transition_id = $this->get_object_id_from_action("contract_transition_", $notify["trigger_action"]);
                        $transition_details = $this->email_notification_scheme->load_contract_workflow_transition_notification_details($transition_id);
                        $url = app_url("modules/contract/contract_workflows/edit_transition/" . $transition_details["id"]);
                        $data["notifications_scheme"][$index]["trigger_name"] = $this->lang->line("transition") . ": <a href=\"javascript:;\" onclick=\"redirectToTab('" . $url . "','notifications')\">" . $transition_details["name"] . "</a><br/>" . $this->lang->line("workflow") . ": " . $transition_details["workflow_name"];
                    }
                    if (mb_substr($notify["trigger_action"], 0, 4) === "sla_") {
                        $sla_id = 0; 
                        $sla_details = [];
                        if(mb_substr($notify["trigger_action"], 0, 13) === "sla_contract_"){ 
                            $sla_id = $this->get_object_id_from_action("sla_contract_", $notify["trigger_action"]);
                             $sla_details = $this->email_notification_scheme->load_sla_contract_details($sla_id);
                              $url = app_url("modules/contract/sla_management/edit/" . $sla_details["id"]);
                        }else{
                             $sla_id = $this->get_object_id_from_action("sla_", $notify["trigger_action"]);
                            $sla_details = $this->email_notification_scheme->load_sla_details($sla_id);
                             $url = app_url("sla_management/edit/" . $sla_details["id"]);
                        }                       
                       
                        $data["notifications_scheme"][$index]["trigger_name"] = $this->lang->line("sla") . ": <a href=\"javascript:;\" onclick=\"redirectToTab('" . $url . "','notifications')\">" . $sla_details["name"] . "</a><br/>" . $this->lang->line("workflow") . ": " . $sla_details["workflow_name"];
                    }
                    $notify_ccs = explode(";", $notify["notify_cc"]);
                    foreach ($notify_ccs as $notify_cc) {
                        if ($notify_cc && !in_array($notify_cc, $this->email_notification_scheme->get("predefinedValues"))) {
                            if (!in_array($notify_cc, $data["users_emails"]) && !in_array($notify["trigger_action"], $this->email_notification_scheme->get("cpTriggers"))) {
                                array_push($data["users_emails"], $notify_cc);
                            }
                            if (!in_array($notify_cc, $data["cp_emails"]) && in_array($notify["trigger_action"], $this->email_notification_scheme->get("cpTriggers"))) {
                                array_push($data["cp_emails"], $notify_cc);
                            }
                        }
                    }
                    $notify_to_emails = explode(";", $notify["notify_to"]);
                    foreach ($notify_to_emails as $notify_to_email) {
                        if (!in_array($notify_to_email, $this->email_notification_scheme->get("predefinedValues")) && $notify_to_emails) {
                            if (!in_array($notify_to_email, $data["users_emails"]) && !in_array($notify["trigger_action"], $this->email_notification_scheme->get("cpTriggers"))) {
                                array_push($data["users_emails"], $notify_to_email);
                            }
                            if (!in_array($notify_to_email, $data["cp_emails"]) && in_array($notify["trigger_action"], $this->email_notification_scheme->get("cpTriggers"))) {
                                array_push($data["cp_emails"], $notify_to_email);
                            }
                        }
                    }
                } else {
                    unset($data["notifications_scheme"][$index]);
                }
            }
            $arr1 = [];
            foreach ($data["users_emails"] as $key => $value) {
                $arr[$key]["email"] = $value;
            }
            if (!empty($arr)) {
                $data["users_emails"] = $arr;
            }
            foreach ($data["cp_emails"] as $key => $value) {
                $arr1[$key]["email"] = $value;
            }
            if (!empty($arr)) {
                $data["cp_emails"] = $arr1;
            }
            $this->load->view("partial/header");
            $this->load->view("notifications_scheme/index", $data);
            $this->load->view("partial/footer");
        } else {
            if (!$this->input->is_ajax_request()) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("dashboard");
            }
            $response = $unvalid_emails = [];
            $notify_cc_emails = $this->input->post("notifyCCEmails");
            $notify_to_emails = $this->input->post("notifyToEmails");
            $notify_checkbox = $this->input->post("notifyCheckbox");
            if ($notify_cc_emails) {
                $field = "notify_cc";
                $is_updated_notifiy_cc = $this->save_email_notifications($notify_cc_emails, $field);
                $response["notify_cc_result"] = $is_updated_notifiy_cc["notify_cc_result"];
                $response["unvalid_emails"] = $is_updated_notifiy_cc["unvalid_emails"];
                if ($is_updated_notifiy_cc["unvalid_emails"]) {
                    $unvalid_emails = $is_updated_notifiy_cc["unvalid_emails"];
                }
            }
            if ($notify_to_emails) {
                $field = "notify_to";
                $is_updated_notifiy_to = $this->save_email_notifications($notify_to_emails, $field);
                $response["notify_to_result"] = $is_updated_notifiy_to["notify_to_result"];
                if ($is_updated_notifiy_to["unvalid_emails"]) {
                    $unvalid_emails = $unvalid_emails ? $unvalid_emails + $is_updated_notifiy_to["unvalid_emails"] : $is_updated_notifiy_to["unvalid_emails"];
                }
            }
            if ($notify_checkbox) {
                $field = "hide_show_send_email_notification";
                foreach ($notify_checkbox as $checkbox) {
                    foreach ($checkbox as $key => $value) {
                        $response[$field . "_result"] = $this->email_notification_scheme->update_records($key, $value == "true" ? 1 : 0, $field);
                    }
                }
            }
            $response["unvalid_emails"] = $unvalid_emails;
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    private function get_object_id_from_action($object, $trigger_action)
    { 
        $ret=mb_substr($trigger_action, strlen($object), strlen($trigger_action)); 
        return $ret;
    }
    private function save_email_notifications($notifications_emails, $field)
    {
        $unvalid_emails = [];
        foreach ($notifications_emails as $notifications_email) {
            $fixed_value = "";
            $id = key($notifications_email);
            $emails = $notifications_email[$id];
            $emails = explode(";", $emails);
            $this->email_notification_scheme->fetch(["id" => $id]);
            $notifications = $this->email_notification_scheme->get_field($field);
            $trigger_action = $this->email_notification_scheme->get_field("trigger_action");
            $notify = explode(";", $notifications);
            foreach ($notify as $key => $val) {
                if (in_array($val, $this->email_notification_scheme->get("predefinedValues"))) {
                    $fixed_value .= $val . ";";
                }
            }
            $emails = array_filter($emails);
            if (!empty($emails)) {
                foreach ($emails as $key => $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        unset($emails[$key]);
                        $index = mb_substr($field, -2);
                        $unvalid_emails[$index . "-" . $id][$this->lang->line($trigger_action)][] = $email;
                    }
                }
            }
            $emails_to_insert = implode(";", $emails);
            $data = isset($fixed_value) && $fixed_value ? $fixed_value . $emails_to_insert : $emails_to_insert;
            $response[$field . "_result"] = $this->email_notification_scheme->update_records($id, $data, $field);
        }
        $response["unvalid_emails"] = isset($unvalid_emails) ? $unvalid_emails : "";
        return $response;
    }
    public function check_usernames_compatibility()
    {
        $response = [];
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $data["a4l_conflicting_users"] = $this->user->get_username_conflicted_users();
        $this->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
        $data["cp_conflicting_users"] = $this->customer_portal_users->get_username_conflicted_users();
        if (!empty($data["a4l_conflicting_users"]) || !empty($data["cp_conflicting_users"])) {
            $response["html"] = $this->load->view("system_preferences/incompatible_users", $data, true);
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function outgoingMailSmtpPasRequiresAuthentication($default)
    {
        return form_dropdown("systemValues[outgoingMailSmtpPasRequiresAuthentication][keyValue]", ["yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueoutgoingMailSmtpPasRequiresAuthentication\"");
    }
    private function emailMappingOption($default)
    {
        $array = ["mail" => $this->lang->line("mail"), "userPrincipalName" => $this->lang->line("user_principal_name")];
        return form_dropdown("systemValues[emailMappingOption][keyValue]", $array, $default, "id=\"valueemailMappingOption\"");
    }
    private function caseContainerDocumentTypeId($default)
    {
        $this->load->model("legal_case_container_document_type");
        return form_dropdown("systemValues[caseContainerDocumentTypeId][keyValue]", $this->legal_case_container_document_type->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecaseContainerDocumentTypeId\"");
    }
    private function caseContainerDocumentStatusId($default)
    {
        $this->load->model("legal_case_container_document_status");
        return form_dropdown("systemValues[caseContainerDocumentStatusId][keyValue]", $this->legal_case_container_document_status->load_list([], ["firstLine" => ["" => $this->lang->line("not_set")]]), $default, "id=\"valuecaseContainerDocumentStatusId\"");
    }
    private function disableArchivedMatters($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[disableArchivedMatters][keyValue]", $array, $default, "id=\"valuedisableArchivedMatters\"");
    }
    private function onlyReporterEditMetaData($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[onlyReporterEditMetaData][keyValue]", $array, $default, "id=\"valueonlyReporterEditMetaData\"");
    }
    private function webhooks_enabled($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[webhooks_enabled][keyValue]", $array, $default, "id=\"valuewebhooks_enabled\"");
    }
    private function enable_webhook_ssl_verification($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[enable_webhook_ssl_verification][keyValue]", $array, $default, "id=\"valueenable_webhook_ssl_verification\"");
    }
    private function webhook_url_1($default)
    {
        return form_input(["name" => "systemValues[webhook_url_1][keyValue]", "id" => "valuewebhook_url_1", "type" => "text", "value" => $default, "class" => "form-control", "style" => "width:50%"]) . " ";
    }
    private function webhook_url_2($default)
    {
        return form_input(["name" => "systemValues[webhook_url_2][keyValue]", "id" => "valuewebhook_url_2", "type" => "text", "value" => $default, "class" => "form-control", "style" => "width:50%"]) . " ";
    }
    private function companyManageHideCorporateData($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[companyManageHideCorporateData][keyValue]", $array, $default, "id=\"valuecompanyManageHideCorporateData\"");
    }
    private function cpContactCategory($default)
    {
        $this->load->model("contact_company_category");
        return form_dropdown("systemValues[cpContactCategory][keyValue]", $this->contact_company_category->load_categories_per_lang("not_set"), $default, "id=\"valuecpContactCategory\"");
    }
    private function cp_signup_approval_type($default)
    {
        $array = [$this->lang->line("approved_by_default"), $this->lang->line("approved_by_user")];
        return form_dropdown("systemValues[cp_signup_approval_type][keyValue]", $array, $default, "id=\"valuecp_signup_approval_type\"");
    }
    private function cp_signup_approval_user($default)
    {
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $array = ["" => "---"] + $this->user->load_users_list();
        return form_dropdown("systemValues[cp_signup_approval_user][keyValue]", $array, $default, "id=\"valuecp_signup_approval_user\"");
    }
    private function defaultNewTimeLogStatus($default)
    {
        $values = [$this->lang->line("billable"), $this->lang->line("non-billable")];
        return form_dropdown("systemValues[defaultNewTimeLogStatus][keyValue]", $values, $default, "id=\"valuedefaultNewTimeLogStatus\"");
    }
    private function privacyPerAssignedTeam($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[privacyPerAssignedTeam][keyValue]", $array, $default, "id=\"valueprivacyPerAssignedTeam\"");
    }
    private function exportFilters($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[exportFilters][keyValue]", $array, $default, "id=\"valueexportFilters\"");
    }
    private function AllowInternalRefLink($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[AllowInternalRefLink][keyValue]", $array, $default, "id=\"valueAllowInternalRefLink\"");
    }
    private function AllowExternalCourtRef($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[AllowExternalCourtRef][keyValue]", $array, $default, "id=\"valueAllowExternalCourtRef\"");
    }
    private function menu_url_1($default)
    {
        return $this->menu_external_links("menu_url_1", $default);
    }
    private function menu_url_2($default)
    {
        return $this->menu_external_links("menu_url_2", $default);
    }
    private function menu_url_3($default)
    {
        return $this->menu_external_links("menu_url_3", $default);
    }
    private function menu_url_4($default)
    {
        return $this->menu_external_links("menu_url_4", $default);
    }
    private function menu_url_5($default)
    {
        return $this->menu_external_links("menu_url_5", $default);
    }
    private function menu_url_6($default)
    {
        return $this->menu_external_links("menu_url_6", $default);
    }
    private function menu_url_7($default)
    {
        return $this->menu_external_links("menu_url_7", $default);
    }
    private function menu_url_8($default)
    {
        return $this->menu_external_links("menu_url_8", $default);
    }
    private function menu_url_9($default)
    {
        return $this->menu_external_links("menu_url_9", $default);
    }
    private function menu_url_10($default)
    {
        return $this->menu_external_links("menu_url_10", $default);
    }
    private function menu_external_links($key, $value)
    {
        $value = (array) (empty($value) ? array_fill_keys(["title", "target"], "") : unserialize($value));
        if (isset($value[0])) {
            $value[""] = $value[0];
            unset($value[0]);
        }
        return "<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][title]", "id" => "value" . $key, "type" => "text", "placeholder" => $this->lang->line("custom_link_title_placeholder"), "value" => $value["title"], "class" => "form-control keyName" . $key]) . "</span>\r\n\t\t\t\t<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][target]", "id" => "value" . $key, "type" => "text", "placeholder" => $this->lang->line("custom_link_url_placeholder"), "value" => $value["target"], "class" => "form-control keyName" . $key]) . "</span >";
    }
    private function AllowFeatureHearingVerificationProcess($default)
    {
        return form_dropdown("systemValues[AllowFeatureHearingVerificationProcess][keyValue]", ["" => $this->lang->line("not_set"), "yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default, "id=\"valueAllowFeatureHearingVerificationProcess\"");
    }
    private function HearingVerificationProcessUserGroups($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[HearingVerificationProcessUserGroups][keyValue][]", $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["id NOT IN (" . $this->user->get("systemAdministrationGroupId") . ")", NULL, false], ["needApprovalOnAdd !=", "1"]]]), $default, "id=\"valueHearingVerificationProcessUserGroups\" multiple=\"multiple\"");
    }
    private function taskPrivacyBasedOnMatterPrivacy($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[taskPrivacyBasedOnMatterPrivacy][keyValue]", $array, $default, "id=\"valuetaskPrivacyBasedOnMatterPrivacy\"");
    }
    private function timeInternalStatus($default)
    {
        $this->load->model("time_internal_statuses_language", "time_internal_statuses_languagefactory");
        $this->time_internal_statuses_language = $this->time_internal_statuses_languagefactory->get_instance();
        return form_dropdown("systemValues[timeInternalStatus][keyValue]", $this->time_internal_statuses_language->load_list_per_language(), $default, "id=\"valuetimeInternalStatus\"");
    }
    private function abilitySetLatestDevelopment($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[abilitySetLatestDevelopment][keyValue]", $array, $default, "id=\"valueabilitySetLatestDevelopment\"");
    }
    private function archiveContractStatus($default)
    {
        $this->load->model("contract_status_language", "contract_status_languagefactory");
        $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
        $statusesList = $this->contract_status_language->load_list_per_language();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[archiveContractStatus][keyValue][]", $statusesList, $default, "id=\"valuearchiveContractStatus\" multiple=\"multiple\"");
    }
    private function allowTimeEntryLoggingRule($default)
    {
        $default = explode(", ", $default);
        $dropdown = form_dropdown("systemValues[allowTimeEntryLoggingRule][keyValue][]", ["yes" => $this->lang->line("yes"), "no" => $this->lang->line("no")], $default[0], "id=\"valueallowTimeEntryLoggingRule\"");
        $date_input = form_input(["name" => "systemValues[allowTimeEntryLoggingRule][keyValue][]", "type" => "date", "id" => "value2allowTimeEntryLoggingRule", "placeholder" => "YYYY-MM-DD", "value" => $default[1], "class" => "form-control mt-2 col-md-7", "data-validation-engine" => "validate[required]"]);
        $dropdown_div = "<div class=\"row\"><span class=\"col-md-3\">" . $this->lang->line("activate") . ": </span>" . $dropdown . "</div>";
        $date_div = "<div class=\"row\"><span class=\"col-md-3 pt-3 pr-0\">" . $this->lang->line("activation_date") . "</span>" . $date_input . "</div>";
        return $dropdown_div . $date_div;
    }
    //multifactor auth
    private function mfaEnabled($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[mfaEnabled][keyValue]", $array, $default, "id=\"valuemfaEnabled\"");
    }
    private function mfaChannel($default)
    {
        $array = [1 => $this->lang->line("sms"), 2 => $this->lang->line("email"), 3 => $this->lang->line("both"), 0 => $this->lang->line("not_set")];
        return form_dropdown("systemValues[mfaChannel][keyValue]", $array, $default, "id=\"valuemfaChannel\"");
    }
    private function oneDeviceLoggedInAtAtime($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[oneDeviceLoggedInAtAtime][keyValue]", $array, $default, "id=\"valueoneDeviceLoggedInAtAtime\"");
    }
    private function smsFeatureEnabled($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[smsFeatureEnabled][keyValue]", $array, $default, "id=\"valuesmsFeatureEnabled\"");
    }
    private function everyLoginRequireOTP($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[everyLoginRequireOTP][keyValue]", $array, $default, "id=\"valueeveryLoginRequireOTP\"");
    }
    private function smsPassword($default)
    {
        return form_input(["name" => "systemValues[smsPassword][keyValue]", "id" => "valuesmsPassword", "type" => "smsPassword", "autocomplete" => "Off", "value" => "", "class" => "form-control"]);
    }
    private function smsAuthType($default)
    {
        $array = [1 => $this->lang->line("no_auth"), 2 => $this->lang->line("basic_auth"), 3 => $this->lang->line("api_key"), 0 => $this->lang->line("bearer_token")];
        return form_dropdown("systemValues[smsAuthType][keyValue]", $array, $default, "id=\"valuesmsAuthType\"");
    }

    public function add_remove_matter_contributors_from_trigger()
    {
        $step = $this->input->post("step");
        if ($step == "confirm_message") {
            $data = [];
            $data["step"] = "confirm_message";
            $response = [];
            $data["trigger"] = $this->input->post("trigger");
            $data["action"] = $this->input->post("action");
            $response["html"] = $this->load->view("notifications_scheme/add_remove_matter_contributors_dialog", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            if ($step == "confirm_action") {
                $this->load->model("email_notification_scheme");
                $trigger = $this->input->post("trigger");
                $action = $this->input->post("action");
                $this->email_notification_scheme->reset_fields();
                $this->email_notification_scheme->fetch(["trigger_action" => $trigger]);
                $notify_to = $this->email_notification_scheme->get_field("notify_to");
                if ($action == "remove") {
                    $notify_to = str_replace(";matter_contributors", "", $notify_to);
                } else {
                    $notify_to .= ";matter_contributors";
                }
                $this->email_notification_scheme->set_field("notify_to", $notify_to);
                $result = $this->email_notification_scheme->update();
                if ($result) {
                    $this->set_flashmessage("success", $this->lang->line($action == "remove" ? "remove_matter_contributors_success" : "add_matter_contributors_success"));
                } else {
                    $this->set_flashmessage("error", $this->lang->line($action == "remove" ? "remove_matter_contributors_failure" : "add_matter_contributors_failure"));
                }
                redirect("system_preferences/email_notification_scheme");
            }
        }
    }

}

