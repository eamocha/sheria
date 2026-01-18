<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_fields extends My_Model_Factory
{
}
class mysql_Opinion_fields extends My_Model
{
    protected $modelName = "opinion_fields";
    protected $lookup_types = [["table" => "legal_cases", "url" => "cases/autocomplete", "controller" => "cases", "model" => "legal_case", "model_factory" => true, "id_pad_length" => 8, "external_data" => false, "external_data_properties" => NULL, "display_properties" => ["first_segment" => ["column_table" => "legal_cases", "column_name" => "caseID"], "second_segment" => ["column_table" => "legal_cases", "column_name" => "subject"], "format" => ["value" => "double_segment", "single_segment" => "%s", "double_segment" => "%s: %s"]]], ["table" => "users", "url" => "users/autocomplete/active", "controller" => "users", "model" => "user", "model_factory" => true, "id_pad_length" => 10, "external_data" => true, "external_data_properties" => ["table" => "user_profiles", "foreign_key" => "user_id"], "display_properties" => ["first_segment" => ["column_table" => "user_profiles", "column_name" => "firstName"], "second_segment" => ["column_table" => "user_profiles", "column_name" => "lastName"], "format" => ["value" => "double_segment", "single_segment" => "%s", "double_segment" => "%s %s"]]], ["table" => "opinion_locations", "url" => "opinions/location_autocomplete", "controller" => "opinions", "model" => "opinion", "model_factory" => true, "id_pad_length" => 0, "external_data" => false, "external_data_properties" => NULL, "display_properties" => ["first_segment" => ["column_table" => "opinion_locations", "column_name" => "location"], "format" => ["value" => "single_segment", "single_segment" => "%s"]]]];
    public function __construct()
    {
        parent::__construct();
        $this->fields = ["description" => ["db_key" => "description", "lang_key" => "description", "type" => "long_text", "db_required" => true, "group" => "main"], "related_case" => ["db_key" => "legal_case_id", "lang_key" => "related_case", "type" => "single_lookup", "type_data" => "cases", "db_required" => false, "group" => "main"], "assignee" => ["db_key" => "assigned_to", "lang_key" => "assigned_to", "type" => "single_lookup", "type_data" => "users", "db_required" => true, "group" => "main"], "reporter" => ["db_key" => "reporter", "lang_key" => "requested_by", "type" => "single_lookup", "type_data" => "users", "db_required" => false, "group" => "main"], "priority" => ["db_key" => "priority", "lang_key" => "priority", "type" => "list", "db_required" => true, "group" => "main"], "due_date" => ["db_key" => "due_date", "lang_key" => "due_date", "type" => "date", "db_required" => true, "group" => "main"], "estimated_effort" => ["db_key" => "estimated_effort", "lang_key" => "estimatedEffort", "type" => "short_text", "db_required" => false, "group" => "main"], "contributors" => ["db_key" => "contributors", "lang_key" => "contributors", "type" => "multiple_lookup", "type_data" => "users", "db_required" => false, "group" => "multiple_records"], "location" => ["db_key" => "opinion_location_id", "lang_key" => "location", "type" => "single_lookup", "type_data" => "opinions", "db_required" => false, "group" => "main"], "comment" => ["db_key" => "comment", "lang_key" => "comment", "type" => "long_text", "db_required" => false, "group" => "opinion_comments"]];
        $this->ci->load->model("opinion", "opinionfactory");
        $this->ci->opinion = $this->ci->opinionfactory->get_instance();
        $this->ci->load->model("custom_field", "custom_fieldfactory");
        $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
        $this->fields = $this->load_all_fields();
    }
    public function load_all_fields()
    {
        $custom_fields = $this->ci->custom_field->load_list_per_language($this->ci->opinion->get("modelName"));
        if (is_array($custom_fields) && !empty($custom_fields)) {
            foreach ($custom_fields as $custom_field) {
                $this->fields[$custom_field["id"]]["db_key"] = $custom_field["id"];
                $this->fields[$custom_field["id"]]["display_key"] = $custom_field["customName"];
                $this->fields[$custom_field["id"]]["type"] = $custom_field["type"];
                $this->fields[$custom_field["id"]]["type_data"] = $custom_field["type_data"];
                $this->fields[$custom_field["id"]]["group"] = "custom_field";
            }
        }
        return $this->fields;
    }
    public function return_screen_fields($opinion_id = 0, $transition = 0, $lang = false)
    {
        $this->ci->load->model("opinion_workflow_status_transition_screen_field", "opinion_workflow_status_transition_screen_fieldfactory");
        $this->ci->opinion_workflow_status_transition_screen_field = $this->ci->opinion_workflow_status_transition_screen_fieldfactory->get_instance();
        if (!$this->ci->opinion_workflow_status_transition_screen_field->fetch(["transition" => $transition])) {
            return false;
        }
        $data = unserialize($this->ci->opinion_workflow_status_transition_screen_field->get_field("data"));
        $data_fields = [];
        $this->ci->opinion->fetch($opinion_id);
        foreach ($data as $field => $is_required) {
            $field_data = [];
            $db_key = $this->fields[$field]["db_key"];
            $field_data_type = $this->fields[$field]["type"];
            switch ($this->fields[$field]["group"]) {
                case "main":
                    switch ($field_data_type) {
                        case "short_text":
                            $values = $this->return_field_values($opinion_id, $db_key);
                            $field_data = ["id" => $db_key, "field_label" => $this->ci->lang->line($this->fields[$field]["lang_key"]), "field_name" => "main_fields[" . $db_key . "]", "field_type" => $field_data_type, "field_value" => $values ? $values : "", "field_extra" => "dir=\"auto\" id=\"screen-field-" . $db_key . "\" class=\"form-control\" field-type=\"" . $field_data_type . "\"", "required" => $is_required ? "required" : ""];
                            break;
                        case "long_text":
                            $values = $this->return_field_values($opinion_id, $db_key, $field_data_type);
                            $field_data = ["id" => $db_key, "field_label" => $this->ci->lang->line($this->fields[$field]["lang_key"]), "field_name" => "main_fields[" . $db_key . "]", "field_type" => $field_data_type, "field_value" => $values ? $values : "", "field_extra" => "dir=\"auto\" id=\"screen-field-" . $db_key . "\" class=\"form-control\" field-type=\"" . $field_data_type . "\"", "required" => $is_required ? "required" : ""];
                            break;
                        case "list":
                            $values = $this->return_field_values($opinion_id, $db_key);
                            $options = [];
                            if (method_exists($this, $field . "_load_list")) {
                                $options = $this->{$field . "_load_list"}();
                            }
                            $field_data = ["id" => $db_key, "field_label" => $this->ci->lang->line($this->fields[$field]["lang_key"]), "field_name" => "main_fields[" . $db_key . "]", "field_type" => $field_data_type, "field_value" => $values ? $values : "", "field_extra" => "id=\"screen-field-" . $db_key . "\" class=\"form-control\" field-type=\"" . $field_data_type . "\"", "field_options" => $options, "required" => $is_required ? "required" : ""];
                            break;
                        case "date":
                            $values = $this->return_field_values($opinion_id, $db_key);
                            $field_data = ["id" => $db_key, "field_label" => $this->ci->lang->line($this->fields[$field]["lang_key"]), "field_name" => "main_fields[" . $db_key . "]", "field_type" => $field_data_type, "field_value" => $values ? $values : "", "field_extra" => "id=\"screen-field-" . $db_key . "\" placeholder=\"YYYY-MM-DD\" class=\"date form-control\" field-type=\"" . $field_data_type . "\"", "required" => $is_required ? "required" : ""];
                            break;
                        case "single_lookup":
                            $type_data = $this->fields[$field]["type_data"];
                            $lookup_type_properties = $this->get_lookup_type_properties($type_data);
                            $field_extra = "class=\"form-control search\" ";
                            $field_extra .= "id=\"screen-field-" . $db_key . "\" field-type=\"" . $field_data_type . "\" field-type-data=\"" . $lookup_type_properties["url"] . "\" ";
                            $second_segment = isset($lookup_type_properties["display_properties"]["second_segment"]["column_name"]) ? "," . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] : "";
                            $third_segment = isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? "," . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : "";
                            $field_extra .= "display-segments=\"" . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . $second_segment . $third_segment . "\"";
                            $field_extra .= " display-format-single-segment=\"" . $lookup_type_properties["display_properties"]["format"]["single_segment"] . "\" ";
                            $field_extra .= isset($lookup_type_properties["display_properties"]["format"]["double_segment"]) ? " display-format-double-segment=\"" . $lookup_type_properties["display_properties"]["format"]["double_segment"] . "\" " : "";
                            $field_extra .= isset($lookup_type_properties["display_properties"]["format"]["triple_segment"]) ? " display-format-triple-segment=\"" . $lookup_type_properties["display_properties"]["format"]["triple_segment"] . "\" " : "";
                            if (method_exists($this, "return_" . $field . "_value")) {
                                $values = $this->{"return_" . $field . "_value"}($opinion_id);
                            }
                            $field_data = ["id" => $db_key, "field_label" => $this->ci->lang->line($this->fields[$field]["lang_key"]), "field_name" => "main_fields[" . $db_key . "][value]", "field_hidden_name" => "main_fields[" . $db_key . "][id]", "field_type" => $field_data_type, "field_value" => $values["value"], "field_hidden_value" => $values["value_id"], "field_extra" => $field_extra, "required" => $is_required ? "required" : ""];
                            break;
                        default:
                            if (!$lang) {
                                $field_data["html"] = $this->ci->load->view("templates/form_component", $field_data, true);
                            }
                    }
                    break;
                case "custom_field":
                    $field_data = $this->ci->custom_field->get_field_html($this->ci->opinion->get("modelName"), $opinion_id, $field, $lang);
                    if (!$lang) {
                        $field_data["html"] = $field_data["custom_field"];
                        $field_data["hidden_html"] = $field_data["hidden_custom_field_id"] . $field_data["hidden_record_id"] . $field_data["hidden_value_id"];
                        $field_data["field_name"] = $field_data["id"];
                        $field_data["field_type"] = $field_data["type"];
                        $field_data["field_label"] = $field_data["customName"];
                    } else {
                        $field_data["category"] = $this->fields[$field]["group"];
                    }
                    $field_data["required"] = $is_required ? "required" : "";
                    break;
                case "multiple_records":
                    $type_data = $this->fields[$field]["type_data"];
                    $lookup_type_properties = $this->get_lookup_type_properties($type_data);
                    $field_extra = "class=\"form-control multiple-lookup \" ";
                    $field_extra .= "id=\"screen-field-" . $field . "\" field-type=\"" . $field_data_type . "\" field-type-data=\"" . $lookup_type_properties["url"] . "\" ";
                    $second_segment = isset($lookup_type_properties["display_properties"]["second_segment"]["column_name"]) ? "," . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] : "";
                    $third_segment = isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? "," . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : "";
                    $field_extra .= "display-segments=\"" . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . $second_segment . $third_segment . "\"";
                    $field_extra .= " display-format-single-segment=\"" . $lookup_type_properties["display_properties"]["format"]["single_segment"] . "\" ";
                    $field_extra .= isset($lookup_type_properties["display_properties"]["format"]["double_segment"]) ? " display-format-double-segment=\"" . $lookup_type_properties["display_properties"]["format"]["double_segment"] . "\" " : "";
                    $field_extra .= isset($lookup_type_properties["display_properties"]["format"]["triple_segment"]) ? " display-format-triple-segment=\"" . $lookup_type_properties["display_properties"]["format"]["triple_segment"] . "\" " : "";
                    if (method_exists($this, "return_" . $field . "_value")) {
                        $values = $this->{"return_" . $field . "_value"}($opinion_id);
                    }
                    $default_data = ["id" => $field, "field_label" => $this->ci->lang->line($this->fields[$field]["lang_key"]), "field_name" => "multiple_records[" . $field . "]", "field_type" => $field_data_type, "required" => $is_required ? "required" : ""];
                    if (!$lang) {
                        $multiple_data[$field] = $default_data;
                        $multiple_data[$field]["field_extra"] = $field_extra;
                        if (!empty($values)) {
                            foreach ($values as $record) {
                                $record["name"] = $record["status"] === "Inactive" ? $record["name"] . "(" . $this->ci->lang->line("Inactive") . ")" : $record["name"];
                                $record_data["field_value"] = [$record["id"], $record["name"]];
                                $multiple_data[$field]["records"][] = $record_data;
                            }
                        }
                    } else {
                        $field_data = $default_data;
                        if (!empty($values)) {
                            foreach ($values as $record) {
                                $field_data[$field]["records"][]["field_value"] = [$record["id"], $record["name"]];
                            }
                        }
                    }
                    break;
                case "opinion_comments":
                    $field_data = ["id" => $db_key, "field_label" => $this->ci->lang->line($this->fields[$field]["lang_key"]), "field_name" => "opinion_comments[" . $db_key . "]", "field_type" => $field_data_type, "field_value" => "", "field_extra" => "dir=\"auto\" id=\"screen-field-" . $db_key . "\" class=\"form-control\" field-type=\"" . $field_data_type . "\"", "required" => $is_required ? "required" : ""];
                    if (!$lang) {
                        $field_data["html"] = $this->ci->load->view("templates/form_component", $field_data, true);
                    }
                    break;
                default:
                    if (!empty($field_data)) {
                        if ($lang) {
                            unset($field_data["field_extra"]);
                        }
                        $data_fields["fields"][] = $field_data;
                        $field_data = [];
                    }
                    if (isset($multiple_data) && !empty($multiple_data)) {
                        $data_fields["fields"][]["multiple_records"] = $multiple_data;
                        $multiple_data = [];
                    }
            }
        }
        return $data_fields;
    }
    public function return_field_values($opinion_id = 0, $field = 0)
    {
        $_table = $this->_table;
        $this->_table = "opinions";
        $query["select"] = [$field, false];
        $query["where"] = ["id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response[$field];
    }
    public function return_assignee_value($opinion_id = 0)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query["select"] = ["opinions.assignedToId as value_id, CASE WHEN opinions.assignee_status='inactive' then CONCAT( opinions.assigned_to, ' ',  ' (','" . $this->ci->lang->line("inactive") . "' ,')') else opinions.assigned_to END as value", false];
        $query["where"] = ["opinions.id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function return_reporter_value($opinion_id = 0)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query["select"] = ["opinions.reportedById as value_id, CASE WHEN opinions.reporter_status='inactive' then CONCAT( opinions.reporter, ' ',  ' (','" . $this->ci->lang->line("inactive") . "' ,')') else opinions.reporter END as value", false];
        $query["where"] = ["opinions.id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function return_location_value($opinion_id = 0)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query["select"] = ["opinions.opinion_location_id as value_id, opinions.location as value", false];
        $query["where"] = ["opinions.id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function return_related_case_value($opinion_id = 0)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query["select"] = ["opinions.legal_case_id as value_id, CONCAT( opinions.caseId, ': ', opinions.caseSubject ) as value", false];
        $query["where"] = ["opinions.id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function return_contributors_value($opinion_id = 0)
    {
        return $this->ci->opinion->load_opinion_contributors($opinion_id);
    }
    public function priority_load_list()
    {
        return array_combine($this->ci->opinion->get("priorityValues"), [$this->ci->lang->line("critical"), $this->ci->lang->line("high"), $this->ci->lang->line("medium"), $this->ci->lang->line("low")]);
    }
    public function get_lookup_type_properties($lookup_type)
    {
        return $this->lookup_types[array_search($lookup_type, array_column($this->lookup_types, "controller"))];
    }
    public function validate_fields($transition)
    {
        $this->ci->load->model("opinion_workflow_status_transition_screen_field", "opinion_workflow_status_transition_screen_fieldfactory");
        $this->ci->opinion_workflow_status_transition_screen_field = $this->ci->opinion_workflow_status_transition_screen_fieldfactory->get_instance();
        $response["result"] = false;
        if (!$this->ci->opinion_workflow_status_transition_screen_field->fetch(["transition" => $transition])) {
            $response["errors"] = $this->ci->lang->line("no_data");
            return $response;
        }
        $is_required = unserialize($this->ci->opinion_workflow_status_transition_screen_field->get_field("data"));
        $records["customFields"] = $this->ci->input->post("customFields", true);
        $records["main_fields"] = $this->ci->input->post("main_fields", true);
        $records["multiple_records"] = $this->ci->input->post("multiple_records", true);
        $records["opinion_comments"] = $this->ci->input->post("opinion_comments", true);
        $required_fields = array_filter($is_required);
        if (!empty($required_fields) && !array_filter($records) || empty($is_required)) {
            $response["errors"] = $this->ci->lang->line("no_data");
            return $response;
        }
        $errors = [];
        foreach ($records as $type => $data) {
            if (is_array($data)) {
                foreach ($data as $field => $value) {
                    $db_key = array_search($field, array_column($this->fields, "db_key"));
                    $array_keys = array_keys($this->fields);
                    $key = $array_keys[$db_key];
                    if ($is_required[$key]) {
                        switch ($type) {
                            case "customFields":
                                if ((!isset($value["text_value"]) || $value["text_value"] === "") && (isset($value["date_value"]) && isset($value["time_value"]) && (!$value["date_value"] || !$value["time_value"]) || isset($value["date_value"]) && !isset($value["time_value"]) && !$value["date_value"] || !isset($value["date_value"]) && !isset($value["time_value"]))) {
                                    $errors[$field] = $this->ci->lang->line("cannot_be_blank_rule");
                                }
                                break;
                            case "main_fields":
                                if (is_array($value)) {
                                    foreach ($value as $value_to_check) {
                                        if (empty($value_to_check)) {
                                            $errors[$field] = $this->ci->lang->line("cannot_be_blank_rule");
                                        }
                                    }
                                } else {
                                    if (empty($value)) {
                                        $errors[$field] = $this->ci->lang->line("cannot_be_blank_rule");
                                    }
                                }
                                break;
                            case "multiple_records":
                                if (is_array($value) && !array_filter($value) || !$value) {
                                    $errors[$field] = $this->ci->lang->line("cannot_be_blank_rule");
                                }
                                break;
                            case "opinion_comments":
                                if (empty($value)) {
                                    $errors[$field] = $this->ci->lang->line("cannot_be_blank_rule");
                                }
                                break;
                        }
                    }
                }
            }
        }
        $response["result"] = !empty($errors) ? false : true;
        if (!empty($errors)) {
            $response["errors"] = $errors;
        }
        return $response;
    }
    public function save_fields($opinion_id = 0)
    {
        if (!$this->ci->opinion->fetch($opinion_id)) {
            return false;
        }
        $logged_user = $this->ci->is_auth->get_user_id();
        $records["customFields"] = $this->ci->input->post("customFields", true);
        $records["main_fields"] = $this->ci->input->post("main_fields", true);
        $records["multiple_records"] = $this->ci->input->post("multiple_records", true);
        $records["opinion_comments"] = $this->ci->input->post("opinion_comments", true);
        $response["result"] = true;
        foreach ($records as $type => $data) {
            if (is_array($data)) {
                if ($type === "customFields") {
                    $this->ci->custom_field->update_custom_fields($data);
                } else {
                    foreach ($data as $field => $value) {
                        switch ($type) {
                            case "multiple_records":
                                $lookup_error = $this->ci->opinion->get_lookup_validation_errors($this->ci->opinion->get("lookupInputsToValidate"), $this->ci->input->post(NULL));
                                if ($lookup_error) {
                                    $response["validation_errors"] = $lookup_error;
                                } else {
                                    $this->ci->load->model("opinion_contributor");
                                    $contributors_data = ["opinion_id" => $opinion_id, "users" => array_filter($value)];
                                    $this->ci->opinion_contributor->insert_contributors($contributors_data);
                                }
                                break;
                            case "main_fields":
                                $value = is_array($value) ? isset($value["id"]) ? $value["id"] : $value[0] : $value;
                                $this->ci->opinion->fetch($opinion_id);
                                $this->ci->opinion->set_field($field, $value);
                                if (!$this->ci->opinion->update()) {
                                    $response["validation_errors"] = $this->ci->opinion->get("validationErrors");
                                    $response["result"] = false;
                                } else {
                                    if ($this->ci->opinion->get_field("stage")) {
                                        $this->ci->load->model("legal_case_litigation_detail", "legal_case_litigation_detailfactory");
                                        $this->ci->legal_case_litigation_detail = $this->ci->legal_case_litigation_detailfactory->get_instance();
                                        $this->ci->legal_case_litigation_detail->update_stage_order($this->ci->opinion->get_field("stage"));
                                    }
                                }
                                break;
                            case "opinion_comments":
                                if ($this->fields[$field]["db_required"] || !empty($value)) {
                                    $this->ci->load->model("opinion_comment", "opinion_commentfactory");
                                    $this->ci->opinion_comment = $this->ci->opinion_commentfactory->get_instance();
                                    $comment = $this->ci->opinion_comment->regenerate_comment($value);
                                    $this->ci->opinion_comment->set_field("opinion_id", $opinion_id);
                                    $this->ci->opinion_comment->set_field("comment", $comment);
                                    $this->ci->opinion_comment->set_field("createdOn", date("Y-m-d H:i:s"));
                                    $this->ci->opinion_comment->set_field("modifiedBy", $logged_user);
                                    $this->ci->opinion_comment->set_field("edited", "0");
                                    if (!$this->ci->opinion_comment->insert()) {
                                        $response["validation_errors"] = $this->ci->opinion_comment->get("validationErrors");
                                        $response["result"] = false;
                                    } else {
                                        $opinion_comment_id = $this->ci->opinion_comment->get_field("id");
                                    }
                                }
                                break;
                        }
                    }
                    if (!empty($response["validation_errors"]) && isset($opinion_comment_id)) {
                        $this->ci->opinion_comment->delete($opinion_comment_id);
                    }
                }
            }
        }
        return $response;
    }
}
class mysqli_Opinion_fields extends mysql_Opinion_fields
{
}
class sqlsrv_Opinion_fields extends mysql_Opinion_fields
{
    public function return_field_values($opinion_id = 0, $field = 0, $field_type = "")
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $select_field = $field;
        if ($field_type == "long_text") {
            $select_field = "CAST (" . $field . " AS NVARCHAR(MAX)) as " . $field;
        }
        $query["select"] = [$select_field, false];
        $query["where"] = ["id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response[$field];
    }
    public function return_assignee_value($opinion_id = 0)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query["select"] = ["opinions.assignedToId as value_id, CASE WHEN opinions.assignee_status='inactive' then ( opinions.assigned_to + ' ' +  ' (' + '" . $this->ci->lang->line("inactive") . "' + ')') else opinions.assigned_to END as value", false];
        $query["where"] = ["opinions.id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function return_reporter_value($opinion_id = 0)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query["select"] = ["opinions.reportedById as value_id, CASE WHEN opinions.reporter_status='inactive' then ( opinions.reporter + ' ' +  ' (' + '" . $this->ci->lang->line("inactive") . "' + ')') else opinions.reporter END as value", false];
        $query["where"] = ["opinions.id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function return_related_case_value($opinion_id = 0)
    {
        $_table = $this->_table;
        $this->_table = "opinions_detailed_view AS opinions";
        $query["select"] = ["opinions.legal_case_id as value_id, ( opinions.caseId + ': ' + opinions.caseSubject ) as value", false];
        $query["where"] = ["opinions.id", $opinion_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
}

?>