<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Contract_template extends My_Model_Factory
{
}
class mysqli_Contract_template extends My_Model
{
    protected $modelName = "contract_template";
    protected $_table = "contract_templates";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "type_id", "sub_type_id", "name", "status", "document_id", "show_in_cp", "createdOn", "createdBy", "modifiedOn", "modifiedBy"];
    protected $required_fields = ["contract_name"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["type_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "sub_type_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "name" => ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)], "unique" => ["rule" => ["combinedUnique", ["name"]], "message" => sprintf($this->ci->lang->line("is_unique_rule"), $this->ci->lang->line("template_name"))]], "status" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "document_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
        $this->contract_fields = ["name" => ["db_key" => "name", "lang_key" => "contract_name", "type" => "short_text", "db_required" => true, "group" => "main"], "description" => ["db_key" => "description", "lang_key" => "description", "type" => "long_text", "db_required" => false, "group" => "main"], "assigned_team" => ["db_key" => "assigned_team_id", "lang_key" => "provider_group_id", "type" => "list", "db_required" => true, "group" => "main"], "assignee" => ["db_key" => "assignee_id", "lang_key" => "assignee", "type" => "list", "db_required" => false, "group" => "main"], "priority" => ["db_key" => "priority", "lang_key" => "priority", "type" => "list", "db_required" => true, "group" => "main"], "contract_date" => ["db_key" => "contract_date", "lang_key" => "contract_date", "type" => "date", "db_required" => false, "group" => "main"], "start_date" => ["db_key" => "start_date", "lang_key" => "start_date", "type" => "date", "db_required" => false, "group" => "main"], "end_date" => ["db_key" => "end_date", "lang_key" => "end_date", "type" => "date", "db_required" => false, "group" => "main"], "value" => ["db_key" => "value", "lang_key" => "value", "type" => "short_text", "db_required" => false, "group" => "main"], "currency" => ["db_key" => "currency_id", "lang_key" => "currency", "type" => "list", "db_required" => false, "group" => "main"], "party" => ["db_key" => "party_id", "lang_key" => "party", "type" => "multiple_lookup_per_type", "db_required" => false, "group" => "multiple_records"], "reference_number" => ["db_key" => "reference_number", "lang_key" => "reference_number", "type" => "short_text", "db_required" => false, "group" => "main"], "requester" => ["db_key" => "requester_id", "lang_key" => "requester", "type" => "single_lookup", "type_data" => "contacts", "db_required" => false, "group" => "main"], "country" => ["db_key" => "country_id", "lang_key" => "country", "type" => "list", "db_required" => false, "group" => "main"], "applicable_law" => ["db_key" => "app_law_id", "lang_key" => "applicable_law", "type" => "list", "db_required" => false, "group" => "main"], "renewal_type" => ["db_key" => "renewal_type", "lang_key" => "renewal", "type" => "multiple_fields_per_type", "db_required" => false, "group" => "main"]];
        $this->ci->load->model("contract_template_page", "contract_template_pagefactory");
        $this->ci->contract_template_page = $this->ci->contract_template_pagefactory->get_instance();
        $this->ci->load->model("contract_template_group", "contract_template_groupfactory");
        $this->ci->contract_template_group = $this->ci->contract_template_groupfactory->get_instance();
        $this->ci->load->model("contract_template_variable", "contract_template_variablefactory");
        $this->ci->contract_template_variable = $this->ci->contract_template_variablefactory->get_instance();
    }
    public function load_data($lang = 0)
    {
        $this->ci->load->model("contract_type_language", "contract_type_languagefactory");
        $this->ci->contract_type_language = $this->ci->contract_type_languagefactory->get_instance();
        $this->ci->load->model("sub_contract_type_language", "sub_contract_type_languagefactory");
        $this->ci->sub_contract_type_language = $this->ci->sub_contract_type_languagefactory->get_instance();
        $data["types"] = $this->ci->contract_type_language->load_all_types_per_language($lang);
        $lang_keys = array_column($this->contract_fields, "lang_key");
        $lang_vals = array_map(function ($val) {
            return $this->ci->lang->line($val);
        }, $lang_keys);
        $data["variable_types"] = ["contract_field" => $this->ci->lang->line("contract_meta_data_field"), "template_field" => $this->ci->lang->line("template_field")];
        $data["mapped_fields"] = array_combine(array_keys($this->contract_fields), $lang_vals);
        $data["required_fields"] = $this->required_fields;
        $data["field_types"] = ["" => "", "short_text" => $this->ci->lang->line("short_text"), "long_text" => $this->ci->lang->line("long_text"), "date" => $this->ci->lang->line("date"), "list" => $this->ci->lang->line("list"), "check_boxes" => $this->ci->lang->line("check_boxes"), "radio_buttons" => $this->ci->lang->line("radio_buttons")];
        return $data;
    }
    public function save_data()
    {
        $response["result"] = false;
        $template_post = $this->ci->input->post("template");
        $name = $template_post["name"] ?? NULL;
        $type_id = $template_post["type_id"] ?? NULL;
        $sub_type_id = isset($template_post["sub_type_id"]) && $template_post["sub_type_id"] ? $template_post["sub_type_id"] : NULL;
        $this->ci->contract_template->set_field("name", $name);
        $this->ci->contract_template->set_field("type_id", $type_id);
        $this->ci->contract_template->set_field("sub_type_id", $sub_type_id);
        $this->ci->contract_template->set_field("status", "open");
        $this->ci->contract_template->set_field("show_in_cp", 1);
        if ($this->ci->contract_template->validate()) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["validation_errors"]["uploadDoc"] = $this->ci->lang->line("file_required");
            } else {
                $upload_response = $this->ci->dmsnew->upload_file(["module" => "doc", "container_name" => "Contract Templates", "upload_key" => "uploadDoc"]);
               $this->ci->contract_template->set_field("document_id", $upload_response["file"]["id"]);
                $this->ci->contract_template->insert();
                $template_id = $this->ci->contract_template->get_field("id");
                $response = $this->insert_template_content($template_id, "add");
                if (!$response["result"]) {
                    $this->ci->contract_template->delete(["where" => ["id", $template_id]]);
                }
            }
        } else {
            $response["validation_errors"]["template"] = $this->ci->contract_template->get("validationErrors");
        }
        return $response;
    }
    public function update_data()
    {
        $response["result"] = false;
        $template_post = $this->ci->input->post("template");
        $this->ci->contract_template->fetch($template_post["template_id"]);
        $this->ci->contract_template->set_field("name", $template_post["name"]);
        $this->ci->contract_template->set_field("type_id", $template_post["type_id"]);
        $this->ci->contract_template->set_field("sub_type_id", $template_post["sub_type_id"] != "" ? $template_post["sub_type_id"]==0?NULL:$template_post["sub_type_id"] : NULL);
        $this->ci->contract_template->set_field("status", "open");
        if ($this->ci->contract_template->validate()) {
            if (!empty($_FILES) && !$_FILES["uploadDoc"]["name"]) {
                $response["validation_errors"]["uploadDoc"] = $this->ci->lang->line("file_required");
            } else {
                if (!empty($_FILES)) {
                    $upload_response = $this->ci->dmsnew->upload_file(["module" => "doc", "container_name" => "Contract Templates", "upload_key" => "uploadDoc"]);
                    $this->ci->contract_template->set_field("document_id", $upload_response["file"]["id"]);
                }
                $this->ci->contract_template->update();
                $template_id = $template_post["template_id"];
                $response = $this->insert_template_content($template_id, "edit");
            }
        } else {
            $response["validation_errors"]["template"] = $this->ci->contract_template->get("validationErrors");
        }
        return $response;
    }
    private function insert_template_content($template_id, $action)
    {
        $response["result"] = false;
        $validate = $this->validate_questionnaire();
        if ($validate["result"]) {
            if ($action == "edit") {
                $pages_ids = $this->ci->contract_template_page->load_list(["select" => "contract_template_pages.id", "where" => ["contract_template_pages.template_id", $template_id]], ["key" => "id", "value" => "id"]);
                $this->ci->contract_template_page->delete(["where" => ["template_id", $template_id]]);
                if (!empty($pages_ids)) {
                    $groups_ids = $this->ci->contract_template_group->load_list(["select" => "contract_template_groups.id", "where_in" => ["contract_template_groups.page_id", $pages_ids]], ["key" => "id", "value" => "id"]);
                    $this->ci->contract_template_group->delete(["where_in" => ["page_id", $pages_ids]]);
                    if (!empty($groups_ids)) {
                        $this->ci->contract_template_group->delete(["where" => ["group_id", $template_id]]);
                    }
                }
            }
            if ($this->ci->input->post("pages")) {
                $details_post = $this->ci->input->post("pages");
                foreach ($details_post as $p_id => $page) {
                    if (isset($page["groups"])) {
                        $groups_data = $page["groups"];
                        unset($details_post[$p_id]["groups"]);
                    }
                    $this->ci->contract_template_page->reset_fields();
                    $this->ci->contract_template_page->set_fields($page);
                    $this->ci->contract_template_page->set_field("template_id", $template_id);
                    if ($this->ci->contract_template_page->insert()) {
                        $page_id = $this->ci->contract_template_page->get_field("id");
                        if (isset($groups_data)) {
                            foreach ($groups_data as $g_id => $group) {
                                $this->ci->contract_template_group->reset_fields();
                                $this->ci->contract_template_group->set_field("title", $group["title"]);
                                $this->ci->contract_template_group->set_field("page_id", $page_id);
                                if ($this->ci->contract_template_group->insert()) {
                                    $group_id = $this->ci->contract_template_group->get_field("id");
                                    if (isset($group["variables"])) {
                                        foreach ($group["variables"] as $v_id => $variable) {
                                            $this->ci->contract_template_variable->reset_fields();
                                            $this->ci->contract_template_variable->set_fields($variable);
                                            $this->ci->contract_template_variable->set_field("group_id", $group_id);
                                            $this->ci->contract_template_variable->set_field("description", $this->ci->input->post("pages", false, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>")[$p_id]["groups"][$g_id]["variables"][$v_id]["description"]);
                                            if ($this->ci->contract_template_variable->insert()) {
                                                $variable_id = $this->ci->contract_template_variable->get_field("id");
                                                $response["result"] = true;
                                                $response["template_id"] = $template_id;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $response["result"] = true;
            }
            return $response;
        } else {
            return $validate;
        }
    }
    public function validate_questionnaire()
    {
        $response["result"] = true;
        $response["end_date_is_in_variables"] = false;
        if ($this->ci->input->post("pages")) {
            $details_post = $this->ci->input->post("pages");
            foreach ($details_post as $p_id => $page) {
                if (isset($page["groups"])) {
                    $groups_data = $page["groups"];
                }
                $this->ci->contract_template_page->reset_fields();
                $this->ci->contract_template_page->set_fields($page);
                $this->ci->contract_template_page->set_field("template_id", 1);
                if ($this->ci->contract_template_page->validate()) {
                    if (isset($groups_data)) {
                        foreach ($groups_data as $g_id => $group) {
                            $this->ci->contract_template_group->reset_fields();
                            $this->ci->contract_template_group->set_field("title", $group["title"]);
                            $this->ci->contract_template_group->set_field("page_id", 1);
                            if ($this->ci->contract_template_group->validate()) {
                                if (isset($group["variables"])) {
                                    foreach ($group["variables"] as $v_id => $variable) {
                                        $this->ci->contract_template_variable->reset_fields();
                                        $this->ci->contract_template_variable->set_fields($variable);
                                        $this->ci->contract_template_variable->set_field("group_id", 1);
                                        if ($this->ci->contract_template_variable->validate()) {
                                            $response["result"] = true;
                                            if ($variable["variable_property"] == "contract_field") {
                                                $field_details = $this->contract_fields[$variable["property_details"]];
                                                if (in_array($variable["property_details"], $this->required_fields)) {
                                                    unset($this->required_fields[array_search($variable["property_details"], $this->required_fields)]);
                                                }
                                                $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["db_key"] = $field_details["db_key"];
                                                $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["field_details"]["type"] = $field_details["type"];
                                                if ($variable["property_details"] == "end_date") {
                                                    $response["end_date_is_in_variables"] = true;
                                                }
                                                switch ($field_details["type"]) {
                                                    case "list":
                                                        if (method_exists($this, $variable["property_details"] . "_load_list")) {
                                                            $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["field_details"]["list"] = $this->{$variable["property_details"] . "_load_list"}();
                                                        }
                                                        break;
                                                    case "single_lookup":
                                                        $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["field_details"]["lookup"] = $field_details["type_data"];
                                                        break;
                                                    case "multiple_fields_per_type":
                                                        if (method_exists($this, $variable["property_details"] . "_load_list")) {
                                                            $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["field_details"]["multiple_fields_per_type"] = $this->{$variable["property_details"] . "_load_list"}();
                                                        }
                                                        break;
                                                }
                                            } else {
                                                $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["field_details"]["type"] = $variable["property_details"];
                                                switch ($variable["property_details"]) {
                                                    case "list":
                                                        if ($variable["property_data"]) {
                                                            $list = explode(",", $variable["property_data"]);
                                                            $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["field_details"]["list"] = ["" => $this->ci->lang->line("none")] + array_combine($list, $list);
                                                        } else {
                                                            $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["field_details"]["list"] = [];
                                                        }
                                                        break;
                                                    case true:
                                                        $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["field_details"]["options"] = explode(",", $variable["property_data"]);
                                                        break;
                                                }
                                            }
                                            $details_post[$p_id]["groups"][$g_id]["variables"][$v_id]["id"] = $v_id;
                                        } else {
                                            $response["validation_errors"]["pages"][$p_id]["groups"][$g_id]["variables"][$v_id] = $this->ci->contract_template_variable->get("validationErrors");
                                            $response["result"] = false;
                                            return $response;
                                        }
                                    }
                                }
                                $details_post[$p_id]["groups"][$g_id]["id"] = $g_id;
                            } else {
                                $response["validation_errors"]["pages"][$p_id]["groups"][$g_id] = $this->ci->contract_template_group->get("validationErrors");
                                $response["result"] = false;
                                return $response;
                            }
                        }
                    }
                    $details_post[$p_id]["id"] = $p_id;
                } else {
                    $response["validation_errors"]["pages"][$p_id] = $this->ci->contract_template_page->get("validationErrors");
                    $response["result"] = false;
                    return $response;
                }
            }
        } else {
            $response["result"] = true;
        }
        if ($response["result"]) {
            $response["result"] = true;
            $response["pages_data"] = $details_post;
            $response["required_fields"] = $this->required_fields;
        }
        return $response;
    }
    public function load_template_data($id = 0, $return_variable_html = false)
    {
        if ($this->ci->contract_template->fetch($id)) {
            $data["template"] = $this->ci->contract_template->get_fields();
            $this->ci->load->model("document_management_system", "document_management_systemfactory");
            $this->ci->document_management_system = $this->ci->document_management_systemfactory->get_instance();
            $this->ci->document_management_system->fetch($data["template"]["document_id"]);
            $data["template"]["document_name"] = $this->ci->document_management_system->get_field("name") . "." . $this->ci->document_management_system->get_field("extension");
            $this->ci->load->model("sub_contract_type_language", "sub_contract_type_languagefactory");
            $this->ci->sub_contract_type_language = $this->ci->sub_contract_type_languagefactory->get_instance();
            $data["type_sub_types"] = $this->ci->sub_contract_type_language->load_list_per_type_per_language($data["template"]["type_id"]);
            $table = $this->_table;
            $this->_table = "contract_template_pages as pages";
            $pages_query = ["select" => "pages.*", "where" => ["pages.template_id", $id]];
            $data["pages"] = $this->load_all($pages_query);
            $data["total_pages_count"] = 0;
            $data["total_groups_count"] = 0;
            $data["total_variables_count"] = 0;
            $data["end_date_is_in_variables"] = false;
            $renewal_type_is_variable = false;
            $required_fields_arr = [];
            if ($data["pages"]) {
                foreach ($data["pages"] as $index => $page) {
                    $data["total_pages_count"]++;
                    $data["pages"][$index]["groups"] = $this->ci->contract_template_group->load_all(["where" => ["page_id", $page["id"]]]);
                    if ($data["pages"][$index]["groups"]) {
                        foreach ($data["pages"][$index]["groups"] as $index1 => $group) {
                            $data["total_groups_count"]++;
                            $variables = $this->ci->contract_template_variable->load_all(["where" => ["group_id", $group["id"]]]);
                            if (!$return_variable_html) {
                                foreach ($variables as $key => $variable) {
                                    $data["total_variables_count"]++;
                                    if ($variable["variable_property"] == "contract_field") {
                                        $field_details = $this->contract_fields[$variable["property_details"]];
                                        if (in_array($variable["property_details"], $this->required_fields)) {
                                            unset($this->required_fields[array_search($variable["property_details"], $this->required_fields)]);
                                        }
                                        $variables[$key]["db_key"] = $field_details["db_key"];
                                        $variables[$key]["field_details"]["type"] = $field_details["type"];
                                        if ($variable["property_details"] == "end_date") {
                                            $data["end_date_is_in_variables"] = true;
                                        }
                                        switch ($field_details["type"]) {
                                            case "list":
                                                if (method_exists($this, $variable["property_details"] . "_load_list")) {
                                                    $variables[$key]["field_details"]["list"] = $this->{$variable["property_details"] . "_load_list"}();
                                                }
                                                break;
                                            case "single_lookup":
                                                $variables[$key]["field_details"]["lookup"] = $field_details["type_data"];
                                                break;
                                            case "multiple_fields_per_type":
                                                if (method_exists($this, $variable["property_details"] . "_load_list")) {
                                                    $variables[$key]["field_details"]["multiple_fields_per_type"] = $this->{$variable["property_details"] . "_load_list"}();
                                                }
                                                $renewal_type_is_variable = true;
                                                break;
                                        }
                                    } else {
                                        $variables[$key]["field_details"]["type"] = $variable["property_details"];
                                        switch ($variable["property_details"]) {
                                            case "list":
                                                if ($variable["property_data"]) {
                                                    $list = explode(",", $variable["property_data"]);
                                                    $variables[$key]["field_details"]["list"] = ["" => $this->ci->lang->line("none")] + array_combine($list, $list);
                                                } else {
                                                    $variables[$key]["field_details"]["list"] = [];
                                                }
                                                break;
                                            case true:
                                                $variables[$key]["field_details"]["options"] = explode(",", $variable["property_data"]);
                                                break;
                                        }
                                    }
                                }
                            }
                            $data["pages"][$index]["groups"][$index1]["variables"] = $variables;
                        }
                    }
                }
                if (!$data["end_date_is_in_variables"] && $renewal_type_is_variable) {
                    array_push($required_fields_arr, "end_date");
                }
                $data["required_fields"] = array_merge($required_fields_arr, $this->required_fields);
            }
            $this->_table = $table;
            return $data;
        } else {
            return false;
        }
    }
    public function return_contract_variables_value()
    {
        return $this->ci->contract->load_contract_contributors($contract_id);
    }
    public function return_contributors_value($contract_id = 0)
    {
        return $this->ci->contract->load_contract_contributors($contract_id);
    }
    public function return_party_value($contract_id = 0)
    {
        $this->ci->load->model("contract_party", "contract_partyfactory");
        $this->ci->contract_party = $this->ci->contract_partyfactory->get_instance();
        return $this->ci->contract_party->fetch_contract_parties_data($contract_id);
    }
    public function assigned_team_load_list()
    {
        $this->ci->load->model("provider_group");
        return $this->ci->provider_group->load_list([]);
    }
    public function assignee_load_list()
    {
        $data["users"] = $this->ci->user->load_users_list("", ["key" => "id", "value" => "name"]);
        return ["" => "---"] + $data["users"];
    }
    public function priority_load_list()
    {
        if (empty($this->ci->contract)) {
            $this->ci->load->model("contract", "contractfactory");
            $this->ci->contract = $this->ci->contractfactory->get_instance();
        }
        return array_combine(["" => ""] + $this->ci->contract->get("priorityValues"), [$this->ci->lang->line("choose_priority"), $this->ci->lang->line("critical"), $this->ci->lang->line("high"), $this->ci->lang->line("medium"), $this->ci->lang->line("low")]);
    }
    public function applicable_law_load_list()
    {
        $this->ci->load->model("applicable_law_language", "applicable_law_languagefactory");
        $this->ci->applicable_law_language = $this->ci->applicable_law_languagefactory->get_instance();
        return $this->ci->applicable_law_language->load_list_per_language();
    }
    public function country_load_list()
    {
        $this->ci->load->model("country", "countryfactory");
        $this->ci->country = $this->ci->countryfactory->get_instance();
        return $this->ci->country->load_countries_list();
    }
    public function currency_load_list()
    {
        $this->ci->load->model("iso_currency");
        return $this->ci->iso_currency->load_list(["order_by" => ["id", "asc"]], ["firstLine" => ["" => $this->ci->lang->line("none")]]);
    }
    public function delete_relations($id)
    {
        $sql = "DELETE variables FROM contract_template_variables variables\r\n                JOIN contract_template_groups groups ON groups.id = variables.group_id\r\n                JOIN contract_template_pages pages ON pages.id = groups.page_id\r\n                JOIN contract_templates ON contract_templates.id = pages.template_id\r\n                WHERE contract_templates.id = " . $id;
        if ($this->ci->db->query($sql)) {
            $sql = "DELETE groups FROM contract_template_groups groups\r\n                JOIN contract_template_pages pages ON pages.id = groups.page_id\r\n                JOIN contract_templates ON contract_templates.id = pages.template_id\r\n                WHERE contract_templates.id = " . $id;
            if ($this->ci->db->query($sql)) {
                $sql = "DELETE pages FROM contract_template_pages pages\r\n                JOIN contract_templates ON contract_templates.id = pages.template_id\r\n                WHERE contract_templates.id = " . $id;
                if ($this->ci->db->query($sql)) {
                    $this->ci->db->where("contract_templates.id", $id);
                    return $this->ci->db->delete("contract_templates");
                }
            }
        }
        return false;
    }
    public function renewal_type_load_list()
    {
        if (empty($this->ci->contract)) {
            $this->ci->load->model("contract", "contractfactory");
            $this->ci->contract = $this->ci->contractfactory->get_instance();
        }
        $renewals = ["" => "---"] + array_combine($this->ci->contract->get("renewal_values"), [$this->ci->lang->line("one_time"), $this->ci->lang->line("renewable_automatically"), $this->ci->lang->line("renewable_with_notice"), $this->ci->lang->line("unlimited_period"), $this->ci->lang->line("other")]);
        return $renewals;
    }
}
class mysql_Contract_template extends mysqli_Contract_template
{
}
class sqlsrv_Contract_template extends mysqli_Contract_template
{
}

?>