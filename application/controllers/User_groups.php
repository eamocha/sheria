<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class User_groups extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
    }
    public function check_actions_changes()
    {
        $modules = ["core", "money", "contract"];
        $dirActions = $this->get_dir_actions("core");
        foreach ($modules as $module) {
            $dirActions = $this->get_dir_actions($module);
            $staticActions = $this->get_actions_aliases($module);
            $staticActions = array_keys($staticActions);
            echo $module;
            print_r("<pre>");
            print_r($module);
            print_r(array_diff($dirActions, $staticActions));
            print_r("</pre>");
        }
        exit;
    }
    public function index($type = "")
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                exit("Error");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->user_group->k_load_all_user_groups($filter, $sortable);
            } else {
                $response["records"] = $this->user_group->k_load_all_user_groups($filter, $sortable);
                $response["html"] = $this->load->view("users/search_results", $response["records"], true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            if (!strcmp($type, "report")) {
                $data["isReport"] = true;
                $pageTitle = $this->lang->line("user_group_management_report");
            } else {
                $data["isReport"] = false;
                $pageTitle = $this->lang->line("user_groups");
            }
            $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $pageTitle);
            $this->load->model("system_preference");
            $data["isUserMaker"] = $this->is_auth->user_is_maker();
            $data["isUserChecker"] = $this->is_auth->user_is_checker();
            $data["makerCheckerFeatureStatus"] = $this->system_preference->get_value_by_key("makerCheckerFeatureStatus");
            $data["makerCheckerFeatureStatus"] = $data["makerCheckerFeatureStatus"]["keyValue"] == "yes" ? true : false;
            $data["records"] = $this->user_group->all_groups();
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["fb"] = $this->session->flashdata("fb");
            $this->load->model("system_preference");
            $api_data = $this->system_preference->get_value_by_key("APIEnableStatus");
            $active_directory_api_data = $this->system_preference->get_value_by_key("adEnabled");
            $data["isUserMaker"] = $this->is_auth->user_is_maker();
            $data["isUserChecker"] = $this->is_auth->user_is_checker();
            $data["makerCheckerFeatureStatus"] = $this->system_preference->get_value_by_key("makerCheckerFeatureStatus");
            $data["makerCheckerFeatureStatus"] = $data["makerCheckerFeatureStatus"]["keyValue"] == "yes" ? true : false;
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("scripts/user_groups_search", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("user_groups/index", $data);
        }
    }
    public function management_report()
    {
        $this->index("report");
    }
    public function add($cloned_id = "")
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("add_user_group");
                $data["fieldLabel"] = $this->lang->line("name");
                $data["component"] = "user_groups";
                $response["html"] = $this->load->view("administration/onthefly_template", $data, true);
                $response["isLayoutRTL"] = $this->is_auth->is_layout_rtl();
            }
            if ($this->input->post(NULL)) {
                $id = 0;
                $this->check_user_group_accessibility($id, "save");
                $isUserMaker = $this->is_auth->user_is_maker();
                if ($isUserMaker) {
                    $this->user_group->set_field("flagNeedApproval", "1");
                    $this->user_group->set_field("needApprovalOnAdd", empty($id) ? "1" : "0");
                } else {
                    $this->user_group->set_field("flagNeedApproval", "0");
                    $this->user_group->set_field("needApprovalOnAdd", "0");
                }
                $this->user_group->set_fields($this->input->post(NULL));
                $this->user_group->set_field("system_group", "0");
                $result = $this->user_group->insert();
                $response["insertStatus"] = $result;
                $response["id"] = $this->user_group->get_field("id");
                $response["name"] = $this->input->post("name");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->save(0, $cloned_id);
        }
    }
    public function edit($id = "0")
    {
        $this->check_user_group_flag_need_approval($id);
        $this->save($id);
    }
    public function clone_group($cloned_id)
    {
        $this->add($cloned_id);
    }
    private function save($id = "0", $cloned_id = "")
    {
        $this->check_user_group_accessibility($id, "save");
        $isUserMaker = $this->is_auth->user_is_maker();
        $data = [];
        $userGroupFormHasChanged = true;
        $new_user_group_id = $id;
        if ($this->input->post(NULL)) {
            if ($isUserMaker) {
                $this->user_group->set_field("flagNeedApproval", "1");
                $this->user_group->set_field("needApprovalOnAdd", empty($id) ? "1" : "0");
            } else {
                $this->user_group->set_field("flagNeedApproval", "0");
                $this->user_group->set_field("needApprovalOnAdd", "0");
            }
            $this->user_group->set_fields($this->input->post(NULL));
            if (empty($id)) {
                $this->user_group->set_field("system_group", "0");
                if (($result = $this->user_group->insert()) && $isUserMaker) {
                    $new_user_group_id = $this->user_group->get_field("id");
                    $this->insert_user_group_changes_authorization("add", $this->user_group->get_field("id"), $this->input->post(NULL));
                }
            } else {
                if ($this->user_group->get_field("system_group") == 1) {
                    $this->set_flashmessage("warning", $this->lang->line("user_group_edition_not_approved"));
                    redirect("user_groups/index");
                } else {
                    if ($isUserMaker) {
                        if ($result = $this->user_group->validate()) {
                            $this->user_group->fetch($id);
                            $oldData = $this->user_group->get_fields();
                            $this->user_group->reset_fields();
                            $userGroupFormHasChanged = $this->insert_user_group_changes_authorization("edit", $id, $this->input->post(NULL), $oldData);
                        }
                    } else {
                        $result = $this->user_group->update();
                    }
                }
            }
            if ($result) {
                $this->load->model("user_group_permission");
                if ($isUserMaker) {
                    if (!$userGroupFormHasChanged) {
                        $this->set_flashmessage("information", $this->lang->line("no_changes"));
                    } else {
                        $this->set_flashmessage("warning", $this->lang->line("changes_user_group_data_need_aproval"));
                        if ($cloned_id) {
                            $this->load->model("user_group_permissions_changes_authorization", "user_group_permissions_changes_authorizationfactory");
                            $this->UGPCA = $this->user_group_permissions_changes_authorizationfactory->get_instance();
                            $data = $this->user_group_permission->get_permission_data($cloned_id);
                            foreach ($data as $key => $value) {
                                $this->UGPCA->set_field("columnName", "permission");
                                $this->UGPCA->set_field("columnValue", NULL);
                                $this->UGPCA->set_field("columnRequestedValue", $this->user_group_permission->_serialize($data));
                                $this->UGPCA->set_field("module", $key);
                                $this->UGPCA->set_field("columnStatus", "Pending");
                                $this->UGPCA->set_field("affectedUserGroupId", $new_user_group_id);
                                $this->UGPCA->set_field("makerId", $this->session->userdata("AUTH_user_id"));
                                $this->UGPCA->set_field("createdOn", date("Y-m-d H:i:s"));
                                $result = $this->UGPCA->insert();
                                $this->UGPCA->reset_fields();
                                $notification_data = ["status" => "unseen", "message" => sprintf($this->lang->line("changes_user_group_permission_need_aproval"), $post_dta["name"])];
                                $this->load->model("notification", "notificationfactory");
                                $this->notification = $this->notificationfactory->get_instance();
                                $this->notification->notify_checkers_list($notification_data);
                            }
                        }
                    }
                    redirect("user_groups/index");
                } else {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                    if ($cloned_id) {
                        $data = $this->user_group_permission->get_permission_data($cloned_id);
                        if (!empty($data)) {
                            $this->user_group_permission->set_permission_data($this->user_group->get_field("id"), $data);
                        }
                        if ($this->input->post("submitAndUpdate")) {
                            redirect("user_groups/permissions/" . $this->user_group->get_field("id") . "/core");
                        }
                    }
                    redirect("user_groups/index");
                }
            } else {
                if ($this->user_group->is_valid()) {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                    redirect("user_groups/index");
                }
            }
        } else {
            $this->user_group->fetch($id);
        }
        $data["id"] = $id;
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("user_groups"));
        if ($cloned_id) {
            $this->load->model("user_group_permission");
            $old_group = $this->user_group->load_group_by_id($cloned_id);
            $data["cloned_group"] = $old_group["name"];
            $this->load->view("user_groups/form_clone", $data);
        } else {
            $this->load->view("user_groups/form", $data);
        }
    }
    public function delete($id)
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $this->check_user_group_accessibility($id, "delete");
            $this->user_group->fetch($id);
            if ($this->user_group->get_field("system_group") == 1) {
                $response["msg"] = $this->lang->line("user_group_edition_not_approved");
                $response["status"] = "";
            } else {
                $this->load->model("user", "userfactory");
                $this->user = $this->userfactory->get_instance();
                $hasUsers = $this->user->user_group_has_users($id);
                if (!$hasUsers) {
                    $this->load->model("user_group_permissions_changes_authorization", "user_group_permissions_changes_authorizationfactory");
                    $this->UGPCA = $this->user_group_permissions_changes_authorizationfactory->get_instance();
                    $hasPermission = $this->UGPCA->user_group_has_permissions($id);
                    if (!$hasPermission) {
                        $this->load->model("user_group_permission");
                        if ($this->user_group_permission->fetch(["user_group_id" => $id])) {
                            $this->user_group_permission->delete($this->user_group_permission->get_field("id"));
                        }
                        $this->load->model("user_groups_changes_authorization", "user_groups_changes_authorizationfactory");
                        $this->user_groups_changes_authorization = $this->user_groups_changes_authorizationfactory->get_instance();
                        $this->user_groups_changes_authorization->delete_changes_by_group_id($id);
                        if ($this->user_group->delete($id)) {
                            $response["msg"] = $this->lang->line("record_deleted");
                            $response["status"] = 202;
                        } else {
                            $response["msg"] = $this->lang->line("record_not_deleted");
                            $response["status"] = 102;
                        }
                    } else {
                        $response["msg"] = $this->lang->line("user_group_not_deleted_cz_contains_one_permission");
                        $response["status"] = 104;
                    }
                } else {
                    $response["msg"] = $this->lang->line("user_group_not_deleted_cz_contains_one_user");
                    $response["status"] = 104;
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function list_users($user_group_id = "")
    {
        $this->check_user_group_accessibility($user_group_id);
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $data["records"] = [];
        if ($user_group_id) {
            $data["records"] = $this->user->get_user_groups($user_group_id);
        }
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("user_groups"));
        $data["user_groups_list"] = $this->user_group->load_list(["where" => ["name !=", $this->user_group->get("superAdminInfosystaName")]], ["firstLine" => ["" => $this->lang->line("choose_user_group")]]);
        $data["user_group_id"] = $user_group_id;
        $this->load->view("user_groups/list_users", $data);
    }
    protected function list_actions($file)
    {
        if (!is_file($file)) {
            return [];
        }
        $fileContents = file_get_contents($file);
        $functionsRegEx = "/public[\\s\\n]+function[\\s\\n]+([^_]\\S+)[\\s\\n]*\\(/";
        $functionsList = [];
        preg_match_all($functionsRegEx, $fileContents, $functionsList);
        return count($functionsList) && isset($functionsList[1]) ? $functionsList[1] : [];
    }
    private function get_aliases($module = "")
    {
        $result = [];
        $aliases = $this->user_group->get_aliases($module);
        if ($aliases->num_rows()) {
            $result = [];
            foreach ($aliases->result_array() as $alias) {
                $result[trim($alias["action"], "/")] = $alias["alias"];
            }
        }
        return $result;
    }
    public function permissions_list_by_user($user_id)
    {
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $group_id = $this->user->load(["select" => ["user_group_id"], "where" => ["id", $user_id]]);
        $this->check_user_group_accessibility($group_id);
        $data["core_data"] = $this->permissions($group_id, "core", true);
        $data["money_data"] = $this->permissions($group_id, "money", true);
        $data["headerBg"] = "#cccccc";
        $filename = urlencode($this->lang->line("permissions_list_by_user"));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/permissions_list", $data);
        $this->load->view("excel/footer");
    }
    public function permissions_list_by_group($group_id)
    {
        $this->check_user_group_accessibility($group_id);
        $data["core_data"] = $this->permissions($group_id, "core", true);
        $data["money_data"] = $this->permissions($group_id, "money", true);
        $data["headerBg"] = "#cccccc";
        $filename = urlencode($this->lang->line("permissions_list_by_group"));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/permissions_list", $data);
        $this->load->view("excel/footer");
    }
    public function permissions($group_id = 0, $selectedModule = "", $return_data = false)
    {
        $this->check_user_group_accessibility($group_id);
        $this->load->model("user_group_permission");
        $groupPermissions = $this->user_group_permission->get_permissions($group_id, false);
        $this->load->model("user_group_permissions_changes_authorization", "user_group_permissions_changes_authorizationfactory");
        $this->UGPCA = $this->user_group_permissions_changes_authorizationfactory->get_instance();
        $isUserMaker = $this->is_auth->user_is_maker();
        $result = false;
        $isFormChanged = true;
        if ($this->input->post(NULL)) {
            if (empty($selectedModule)) {
                redirect("user_groups/permissions/" . $group_id);
            }
            $group_id = $this->input->post("group_id");
            if (!isset($groupPermissions[$selectedModule])) {
                $groupPermissions[$selectedModule] = [];
            }
            $oldModulePermissions = $groupPermissions[$selectedModule];
            $groupPermissions[$selectedModule] = (array) $this->input->post("permissions");
            if ($isUserMaker) {
                $newPermissions = array_diff_assoc($groupPermissions[$selectedModule], $oldModulePermissions);
                if (empty($newPermissions)) {
                    $removedPermissions = array_diff_assoc($oldModulePermissions, $groupPermissions[$selectedModule]);
                    if (!empty($removedPermissions)) {
                        $newPermissions = $groupPermissions[$selectedModule];
                    }
                }
                if (!empty($newPermissions)) {
                    $this->user_group_permission->fetch(["user_group_id" => $group_id]);
                    $this->UGPCA->set_field("columnName", "permission");
                    $this->UGPCA->set_field("columnValue", $this->user_group_permission->get_field("data"));
                    $this->UGPCA->set_field("columnRequestedValue", $this->user_group_permission->_serialize($groupPermissions));
                    $this->UGPCA->set_field("module", $selectedModule);
                    $this->UGPCA->set_field("columnStatus", "Pending");
                    $this->UGPCA->set_field("affectedUserGroupId", $group_id);
                    $this->UGPCA->set_field("makerId", $this->session->userdata("AUTH_user_id"));
                    $this->UGPCA->set_field("createdOn", date("Y-m-d H:i:s"));
                    $result = $this->UGPCA->insert();
                    $groupData = $this->user_group->load_group_by_id($group_id);
                    $notificationData = ["status" => "unseen", "message" => sprintf($this->lang->line("changes_user_group_permission_need_aproval"), $groupData["name"])];
                    $this->load->model("notification", "notificationfactory");
                    $this->notification = $this->notificationfactory->get_instance();
                    $this->notification->notify_checkers_list($notificationData);
                } else {
                    $isFormChanged = false;
                }
            } else {
                $result = $this->user_group_permission->set_permission_data($group_id, $groupPermissions);
            }
            if ($result) {
                if ($isUserMaker) {
                    $this->set_flashmessage("warning", $this->lang->line("changes_user_group_data_need_aproval"));
                } else {
                    $this->set_flashmessage("success", $this->lang->line("permissions_saved"));
                }
            } else {
                if (!$isFormChanged) {
                    $this->set_flashmessage("warning", $this->lang->line("no_changes_applied_to_permissions"));
                } else {
                    $this->set_flashmessage("warning", $this->lang->line("permissions_not_saved"));
                }
            }
            redirect("user_groups/permissions/" . $group_id . "/" . $selectedModule);
        }
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("user_groups"));
        $controllers = [];
        $actions = [];
        $this->load->library("inflector");
        $data = $this->get_controllers_actions($selectedModule);
        $data["userGroups"] = $this->user_group->load_list(["where" => [["name !=", $this->user_group->get("superAdminInfosystaName")], ["needApprovalOnAdd !=", "1"]]], ["firstLine" => ["" => $this->lang->line("select_user_group")]]);
        $data["groupPermissions"] = isset($groupPermissions[$selectedModule]) ? $groupPermissions[$selectedModule] : [];
        $data["group_id"] = $group_id;
        $data["_modules"] = $this->read_controllers_actions();
        $data["selectedModule"] = $selectedModule;
        if ($return_data) {
            return $data;
        }
        $this->load->model("system_preference");
        $data["isUserMaker"] = $isUserMaker;
        $data["isUserChecker"] = $this->is_auth->user_is_checker();
        $data["makerCheckerFeatureStatus"] = $this->system_preference->get_value_by_key("makerCheckerFeatureStatus");
        $data["makerCheckerFeatureStatus"] = $data["makerCheckerFeatureStatus"]["keyValue"] == "yes" ? true : false;
        $data["modulePendingApprovals"] = $this->UGPCA->module_has_pending_approvals($group_id, $selectedModule);
        $this->includes("bootstrap/js/bootstrap-affix", "js");
        $this->load->view("user_groups/permissions", $data);
    }
    private function get_controllers_actions($module = "")
    {
        if (empty($module)) {
            return ["controllers" => [], "actions" => []];
        }
        $controllersJSON = [];
        $actionsJSON = [];

        if ($this->cloud_installation_type) {
            $licenseControllers = [
                "subscription" => "Subscription"
            ];
            $licenseActions = [
                "subscription" => [
                    "subscribe/" => "Subscribe now",
                    "details/" => "Subscription",
                    "adjust_plan/" => "Adjust Plan",
                    "update_card/" => "Update Card",
                    "add_user/" => "Purchase Additional User"
                ]
            ];
        } else {
            $licenseControllers = [
                "license_manager" => "Manage License"
            ];
            $licenseActions = [
                "license_manager" => [
                    "install/" => "Install"
                ]
            ];
        }
        $this->load->config('core_controllers');
        $coreControllers = $this->config->item('core_controllers');
        $coreActions = $this->config->item('core_actions');

        $controllersJSON["core"] = json_encode(array_merge($licenseControllers, $coreControllers));
        $actionsJSON["core"] = json_encode(array_merge($licenseActions, $coreActions));

        // money
        $this->load->config('money_controllers');

        $moneyControllers = $this->config->item('money_controllers');
        $moneyActions = $this->config->item('money_actions');

        $controllersJSON["money"] = json_encode($moneyControllers);
        $actionsJSON["money"] = json_encode($moneyActions);
        //contracts
        $this->load->config('contract_controllers');

        $contractControllers = $this->config->item('contract_controllers');
        $contractActions = $this->config->item('contract_actions');

        $controllersJSON["contract"] = json_encode($contractControllers);
        $actionsJSON["contract"] = json_encode($contractActions);

        $controllersJSON["api"] = "{}";
        $actionsJSON["api"] = "{}";
        $controllersJSON["exporter"] = "{}";
        $actionsJSON["exporter"] = "{}";
        $controllersJSON["outlook"] = "{}";
        $actionsJSON["outlook"] = "{}";
        $controllersJSON["microsoft-teams"] = "{}";
        $actionsJSON["microsoft-teams"] = "{}";
        $controllers = (array) json_decode($controllersJSON[$module]);
        $actions = (array) json_decode($actionsJSON[$module]);
        return compact("actions", "controllers");
    }
    private function get_actions_aliases($module)
    {
        $data = $this->get_controllers_actions($module);
        $outputPermissions = [];
        foreach ($data["controllers"] as $controllerName => $controllerAlias) {
            foreach ($data["actions"] as $controllerAction => $actionsList) {
                if ($controllerName === $controllerAction) {
                    $controllerKey = "/" . $controllerName . "/";
                    $outputPermissions[$controllerKey] = $controllerAlias;
                    $actionsTempList = json_decode(json_encode($actionsList), true);
                    $actionTempKey = "";
                    foreach ($actionsTempList as $actionKey => $actionAlias) {
                        $actionTempKey = $controllerKey . $actionKey;
                        $actionFullAlais = $controllerAlias . " - " . $actionAlias;
                        $outputPermissions[$actionTempKey] = $actionFullAlais;
                    }
                }
            }
        }
        return $outputPermissions;
    }
    private function _get_active_modules()
    {
        $modules = $this->activeInstalledModules;
        $modules["money"] = "Money";
        return $modules;
    }
    private function read_controllers_actions($selectedModule = "")
    {
        $exceptions = [];
        $controllers = [];
        $actions = [];
        $aliases = $this->get_aliases($selectedModule);
        $_modules = ["" => "Select Module", "core" => "Core"];
        $_modulePath = ["core" => FCPATH . "application" . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR];
        $activeInstalledModules = $this->_get_active_modules();
        foreach ($activeInstalledModules as $installedModule => $modName) {
            $_modules[$installedModule] = $modName;
            $_modulePath[$installedModule] = FCPATH . "modules" . DIRECTORY_SEPARATOR . $installedModule . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR;
        }
        if (empty($selectedModule)) {
            return $_modules;
        }
        $path = $_modulePath[$selectedModule];
        foreach ($this->is_auth->excempted_uri as $exception) {
            $exceptions[] = trim($exception, "/");
        }
        unset($exception);
        $dirHan = opendir($path);
        while ($file = readdir($dirHan)) {
            if (is_file($path . $file)) {
                $controller = mb_substr($file, 0, strrpos($file, "."));
                if (!in_array($controller, $exceptions)) {
                    $controllers[$controller] = $this->inflector->humanize(isset($aliases[$controller]) ? $aliases[$controller] : $controller);
                    $_tempActions = $this->list_actions($path . $file);
                    sort($_tempActions);
                    $actions[$controller] = [];
                    foreach ($_tempActions as $action) {
                        if (!in_array($controller . "/" . $action, $exceptions)) {
                            $actions[$controller][$action . "/"] = $this->inflector->humanize(isset($aliases[$controller . "/" . $action]) ? $aliases[$controller . "/" . $action] : $action);
                        }
                    }
                }
            }
        }
        closedir($dirHan);
        echo "<pre>";
        if (!isset($aliases[""])) {
            echo $selectedModule;
            echo ",,,";
            echo $this->inflector->humanize($selectedModule);
        }
        echo PHP_EOL;
        foreach ($controllers as $controllerName => $controllerAlias) {
            if (!isset($aliases[$controllerName])) {
                echo $selectedModule;
                echo ",";
                echo $controllerName;
                echo ",,";
                echo $controllerAlias;
                echo "";
                echo PHP_EOL;
            }
            if (isset($action[$controllerName])) {
                foreach ($actions[$controllerName] as $actionName => $actionAlias) {
                    if (!isset($aliases[$controllerName . "/" . trim($actionName, "/")])) {
                        echo $selectedModule;
                        echo ",";
                        echo $controllerName;
                        echo ",";
                        echo trim($actionName, "/");
                        echo ",";
                        echo $actionAlias;
                        echo "";
                        echo PHP_EOL;
                    }
                }
            }
        }
        echo "</pre>";
        exit;
    }
    private function readable_controllers_actions($selectedModule = "")
    {
        $exceptions = [];
        $controllers = [];
        $actions = [];
        $aliases = $this->get_aliases($selectedModule);
        $_modules = ["" => "Select Module", "core" => "Core"];
        $_modulePath = ["core" => FCPATH . "application" . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR];
        $activeInstalledModules = $this->_get_active_modules();
        foreach ($activeInstalledModules as $installedModule => $modName) {
            $_modules[$installedModule] = $modName;
            $_modulePath[$installedModule] = FCPATH . "modules" . DIRECTORY_SEPARATOR . $installedModule . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR;
        }
        if (empty($selectedModule)) {
            return $_modules;
        }
        $path = $_modulePath[$selectedModule];
        foreach ($this->is_auth->excempted_uri as $exception) {
            $exceptions[] = trim($exception, "/");
        }
        unset($exception);
        $dirHan = opendir($path);
        while ($file = readdir($dirHan)) {
            if (is_file($path . $file)) {
                $controller = mb_substr($file, 0, strrpos($file, "."));
                if (!in_array($controller, $exceptions)) {
                    $controllers[$controller] = $this->inflector->humanize(isset($aliases[$controller]) ? $aliases[$controller] : $controller);
                    $_tempActions = $this->list_actions($path . $file);
                    sort($_tempActions);
                    $actions[$controller] = [];
                    foreach ($_tempActions as $action) {
                        if (!in_array($controller . "/" . $action, $exceptions)) {
                            $actions[$controller][$action . "/"] = $this->inflector->humanize(isset($aliases[$controller . "/" . $action]) ? $aliases[$controller . "/" . $action] : $action);
                        }
                    }
                }
            }
        }
        closedir($dirHan);
        echo "<pre>";
        echo PHP_EOL;
        foreach ($controllers as $controllerName => $controllerAlias) {
            $i = 1;
            if (!isset($aliases[$controllerName])) {
                echo "<h3>";
                echo $controllerAlias;
                echo "</h3>";
                echo PHP_EOL;
            }
            if (isset($action[$controllerName])) {
                foreach ($actions[$controllerName] as $actionName => $actionAlias) {
                    if (!isset($aliases[$controllerName . "/" . trim($actionName, "/")])) {
                        echo "<p>";
                        echo $i++;
                        echo "- ";
                        echo trim($actionName, "/");
                        echo " , <strong>";
                        echo $actionAlias;
                        echo "</strong><p>";
                        echo PHP_EOL;
                    }
                }
            }
        }
        echo "</pre>";
        exit;
    }
    private function check_user_group_accessibility($id, $mode = "")
    {
        $systemPreferences = $this->session->userdata("systemPreferences");
        $authUserGroupId = $this->session->userdata("AUTH_user_group_id");
        $this->user_group->fetch(["name" => $this->user_group->get("superAdminInfosystaName")]);
        $superAdminInfosystaId = $this->user_group->get_field("id");
        if ($id == $superAdminInfosystaId && $authUserGroupId != $superAdminInfosystaId) {
            $this->set_flashmessage("information", $this->lang->line("permission_not_allowed"));
            redirect("user_groups");
        }
        if (($mode === "save" || $mode === "delete") && $id == $superAdminInfosystaId) {
            $this->set_flashmessage("information", $this->lang->line("permission_not_allowed"));
            redirect("user_groups");
        }
        unset($systemPreferences);
        unset($authUserGroupId);
        unset($superAdminInfosystaId);
        $this->user_group->reset_fields();
    }
    private function insert_user_group_changes_authorization($mode, $affectedUserGroupId, $newData, $oldData = [])
    {
        $return = true;
        $templateRequestFields = ["name", "description"];
        $columnsMultipleValues = [];
        $this->load->model("user_groups_changes_authorization", "user_groups_changes_authorizationfactory");
        $this->user_groups_changes_authorization = $this->user_groups_changes_authorizationfactory->get_instance();
        if ($mode == "add") {
            $userChanges = [];
            foreach ($templateRequestFields as $field) {
                foreach ($newData as $userField => $userValue) {
                    if ($field == $userField && $userValue) {
                        array_push($userChanges, ["changeType" => "add", "columnName" => $field, "columnRequestedValue" => $userValue, "columnStatus" => "Pending", "columnType" => in_array($field, $columnsMultipleValues) ? "multiple" : "text", "affectedUserGroupId" => $affectedUserGroupId, "makerId" => $this->session->userdata("AUTH_user_id"), "createdOn" => date("Y-m-d H:i:s")]);
                    }
                }
            }
            $this->user_groups_changes_authorization->insert_batch($userChanges);
        } else {
            $newChanges = array_diff_assoc($newData, $oldData);
            $userChanges = [];
            foreach ($templateRequestFields as $field) {
                foreach ($newChanges as $userField => $userValue) {
                    if ($field == $userField) {
                        array_push($userChanges, ["changeType" => "edit", "columnName" => $field, "columnValue" => $oldData[$field], "columnRequestedValue" => $userValue, "columnStatus" => "Pending", "columnType" => in_array($field, $columnsMultipleValues) ? "multiple" : "text", "affectedUserGroupId" => $affectedUserGroupId, "makerId" => $this->session->userdata("AUTH_user_id"), "createdOn" => date("Y-m-d H:i:s")]);
                    }
                }
            }
            if (!empty($userChanges)) {
                $this->user_groups_changes_authorization->insert_batch($userChanges);
                $this->user_group->fetch($affectedUserGroupId);
                $this->user_group->set_field("flagNeedApproval", "1");
                $this->user_group->update();
            } else {
                $return = false;
            }
        }
        $groupData = $this->user_group->load_group_by_id($affectedUserGroupId);
        $notificationData = ["status" => "unseen", "message" => sprintf($this->lang->line("changes_user_group_need_aproval"), $groupData["name"])];
        $this->load->model("notification", "notificationfactory");
        $this->notification = $this->notificationfactory->get_instance();
        $this->notification->notify_checkers_list($notificationData);
        return $return;
    }
    private function check_user_group_flag_need_approval($id)
    {
        if ($this->user_group->fetch($id) && $this->user_group->get_field("flagNeedApproval") == "1") {
            $this->set_flashmessage("warning", $this->lang->line("changes_user_group_data_need_aproval"));
            redirect("user_groups");
        }
    }
    public function checker_approve_changes()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("user_groups");
        }
        $response = [];
        $this->user_group->fetch($this->input->post("id"));
        if ($this->user_group->get_field("flagNeedApproval") == "1" && $this->is_auth->user_is_checker()) {
            $this->load->model("user_groups_changes_authorization", "user_groups_changes_authorizationfactory");
            $this->user_groups_changes_authorization = $this->user_groups_changes_authorizationfactory->get_instance();
            $requiredFields = ["name"];
            $this->input->post("modeType");
            switch ($this->input->post("modeType")) {
                case "getForm":
                    $data = [];
                    $data["affectedUserChanges"] = $this->user_groups_changes_authorization->load_changes_per_affected_user($this->input->post("id"));
                    $response["changeType"] = isset($data["affectedUserChanges"][0]) ? $data["affectedUserChanges"][0]["changeType"] : "";
                    $data["id"] = $this->input->post("id");
                    $data["changeType"] = $response["changeType"];
                    $data["requiredFields"] = $requiredFields;
                    $response["html"] = $this->load->view("user_groups/approve_changes_form", $data, true);
                    break;
                case "discardUser":
                    $changeType = $this->input->post("changeType");
                    if ($changeType === "add") {
                        $this->user_group->discard_user_changes($this->input->post("id"));
                    }
                    break;
                case "submitApproveForm":
                    $changeType = $this->input->post("changeType");
                    $approvedFields = $this->input->post("changeIds") ? $this->input->post("changeIds") : [];
                    $response["result"] = false;
                    if ($changeType === "add") {
                        $fieldsChanged = $this->user_groups_changes_authorization->get_fields_changed_per_affected_user($this->input->post("id"));
                        $fieldsChanged = array_diff($fieldsChanged, $requiredFields);
                        $fieldsNotApproved = array_diff($fieldsChanged, $approvedFields);
                        $this->user_group->fetch($this->input->post("id"));
                        $response["result"] = $this->user_group->validate();
                        if ($response["result"]) {
                            if (!empty($fieldsNotApproved)) {
                                foreach ($fieldsNotApproved as $field) {
                                    if (in_array($field, $this->user_group->get("_fieldsNames"))) {
                                        $this->user_group->set_field($field, NULL);
                                    }
                                }
                            }
                            $this->user_group->set_field("flagNeedApproval", "0");
                            $this->user_group->set_field("needApprovalOnAdd", "0");
                            $response["result"] = $this->user_group->update();
                            if ($response["result"]) {
                                $response["result"] = $this->user_groups_changes_authorization->update_after_approve_per_affected_user($this->input->post("id"), $fieldsNotApproved);
                            }
                        }
                    } else {
                        if ($changeType === "edit") {
                            if (!empty($approvedFields)) {
                                $pendingColValues = $this->user_groups_changes_authorization->get_pending_edit_fields_values_per_affected_user($this->input->post("id"));
                                $this->user_group->fetch($this->input->post("id"));
                                foreach ($approvedFields as $field) {
                                    foreach ($pendingColValues as $pendField => $pendValue) {
                                        if ($field == $pendValue["columnName"] && in_array($field, $this->user_group->get("_fieldsNames"))) {
                                            $this->user_group->set_field($field, $pendValue["columnRequestedValue"]);
                                        }
                                    }
                                }
                                $this->user_group->set_field("flagNeedApproval", "0");
                                $response["result"] = $this->user_group->update();
                                if ($response["result"]) {
                                    $pendCols = [];
                                    foreach ($pendingColValues as $val) {
                                        $pendCols[] = $val["columnName"];
                                    }
                                    $fieldsNotApproved = array_diff($pendCols, $approvedFields);
                                    $response["result"] = $this->user_groups_changes_authorization->update_after_approve_per_affected_user($this->input->post("id"), $fieldsNotApproved);
                                }
                            } else {
                                $this->user_group->fetch($this->input->post("id"));
                                $this->user_group->set_field("flagNeedApproval", "0");
                                $response["result"] = $this->user_group->update();
                                if ($response["result"]) {
                                    $pendingFields = $this->user_groups_changes_authorization->get_fields_changed_per_affected_user($this->input->post("id"));
                                    $response["result"] = $this->user_groups_changes_authorization->update_after_approve_per_affected_user($this->input->post("id"), $pendingFields);
                                }
                            }
                        }
                    }
                    $response["validationErrors"] = $this->user_group->get("validationErrors");
                    break;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function permissions_checker_approve_changes()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("user_groups");
        }
        $response = [];
        $this->user_group->fetch($this->input->post("id"));
        if ($this->is_auth->user_is_checker()) {
            $this->load->model("user_group_permissions_changes_authorization", "user_group_permissions_changes_authorizationfactory");
            $this->UGPCA = $this->user_group_permissions_changes_authorizationfactory->get_instance();
            $requiredFields = ["permission"];
            $this->input->post("modeType");
            switch ($this->input->post("modeType")) {
                case "getForm":
                    $data = [];
                    $data["outputPermissions"] = $this->get_actions_aliases($this->input->post("module"));
                    $data["affectedUserChanges"] = $this->UGPCA->load_changes_per_affected_user($this->input->post("id"), $this->input->post("module"));
                    $data["affectedUserChanges"]["columnRequestedValue"] = unserialize($data["affectedUserChanges"]["columnRequestedValue"]);
                    $data["affectedUserChanges"]["columnRequestedValue"] = $data["affectedUserChanges"]["columnRequestedValue"][$this->input->post("module")];
                    $data["affectedUserChanges"]["columnValue"] = unserialize($data["affectedUserChanges"]["columnValue"]);
                    $data["affectedUserChanges"]["columnValue"] = $data["affectedUserChanges"]["columnValue"][$this->input->post("module")];
                    $data["id"] = $this->input->post("id");
                    $data["requiredFields"] = $requiredFields;
                    $data["module"] = $this->input->post("module");
                    $response["html"] = $this->load->view("user_groups/pemissions_approve_changes_form", $data, true);
                    break;
                case "submitApproveForm":
                    $approvedFields = $this->input->post("changeIds") ? $this->input->post("changeIds") : [];
                    $response["result"] = false;
                    $response["formDiscarded"] = false;
                    $this->UGPCA->fetch(["affectedUserGroupId" => $this->input->post("id"), "module" => $this->input->post("module"), "columnStatus" => "Pending"]);
                    if (!empty($approvedFields)) {
                        $this->load->model("user_group_permission");
                        $groupPermissions = $this->user_group_permission->get_permissions($this->input->post("id"), false);
                        $groupPermissions[$this->input->post("module")] = (array) $approvedFields;
                        $response["result"] = $this->user_group_permission->set_permission_data($this->input->post("id"), $groupPermissions);
                        if ($response["result"]) {
                            $this->UGPCA->set_field("columnApprovedValue", $this->user_group_permission->_serialize($groupPermissions));
                            $this->UGPCA->set_field("authorizedOn", date("Y-m-d H:i:s"));
                            $this->UGPCA->set_field("checkerId", $this->session->userdata("AUTH_user_id"));
                            $this->UGPCA->set_field("columnStatus", "Approved");
                            $response["result"] = $this->UGPCA->update();
                        }
                    }
                    break;
                case "discardChanges":
                    $this->UGPCA->fetch(["affectedUserGroupId" => $this->input->post("id"), "module" => $this->input->post("module"), "columnStatus" => "Pending"]);
                    $this->UGPCA->set_field("authorizedOn", date("Y-m-d H:i:s"));
                    $this->UGPCA->set_field("checkerId", $this->session->userdata("AUTH_user_id"));
                    $this->UGPCA->set_field("columnStatus", "Rejected");
                    $response["result"] = $this->UGPCA->update();
                    $response["formDiscarded"] = true;
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
            $response = $this->user_group->k_load_all_maker_checker_changes($filter, $sortable);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("maker_checker_user_groups_report"));
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
            $this->includes("scripts/maker_checker_user_groups_report", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("user_groups/maker_checker_report", $data);
            $this->load->view("partial/footer");
        }
    }
    public function maker_checker_permissions_report()
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $action = $this->input->post("action");
            if (isset($action) && $action === "viewPermissions") {
                $id = $this->input->post("id");
                $data = [];
                $data["changesData"] = $this->user_group->load_maker_checker_permissions_changes($id);
                $data["outputPermissions"] = $this->get_actions_aliases($data["changesData"]["module"]);
                $response["html"] = $this->load->view("user_groups/maker_checker_permissions_view_changes", $data, true);
            } else {
                $response = $this->user_group->k_load_all_maker_checker_permissions_changes($filter, $sortable);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("maker_checker_user_groups_permissions_report"));
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
            $this->includes("scripts/maker_checker_user_groups_permissions_report", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("user_groups/maker_checker_permissions_report", $data);
            $this->load->view("partial/footer");
        }
    }
    private function get_dir_actions($selectedModule)
    {
        $exceptions = [];
        $controllers = [];
        $actions = [];
        $aliases = $this->get_aliases($selectedModule);
        $_modules = ["" => "Select Module", "core" => "Core"];
        $_modulePath = ["core" => FCPATH . "application" . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR];
        $activeInstalledModules = $this->_get_active_modules();
        foreach ($activeInstalledModules as $installedModule => $modName) {
            $_modules[$installedModule] = $modName;
            $_modulePath[$installedModule] = FCPATH . "modules" . DIRECTORY_SEPARATOR . $installedModule . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR;
        }
        if (empty($selectedModule)) {
            return $_modules;
        }
        $path = $_modulePath[$selectedModule];
        foreach ($this->is_auth->excempted_uri as $exception) {
            $exceptions[] = trim($exception, "/");
        }
        unset($exception);
        $dirHan = opendir($path);
        while ($file = readdir($dirHan)) {
            if (is_file($path . $file)) {
                $controller = mb_substr($file, 0, strrpos($file, "."));
                $controller = strtolower($controller);
                if (!in_array($controller, $exceptions)) {
                    $controllers[$controller] = $this->inflector->humanize(isset($aliases[$controller]) ? $aliases[$controller] : $controller);
                    $_tempActions = $this->list_actions($path . $file);
                    sort($_tempActions);
                    $actions[$controller] = [];
                    foreach ($_tempActions as $action) {
                        if (!in_array($controller . "/" . $action, $exceptions)) {
                            $actions[$controller][$action . "/"] = $this->inflector->humanize(isset($aliases[$controller . "/" . $action]) ? $aliases[$controller . "/" . $action] : $action);
                        }
                    }
                }
            }
        }
        $tempActions = [];
        foreach ($actions as $controller => $actionsList) {
            $tempActions[] = "/" . $controller . "/";
            foreach ($actionsList as $actionKey => $actionAlias) {
                $tempActions[] = "/" . $controller . "/" . $actionKey;
            }
        }
        return $tempActions;
    }
}

?>