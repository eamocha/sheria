<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class System_preference extends My_Model
{
    protected $modelName = "system_preference";
    protected $_table = "system_preferences";
    protected $_listFieldName = "keyValue";
    protected $_pk = "keyName";
    protected $_fieldsNames = ["groupName", "keyName", "keyValue"];
    protected $allowedNulls = ["groupName", "keyValue"];
    protected $dashboardData = [];
    protected $validate = [];
    protected $morePreferences = [];
    protected $systemPreferences =[
        "DefaultValues" => ["archiveCaseStatus" => "", "archiveTaskStatus" => "", "boardMemberRoleId" => "", "caseContainerStatusId" => "", "caseDocumentStatusId" => "", "caseDocumentTypeId" => "", "caseContainerDocumentStatusId" => "", "caseContainerDocumentTypeId" => "", "caseTypeLitigationId" => "", "caseTypeProjectId" => "", "caseValueCurrency" => "", "companyDocumentStatusId" => "", "companyDocumentTypeId" => "", "companyLegalTypeId" => "", "companyCapitalVisualizeDecimals" => "", "contactCompanyCategoryId" => "", "contactCompanySubCategoryId" => "", "companyManageHideCorporateData" => "", "contactCountryId" => "", "contactDocumentStatusId" => "", "contactDocumentTypeId" => "", "contactNationalityId" => "", "shareholderVoteFactor" => "", "shareholderVoteYear" => "", "hearingReminderType" => "", "socialSecurityReminderType" => "", "providerGroupId" => "", "providerGroupIdCaseAssignee" => "", "reminderType" => "", "seniorityLevel" => "", "taskTypeId" => "", "roundUpTimeLogs" => "", "kpiUserGroups" => "", "gridsAdminUserGroups" => "", "ipStatus" => "", "ipRight" => "", "companySharesVisualizePreferredShares" => "", "caseMaxOpponents" => "", "disableArchivedMatters" => "", "onlyReporterEditMetaData" => "", "defaultNewTimeLogStatus" => "", "privacyPerAssignedTeam" => "", "copySummaryAndCommentsToPostponedHearing" => "", "taskPrivacyBasedOnMatterPrivacy" => "", "timeInternalStatus" => "", "matterCapAfterInvoicing" => "", "abilitySetLatestDevelopment" => "","defaultCompany"=>""],
        "Notifications" => ["notificationShowList" => NULL, "notificationIntervalDate" => NULL],
        "OutgoingMail" => ["outgoingMailSubjectPrefix" => "", "outgoingMailFromAddress" => "", "outgoingMailFromName" => "", "outgoingMailSmtpEncryption" => "", "outgoingMailSmtpHost" => "", "outgoingMailSmtpPasRequiresAuthentication" => "", "outgoingMailSmtpPass" => "", "outgoingMailSmtpPort" => "", "outgoingMailSmtpNameUser" => "", "outgoingMailTimeout" => "", "outgoingMailMailer" => "", "use_a4l_smtp" => ""],
        "Reminders" => ["reminderIntervalDate" => "", "reminderShowList" => ""],
        "SystemValues" => ["APIEnableStatus" => "", "AllowFeatureSLAManagement" => "", "sysDayEndOn" => "", "sysDayStartOn" => "", "sysDaysOff" => "", "businessDayEquals" => "", "businessWeekEquals" => "", "systemAdministrationGroupId" => "", "systemUserRateViewerGroupId" => "", "systemLanguage" => "", "systemTimezone" => "", "warningMessageOnLoginPage" => "", "staySignedIn" => "", "exportFilters" => "", "hijriCalendarConverter" => "", "hijriCalendarFeature" => "", "allowTimeEntryLoggingRule" => ""],
        "ActiveDirectory" => ["host" => NULL, "domain" => NULL, "port" => NULL, "username" => NULL, "password" => NULL, "adEnabled" => NULL, "loginWithoutDomain" => NULL, "ssoApp4legal" => NULL, "ssoApp4legalCustomerPortal" => NULL, "emailMappingOption" => NULL, "user_group_sync" => NULL, "user_multiple_groups" => NULL],
        "PasswordPolicy" => ["passwordDisallowedPrevious" => "", "passwordForceChange" => "", "passwordLockout" => "", "passwordMinimumLength" => "", "passwordStrongComplexity" => "", "loggedOutPeriod" => ""],
        "HearingVerificationProcess" => ["AllowFeatureHearingVerificationProcess" => 0, "HearingVerificationProcessUserGroups" => ""],
        "MakerCheckerControl" => ["makerCheckerFeatureStatus" => "", "userMakerGroups" => "", "userCheckerGroups" => ""],
        "CustomerPortalConfig" => ["AllowFeatureCustomerPortal" => "","enable_cplegal_opinions"=>"","cp_legal_opinions_label"=>"", "cpAppTitle" => "", "cpWelcomeMessage" => "", "cpFormLabel" => "", "cp_container_form_label" => "", "cpPasswordLockout" => "", "cpContactCategory" => "", "cp_contract_form_label" => "", "cp_signup_approval_type" => 0, "cp_signup_approval_user" => "", "cp_signup_accepted_domain" => "", "AllowAddContacts" => ""],
        "DocuSignIntegration" => ["docusign_client_id" => "", "docusign_client_secret" => "", "docusign_authorizationServer" => "", "docusign_template_subject" => "", "docusign_template_message" => ""],
        "webhooks" => ["webhooks_enabled" => 0, "enable_webhook_ssl_verification" => 0, "webhook_url_1" => "", "webhook_url_2" => ""],
        "MenuExternalLinks" => ["menu_url_1" => "", "menu_url_2" => "", "menu_url_3" => "", "menu_url_4" => "", "menu_url_5" => "", "menu_url_6" => "", "menu_url_7" => "", "menu_url_8" => "", "menu_url_9" => "", "menu_url_10" => ""],
        "ExternalLinks" => ["AllowInternalRefLink" => "", "InternalRefLink" => "", "AllowExternalCourtRef" => "", "ExternalCourtRefLink" => ""],
        "AdvisorConfig" => ["AllowFeatureAdvisor" => "", "advisorPasswordLockout" => "", "SharedDocumentsLegalCases" => NULL],
        "ContractDefaultValues" => ["archiveContractStatus" => "", "AllowContractSLAManagement" => "", "EnableContractRenewalFeature" => "","AutoCreateTaskOnNewContract"=>"","taskTypeIdOnNewContract"=>""],
        "AdditionalFeatures" => ["EnableCorrespondenceModule" => "","EnableTimeFeature" => "", "EnableOpinionsModule" => "", "EnableConveyancingModule" => "","EnableIntellectualPropertyModule" => "",    "EnableProsecutionModule" => "", "EnableOtherAgreementsModule" => "", "EnableLegislativeDraftingModule" => ""],
        "SMSGateway" => [ "smsAuthToken" => "", "smsPassword" => "", "smsUsername" => "", "smsFeatureEnabled" => "", "smsUrl" => "","smsAuthType"=>""],
        "mfa" => ["mfaEnabled" => "", "mfaChannel" => "", "otpExpiryMinutes" => "", "everyLoginRequireOTP" => "","oneDeviceLoggedInAtAtime"=>""]];
public function __construct()
    {
        parent::__construct();
        $this->ci->load->model("instance_data");
        $this->ci->instance_data_array = $this->ci->instance_data->get_values();
        $this->validate = ["keyName" => ["required" => true, "allowEmpty" => false, "message" => $this->ci->lang->line("empty")], "data" => ["required" => false, "allowEmpty" => true, "message" => $this->ci->lang->line("empty")]];
        $this->get_key_groups();
    }
    public function get_value_by_key($keyName)
    {
        if (!empty($keyName)) {
            return $this->load(["where" => ["keyName", $keyName]]);
        }
        return "";
    }
    public function get_values_by_group($group_name)
    {
        $response = [];
        $groups = $this->load_all(["where" => ["groupName", $group_name]]);
        foreach ($groups as $group) {
            $response[$group["keyName"]] = $group["keyValue"];
        }
        return $response;
    }
    public function set_value_by_key($groupName, $keyName, $keyValue)
    {
        if (method_exists($this, $keyName . "_write")) {
            $keyValue = $this->{$keyName . "_write"}($keyName, $keyValue);
        } else {
            if (is_array($keyValue)) {
                $keyValue = implode(", ", $keyValue);
            }
        }
        $dataSet = ["groupName" => $groupName, "keyName" => $keyName, "keyValue" => $keyValue];
        if ($this->insert_on_duplicate_key_update($dataSet, ["keyName"])) {
            $this->systemPreferences[$groupName][$keyName] = $keyValue;
            $this->ci->session->set_userdata("systemPreferences", $this->get_values());
            if ($keyName === "gridsAdminUserGroups") {
                $this->ci->session->set_userdata("AUTH_is_grid_admin", $this->ci->is_auth->userIsGridAdmin());
            }
            if (method_exists($this, $keyName . "_post_write")) {
                $this->{$keyName . "_post_write"}($keyName, $keyValue);
            }
            return true;
        }
        return false;
    }
    private function password_write($keyName, $keyValue)
    {
        $ci =& get_instance();
        $ci->load->library("encryption");
        if ($keyValue == "") {
            $old_values = $this->get_value_by_key($keyName);
            return $old_values["keyValue"];
        }
        return $ci->encryption->encrypt($keyValue);
    }
    private function outgoingMailSmtpPass_write($keyName, $keyValue)
    {
        $ci =& get_instance();
        $ci->load->library("encryption");
        if ($keyValue == "") {
            $old_values = $this->get_value_by_key($keyName);
            return $old_values["keyValue"];
        }
        return $ci->encryption->encrypt($keyValue);
    }
    private function outgoingMailSmtpPasRequiresAuthentication_post_write($keyName, $keyValue)
    {
        if ($keyValue == "no") {
            $dataSet = ["groupName" => "OutgoingMail", "keyName" => "outgoingMailSmtpPass", "keyValue" => ""];
            $this->insert_on_duplicate_key_update($dataSet, ["keyName"]);
        }
        return $keyValue;
    }
    public function get_values()
    {
        $keyValuePairs = [];
        $keyValuePairs = array_merge($keyValuePairs, $this->morePreferences);
        foreach ($this->systemPreferences as $groupName => $keyValues) {
            $keyValuePairs = array_merge($keyValuePairs, $keyValues);
        }
        return $keyValuePairs;
    }
    public function set_values_by_group_key($dataSet, $save = false)
    {
        if (false === $dataSet && true === $save) {
            return $this->insert_on_duplicate_update_batch($this->systemPreferences, $this->_pk);
        }
        $dataSet = array_map([$this, "implode_values"], $dataSet);
        ksort($this->systemPreferences);
        if (true === $save) {
            $response = $this->insert_on_duplicate_update_batch($dataSet, $this->_pk);
            $this->ci->session->set_userdata("AUTH_is_grid_admin", $this->ci->is_auth->userIsGridAdmin());
            return $response;
        }
    }
    private function implode_values(&$set)
    {
        if (!(isset($set["groupName"]) && isset($set["keyName"]) && isset($this->systemPreferences[$set["groupName"]])) || !array_key_exists($set["keyName"], $this->systemPreferences[$set["groupName"]])) {
            return false;
        }
        if (!isset($set["keyValue"])) {
            $set["keyValue"] = NULL;
        } else {
            if (method_exists($this, $set["keyName"] . "_write")) {
                $set["keyValue"] = $this->{$set["keyName"] . "_write"}($set["keyName"], $set["keyValue"]);
            } else {
                if (is_array($set["keyValue"])) {
                    $set["keyValue"] = implode(", ", $set["keyValue"]);
                }
            }
        }
        $this->systemPreferences[$set["groupName"]][$set["keyName"]] = $set["keyValue"];
        return $set;
    }
    public function get_key_groups()
    {
        $savedPreferences = $this->load_list(["order_by" => "groupName asc, keyName asc"], ["optgroup" => "groupName"]);
        foreach ($this->systemPreferences as $groupName => $keyValues) {
            if (isset($savedPreferences[$groupName])) {
                $savedPreferences[$groupName] = array_intersect_key($savedPreferences[$groupName], $keyValues);
                $this->systemPreferences[$groupName] = array_merge($this->systemPreferences[$groupName], $savedPreferences[$groupName]);
                unset($savedPreferences[$groupName]);
            }
            ksort($this->systemPreferences[$groupName]);
        }
        foreach ($savedPreferences as $groupName => $keyValues) {
            $this->morePreferences = array_merge($this->morePreferences, $savedPreferences[$groupName]);
        }
        ksort($this->systemPreferences);
        return $this->systemPreferences;
    }
    public function get_value($keyName, $groupName = "")
    {
        if (method_exists($this, $keyName . "_read")) {
            return $this->{$keyName . "_read"}($keyName, $groupName);
        }
        if (array_key_exists($groupName, $this->systemPreferences) && array_key_exists($keyName, $this->systemPreferences[$groupName])) {
            return $this->systemPreferences[$groupName][$keyName];
        }
        return false;
    }
    public function get_specified_system_preferences($preferences_key_name)
    {
        $result = NULL;
        if (!empty($preferences_key_name)) {
            $result_set = NULL;
            if (is_array($preferences_key_name)) {
                $result_set = $this->load_all(["where_in" => ["keyName", $preferences_key_name]]);
                if (!empty($result_set)) {
                    foreach ($result_set as $preference) {
                        $result[$preference["keyName"]] = $preference["keyValue"];
                    }
                }
            } else {
                $result_set = $this->load(["where" => ["keyName", $preferences_key_name]]);
                if (!empty($result_set)) {
                    $result = $result_set["keyValue"];
                }
            }
        }
        return $result;
    }
}

?>