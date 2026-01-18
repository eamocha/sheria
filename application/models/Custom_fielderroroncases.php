<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Custom_field extends My_Model_Factory
{
}
class mysqli_Custom_field extends My_Model
{
    protected $modelName = "custom_field";
    protected $_table = "custom_fields";
    protected $_listFieldName = "id";
    protected $_fieldsNames = ["id", "model", "type", "type_data", "field_order", "category", "cp_visible"];
    protected $models = [["table" => "companies", "controller" => "companies", "model" => "company"],
        ["table" => "contacts", "controller" => "contacts", "model" => "contact"],
        ["table" => "legal_cases", "controller" => "cases", "model" => "legal_case"],
        ["table" => "legal_cases", "controller" => "cases", "model" => "matter"],
        ["table" => "legal_cases", "controller" => "cases", "model" => "criminal"],
        ["table" => "company_assets", "controller" => "companies", "model" => "company_asset"],
        ["table" => "tasks", "controller" => "tasks", "model" => "task"],
        ["table" => "opinions", "controller" => "legal_opinions", "model" => "opinion"],
        ["table" => "legal_case_hearings", "controller" => "cases", "model" => "legal_case_hearing"],
        ["table" => "contract", "controller" => "contracts", "model" => "contract"]];
    protected $types = ["short_text", "long_text", "date", "date_time", "list", "lookup", "number"];
    protected $lookup_types = [["table" => "companies", "controller" => "companies", "model" => "company", "model_factory" => true, "id_pad_length" => 8, "external_data" => false, "external_data_properties" => NULL, "display_properties" => ["first_segment" => ["column_table" => "companies", "column_name" => "name"], "second_segment" => ["column_table" => "companies", "column_name" => "shortName"], "format" => ["value" => "single_segment", "single_segment" => "%s", "double_segment" => "%s (%s)"]]], ["table" => "contacts", "controller" => "contacts", "model" => "contact", "model_factory" => true, "id_pad_length" => 8, "external_data" => false, "external_data_properties" => NULL, "display_properties" => ["first_segment" => ["column_table" => "contacts", "column_name" => "firstName"], "second_segment" => ["column_table" => "contacts", "column_name" => "father"], "third_segment" => ["column_table" => "contacts", "column_name" => "lastName"], "format" => ["value" => "triple_segment", "single_segment" => "%s", "double_segment" => "%s %s", "triple_segment" => "%s %s %s"]]], ["table" => "users", "controller" => "users", "model" => "user", "model_factory" => true, "id_pad_length" => 10, "external_data" => true, "external_data_properties" => ["table" => "user_profiles", "foreign_key" => "user_id"], "display_properties" => ["first_segment" => ["column_table" => "user_profiles", "column_name" => "firstName"], "second_segment" => ["column_table" => "user_profiles", "column_name" => "lastName"], "format" => ["value" => "double_segment", "single_segment" => "%s", "double_segment" => "%s %s"]]]];
    protected $section_types = ["short_text" => "main", "long_text" => "main", "date" => "date", "date_time" => "date", "list" => "main", "lookup" => "people", "number" => "main"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["model" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", array_column($this->models, "model")], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "type" => ["required" => true, "allowEmpty" => false, "rule" => ["inList", $this->types], "message" => sprintf($this->ci->lang->line("required_list_values"), implode(", ", $this->types))]];
    }
    public function load_fields($model)
    {
        $this->ci->load->model("language");
        $lang_id = $this->ci->language->get_id_by_session_lang();
        $query = ["select" => "custom_fields.*, custom_fields_languages.customName as customName,languages.name as langName,order_table.customName as orderedName", "join" => [["custom_fields_languages", "custom_fields_languages.custom_field_id = custom_fields.id", "left"], ["languages", "languages.id = custom_fields_languages.language_id", "left"], ["custom_fields_languages as order_table", "order_table.custom_field_id = custom_fields.id and order_table.language_id = '" . $lang_id . "'", "left"]], "where" => ["custom_fields.model like '" . $model . "'", NULL, false], "order_by" => ["custom_fields.field_order", "asc"]];
        return $this->load_all($query);
    }
    public function load_list_per_language($model, $lang = "en")
    {
        $a4lLang = $this->ci->session->userdata("AUTH_language");
        if ($a4lLang) {
            $lang = strtolower(substr($a4lLang, 0, 2));
        }
        $query = ["select" => "custom_fields.id, custom_fields.model, custom_fields.type, custom_fields.type_data, custom_fields.field_order, custom_fields_languages.customName", "join" => [["custom_fields_languages", "custom_fields_languages.custom_field_id = custom_fields.id", "left"], ["languages", "languages.id = custom_fields_languages.language_id", "left"]], "where" => [["languages.name", $lang], ["custom_fields.model", $model]], "order_by" => ["custom_fields.field_order"]];
        return $this->load_all($query);
    }
    public function load_legal_case_list_per_language($category, $type, $lang = "en")
    {
        $a4lLang = $this->ci->session->userdata("AUTH_language");
        if ($a4lLang) {
            $lang = strtolower(substr($a4lLang, 0, 2));
        }
        $query = ["select" => "custom_fields.id, custom_fields.model, custom_fields.type, custom_fields.type_data, custom_fields.field_order, custom_fields_languages.customName", "join" => [["custom_fields_languages", "custom_fields_languages.custom_field_id = custom_fields.id", "left"], ["languages", "languages.id = custom_fields_languages.language_id", "left"]], "where" => [["languages.name", $lang], ["custom_fields.model", "legal_case"], ["((custom_fields.id NOT IN (SELECT custom_fields_case_types.custom_field_id from custom_fields_case_types)) \r\n            OR \r\n            (" . $type . " IN (SELECT custom_fields_case_types.type_id from custom_fields_case_types WHERE custom_fields_case_types.custom_field_id = custom_fields.id)))", NULL, false]], "like" => ["custom_fields.category", $category], "order_by" => ["custom_fields.field_order"]];
        return $this->load_all($query);
    }
    public function load_contract_list_per_language($type, $lang = "en")
    {
        $a4lLang = $this->ci->session->userdata("AUTH_language");
        if ($a4lLang) {
            $lang = strtolower(substr($a4lLang, 0, 2));
        }
        $query = ["select" => "custom_fields.id, custom_fields.model, custom_fields.type, custom_fields.type_data, custom_fields.field_order, custom_fields_languages.customName", "join" => [["custom_fields_languages", "custom_fields_languages.custom_field_id = custom_fields.id", "left"], ["languages", "languages.id = custom_fields_languages.language_id", "left"]], "where" => [["languages.name", $lang], ["custom_fields.model", "contract"], ["((custom_fields.id NOT IN (SELECT custom_fields_per_model_types.custom_field_id from custom_fields_per_model_types)) \r\n            OR \r\n            (" . $type . " IN (SELECT custom_fields_per_model_types.type_id from custom_fields_per_model_types WHERE custom_fields_per_model_types.custom_field_id = custom_fields.id)))", NULL, false]], "order_by" => ["custom_fields.field_order"]];
        return $this->load_all($query);
    }
    public function insert_new_record()
    {
        $data = ["id" => NULL];
        $this->ci->db->insert($this->_table, $data);
        if (0 < $this->ci->db->affected_rows()) {
            return $this->ci->db->insert_id();
        }
        return 0;
    }
    public function load_custom_fields($id, $model, $lang = false)
    {
        $table = $this->get_model_properties("model", $model, "table");
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT custom_fields.id, custom_fields.model, custom_fields.type, custom_fields.type_data, custom_fields.field_order, custom_fields_languages.customName,\r\n            GROUP_CONCAT(custom_field_values.id) as value_id, GROUP_CONCAT(custom_field_values.text_value) AS text_value, custom_field_values.date_value, custom_field_values.time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id\r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $id . "\r\n            LEFT JOIN " . $table . " ON " . $table . ".id = custom_field_values.recordId\r\n            LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n        WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.model = '" . $model . "'\r\n            group by id\r\n        ORDER BY custom_fields.field_order";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
    public function load_custom_field($id, $record_id, $lang = false)
    {
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT custom_fields.id, custom_fields.model, custom_fields.type, custom_fields.type_data, custom_fields.field_order, custom_fields_languages.customName,\r\n             GROUP_CONCAT(custom_field_values.id) as value_id, GROUP_CONCAT(custom_field_values.text_value) AS text_value, custom_field_values.date_value, custom_field_values.time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id \r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $record_id . "\r\n            LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n            WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.id = '" . $id . "'\r\n             group by id\r\n        ORDER BY custom_fields.field_order";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
    public function load_legal_case_custom_fields($id, $model, $lang = false, $model_type_id = 0)
    {
        $table = $this->get_model_properties("model", $model, "table");
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT custom_fields.id, custom_fields.model, custom_fields.type, custom_fields.type_data, custom_fields.field_order, custom_fields_languages.customName,\r\n            GROUP_CONCAT(custom_field_values.id) as value_id, GROUP_CONCAT(custom_field_values.text_value) AS text_value, custom_field_values.date_value, custom_field_values.time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id\r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $id . "\r\n            LEFT JOIN " . $table . " ON " . $table . ".id = custom_field_values.recordId\r\n            LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n        WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.model = '" . $model . "' \r\n            AND custom_fields.category LIKE (SELECT CONCAT('%'," . $table . ".category,'%') FROM " . $table . " where " . $table . ".id = " . $id . ")\r\n            AND (\r\n            (custom_fields.id NOT IN (SELECT custom_fields_case_types.custom_field_id from custom_fields_case_types)) \r\n            OR \r\n            ((SELECT " . $table . ".case_type_id  FROM " . $table . " where " . $table . ".id = " . $id . ")IN (SELECT custom_fields_case_types.type_id from custom_fields_case_types WHERE custom_fields_case_types.custom_field_id = custom_fields.id))\r\n            )\r\n            group by id\r\n        ORDER BY custom_fields.field_order";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
    public function load_contract_custom_fields($contract_id, $model, $lang = false, $model_type_id = 0)
    {
        $table = $this->get_model_properties("model", $model, "table");
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT custom_fields.id, custom_fields.model, custom_fields.type, custom_fields.type_data, custom_fields.field_order, custom_fields_languages.customName,\r\n            GROUP_CONCAT(custom_field_values.id) as value_id, GROUP_CONCAT(custom_field_values.text_value) AS text_value, custom_field_values.date_value, custom_field_values.time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id\r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $contract_id . "\r\n            LEFT JOIN " . $table . " ON " . $table . ".id = custom_field_values.recordId " . ($model_type_id ? " LEFT JOIN custom_fields_per_model_types ON custom_fields_per_model_types.custom_field_id = custom_fields.id " : "") . "LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n        WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.model = '" . $table . "' \r\n            AND (" . ($model_type_id ? "custom_fields_per_model_types.type_id = " . $model_type_id . ")" : "(custom_fields.id NOT IN (SELECT custom_fields_per_model_types.custom_field_id from custom_fields_per_model_types)) \r\n            OR \r\n            ((SELECT " . $table . ".type_id  FROM " . $table . " where " . $table . ".id = " . $contract_id . ")IN (SELECT custom_fields_per_model_types.type_id from custom_fields_per_model_types WHERE custom_fields_per_model_types.custom_field_id = custom_fields.id))\r\n            )") . "\r\n            group by id\r\n        ORDER BY custom_fields.field_order";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
    public function get_field_html($model, $record_id = NULL, $custom_field_id = NULL, $lang = false, $extra_fields = [], $model_type_id = 0)
    {
        $custom_field_data = [];
        if ($custom_field_id) {
            $custom_fields = $this->load_custom_field($custom_field_id, $record_id, $lang);
        } else {
            $custom_fields = method_exists($this, "load_" . $model . "_custom_fields") ? $this->{"load_" . $model . "_custom_fields"}($record_id, $model, $lang, $model_type_id) : $this->load_custom_fields($record_id, $model, $lang);
        }
        if ($lang && $custom_field_id) {
            $custom_field = $custom_fields[0];
            switch ($custom_field["type"]) {
                case "short_text":
                    $custom_field_data = ["id" => $custom_field["id"], "field_name" => "customFields[" . $custom_field["id"] . "][text_value]", "field_hidden_name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "field_hidden_record" => "customFields[" . $custom_field["id"] . "][recordId]", "field_label" => $custom_field["customName"], "field_type" => $custom_field["type"], "field_value" => $custom_field["text_value"], "value_id" => $custom_field["value_id"]];
                    break;
                case "long_text":
                    $custom_field_data = ["id" => $custom_field["id"], "field_name" => "customFields[" . $custom_field["id"] . "][text_value]", "field_hidden_name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "field_hidden_record" => "customFields[" . $custom_field["id"] . "][recordId]", "field_label" => $custom_field["customName"], "field_type" => $custom_field["type"], "field_value" => $custom_field["text_value"], "value_id" => $custom_field["value_id"]];
                    break;
                case "date":
                    $custom_field_data = ["id" => $custom_field["id"], "field_name" => "customFields[" . $custom_field["id"] . "][date_value]", "field_hidden_name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "field_hidden_record" => "customFields[" . $custom_field["id"] . "][recordId]", "field_label" => $custom_field["customName"], "field_type" => $custom_field["type"], "field_value" => empty($custom_field["date_value"]) ? "" : $custom_field["date_value"], "value_id" => $custom_field["value_id"]];
                    break;
                case "date_time":
                    $custom_field_data = ["id" => $custom_field["id"], "field_name" => ["date" => "customFields[" . $custom_field["id"] . "][date_value]", "time" => "customFields[" . $custom_field["id"] . "][time_value]"], "field_hidden_name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "field_hidden_record" => "customFields[" . $custom_field["id"] . "][recordId]", "field_label" => $custom_field["customName"], "field_type" => $custom_field["type"], "field_value" => ["date" => empty($custom_field["date_value"]) ? "" : $custom_field["date_value"], "time" => empty($custom_field["time_value"]) ? "" : $custom_field["time_value"]], "value_id" => $custom_field["value_id"]];
                    break;
                case "list":
                    $custom_field_data = ["id" => $custom_field["id"], "field_name" => "customFields[" . $custom_field["id"] . "][text_value][]", "field_hidden_name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "field_hidden_record" => "customFields[" . $custom_field["id"] . "][recordId]", "field_label" => $custom_field["customName"], "field_type" => $custom_field["type"], "field_value" => empty($custom_field["text_value"]) ? [] : explode(",", $custom_field["text_value"]), "value_id" => $custom_field["value_id"], "field_options" => array_combine(explode(",", $custom_field["type_data"]), explode(",", $custom_field["type_data"]))];
                    break;
                case "lookup":
                    $custom_field_data = ["id" => $custom_field["id"], "field_name" => "customFields[" . $custom_field["id"] . "][text_value][]", "field_hidden_name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "field_hidden_record" => "customFields[" . $custom_field["id"] . "][recordId]", "field_label" => $custom_field["customName"], "field_type" => $custom_field["type"], "field_value" => $this->get_lookup_data($custom_field), "value_id" => $custom_field["value_id"], "type_data" => $custom_field["type_data"]];
                    break;
                case "number":
                    $custom_field_data = ["id" => $custom_field["id"], "field_name" => "customFields[" . $custom_field["id"] . "][text_value]", "field_hidden_name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "field_hidden_record" => "customFields[" . $custom_field["id"] . "][recordId]", "field_label" => $custom_field["customName"], "field_type" => $custom_field["type"], "field_value" => $custom_field["text_value"], "value_id" => $custom_field["value_id"]];
                    break;
                default:
                    return $custom_field_data;
            }
        } else {
            foreach ($custom_fields as $key => $custom_field) {
                $hidden_custom_field_id = isset($extra_fields["form"]) ? ["name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "value" => $custom_field["id"], "form" => isset($extra_fields["form"]) ? $extra_fields["form"] : "", "type" => "hidden"] : ["name" => "customFields[" . $custom_field["id"] . "][custom_field_id]", "value" => $custom_field["id"], "type" => "hidden"];
                $custom_fields[$key]["hidden_custom_field_id"] = form_input($hidden_custom_field_id);
                $hidden_record_id = isset($extra_fields["form"]) ? ["name" => "customFields[" . $custom_field["id"] . "][recordId]", "value" => $record_id, "form" => isset($extra_fields["form"]) ? $extra_fields["form"] : "", "type" => "hidden"] : ["name" => "customFields[" . $custom_field["id"] . "][recordId]", "value" => $record_id, "type" => "hidden"];
                $custom_fields[$key]["hidden_record_id"] = form_input($hidden_record_id);
                $hidden_value_id = isset($extra_fields["form"]) ? ["name" => "customFields[" . $custom_field["id"] . "][value_id]", "value" => $custom_field["value_id"], "form" => isset($extra_fields["form"]) ? $extra_fields["form"] : "", "type" => "hidden"] : ["name" => "customFields[" . $custom_field["id"] . "][value_id]", "value" => $custom_field["value_id"], "type" => "hidden"];
                $custom_fields[$key]["hidden_value_id"] = form_input($hidden_value_id);
                switch ($custom_field["type"]) {
                    case "short_text":
                        $custom_field_data = ["field_name" => "customFields[" . $custom_field["id"] . "][text_value]", "field_type" => $custom_field["type"], "field_value" => $custom_field["text_value"], "field_extra" => "dir=\"auto\" id=\"custom-field-" . $custom_field["id"] . "\" class=\"form-control\" field-type=\"" . $custom_field["type"] . "\" " . (isset($extra_fields["form"]) ? "form=\"" . $extra_fields["form"] . "\"" : ""), "field_options" => ""];
                        break;
                    case "long_text":
                        $custom_field_data = ["field_name" => "customFields[" . $custom_field["id"] . "][text_value]", "field_type" => $custom_field["type"], "field_value" => $custom_field["text_value"], "field_extra" => "dir=\"auto\" id=\"custom-field-" . $custom_field["id"] . "\" class=\"form-control\" field-type=\"" . $custom_field["type"] . "\" " . (isset($extra_fields["form"]) ? "form=\"" . $extra_fields["form"] . "\"" : ""), "field_options" => ""];
                        break;
                    case "date":
                        $custom_field_data = ["field_name" => "customFields[" . $custom_field["id"] . "][date_value]", "field_type" => $custom_field["type"], "field_value" => empty($custom_field["date_value"]) ? "" : $custom_field["date_value"], "field_extra" => "id=\"custom-field-" . $custom_field["id"] . "\" placeholder=\"YYYY-MM-DD\" class=\"date form-control\" field-type=\"" . $custom_field["type"] . "\" " . (isset($extra_fields["form"]) ? "form=\"" . $extra_fields["form"] . "\"" : ""), "field_options" => "", "container_id" => "id=\"custom-field-date-" . $custom_field["id"] . "-container\""];
                        break;
                    case "date_time":
                        $custom_field_data = ["field_name" => ["date" => "customFields[" . $custom_field["id"] . "][date_value]", "time" => "customFields[" . $custom_field["id"] . "][time_value]"], "field_type" => $custom_field["type"], "field_value" => ["date" => empty($custom_field["date_value"]) ? "" : $custom_field["date_value"], "time" => empty($custom_field["time_value"]) ? "" : $custom_field["time_value"]], "field_extra" => ["date" => "id=\"custom-field-date-" . $custom_field["id"] . "\" placeholder=\"YYYY-MM-DD\" class=\"date form-control\" field-type=\"" . $custom_field["type"] . "\" " . (isset($extra_fields["form"]) ? "form=\"" . $extra_fields["form"] . "\"" : ""), "time" => "id=\"custom-field-time-" . $custom_field["id"] . "\" placeholder=\"HH:MM\" class=\"time form-control\" field-type=\"" . $custom_field["type"] . "\" " . (isset($extra_fields["form"]) ? "form=\"" . $extra_fields["form"] . "\"" : "")], "container_id" => "id=\"custom-field-time-" . $custom_field["id"] . "-container\"", "field_options" => ""];
                        break;
                    case "list":
                        $custom_field_data = ["field_name" => "customFields[" . $custom_field["id"] . "][text_value][]", "field_type" => $custom_field["type"], "field_value" => empty($custom_field["text_value"]) ? [] : explode(",", $custom_field["text_value"]), "field_extra" => "id=\"custom-field-" . $custom_field["id"] . "\" multiple field-type=\"" . $custom_field["type"] . "\" " . (isset($extra_fields["form"]) ? "form=\"" . $extra_fields["form"] . "\"" : ""), "field_options" => array_combine(explode(",", $custom_field["type_data"]), explode(",", $custom_field["type_data"]))];
                        break;
                    case "lookup":
                        $lookup_type_properties = $this->get_lookup_type_properties($custom_field["type_data"]);
                        $field_extra = "id=\"custom-field-" . $custom_field["id"] . "\" multiple class=\"selectized\" ";
                        $field_extra .= "field-type=\"" . $custom_field["type"] . "\" field-type-data=\"" . $custom_field["type_data"] . "\" ";
                        $field_extra .= "display-segments=\"" . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . "," . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . (isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? "," . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : "") . "\" ";
                        $field_extra .= "display-format-single-segment=\"" . $lookup_type_properties["display_properties"]["format"]["single_segment"] . "\" ";
                        $field_extra .= "display-format-double-segment=\"" . $lookup_type_properties["display_properties"]["format"]["double_segment"] . "\" ";
                        if (isset($extra_fields["form"])) {
                            $field_extra .= "form=\"" . $extra_fields["form"] . "\" ";
                            $field_extra .= "form=\"" . $extra_fields["form"] . "\"";
                        }
                        $field_extra .= isset($lookup_type_properties["display_properties"]["format"]["triple_segment"]) ? "display-format-triple-segment=\"" . $lookup_type_properties["display_properties"]["format"]["triple_segment"] . "\" " : "";
                        $custom_field_data = ["field_id" => "custom-field-" . $custom_field["id"], "field_name" => "customFields[" . $custom_field["id"] . "][text_value][]", "field_type" => $custom_field["type"], "field_value" => $this->get_lookup_data($custom_field), "field_extra" => $field_extra, "field_options" => ""];
                        break;
                    case "number":
                        $custom_field_data = ["field_name" => "customFields[" . $custom_field["id"] . "][text_value]", "field_type" => $custom_field["type"], "field_value" => $custom_field["text_value"], "field_extra" => "dir=\"auto\" id=\"custom-field-" . $custom_field["id"] . "\" class=\"form-control\" field-type=\"" . $custom_field["type"] . "\" " . (isset($extra_fields["form"]) ? "form=\"" . $extra_fields["form"] . "\"" : ""), "field_options" => ""];
                        break;
                    default:
                        $custom_field_data += ["record_id" => $record_id];
                        $custom_fields[$key]["custom_field"] = $this->ci->load->view("templates/form_component", $custom_field_data, true);
                }
            }
            return $custom_field_id ? $custom_fields[0] : $custom_fields;
        }
    }
    public function load_lookup_model($lookup_type_properties)
    {
        $model = NULL;
        if (empty($this->ci->{$lookup_type_properties["model"]})) {
            if ($lookup_type_properties["model_factory"]) {
                $this->ci->load->model($lookup_type_properties["model"], $lookup_type_properties["model"] . "factory");
                $model = $this->ci->{$lookup_type_properties["model"]} = $this->ci->{$lookup_type_properties["model"] . "factory"}->get_instance();
            } else {
                $model = $this->ci->load->model($lookup_type_properties["model"]);
            }
        } else {
            $model = $this->ci->{$lookup_type_properties["model"]};
        }
        return $model;
    }
    public function get_lookup_data($custom_field)
    {
        $lookup_data = [];
        $lookup_type_properties = $this->get_lookup_type_properties($custom_field["type_data"]);
        $lookup_model = $this->load_lookup_model($lookup_type_properties);
        $look_up_records_ids = explode(",", $custom_field["text_value"]);
        if (array_filter($look_up_records_ids)) {
            foreach ($look_up_records_ids as $id) {
                $lookup_up_record_fetch_query["select"][] = [$lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . ", " . $lookup_type_properties["display_properties"]["second_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"]];
                if (isset($lookup_type_properties["display_properties"]["third_segment"]["column_table"])) {
                    $lookup_up_record_fetch_query["select"][] = [$lookup_type_properties["display_properties"]["third_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"]];
                }
                $lookup_up_record_fetch_query["where"] = [[$lookup_type_properties["table"] . ".id", $id]];
                if ($lookup_type_properties["external_data"]) {
                    $lookup_up_record_fetch_query["join"] = [$lookup_type_properties["external_data_properties"]["table"], $lookup_type_properties["external_data_properties"]["table"] . "." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = " . $lookup_type_properties["table"] . ".id", "left"];
                }
                $look_up_record = $lookup_model->load($lookup_up_record_fetch_query);
                if ($lookup_type_properties["display_properties"]["format"]["value"] == "single_segment") {
                    $lookup_data[$id] = sprintf($lookup_type_properties["display_properties"]["format"]["single_segment"], $look_up_record[$lookup_type_properties["display_properties"]["first_segment"]["column_name"]]);
                } else {
                    if ($lookup_type_properties["display_properties"]["format"]["value"] == "double_segment") {
                        $lookup_data[$id] = sprintf($lookup_type_properties["display_properties"]["format"]["double_segment"], $look_up_record[$lookup_type_properties["display_properties"]["first_segment"]["column_name"]], $look_up_record[$lookup_type_properties["display_properties"]["second_segment"]["column_name"]]);
                    } else {
                        $lookup_data[$id] = sprintf($lookup_type_properties["display_properties"]["format"]["triple_segment"], $look_up_record[$lookup_type_properties["display_properties"]["first_segment"]["column_name"]], $look_up_record[$lookup_type_properties["display_properties"]["second_segment"]["column_name"]], $look_up_record[$lookup_type_properties["display_properties"]["third_segment"]["column_name"]]);
                    }
                }
            }
        }
        return $lookup_data;
    }
    public function update_custom_fields($data)
    {
        $this->ci->load->model("custom_field_value");
        if (!empty($data)) {
            $custom_field_values_records = [];
            foreach ($data as $custom_field_data) {
                $this->reset_fields();
                $this->fetch($custom_field_data["custom_field_id"]);
                if (isset($custom_field_data["value_id"])) {
                    $this->delete_custom_field_values($custom_field_data["custom_field_id"], $custom_field_data["recordId"]);
                }
                if (!empty($custom_field_data["text_value"]) || isset($custom_field_data["text_value"]) && $custom_field_data["text_value"] !== "" || !empty($custom_field_data["date_value"]) || !empty($custom_field_data["time_value"])) {
                    $custom_field_value_record = ["custom_field_id" => $custom_field_data["custom_field_id"], "recordId" => $custom_field_data["recordId"], "text_value" => NULL, "date_value" => NULL, "time_value" => NULL];
                    $this->get_field("type");
                    switch ($this->get_field("type")) {
                        case "date":
                            if (!empty($custom_field_data["date_value"])) {
                                $custom_field_value_record["date_value"] = $custom_field_data["date_value"];
                                array_push($custom_field_values_records, $custom_field_value_record);
                            }
                            break;
                        case "date_time":
                            if (!empty($custom_field_data["date_value"])) {
                                $custom_field_value_record["date_value"] = $custom_field_data["date_value"];
                            }
                            if (!empty($custom_field_data["time_value"])) {
                                $custom_field_value_record["time_value"] = $custom_field_data["time_value"];
                            }
                            array_push($custom_field_values_records, $custom_field_value_record);
                            break;
                        case "list":
                            if (!empty($custom_field_data["text_value"])) {
                                foreach ($custom_field_data["text_value"] as $val) {
                                    $custom_field_value_record = ["custom_field_id" => $custom_field_data["custom_field_id"], "recordId" => $custom_field_data["recordId"], "text_value" => ltrim($val), "date_value" => NULL, "time_value" => NULL];
                                    array_push($custom_field_values_records, $custom_field_value_record);
                                }
                            }
                            break;
                        case "lookup":
                            if (!empty($custom_field_data["text_value"])) {
                                foreach ($custom_field_data["text_value"] as $val) {
                                    $custom_field_value_record = ["custom_field_id" => $custom_field_data["custom_field_id"], "recordId" => $custom_field_data["recordId"], "text_value" => $val, "date_value" => NULL, "time_value" => NULL];
                                    array_push($custom_field_values_records, $custom_field_value_record);
                                }
                            }
                            break;
                        case "number":
                            if ($custom_field_data["text_value"] !== "" && is_numeric($custom_field_data["text_value"])) {
                                $custom_field_value_record["text_value"] = $custom_field_data["text_value"];
                                array_push($custom_field_values_records, $custom_field_value_record);
                            }
                            break;
                        default:
                            if ($custom_field_data["text_value"] !== "") {
                                $custom_field_value_record["text_value"] = $custom_field_data["text_value"];
                                array_push($custom_field_values_records, $custom_field_value_record);
                            }
                    }
                }
            }
            $result = !empty($custom_field_values_records) ? $this->ci->custom_field_value->insert_batch($custom_field_values_records) : true;
            return $result;
        } else {
            return true;
        }
    }
    public function delete_custom_field_values($custom_field_id, $record_id)
    {
        $this->ci->custom_field_value->delete(["where" => [["custom_field_id", $custom_field_id], ["recordId", $record_id]]]);
    }
    public function set_fields_order($fields_order_data)
    {
        foreach ($fields_order_data as $field_data) {
            $this->reset_fields();
            $this->fetch($field_data["id"]);
            $this->set_field("field_order", $field_data["field_order"]);
            $this->update();
        }
        return true;
    }
    public function get_types()
    {
        $types = [];
        foreach ($this->get("types") as $type) {
            if ($type == "lookup") {
                foreach ($this->get("lookup_types") as $lookup_type) {
                    $types[] = ["type_key" => "lookup_" . $lookup_type["controller"], "type_label" => $this->ci->lang->line("lookup_" . $lookup_type["controller"])];
                }
            } else {
                $types[] = ["type_key" => $type, "type_label" => $this->ci->lang->line($type)];
            }
        }
        return $types;
    }
    public function get_new_field_order($model)
    {
        $result = $this->load(["select" => ["(MAX(field_order) + 1) as field_order"], "where" => [["model", $model]], "order_by" => ["field_order"]]);
        return $result["field_order"];
    }
    public function save_custom_field_data($custom_field_id, $custom_field_model, $data)
    {
        $old_category = $this->get_field("category");
        $custom_field_data = $custom_field_language_data = $response = [];
        $custom_field_data["model"] = $custom_field_model;
        if ($custom_field_model === "legal_case") {
            if (!isset($data["model_type"]) || empty($data["model_type"])) {
                $response["validationErrors"]["type_id"] = $this->ci->lang->line("cannot_be_blank_rule");
            }
            if (!isset($data["category"]) || empty($data["category"])) {
                $response["validationErrors"]["category"] = $this->ci->lang->line("cannot_be_blank_rule");
            } else {
                $custom_field_data["category"] = implode(",", $data["category"]);
            }
        }
        $custom_field_type = !empty($data["type"]) ? $data["type"] : $this->get_field("type");
        if ($custom_field_type == "list") {
            $custom_field_data["type"] = $custom_field_type;
            $custom_field_data["type_data"] = $data["type_data"];
            $this->validate["type_data"] = ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")];
            if (!empty($custom_field_id)) {
                $list_option_validation = $this->validate_list_options($custom_field_id, $custom_field_data["type_data"]);
                if (!empty($list_option_validation)) {
                    $response["validationErrors"]["type_data"] = sprintf($this->ci->lang->line("delete_list_options_forbidden"), $list_option_validation, $this->ci->lang->line($this->get_model_properties("model", $custom_field_model, "controller")));
                }
            }
        } else {
            if (strpos($custom_field_type, "lookup_") !== false) {
                $custom_field_data["type"] = "lookup";
                $custom_field_data["type_data"] = substr($data["type"], 7, strlen($data["type"]) - 7);
            } else {
                if ($custom_field_type == "lookup") {
                    $custom_field_type = "lookup_" . $data["type_data"];
                } else {
                    $custom_field_data["type"] = $custom_field_type;
                }
            }
        }
        $this->set_fields($custom_field_data);
        $this->ci->load->model(["custom_fields_language", "language"]);
        $languages = $this->ci->language->load_all();
        if ($this->ci->db->dbdriver == "sqlsrv") {
            $data["name_sp"] = "";
        }
        $auth_language = substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        if (empty($data["name_" . $auth_language])) {
            $response["validationErrors"]["name_" . $auth_language] = $this->ci->lang->line("cannot_be_blank_rule");
        }
        foreach ($languages as $language_key => $language) {
            $query["select"] = ["custom_fields_languages.id"];
            $query["join"] = ["custom_fields", "custom_fields.id = custom_fields_languages.custom_field_id", "left"];
            $query["where"] = [["custom_fields.model", $custom_field_model], ["language_id", $language["id"]]];
            if (!empty($custom_field_id)) {
                $query["where"] = [["custom_fields.id !=", $custom_field_id], ["customName", $data["name_" . $language["name"]]], ["language_id", $language["id"]], ["custom_fields.model", $custom_field_model]];
            } else {
                $query["where"][] = ["customName", !empty($data["name_" . $language["name"]]) ? $data["name_" . $language["name"]] : $data["name_" . $auth_language]];
            }
            $query_result = $this->ci->custom_fields_language->load_all($query);
            if (!empty($query_result)) {
                $response["validationErrors"]["name_" . $language["name"]] = sprintf($this->ci->lang->line("is_unique_rule"), $this->ci->lang->line("name"));
            } else {
                $custom_name = empty($data["name_" . $language["name"]]) ? $data["name_" . $auth_language] : $data["name_" . $language["name"]];
                array_push($custom_field_language_data, ["language_id" => $language["id"], "customName" => $custom_name]);
            }
        }
        if (empty($response["validationErrors"]) && $this->validate()) {
            if (empty($custom_field_id)) {
                $this->set_field("field_order", $this->get_new_field_order($custom_field_model));
                $response["result"] = $this->insert();
            } else {
                if ($custom_field_model === "legal_case") {
                    $changed = $this->is_custom_field_model_details_changed($custom_field_id, $old_category);
                }
                $response["result"] = $this->update();
            }
            if ($response["result"]) {
                $response["customFieldId"] = $this->get_field("id");
                if ($custom_field_model === "legal_case") {
                    $this->ci->load->model("custom_fields_case_type", "custom_fields_case_typefactory");
                    $this->ci->custom_fields_case_type = $this->ci->custom_fields_case_typefactory->get_instance();
                    $this->ci->custom_fields_case_type->delete(["where" => ["custom_field_id", $response["customFieldId"]]]);
                    if (is_array($data["model_type"]) && !empty($data["model_type"])) {
                        foreach ($data["model_type"] as $type_id) {
                            $this->ci->custom_fields_case_type->set_field("custom_field_id", $response["customFieldId"]);
                            $this->ci->custom_fields_case_type->set_field("type_id", $type_id);
                            $this->ci->custom_fields_case_type->insert();
                            $this->ci->custom_fields_case_type->reset_fields();
                        }
                    }
                    if ($custom_field_id && isset($changed) && $changed) {
                        $field_ids = $this->ci->db->select("id")->where("relatedCaseField", "customField_" . $custom_field_id)->get("customer_portal_screen_fields");
                        $field_id = $field_ids->result()[0] ?? false;
                        if ($field_id && $this->ci->db->where("customer_portal_screen_field_id", $field_id->id)->delete("customer_portal_screen_field_languages")) {
                            $this->ci->db->where("relatedCaseField", "customField_" . $custom_field_id)->delete("customer_portal_screen_fields");
                        }
                    }
                }
                $response["field_type"] = $this->ci->lang->line($custom_field_type);
                $response["customFieldModel"] = $custom_field_model;
                foreach ($custom_field_language_data as $row_key => $row_data) {
                    $this->ci->custom_fields_language->reset_fields();
                    if ($this->ci->custom_fields_language->fetch(["custom_field_id" => $this->get_field("id"), "language_id" => $row_data["language_id"]])) {
                        $this->ci->custom_fields_language->set_fields($row_data);
                        $this->ci->custom_fields_language->update();
                    } else {
                        $this->ci->custom_fields_language->set_fields(array_merge(["custom_field_id" => $this->get_field("id")], $row_data));
                        $this->ci->custom_fields_language->insert();
                    }
                }
                $response["customFieldLanguageData"] = array_column($custom_field_language_data, "customName");
                if ($this->ci->db->dbdriver == "sqlsrv") {
                    array_pop($response["customFieldLanguageData"]);
                }
            } else {
                $response["result"] = false;
            }
        } else {
            $response["validationErrors"] = !empty($response["validationErrors"]) ? array_merge($response["validationErrors"], $this->get_validation_errors()) : $this->get_validation_errors();
            $response["result"] = false;
        }
        return $response;
    }
    public function validate_list_options($custom_field_id, $list_options)
    {
        $this->fetch($custom_field_id);
        $current_list_options = $this->get_field("type_data");
        $removed_list_options = array_diff(explode(",", $current_list_options), explode(",", $list_options));
        $in_use_list_options = $delete_forbidden_list_options = [];
        if (empty($this->ci->custom_field_value)) {
            $this->ci->load->model("custom_field_value");
        }
        $query["select"] = [$this->ci->custom_field_value->_table . ".text_value"];
        $query["join"] = [$this->_table, $this->_table . ".id = " . $this->ci->custom_field_value->_table . ".custom_field_id", "left"];
        $query["where"] = [[$this->_table . ".type", "list"]];
        $query_data = $this->ci->custom_field_value->load_all($query);
        $in_use_list_options = array_column($query_data, "text_value");
        foreach ($in_use_list_options as $list_options) {
            foreach (explode(",", $list_options) as $list_option) {
                if (in_array($list_option, $removed_list_options) && !in_array($list_option, $delete_forbidden_list_options)) {
                    $delete_forbidden_list_options[] = $list_option;
                }
            }
        }
        return implode(",", $delete_forbidden_list_options);
    }
    public function prep_custom_field_filters($model, &$filters, &$query, $field = "", $table = false)
    {
        foreach ($filters as &$_filter) {
            $this->remove_cast_from_filter_operator($_filter["operator"]);
        }
        unset($_filter);
        foreach ($filters as $filter_key => $filter) {
            if (isset($filter["text_value"])) {
                if (is_array($filter["text_value"])) {
                    foreach ($filter["text_value"] as $value_key => $value) {
                        $filters[$filter_key]["text_value"][$value_key] = addslashes($value);
                    }
                } else {
                    $filters[$filter_key]["text_value"] = addslashes($filters[$filter_key]["text_value"]);
                }
            }
        }
        $table = $table ? $table : $this->get_model_properties("model", $model, "table");
        $field = $field ? $field : "id";
        foreach ($filters as $filter) {
            $this->reset_fields();
            $this->fetch($filter["id"]);
            $this->get_field("type");
            switch ($this->get_field("type")) {
                case "date":
                    $sql = "\r\n                    SELECT custom_field_values.recordId\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_id = '" . $filter["id"] . "'";
                    if (!empty($filter["date_value"]["start"])) {
                        $sql .= " AND date_value " . $this->get_k_operator($filter["operator"]["start"], $filter["date_value"]["start"]) . " '" . $filter["date_value"]["start"] . "'";
                    }
                    if (!empty($filter["date_value"]["end"])) {
                        $sql .= " AND date_value " . $this->get_k_operator($filter["operator"]["end"], $filter["date_value"]["end"]) . " '" . $filter["date_value"]["end"] . "'";
                    }
                    $query["where"][] = [$table . "." . $field . " IN (" . $sql . ")"];
                    break;
                case "date_time":
                    $sql = "\r\n                    SELECT custom_field_values.recordId\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_id = '" . $filter["id"] . "'";
                    if (!empty($filter["date_value"]["start"])) {
                        $sql .= " AND date_value " . $this->get_k_operator($filter["operator"]["start"], $filter["date_value"]["start"]) . " '" . $filter["date_value"]["start"] . "'";
                    }
                    if (!empty($filter["date_value"]["end"])) {
                        $sql .= " AND date_value " . $this->get_k_operator($filter["operator"]["end"], $filter["date_value"]["end"]) . " '" . $filter["date_value"]["end"] . "'";
                    }
                    if (!empty($filter["time_value"])) {
                        $sql .= " AND time_value " . $this->get_k_operator($filter["operator"]["time"], $filter["time_value"]) . " '" . $filter["time_value"] . "'";
                    }
                    $query["where"][] = [$table . "." . $field . " IN (" . $sql . ")"];
                    break;
                case "list":
                    $sql = "\r\n                    SELECT cfv_filter.recordId\r\n                    FROM (SELECT group_concat(text_value) as text_value, recordId\r\n                          FROM custom_field_values\r\n                          WHERE custom_field_id = '" . $filter["id"] . "'\r\n                          group by recordId) as cfv_filter WHERE 1 = 1";
                    if ($filter["text_value"]) {
                        $sql .= " AND (";
                        foreach ($filter["text_value"] as $index => $selected_list_option) {
                            $sql .= ($index !== 0 ? " OR " : "") . "find_in_set('" . $selected_list_option . "', cfv_filter.text_value) != 0";
                        }
                        $sql .= ")";
                    }
                    $query["where"][] = [$table . "." . $field . " " . ($filter["operator"] == "neq" ? "NOT " : "") . "IN (" . $sql . ")"];
                    break;
                case "lookup":
                    $lookup_type_properties = $this->get_lookup_type_properties($this->get_field("type_data"));
                    if ($lookup_type_properties["display_properties"]["format"]["value"] == "single_segment") {
                        $look_up_value = $lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"];
                    } else {
                        if ($lookup_type_properties["display_properties"]["format"]["value"] == "double_segment") {
                            $look_up_value = "CONCAT(" . $lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . ",' ',\r\n                            " . $lookup_type_properties["display_properties"]["second_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . ")";
                        } else {
                            $look_up_value = "\r\n                        CONCAT(" . $lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . ",' ',\r\n                            " . $lookup_type_properties["display_properties"]["second_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . ",' ',\r\n                            " . $lookup_type_properties["display_properties"]["third_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] . ")";
                            $look_up_value1 = "\r\n                        CONCAT(" . $lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . ",' ',\r\n                            " . $lookup_type_properties["display_properties"]["third_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] . ")";
                        }
                    }
                    $condition = $look_up_value . $this->get_k_operator($filter["operator"], $filter["text_value"]) . "'" . $filter["text_value"] . "'" . (isset($look_up_value1) ? " OR " . $look_up_value1 . $this->get_k_operator($filter["operator"], $filter["text_value"]) . "'" . $filter["text_value"] . "'" : "");
                    $sql = "\r\n                    SELECT cfv_filter.recordId\r\n                    FROM \r\n                    (SELECT group_concat(text_value) as text_value, recordId\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_id = '" . $filter["id"] . "'\r\n                    group by recordId) as cfv_filter WHERE 1 = 1\r\n                        AND find_in_set(\r\n                            LPAD(\r\n                                CONVERT(\r\n                                    (\r\n                                        SELECT " . $lookup_type_properties["table"] . ".id\r\n                                        FROM " . $lookup_type_properties["table"] . " " . ($lookup_type_properties["external_data"] ? "LEFT JOIN " . $lookup_type_properties["external_data_properties"]["table"] . " ON " . $lookup_type_properties["external_data_properties"]["table"] . "." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = " . $lookup_type_properties["table"] . ".id" : "") . "\r\n                                        WHERE " . $condition . "\r\n                                    ), NCHAR\r\n                                ), " . $lookup_type_properties["id_pad_length"] . ", '0'), cfv_filter.text_value) != 0";
                    $query["where"][] = [$table . "." . $field . " IN (" . $sql . ")"];
                    break;
                default:
                    $sql = "\r\n                    SELECT custom_field_values.recordId\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_values.custom_field_id = '" . $filter["id"] . "'\r\n                        AND custom_field_values.text_value " . $this->get_k_operator($filter["operator"], $filter["text_value"]) . " '" . addslashes($filter["text_value"]) . "'";
                    $query["where"][] = [$table . "." . $field . " IN (" . $sql . ")"];
            }
        }
    }
    public function get_model_properties($get_by_property, $get_by_property_value, $property)
    {
        return $this->models[array_search($get_by_property_value, array_column($this->models, $get_by_property))][$property];
    }
    public function get_lookup_type_properties($lookup_type)
    {
        return $this->lookup_types[array_search($lookup_type, array_column($this->lookup_types, "controller"))];
    }
    public function load_field_id_by_relation($field_name, $lang_id, $model)
    {
        $query = [];
        $response = [];
        $query["select"] = "custom_fields.id";
        $query["join"] = [["custom_fields_languages", "custom_fields_languages.custom_field_id = custom_fields.id", "left"]];
        $query["where"][] = ["custom_fields.model", $model];
        $query["where"][] = ["custom_fields_languages.language_id", $lang_id];
        $query["where"][] = ["custom_fields_languages.customName", $field_name];
        $return = $this->load($query);
        return $return ? $return["id"] * 1 : false;
    }
    public function load_lookup_record_ids($type_data, $lookup_values)
    {
        $lookup_ids = [];
        $properties = $this->get_lookup_type_properties($type_data);
        $model_instance = $this->load_lookup_model($properties);
        foreach ($lookup_values as $lookup_name) {
            if ($properties["display_properties"]["format"]["value"] === "single_segment") {
                if ($model_instance->fetch([$properties["display_properties"]["first_segment"]["column_name"] => $lookup_name])) {
                    $lookup_ids[] = $model_instance->get_field("id");
                }
            } else {
                $lookup_values_set = explode(" ", $lookup_name, 2);
                $column_table = $properties["display_properties"]["first_segment"]["column_table"];
                $column_name_1 = $properties["display_properties"]["first_segment"]["column_name"];
                $column_name_2 = $properties["display_properties"]["second_segment"]["column_name"];
                $column_id = $properties["external_data"] ? $properties["external_data_properties"]["foreign_key"] : "id";
                $query = [];
                $table = $this->_table;
                $this->_table = $column_table;
                $query["select"] = [$column_table . "." . $column_id . " as select_id", false];
                $query["where"][] = [$column_table . "." . $column_name_1, $lookup_values_set[0]];
                $query["where"][] = [$column_table . "." . $column_name_2, $lookup_values_set[1]];
                if ($return = $this->load($query)) {
                    $lookup_ids[] = $return["select_id"] * 1;
                }
                $this->_table = $table;
            }
        }
        return $lookup_ids;
    }
    public function count_related_objects($object_id, $object_table)
    {
        $query = "SELECT COUNT(custom_field_values.custom_field_id) AS related_lookup_custom_fields_count FROM custom_field_values\r\n                  LEFT JOIN custom_fields ON custom_fields.id = custom_field_values.custom_field_id\r\n                  WHERE custom_fields.type_data = '" . $object_table . "' AND " . $object_id . " = custom_field_values.text_value";
        $query_result = $this->ci->db->query($query);
        $query_data = $query_result->result_array();
        return $query_data[0]["related_lookup_custom_fields_count"];
    }
    public function load_grid_custom_fields($model, $table)
    {
        $custom_fields = $this->ci->custom_field->load_list_per_language($model);
        $parameters = [];
        foreach ($custom_fields as $field_data) {
            switch ($field_data["type"]) {
                case "date":
                    $parameters["Field"][$field_data["id"]] = "(SELECT cfv.date_value FROM custom_field_values AS cfv WHERE " . $table . ".id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                case "date_time":
                    $parameters["Field"][$field_data["id"]] = "(SELECT CONCAT(cfv.date_value, ' ', TIME_FORMAT(cfv.time_value, '%h:%i')) FROM custom_field_values AS cfv WHERE " . $table . ".id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                case "lookup":
                    $lookup_type_properties = $this->ci->custom_field->get_lookup_type_properties($field_data["type_data"]);
                    $lookup_displayed_columns_table = $lookup_type_properties["external_data"] ? "ltedt" : "ltt";
                    $lookup_external_data_join = $lookup_type_properties["external_data"] ? "LEFT JOIN " . $lookup_type_properties["external_data_properties"]["table"] . " ltedt ON ltedt." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = ltt.id" : "";
                    $last_segment = isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . ",' '," . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"];
                    $parameters["Field"][$field_data["id"]] = "\r\n                    (\r\n                        SELECT GROUP_CONCAT(" . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . ",' ' ," . $last_segment . " SEPARATOR ', ')\r\n                           FROM custom_field_values cfv\r\n                        left join " . $lookup_type_properties["table"] . " ltt on ltt.id = cfv.text_value " . $lookup_external_data_join . "\r\n                        where cfv.recordId = " . $table . ".id  and custom_field_id = " . $field_data["id"] . "\r\n                    )";
                    break;
                case "list":
                    $parameters["Field"][$field_data["id"]] = "(SELECT GROUP_CONCAT(cfv.text_value) FROM custom_field_values AS cfv WHERE cfv.recordId = " . $table . ".id AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                default:
                    $parameters["Field"][$field_data["id"]] = "(SELECT cfv.text_value FROM custom_field_values as cfv WHERE " . $table . ".id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
            }
        }
        $select = "";
        if (isset($parameters["Field"])) {
            $select .= ",";
            $count = 0;
            foreach ($parameters["Field"] as $id => $value) {
                $select .= $value . " as custom_field_" . $id . ($count != count($parameters["Field"]) - 1 ? "," : "");
                $count++;
            }
        }
        return $select;
    }
    public function validate_custom_field($custom_fields_value)
    {
        $response["result"] = true;
        if (!class_exists("Validation")) {
            $this->ci->load->library("Validation");
        }
        foreach ($custom_fields_value as $custom_field_value) {
            if (!empty($custom_field_value["date_value"])) {
                $date_validate = Validation::date($custom_field_value["date_value"]);
                if (!$date_validate) {
                    $field_name = "date_value_" . $custom_field_value["custom_field_id"];
                    $response["validationErrors"][$field_name] = sprintf($this->ci->lang->line("required_date_rule"), $this->ci->lang->line("date_value"));
                }
            }
            if (!empty($custom_field_value["time_value"])) {
                $time_validate = Validation::time($custom_field_value["time_value"]);
                if (!$time_validate) {
                    $field_name = "time_value_" . $custom_field_value["custom_field_id"];
                    $response["validationErrors"][$field_name] = sprintf($this->ci->lang->line("required_time_rule"), $this->ci->lang->line("time_value"));
                }
            }
        }
        if (!empty($response["validationErrors"])) {
            $response["result"] = false;
        }
        return $response;
    }
    public function remove_cast_from_filter_operator(&$operator)
    {
        if (is_array($operator)) {
            foreach ($operator as &$_operator) {
                if (4 < mb_strlen($_operator) && substr($_operator, 0, 5) === "cast_") {
                    $_operator = substr($_operator, 5);
                }
            }
            unset($_operator);
        }
    }
    public function validate_custom_fields_relation()
    {
        $post_data = $this->ci->input->post(NULL, true);
        $new_category = $post_data["new_category"] ?? $post_data["category"];
        $query = ["select" => "custom_fields.id", "join" => [["custom_field_values", "custom_field_values.custom_field_id = custom_fields.id", "inner"]], "where" => [["\r\n                     custom_fields.id NOT IN (\r\n                    SELECT cf.id  FROM custom_fields AS cf\r\n                    WHERE  cf.model = 'legal_case'\r\n                        AND ((cf.id NOT IN (SELECT custom_fields_case_types.custom_field_id from custom_fields_case_types))\r\n                        OR\r\n                        (" . $post_data["new_type"] . " IN (SELECT custom_fields_case_types.type_id from custom_fields_case_types WHERE custom_fields_case_types.custom_field_id = cf.id)))\r\n                        AND  cf.category LIKE '%" . $new_category . "%'\r\n                )"], ["custom_field_values.recordId", $post_data["case_id"]], ["custom_fields.model", "legal_case"], ["((custom_fields.id NOT IN (SELECT custom_fields_case_types.custom_field_id from custom_fields_case_types)) \r\n            OR \r\n            (" . $post_data["old_type"] . " IN (SELECT custom_fields_case_types.type_id from custom_fields_case_types WHERE custom_fields_case_types.custom_field_id = custom_fields.id)))", NULL, false]], "like" => ["custom_fields.category", $post_data["category"]]];
        $response = $this->load_list($query);
        return $response;
    }
    public function return_field_value_relation($id, $category, $types)
    {
        $types = implode(",", $types);
        $query = "\r\n            SELECT COUNT(0) as num_rows\r\n            FROM legal_cases\r\n            INNER JOIN custom_field_values ON custom_field_values.recordId = legal_cases.id AND custom_field_values.custom_field_id = " . $id . "\r\n            WHERE '" . $category . "' LIKE CONCAT('%',legal_cases.category,'%') AND legal_cases.case_type_id IN (" . $types . ")\r\n            AND ((text_value IS NOT NULL AND text_value != '') OR (date_value IS NOT NULL) OR (time_value IS NOT NULL AND time_value != ''))\r\n        ";
        $query_result = $this->ci->db->query($query);
        $query_data = $query_result->result_array();
        $num_rows = array_column($query_data, "num_rows");
        return 0 < $num_rows[0] ? false : true;
    }
    public function return_contract_field_value_relation($id, $types)
    {
        $types = implode(",", $types);
        $query = "\r\n            SELECT COUNT(0) as num_rows\r\n            FROM contract\r\n            INNER JOIN custom_field_values ON custom_field_values.recordId = contract.id AND custom_field_values.custom_field_id = " . $id . "\r\n            WHERE contract.type_id IN (" . $types . ")\r\n            AND ((text_value IS NOT NULL AND text_value != '') OR (date_value IS NOT NULL) OR (time_value IS NOT NULL AND time_value != ''))\r\n        ";
        $query_result = $this->ci->db->query($query);
        $query_data = $query_result->result_array();
        $num_rows = array_column($query_data, "num_rows");
        return 0 < $num_rows[0] ? false : true;
    }
    public function check_field_relation_per_category_type($id)
    {
        $query = "\r\n            SELECT COUNT(0) as num_rows\r\n            FROM legal_cases\r\n            INNER JOIN custom_fields ON custom_fields.id = " . $id . "\r\n            INNER JOIN custom_field_values ON custom_field_values.recordId = legal_cases.id AND custom_field_values.custom_field_id = " . $id . "\r\n            WHERE custom_fields.category LIKE CONCAT('%',legal_cases.category,'%') AND legal_cases.case_type_id IN (SELECT custom_fields_case_types.type_id from custom_fields_case_types WHERE custom_fields_case_types.custom_field_id = custom_fields.id)\r\n            AND ((text_value IS NOT NULL AND text_value != '') OR (date_value IS NOT NULL) OR (time_value IS NOT NULL AND time_value != ''))\r\n        ";
        $query_result = $this->ci->db->query($query);
        $query_data = $query_result->result_array();
        $num_rows = array_column($query_data, "num_rows");
        return 0 < $num_rows[0] ? false : true;
    }
    public function check_field_relation_per_contract_type($id)
    {
        $query = "\r\n            SELECT COUNT(0) as num_rows\r\n            FROM contract\r\n            INNER JOIN custom_fields ON custom_fields.id = " . $id . "\r\n            INNER JOIN custom_field_values ON custom_field_values.recordId = contract.id AND custom_field_values.custom_field_id = " . $id . "\r\n            WHERE contract.type_id IN (SELECT custom_fields_per_model_types.type_id from custom_fields_per_model_types WHERE custom_fields_per_model_types.custom_field_id = custom_fields.id)\r\n            AND ((text_value IS NOT NULL AND text_value != '') OR (date_value IS NOT NULL) OR (time_value IS NOT NULL AND time_value != ''))\r\n        ";
        $query_result = $this->ci->db->query($query);
        $query_data = $query_result->result_array();
        $num_rows = array_column($query_data, "num_rows");
        return 0 < $num_rows[0] ? false : true;
    }
    public function is_custom_field_model_details_changed($id, $old_category)
    {
        $changed = false;
        $new_category = $this->ci->input->post("category", true);
        $old_category_arr = explode(",", $old_category);
        $relation = count(array_intersect($new_category, $old_category_arr));
        if (count($new_category) < count($old_category_arr) || $relation === 0 || $relation < count($old_category_arr)) {
            $changed = true;
        }
        $this->ci->load->model("custom_fields_case_type", "custom_fields_case_typefactory");
        $this->ci->custom_fields_case_type = $this->ci->custom_fields_case_typefactory->get_instance();
        $old_types = array_column($this->ci->custom_fields_case_type->load_all(["where" => ["custom_field_id", $id]]), "type_id");
        $new_types = $this->ci->input->post("model_type", true);
        if (!empty($old_types) && is_array($new_types)) {
            $relation = count(array_intersect($new_types, $old_types));
            if (count($new_types) < count($old_types) || $relation === 0 || $relation < count($old_types)) {
                $changed = true;
            }
        }
        return $changed;
    }
    public function is_contract_custom_field_details_changed($id)
    {
        $changed = false;
        $this->ci->load->model("custom_fields_per_model_type", "custom_fields_per_model_typefactory");
        $this->ci->custom_fields_per_model_type = $this->ci->custom_fields_per_model_typefactory->get_instance();
        $old_types = array_column($this->ci->custom_fields_per_model_type->load_all(["where" => ["custom_field_id", $id]]), "type_id");
        $new_types = $this->ci->input->post("model_type", true);
        if (!empty($old_types) && is_array($new_types)) {
            $relation = count(array_intersect($new_types, $old_types));
            if (count($new_types) < count($old_types) || $relation === 0 || $relation < count($old_types)) {
                $changed = true;
            }
        }
        return $changed;
    }
    public function load_custom_fields_as_array($id, $model)
    {
        $custom_fields = $this->load_custom_fields($id, $model, "en");
        $custom_fields_arr = [];
        foreach ($custom_fields as $key => $custom_field) {
            $text_value = "";
            switch ($custom_field["type"]) {
                case "lookup":
                    $ids = explode(",", $custom_field["text_value"]);
                    switch ($custom_field["type_data"]) {
                        case "companies":
                            $this->ci->load->model("company", "companyfactory");
                            $this->ci->company = $this->ci->companyfactory->get_instance();
                            $companies = $this->ci->company->load_companies($ids);
                            $text_value = implode(", ", array_column($companies, "name"));
                            break;
                        case "contacts":
                            $this->ci->load->model("contact", "contactfactory");
                            $this->ci->contact = $this->ci->contactfactory->get_instance();
                            $contacts = $this->ci->contact->load_contacts($ids);
                            $text_value = implode(", ", array_column($contacts, "name"));
                            break;
                        case "users":
                            $this->ci->load->model("user", "userfactory");
                            $this->ci->user = $this->ci->userfactory->get_instance();
                            $users = $this->ci->user->load_users($ids);
                            $text_value = implode(", ", array_column($users, "name"));
                            break;
                    }
                    break;
                case "date_time":
                    $text_value = $custom_field["date_value"] . " " . $custom_field["time_value"];
                    break;
                default:
                    $text_value = $custom_field["text_value"];
                    $custom_fields_arr = array_merge($custom_fields_arr, [str_replace(" ", "_", $custom_field["customName"]) => $text_value]);
            }
        }
        return $custom_fields_arr;
    }
    public function load_custom_fields_name($model, $category = "", $lang = "en")
    {
        $arr = [];
        $query = ["select" => "custom_fields_languages.customName", "join" => [["custom_fields_languages", "custom_fields_languages.custom_field_id = custom_fields.id", "left"], ["languages", "languages.id = custom_fields_languages.language_id", "left"]], "where" => [["languages.name", $lang], ["custom_fields.model", $model]], "order_by" => ["custom_fields.field_order"]];
        if ($category !== "" && $model == "legal_case") {
            $query["like"] = ["custom_fields.category", $category];
        }
        $fields = $this->load_all($query);
        foreach ($fields as $key => $value) {
            $arr = array_merge($arr, [str_replace(" ", "_", $value["customName"]) => str_replace(" ", "_", $value["customName"])]);
        }
        return $arr;
    }
    public function load_custom_fields_visible_to_cp($id, $model, $lang = false)
    {
        $table = $this->get_model_properties("model", $model, "table");
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT custom_fields.id, custom_fields.model, custom_fields.type, custom_fields.type_data, custom_fields.field_order, custom_fields_languages.customName,\r\n            GROUP_CONCAT(custom_field_values.id) as value_id, GROUP_CONCAT(custom_field_values.text_value) AS text_value, custom_field_values.date_value, custom_field_values.time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id\r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $id . "\r\n            LEFT JOIN " . $table . " ON " . $table . ".id = custom_field_values.recordId\r\n            LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n        WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.model = '" . $model . "'\r\n            And custom_fields.cp_visible = '1'\r\n            group by id\r\n        ORDER BY custom_fields.field_order";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
}
class mysql_Custom_field extends mysqli_Custom_field
{
}
class sqlsrv_Custom_field extends mysqli_Custom_field
{
    public function insert_new_record()
    {
        $this->ci->db->simple_query("INSERT INTO custom_fields DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }
    public function prep_custom_field_filters($model, &$filters, &$query, $field = "", $table = false)
    {
        foreach ($filters as &$_filter) {
            $this->remove_cast_from_filter_operator($_filter["operator"]);
        }
        unset($_filter);
        $table = $table ? $table : $this->get_model_properties("model", $model, "table");
        $field = $field ? $field : "id";
        foreach ($filters as $filter) {
            $this->reset_fields();
            $this->fetch($filter["id"]);
            $this->get_field("type");
            switch ($this->get_field("type")) {
                case "date":
                    $sql = "\r\n                    SELECT custom_field_values.recordId\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_id = '" . $filter["id"] . "'";
                    if (!empty($filter["date_value"]["start"])) {
                        $sql .= " AND date_value " . $this->get_k_operator($filter["operator"]["start"], $filter["date_value"]["start"]) . " '" . $filter["date_value"]["start"] . "'";
                    }
                    if (!empty($filter["date_value"]["end"])) {
                        $sql .= " AND date_value " . $this->get_k_operator($filter["operator"]["end"], $filter["date_value"]["end"]) . " '" . $filter["date_value"]["end"] . "'";
                    }
                    $query["where"][] = [$table . "." . $field . " IN (" . $sql . ")"];
                    break;
                case "date_time":
                    $sql = "\r\n                    SELECT custom_field_values.recordId\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_id = '" . $filter["id"] . "'";
                    if (!empty($filter["date_value"]["start"])) {
                        $sql .= " AND date_value " . $this->get_k_operator($filter["operator"]["start"], $filter["date_value"]["start"]) . " '" . $filter["date_value"]["start"] . "'";
                    }
                    if (!empty($filter["date_value"]["end"])) {
                        $sql .= " AND date_value " . $this->get_k_operator($filter["operator"]["end"], $filter["date_value"]["end"]) . " '" . $filter["date_value"]["end"] . "'";
                    }
                    if (!empty($filter["time_value"])) {
                        $sql .= " AND time_value " . $this->get_k_operator($filter["operator"]["time"], $filter["time_value"]) . " '" . $filter["time_value"] . "'";
                    }
                    $query["where"][] = [$table . "." . $field . " IN (" . $sql . ")"];
                    break;
                case "list":
                    $sql = "\r\n                       SELECT cfv_filter.recordId\r\n                            FROM (SELECT\r\n                             recordId, \r\n                             text_value = STUFF(\r\n                                 (\r\n                                    SELECT ',' + cfv1.text_value\r\n                                    FROM custom_field_values as cfv1\r\n                                    WHERE cfv1.custom_field_id  = '" . $filter["id"] . "'\r\n                                    AND custom_field_values.recordId = cfv1.recordId FOR XML PATH ('')\r\n                                ) , 1, 1, '')\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_id = '" . $filter["id"] . "'\r\n                    group by recordId) as cfv_filter where 1=1 \r\n                    ";
                    if ($filter["text_value"]) {
                        $sql .= "AND (";
                        foreach ($filter["text_value"] as $index => $selected_list_option) {
                            $sql .= ($index !== 0 ? " OR " : "") . "PATINDEX ('%' + CONVERT(varchar(255), '" . $selected_list_option . "') + '%', cfv_filter.text_value) != 0";
                        }
                        $sql .= ")";
                    }
                    $query["where"][] = [$table . "." . $field . " " . ($filter["operator"] == "neq" ? "NOT " : "") . "IN (" . $sql . ")"];
                    break;
                case "lookup":
                    $lookup_type_properties = $this->get_lookup_type_properties($this->get_field("type_data"));
                    if ($lookup_type_properties["display_properties"]["format"]["value"] == "single_segment") {
                        $look_up_value = $lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"];
                    } else {
                        if ($lookup_type_properties["display_properties"]["format"]["value"] == "double_segment") {
                            $look_up_value = "\r\n                            " . $lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . " +\r\n                            ' ' +\r\n                            " . $lookup_type_properties["display_properties"]["second_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"];
                        } else {
                            $look_up_value = "\r\n                            " . $lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . " +\r\n                            ' ' +\r\n                            " . $lookup_type_properties["display_properties"]["second_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . " +\r\n                            ' ' +\r\n                            " . $lookup_type_properties["display_properties"]["third_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"];
                            $look_up_value1 = "\r\n                            " . $lookup_type_properties["display_properties"]["first_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . " +\r\n                            ' ' +\r\n                            " . $lookup_type_properties["display_properties"]["third_segment"]["column_table"] . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"];
                        }
                    }
                    $condition = $look_up_value . $this->get_k_operator($filter["operator"], $filter["text_value"]) . "'" . $filter["text_value"] . "'" . (isset($look_up_value1) ? " OR " . $look_up_value1 . $this->get_k_operator($filter["operator"], $filter["text_value"]) . "'" . $filter["text_value"] . "'" : "");
                    $sql = "\r\n                    SELECT cfv_filter.recordId\r\n                            FROM (SELECT\r\n                             recordId, \r\n                             text_value = STUFF(\r\n                                 (\r\n                                    SELECT ',' + cfv1.text_value\r\n                                    FROM custom_field_values as cfv1\r\n                                    WHERE cfv1.custom_field_id  = '" . $filter["id"] . "'\r\n                                    AND custom_field_values.recordId = cfv1.recordId FOR XML PATH ('')\r\n                                ) , 1, 1, '')\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_id = '" . $filter["id"] . "'\r\n                    group by recordId) as cfv_filter where 1=1 \r\n                        AND PATINDEX ('%_,'+ CONVERT(varchar(255), (\r\n                                            RIGHT(REPLICATE('0', " . $lookup_type_properties["id_pad_length"] . ") + (\r\n                                                SELECT " . $lookup_type_properties["table"] . ".id\r\n                                                FROM " . $lookup_type_properties["table"] . " " . ($lookup_type_properties["external_data"] ? "LEFT JOIN " . $lookup_type_properties["external_data_properties"]["table"] . " ON " . $lookup_type_properties["external_data_properties"]["table"] . "." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = " . $lookup_type_properties["table"] . ".id" : "") . "\r\n                                                WHERE " . $condition . "\r\n                                            ), " . $lookup_type_properties["id_pad_length"] . ")\r\n                                        )\r\n                                    ) +',_%' , ',,'+ cfv_filter.text_value+',,') != 0";
                    $query["where"][] = [$table . "." . $field . " IN (" . $sql . ")"];
                    break;
                default:
                    $sql = "\r\n                    SELECT custom_field_values.recordId\r\n                    FROM custom_field_values\r\n                    WHERE custom_field_values.custom_field_id = '" . $filter["id"] . "'\r\n                        AND custom_field_values.text_value " . $this->get_k_operator($filter["operator"], $filter["text_value"]) . " '" . $this->escape_universal_search_keyword($filter["text_value"]) . "'";
                    $query["where"][] = [$table . "." . $field . " IN (" . $sql . ")"];
            }
        }
    }
    public function count_related_objects($object_id, $object_table)
    {
        $query = "SELECT COUNT(custom_field_values.custom_field_id) AS related_lookup_custom_fields_count FROM custom_field_values\r\n                  LEFT JOIN custom_fields ON custom_fields.id = custom_field_values.custom_field_id\r\n                  WHERE custom_fields.type_data = '" . $object_table . "' AND CAST(" . $object_id . " AS NVARCHAR(10)) = custom_field_values.text_value";
        $query_result = $this->ci->db->query($query);
        $query_data = $query_result->result_array();
        return $query_data[0]["related_lookup_custom_fields_count"];
    }
    public function load_grid_custom_fields($model, $table, $cf_max = "")
    {
        $custom_fields = $this->ci->custom_field->load_list_per_language($model);
        $parameters = [];
        foreach ($custom_fields as $field_data) {
            switch ($field_data["type"]) {
                case "date":
                    $parameters["Field"][$field_data["id"]] = "(SELECT cfv.date_value FROM custom_field_values AS cfv WHERE " . $table . ".id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                case "date_time":
                    $parameters["Field"][$field_data["id"]] = "(SELECT (FORMAT(cfv.date_value, N'yyyy-MM-dd') + ' ' + FORMAT(cfv.time_value, N'hh\\:mm')) FROM custom_field_values AS cfv WHERE " . $table . ".id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
                    break;
                case "lookup":
                    $lookup_type_properties = $this->ci->custom_field->get_lookup_type_properties($field_data["type_data"]);
                    $lookup_displayed_columns_table = $lookup_type_properties["external_data"] ? "ltedt" : "ltt";
                    $lookup_external_data_join = $lookup_type_properties["external_data"] ? "LEFT JOIN " . $lookup_type_properties["external_data_properties"]["table"] . " ltedt ON ltedt." . $lookup_type_properties["external_data_properties"]["foreign_key"] . " = ltt.id" : "";
                    $last_segment = isset($lookup_type_properties["display_properties"]["third_segment"]["column_name"]) ? $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"] . ",' '," . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["third_segment"]["column_name"] : $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["second_segment"]["column_name"];
                    $parameters["Field"][$field_data["id"]] = "\r\n                         (\r\n                              STUFF((\r\n                                  SELECT ',' + " . $lookup_displayed_columns_table . "." . $lookup_type_properties["display_properties"]["first_segment"]["column_name"] . "+ ' '+" . $last_segment . "\r\n                                  FROM custom_field_values cfv\r\n                                  left join " . $lookup_type_properties["table"] . " ltt on CAST(ltt.id AS VARCHAR) = cfv.text_value " . $lookup_external_data_join . "\r\n                                  where cfv.recordId = " . $table . ".id  and custom_field_id = " . $field_data["id"] . "\r\n                                  FOR XML PATH('')), 1, 1, '')\r\n                          )";
                    break;
                case "list":
                    $parameters["Field"][$field_data["id"]] = "( \r\n                    STUFF((SELECT ',' + cfv.text_value FROM custom_field_values cfv WHERE cfv.recordId = " . $table . ".id AND cfv.custom_field_id = " . $field_data["id"] . " FOR XML PATH ('')), 1, 1, ''))";
                    break;
                default:
                    $parameters["Field"][$field_data["id"]] = "(SELECT cfv.text_value FROM custom_field_values as cfv WHERE " . $table . ".id = cfv.recordId AND cfv.custom_field_id = " . $field_data["id"] . ")";
            }
        }
        $select = "";
        if (isset($parameters["Field"])) {
            $select .= ",";
            $count = 0;
            foreach ($parameters["Field"] as $id => $value) {
                $select .= $value . " as custom_field_" . $id . ($count != count($parameters["Field"]) - 1 ? "," : "");
                $count++;
                if ($count == $cf_max) {
                }
            }
        }
        return $select;
    }
    public function load_custom_fields($id, $model, $lang = false)
    {
        $table = $this->get_model_properties("model", $model, "table");
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT MAX(custom_fields.id) as id, MAX(custom_fields.model) as model, MAX(custom_fields.type) as type, MAX(custom_fields.type_data) as type_data, MAX(custom_fields.field_order) as field_order, MAX(custom_fields_languages.customName) as customName,\r\n            value_id = STUFF((SELECT ',' + CAST(cfv.id as varchar) FROM custom_field_values as cfv WHERE cfv.custom_field_id = custom_fields.id AND cfv.recordId = " . $id . "  FOR XML PATH ('')), 1, 1, ''),\r\n            text_value = STUFF((SELECT ',' + cfv1.text_value FROM custom_field_values as cfv1 WHERE cfv1.custom_field_id = custom_fields.id AND cfv1.recordId = " . $id . "  FOR XML PATH ('')), 1, 1, ''), \r\n            MAX(custom_field_values.date_value) as date_value, MAX(custom_field_values.time_value) as time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id\r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $id . "\r\n            LEFT JOIN " . $table . " ON " . $table . ".id = custom_field_values.recordId\r\n            LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n        WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.model = '" . $model . "'\r\n            group by custom_fields.id\r\n            ORDER BY field_order\r\n        ";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
    public function load_legal_case_custom_fields($id, $model, $lang = false, $model_type_id = 0)
    {
        $table = $this->get_model_properties("model", $model, "table");
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT MAX(custom_fields.id) as id, MAX(custom_fields.model) as model, MAX(custom_fields.type) as type, MAX(custom_fields.type_data) as type_data, MAX(custom_fields.field_order) as field_order, MAX(custom_fields_languages.customName) as customName,\r\n            value_id = STUFF((SELECT ',' + CAST(cfv.id as varchar) FROM custom_field_values as cfv WHERE cfv.custom_field_id = custom_fields.id AND cfv.recordId = " . $id . "  FOR XML PATH ('')), 1, 1, ''),\r\n            text_value = STUFF((SELECT ',' + cfv1.text_value FROM custom_field_values as cfv1 WHERE cfv1.custom_field_id = custom_fields.id AND cfv1.recordId = " . $id . "  FOR XML PATH ('')), 1, 1, ''), \r\n            MAX(custom_field_values.date_value) as date_value, MAX(custom_field_values.time_value) as time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id\r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $id . "\r\n            LEFT JOIN " . $table . " ON " . $table . ".id = custom_field_values.recordId\r\n            LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n        WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.model = '" . $model . "'\r\n             AND custom_fields.category LIKE (SELECT ('%'+" . $table . ".category+'%') FROM " . $table . " where " . $table . ".id = " . $id . ")\r\n            AND (\r\n            (custom_fields.id NOT IN (SELECT custom_fields_case_types.custom_field_id from custom_fields_case_types)) \r\n            OR \r\n            ((SELECT " . $table . ".case_type_id  FROM " . $table . " where " . $table . ".id = " . $id . ")IN (SELECT custom_fields_case_types.type_id from custom_fields_case_types WHERE custom_fields_case_types.custom_field_id = custom_fields.id))\r\n            )\r\n            group by custom_fields.id\r\n            ORDER BY field_order";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
    public function load_contract_custom_fields($contract_id, $model, $lang = false, $model_type_id = 0)
    {
        $table = $this->get_model_properties("model", $model, "table");
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT MAX(custom_fields.id) as id, MAX(custom_fields.model) as model, MAX(custom_fields.type) as type, MAX(custom_fields.type_data) as type_data, MAX(custom_fields.field_order) as field_order, MAX(custom_fields_languages.customName) as customName, \r\n            value_id = STUFF((SELECT ',' + CAST(cfv.id as varchar) FROM custom_field_values as cfv WHERE cfv.custom_field_id = custom_fields.id AND cfv.recordId = " . $contract_id . "  FOR XML PATH ('')), 1, 1, ''),\r\n             text_value = STUFF((SELECT ',' + cfv1.text_value FROM custom_field_values as cfv1 WHERE cfv1.custom_field_id = custom_fields.id AND cfv1.recordId = " . $contract_id . "  FOR XML PATH ('')), 1, 1, ''), \r\n             MAX(custom_field_values.date_value) as date_value, MAX(custom_field_values.time_value) as time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id\r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $contract_id . "\r\n            LEFT JOIN " . $table . " ON " . $table . ".id = custom_field_values.recordId " . ($model_type_id ? " LEFT JOIN custom_fields_per_model_types ON custom_fields_per_model_types.custom_field_id = custom_fields.id " : "") . "LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n        WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.model = '" . $table . "' \r\n            AND (" . ($model_type_id ? "custom_fields_per_model_types.type_id = " . $model_type_id . ")" : "(custom_fields.id NOT IN (SELECT custom_fields_per_model_types.custom_field_id from custom_fields_per_model_types)) \r\n            OR \r\n            ((SELECT " . $table . ".type_id  FROM " . $table . " where " . $table . ".id = " . $contract_id . ")IN (SELECT custom_fields_per_model_types.type_id from custom_fields_per_model_types WHERE custom_fields_per_model_types.custom_field_id = custom_fields.id))\r\n            )") . "\r\n            group by custom_fields.id\r\n        ORDER BY field_order";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
    public function load_custom_field($id, $record_id, $lang = false)
    {
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT MAX(custom_fields.id) as id, MAX(custom_fields.model) as model, MAX(custom_fields.type) as type, MAX(custom_fields.type_data) as type_data, MAX(custom_fields.field_order) as field_order, MAX(custom_fields_languages.customName) as customName,\r\n            value_id = STUFF((SELECT ',' + CAST(cfv.id as varchar) FROM custom_field_values as cfv WHERE cfv.custom_field_id = custom_fields.id AND cfv.recordId = " . $record_id . "  FOR XML PATH ('')), 1, 1, ''),\r\n            text_value = STUFF((SELECT ',' + cfv1.text_value FROM custom_field_values as cfv1 WHERE cfv1.custom_field_id = custom_fields.id AND cfv1.recordId = " . $record_id . "  FOR XML PATH ('')), 1, 1, ''),  \r\n            MAX(custom_field_values.date_value) as date_value, MAX(custom_field_values.time_value) as time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id \r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $record_id . "\r\n            LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n            WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.id = '" . $id . "'\r\n            group by custom_fields.id\r\n        ORDER BY field_order";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
    public function return_field_value_relation($id, $category, $types)
    {
        $types = implode(",", $types);
        $query = "\r\n            SELECT COUNT(0) as num_rows\r\n            FROM legal_cases\r\n            INNER JOIN custom_field_values ON custom_field_values.recordId = legal_cases.id AND custom_field_values.custom_field_id = " . $id . "\r\n            WHERE '" . $category . "' LIKE '%'+legal_cases.category+'%' AND legal_cases.case_type_id IN (" . $types . ")\r\n            AND ((text_value IS NOT NULL AND text_value != '') OR (date_value IS NOT NULL) OR (time_value IS NOT NULL AND time_value != ''))\r\n        ";
        $query_result = $this->ci->db->query($query);
        $query_data = $query_result->result_array();
        $num_rows = array_column($query_data, "num_rows");
        return 0 < $num_rows[0] ? false : true;
    }
    public function check_field_relation_per_category_type($id)
    {
        $query = "\r\n            SELECT COUNT(0) as num_rows\r\n            FROM legal_cases\r\n            INNER JOIN custom_fields ON custom_fields.id = " . $id . "\r\n            INNER JOIN custom_field_values ON custom_field_values.recordId = legal_cases.id AND custom_field_values.custom_field_id = " . $id . "\r\n            WHERE custom_fields.category LIKE '%'+legal_cases.category+'%' AND legal_cases.case_type_id IN (SELECT custom_fields_case_types.type_id from custom_fields_case_types WHERE custom_fields_case_types.custom_field_id = custom_fields.id)\r\n            AND ((text_value IS NOT NULL AND text_value != '') OR (date_value IS NOT NULL) OR (time_value IS NOT NULL AND time_value != ''))\r\n        ";
        $query_result = $this->ci->db->query($query);
        $query_data = $query_result->result_array();
        $num_rows = array_column($query_data, "num_rows");
        return 0 < $num_rows[0] ? false : true;
    }
    public function load_custom_fields_visible_to_cp($id, $model, $lang = false)
    {
        $table = $this->get_model_properties("model", $model, "table");
        $lang_code = $lang ? $lang : substr($this->ci->session->userdata("AUTH_language"), 0, 2);
        $query = "SELECT MAX(custom_fields.id) as id, MAX(custom_fields.model) as model, MAX(custom_fields.type) as type, MAX(custom_fields.type_data) as type_data, MAX(custom_fields.field_order) as field_order, MAX(custom_fields_languages.customName) as customName,\r\n            value_id = STUFF((SELECT ',' + CAST(cfv.id as varchar) FROM custom_field_values as cfv WHERE cfv.custom_field_id = custom_fields.id AND cfv.recordId = " . $id . "  FOR XML PATH ('')), 1, 1, ''),\r\n            text_value = STUFF((SELECT ',' + cfv1.text_value FROM custom_field_values as cfv1 WHERE cfv1.custom_field_id = custom_fields.id AND cfv1.recordId = " . $id . "  FOR XML PATH ('')), 1, 1, ''), \r\n            MAX(custom_field_values.date_value) as date_value, MAX(custom_field_values.time_value) as time_value\r\n        FROM custom_fields\r\n            LEFT JOIN custom_fields_languages ON custom_fields_languages.custom_field_id = custom_fields.id\r\n            LEFT JOIN custom_field_values ON custom_field_values.custom_field_id = custom_fields.id AND custom_field_values.recordId = " . $id . "\r\n            LEFT JOIN " . $table . " ON " . $table . ".id = custom_field_values.recordId\r\n            LEFT JOIN languages ON languages.id = custom_fields_languages.language_id\r\n        WHERE languages.name = '" . $lang_code . "'\r\n            AND custom_fields.model = '" . $model . "'\r\n            AND custom_fields.cp_visible = 1\r\n            group by custom_fields.id\r\n            ORDER BY field_order\r\n        ";
        $query_result = $this->ci->db->query($query);
        return $query_result->result_array();
    }
}

?>