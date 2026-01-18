<?php


if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
require_once INSTANCE_PATH . "saml/lib/_autoload.php";
class Users extends Top_controller
{
    public $maxPassword = 25;
    public $accessMessage = "";
    public $User;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $system_preference = $this->session->userdata("systemPreferences");
    }
    public function autocomplete($active = "")
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $this->load->model("user_profile");
        $term = $this->input->get("term");
        $results = $this->user->lookup($term, $active);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    public function fetch()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $id = $this->input->post("id");
        $this->user->fetch($id);
        $response["record"] = $this->user->get_fields();
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function index()
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->user->k_load_all_users($filter, $sortable);
            } else {
                $response["records"] = $this->user->k_load_all_users($filter, $sortable);
                $response["html"] = $this->load->view("users/search_results", $response["records"], true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
         //   $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("users"));
            $this->load->model(["user_profile", "provider_group"]);
            $this->load->model("user_group", "user_groupfactory");
            $this->user_group = $this->user_groupfactory->get_instance();
            $this->load->model("country", "countryfactory");
            $this->country = $this->countryfactory->get_instance();
            $this->load->model("system_preference");
            $system_preference = $this->system_preference->get_key_groups();
            $this->load->model("saml_configuration");
            $saml_configuration = $this->saml_configuration->get_values();
            $enabled_idp = $saml_configuration["idp"] == "none" ? false : $saml_configuration["idp"];
            $enabled_idp = $this->instance_data_array["installationType"] == "on-cloud" ? $enabled_idp : false;
            $this->load->model("instance_data");
            $data["instance_data"] = $this->instance_data->get_values();
            $data["countries"] = $this->country->load_countries_list();
            $data["userGroups"] = $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]);
            $data["providerGroups"] = $this->provider_group->load_list(["where" => ["allUsers !=", 1]], ["firstLine" => ["" => $this->lang->line("all")]]);
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $data["license"] = $this->user->load_users_license_details();
            $data["isLawyerValues"] = array_combine($this->user_profile->get("isLawyerValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["pendingApprovalValues"] = array_combine($this->user_profile->get("pendingApprovalValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["bannedValues"] = array_combine($this->user->get("bannedValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
            $data["statuses"] = array_combine($this->user_profile->get("statusValues"), ["", $this->lang->line("Active"), $this->lang->line("Inactive")]);
            $data["userDirectories"] = array_combine(["", 0, 1, "azure_ad", "onelogin"], ["", $this->lang->line("LocalDirectory"), $this->lang->line("ActiveDirectory"), $this->lang->line("AzureDirectory"), $this->lang->line("onelogin")]);
            $data["userTypes"] = array_combine(["", "core", "contract", "both"], ["", $this->lang->line("core"), $this->lang->line("contract"), $this->lang->line("both")]);
            $this->load->model("seniority_level");
            $data["seniority_Levels"] = $this->seniority_level->load_list([]);
            $this->load->model("system_preference");
            $api_data = $this->system_preference->get_value_by_key("APIEnableStatus");
            $data["api_is_enabled"] = $api_data["keyValue"] == "yes" ? true : false;
            $active_directory_api_data = $this->system_preference->get_value_by_key("adEnabled");
            $data["active_directory_is_enabled"] = $active_directory_api_data["keyValue"] == 1 ? true : false;
            $data["isUserMaker"] = $this->is_auth->user_is_maker();
            $data["isUserChecker"] = $this->is_auth->user_is_checker();
            $data["makerCheckerFeatureStatus"] = $this->system_preference->get_value_by_key("makerCheckerFeatureStatus");
            $data["makerCheckerFeatureStatus"] = $data["makerCheckerFeatureStatus"]["keyValue"] == "yes" ? true : false;
            $data["enabled_idp"] = $enabled_idp;
            $data["can_add_user"] = $this->user_profile->abilityToIncreaseLicenseUsers();
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("scripts/users_search", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("users/index", $data);
        }
    }
    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            $this->save(0);
        } else {
            $this->add_user_dialog();
        }
    }
    private function save($id = "0", $view = "edit")
    {
        $data = [];
        $isUserMaker = $this->is_auth->user_is_maker();
        $data["license"] = $this->user->load_users_license_details();
        $this->load->model(["provider_group", "seniority_level", "user_profile", "provider_group_user"]);
        $this->load->model("user_group", "user_groupfactory");
        $this->load->model("department");

        $this->user_group = $this->user_groupfactory->get_instance();
        $this->user_profile->reset_fields();
        $data["can_add_user"] = $this->user_profile->abilityToIncreaseLicenseUsers();
        $this->user_group->fetch(["name" => $this->user_group->get("superAdminInfosystaName")]);
        $superAdminInfosystaId = $this->user_group->get_field("id");
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $userFormHasChanged = true;
        $mv_data = [];
        if ($view == "profile") {
            $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
            $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
        }
        if ($this->input->post(NULL)) {
            if ($this->input->post("provider_groups_users")) {
                $provider_groups_users = $this->input->post("provider_groups_users");
                unset($_POST["provider_groups_users"]);
            } else {
                $provider_groups_users = false;
            }
            $userData = [];
            if ($this->input->post("userData")) {
                $userData = $this->input->post("userData");
                $userData["password"] = $this->input->post("userData[password]", false);
                $password_used = $userData["password"];
                $userData["confirmPassword"] = $this->input->post("userData[confirmPassword]", false);
            }
            if (isset($userData["email"]) && $this->cloud_installation_type == "on-cloud") {
                $userData["username"] = $userData["email"];
            }
            unset($userData["isAd"]);
            unset($_POST["userData"]);
            if (!$this->input->post("status") || $this->input->post("status") == "") {
                if (empty($id) && $isUserMaker) {
                    $_POST["flagNeedApproval"] = "1";
                    $_POST["status"] = "Inactive";
                    $userData["banned"] = "1";
                } else {
                    $_POST["status"] = "Active";
                    $userData["banned"] = "0";
                }
            } else {
                if (empty($id)) {
                    $userData["banned"] = $this->input->post("status") == "Active" ? "0" : "1";
                } else {
                    if ($this->input->post("status") == "Inactive") {
                        $userData["banned"] = "1";
                        if (!$isUserMaker) {
                            $this->user->destroy_session_user_id($this->input->post("id"));
                        }
                    } else {
                        if ($this->input->post("status") == "Active") {
                            $userData["banned"] = "0";
                        }
                    }
                }
            }
            $this->provider_group->set_fields($provider_groups_users);
            $audit_arr = ["user_id" => "", "action" => "", "fieldName" => "", "dataBefore" => NULL, "dataAfter" => "", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->is_auth->get_user_id()];
            $user_changes_arr = [];
            $userProfile_changes_arr = [];
            if (isset($userData["user_group_id"]) && $userData["user_group_id"] == $superAdminInfosystaId && $this->session->userdata("AUTH_user_group_id") != $superAdminInfosystaId) {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("information", $this->lang->line("permission_not_allowed"));
                    redirect("users");
                } else {
                    $response["status"] = false;
                    $response["message"] = $this->lang->line("permission_not_allowed");
                }
            }
            $addFormFlag = 0;
            $user_profile_validations = $this->user_profile->get("validate");
            $user_profile_validations["user_code"]["required"] = ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 2], "message" => sprintf($this->lang->line("min_length_rule"), $this->lang->line("user_code"), 2)];
            $user_profile_validations["user_code"]["isUnique"] = ["rule" => "isUnique", "message" => $this->lang->line("already_exists")];
            $this->user_profile->set("validate", $user_profile_validations);
            $access_type = $this->input->post("access_type", true);
            if ($access_type) {
                $type = "core";
                if (in_array("contract", $access_type)) {
                    $type = "contract";
                    if (in_array("core", $access_type)) {
                        $type = "both";
                    }
                }
            }
            if (empty($id)) {
                $addFormFlag = 1;
                $userData["password"] = password_hash($userData["password"], PASSWORD_DEFAULT);
                $userData["created"] = date("Y-m-d H:i:s", time());
                $userData["modified"] = date("Y-m-d H:i:s", time());
                $userData["modifiedBy"] = $this->is_auth->get_user_id();
                $userData["isAd"] = 0;
                $this->user->set_fields($userData);
                $this->user->set_field("type", $type ?? "core");
                $_POST["overridePrivacy"] = "no";
                $this->user_profile->set_fields($this->input->post(NULL));
                $this->user_profile->validate();
                $user_profile_validation = $this->user_profile->get("validationErrors");
                $is_user_id_the_only_error = count($user_profile_validation) == 1 && key($user_profile_validation) == "user_id";
                if ($is_user_id_the_only_error && $this->user->insert()) {
                    $id = $this->user->get_field("id");
                    unset($_POST["id"]);
                    $this->user_profile->set_field("user_id", $this->user->get_field("id"));
                    if ($this->user_profile->insert()) {
                        $getting_started["show"] = true;
                        $this->user_preference->set_value("getting_started", serialize($getting_started), true, $id);
                        $this->user->relateToCC($this->user->get_field("email"));
                        if ($isUserMaker) {
                            $newChangesData = array_merge($userData, $this->input->post(NULL));
                            $newChangesData["provider_group"] = $provider_groups_users;
                            $this->user->insert_user_changes_authorization("add", $this->user->get_field("id"), $newChangesData);
                        } else {
                            $this->user->touch_logs("insert", []);
                            $this->user_profile->touch_logs("insert", []);
                            $this->user->duplicate_user_to_contact();
                        }
                        $result = true;
                    } else {
                        $this->db->where("id", $this->user->get_field("id"));
                        $this->db->delete("users");
                        $id = 0;
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            } else {
                $oldUserValues = $this->user->get_old_values($id);
                if ($oldUserValues["isAd"] == "1") {
                    $userData["isAd"] = 1;
                } else {
                    $userData["isAd"] = 0;
                }
                $oldUserProfileValues = $this->user_profile->get_old_values($id);
                $this->user->fetch($id);
                $this->user_profile->fetch(["user_id" => $id]);
                $mv_data = ["firstName" => ["old" => $this->user_profile->get_field("firstName"), "new" => $this->input->post("firstName")], "lastName" => ["old" => $this->user_profile->get_field("lastName"), "new" => $this->input->post("lastName")]];
                unset($_POST["user_id"]);
                unset($_POST["id"]);
                if (empty($userData["password"])) {
                    unset($userData["password"]);
                } else {
                    $userData["password"] = password_hash($userData["password"], PASSWORD_DEFAULT);
                }
                if ($view == "profile") {
                    $data["workthrough"] = [];
                    $userData["user_group_id"] = $this->user->get_field("user_group_id");
                    $userData["email"] = $this->user->get_field("email");
                    $walkthrough_post = $this->input->post("workthrough");
                    if (!empty($walkthrough_post) && is_array($this->input->post("workthrough"))) {
                        $workthrough_arr = [];
                        foreach ($walkthrough_post as $key => $workthrough) {
                            if ($workthrough == 1) {
                                array_push($workthrough_arr, $key);
                            }
                        }
                        if (!empty($workthrough_arr)) {
                            $userData["workthrough"] = serialize($workthrough_arr);
                        } else {
                            $userData["workthrough"] = "";
                        }
                        $data["workthrough"] = $workthrough_arr;
                    }
                }
                $this->user->set_fields($userData);
                $this->user_profile->set_fields($this->input->post(NULL));
                $this->user->set_field("modified", date("Y-m-d H:i:s"));
                $this->user->set_field("modifiedBy", $this->is_auth->get_user_id());
                $this->user->set_field("type", $type ?? $this->user->get_field("type"));
                if ($isUserMaker) {
                    if (($result = $this->user->validate()) && ($result = $this->user_profile->validate())) {
                        $newChangesData = array_merge($userData, $this->input->post(NULL));
                        $oldValuesData = array_merge($oldUserValues, $oldUserProfileValues);
                        $newChangesData["provider_group"] = $provider_groups_users;
                        $userFormHasChanged = $this->user->insert_user_changes_authorization("editForm", $id, $newChangesData, $oldValuesData);
                    }
                } else {
                    if ($this->user->validate() && $this->user_profile->validate() && $this->user->update()) {
                        if (isset($userData["workthrough"])) {
                            $this->session->set_userdata("workthrough", $userData["workthrough"]);
                            $this->workthrough = $userData["workthrough"];
                        }
                        if ($this->user_profile->update()) {
                            if ($this->user->get_field("email") != $oldUserValues["email"]) {
                                $this->user->relateToCC($this->user->get_field("email"), $oldUserValues["email"]);
                            }
                            $this->user->touch_logs("update", $oldUserValues);
                            $this->user_profile->touch_logs("update", $oldUserProfileValues);
                            if ($id == $this->session->userdata("AUTH_user_id")) {
                                $this->session->set_userdata("AUTH_userProfileName", $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName"));
                                $this->session->set_userdata("AUTH_userFirstName", $this->user_profile->get_field("firstName"));
                                $this->session->set_userdata("AUTH_user_profilePicture", $this->user_profile->get_field("profilePicture"));
                                $this->session->set_userdata("AUTH_email_address", $this->user->get_field("email"));
                                $this->session->set_userdata("AUTH_username", $this->user->get_field("username"));
                            }
                            $result = true;
                            if ($view == "profile") {
                                $signatures = $this->input->post("signature");
                                if ($signatures) {
                                    $result = true;
                                    if (array_filter($signatures["label"])) {
                                        foreach ($signatures["label"] as $index => $signature) {
                                            $this->user_signature_attachment->reset_fields();
                                            if (isset($signatures["id"][$index]) && $signatures["id"][$index]) {
                                                $this->user_signature_attachment->fetch($signatures["id"][$index]);
                                                $this->user_signature_attachment->set_field("label", $signature);
                                                $result = $this->user_signature_attachment->update();
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $result = false;
                        }
                    } else {
                        $this->user_profile->validate();
                        $result = false;
                    }
                }
            }
            if ($result) {
                unset($_POST);
                unset($userData);
                $userFullName = $this->user_profile->get_fields(["firstName", "lastName"]);
                if ($addFormFlag || !$addFormFlag && !$isUserMaker) {
                    if (is_array($provider_groups_users)) {
                        if (count($provider_groups_users)) {
                            $this->provider_group_user->delete_other_user_provider_groups($provider_groups_users, $id);
                            $this->provider_group_user->add_user_to_provider_groups($provider_groups_users, $id);
                        } else {
                            $this->provider_group_user->delete_user_from_provider_groups($id);
                        }
                    } else {
                        $this->provider_group_user->delete_other_user_provider_groups($provider_groups_users, $id);
                    }
                    unset($provider_groups_users);
                }
                $this->call_materialized_view_triggers("hearing_lawyer", $id, $mv_data);
                if ($isUserMaker) {
                    if (!$this->input->is_ajax_request()) {
                        if (!$userFormHasChanged) {
                            $this->set_flashmessage("information", $this->lang->line("no_changes"));
                        } else {
                            $this->set_flashmessage("warning", $this->lang->line("changes_user_data_need_aproval"));
                        }
                        redirect("users/index");
                    } else {
                        $response["status"] = false;
                        $response["validationErrors"] = $this->user->get("validationErrors") + $this->user_profile->get("validationErrors");
                        $response["message"] = !$userFormHasChanged ? $this->lang->line("no_changes") : $this->lang->line("changes_user_data_need_aproval");
                    }
                } else {
                    $this->load->library("licensor");
                    $this->load->model("email_notification_scheme");
                    $this->load->library("email_notifications");
                    $licenses = $this->licensor->get_all_licenses();
                    $object = "add_user";
                    $model_data["watchers_ids"] = [$this->user->get_field("id")];
                    $notifications_emails = $this->email_notification_scheme->get_emails($object, "user", $model_data);
                    extract($notifications_emails);
                    $to_emails = array_filter($to_emails);
                    $encoded_username = md5(mt_rand() . $this->user->get_field("username"));
                    $user_id_email = $this->user->get_field("id");
                    $change_password_url = site_url("pages/change_password/" . $user_id_email . "/" . $encoded_username);
                    if (!empty($to_emails)) {
                        $notifications_data = ["to" => $to_emails, "cc" => [], "object" => "add_user", "object_id" => $this->user->get_field("id"), "link_to_account" => $change_password_url, "user_email" => $this->user->get_field("email"), "department_core" => $licenses["core"]["App4Legal"]["clientName"]];
                        $this->email_notifications->notify($notifications_data);
                        $userProfileData["forgetPasswordHashKey"] = $encoded_username;
                        $userProfileData["flagChangePassword"] = 1;
                        $this->user_profile->set_fields($userProfileData);
                        $this->user_profile->update();
                        $response["emailSent"] = true;
                    }
                    if (!$this->input->is_ajax_request()) {
                        $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("user") . " \"" . implode(" ", $userFullName) . "\" "));
                        redirect("users/" . $view . "/" . $id);
                    } else {
                        $response["status"] = true;
                        $response["id"] = $this->user->get_field("id");
                        $response["validationErrors"] = $this->user->get("validationErrors") + $this->user_profile->get("validationErrors");
                        $response["message"] = sprintf($this->lang->line("save_record_successfull"), $this->lang->line("user") . " \"" . implode(" ", $userFullName) . "\" ");
                    }
                }
            } else {
                if ($this->user->is_valid() && $this->user_profile->is_valid() && $this->user_signature_attachment->is_valid()) {
                    if (!$this->input->is_ajax_request()) {
                        $this->set_flashmessage("success", sprintf($this->lang->line("save_record_failed"), $this->lang->line("user")));
                        redirect("users/index");
                    } else {
                        $response["status"] = false;
                        $response["validationErrors"] = $this->user->get("validationErrors") + $this->user_profile->get("validationErrors");
                        $response["message"] = sprintf($this->lang->line("save_record_failed"), $this->lang->line("user"));
                    }
                } else {
                    if ($this->input->is_ajax_request()) {
                        $response["status"] = false;
                        $response["validationErrors"] = $this->user->get("validationErrors") + $this->user_profile->get("validationErrors");
                        $response["message"] = sprintf($this->lang->line("save_record_failed"), $this->lang->line("user"));
                    }
                }
            }
        } else {
            if ($id && $id < 1) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("users");
            }
            $userIsFetched = $this->user->fetch($id);
            if ($id) {
                if ($userIsFetched) {
                    $this->user_profile->fetch(["user_id" => $id]);
                    if ($this->user->get_field("user_group_id") == $superAdminInfosystaId && $this->session->userdata("AUTH_user_group_id") != $superAdminInfosystaId) {
                        $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                        redirect("users");
                    }
                } else {
                    $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                    redirect("users");
                }
            }
            if ($view == "clone") {
                $userGroupId = $this->user->get_field("user_group_id");
                $this->user->reset_fields();
                $this->user->set_field("user_group_id", $userGroupId);
                $userProfileData = $this->user_profile->get_fields();
                $this->user_profile->reset_fields();
                $userProfileData["id"] = "";
                $userProfileData["user_id"] = "";
                $userProfileData["firstName"] = "";
                $userProfileData["lastName"] = "";
                $userProfileData["user_code"] = "";
                $userProfileData["father"] = "";
                $userProfileData["mother"] = "";
                $userProfileData["employeeId"] = "";
                $userProfileData["foreign_first_name"] = "";
                $userProfileData["foreign_last_name"] = "";
                $userProfileData["dateOfBirth"] = "";
                $userProfileData["mobile"] = "";
                $userProfileData["profilePicture"] = NULL;
                $this->user_profile->set_fields($userProfileData);
            }
        }
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->load->model("system_preference");
            $system_preference = $this->system_preference->get_key_groups();
            $this->load->model("saml_configuration");
            $saml_configuration = $this->saml_configuration->get_values();
            $enabled_idp = $saml_configuration["idp"] == "none" ? 0 : 1;
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("users"));
            $data["statuses"] = array_combine($this->user_profile->get("statusValues"), [$this->lang->line("choose_status"), $this->lang->line("active"), $this->lang->line("inactive")]);
            $data["genders"] = array_combine($this->user_profile->get("genderValues"), [$this->lang->line("choose_gender"), $this->lang->line("male"), $this->lang->line("female")]);
            $data["titles"] = array_combine($this->user_profile->get("titleValues"), [$this->lang->line("choose_title"), $this->lang->line("mr"), $this->lang->line("mrs"), $this->lang->line("miss"), $this->lang->line("dr"), $this->lang->line("me"), $this->lang->line("judge"), $this->lang->line("sen")]);
            $data["provider_groups_users"] = $this->provider_group_user->get_user_provider_groups($id);
            $data["seniority_Levels"] = $this->seniority_level->load_list([]);
            $data["departments"]= $this->department->load_list([]);
            $data["is_idp_enabled"] = $enabled_idp;
            $systemPreferences = $this->session->userdata("systemPreferences");
            if (isset($systemPreferences["seniorityLevel"])) {
                $data["default_seniority_Level"] = $systemPreferences["seniorityLevel"];
            }
            if (empty($id)) {
                $data["userGroups"] = $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]], ["firstLine" => ["" => ""]]);
            } else {
                if ($this->user->get_field("user_group_id") == $superAdminInfosystaId) {
                    $data["userGroups"] = $this->user_group->load_list([], ["firstLine" => ["" => ""]]);
                } else {
                    $data["userGroups"] = $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]], ["firstLine" => ["" => ""]]);
                }
            }
            $data["Countries"] = $this->country->load_countries_list();
            $data["Currencies"] = $this->country->load_currency_list();
            $data["id"] = $id ? $view == "clone" ? 0 : $id : 0;
            if ($view == "clone") {
                $view = "edit";
            }
            $data["isUserMaker"] = $isUserMaker;
            $data["auto_user_code"] = $this->user_profile->auto_generate_user_code();
            $this->includes("jquery/css/chosen", "css");
            $this->includes("jquery/chosen.min", "js");
            if ($view == "profile") {
                $data["signatures"] = $this->user_signature_attachment->load_all(["where" => ["user_id", $id]]);
                $data["integration_settings"] = unserialize($this->user_preference->get_value("integration"));
                $data["workthrough"] = unserialize($this->user->get_field("workthrough"));
                $data["workthrough"] = $data["workthrough"] ? $data["workthrough"] : [];
                $this->includes("signature/signature_pad", "js");
            }
            $this->includes("scripts/users_add_edit", "js");
            $this->load->view("users/" . $view, $data);
        }
    }
    public function edit($id = "0", $view = "edit")
    {
        if (!$this->input->is_ajax_request()) {
            $this->check_user_flag_need_approval($id);
            $this->save($id, $view);
        }
    }
    private function check_user_flag_need_approval($id)
    {
        $this->load->model("user_profile");
        if ($this->user_profile->fetch(["user_id" => $id]) && $this->user_profile->get_field("flagNeedApproval") == "1") {
            $this->set_flashmessage("warning", $this->lang->line("changes_user_data_need_aproval"));
            redirect("users");
        }
    }
    public function get_profile_picture($id = 0, $thumbsImage = false)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if ($id < 0) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $this->load->model("user_profile");
        $this->user_profile->fetch(["user_id" => $id]);
        $profile_picture = $this->user_profile->get_field("profilePicture");
        if (!empty($profile_picture)) {
            $fileDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . str_pad($id, 10, "0", STR_PAD_LEFT) . DIRECTORY_SEPARATOR . "profilePicture";
            if ($thumbsImage == 1) {
                $fileDirectory = $fileDirectory . DIRECTORY_SEPARATOR . "thumbs";
            }
            $file = $fileDirectory . DIRECTORY_SEPARATOR . $profile_picture;
            if (!file_exists($file) && mb_substr($profile_picture, 0, 11) === "a4l_avatar_") {
                $file = FCPATH . "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "avatars" . DIRECTORY_SEPARATOR . $profile_picture;
            }
            $content = @file_get_contents($file);
            if ($content) {
                $this->load->helper("download");
                force_download($profile_picture, $content);
            }
        } else {
            $file = "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "nophoto.png";
            $content = @file_get_contents($file);
            if ($content) {
                $this->load->helper("download");
                force_download("nophoto.png", $content);
            }
        }
    }

    public function login()
    {
        $response = [];
        if ($this->is_auth->is_logged_in() && !$this->input->is_ajax_request()) {
            $this->set_flashmessage("information", $this->lang->line("already_loggin"));
            redirect("dashboard");
        }
        $data = [];
        $data["is_cloud"] = $this->cloud_installation_type;
        $data["idp_enabled"] = $this->instance_data_values["idp_enabled"];
        $data["forgot_password_url"] = "";
        if (isset($this->instance_data_array["instanceID"])) {
            $cloud_url = substr(current_url(), 0, strpos(current_url(), $this->instance_data_array["instanceID"]));
            if ($cloud_url && ($cloud_config = file_get_contents($cloud_url . "config.ini"))) {
                $cloud_config = parse_ini_string($cloud_config, true);
                $data["forgot_password_url"] = $cloud_config["forgot_password_url"];
            }
        }
        $sso_authentication = $input_validated = $authenticated = NULL;
        $this->load->library("form_validation");
        $this->form_validation->set_rules("username", $this->lang->line("username"), "trim|required|xss_clean");
        $this->form_validation->set_rules("password", $this->lang->line("password"), "trim|required");
        if (!$this->input->is_ajax_request()) {
            $redirect_to = $this->input->post("redirect_to");
            if (empty($redirect_to)) {
                $data["redirect_to"] = $this->session->flashdata("redirectTo");
            } else {
                $hash = $this->input->post("hashPart");
                $url_hash = strcmp($hash, "") ? "#" . $hash : "";
                $data["redirect_to"] = $redirect_to . $url_hash;
            }
        }
        $form_authentication = $this->input->post(NULL) !== false;
        $sso_app4legal = $this->system_preference->get_specified_system_preferences("ssoApp4legal");
        if ($sso_app4legal) {
            $server_auth_user = $this->input->server("AUTH_USER");
            $sso_authentication = !$form_authentication && !empty($server_auth_user);
        }
        $authenticating = $this->uri->segment(3) != "alternative_login" && ($form_authentication || $sso_authentication);
        if ($authenticating) {
            $login = $this->input->post("username");
            if ($form_authentication) {
                $input_validated = $this->form_validation->run();
            } else {
                $login = "sso|" . $server_auth_user;
            }
            if (!isset($input_validated) || $input_validated) {
                $authenticated = $this->is_auth->login($this->user, $login, $this->input->post("password", false), $this->input->post("remember"));
                $user_data = $this->user->get_fields();
                if ($sso_authentication) {
                    if (empty($user_data["username"])) {
                        $active_directory_domain = $this->system_preference->get_specified_system_preferences("domain");
                        $login = mb_substr($login, strpos($login, "\\") + 1, strlen($login));
                        $login = $login . "@" . $active_directory_domain;
                    } else {
                        $login = $user_data["username"];
                    }
                }
                if ($authenticated === 'mfa_required') {
                    // Redirect to OTP verification page
                    redirect('users/verify_otp');
                } elseif ($authenticated === true) {
                    $this->is_auth->_set_session($user_data);
                    $this->is_auth->_set_last_ip_and_last_login($user_data["id"], $this->user);
                    $this->is_auth_event->user_logged_in($user_data["id"]);
                    $this->is_auth->write_log("LOG", $this->input->ip_address() . "\t" . $login . "\t\tSuccessful login");
                    $this->_login_logs_inject_db(1, $login);
                    $this->session->sess_regenerate(true);
                    if ($this->config->item("allowed_single_session")) {
                        $session_id = session_id();
                        $this->user->set_session($user_data["id"], $session_id);
                    }
                    if ($this->input->is_ajax_request()) {
                        $response["result"] = true;
                        $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    } else {
                        if (empty($data["redirect_to"])) {
                            redirect("dashboard");
                        }
                        if (strpos($data["redirect_to"], "keyword=") !== false) {
                            $url = explode("keyword=", $data["redirect_to"]);
                            if (empty($url[1])) {
                                redirect($data["redirect_to"]);
                            }
                            $data["redirect_to"] = $url[0] . "keyword=" . rawurlencode(str_replace("+", "%20", urlencode($url[1])));
                            redirect($data["redirect_to"]);
                        }
                        redirect($data["redirect_to"]);
                    }
                } else {
                    $this->is_auth->write_log("LOG", $this->input->ip_address() . "\t" . $login . "\t\tFailed to login");
                    $data["user_banned"] = $user_data["banned"];
                    $password_lockout = $this->system_preference->get_specified_system_preferences("passwordLockout");
                    if (0 < $password_lockout) {
                        $login_attempts = $this->session->userdata("loginAttemptsByPasswordLockout");
                        $login_attempts = isset($login_attempts[$user_data["id"]]) ? $login_attempts[$user_data["id"]] : 1;
                        if ($password_lockout <= $login_attempts) {
                            if (!$this->is_auth->is_super_admin($user_data["user_group_id"])) {
                                $this->user->set_user($this->user->get_field("id"), ["banned" => 1, "ban_reason" => sprintf($this->lang->line("ban_reason_by_password_lockout"), $password_lockout)]);
                                $this->session->set_userdata("loginAttemptsByPasswordLockout", [$user_data["id"] => 1]);
                            }
                        } else {
                            $this->session->set_userdata("loginAttemptsByPasswordLockout", [$user_data["id"] => ++$login_attempts]);
                        }
                    }
                    $this->_login_logs_inject_db(2, $login);
                    if (!$this->input->is_ajax_request()) {
                        $data["warningMessageOnLoginPage"] = $this->system_preference->get_value_by_key("warningMessageOnLoginPage");
                        $data["warningMessageOnLoginPage"] = $data["warningMessageOnLoginPage"]["keyValue"]??"";
                    }
                }
            }
        }
        if (!$authenticating || isset($input_validated) && !$input_validated || !$authenticated) {
            if ($this->input->is_ajax_request()) {
                $error = $this->is_auth->get_auth_error();
                $password_error = form_error("password");
                $data["user_banned"] = isset($data["user_banned"]) ? $data["user_banned"] : false;
                if (empty($error) && empty($password_error) && !$data["user_banned"]) {
                    $this->user->fetch($this->input->get("id"));
                    $response["username"] = $this->user->get_field("username");
                    $userDirectory = $this->user->get_field("userDirectory");
                    $response["html"] = !empty($userDirectory) ? $this->load->view("users/quick_login_idp", $data + ["userDirectory" => $userDirectory], true) : $this->load->view("users/quick_login", $data, true);
                } else {
                    $error_array = ["auth" => $error, "password" => $password_error ? $this->lang->line("password_is_required") : "", "is_banned" => $data["user_banned"] ? $this->lang->line("user_banned") : ""];
                $response["error"] = $error_array;
                $response["result"] = false;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->force_cloud_login_screen();
            $this->load->view("partial/header");
            $this->load->view("users/login", $data);
            $this->load->view("partial/footer");
        }
    }
}
    public function verify_otp()
    {    $user_data = $this->session->userdata('mfa_user_data');
        $response = ['success' => false, 'message' => 'Invalid OTP or session expired.', "otp_expiry_timestamp" => ""];
        $user_id = $user_data['id'];
        $this->load->model('user', 'userfactory');
        $this->user=$this->userfactory->get_instance();
        $this->user->fetch($user_id); // Fetch the user based on the ID stored in session

        if (empty($user_data)|| !isset($user_data['id']))
        {
            $this->set_flashmessage("error", $this->lang->line("otp_session_expired"));
           redirect("users/login");
        }
        if (empty($this->input->post()) )
        {
            $response['message'] = $this->lang->line("otp_needed");
            $response["otp_expiry_timestamp"]=strtotime($this->user->get_field("otp_expiry"));
            $this->load->view("partial/header");
            $this->load->view("users/verify_otp", $response); // Load the OTP verification form
            $this->load->view("partial/footer");
            return ;
        } else
        {
            $otp = $this->input->post('otp');
        // Now, proceed with OTP validation.

        $otp_verified = $this->is_auth->verify_otp($user_id, $otp);

        if ($otp_verified["result"]) {
            // OTP successfully verified.Clear MFA session data as it's no longer needed
            $this->session->unset_userdata('mfa_user_data');
            // Complete the login process (standard session setup)
            $this->is_auth->_set_session($this->user->get_fields());
            $this->is_auth->_set_last_ip_and_last_login($user_id, $this->user);
            $this->is_auth_event->user_logged_in($user_id);
            $this->is_auth->write_log("LOG", $this->input->ip_address() . "\t" . ($user_data['email'] ?? $user_data['username']) . "\t\tSuccessful MFA login");
            $this->_login_logs_inject_db(1, ($user_data['email'] ?? $user_data['username']));
            $this->session->sess_regenerate(true);

            if ($this->config->item("allowed_single_session")) {
                $session_id = session_id();
                $this->user->set_session($user_id, $session_id);
            }

            $response['success'] = true;
            $response['message'] = "";
            $response['redirect'] = base_url('dashboard'); // Redirect to dashboard or intended page

            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("success", $this->lang->line("otp_verification_successiful"));
                redirect('dashboard');
            }
        } else {
            // OTP verification failed
            $response["otp_expiry_timestamp"]=$otp_verified["otp_expiry_timestamp"];
            $this->is_auth->write_log('error', 'OTP verification failed for '.$user_data['email']);
            // --- DEBUG LOG END ---
            if ($this->input->is_ajax_request()) {
                $response['message'] = $otp_verified["message"] ??'Invalid OTP. Please try again.';

                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("error", $otp_verified["message"] ?: $this->lang->line("mfa_otp_invalid"));
                // Reload the OTP view to allow retrying, passing error message to the view
                $data['message'] = $otp_verified["message"] ?? $this->lang->line("mfa_otp_invalid");
                $data["otp_expiry_timestamp"]=$user_data["otp_expiry_timestamp"]??0;

                $this->load->view("partial/header");
                $this->load->view("users/verify_otp", $data); // Pass $data to the view
                $this->load->view("partial/footer");
            }
        }
        }
    }
    public function resend_otp() {
        if (!$this->input->is_ajax_request()) {
            show_404(); // Only allow AJAX requests
        }

        $response = ['success' => false, 'message' => '',"otp_expiry_timestamp"=>""];
        $mfa_user_data = $this->session->userdata('mfa_user_data');

        if (empty($mfa_user_data) || !isset($mfa_user_data['id'])) {
            $response['message'] = $this->lang->line("mfa_session_expired") ?: 'Your session has expired. Please log in again.';
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            redirect("users/login");
        }

        $user_id = $mfa_user_data['id'];

        $this->load->model('user', 'userfactory');
        $this->user=$this->userfactory->get_instance();
        $this->user->fetch($user_id);
        $email=$this->user->get_field("email");

        $this->load->model("user_profile");

        $this->user_profile->fetch(["user_id" => $user_id]);
        $mobile =$this->user_profile->get_field("mobile")??$this->user_profile->get_field("phone");



        $new_otp_data = $this->is_auth->send_otp_for_login($user_id,$mobile,$email);

        if ($new_otp_data) {
            $response['success'] = true;
            $response['message'] = $this->lang->line("mfa_otp_resent_success");
            $response['new_expiry_timestamp'] = $this->user->get_field("otp_expiry");; // Pass new expiry back to frontend

        } else {
            $response['message'] = $this->lang->line("mfa_otp_resent_fail");
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    public function loginwithoutMFA()
    {
        $response = [];
        if ($this->is_auth->is_logged_in() && !$this->input->is_ajax_request()) {
            $this->set_flashmessage("information", $this->lang->line("already_loggin"));
            redirect("dashboard");
        }
        $data = [];
        $data["is_cloud"] = $this->cloud_installation_type;
        $data["idp_enabled"] = $this->instance_data_values["idp_enabled"];
        $data["forgot_password_url"] = "";
        if (isset($this->instance_data_array["instanceID"])) {
            $cloud_url = substr(current_url(), 0, strpos(current_url(), $this->instance_data_array["instanceID"]));
            if ($cloud_url && ($cloud_config = file_get_contents($cloud_url . "config.ini"))) {
                $cloud_config = parse_ini_string($cloud_config, true);
                $data["forgot_password_url"] = $cloud_config["forgot_password_url"];
            }
        }
        $sso_authentication = $input_validated = $authenticated = NULL;
        $this->load->library("form_validation");
        $this->form_validation->set_rules("username", $this->lang->line("username"), "trim|required|xss_clean");
        $this->form_validation->set_rules("password", $this->lang->line("password"), "trim|required");
        if (!$this->input->is_ajax_request()) {
            $redirect_to = $this->input->post("redirect_to");
            if (empty($redirect_to)) {
                $data["redirect_to"] = $this->session->flashdata("redirectTo");
            } else {
                $hash = $this->input->post("hashPart");
                $url_hash = strcmp($hash, "") ? "#" . $hash : "";
                $data["redirect_to"] = $redirect_to . $url_hash;
            }
        }
        $form_authentication = $this->input->post(NULL) !== false;
        $sso_app4legal = $this->system_preference->get_specified_system_preferences("ssoApp4legal");
        if ($sso_app4legal) {
            $server_auth_user = $this->input->server("AUTH_USER");
            $sso_authentication = !$form_authentication && !empty($server_auth_user);
        }
        $authenticating = $this->uri->segment(3) != "alternative_login" && ($form_authentication || $sso_authentication);
        if ($authenticating) {
            $login = $this->input->post("username");
            if ($form_authentication) {
                $input_validated = $this->form_validation->run();
            } else {
                $login = "sso|" . $server_auth_user;
            }
            if (!isset($input_validated) || $input_validated) {
                $authenticated = $this->is_auth->login($this->user, $login, $this->input->post("password", false), $this->input->post("remember"));
                $user_data = $this->user->get_fields();
                if ($sso_authentication) {
                    if (empty($user_data["username"])) {
                        $active_directory_domain = $this->system_preference->get_specified_system_preferences("domain");
                        $login = mb_substr($login, strpos($login, "\\") + 1, strlen($login));
                        $login = $login . "@" . $active_directory_domain;
                    } else {
                        $login = $user_data["username"];
                    }
                }
                if ($authenticated) {
                    $this->is_auth->_set_session($user_data);
                    $this->is_auth->_set_last_ip_and_last_login($user_data["id"], $this->user);
                    $this->is_auth_event->user_logged_in($user_data["id"]);
                    $this->is_auth->write_log("LOG", $this->input->ip_address() . "\t" . $login . "\t\tSuccessful login");
                    $this->_login_logs_inject_db(1, $login);
                    $this->session->sess_regenerate(true);
                    if ($this->config->item("allowed_single_session")) {
                        $session_id = session_id();
                        $this->user->set_session($user_data["id"], $session_id);
                    }
                    if ($this->input->is_ajax_request()) {
                        $response["result"] = true;
                        $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    } else {
                        if (empty($data["redirect_to"])) {
                            redirect("dashboard");
                        }
                        if (strpos($data["redirect_to"], "keyword=") !== false) {
                            $url = explode("keyword=", $data["redirect_to"]);
                            if (empty($url[1])) {
                                redirect($data["redirect_to"]);
                            }
                            $data["redirect_to"] = $url[0] . "keyword=" . rawurlencode(str_replace("+", "%20", urlencode($url[1])));
                            redirect($data["redirect_to"]);
                        }
                        redirect($data["redirect_to"]);
                    }
                } else {
                    $this->is_auth->write_log("LOG", $this->input->ip_address() . "\t" . $login . "\t\tFailed to login");
                    $data["user_banned"] = $user_data["banned"];
                    $password_lockout = $this->system_preference->get_specified_system_preferences("passwordLockout");
                    if (0 < $password_lockout) {
                        $login_attempts = $this->session->userdata("loginAttemptsByPasswordLockout");
                        $login_attempts = isset($login_attempts[$user_data["id"]]) ? $login_attempts[$user_data["id"]] : 1;
                        if ($password_lockout <= $login_attempts) {
                            if (!$this->is_auth->is_super_admin($user_data["user_group_id"])) {
                                $this->user->set_user($this->user->get_field("id"), ["banned" => 1, "ban_reason" => sprintf($this->lang->line("ban_reason_by_password_lockout"), $password_lockout)]);
                                $this->session->set_userdata("loginAttemptsByPasswordLockout", [$user_data["id"] => 1]);
                            }
                        } else {
                            $this->session->set_userdata("loginAttemptsByPasswordLockout", [$user_data["id"] => ++$login_attempts]);
                        }
                    }
                    $this->_login_logs_inject_db(2, $login);
                    if (!$this->input->is_ajax_request()) {
                        $data["warningMessageOnLoginPage"] = $this->system_preference->get_value_by_key("warningMessageOnLoginPage");
                        $data["warningMessageOnLoginPage"] = $data["warningMessageOnLoginPage"]["keyValue"];
                    }
                }
            }
        }
        if (!$authenticating || isset($input_validated) && !$input_validated || !$authenticated) {
            if ($this->input->is_ajax_request()) {
                $error = $this->is_auth->get_auth_error();
                $password_error = form_error("password");
                $data["user_banned"] = isset($data["user_banned"]) ? $data["user_banned"] : false;
                if (empty($error) && empty($password_error) && !$data["user_banned"]) {
                    $this->user->fetch($this->input->get("id"));
                    $response["username"] = $this->user->get_field("username");
                    $userDirectory = $this->user->get_field("userDirectory");
                    $response["html"] = !empty($userDirectory) ? $this->load->view("users/quick_login_idp", $data + ["userDirectory" => $userDirectory], true) : $this->load->view("users/quick_login", $data, true);
                } else {
                    $error_array = ["auth" => $error, "password" => $password_error ? $this->lang->line("password_is_required") : "", "is_banned" => $data["user_banned"] ? $this->lang->line("user_banned") : ""];
                    $response["error"] = $error_array;
                    $response["result"] = false;
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $this->force_cloud_login_screen();
                $this->load->view("partial/header");
                $this->load->view("users/login", $data);
                $this->load->view("partial/footer");
            }
        }
    }
    private function _login_logs_inject_db($actionStatus, $user_login)
    {
        $userId = $this->session->userdata("AUTH_user_id");
        $userData = [];
        $log_message = "";
        $log_message_status = "";
        $action = "";
        switch ($actionStatus) {
            case 1:
                $action = "login";
                $log_message = "Successful login";
                $log_message_status = "log_msg_status_1";
                break;
            case 2:
                $userData = $this->user->get_user_by_username(set_value("username"));
                $userData = empty($userData) ? $this->user->get_user_by_email(set_value("username")) : $userData;
                $userId = isset($userData["id"]) ? $userData["id"] : 0;
                $log_message = "Failed to login";
                $log_message_status = "log_msg_status_2";
                $action = "login";
                break;
            case 3:
                $log_message = "Successful logout";
                $log_message_status = "log_msg_status_3";
                $action = "logout";
                break;
            default:
                if (!$user_login) {
                    return true;
                }
        }
                $this->load->model("login_history_log", "login_history_logfactory");
                $this->login_history_log = $this->login_history_logfactory->get_instance();
                $this->login_history_log->reset_fields();
                if ($userId) {
                    $this->login_history_log->set_field("user_id", $userId);
                }
                $this->login_history_log->set_field("userLogin", $user_login);
                $this->login_history_log->set_field("action", $action);
                $this->login_history_log->set_field("source_ip", $this->input->ip_address());
                $this->login_history_log->set_field("log_message", $log_message);
                $this->login_history_log->set_field("log_message_status", $log_message_status);
                $this->login_history_log->set_field("logDate", date("Y-m-d H:i:s", time()));
                $this->login_history_log->set_field("user_agent", mb_substr($this->input->user_agent(), 0, 120));
                $this->login_history_log->insert();

    }
    public function logout()
    {
        $cloud_config_params = $this->session->userdata("cloud_config_params");
        $login_log_message = $this->input->ip_address() . "\t" . $this->is_auth->get_user_name() . "\t\tSuccessful logout";
        $this->is_auth->write_log("LOG", $login_log_message);
        $this->_login_logs_inject_db(3, $this->is_auth->get_user_name());
        $this->load->model("saml_configuration");
        $saml_configuration = $this->saml_configuration->get_values();
        $enabled_idp = $saml_configuration["idp"] == "none" ? false : true;
        $this->is_auth->logout();
        if ($this->session->userdata("a4l_sso_login") && $enabled_idp) {
            $auth = new SimpleSAML_Auth_Simple("app4legal-" . $saml_configuration["idp"]);
            $auth->logout();
        }
        $installation_type = $this->user->get_instance_value_by_key("installationType");
        if ($this->session->userdata("is_cloud")) {
            redirect(isset($cloud_config_params["login_url"]) ? $cloud_config_params["login_url"] : "users/login");
        } else {
            redirect("users/login");
        }
    }
    public function profile()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login/");
        }
        $this->load->model("user_profile");
        $id = $this->session->userdata("AUTH_user_id");
        if (!$this->input->is_ajax_request()) {
            $user_id = $this->session->userdata("AUTH_user_id");
            $this->save($user_id, "profile");
        }
    }


   public function change_password()
    {
        if ($this->input->is_ajax_request()) {
            if (!$this->is_auth->is_logged_in()) {
                exit("login_needed");
            }
            $response = [];
            $response["resultCode"] = 0;
            $this->input->post("type");
            switch ($this->input->post("type")) {
                case "submit":
                    $this->load->library("form_validation");
                    $this->form_validation->set_rules("newPassword", $this->lang->line("new_password"), "trim|required|xss_clean|min_length[" . $this->user->minPassword . "]|max_length[" . $this->user->maxPassword . "]|callback__password_history_check");
                    $this->form_validation->set_rules("confirmNewPassword", $this->lang->line("confirm_new_password"), "trim|required|xss_clean|matches[newPassword]");
                    $response["result"] = $this->form_validation->run();
                    if ($response["result"]) {
                        $userId = $this->input->post("userId");
                        $newPassword = $this->input->post("newPassword");
                        $this->user->fetch($userId);
                        if (!password_verify($this->input->post("oldPassword"), $this->user->get_field("password"))) {
                            $response["resultCode"] = 101;
                            $response["message"] = $this->lang->line("old_password_not_match");
                        } else {
                            $oldUserValues = $this->user->get_old_values($userId);
                            $this->user->set_field("id", $userId);
                            $this->user->set_field("password", password_hash($newPassword, PASSWORD_DEFAULT));
                            $response["status"] = $this->user->update();
                            if ($response["status"]) {
                                $this->user->touch_logs("update", $oldUserValues);
                                $this->user->log_password_change();
                                $response["message"] = $this->lang->line("password_changed_successfully");
                            }
                        }
                    } else {
                        $systemPreferences = $this->session->userdata("systemPreferences");
                        $response["message"] = sprintf($this->lang->line("_password_history_check"), isset($systemPreferences["passwordDisallowedPrevious"]) ? $systemPreferences["passwordDisallowedPrevious"] : "0");
                    }
                    break;
                default:
                    $userId = $this->input->post("userId");
                    $response["html"] = $this->load->view("users/change_password", compact("userId"), true);
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
            }
        } else {
            if (!$this->is_auth->is_logged_in()) {
                redirect("users/login/");
            }
            $this->load->library("form_validation");
            $userId = $this->session->userdata("AUTH_user_id");
            if ($this->session->userdata("AUTH_isAd") == "1") {
                $this->set_flashmessage("error", $this->set_flashmessage("error", sprintf($this->lang->line("no_password_change"), $this->lang->line("ActiveDirectory"))));
                redirect("dashboard");
            } else {
                if ($this->session->userdata("a4l_sso_login") && $this->session->userdata("is_cloud")) {
                    $this->load->model("saml_configuration");
                    $saml_configuration = $this->saml_configuration->get_values();
                    $this->set_flashmessage("error", $this->set_flashmessage("error", sprintf($this->lang->line("no_password_change"), $this->lang->line($saml_configuration["idp"]))));
                    redirect("dashboard");
                }
            }
            if ($this->input->post(NULL)) {
                $this->form_validation->set_rules("newPassword", $this->lang->line("new_password"), "trim|required|min_length[" . $this->user->minPassword . "]|max_length[" . $this->user->maxPassword . "]|callback__password_history_check");
                $this->form_validation->set_rules("confirmNewPassword", $this->lang->line("confirm_new_password"), "trim|required|matches[newPassword]");
                if ($this->form_validation->run()) {
                    $userId = $this->input->post("userId");
                    $this->user->fetch($userId);
                    $stored_hash = $this->user->get_field("password");
                    if (!password_verify($this->input->post("oldPassword"), $this->user->get_field("password"))) {
                        $this->set_flashmessage("error", $this->lang->line("old_password_not_match"));
                        redirect("users/change_password");
                    } else {
                        $oldUserValues = $this->user->get_old_values($userId);
                        $this->user->set_field("id", $userId);
                        $this->user->set_field("password", password_hash($this->input->post("newPassword", false), PASSWORD_DEFAULT));
                        if ($this->user->update()) {
                            $this->user->touch_logs("update", $oldUserValues);
                            $this->user->log_password_change();
                            $this->load->model("user_profile");
                            $oldValues = $this->user_profile->get_old_values($userId);
                            $oldValues["flagChangePassword"] = (string) $oldValues["flagChangePassword"];
                            $this->user_profile->fetch(["user_id" => $userId]);
                            if ($this->user_profile->get_field("flagChangePassword") == 1) {
                                $this->user_profile->set_field("flagChangePassword", "0");
                                if ($this->user_profile->update()) {
                                    $this->session->set_userdata("AUTH_userflagChangePassword", 0);
                                    $this->user_profile->touch_logs("update", $oldValues);
                                }
                            }
                            $this->set_flashmessage("success", $this->lang->line("password_changed_successfully"));
                            redirect("dashboard");
                        } else {
                            $this->set_flashmessage("error", $this->lang->line("password_changed_failed"));
                            redirect("dashboard");
                        }
                    }
                } else {
                    $this->load->view("partial/header");
                    $this->load->view("users/change_password", compact("userId"));
                    $this->load->view("partial/footer");
                }
            } else {
                $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("users"));
                $this->load->view("partial/header");
                $this->load->view("users/change_password", compact("userId"));
                $this->load->view("partial/footer");
            }
        }
    }
    public function _password_history_check($clear_pass)
    {
        $systemPreferences = $this->session->userdata("systemPreferences");
        if (isset($systemPreferences["passwordDisallowedPrevious"]) && 0 < $systemPreferences["passwordDisallowedPrevious"] && $this->session->userdata("AUTH_username") != $this->user->get("isAdminUser")) {
            $this->load->model("user_password");
            $old_passwords = $this->user_password->load_old_password($this->input->post("userId"));
            if (!empty($old_passwords)) {
                foreach ($old_passwords as $old_password) {
                    if (substr($old_password, 0, 7) !== "\$2y\$10\$") {
                        if (crypt($this->is_auth->_encode($clear_pass), $old_password) === $old_password) {
                            $this->lang->language["_password_history_check"] = sprintf($this->lang->language["_password_history_check"], $systemPreferences["passwordDisallowedPrevious"]);
                            return false;
                        }
                    } else {
                        if (password_verify($clear_pass, $old_password)) {
                            $this->lang->language["_password_history_check"] = sprintf($this->lang->language["_password_history_check"], $systemPreferences["passwordDisallowedPrevious"]);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
    public function activate_deactivate()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("users");
        }
        $response["status"] = true;
        $this->load->model("user_profile");
        $this->user->fetch($this->input->post("id"));
        $type = $this->user->get_field("type");
        $license_details = $this->user->load_users_license_details();
        $core_exceeded = $contract_exceeded = false;
        if ($this->input->post("newStatus") == "Active") {
            if (($type == "core" || $type == "both") && $license_details["core"]["maxActiveUsers"] <= $license_details["core"]["active_users"]) {
                $response["status"] = false;
                $response["licenseMsg"] = sprintf($this->lang->line("license_exceeded_for"), $this->lang->line("core"));
                $core_exceeded = true;
            }
            if (($type == "contract" || $type == "both") && $license_details["contract"]["maxActiveUsers"] <= $license_details["contract"]["active_users"]) {
                $response["status"] = false;
                $response["licenseMsg"] = sprintf($this->lang->line("license_exceeded_for"), $this->lang->line("contract"));
                $contract_exceeded = true;
            }
            if ($core_exceeded && $contract_exceeded) {
                $response["status"] = false;
                $response["licenseMsg"] = sprintf($this->lang->line("license_exceeded_for"), $this->lang->line("core") . " & " . $this->lang->line("contract"));
            }
        }
        if ($response["status"]) {
            $oldValues = $this->user_profile->get_old_values($this->input->post("id"), ["status"]);
            $newChangesData = ["status" => $this->input->post("newStatus")];
            $isUserMaker = $this->is_auth->user_is_maker();
            $this->user_profile->fetch(["user_id" => $this->input->post("id")]);
            $this->user_profile->set_field("status", $this->input->post("newStatus"));
            if ($isUserMaker) {
                if ($this->user_profile->validate()) {
                    $this->user->insert_user_changes_authorization("edit", $this->input->post("id"), $newChangesData, $oldValues);
                    $response["msg"] = $this->lang->line("changes_user_data_need_aproval");
                } else {
                    $response["validationErrors"] = $this->user_profile->get("validationErrors");
                }
            } else {
                $response["status"] = $this->user_profile->update();
                if ($response["status"]) {
                    $this->user_profile->touch_logs("update", $oldValues);
                    $this->user->fetch($this->input->post("id"));
                    $this->user->set_field("banned", $this->input->post("newStatus") == "Active" ? 0 : 1);
                    $this->user->update($this->input->post("id"));
                    if ($this->input->post("newStatus") == "Inactive") {
                        $this->load->model("user_autologin");
                        $user_autologin_info = ["user_id" => $this->input->post("id")];
                        $this->user_autologin->prune_keys($user_autologin_info);
                        $this->user_autologin->clear_keys($user_autologin_info);
                        $this->user->destroy_session_user_id($this->input->post("id"));
                    }
                } else {
                    $response["validationErrors"] = $this->user_profile->get("validationErrors");
                }
            }
        }
        $this->user_profile->reset_fields();
        $response["subscription_deny_additions"] = $this->user_profile->abilityToIncreaseLicenseUsers();
        $license_details = $this->user->load_users_license_details();
        $response["core_active_users_msg"] = sprintf($this->lang->line("maximum_allowed_active_users"), $this->lang->line("core"), $license_details["core"]["maxActiveUsers"], $license_details["core"]["active_users"]);
        $response["contract_active_users_msg"] = sprintf($this->lang->line("maximum_allowed_active_users"), $this->lang->line("contract"), $license_details["contract"]["maxActiveUsers"], $license_details["contract"]["active_users"]);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function ban_unban()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("users");
        }
        $response = [];
        $this->input->post("type");
        switch ($this->input->post("type")) {
            case "submit":
                $oldValues = $this->user->get_old_values($this->input->post("id"));
                $newChangesData = ["banned" => $this->input->post("banned"), "ban_reason" => $this->input->post("ban_reason")];
                $isUserMaker = $this->is_auth->user_is_maker();
                $this->user->fetch($this->input->post("id"));
                $this->user->set_field("banned", $newChangesData["banned"]);
                $this->user->set_field("ban_reason", $newChangesData["ban_reason"]);
                $this->load->model("user_profile");
                $this->user_profile->fetch(["user_id" => $this->input->post("id")]);
                $profileStatus = $this->user_profile->get_field("status");
                if ($newChangesData["banned"] == "0" && $profileStatus == "Inactive") {
                    $response["msg"] = $this->lang->line("user_inactive_msg_ban_coneversion");
                } else {
                    if ($isUserMaker) {
                        if ($this->user->validate()) {
                            $this->user->insert_user_changes_authorization("edit", $this->input->post("id"), $newChangesData, $oldValues);
                            $response["msg"] = $this->lang->line("changes_user_data_need_aproval");
                        } else {
                            $response["validationErrors"] = $this->user->get("validationErrors");
                        }
                    } else {
                        $response["status"] = $this->user->Update();
                        if ($response["status"]) {
                            $this->user->touch_logs("update", $oldValues);
                            if ($this->input->post("banned") == 1) {
                                $this->user->destroy_session_user_id($this->input->post("id"));
                            }
                        }
                    }
                }
                break;
            case "getForm":
                $this->user->fetch($this->input->post("id"));
                $_POST = $this->user->get_fields();
                $response["html"] = $this->load->view("users/ban_unban_form", $this->input->post(NULL), true);
                break;
            default:
        }
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));

    }
    public function override_privacy()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("users");
        }
        $response = [];
        $this->load->model("user_profile");
        $oldValues = $this->user_profile->get_old_values($this->input->post("id"));
        $this->user_profile->fetch(["user_id" => $this->input->post("id")]);
        $this->user_profile->set_field("overridePrivacy", $this->input->post("overridePrivacy"));
        $isUserMaker = $this->is_auth->user_is_maker();
        if ($isUserMaker) {
            if ($this->user_profile->validate()) {
                $this->user->insert_user_changes_authorization("edit", $this->input->post("id"), ["overridePrivacy" => $this->input->post("overridePrivacy")], $oldValues);
                $response["msg"] = $this->lang->line("changes_user_data_need_aproval");
            }
        } else {
            $response["result"] = $this->user_profile->update();
            if ($response["result"]) {
                $this->user_profile->touch_logs("update", $oldValues);
                $this->session->set_userdata("AUTH_userOverridePrivacy", $this->user_profile->get_field("overridePrivacy"));
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function audit_reports()
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->user->k_load_all_users_audit_reports($filter, $sortable);
            } else {
                $response["records"] = $this->user->k_load_all_users_audit_reports($filter, $sortable);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
           // $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("users_audit_report"));
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["actions"] = ["" => "", "insert" => $this->lang->line("insert"), "update" => $this->lang->line("update")];
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("scripts/users_audit_reports", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("users/audit_reports", $data);
            $this->load->view("partial/footer");
        }
    }
    public function flag_to_change_password()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("users");
        }
        $response = [];
        $this->load->model("user_profile");
        $this->user->fetch($this->input->post("id"));
        if ($this->user->get_field("isAd") == "0" || !$this->user->get_field("userDirectory")) {
            $oldValues = $this->user_profile->get_old_values($this->input->post("id"));
            $oldValues["flagChangePassword"] = (string) $oldValues["flagChangePassword"];
            $this->user_profile->fetch(["user_id" => $this->input->post("id")]);
            $this->user_profile->set_field("flagChangePassword", "1");
            $response["result"] = $this->user_profile->update();
            if ($response["result"]) {
                $this->user_profile->touch_logs("update", $oldValues);
            }
        } else {
            $response["resutl"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function login_history_report()
    {
        if ($this->input->post(NULL)) {
            $this->load->model("login_history_log", "login_history_logfactory");
            $this->login_history_log = $this->login_history_logfactory->get_instance();
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $response = $this->login_history_log->k_load_all_login_history_logs($filter, $sortable);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
         //   $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("login_history_report"));
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $data["actions"] = ["" => "", "login" => $this->lang->line("sign_in"), "logout" => $this->lang->line("sign_out")];
            $data["logMessageStatuses"] = ["" => "", "successful" => $this->lang->line("successful"), "failed" => $this->lang->line("failed")];
            $data["logMessageValues"] = ["log_msg_status_1" => $this->lang->line("log_msg_status_1"), "log_msg_status_2" => $this->lang->line("log_msg_status_2"), "log_msg_status_3" => $this->lang->line("log_msg_status_3")];
            $this->load->model("user_group", "user_groupfactory");
            $this->user_group = $this->user_groupfactory->get_instance();
            $data["userGroups"] = $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]);
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("scripts/login_history_report", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("users/login_history_report", $data);
            $this->load->view("partial/footer");
        }
    }
    public function clear_login_history_logs()
    {
        $this->load->model("login_history_log", "login_history_logfactory");
        $this->login_history_log = $this->login_history_logfactory->get_instance();
        if (!$this->input->post(NULL)) {
            $data = [];
            $this->load->view("partial/header");
            $this->load->view("users/clear_login_history_logs", $data);
            $this->load->view("partial/footer");
        } else {
            $intervalDate = $this->input->post("intervalDate");
            $intervalDates = ["3m", "6m", "9m", "12m"];
            if (!in_array($intervalDate, $intervalDates)) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("users/login_history_report");
            } else {
                $this->login_history_log->delete_history($intervalDate);
            }
            $this->set_flashmessage("information", sprintf($this->lang->line("delete_record_successfull"), $this->lang->line("login_history")));
            redirect("users/login_history_report");
        }
    }
    public function import_from_ad()
    {
        $this->load->model("system_preference");
        $system_preference = $this->system_preference->get_key_groups();
      //  $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("ActiveDirectory"));
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        $data["userGroups"] = $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]);
        if ($this->input->post(NULL)) {
            $groupId = $this->input->post("user_group_id");
            $users = $this->input->post("usersToAdd");
            $user_names = $this->input->post("usernamesToAdd");
            $access_type = $this->input->post("type");
            $usersAdded = 0;
            $html = "";
            $usersCount = is_array($users) ? count($users) : 0;
            $this->load->model(["user_profile"]);
            $licenses = $this->licensor->get_all_licenses();
            $activeUsers = $this->user->count_active_users($access_type);
            $remainingActiveUsersCount = 0;
            if ($access_type == "both") {
                $key_prefix = APPNAME . "::" . $this->inflector->humanize("contract");
                if (isset($licenses["contract"][$key_prefix])) {
                    $remainingActiveUsersCount = $licenses["core"][APPNAME]["maxActiveUsers"] + $licenses["contract"][$key_prefix]["maxActiveUsers"] - $activeUsers["totalCount"];
                }
            } else {
                $key_prefix = $access_type == "contract" ? APPNAME . "::" . $this->inflector->humanize("contract") : APPNAME;
                $remainingActiveUsersCount = $licenses[$access_type][$key_prefix]["maxActiveUsers"] - $activeUsers["totalCount"];
            }
            $isUserMaker = $this->is_auth->user_is_maker();
            if (!$isUserMaker && $remainingActiveUsersCount < $usersCount) {
                $this->set_flashmessage("error", $this->lang->line("license_exceeded"), 10000);
                redirect("users/import_from_ad");
            }
            if ($users) {
                $this->load->library("ActiveDirectory");
                $ad = new ActiveDirectory();
                $ad->bindAdmin();
                $allSuccess = true;
                foreach ($users as $index => $user) {
                    $this->user = NULL;
                    $this->user = $this->userfactory->get_instance();
                    $userData["username"] = $user_names[$index];
                    $userData["email"] = $user;
                    $userData["banned"] = $isUserMaker ? "1" : "0";
                    $userData["isAd"] = 1;
                    $userData["user_group_id"] = $groupId;
                    $userData["type"] = $access_type;
                    $userData["created"] = date("Y-m-d H:i:s", time());
                    $userData["modified"] = date("Y-m-d H:i:s", time());
                    $userData["modifiedBy"] = $this->is_auth->get_user_id();
                    $this->user->set_fields($userData);
                    if (!$this->user->get_user_by_username($user) && $this->user->insert()) {
                        $userAd = $ad->searchUsers($user);
                        $profileData["overridePrivacy"] = "no";
                        $profileData["firstName"] = $userAd[0]["givenname"][0];
                        $profileData["lastName"] = $userAd[0]["sn"][0];
                        if ($isUserMaker) {
                            $profileData["flagNeedApproval"] = "1";
                            $profileData["status"] = "Inactive";
                        } else {
                            $profileData["status"] = "Active";
                        }
                        $profileData["gender"] = "";
                        $profileData["title"] = "";
                        $profileData["father"] = "";
                        $profileData["mother"] = "";
                        $profileData["nationality"] = "";
                        $profileData["jobTitle"] = "";
                        $profileData["isLawyer"] = "no";
                        $profileData["website"] = "";
                        $profileData["phone"] = "";
                        $profileData["fax"] = "";
                        $profileData["mobile"] = "";
                        $profileData["address1"] = "";
                        $profileData["address2"] = "";
                        $profileData["city"] = "";
                        $profileData["state"] = "";
                        $profileData["zip"] = "";
                        $profileData["country"] = "";
                        $profileData["comments"] = "";
                        $profileData["department"] = isset($userAd[0]["department"][0]) ? $userAd[0]["department"][0] : "";
                        $profileData["employeeId"] = isset($userAd[0]["employeeid"][0]) ? $userAd[0]["employeeid"][0] : "";
                        $profileData["ad_userCode"] = isset($userAd[0]["objectguid"][0]) ? $userAd[0]["objectguid"][0] : "";
                        $profileData["user_code"] = "UC" . ($this->user->get_field("id") - 1);
                        $systemPreferences = $this->session->userdata("systemPreferences");
                        if (isset($systemPreferences["seniorityLevel"])) {
                            $profileData["seniority_level_id"] = $systemPreferences["seniorityLevel"];
                        }
                        $this->user_profile->reset_fields();
                        $this->user_profile->set_fields($profileData);
                        $this->user_profile->set_field("user_id", $this->user->get_field("id"));
                        if ($result = $this->user_profile->insert()) {
                            $this->user->relateToCC($this->user->get_field("email"));
                            $this->user->duplicate_user_to_contact();
                            if ($isUserMaker) {
                                $newChangesData = array_merge($userData, $profileData);
                                $this->user->insert_user_changes_authorization("add", $this->user->get_field("id"), $newChangesData);
                            }
                            $usersAdded++;
                            $this->user->touch_logs("insert", $userData);
                            $this->user_profile->touch_logs("insert", $profileData);
                        } else {
                            $this->user->delete($this->user->get_field("id"));
                        }
                    }
                }
                $html = "<ul>";
                $html .= "<li>" . sprintf($this->lang->line("ActiveDirectoryAdded"), $usersAdded, $usersCount - $usersAdded) . "</li>";
                $html .= "</ul>";
                $this->set_flashmessage("information", $html, 10000);
                redirect("users/index");
            } else {
                $this->set_flashmessage("information", $this->lang->line("ad_no_users_selected"), 10000);
                redirect("users/import_from_ad");
            }
        }
        $this->load->view("users/importad", $data);
    }
    public function ajax_search_ad()
    {
        if ($this->input->is_ajax_request()) {
            $usernameQuery = trim((string) $this->input->get("term"));
            $this->load->library("ActiveDirectory");
            $ad = new ActiveDirectory();
            if ($ad->bindAdmin()) {
                $result = $ad->searchUsers($usernameQuery);
                if ($result) {
                    echo json_encode($result);
                } else {
                    $result = ["noResults" => "An Error has occured"];
                    echo json_encode($result);
                }
            } else {
                $result["error"] = 1;
                echo json_encode($result);
            }
        } else {
            redirect("users");
        }
    }
    public function convert_local_directory_users_to_active_directory()
    {
        if ($this->input->is_ajax_request()) {
            $userId = $this->input->post("id");
            $this->user->fetch($userId);
            $user = $this->user->get_fields();
            $this->load->library("ActiveDirectory");
            $ad = new ActiveDirectory();
            $result = [];
            if ($ad->bindAdmin()) {
                $data = $ad->searchUsers($user["username"]);
                if ($data) {
                    $this->load->model(["user_profile"]);
                    $this->user->set_field("isAd", 1);
                    $this->user->set_field("password", "");
                    $this->user->set_field("username", $data[0]["userprincipalname"][0]);
                    $this->user_profile->fetch(["user_id" => $userId]);
                    $userProfileFields = $this->user_profile->get_fields();
                    $updateUserProfile = false;
                    $data[0]["objectguid"][0] = $data[0]["objectguid"][0];
                    $fieldsToCheck = ["department" => "department", "employeeId" => "employeeid", "ad_userCode" => "objectguid"];
                    $newData = ["isAd" => "1"];
                    $oldData = ["isAd" => "0"];
                    foreach ($fieldsToCheck as $dbField => $adField) {
                        if (strlen($userProfileFields[$dbField]) < 1 && isset($data[0][$adField][0])) {
                            $newData[$dbField] = $data[0][$adField][0];
                            $oldData[$dbField] = $userProfileFields[$dbField];
                            $this->user_profile->set_field($dbField, $data[0][$adField][0]);
                            $updateUserProfile = true;
                        }
                    }
                    $isUserMaker = $this->is_auth->user_is_maker();
                    if ($isUserMaker) {
                        $this->user->insert_user_changes_authorization("edit", $userId, $newData, $oldData);
                        $result["success"] = 1;
                        $result["msg"] = $this->lang->line("changes_user_data_need_aproval");
                        echo json_encode($result);
                    } else {
                        if (!$this->user->update()) {
                            $result["error"] = 1;
                            echo json_encode($result);
                        } else {
                            if ($updateUserProfile && !$this->user_profile->update()) {
                                $result["error"] = 1;
                                echo json_encode($result);
                            } else {
                                $result["success"] = 1;
                                echo json_encode($result);
                            }
                        }
                    }
                } else {
                    $result = ["noResults" => "An Error has occured"];
                    echo json_encode($result);
                }
            } else {
                $result["error"] = 1;
                echo json_encode($result);
            }
        } else {
            redirect("users");
        }
    }
    public function ad_test_connection()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->library("ActiveDirectory");
            $ad = new ActiveDirectory();
            $response = [];
            if ($ad->bindAdmin()) {
                $response["status"] = 202;
            } else {
                $response["status"] = 101;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
    }
    public function sync_user_from_active_directory()
    {
        if ($this->input->is_ajax_request()) {
            $result = [];
            $userId = $this->input->post("id");
            $isUserMaker = $this->is_auth->user_is_maker();
            $this->load->model(["user_profile"]);
            $this->user = $this->userfactory->get_instance();
            $this->user->fetch($userId);
            $this->user_profile->fetch(["user_id" => $userId]);
            $userProfileFields = $this->user_profile->get_fields();
            $ad_userCode = $userProfileFields["ad_userCode"];
            $username = $this->user->get_field("username");
            $this->load->library("ActiveDirectory");
            $ad = new ActiveDirectory();
            if ($ad->bindAdmin()) {
                $userAD = isset($ad_userCode) && !is_null($ad_userCode) && strcmp($ad_userCode, "") ? $ad->searchUsersByCode($ad_userCode) : $ad->searchUsers($username);
                if ($userAD) {
                    $userProfileAdData = [];
                    $userAdData = [];
                    $oldUserData = [];
                    $fieldsToCheck = ["department" => "department", "employeeId" => "employeeid", "ad_userCode" => "objectguid", "firstName" => "givenname", "lastName" => "sn"];
                    foreach ($fieldsToCheck as $dbField => $adField) {
                        if (isset($userAD[0][$adField][0]) && strcmp($userProfileFields[$dbField], $userAD[0][$adField][0])) {
                            $userProfileAdData[$dbField] = $userAD[0][$adField][0];
                            $oldUserData[$dbField] = $userProfileFields[$dbField];
                        }
                    }
                    $usernameToInsert = $username;
                    $updateUsername = false;
                    if (isset($userAD[0]["userprincipalname"][0]) && strcmp($username, $userAD[0]["userprincipalname"][0])) {
                        $usernameToInsert = $userAD[0]["userprincipalname"][0];
                        $updateUsername = true;
                    }
                    if (is_array($userProfileAdData) && 0 < count($userProfileAdData) || $updateUsername) {
                        if ($isUserMaker) {
                            if ($updateUsername === true) {
                                $oldUserData["username"] = $username;
                                $userProfileAdData["username"] = $usernameToInsert;
                                $oldUserData["email"] = $username;
                                $userProfileAdData["email"] = $usernameToInsert;
                            }
                            $this->user->insert_user_changes_authorization("edit", $userId, $userProfileAdData, $oldUserData);
                            $result["success"] = 1;
                            $result["warning"] = $this->lang->line("changes_user_data_need_aproval");
                        } else {
                            $this->user_profile->set_fields($userProfileAdData);
                            if ($this->user_profile->update()) {
                                $this->user->set_field("username", $usernameToInsert);
                                if ($this->cloud_installation_type) {
                                    $this->user->set_field("email", $usernameToInsert);
                                    $this->user->relateToCC($usernameToInsert, $this->user->get_field("email"));
                                }
                                if ($updateUsername === true && !$this->user->update()) {
                                    $result["success"] = 0;
                                    $result["msg"] = $this->lang->line("update_user_login_failed");
                                } else {
                                    $result["success"] = 1;
                                    $result["msg"] = $this->lang->line("user_data_saved_successfully");
                                }
                            } else {
                                $result["success"] = 0;
                                $result["msg"] = $this->lang->line("update_user_profile_failed");
                            }
                        }
                    } else {
                        $result["success"] = 1;
                        $result["msg"] = $this->lang->line("no_changes");
                    }
                } else {
                    $result["success"] = 0;
                    $result["noResults"] = $this->lang->line("user_email_and_or_code_not_matched");
                }
            } else {
                $result["success"] = 0;
                $result["msg"] = $this->lang->line("ad_connection_error");
            }
        } else {
            redirect("users");
        }
        echo json_encode($result);
    }
    public function revoke_api_key()
    {
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        $this->load->model("user_api_key", "user_api_keyfactory");
        $this->user_api_key = $this->user_api_keyfactory->get_instance();
        $response["result"] = $this->user_api_key->remove($this->input->post("id"));
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function checker_approve_changes()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("users");
        }
        $response = [];
        $this->load->model("user_profile");
        $this->user_profile->fetch(["user_id" => $this->input->post("id")]);
        if ($this->user_profile->get_field("flagNeedApproval") == "1" && $this->is_auth->user_is_checker()) {
            $this->load->model("user_changes_authorization", "user_changes_authorizationfactory");
            $this->user_changes_authorization = $this->user_changes_authorizationfactory->get_instance();
            $requiredFields = ["username", "email", "password", "firstName", "lastName", "isAd", "user_group_id", "ad_userCode", "user_code"];
            $this->input->post("modeType");
            switch ($this->input->post("modeType")) {
                case "getForm":
                    $data = [];
                    $data["affectedUserChanges"] = $this->user_changes_authorization->load_changes_per_affected_user($this->input->post("id"));
                    $response["changeType"] = isset($data["affectedUserChanges"][0]) ? $data["affectedUserChanges"][0]["changeType"] : "";
                    $data["id"] = $this->input->post("id");
                    $data["changeType"] = $response["changeType"];
                    $data["requiredFields"] = $requiredFields;
                    $response["html"] = $this->load->view("users/approve_changes_form", $data, true);
                    break;
                case "discardUser":
                    $changeType = $this->input->post("changeType");
                    if ($changeType === "add") {
                        $this->user->discard_user_changes($this->input->post("id"));
                    }
                    break;
                case "submitApproveForm":
                    $changeType = $this->input->post("changeType");
                    $approvedFields = $this->input->post("changeIds") ? $this->input->post("changeIds") : [];
                    $response["result"] = false;
                    if ($changeType === "add") {
                        $fieldsChanged = $this->user_changes_authorization->get_fields_changed_per_affected_user($this->input->post("id"));
                        $fieldsChanged = array_diff($fieldsChanged, $requiredFields);
                        $fieldsNotApproved = array_diff($fieldsChanged, $approvedFields);
                        $this->user_profile->fetch(["user_id" => $this->input->post("id")]);
                        $this->user_profile->set_field("status", "Active");
                        $response["result"] = $this->user_profile->validate();
                        if ($response["result"]) {
                            if (!empty($fieldsNotApproved)) {
                                foreach ($fieldsNotApproved as $field) {
                                    if (in_array($field, $this->user_profile->get("_fieldsNames"))) {
                                        if ($field === "isLawyer") {
                                            $this->user_profile->set_field("isLawyer", "no");
                                        } else {
                                            $this->user_profile->set_field($field, "");
                                        }
                                    }
                                }
                            }
                            $this->user_profile->set_field("flagNeedApproval", "0");
                            $response["result"] = $this->user_profile->update();
                            if ($response["result"]) {
                                if (in_array("provider_group", $fieldsNotApproved)) {
                                    $this->load->model("provider_group_user");
                                    $this->provider_group_user->delete_user_from_provider_groups($this->input->post("id"));
                                }
                                $this->user->fetch($this->input->post("id"));
                                $this->user->set_field("banned", "0");
                                $this->user->update();
                                $this->user->touch_logs("insert", []);
                                $this->user_profile->touch_logs("insert", []);
                                $response["result"] = $this->user_changes_authorization->update_after_approve_per_affected_user($this->input->post("id"), $fieldsNotApproved);
                            }
                        }
                    } else {
                        if ($changeType === "edit") {
                            if (!empty($approvedFields)) {
                                $pendingColValues = $this->user_changes_authorization->get_pending_edit_fields_values_per_affected_user($this->input->post("id"));
                                $this->user->fetch($this->input->post("id"));
                                $this->user_profile->fetch(["user_id" => $this->input->post("id")]);
                                $providerGroupsIds = "";
                                $userTempFields = [];
                                $userTempProfileFields = [];
                                foreach ($approvedFields as $field) {
                                    foreach ($pendingColValues as $pendField => $pendValue) {
                                        if ($field == $pendValue["columnName"]) {
                                            if (in_array($field, $this->user->get("_fieldsNames"))) {
                                                $this->user->set_field($field, $pendValue["columnRequestedValue"]);
                                                array_push($userTempFields, $field);
                                            } else {
                                                if (in_array($field, $this->user_profile->get("_fieldsNames"))) {
                                                    array_push($userTempProfileFields, $field);
                                                    $this->user_profile->set_field($field, $pendValue["columnRequestedValue"]);
                                                    if ($field === "status" && $pendValue["columnRequestedValue"] === "Inactive") {
                                                        $this->user->set_field("banned", "1");
                                                        $this->user->destroy_session_user_id($this->input->post("id"));
                                                    } else {
                                                        if ($field === "status" && $pendValue["columnRequestedValue"] === "Active") {
                                                            $this->user->set_field("banned", "0");
                                                        }
                                                    }
                                                } else {
                                                    if ($field === "provider_group") {
                                                        $providerGroupsIds = $pendValue["columnRequestedValue"];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $userOldValues = $this->user->get_old_values($this->input->post("id"));
                                $userProfileOldValues = $this->user_profile->get_old_values($this->input->post("id"));
                                $userFields = [];
                                $userProfileValues = [];
                                if (!empty($userTempFields)) {
                                    foreach ($userTempFields as $uField) {
                                        $userFields[$uField] = $userOldValues[$uField];
                                    }
                                    $userFields["id"] = $this->input->post("id");
                                    $this->user->touch_logs("update", $userFields);
                                }
                                if (!empty($userTempProfileFields)) {
                                    foreach ($userTempProfileFields as $upField) {
                                        $userProfileValues[$upField] = $userProfileOldValues[$upField];
                                    }
                                    $userProfileValues["user_id"] = $this->input->post("id");
                                    $this->user_profile->touch_logs("update", $userProfileValues);
                                }
                                $this->user_profile->set_field("flagNeedApproval", "0");
                                $response["result"] = $this->user->update();
                                if ($response["result"]) {
                                    $response["result"] = $this->user_profile->update();
                                    if ($response["result"] && in_array("provider_group", $approvedFields)) {
                                        $this->load->model("provider_group_user");
                                        $this->provider_group_user->delete_user_from_provider_groups($this->input->post("id"));
                                        if ($providerGroupsIds) {
                                            $this->provider_group_user->add_user_to_provider_groups(explode(", ", $providerGroupsIds), $this->input->post("id"));
                                        }
                                    }
                                    $pendCols = [];
                                    foreach ($pendingColValues as $val) {
                                        $pendCols[] = $val["columnName"];
                                    }
                                    $fieldsNotApproved = array_diff($pendCols, $approvedFields);
                                    $response["result"] = $this->user_changes_authorization->update_after_approve_per_affected_user($this->input->post("id"), $fieldsNotApproved);
                                }
                            } else {
                                $this->user_profile->fetch(["user_id" => $this->input->post("id")]);
                                $this->user_profile->set_field("flagNeedApproval", "0");
                                $response["result"] = $this->user_profile->update();
                                if ($response["result"]) {
                                    $pendingFields = $this->user_changes_authorization->get_fields_changed_per_affected_user($this->input->post("id"));
                                    $response["result"] = $this->user_changes_authorization->update_after_approve_per_affected_user($this->input->post("id"), $pendingFields);
                                }
                            }
                        }
                    }
                    $response["validationErrors"] = $this->user_profile->get("validationErrors");
                    break;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function maker_checker_report()
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $response = $this->user->k_load_all_maker_checker_changes($filter, $sortable);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
         //   $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("maker_checker_users_report"));
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $data["actions"] = ["" => "", "add" => $this->lang->line("insert"), "edit" => $this->lang->line("update")];
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("scripts/maker_checker_users_report", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("users/maker_checker_report", $data);
            $this->load->view("partial/footer");
        }
    }
    public function user_management_report()
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("users");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->user->user_management_report($filter, $sortable);
            } else {
                $response["records"] = $this->user->user_management_report($filter, $sortable);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
        //    $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("user_management_report"));
            $this->load->model(["user_profile", "provider_group"]);
            $this->load->model("user_group", "user_groupfactory");
            $this->user_group = $this->user_groupfactory->get_instance();
            $this->load->model("country", "countryfactory");
            $this->country = $this->countryfactory->get_instance();
            $data["countries"] = $this->country->load_countries_list();
            $data["userGroups"] = $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]);
            $data["providerGroups"] = $this->provider_group->load_list();
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsBigText"] = $this->get_filter_operators("bigText");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $data["license"] = $this->user->load_users_license_details();
            $data["isLawyerValues"] = array_combine($this->user_profile->get("isLawyerValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["bannedValues"] = array_combine($this->user->get("bannedValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
            $data["statuses"] = array_combine($this->user_profile->get("statusValues"), ["", $this->lang->line("Active"), $this->lang->line("Inactive")]);
            $data["userDirectories"] = array_combine(["", 0, 1, "azure_ad", "onelogin"], ["", $this->lang->line("LocalDirectory"), $this->lang->line("ActiveDirectory"), $this->lang->line("AzureDirectory"), $this->lang->line("onelogin")]);
            $makerCheckerFeatureStatus = $this->system_preference->get_value_by_key("makerCheckerFeatureStatus");
            $data["makerCheckerFeatureStatus"] = $makerCheckerFeatureStatus["keyValue"] == "yes" ? true : false;
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("scripts/user_management_report", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("users/user_management_report", $data);
            $this->load->view("partial/footer");
        }
    }
    public function avatar_uploader()
    {
        $this->authenticate_exempted_actions();
        $response = $data = [];
        $this->load->model(["user_profile", "provider_group"]);
        $users_path = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . str_pad($this->is_auth->get_user_id(), 10, "0", STR_PAD_LEFT);
        $logged_user_profile_pic_path = $users_path . DIRECTORY_SEPARATOR . "profilePicture";
        $logged_user_profile_pic_thumb_path = $logged_user_profile_pic_path . DIRECTORY_SEPARATOR . "thumbs";
        $getting_started_settings = unserialize($this->user_preference->get_value("getting_started"));
        $language_auth_language = $this->session->userdata("AUTH_language");
        $this->session->set_userdata("AUTH_language_initial_setup", $language_auth_language);
        if ($this->input->is_ajax_request()) {
            if ($this->input->post("avatar_number")) {
                $response["result"] = false;
                $system_avatar_name = "a4l_avatar_" . $this->input->post("avatar_number") . ".png";
                if (file_exists(FCPATH . "assets" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "avatars" . DIRECTORY_SEPARATOR . $system_avatar_name)) {
                    if ($this->user_profile->fetch(["user_id" => $this->is_auth->get_user_id()])) {
                        $this->user_profile->set_field("profilePicture", $system_avatar_name);
                        if ($this->user_profile->update()) {
                            $this->session->set_userdata("AUTH_user_profilePicture", $system_avatar_name);
                            $getting_started_settings["add_avatar_step_done"] = true;
                            $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                            $this->empty_directory($logged_user_profile_pic_thumb_path . DIRECTORY_SEPARATOR . "*");
                            $this->empty_directory($logged_user_profile_pic_path . DIRECTORY_SEPARATOR . "*");
                            $response["result"] = true;
                        }
                    } else {
                        $this->session->set_userdata("AUTH_user_profilePicture", NULL);
                    }
                }
            } else {
                $getting_started_settings["auto_open_avatar_form"] = false;
                $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                $response["html"] = $this->load->view("users/avatar_upload", $data, true);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            if (!empty($_FILES["user_avatar"]["name"])) {
                $upload_data = [];
                $response["error"] = false;
                $this->user_profile->fetch(["user_id" => $this->is_auth->get_user_id()]);
                $profile_pic_name = $this->security->sanitize_filename($_FILES["user_avatar"]["name"]);
                if (!is_dir($users_path)) {
                    @mkdir($users_path, 493, true);
                }
                if (!is_dir($logged_user_profile_pic_path)) {
                    @mkdir($logged_user_profile_pic_path, 493, true);
                }
                if (!is_dir($logged_user_profile_pic_thumb_path)) {
                    @mkdir($logged_user_profile_pic_thumb_path, 493, true);
                }
                $this->config->load("allowed_file_uploads", true);
                $allowed_types = $this->config->item("user_profile_picture", "allowed_file_uploads");
                $config = ["upload_path" => $logged_user_profile_pic_path, "file_name" => $profile_pic_name, "max_size" => 1024, "max_height" => 3000, "max_width" => 3000, "allowed_types" => $allowed_types];
                $this->load->library("upload", $config);
                if (!$this->upload->do_upload("user_avatar")) {
                    $response["error"] = $this->upload->display_errors();
                } else {
                    $upload_data = $this->upload->data();
                    $image_config = ["source_image" => $upload_data["full_path"], "new_image" => $logged_user_profile_pic_thumb_path, "maintain_ration" => true, "width" => 120, "height" => 120];
                    $this->load->library("image_lib", $image_config);
                    $this->image_lib->resize();
                    $old_profile_pic = $this->user_profile->get_field("profilePicture");
                    if ($old_profile_pic) {
                        $this->empty_directory($logged_user_profile_pic_thumb_path . DIRECTORY_SEPARATOR . $old_profile_pic);
                        $this->empty_directory($logged_user_profile_pic_path . DIRECTORY_SEPARATOR . $old_profile_pic);
                    }
                    $this->user_profile->set_field("profilePicture", $upload_data["file_name"]);
                    if ($this->user_profile->update()) {
                        $getting_started_settings["add_avatar_step_done"] = true;
                        $this->user_preference->set_value("getting_started", serialize($getting_started_settings), true);
                        $this->session->set_userdata("AUTH_user_profilePicture", $profile_pic_name);
                    }
                }
                $this->load->view("users/avatar_uploader_result", $response);
            }
        }
    }
    private function empty_directory($dir)
    {
        $files = glob($dir);
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
    public function login_sso_external($idp = "azure_ad")
    {
        $this->load->model("system_preference");
        $redirect_url = $this->input->get("redirect_url") ? base64_decode($_GET["redirect_url"]) : "dashboard";
        $query_str = parse_url($redirect_url, PHP_URL_QUERY);
        $redirect_url .= $query_str ? "&" : "?";
        require_once substr(COREPATH, 0, -12) . "/application/libraries/saml/SSO_factory.php";
        $idp_object = SSO_factory::get_idp($idp);
        if (!$idp_object->is_enabled()) {
            $response["status"] = 403;
            $response["error"] = $this->lang->line($idp_object::ENABLE_IDP);
            $encoded_response = base64_encode(json_encode($response));
            redirect($redirect_url . "sso_response=" . $encoded_response);
        }
        if (!$idp_object->is_configured()) {
            $response["status"] = 403;
            $response["error"] = $this->lang->line($idp_object::CONFIGURE_IDP);
            $encoded_response = base64_encode(json_encode($response));
            redirect($redirect_url . "sso_response=" . $encoded_response);
        }
        $idp_object->authenticate();
        $email = $idp_object->get_email();
        if ($email) {
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $user = $this->user->get_user_by_email($email);
            if ($user) {
                if ($user["userDirectory"] != $idp_object::IDP_NAME) {
                    $response["status"] = 403;
                    $response["error"] = $this->lang->line("sso_not_available");
                    $encoded_response = base64_encode(json_encode($response));
                    redirect($redirect_url . "sso_response=" . $encoded_response);
                } else {
                    $authenticated = $this->is_auth->sso_login($this->user, $user["email"]);
                    if (isset($authenticated) && $authenticated) {
                        $user_group_configuration = $idp_object->get_user_group_config();
                        if ($user_group_configuration["user_group_sync"] == "yes") {
                            $fetched_groups = $idp_object->get_user_group($email);
                            if (!$fetched_groups) {
                                $response["status"] = 403;
                                $response["error"] = $this->lang->line("unable_to_fetch_user_groups");
                                $encoded_response = base64_encode(json_encode($response));
                                redirect($redirect_url . "sso_response=" . $encoded_response);
                            } else {
                                if (is_array($fetched_groups)) {
                                    if (count($fetched_groups) == 0) {
                                        $response["status"] = 403;
                                        $response["error"] = $this->lang->line("user_dont_belong_to_groups");
                                        $encoded_response = base64_encode(json_encode($response));
                                        redirect($redirect_url . "sso_response=" . $encoded_response);
                                    } else {
                                        if (1 < count($this->is_auth->check_exist_groups($fetched_groups)) && $user_group_configuration["user_multiple_groups"] == "no") {
                                            $response["status"] = 403;
                                            $response["error"] = $this->lang->line("user_belong_to_multiple_groups");
                                            $encoded_response = base64_encode(json_encode($response));
                                            redirect($redirect_url . "sso_response=" . $encoded_response);
                                        }
                                    }
                                }
                            }
                            $validate_group = $this->is_auth->validate_update_user_group($user["id"], $user["user_group_id"], $fetched_groups);
                            if (!$validate_group["result"]) {
                                $response["status"] = 403;
                                $response["error"] = $validate_group["msg"];
                                $encoded_response = base64_encode(json_encode($response));
                                redirect($redirect_url . "sso_response=" . $encoded_response);
                            }
                        }
                        $this->load->model("user_api_key", "user_api_keyfactory");
                        $this->user_api_key = $this->user_api_keyfactory->get_instance();
                        $token = $this->user_api_key->create($user["id"]);
                        if ($token) {
                            $response["status"] = 200;
                            $response["success"]["msg"] = "Login Successfully";
                            $response["success"]["data"] = ["username" => $user["username"], "email" => $user["email"], "token" => $token];
                            $encoded_response = base64_encode(json_encode($response));
                            redirect($redirect_url . "sso_response=" . $encoded_response);
                        }
                    } else {
                        $response["status"] = 401;
                        $response["error"] = "Authentication error. User does not exist";
                        $encoded_response = base64_encode(json_encode($response));
                        redirect($redirect_url . "sso_response=" . $encoded_response);
                    }
                }
            } else {
                $response["status"] = 401;
                $response["error"] = "User does not exist";
                $encoded_response = base64_encode(json_encode($response));
                redirect($redirect_url . "sso_response=" . $encoded_response);
            }
        } else {
            $response["status"] = 401;
            $response["error"] = "Email does not exist";
            $encoded_response = base64_encode(json_encode($response));
            redirect($redirect_url . "sso_response=" . $encoded_response);
        }
        $response["status"] = 400;
        $response["error"] = "Error Occurred";
        $encoded_response = base64_encode(json_encode($response));
        redirect($redirect_url . "sso_response=" . $encoded_response);
    }
    public function login_idps($idp = "azure_ad", $redirect = "")
    {
        require_once substr(COREPATH, 0, -12) . "/application/libraries/saml/SSO_factory.php";
        $idp_object = SSO_factory::get_idp($idp);
        if (!$idp_object->is_enabled()) {
            $this->set_flashmessage("error", $this->lang->line($idp_object::ENABLE_IDP));
            redirect("");
        }
        if (!$idp_object->is_configured()) {
            $this->set_flashmessage("information", $this->lang->line($idp_object::CONFIGURE_IDP));
            redirect("");
        }
        $idp_object->authenticate();

        $email = $idp_object->get_email();
        if ($email && !$this->is_auth->is_logged_in()) {
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $user = $this->user->get_user_by_email($email);
            if ($user) {
                if ($user["userDirectory"] != $idp_object::IDP_NAME) {
                    $this->set_flashmessage("information", $this->lang->line("sso_not_available"));
                    redirect("");
                } else {
                    $authenticated = $this->is_auth->sso_login($this->user, $user["email"]);
                    if (isset($authenticated) && $authenticated) {
                        $user_group_configuration = $idp_object->get_user_group_config();
                        if ($user_group_configuration["user_group_sync"] == "yes") {
                            $fetched_groups = $idp_object->get_user_group($email);
                            if (!$fetched_groups) {
                                $this->set_flashmessage("information", $this->lang->line("unable_to_fetch_user_groups"));
                                redirect("");
                            } else {
                                if (is_array($fetched_groups)) {
                                    if (count($fetched_groups) == 0) {
                                        $this->set_flashmessage("information", $this->lang->line("user_dont_belong_to_groups"));
                                        redirect("");
                                    } else {
                                        if (1 < count($this->is_auth->check_exist_groups($fetched_groups)) && $user_group_configuration["user_multiple_groups"] == "no") {
                                            $this->set_flashmessage("information", $this->lang->line("user_belong_to_multiple_groups"));
                                            redirect("");
                                        }
                                    }
                                }
                            }
                            $validate_group = $this->is_auth->validate_update_user_group($user["id"], $user["user_group_id"], $fetched_groups);
                            if (!$validate_group["result"]) {
                                $this->set_flashmessage("information", $validate_group["msg"]);
                                redirect("");
                            }
                        }
                        $this->is_auth->_set_session($user);
                        $this->is_auth->_set_last_ip_and_last_login($user["id"], $this->user);
                        $this->is_auth_event->user_logged_in($user["id"]);
                        $this->is_auth->write_log("LOG", $this->input->ip_address() . "  " . $user["username"] . "     Successful login via SSO");
                        $this->_login_logs_inject_db(1, $user["username"]);
                        $this->session->sess_regenerate(true);
                        $this->session->set_userdata("a4l_sso_authentication", true);
                        $this->session->set_userdata("a4l_sso_login", true);
                        if ($this->config->item("allowed_single_session")) {
                            $session_id = session_id();
                            $this->user->set_session($user["id"], $session_id);
                        }
                        if ($this->input->is_ajax_request()) {
                            $response["result"] = true;
                            $this->output->set_content_type("application/json")->set_output(json_encode($response));
                        } else {
                            if (!empty($redirect)) {
                                $data["redirect_to"] = $_SERVER["HTTP_REFERER"];
                            }
                            if (empty($data["redirect_to"])) {
                                redirect("dashboard");
                            }
                            if (strpos($data["redirect_to"], "keyword=") !== false) {
                                $url = explode("keyword=", $data["redirect_to"]);
                                if (empty($url[1])) {
                                    redirect($data["redirect_to"]);
                                }
                                $data["redirect_to"] = $url[0] . "keyword=" . rawurlencode(str_replace("+", "%20", urlencode($url[1])));
                                redirect($data["redirect_to"]);
                            }
                            redirect($data["redirect_to"]);
                        }
                    } else {
                        $this->is_auth->write_log("LOG", $this->input->ip_address() . "  " . $user["username"] . "     Failed to login via SSO");
                        $data["user_banned"] = $user["banned"];
                        $password_lockout = $this->system_preference->get_specified_system_preferences("passwordLockout");
                        if (0 < $password_lockout) {
                            $login_attempts = $this->session->userdata("loginAttemptsByPasswordLockout");
                            $login_attempts = isset($login_attempts[$user_data["id"]]) ? $login_attempts[$user["id"]] : 1;
                            if ($password_lockout <= $login_attempts) {
                                if (!$this->is_auth->is_super_admin($user["user_group_id"])) {
                                    $this->user->set_user($this->user->get_field("id"), ["banned" => 1, "ban_reason" => sprintf($this->lang->line("ban_reason_by_password_lockout"), $password_lockout)]);
                                    $this->session->set_userdata("loginAttemptsByPasswordLockout", [$user["id"] => 1]);
                                }
                            } else {
                                $this->session->set_userdata("loginAttemptsByPasswordLockout", [$user["id"] => ++$login_attempts]);
                            }
                        }
                        $this->_login_logs_inject_db(2, $user["username"]);
                        if (!$this->input->is_ajax_request()) {
                            $data["warningMessageOnLoginPage"] = $this->system_preference->get_value_by_key("warningMessageOnLoginPage");
                            $data["warningMessageOnLoginPage"] = $data["warningMessageOnLoginPage"]["keyValue"];
                        }
                        $response["errors"] = $this->lang->line("auth_login_authentication_incorrect");
                        $response["result"] = false;
                    }
                }
            } else {
                $response["errors"] = sprintf($this->lang->line("not_exists"), "", $email);
            }
        }
        if (isset($response["errors"]) && !empty($response["errors"])) {
            $this->set_flashmessage("information", $response["errors"]);
        }
        redirect(site_url("users/login/"));
    }
    public function delete_signature($id)
    {
        $this->authenticate_exempted_actions();
        $users_path = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . str_pad($this->is_auth->get_user_id(), 10, "0", STR_PAD_LEFT);
        $logged_signature_pic_path = $users_path . DIRECTORY_SEPARATOR . "signature";
        $response["result"] = false;
        $this->load->model("contract_approval_history", "contract_approval_historyfactory");
        $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
        $signature_used = $this->contract_approval_history->load_all(["where" => [["done_by_type", "user"], ["signature_id", $id]]]);
        if ($signature_used) {
            $response["display_message"] = $this->lang->line("delete_error_signature_to_approval");
        } else {
            $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
            $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
            $this->user_signature_attachment->fetch($id);
            $old_signature_pic = $this->user_signature_attachment->get_field("signature");
            if ($old_signature_pic) {
                $this->empty_directory($logged_signature_pic_path . DIRECTORY_SEPARATOR . $old_signature_pic);
            }
            if ($this->user_signature_attachment->delete($id)) {
                $response["result"] = true;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function get_signature_picture($id = 0)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if ($id < 0) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
        $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
        $this->user_signature_attachment->fetch($id);
        $user_id = $this->user_signature_attachment->get_field("user_id");
        $signature_picture = $this->user_signature_attachment->get_field("signature");
        if (!empty($signature_picture)) {
            $fileDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . str_pad($user_id, 10, "0", STR_PAD_LEFT) . DIRECTORY_SEPARATOR . "signature";
            $file = $fileDirectory . DIRECTORY_SEPARATOR . $signature_picture;
            $content = @file_get_contents($file);
            if ($content) {
                $this->load->helper("download");
                force_download($signature_picture, $content);
            }
        }
    }
    public function add_signature()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("users/profile");
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        include APPPATH . "libraries/I18N/Arabic.php";
        $user_id = $this->session->userdata("AUTH_user_id");
        if (!$this->input->post(NULL)) {
            $data["title"] = $this->lang->line("add_signature");
            $data["full_name"] = $this->session->userdata("AUTH_userProfileName");
            if (preg_match("/\\p{Arabic}/u", $data["full_name"])) {
                $data["fonts"] = ["Khebrat-Musamim-Bold" => "Khebrat-Musamim-Bold", "N-Ketab-Italic" => "N-Ketab", "Arslan" => "Arslan Wessam B", "Bahij_Droid_Naskh-Bold" => "Bahij Droid Naskh Bold"];
            } else {
                $data["fonts"] = ["lucida_handwriting" => "Lucida Handwriting", "lucida_console" => "Lucida Console", "brush_script" => "Brush Script MT", "papyrus" => "Papyrus"];
            }
            $Arabic = new I18N_Arabic("Glyphs");
            $this->load->model("user_profile");
            $this->user_profile->fetch(["user_id" => $user_id]);
            $data["initials"] = mb_substr($this->user_profile->get_field("firstName"), 0, 1) . "." . mb_substr($this->user_profile->get_field("lastName"), 0, 1);
            $response["html"] = $this->load->view("users/add_signature", $data, true);
        } else {
            $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
            $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
            $users_path = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . str_pad($this->is_auth->get_user_id(), 10, "0", STR_PAD_LEFT);
            $logged_signature_pic_path = $users_path . DIRECTORY_SEPARATOR . "signature";
            $ext = ".png";
            if (!is_dir($users_path)) {
                @mkdir($users_path, 493, true);
            }
            if (!is_dir($logged_signature_pic_path)) {
                @mkdir($logged_signature_pic_path, 493, true);
            }
            $active_tab = $this->input->post("active_tab", true);
            switch ($active_tab) {
                case "choose":
                    if ($this->input->post("choose", true)) {
                        $choose_post = $this->input->post("choose", true);
                        $this->load->library("PHPTextToImage");
                        if ($choose_post["full_name"]) {
                            $is_arabic = preg_match("/\\p{Arabic}/u", $choose_post["full_name"]);
                            if ($is_arabic) {
                                $Arabic = new I18N_Arabic("Glyphs");
                                $r = $Arabic->utf8Glyphs($choose_post["full_name"]);
                                $response = $this->save_text_to_image($r, $logged_signature_pic_path, $ext, $choose_post["font"], "signature");
                            } else {
                                $response = $this->save_text_to_image($choose_post["full_name"], $logged_signature_pic_path, $ext, $choose_post["font"], "signature");
                            }
                        }
                        if ($choose_post["initials"]) {
                            $response = $this->save_text_to_image($choose_post["initials"], $logged_signature_pic_path, $ext, $choose_post["font"], "initials");
                        }
                    }
                    break;
                case "drawing":
                    if ($this->input->post("draw", true)) {
                        $sig_draw = $this->input->post("draw", true);
                        if (isset($sig_draw["signature"])) {
                            $response = $this->save_drawings_to_image($sig_draw["signature"], $logged_signature_pic_path, $ext, "signature");
                        }
                        if (isset($sig_draw["initials"])) {
                            $response = $this->save_drawings_to_image($sig_draw["initials"], $logged_signature_pic_path, $ext, "initials");
                        }
                    }
                    break;
                case "uploading":
                    if ($this->input->post("upload", true)) {
                        $signatures = $this->input->post("upload", true);
                        if (array_filter($signatures["label"])) {
                            $this->config->load("allowed_file_uploads", true);
                            $response = $this->validate_save_upload_signature($signatures, $logged_signature_pic_path);
                            if ($response["result"]) {
                                $response = $this->validate_save_upload_signature($signatures, $logged_signature_pic_path, true);
                            }
                        } else {
                            $response["result"] = false;
                            $response["validation_errors"]["label"] = $this->lang->line("cannot_be_blank_rule");
                        }
                    }
                    break;
                default:
                    $response["result"] = false;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function save_text_to_image($text, $logged_signature_pic_path, $ext, $font_type, $type)
    {
        $img = new PHPTextToImage();
        $font_path = FCPATH . "assets" . DIRECTORY_SEPARATOR . "signature" . DIRECTORY_SEPARATOR . "fonts" . DIRECTORY_SEPARATOR . $font_type . ".ttf";
        $img->createImage($text, "#000000", "#FFFFFF", $font_path);
        $label = "default_" . $type . "_" . rand();
        $file_path = $logged_signature_pic_path . DIRECTORY_SEPARATOR . $label;
        $result = $img->saveAsPng($file_path);
        if ($result) {
            $this->set_user_signature_attachments($label, $type, $ext);
            if (!$this->user_signature_attachment->insert()) {
                $response["result"] = false;
                $response["validation_errors"] = $this->user_signature_attachment->get("validationErrors");
            } else {
                $response["result"] = true;
            }
        } else {
            $response["validation_errors"][$type] = $this->lang->line("signature_choose_error");
        }
        return $response;
    }
    private function set_user_signature_attachments($label, $type, $ext)
    {
        $this->user_signature_attachment->reset_fields();
        $this->user_signature_attachment->set_field("user_id", $this->session->userdata("AUTH_user_id"));
        $this->user_signature_attachment->set_field("label", $label);
        $this->user_signature_attachment->set_field("type", $type);
        $this->user_signature_attachment->set_field("signature", $label . $ext);
        $this->user_signature_attachment->set_field("is_default", "0");
    }
    private function save_drawings_to_image($drawings_post, $logged_signature_pic_path, $ext, $type)
    {
        $response["result"] = true;
        $img_data = base64_decode($drawings_post);
        $label = "draw_" . $type . "_" . rand();
        $file_path = $logged_signature_pic_path . DIRECTORY_SEPARATOR . $label . $ext;
        $file = fopen($file_path, "w");
        if (fwrite($file, $img_data)) {
            $this->set_user_signature_attachments($label, $type, $ext);
            if (!$this->user_signature_attachment->insert()) {
                $response["result"] = false;
                $response["validation_errors"] = $this->user_signature_attachment->get("validationErrors");
            }
        } else {
            $response["result"] = false;
            $response["validation_errors"][$type] = $this->lang->line("signature_drawing_error");
        }
        fclose($file);
        return $response;
    }
    private function validate_save_upload_signature($signatures, $logged_signature_pic_path, $insert = false)
    {
        $count = 0;
        $allowed_types = $this->config->item("user_signature", "allowed_file_uploads");
        $response["result"] = true;
        foreach ($signatures["label"] as $index => $label) {
            $count++;
            $this->user_signature_attachment->reset_fields();
            if (!empty($_FILES["upload"]["name"][$index])) {
                $ext = pathinfo($_FILES["upload"]["name"][$index], PATHINFO_EXTENSION);
                $config = ["upload_path" => $logged_signature_pic_path, "max_size" => 10240, "max_height" => 3000, "max_width" => 3000, "allowed_types" => $allowed_types];
                $config["file_name"] = "user_signature" . $label . "." . $ext;
                $_FILES["signatures" . $count] = [];
                $_FILES["signatures" . $count]["name"] = $config["file_name"];
                $_FILES["signatures" . $count]["type"] = $_FILES["upload"]["type"][$index];
                $_FILES["signatures" . $count]["tmp_name"] = $_FILES["upload"]["tmp_name"][$index];
                $_FILES["signatures" . $count]["error"] = $_FILES["upload"]["error"][$index];
                $_FILES["signatures" . $count]["size"] = $_FILES["upload"]["size"][$index];
                $this->user_signature_attachment->set_field("label", $label);
                $this->user_signature_attachment->set_field("user_id", $this->session->userdata("AUTH_user_id"));
                $this->user_signature_attachment->set_field("type", "signature");
                $this->user_signature_attachment->set_field("signature", $config["file_name"]);
                $this->user_signature_attachment->set_field("is_default", "0");
                $this->load->library("upload", $config);
                $this->upload->initialize($config);
                if ($this->user_signature_attachment->validate()) {
                    if ($insert) {
                        if ($this->upload->do_upload("signatures" . $count)) {
                            $this->user_signature_attachment->insert();
                        } else {
                            $response["result"] = false;
                            $response["validation_errors"]["upload"] = sprintf($this->lang->line("file_upload_error"), $_FILES["upload"]["name"][$index]) . $this->upload->display_errors();
                        }
                    }
                } else {
                    $response["result"] = false;
                    $response["validation_errors"] = $this->user_signature_attachment->get("validationErrors");
                }
            } else {
                $response["result"] = false;
                $response["validation_errors"]["file"] = $this->lang->line("signature_upload_error");
            }
        }
        return $response;
    }
    public function set_default_signature()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $response["result"] = false;
        if ($this->input->post("id", true)) {
            $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
            $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
            if ($this->user_signature_attachment->fetch($this->input->post("id", true))) {
                $this->db->where("is_default = '1'")->update("user_signature_attachments", ["is_default" => "0"]);
                $this->user_signature_attachment->set_field("is_default", "1");
                $response["result"] = $this->user_signature_attachment->update();
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function customer_support()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $user_name = $this->session->userdata("AUTH_userProfileName");
        $user_id = $this->session->userdata("AUTH_user_id");
        $user_email_address = $this->session->userdata("AUTH_email_address");
        $on_cloud_client_data = $on_server_client_data = "";
        $this->load->model("instance_data");
        $installation_type = $this->instance_data->get_value_by_key("installationType");
        $this->load->model("user_api_key", "user_api_keyfactory");
        $this->user_api_key = $this->user_api_keyfactory->get_instance();
        $x_api_key = $this->user_api_key->create($user_id);
        $licenses = $this->licensor->get_all_licenses();
        $organization_name = $licenses["core"][APPNAME]["clientName"] ?? APPNAME;
        if ($installation_type["keyValue"] === "on-cloud") {
            $instance_data = $this->session->userdata("instance_data");
            $instanceID = $instance_data["instanceID"] ?? "";
            $on_cloud_client_data = "&instanceId=" . $instanceID . "&organizationName=" . str_replace(" ", "+", $organization_name);
        } else {
            $on_server_client_data = "&url=" . urlencode(base_url()) . "&organizationName=" . str_replace(" ", "+", $organization_name);
        }
        $response = [];
        $response["result"] = true;
        $response["apiKey"] = $x_api_key;
        $response["src"] = "https://support.sheria360.com/start?fullName=" . str_replace(" ", "+", $user_name) . "&email=" . $user_email_address . "&userId=" . $user_id . ($installation_type["keyValue"] === "on-cloud" ? $on_cloud_client_data : $on_server_client_data);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function get_api_token_data()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $user_id = $this->session->userdata("AUTH_user_id");
        $this->load->model("user_api_key", "user_api_keyfactory");
        $this->user_api_key = $this->user_api_keyfactory->get_instance();
        $x_api_key = $this->user_api_key->create($user_id);
        $response["result"] = true;
        $response["apiKey"] = $x_api_key;
        $response["apiBaseUrl"] = $this->config->base_url();
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function user_guide()
    {
     //   $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("user_guide"));
        $data = [];
        if ($this->input->is_ajax_request()) {
            if ($this->input->post(NULL)) {
                $response["status"] = true;
                $module = $this->input->post("module");
                if (!empty($module)) {
                    if ($this->input->post("manage")) {
                        if ($this->input->post("submit")) {
                            $data["workthrough"] = [];
                            $walkthrough_post = $this->input->post("workthrough");
                            if (!empty($walkthrough_post) && is_array($this->input->post("workthrough"))) {
                                $workthrough_arr = [];
                                foreach ($walkthrough_post as $key => $workthrough) {
                                    if ($workthrough == 1) {
                                        array_push($workthrough_arr, $key);
                                    }
                                }
                                if (!empty($workthrough_arr)) {
                                    $userData["workthrough"] = serialize($workthrough_arr);
                                } else {
                                    $userData["workthrough"] = "";
                                }
                                $data["workthrough"] = $workthrough_arr;
                            }
                            $this->user->reset_fields();
                            $this->user->fetch($this->is_auth->get_user_id());
                            $this->user->set_field("workthrough", serialize($data["workthrough"]));
                            $response["status"] = $this->user->update();
                            if ($response["status"]) {
                                $this->session->set_userdata("workthrough", $userData["workthrough"]);
                                $response["workthrough"] = is_null($this->workthrough) ? "" : $data["workthrough"];
                            }
                        } else {
                            $this->user->reset_fields();
                            $this->user->fetch($this->is_auth->get_user_id());
                            $data["workthrough"] = unserialize($this->user->get_field("workthrough"));
                            $data["workthrough"] = $data["workthrough"] ? $data["workthrough"] : [];
                            $response["html"] = $this->load->view("home/walkthrough_settings", $data, true);
                        }
                        $this->output->set_content_type("application/json")->set_output(json_encode($response));
                    } else {
                        if ($this->input->post("clear_all")) {
                            $workthrough = "";
                            $this->user->reset_fields();
                            $this->user->fetch($this->is_auth->get_user_id());
                            $this->user->set_field("workthrough", $workthrough);
                            $response["status"] = false;
                            if ($this->user->update()) {
                                $this->session->set_userdata("workthrough", $workthrough);
                                $this->workthrough = $workthrough;
                                $response["workthrough"] = $workthrough;
                                $response["status"] = true;
                            }
                        } else {
                            $workthrough = $this->session->userdata("workthrough");
                            $workthrough = !empty($workthrough) ? unserialize($workthrough) : [];
                            if (!in_array($module, $workthrough)) {
                                array_push($workthrough, $module);
                            }
                            $workthrough = serialize($workthrough);
                            $this->session->set_userdata("workthrough", $workthrough);
                            $this->user->reset_fields();
                            $this->user->fetch($this->is_auth->get_user_id());
                            $this->user->set_field("workthrough", $workthrough);
                            if ($this->user->update()) {
                                $this->workthrough = $this->session->userdata("workthrough");
                            }
                        }
                    }
                } else {
                    $this->user->reset_fields();
                    $this->user->fetch($this->is_auth->get_user_id());
                    $this->user->set_field("user_guide", 1);
                    if ($this->user->update()) {
                        $this->session->set_userdata("user_guide", date("Y-m-d H:i:s", time()));
                    }
                    if ($this->input->post("module") !== "no") {
                        $this->instance_data->set_value_by_key("first_sign_in", "no");
                    }
                    $this->user_guide = $this->session->userdata("user_guide");
                }
            } else {
                $data["operator_options"] = [];
                $response["status"] = true;
                $response["html"] = $this->load->view("users/first_guide", $data, true);
                $this->load->library("dms");
                $this->dms->add_default_generator_template("hearing");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function initial_setup()
    {
        $data = [];
        if ($this->input->is_ajax_request()) {
            if ($this->input->post(NULL)) {
                $response = [];
                $this->load->model("system_preference");
                $system_values = $this->input->post("systemValues");
                if (isset($system_values["systemTimezone"]) && !empty($system_values["systemTimezone"])) {
                    $this->system_preference->set_value_by_key("SystemValues", "systemTimezone", $system_values["systemTimezone"]);
                }
                if (isset($system_values["caseValueCurrency"]) && !empty($system_values["caseValueCurrency"])) {
                    $this->system_preference->set_value_by_key("DefaultValues", "caseValueCurrency", $system_values["caseValueCurrency"]);
                }
                if (isset($system_values["caseTypeProjectId"]) && !empty($system_values["caseTypeProjectId"])) {
                    $this->system_preference->set_value_by_key("DefaultValues", "caseTypeProjectId", $system_values["caseTypeProjectId"]);
                }
                $language_initial_setup = $this->session->userdata("AUTH_language_initial_setup");
                $this->load->model("user_preference");
                if (isset($language_initial_setup) && !empty($language_initial_setup)) {
                    $this->user_preference->set_value("language", $language_initial_setup, true);
                }
            } else {
                $response["status"] = true;
                $this->load->helper("timezone");
                $time_zone_list = get_timezone();
                $time_zone_list[""] = $this->lang->line("not_set");
                $data["time_zone_list"] = $time_zone_list;
                $this->load->model("country", "countryfactory");
                $this->load->model(["case_type", "provider_group"]);
                $this->load->model("case_type");
                $this->country = $this->countryfactory->get_instance();
                $saved_preferences = $this->system_preference->load_list(["order_by" => "groupName asc, keyName asc"], ["optgroup" => "groupName"]);
                $currencies_list = $this->country->load_currency_list("currencyCode", "currency_country");
                $data["currencies_list"] = $currencies_list;
                $data["case_types"] = ["" => $this->lang->line("none")] + $this->case_type->load_list(["where" => ["isDeleted", 0]]);
                $data["title"] = $this->lang->line("initial_setup");
                $data["case_value_currency"] = isset($saved_preferences["DefaultValues"]["caseValueCurrency"]) ? $saved_preferences["DefaultValues"]["caseValueCurrency"] : "";
                $data["case_type_projectId"] = isset($saved_preferences["DefaultValues"]["caseTypeProjectId"]) ? $saved_preferences["DefaultValues"]["caseTypeProjectId"] : "";
                $data["system_timezone"] = isset($saved_preferences["SystemValues"]["systemTimezone"]) ? $saved_preferences["SystemValues"]["systemTimezone"] : "";
                $response["html"] = $this->load->view("users/initial_setup", $data, true);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function add_user_dialog()
    {
        if ($this->input->post()) {
            if ($this->input->post("generate_pass")) {
                $response["random_password"] = $this->random_str();
                $response["status"] = true;
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $post_data = $this->input->post();
                $_POST["userData"]["confirmPassword"] = $post_data["userData"]["password"];
                $this->save(0);
            }
        } else {
            $this->load->model("user_group", "user_groupfactory");
            $this->user_group = $this->user_groupfactory->get_instance();
            $this->load->model("seniority_level");
            $this->load->model("user_profile");
            $data["title"] = $this->lang->line("add_new_user");
            $data["random_password"] = $this->random_str();
            $data["user_code"] = $this->user_profile->auto_generate_user_code();
            $data["user_groups"] = ["" => $this->lang->line("none")] + $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]]);
            $data["seniority_level"] = ["" => $this->lang->line("none")] + $this->seniority_level->load_list([]);
            $response["html"] = $this->load->view("users/add_user_dialog", $data, true);
            $response["status"] = true;
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function random_str($length = 15, $keyspace = "01278uvwxyzABXYZ@#\$9ab345cdeCDE%^&*FG3456HIJKLMNOPQRS%^&*TUVWfghijklmn0127opqrst%^&*")
    {
        $str = "";
        $max = mb_strlen($keyspace, "8bit") - 1;
        if ($max < 1) {
            throw new Exception("\$keyspace must be at least two characters long");
        }
        for ($i = 0; $i < $length; $i++) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }
}

?>