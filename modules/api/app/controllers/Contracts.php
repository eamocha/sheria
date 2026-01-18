<?php

require "Top_controller.php";
class Contracts extends Top_controller
{
    public $responseData;
    public function __construct()
    {
        parent::__construct();
        $this->authenticate_actions_per_license("contract");
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $this->load->model("party_category_language", "party_category_languagefactory");
        $this->party_category_language = $this->party_category_languagefactory->get_instance();
        $this->load->model("contract_type_language", "contract_type_languagefactory");
        $this->contract_type_language = $this->contract_type_languagefactory->get_instance();
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $this->load->model("contract_status");
        $this->load->model("iso_currency");
        $this->load->model("contract_party", "contract_partyfactory");
        $this->contract_party = $this->contract_partyfactory->get_instance();
        $this->load->model("contract_approval_submission", "contract_approval_submissionfactory");
        $this->contract_approval_submission = $this->contract_approval_submissionfactory->get_instance();
        $this->load->model("contract_signature_submission", "contract_signature_submissionfactory");
        $this->contract_signature_submission = $this->contract_signature_submissionfactory->get_instance();
        $this->load->model(["provider_group"]);
        $this->load->model("applicable_law_language", "applicable_law_languagefactory");
        $this->applicable_law_language = $this->applicable_law_languagefactory->get_instance();
        $this->load->library("dms", ["channel" => $this->user_logged_in_data["channel"], "user_id" => $this->user_logged_in_data["user_id"]]);
        $this->contract->disable_builtin_logs();
        $this->responseData = default_response_data();
    }
    public function add()
    {
        $this->check_license_availability();
        $response = $this->responseData;
        if ($this->input->post(NULL)) {
            $response = $this->save(0);
        } else {
            $response = $this->responseData;
            $data = $this->_load_data();
            $response["success"]["data"] = $data;
        }
        $this->render($response);
    }
    private function _load_data($id = 0)
    {
        $data = [];
        $trigger = 0 < $id ? "edit_contract" : "add_contract";
        $data = $this->load_common_data($trigger);
        $data["title"] = $this->lang->line($trigger);
        $this->provider_group->fetch(["allUsers" => 1]);
        $data["assigned_team_id"] = $this->provider_group->get_field("id");
        $data["assignees"] = ["" => "---"] + $this->user->load_users_list("", ["key" => "id", "value" => "name"]);
        $data["default_priority"] = "medium";
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $custom_fields = $this->custom_field->load_custom_fields($id, $this->contract->get("modelName"), $this->get_lang_code());
        $section_types = $this->custom_field->section_types;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $field) {
                if ($field["type"] === "lookup") {
                    $field["value"] = $this->custom_field->get_lookup_data($field);
                }
                $data["custom_fields"][$section_types[$field["type"]]][] = $field;
            }
        }
        return $data;
    }
    private function load_common_data($trigger)
    {
        $lang = $this->get_lang_code();
        $data["categories"] = $this->party_category_language->load_list_per_language($lang);
        $data["renewals"] = ["" => "---"] + array_combine($this->contract->get("renewal_values"), [$this->lang->line("one_time"), $this->lang->line("renewable_automatically"), $this->lang->line("renewable_with_notice"), $this->lang->line("unlimited_period"), $this->lang->line("other")]);
        $data["types"] = $this->contract_type_language->load_list_per_language($lang);
        $data["priorities"] = array_combine($this->contract->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $data["applicable_laws"] = $this->applicable_law_language->load_list_per_language($lang);
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $data["countries"] = $this->country->load_countries_list(NULL, NULL, true, $lang);
        $data["assigned_teams"] = $this->provider_group->load_list([]);
        $data["currencies"] = $this->iso_currency->load_list(["order_by" => ["id", "asc"]], ["firstLine" => ["" => $this->lang->line("none")]]);
        $this->load->model("email_notification_scheme");
        $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action($trigger);
        $data["today"] = date("Y-m-d");
        $data["party_types"] = ["company" => $this->lang->line("company_or_group"), "contact" => $this->lang->line("contact")];
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
        return $data;
    }
    private function save($contract_id = 0)
    {
        $response = $this->responseData;
        $user_id = $this->user_logged_in_data["user_id"];
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $this->load->model("provider_group");
        $this->load->model("contract_status");
        $this->load->model("contract_workflow", "contract_workflowfactory");
        $this->contract_workflow = $this->contract_workflowfactory->get_instance();
        $parties_edited = [];
        $check_sla = false;
        if ($this->input->post(NULL)) {
            $post_data = $this->input->post(NULL);
            if ($contract_id) {
                $this->contract->fetch($contract_id);
            }
            $this->load->model("party");
            $party_member_types = $this->input->post("party_member_type");
            $party_member_ids = $this->input->post("party_member_id");
            $party_categories = $this->input->post("party_category");
            $sysPref = $this->system_preference->get_key_groups();
            $SystemValues = $sysPref["ContractDefaultValues"];
            $featureOption = isset($SystemValues["AllowContractSLAManagement"]) && $SystemValues["AllowContractSLAManagement"] ? $SystemValues["AllowContractSLAManagement"] : "no";
            if ($featureOption == "yes" && $contract_id) {
                $old_data = [];
                $old_data["contract"] = $this->contract->api_load_data($contract_id);
                $old_data["parties"] = $this->contract_party->load_contract_parties($contract_id);
                if ($party_member_types && $party_member_ids) {
                    $parties_new_data = $this->party->return_parties($party_member_types, $party_member_ids);
                    $parties_new_data_array = [];
                    foreach ($parties_new_data as $party) {
                        $parties_new_data_array += [$party["party_id"] => $party["party_member_type"]];
                    }
                }
                if (!empty($parties_new_data_array) && !empty($old_data["parties"])) {
                    $parties_edited = array_diff_key($parties_new_data_array, $old_data["parties"]);
                } else {
                    if (empty($parties_new_data_array) && !empty($old_data["parties"]) || !empty($parties_new_data_array) && empty($old_data["parties"])) {
                        $parties_edited += ["not equal"];
                    }
                }
                if ($post_data["type_id"] != $old_data["contract"]["type_id"] || $post_data["priority"] != $old_data["contract"]["priority"] || !empty($parties_edited)) {
                    $check_sla = true;
                }
            }
            if ($this->input->post("type_id") && (!$contract_id || $contract_id && $this->input->post("type_id") !== $this->contract->get_field("type_id"))) {
                $workflow_applicable = $this->contract_status->load_workflow_status_per_type($_POST["type_id"]);
                if (empty($workflow_applicable)) {
                    $workflow_applicable = $this->contract_status->load_system_workflow_status();
                }
                $post_data["status_id"] = $workflow_applicable["status_id"] ?? "1";
                $post_data["workflow_id"] = $workflow_applicable["workflow_id"] ?? "1";
            }
            $this->contract->set_fields($post_data);
            $channel = $this->user_logged_in_data["channel"];
            if ($contract_id == 0) {
                $this->contract->set_field("createdBy", $user_id);
                $this->contract->set_field("createdOn", date("Y-m-d H:i:s"));
            }
            $this->contract->set_field("modifiedBy", $user_id);
            $this->contract->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->contract->set_field("channel", $channel);
            $this->contract->set_field("modifiedByChannel", $channel);
            $this->contract->set_field("archived", $contract_id ? $this->contract->get_field("archived") : "no");
            $result = $contract_id ? $this->contract->update() : $this->contract->insert();
            if ($result) {
                  $contract_id = $this->contract->get_field("id");
                $this->load->model("contract_sla_management", "contract_sla_managementfactory");
                $this->contract_sla_management = $this->contract_sla_managementfactory->get_instance();
                if ($contract_id) {
                    $this->save_watchers();
                    $check_sla ? $this->contract_sla_management->edit_contract_sla($contract_id, $user_id, 1) : "";
                } else {
                    $this->contract_sla_management->contract_sla($this->contract->get_field("id"), $user_id, 1);
                }
              
                if ($this->input->post("contributor_id")) {
                    $this->load->model("contract_contributor", "contract_contributorfactory");
                    $this->contract_contributor = $this->contract_contributorfactory->get_instance();
                    $contributors_data = ["contract_id" => $contract_id, "users" => $this->input->post("contributor_id")];
                    $this->contract_contributor->insert_contributors($contributors_data);
                }
                if ($party_member_types && $party_member_ids) {
                    $parties_data = $this->party->return_parties($party_member_types, $party_member_ids);
                    if (!empty($parties_data)) {
                        foreach ($parties_data as $key => $value) {
                            $parties_data[$key]["contract_id"] = $contract_id;
                            $parties_data[$key]["party_category_id"] = isset($party_categories[$key]) && !empty($party_categories[$key]) ? $party_categories[$key] : NULL;
                        }
                        $this->contract_party->insert_contract_parties($contract_id, $parties_data);
                    }
                }
                $watchers = $this->input->post("watchers", true) ?? [];
                $this->load->model("contract_user", "contract_userfactory");
                $this->contract_user = $this->contract_userfactory->get_instance();
                if ($watchers) {
                    if ($this->input->post("contributor_id")) {
                        $watchers = array_merge($watchers, array_diff($this->input->post("contributor_id"), $watchers));
                    }
                    $assigned_to = $this->contract->get_field("assignee_id");
                    if (!in_array($assigned_to, $watchers)) {
                        $watchers[] = $assigned_to;
                    }
                    if (!in_array($this->user_logged_in_data["user_id"], $watchers)) {
                        $watchers[] = $this->user_logged_in_data["user_id"];
                    }
                    $watchers_data = ["contract_id" => $contract_id, "users" => $watchers];
                    $this->contract_user->insert_users($watchers_data);
                } else {
                    $this->contract_user->insert_users(["contract_id" => $contract_id, "users" => []]);
                }
                $post_data["id"] = $contract_id;
                if (!$this->contract_approval_submission->fetch(["contract_id" => $contract_id])) {
                    $this->load->model("approval", "approvalfactory");
                    $this->approval = $this->approvalfactory->get_instance();
                    $data["approval_center"] = $this->approval->update_approval_contract($post_data);
                }
                if (!$this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                    $this->load->model("signature", "signaturefactory");
                    $this->signature = $this->signaturefactory->get_instance();
                    $data["signature_center"] = $this->signature->update_signature_contract($post_data);
                }
                if ($this->contract->get_field("authorized_signatory")) {
                    $this->load->model("signature", "signaturefactory");
                    $this->signature = $this->signaturefactory->get_instance();
                    $this->signature->inject_authorized_signatory_in_signature_models($contract_id, $this->contract->get_field("authorized_signatory"));
                }
                $this->contract->inject_folder_templates($contract_id, "contract", $this->contract->get_field("type_id"));
                $response["success"]["data"]["contract_id"] = $contract_id;
                $response["success"]["msg"] = sprintf($this->lang->line("record_saved"), $this->lang->line("contract"));
                $response["status"] = true;
                $this->add_notification($contract_id);
            } else {
                $response["status"] = false;
                $response["error"] = $this->contract->get("validationErrors");
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        return $response;
    }
    public function autocomplete()
    {
        $response = $this->responseData;
        $term = trim((string) $this->input->post("term"));
        $this->lookup_term_validation($term);
        if (!empty($term)) {
            $user_id = $this->user_logged_in_data["user_id"];
            $this->user_profile->fetch(["user_id" => $user_id]);
            $overridePrivacy = $this->user_profile->get_field("overridePrivacy");
            $response["success"]["data"] = $this->contract->api_lookup($term, $user_id, $overridePrivacy);
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    private function load_contract_data($contract_id)
    {
        $this->load->model("contract_contributor", "contract_contributorfactory");
        $this->contract_contributor = $this->contract_contributorfactory->get_instance();
        $this->load->model("contract_collaborator", "contract_collaboratorfactory");
        $this->contract_collaborator = $this->contract_collaboratorfactory->get_instance();
        $this->load->model("contract_user", "contract_userfactory");
        $this->contract_user = $this->contract_userfactory->get_instance();
        $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
        $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
        $data["contract"] = $this->contract->api_load_data($contract_id);
        $data["parties"] = $this->contract_party->fetch_contract_parties_data($contract_id);
        $data["contributors"] = $this->contract_contributor->load_contributors($contract_id);
        $data["collaborators"] = $this->contract_collaborator->load_collaborators($contract_id);
        $data["watchers"] = $data["contract"]["private"] ? $this->contract_user->load_users($contract_id) : [];
        $data["notifications"] = $this->load_notification_details($contract_id);
        $data["history"] = $this->load_amend_renew_history($contract_id);
        $lang = $this->user_preference->get_field("keyValue");
        $lang_id = $this->language->get_id_by_session_lang($lang);
        $transitions_accessible = $this->contract_workflow_status_transition->load_available_steps($data["contract"]["status_id"], $data["contract"]["workflow_id"], $lang_id);
        $data["available_statuses"] = $transitions_accessible["available_statuses"];
        $data["status_transitions"] = $transitions_accessible["status_transitions"];
        if ($data["contract"]["visible_to_cp"]) {
            $this->load->model("customer_portal_contract_watcher", "customer_portal_contract_watcherfactory");
            $this->customer_portal_contract_watcher = $this->customer_portal_contract_watcherfactory->get_instance();
            $data["contract_watchers"] = $this->customer_portal_contract_watcher->get_contract_watchers($contract_id);
        }
        return $data;
    }
    public function edit($id = 0)
    {
        $this->check_license_availability();
        if ($this->input->post(NULL)) {
            $response = $this->save($this->input->post("id"));
        } else {
            if ($id) {
                $response = $this->responseData;
                $response["success"]["data"] = $this->_load_data($id);
                $response["success"]["data"]["model_code"] = $this->contract->get("modelCode");
                $response["success"]["data"]["contract"] = $this->load_contract_data($id);
            } else {
                $response["error"] = $this->lang->line("data_missing");
            }
        }
        $this->render($response);
    }
    public function add_note()
    {
        $response = $this->responseData;
        if ($this->input->post(NULL)) {
            $this->absolute_note_add($response);
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    public function email_note_add()
    {
        $response = $this->responseData;
        if ($this->input->post(NULL)) {
            $this->load->model("contract_comment_email", "contract_comment_emailfactory");
            $this->contract_comment_email = $this->contract_comment_emailfactory->get_instance();
            $contract_id = $this->input->post("contract_id");
            $this->contract_comment_email->set_fields($this->input->post(NULL));
            if ($this->contract_comment_email->validate()) {
                $contract_comment_id = $this->absolute_note_add($response);
                if (0 < $contract_comment_id) {
                    $this->contract_comment_email->set_field("contract_comment", $contract_comment_id);
                    $allowed_types_arr = ["msg", "eml", "html", "pdf"];
                    $not_allowed_extensions = [];
                    $are_files_uploaded = $this->dms->check_if_files_were_uploaded($_FILES);
                    if ($are_files_uploaded) {
                        $this->load->model("contract_comment", "contract_commentfactory");
                        $this->contract_comment = $this->contract_commentfactory->get_instance();
                        $this->contract_comment->fetch(["id" => $contract_comment_id]);
                        $contract_comment_created_on = $this->contract_comment->get_field("createdOn");
                        $note_parent_folder = $this->dms->create_note_parent_folder($contract_id, $contract_comment_created_on, "contract");
                    }
                    foreach ($_FILES as $uploaded_file_key => $uploaded_file) {
                        if ($uploaded_file_key == "email_file") {
                            $this->upload_attachment($uploaded_file_key, $uploaded_file, $contract_id, $allowed_types_arr, $not_allowed_extensions, true, $contract_comment_created_on ?? NULL, $note_parent_folder["lineage"] ?? NULL);
                            if (!empty($not_allowed_extensions)) {
                                $response["error"] = [];
                                $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                                $response["error"]["notAllowedExtensions"] = $not_allowed_extensions;
                            }
                        }
                    }
                    if (empty($response["error"])) {
                        if ($this->contract_comment_email->validate() && $this->contract_comment_email->insert()) {
                            $response["success"]["msg"] = $this->lang->line("a_new_note_added");
                        } else {
                            $response["error"] = $this->contract_comment_email->get("validationErrors");
                        }
                    }
                }
            } else {
                $response["error"] = $this->contract_comment_email->get("validationErrors");
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    private function absolute_note_add(&$response)
    {
        $this->load->model("contract_comment", "contract_commentfactory");
        $this->contract_comment = $this->contract_commentfactory->get_instance();
        $this->load->helper("format_comment_patterns");
        $this->config->load("allowed_file_uploads", true);
        $allowed_types = $this->config->item("case", "allowed_file_uploads");
        $allowed_types_arr = explode("|", $allowed_types);
        $not_allowed_extensions = [];
        $createdOn = date("Y-m-d H:i:s", strtotime($this->input->post("createdOn") . " " . date("H:i:s", time())));
        $are_files_uploaded = $this->dms->check_if_files_were_uploaded($_FILES);
        if ($are_files_uploaded) {
            $contract_id = $this->input->post("contract_id");
            $note_parent_folder = $this->dms->create_note_parent_folder($contract_id, $createdOn, "contract");
        }
        foreach ($_FILES as $uploaded_file_key => $uploaded_file) {
            if ($uploaded_file_key !== "email_file") {
                $file = $this->upload_attachment($uploaded_file_key, $uploaded_file, $this->input->post("contract_id"), $allowed_types_arr, $not_allowed_extensions, false, $createdOn, $note_parent_folder["lineage"] ?? NULL);
                if ($file) {
                    $_POST["comment"] .= "<p><a href=\"" . base_url() . "contracts/download_file/" . $file["id"] . "\"> " . $uploaded_file["name"] . " </a></p>";
                }
                if (!empty($not_allowed_extensions)) {
                    $response["error"] = [];
                    $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                    $response["error"]["notAllowedExtensions"] = $not_allowed_extensions;
                }
            }
            if (!empty($not_allowed_extensions)) {
                $response["error"] = [];
                $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                $response["error"]["notAllowedExtensions"] = $not_allowed_extensions;
            }
        }
        if (!empty($not_allowed_extensions)) {
            $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
            $response["error"]["notAllowedExtensions"] = $not_allowed_extensions;
        }
        $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>")));
        $_POST["edited"] = 0;
        $response["result"] = false;
        $this->contract_comment->set_fields($this->input->post(NULL));
        $this->contract_comment->set_field("createdBy", $this->user_logged_in_data["user_id"]);
        $this->contract_comment->set_field("createdOn", $createdOn);
        $this->contract_comment->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
        $this->contract_comment->set_field("modifiedOn", date("Y-m-d H:i:s"));
        $this->contract_comment->set_field("channel", $this->user_logged_in_data["channel"]);
        $this->contract_comment->set_field("modifiedByChannel", $this->user_logged_in_data["channel"]);
        $this->contract_comment->set_field("comment", $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
        $this->contract_comment->disable_builtin_logs();
        if ($this->contract_comment->insert()) {
            $response["success"]["msg"] = $this->lang->line("comment_added_successfully");
            $response["result"] = true;
        } else {
            $response["error"] = $this->contract_comment->get("validationErrors");
        }
        return $this->contract_comment->get_field("id");
    }
    public function edit_note($id = 0)
    {
        $response = $this->responseData;
        $this->load->model("contract_comment", "contract_commentfactory");
        $this->contract_comment = $this->contract_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if (!$this->input->post(NULL)) {
            if ($id) {
                if ($this->contract_comment->fetch(["id" => $id])) {
                    $response["success"]["data"] = $this->contract_comment->get_fields();
                    $creator_name = $this->user->get_name_by_id($response["success"]["data"]["createdBy"]);
                    $response["success"]["data"]["created_by_name"] = $creator_name["name"];
                } else {
                    $response["error"] = $this->lang->line("note_not_found");
                }
            } else {
                $response["error"] = $this->lang->line("missing_id");
            }
        } else {
            $this->config->load("allowed_file_uploads", true);
            $allowed_types = $this->config->item("case", "allowed_file_uploads");
            $allowed_types_arr = explode("|", $allowed_types);
            $not_allowed_extensions = [];
            $this->contract_comment->fetch(["id" => $id]);
            $createdOn = date("Y-m-d H:i:s", strtotime($this->input->post("createdOn") . " " . date("H:i:s", time())));
            $attachments = $this->get_comment_attachments($this->contract_comment->get_field("comment"));
            if (0 < count($attachments)) {
                $parent_id = $this->dms->get_document_details(["id" => end($attachments)])["parent"];
                $this->dms->rename_document("contract", $parent_id, "folder", $createdOn);
            }
            $are_files_uploaded = $this->dms->check_if_files_were_uploaded($_FILES);
            if ($are_files_uploaded) {
                $note_parent_folder = $this->dms->create_note_parent_folder($this->contract_comment->get_field("contract_id"), $createdOn, "contract");
            }
            foreach ($_FILES as $uploaded_file_key => $uploaded_file) {
                if ($uploaded_file_key !== "email_file") {
                    $file = $this->upload_attachment($uploaded_file_key, $uploaded_file, $this->contract_comment->get_field("contract_id"), $allowed_types_arr, $not_allowed_extensions, false, $createdOn, $note_parent_folder["lineage"] ?? NULL);
                    if ($file) {
                        $_POST["comment"] .= "<p><a href=\"" . base_url() . "contracts/download_file/" . $file["id"] . "\"> " . $uploaded_file["name"] . " </a></p>";
                    }
                    if (!empty($not_allowed_extensions)) {
                        $response["error"] = [];
                        $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                        $response["error"]["notAllowedExtensions"] = $not_allowed_extensions;
                    }
                }
                if (!empty($not_allowed_extensions)) {
                    $response["error"] = [];
                    $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                    $response["error"]["notAllowedExtensions"] = $not_allowed_extensions;
                }
            }
            if (!empty($not_allowed_extensions)) {
                $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                $response["error"]["notAllowedExtensions"] = $not_allowed_extensions;
            }
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>")), true);
            $this->contract_comment->fetch(["id" => $this->input->post("id"), "contract_id" => $this->input->post("contract_id")]);
            if ($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>") != $this->contract_comment->get_field("comment")) {
                $_POST["edited"] = 1;
            }
            $logged_user = $this->user_logged_in_data["user_id"];
            $this->contract_comment->set_fields($this->input->post(NULL));
            $this->contract_comment->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->contract_comment->set_field("modifiedBy", $logged_user);
            $this->contract_comment->set_field("createdOn", $createdOn);
            $this->contract_comment->set_field("createdBy", $this->contract_comment->get_field("createdBy"));
            $this->contract_comment->set_field("modifiedByChannel", $this->user_logged_in_data["channel"]);
            $this->contract_comment->set_field("comment", $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            if ($this->contract_comment->update()) {
                $response["result"] = true;
                $response["success"]["data"] = ["contract_id" => $this->input->post("contract_id")];
            } else {
                $response["validation_errors"] = $this->contract_comment->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function upload_attachment($uploaded_file_key, $uploaded_file, $contract_Id, &$allowed_types_arr, &$not_allowed_extensions, $is_email_file_attachment = false, $parent_folder_name = NULL, $parent_folder_lineage = NULL)
    {
        $file_info = pathinfo($uploaded_file["name"]);
        $file_info_extension = strtolower($file_info["extension"]);
        if (!in_array($file_info_extension, $allowed_types_arr)) {
            in_array($file_info_extension, $not_allowed_extensions);
            !in_array($file_info_extension, $not_allowed_extensions) ? array_push($not_allowed_extensions, $file_info_extension) : NULL;
        } else {
            $upload_response = $this->dms->upload_file(["module" => "contract", "module_record_id" => $contract_Id, "upload_key" => $uploaded_file_key, "container_name" => $parent_folder_name, "lineage" => $parent_folder_lineage]);
            if ($upload_response["status"]) {
                if ($is_email_file_attachment) {
                    $this->contract_comment_email->set_field("email_file", $upload_response["file"]["id"]);
                } else {
                    return $upload_response["file"];
                }
            }
        }
        return false;
    }
    public function note_list()
    {
        $response = $this->responseData;
        $contract_id = $this->input->post("contract_id");
        if (!$contract_id || empty($contract_id)) {
            $response["error"] = $this->lang->line("missing_id");
        } else {
            $term = trim((string) $this->input->post("term"));
            $page_size = strcmp($this->input->post("pageSize"), "") ? $this->input->post("pageSize") : 20;
            $page_nb = strcmp($this->input->post("pageNb"), "") ? $this->input->post("pageNb") : 1;
            $skip = ($page_nb - 1) * $page_size;
            $this->load->model("contract_comment", "contract_commentfactory");
            $this->contract_comment = $this->contract_commentfactory->get_instance();
            $response["success"] = $this->contract_comment->api_load_contract_comments($contract_id, $page_size, $skip, $term);
            $response["success"]["dbDriver"] = $this->getDBDriver();
        }
        $this->render($response);
    }
    public function note_list_only()
    {
        $response = $this->responseData;
        $contract_id = $this->input->post("contract_id");
        if (!$contract_id || empty($contract_id)) {
            $response["error"] = $this->lang->line("missing_id");
        } else {
            $term = trim((string) $this->input->post("term"));
            $page_size = strcmp($this->input->post("pageSize"), "") ? $this->input->post("pageSize") : 20;
            $page_nb = strcmp($this->input->post("pageNb"), "") ? $this->input->post("pageNb") : 1;
            $skip = ($page_nb - 1) * $page_size;
            $this->load->model("contract_comment", "contract_commentfactory");
            $this->contract_comment = $this->contract_commentfactory->get_instance();
            $response["success"] = $this->contract_comment->api_load_contract_core_and_cp_comments($contract_id, $page_size, $skip, $term);
            $response["success"]["dbDriver"] = $this->getDBDriver();
        }
        $this->render($response);
    }
    public function email_note_list()
    {
        $response = $this->responseData;
        $contract_id = $this->input->post("contract_id");
        if (!$contract_id || empty($contract_id)) {
            $response["error"] = $this->lang->line("missing_id");
        } else {
            $term = trim((string) $this->input->post("term"));
            $page_size = strcmp($this->input->post("pageSize"), "") ? $this->input->post("pageSize") : 20;
            $page_nb = strcmp($this->input->post("pageNb"), "") ? $this->input->post("pageNb") : 1;
            $skip = ($page_nb - 1) * $page_size;
            $this->load->model("contract_comment", "contract_commentfactory");
            $this->contract_comment = $this->contract_commentfactory->get_instance();
            $response["success"] = $this->contract_comment->api_load_contract_comments_emails($contract_id, $page_size, $skip, $term);
            $response["success"]["dbDriver"] = $this->getDBDriver();
        }
        $this->render($response);
    }
    public function note_delete($id = 0)
    {
        $response = $this->responseData;
        if (0 < $id) {
            $this->load->model("contract_comment", "contract_commentfactory");
            $this->contract_comment = $this->contract_commentfactory->get_instance();
            if ($this->contract_comment->fetch($id)) {
                if (!$this->contract_comment->delete($id)) {
                    $response["error"] = $this->lang->line("record_not_deleted");
                } else {
                    $response["success"]["msg"] = $this->lang->line("record_deleted_successfully");
                }
            } else {
                $response["error"] = $this->lang->line("invalid_record");
            }
        } else {
            $response["error"] = $this->lang->line("missing_id");
        }
        $this->render($response);
    }
    public function email_note_delete($id = 0)
    {
        $response = $this->responseData;
        if (0 < $id) {
            $this->load->model("contract_comment_email", "contract_comment_emailfactory");
            $this->contract_comment_email = $this->contract_comment_emailfactory->get_instance();
            if ($this->contract_comment_email->fetch($id)) {
                $this->load->model("contract_comment", "contract_commentfactory");
                $this->contract_comment = $this->contract_commentfactory->get_instance();
                $contract_comment_id = $this->contract_comment_email->get_field("contract_comment");
                $contract_comment_email_id = $this->contract_comment_email->get_field("id");
                if ($this->contract_comment_email->delete(["where" => ["id", $contract_comment_email_id]]) && $this->contract_comment->delete(["where" => ["id", $contract_comment_id]])) {
                    $response["success"]["msg"] = $this->lang->line("record_deleted_successfully");
                } else {
                    $response["error"] = $this->lang->line("record_not_deleted");
                }
            } else {
                $response["error"] = $this->lang->line("invalid_record");
            }
        } else {
            $response["error"] = $this->lang->line("missing_id");
        }
        $this->render($response);
    }
    public function load_templates_per_contract_types()
    {
        $response = $this->responseData;
        $response["templates"] = [];
        $this->load->model("contract_template", "contract_templatefactory");
        $this->contract_template = $this->contract_templatefactory->get_instance();
        if ($this->input->post("type_id")) {
            $where[] = ["type_id", $this->input->post("type_id")];
            if ($this->input->post("sub_type_id")) {
                $where[] = ["sub_type_id", $this->input->post("sub_type_id")];
            }
            $response["success"]["data"]["templates"] = $this->contract_template->load_list(["where" => $where]);
        }
        $this->render($response);
    }
    public function get_contract_types()
    {
        $response = $this->responseData;
        $this->load->model("contract_type_language", "contract_type_languagefactory");
        $this->contract_type_language = $this->contract_type_languagefactory->get_instance();
        $logged_user_id = $this->user_logged_in_data["user_id"];
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $logged_user_id, "keyName" => "language"]);
        $lang = strtolower(substr($this->user_preference->get_field("keyValue"), 0, 2));
        $response["success"]["data"] = $this->contract_type_language->load_list_per_language($lang);
        $this->render($response);
    }
    public function get_contract_type_subtypes($type_id = 0)
    {
        $response = $this->responseData;
        if ($type_id) {
            $this->load->model("sub_contract_type_language", "sub_contract_type_languagefactory");
            $this->sub_contract_type_language = $this->sub_contract_type_languagefactory->get_instance();
            $logged_user_id = $this->user_logged_in_data["user_id"];
            $this->load->model("user_preference");
            $this->user_preference->fetch(["user_id" => $logged_user_id, "keyName" => "language"]);
            $lang = strtolower(substr($this->user_preference->get_field("keyValue"), 0, 2));
            $response["success"]["data"] = $this->sub_contract_type_language->load_list_per_type_per_language($type_id, $lang);
            if (empty($response["success"]["data"])) {
                $response["success"]["msg"] = $this->lang->line("no_subtypes_related_to_selected_type");
            }
        } else {
            $response["error"] = $this->lang->line("missing_id");
        }
        $this->render($response);
    }
    public function add_contract_using_questionnaire($id = 0)
    {
        $response = $this->responseData;
        $this->load->model("contract_template", "contract_templatefactory");
        $this->contract_template = $this->contract_templatefactory->get_instance();
        if (!$this->input->post(NULL)) {
            $response["success"]["data"] = $this->contract_template->load_template_data($id);
        } else {
            $data = $this->contract->save_contract_from_template($this->user_logged_in_data["channel"], $this->user_logged_in_data["user_id"]);
            if ($data["result"]) {
                unset($data["result"]);
                unset($data["msg"]);
                $response["success"]["data"] = $data;
                $response["success"]["msg"] = sprintf($this->lang->line("record_added_successfull"), $this->lang->line("contract"));
            } else {
                $response["error"] = $this->lang->line("record_could_not_be_saved");
            }
        }
        $this->render($response);
    }
    private function get_comment_attachments($comment)
    {
        $ids = NULL;
        preg_match_all("/[|]+\\d+/", $comment, $ids);
        $ids = $ids[0];
        for ($index = 0; $index < count($ids); $index++) {
            $id = $ids[$index];
            $id = ltrim($id, $id[0]);
            $ids[$index] = $id;
        }
        return $ids;
    }
    public function list_contracts()
    {
        $response = $this->responseData;
        $logged_in_user = $this->user_logged_in_data["user_id"];
        $this->user_profile->fetch(["user_id" => $logged_in_user]);
        $override_privacy = $this->user_profile->get_field("overridePrivacy");
        $this->load->model("language");
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $logged_in_user, "keyName" => "language"]);
        $lang = $this->user_preference->get_field("keyValue");
        $lang_id = $this->language->get_id_by_session_lang($lang);
        $response["success"] = $this->contract->api_load_all_contracts($logged_in_user, $override_privacy, $lang_id);
        $response["success"]["dbDriver"] = $this->getDBDriver();
        $this->render($response);
    }
    private function load_amend_renew_history($id = 0)
    {
        $data = [];
        $this->load->model("contract_renewal_history", "contract_renewal_historyfactory");
        $this->contract_renewal_history = $this->contract_renewal_historyfactory->get_instance();
        $data["renewal"] = $this->contract_renewal_history->load_history($id);
        $this->load->model("contract_amendment_history", "contract_amendment_historyfactory");
        $this->contract_amendment_history = $this->contract_amendment_historyfactory->get_instance();
        $data["amendment"] = $this->contract_amendment_history->load_history($id);
        return $data;
    }
    private function load_notification_details($id = 0)
    {
        $response = [];
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $this->load->model("contract_renewal_notification_email", "contract_renewal_notification_emailfactory");
        $this->contract_renewal_notification_email = $this->contract_renewal_notification_emailfactory->get_instance();
        $this->contract_renewal_notification_email->fetch(["contract_id" => $id]);
        $data["emails"] = $this->contract_renewal_notification_email->get_field("emails");
        $this->load->model("contract_renewal_notification_assigned_team", "contract_renewal_notification_assigned_teamfactory");
        $this->contract_renewal_notification_assigned_team = $this->contract_renewal_notification_assigned_teamfactory->get_instance();
        $selected_emails = explode(";", $data["emails"]);
        $users_emails = $this->user->load_active_emails();
        foreach ($selected_emails as $email) {
            if (!in_array($email, $users_emails)) {
                $users_emails[$email] = $email;
            }
        }
        $response["users_emails"] = array_map(function ($users_emails) {
            return ["email" => $users_emails];
        }, array_keys($users_emails));
        $response["teams"] = $this->contract_renewal_notification_assigned_team->load_assigned_teams($id);
        $response["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($id, $this->contract->get("_table"), $this->user_logged_in_data["user_id"]);
        return $response;
    }
    private function add_notification($id = 0)
    {
        $result = true;
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $this->load->model("contract_renewal_notification_email", "contract_renewal_notification_emailfactory");
        $this->contract_renewal_notification_email = $this->contract_renewal_notification_emailfactory->get_instance();
        $this->load->model("contract_renewal_notification_assigned_team", "contract_renewal_notification_assigned_teamfactory");
        $this->contract_renewal_notification_assigned_team = $this->contract_renewal_notification_assigned_teamfactory->get_instance();
        $notify_before = $this->input->post("notify_me_before", true);
        $end_date = $this->input->post("end_date");
        $current_reminder = $this->reminder->load_notify_before_data_to_related_object($id, $this->contract->get("_table"));
        if ($current_reminder && !$notify_before) {
            $result = $this->reminder->remind_before_due_date([], $current_reminder["id"]);
        }
        if ($notify_before && $end_date) {
            $reminder = ["user_id" => $this->user_logged_in_data["user_id"], "remindDate" => $end_date, "contract_id" => $id, "related_id" => $id, "related_object" => $this->contract->get("_table"), "notify_before_time" => $notify_before["time"], "notify_before_time_type" => $notify_before["time_type"], "notify_before_type" => "popup_email"];
            $reminder["summary"] = sprintf($this->lang->line("notify_me_before_expiry_date_message"), $this->lang->line("contract"), $this->contract->get("modelCode") . $id, $end_date);
            $result = $this->reminder->remind_before_due_date($reminder, isset($notify_before["id"]) ? $notify_before["id"] : NULL);
        }
        $notifications = $this->input->post("notifications", true);
        if (isset($notifications["emails"]) && $notifications["emails"]) {
            $this->contract_renewal_notification_email->delete(["where" => ["contract_id", $id]]);
            $this->contract_renewal_notification_email->set_field("contract_id", $id);
            $this->contract_renewal_notification_email->set_field("emails", implode(";", $notifications["emails"]));
            $this->contract_renewal_notification_email->insert();
            $this->contract_renewal_notification_email->reset_fields();
        }
        if (isset($notifications["teams"]) && $notifications["teams"]) {
            $this->contract_renewal_notification_assigned_team->delete(["where" => ["contract_id", $id]]);
            foreach ($notifications["teams"] as $team) {
                $this->contract_renewal_notification_assigned_team->set_field("contract_id", $id);
                $this->contract_renewal_notification_assigned_team->set_field("assigned_team", $team);
                $this->contract_renewal_notification_assigned_team->insert();
                $this->contract_renewal_notification_assigned_team->reset_fields();
            }
        }
        return $result;
    }
    public function move_status($contract_id = 0, $status_id = 0)
    {
        $response = $this->responseData;
        $needs_approval = false;
        $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
        $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
        $this->load->model("contract_approval_submission", "contract_approval_submissionfactory");
        $this->contract_approval_submission = $this->contract_approval_submissionfactory->get_instance();
        $this->load->model("language");
        $lang = $this->user_preference->get_field("keyValue");
        $lang_2 = strtolower(substr($lang, 0, 2));
        $lang_id = $this->language->get_id_by_session_lang($lang);
        if (!$contract_id || !$status_id) {
            $response["error"] = "missing data";
        } else {
            if ($this->contract->fetch($contract_id) && $contract_id && $status_id) {
                $workflow_applicable = $this->contract->get_field("workflow_id");
                $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
                $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
                $old_status = $this->contract->get_field("status_id");
                $type_id = $this->contract->get_field("type_id");
                $this->contract_workflow_status_transition->fetch(["workflow_id" => $workflow_applicable, "from_step" => $old_status, "to_step" => $status_id]);
                $transition_id = $this->contract_workflow_status_transition->get_field("id");
                if ($transition_id && $this->contract_workflow_status_transition->fetch($transition_id) && $this->contract_workflow_status_transition->get_field("approval_needed") && $this->contract_approval_submission->fetch(["contract_id" => $contract_id]) && $this->contract_approval_submission->get_field("status") !== "approved") {
                    $response["error"] = $this->lang->line("needs_approval_before");
                    $needs_approval = true;
                }
                if (!$needs_approval) {
                    $workflow_applicable = 0 < $this->contract->get_field("workflow_id") ? $this->contract->get_field("workflow_id") : ($allowed_statuses = []);
                    $allowed_statuses = $this->contract_workflow_status_transition->load_available_steps($old_status, $workflow_applicable, $lang_id);
                    if ($status_id === $old_status || !in_array($status_id, array_keys($allowed_statuses["available_statuses"]))) {
                        $response["error"] = $this->lang->line("permission_not_allowed");
                    } else {
                        $this->load->model("contract_fields", "contract_fieldsfactory");
                        $this->contract_fields = $this->contract_fieldsfactory->get_instance();
                        $this->contract_fields->load_all_fields($type_id,$lang_2);
                        $data = $this->contract_fields->return_screen_fields($contract_id, $transition_id, $lang_id);
                        if ($transition_id && $data) {
                            $data["title"] = $this->contract_workflow_status_transition->get_field("name");
                            $response["success"]["transition_id"] = $transition_id;
                            $response["success"]["data"] = $data;
                        } else {
                            $this->contract->fetch($contract_id);
                            $this->contract->set_field("status_id", $status_id);
                            if (!$this->contract->update()) {
                                $response["error"] = $this->lang->line("contract_move_status_invalid");
                                $response["validation_errors"] = $this->contract->get("validationErrors");
                            } else {
                                $this->load->model("approval", "approvalfactory");
                                $this->approval = $this->approvalfactory->get_instance();
                                $this->approval->workflow_status_approval_events($contract_id, $this->contract->get_field("workflow_id"), $status_id);
                                $this->load->model("contract_contributor", "contract_contributorfactory");
                                $this->contract_contributor = $this->contract_contributorfactory->get_instance();
                                $transitions_accessible = $this->contract_workflow_status_transition->load_available_steps($status_id, $this->contract->get_field("workflow_id"), $lang_id);
                                $response["available_statuses"] = $transitions_accessible["available_statuses"];
                                $response["status_transitions"] = $transitions_accessible["status_transitions"];
                                $response["contract"]["id"] = $contract_id;
                                $status = $this->contract_status->load_status_details($status_id);
                                $old_status_details = $this->contract_status->load_status_details($old_status);
                                $response["status_name"] = $status["status_name"];
                                $response["status_color"] = $status["status_color"];
                                $logged_in_user = $this->user_logged_in_data["user_id"];
                                $this->user_profile->fetch(["user_id" => $logged_in_user]);
                                $full_name = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName");
                                $contributors = $this->contract_contributor->load_contributors($contract_id);
                                $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
                                $notify["logged_in_user"] = $full_name;
                                $this->load->model("contract_sla_management", "contract_sla_managementfactory");
                                $this->contract_sla_management = $this->contract_sla_managementfactory->get_instance();
                                $this->contract_sla_management->contract_sla($contract_id, $logged_in_user, 1);
                                $this->contract->send_notifications("edit_contract_status", $notify, ["id" => $contract_id, "status" => $status["status_name"], "old_status" => $old_status_details["status_name"]]);
                                if ($this->system_preference->get_values()["webhooks_enabled"] == 1) {
                                    $webhook_data = $this->contract->load_contract_details($contract_id);
                                    $this->contract->trigger_web_hook("contract_status_updated", $webhook_data);
                                }
                                $response["success"]["msg"] = sprintf($this->lang->line("status_updated_message"), $this->lang->line("contract"));
                            }
                        }
                    }
                }
            }
        }
        $this->render($response);
    }
    private function save_watchers()
    {
        $response["result"] = false;
        $post_data = $this->input->post(NULL);
        if (!empty($post_data["contract_id"])) {
            $this->load->model("customer_portal_contract_watcher", "customer_portal_contract_watcherfactory");
            $this->customer_portal_contract_watcher = $this->customer_portal_contract_watcherfactory->get_instance();
            $watchers = $this->input->post("contract_watchers") ? $this->input->post("contract_watchers") : NULL;
            $this->customer_portal_contract_watcher->add_watchers_to_contract($watchers, $this->input->post("contract_id"));
            $response["result"] = true;
        }
        return $response;
    }
    public function awaiting_approvals()
    {
        $response = $this->responseData;
        $logged_in_user = $this->user_logged_in_data["user_id"];
        $this->user_profile->fetch(["user_id" => $logged_in_user]);
        $override_privacy = $this->user_profile->get_field("overridePrivacy");
        $this->load->model("language");
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $logged_in_user, "keyName" => "language"]);
        $lang = $this->user_preference->get_field("keyValue");
        $lang_id = $this->language->get_id_by_session_lang($lang);
        
        $response["success"] = $this->contract_approval_submission->api_load_awaiting_approvals($logged_in_user, $override_privacy, $lang_id);
        $this->render($response);
    }
    public function approval_center(){
        $response = $this->responseData;
        $logged_in_user = $this->user_logged_in_data["user_id"];
        $this->user_profile->fetch(["user_id" => $logged_in_user]);
        $override_privacy = $this->user_profile->get_field("overridePrivacy");
        $this->load->model("language");
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $logged_in_user, "keyName" => "language"]);
        $lang = $this->user_preference->get_field("keyValue");
        $lang_id = $this->language->get_id_by_session_lang($lang);
        $response["success"] = $this->contract_approval_submission->api_load_approval_center($logged_in_user, $override_privacy, $lang_id);
    
        $this->render($response);

    }
  
     public function submit_approval()
{
    $response = $this->responseData;
    $logged_user = $this->user_logged_in_data["user_id"];

    $this->load->model("contract_approval_history", "contract_approval_historyfactory");
    $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
    $this->load->model("contract_approval_status", "contract_approval_statusfactory");
    $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
    $this->load->model("contract_contributor", "contract_contributorfactory");
    $this->contract_contributor = $this->contract_contributorfactory->get_instance();


    $post_data = $this->input->post(NULL);

    if (empty($post_data)) {
        $response["error"] = $this->lang->line("no_data_provided");
        return $this->render($response);
    }

    if (!$this->contract->fetch($post_data["contract_id"])) {
        $response["error"] = $this->lang->line("contract_not_found");
        return $this->render($response);
    }

    if (!$this->contract_approval_status->fetch($post_data["contract_approval_status_id"])) {
        $response["error"] = $this->lang->line("approval_status_not_found");
        return $this->render($response);
    }

    $old_status = $this->contract_approval_status->get_field("status");

    // --- Handle rejected case ---
    if ($post_data["status"] === "rejected") {
        $fields_validation = $this->contract_approval_history->get("validate");
        $fields_validation["comment"] = [
            "required" => [
                "required"   => true,
                "allowEmpty" => false,
                "rule"       => ["minLength", 1],
                "message"    => $this->lang->line("cannot_be_blank_rule")
            ]
        ];
        $this->contract_approval_history->set("validate", $fields_validation);

        if (!empty($post_data["documents"]["id"]) && empty($post_data["documents"]["status_id"])) {
            $response["error"] = $this->lang->line("cannot_be_blank_rule");
            return $this->render($response);
        }

        $this->contract->send_notifications(
            "contract_rejected",
            ["contributors" => [], "logged_in_user" => $this->is_auth->get_fullname()],
            ["id" => $post_data["contract_id"]]
        );
    }

    // --- Set approval history fields ---
    $this->contract_approval_history->set_fields($post_data);
    $this->contract_approval_history->set_field("done_on", $post_data["done_on"] ? $post_data["done_on"] . " " . date("H:i:s") : "");
    $this->contract_approval_history->set_field("done_by", $logged_user);
    $this->contract_approval_history->set_field("done_by_type", "user");
    $this->contract_approval_history->set_field("from_action", $old_status);
    $this->contract_approval_history->set_field("to_action", $post_data["status"]);
    $this->contract_approval_history->set_field("label", $this->contract_approval_status->get_field("label"));
    $this->contract_approval_history->set_field("action", "approve");
    $this->contract_approval_history->set_field("done_by_ip", $this->input->ip_address());
    $this->contract_approval_history->set_field("approval_channel", $this->user_logged_in_data["channel"]);

    if (!$this->contract_approval_history->validate()) {
        $response["error"] = $this->contract_approval_history->get("validationErrors");
        return $this->render($response);
    }

    // --- Save approval history ---
    $this->contract_approval_history->insert();

    // --- Update approval status ---
    $this->contract_approval_status->set_field("status", $post_data["status"]);
    $this->contract_approval_status->update();

    $contract_approval_status_id = $this->contract_approval_status->get_field("id");
    $order = $this->contract_approval_status->get_field("rank");

    // --- Handle approvals ---
    if (!empty($post_data["enforce_previous_approvals"])) {
        $this->contract_approval_status->enforce_previous_approvals(
            $post_data["contract_id"],
            $contract_approval_status_id,
            $post_data["enforce_previous_approvals"],
            $this->contract_approval_status->get_field("rank")
        );
    } elseif ($post_data["status"] === "approved" && $this->contract_approval_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
        if (!$this->contract_approval_status->load_pending_approvals($post_data["contract_id"])) {
            $this->contract_approval_submission->set_field("status", "approved");
            $this->contract_approval_submission->update();

            $contributors = $this->contract_contributor->load_contributors($post_data["contract_id"]);
            $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
            $notify["logged_in_user"] = $this->is_auth->get_fullname();
            $this->contract->send_notifications("contract_approved", $notify, ["id" => $post_data["contract_id"]]);

            // trigger signatures
            if ($this->contract_signature_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                $this->contract_signature_submission->set_field("status", "awaiting_signature");
                $this->contract_signature_submission->update();

                $this->load->model("contract_signature_status", "contract_signature_statusfactory");
                $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
                $next_signees = $this->contract_signature_status->load_next_order(0, $post_data["contract_id"]);

                if ($next_signees) {
                    $signees = ["users" => [], "collaborators" => [], "user_groups" => []];
                    foreach ($next_signees as $next) {
                        $signature_data = $this->contract_signature_status->load_signature_data($next["id"]);
                        if (!empty($signature_data["users"])) $signees["users"] = array_merge($signees["users"], explode(",", $signature_data["users"]));
                        if (!empty($signature_data["collaborators"])) $signees["collaborators"] = array_merge($signees["collaborators"], explode(",", $signature_data["collaborators"]));
                        if (!empty($signature_data["user_groups"])) $signees["user_groups"] = array_merge($signees["user_groups"], explode(",", $signature_data["user_groups"]));
                    }
                    $this->load->model("signature", "signaturefactory");
                    $this->signature = $this->signaturefactory->get_instance();
                    $this->signature->notify_required_signees($signees, $post_data["contract_id"]);
                }
            }
        } else {
            if ($old_status === "rejected") {
                $this->contract_approval_submission->set_field("status", "awaiting_approval");
                $this->contract_approval_submission->update();
            }
            $this->load->model("approval", "approvalfactory");
            $this->approval = $this->approvalfactory->get_instance();
            $this->approval->notify_next_approvers($order, $post_data["contract_id"]);
        }
    }

    // --- Rejected branch ---
    if ($post_data["status"] === "rejected" && $this->contract_approval_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
        $this->contract_approval_submission->set_field("status", "awaiting_revision");
        $this->contract_approval_submission->update();
    }

    // --- Update related documents ---
    if (!empty($post_data["documents"]["id"]) && !empty($post_data["documents"]["status_id"])) {
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();

        foreach ($post_data["documents"]["id"] as $doc_id) {
            $this->document_management_system->fetch($doc_id);
            $this->document_management_system->set_field("document_status_id", $post_data["documents"]["status_id"]);
            $this->document_management_system->update();
            $this->document_management_system->reset_fields();
        }
    }

    // --- Final success response ---
    $response["success"]["data"] = ["contract_id" => $post_data["contract_id"], "overall_status" => $post_data["status"]];
    $response["success"]["msg"]  = sprintf($this->lang->line("status_updated_message"), $post_data["status"]);

    $this->render($response);
}



    //add negotiation to a contract
    public function add_negotiation($id = 0)
    {
        $response = $this->responseData;    
        if (!$this->input->post(NULL)) {
            if ($id) {
                if ($this->contract->fetch(["id" => $id])) {
                    $response["success"]["data"] = $this->contract->get_fields();
                    $creator_name = $this->user->get_name_by_id($response["success"]["data"]["createdBy"]);
                    $response["success"]["data"]["created_by_name"] = $creator_name["name"];
                } else {
                    $response["error"] = $this->lang->line("note_not_found");
                }
            } else {
                $response["error"] = $this->lang->line("missing_id");
            }
        } else {
            $this->contract->fetch(["id" => $this->input->post("id"), "contract_id" => $this->input->post("contract_id")]);
            $_POST["negotiation"] = $this->input->post("negotiation", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $logged_user = $this->user_logged_in_data["user_id"];
            $this->contract->set_fields($this->input->post(NULL));
            $this->contract->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->contract->set_field("modifiedBy", $logged_user);
            $this->contract->set_field("modifiedByChannel", $this->user_logged_in_data["channel"]);
            $this->contract->set_field("negotiation", $this->input->post("negotiation", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            if ($this->contract->update()) {
                $response["result"] = true;
                $response["success"]["data"] = ["contract_id" => $this->input->post("contract_id")];
            } else {
                $response["validation_errors"] = $this->contract->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    //awaiting signature
    public function awaiting_signatures()
    {
        $response = $this->responseData;
        $logged_in_user = $this->user_logged_in_data["user_id"];
        $this->user_profile->fetch(["user_id" => $logged_in_user]);
        $override_privacy = $this->user_profile->get_field("overridePrivacy");
        $this->load->model("language");
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $logged_in_user, "keyName" => "language"]);
        $lang = $this->user_preference->get_field("keyValue");
        $lang_id = $this->language->get_id_by_session_lang($lang);
        $response["success"] = $this->contract_signature_submission->api_load_awaiting_signatures($logged_in_user, $override_privacy, $lang_id);
        $this->render($response);
    }


    public function sign_contract_doc()
    {
    $response = $this->responseData;

    $this->load->model("contract_signature_status", "contract_signature_statusfactory");
    $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();

    // GET branch: requesting docs/signatures
    if ($get_data = $this->input->get(NULL, true)) {
        $contract_id = $get_data["contract_id"] ?? null;

        if (!$contract_id || empty($get_data["contract_signature_status_id"])) {
            $response["error"] = $this->lang->line("contract_required");
            return $this->render($response);
        }

        if ($this->contract_signature_status->fetch($get_data["contract_signature_status_id"])) {
            $signees = $this->contract_signature_status->load_signature_data($get_data["contract_signature_status_id"]);
            $allowed = false;

            if ($signees["users"] && in_array($this->is_auth->get_user_id(), explode(",", $signees["users"]))) {
                $allowed = true;
            }
            if ($signees["user_groups"] && in_array($this->user_logged_in_data["user_group_id"], explode(",", $signees["user_groups"]))) {
                $allowed = true;
            }
            if ($signees["is_requester_manager"]) {
                $this->contract->fetch($contract_id);
                $manager = $this->contract->load_requester_manager($this->contract->get_field("requester_id"));
                if ($manager && $manager["id"] == $this->is_auth->get_user_id()) {
                    $allowed = true;
                }
            }

            if (!$allowed) {
                $response["error"] = $this->lang->line("no_permission_to_sign");
            } else {
                $docs = $this->contract->load_approval_signature_documents($contract_id, "docx");
                if (empty($docs)) {
                    $response["error"] = $this->lang->line("no_related_contracts");
                } else {
                    $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
                    $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();

                    $signatures = $this->user_signature_attachment->load_all([
                        "where" => ["user_id", $this->is_auth->get_user_id()]
                    ]);

                    $data = [
                        "docs"        => $docs,
                        "contract_id" => $contract_id,
                        "id"          => $get_data["contract_signature_status_id"],
                        "signatures"  => $signatures
                    ];

                    $response["success"]["data"] = $data;
                    $response["success"]["msg"]  = $this->lang->line("contract_to_sign");
                }
            }
        }

        return $this->render($response);
    }

    // POST branch: apply signature to document
    $post_data = $this->input->post(NULL, true);

    if (empty($post_data["document_id"])) {
        $response["error"] = $this->lang->line("contract_required");
        return $this->render($response);
    }

    $this->document_management_system->fetch(["id" => $post_data["document_id"]]);
    $doc_details = $this->document_management_system->get_fields();
    $this->document_management_system->reset_fields();

    $core_path = substr(COREPATH, 0, -12);
    $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
    $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $doc_details["name"] . "." . $doc_details["extension"];

    $this->document_management_system->fetch($doc_details["parent"]);
    $lineage = $this->document_management_system->get_field("lineage");
    $template_dir = $documents_root_direcotry . "contracts" . $lineage;

    if (!is_file($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"])) {
        $response["error"] = $this->lang->line("contract_file_not_found");
        return $this->render($response);
    }

    copy($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"], $tmp_file);
    require_once $core_path . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
    $docx = new CreateDocxFromTemplate($tmp_file);
    $docx->setTemplateSymbol("%%");
    $template_variables = array_filter($docx->getTemplateVariables());

    if (empty($template_variables)) {
        $response["error"] = $this->lang->line("no_variable_to_replace");
        unlink($tmp_file);
        return $this->render($response);
    }

    if (!isset($post_data["id"]) || !$this->contract_signature_status->fetch($post_data["id"])) {
        $response["error"] = $this->lang->line("contract_required");
        unlink($tmp_file);
        return $this->render($response);
    }

    if (empty($post_data["signature_id"]) || empty($post_data["variable_name"])) {
        $response["error"] = $this->lang->line("no_signature_variable");
        unlink($tmp_file);
        return $this->render($response);
    }

    $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
    $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
    $this->user_signature_attachment->fetch($post_data["signature_id"]);

    $signature_variable = $post_data["variable_name"];
    $user_signature = $this->user_signature_attachment->get_field("signature");
    $signature_picture = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . str_pad($this->is_auth->get_user_id(), 10, "0", STR_PAD_LEFT) . DIRECTORY_SEPARATOR . "signature" . DIRECTORY_SEPARATOR . $user_signature;

    if (!$user_signature || !file_exists($signature_picture)) {
        $response["error"] = $this->lang->line("no_signature_saved");
        unlink($tmp_file);
        return $this->render($response);
    }

    $wf = new WordFragment($docx, "document");
    $wf->addImage(["src" => $signature_picture, "width" => 150, "height" => 40]);
    $docx->replaceVariableByWordFragment([$signature_variable => $wf], ["type" => "inline"]);

    $file_path = $template_dir . DIRECTORY_SEPARATOR . rand(1000, 9999);
    $docx->createDocx($file_path);

    require_once $core_path . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
    $docx = new Phpdocx\Create\CreateDocx();
    $doc_details["extension"] = "pdf";
    $docx->transformDocument($file_path . ".docx", $file_path . ".pdf", "libreoffice");

    $file_existant_version = $this->document_management_system->get_document_existant_version($doc_details["name"] . ".pdf", "file", $lineage);
    $this->document_management_system->reset_fields();
    $this->document_management_system->set_fields([
        "type"              => "file",
        "name"              => $doc_details["name"],
        "extension"         => $doc_details["extension"],
        "size"              => filesize($file_path . ".pdf"),
        "parent"            => $doc_details["parent"],
        "version"           => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1,
        "document_type_id"  => NULL,
        "document_status_id"=> NULL,
        "comment"           => NULL,
        "module"            => $doc_details["module"],
        "module_record_id"  => $doc_details["module_record_id"],
        "system_document"   => 0,
        "visible"           => 1,
        "visible_in_cp"     => 0,
        "visible_in_ap"     => 0,
        "createdOn"         => date("Y-m-d H:i:s"),
        "createdBy"         => $this->is_auth->get_user_id(),
        "createdByChannel"  => "API",
        "modifiedOn"        => date("Y-m-d H:i:s"),
        "modifiedBy"        => $this->is_auth->get_user_id(),
        "modifiedByChannel" => "API"
    ]);

    if ($this->document_management_system->insert()) {
        $this->document_management_system->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
        if ($this->document_management_system->update()) {
            if (rename($file_path . ".pdf", $template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"))) {
                $uploaded_file = $this->document_management_system->get_document_full_details(["d.id" => $this->document_management_system->get_field("id")]);
                if (empty($file_existant_version)) {
                    $response["success"]["msg"] = $this->lang->line("doc_generated_successfully");
                } else {
                    $this->file_versioning($file_existant_version, $uploaded_file, $response);
                }
            }
        } else {
            $response["error"] = $this->document_management_system->get("validationErrors");
        }
    } else {
        $response["error"] = $this->document_management_system->get("validationErrors");
    }

    unlink($tmp_file);

    $this->contract_signature_submission->fetch(["contract_id" => $post_data["contract_id"]]);
    $response["success"]["data"]["overall_status"] = $this->contract_signature_submission->get_field("status");
    $response["success"]["data"]["related_documents_count"] = $this->document_management_system->count_contract_related_documents($post_data["contract_id"]);

    return $this->render($response);
}

    //signature center
    public function signature_center($contract_id)
{
    $response = $this->responseData;

    if (!$contract_id || !$this->contract->fetch($contract_id)) {
        $response["error"] = $this->lang->line("invalid_record");
        return $this->render($response);
    }

    $this->load->model("contract_signature_status", "contract_signature_statusfactory");
    $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();

    $this->load->model("contract_signature_history", "signature_historyfactory");
    $this->contract_signature_history = $this->signature_historyfactory->get_instance();

    $data = [];
    if ($this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
        $data["overall_status"]    = $this->contract_signature_submission->get_field("status");
        $data["signature_history"] = $this->contract_signature_history->load_history($contract_id);
        $data["signature_center"]  = $this->contract_signature_status->load_signature_center_for_contract($contract_id);
        $data["manager"]           = $this->contract->load_requester_manager($this->contract->get_field("requester_id"));
    }

    $data["contract"] = [
        "id"   => $contract_id,
        "name" => $this->contract->get_field("name"),
    ];
    $data["model_code"] = $this->contract->get("modelCode");

    $response["success"]["data"] = $data;
    $response["success"]["msg"]  = $this->lang->line("signature_center_loaded");

    $this->render($response);
}


}

?>