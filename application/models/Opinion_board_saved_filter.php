<?php



if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_board_saved_filter extends My_Model
{
    protected $modelName = "opinion_board_saved_filter";
    protected $_table = "opinion_board_saved_filters";
    protected $_listFieldName = "value";
    protected $_fieldsNames = ["id", "boardId", "userId", "keyName", "keyValue"];
    protected $_pk = "id";
    protected $allowedNulls = ["keyValue"];
    protected $validate = [];
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["boardId" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("board"))], "userId" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("user"))], "keyName" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "isUnique" => ["rule" => ["combinedUnique", ["userId"]], "message" => $this->ci->lang->line("already_exists")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]], "keyValue" => ["required" => false, "allowEmpty" => true, "rule" => ["minLength", 3], "message" => sprintf($this->ci->lang->line("min_length_rule"), $this->ci->lang->line("keyValue"), 3)]];
    }
    public function get_saved_filters($boardId)
    {
        $result = [];
        if ($this->ci->is_auth->get_user_id()) {
            $query["where"] = [["opinion_board_saved_filters.userId", $this->ci->is_auth->get_user_id()], ["opinion_board_saved_filters.boardId", $boardId]];
            $result = $this->load_all($query);
        }
        return $result;
    }
    public function save_filters()
    {
        $response = [];
        $formData = $this->ci->input->post(NULL, NULL);
        $boardId = (int) $formData["opinionBoardId"];
        $userId = (int) $this->ci->is_auth->get_user_id();
        $keyName = $formData["savedForm"];
        if (isset($formData["usersList"]) && !is_array($formData["usersList"])) {
            if (!empty($formData["usersList"])) {
                $formData["usersList"] = [$formData["usersList"]];
            } else {
                $formData["usersList"] = [];
            }
        }
        if (isset($formData["casesListValues"]) && !is_array($formData["casesListValues"])) {
            if (!empty($formData["casesListValues"])) {
                $formData["casesListValues"] = [$formData["casesListValues"]];
            } else {
                $formData["casesListValues"] = [];
            }
        }
        if (isset($formData["contractListValues"]) && !is_array($formData["contractListValues"])) {
            if (!empty($formData["contractListValues"])) {
                $formData["contractListValues"] = [$formData["contractListValues"]];
            } else {
                $formData["contractListValues"] = [];
            }
        }
        $keyValue = is_array($formData) ? serialize($formData) : false;
        $fieldsToSave = ["boardId" => $boardId, "userId" => $userId, "keyName" => trim($keyName), "keyValue" => $keyValue];
        $this->set_fields($fieldsToSave);
        $response["result"] = $this->insert();
        $response["validationErrors"] = $this->get("validationErrors");
        if ($response["result"]) {
            $filterId = $this->get_field("id");
            $response["id"] = $filterId;
            $response["keyName"] = $this->get_field("keyName");
        }
        return $response;
    }
    public function delete_single_filter($filterId)
    {
        $query["where"] = [["userId", $this->ci->is_auth->get_user_id()], ["id", $filterId]];
        return $this->delete($query);
    }
    public function delete_all_user_filters($boardId)
    {
        $query["where"] = [["userId", $this->ci->is_auth->get_user_id()], ["boardId", $boardId]];
        return $this->delete($query);
    }
    public function convert_matter_filter($saved_grid_board_filter)
    {
        $board_saved_filter_move_arr = [];
        $board_saved_filter_move_arr["logic"] = "and";
        $board_saved_filter_move_arr["filters"] = [];
        foreach ($saved_grid_board_filter as $opinion_board_field_key => $opinion_board_field) {
            switch ($opinion_board_field_key) {
                case "providerGroupList":
                    $this->save_filter_to_arr($board_saved_filter_move_arr, "provider_groups_users.provider_group_id", "in", $opinion_board_field);
                    break;
                case "providerGroupsList":
                    $this->save_filter_to_arr($board_saved_filter_move_arr, "provider_groups_users.provider_group_id", "in", $opinion_board_field);
                    break;
                case "usersList":
                    if (!empty($opinion_board_field)) {
                        if (!is_array($opinion_board_field)) {
                            $opinion_board_field_temp = $opinion_board_field;
                            $opinion_board_field = [];
                            $opinion_board_field[] = $opinion_board_field_temp;
                        }
                    } else {
                        $opinion_board_field = [];
                    }
                    $this->save_filter_to_arr($board_saved_filter_move_arr, "us.user_id", "in", $opinion_board_field);
                    break;
                case "casesListValues":
                    if (!empty($opinion_board_field)) {
                        if (!is_array($opinion_board_field)) {
                            $opinion_board_field_temp = $opinion_board_field;
                            $opinion_board_field = explode(",", $opinion_board_field_temp);
                        }
                    } else {
                        $opinion_board_field = [];
                    }
                    $this->save_filter_to_arr($board_saved_filter_move_arr, "ta.legal_case_id", "in", $opinion_board_field);
                    break;
                case "contractListValues":
                    if (!empty($opinion_board_field)) {
                        if (!is_array($opinion_board_field)) {
                            $opinion_board_field_temp = $opinion_board_field;
                            $opinion_board_field = explode(",", $opinion_board_field_temp);
                        }
                    } else {
                        $opinion_board_field = [];
                    }
                    $this->save_filter_to_arr($board_saved_filter_move_arr, "ta.contract_id", "in", $opinion_board_field);
                    break;
                case "dueDate":
                    $this->save_filter_to_arr($board_saved_filter_move_arr, "ta.due_date", "cast_gte", $opinion_board_field);
                    break;
                case "createdOn":
                    $this->save_filter_to_arr($board_saved_filter_move_arr, "ta.createdOn", "cast_gte", $opinion_board_field);
                    break;
            }
        }
        return ["gridFilters" => json_encode($board_saved_filter_move_arr)];
    }
    private function save_filter_to_arr(&$board_saved_filter_move_arr, $field, $operator, $value, $function = "")
    {
        if (!empty($value)) {
            $moved_arr = $moved_arr_value = [];
            $moved_arr["filters"] = [];
            $moved_arr_value["field"] = $field;
            $moved_arr_value["operator"] = $operator;
            $moved_arr_value["value"] = $value;
            if ($field == "ta.legal_case_id") {
                $this->ci->load->model("legal_case", "legal_casefactory");
                $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
                $moved_arr_value["field_name"] = "";
                $cases_name = $this->ci->legal_case->get_cases_names_by_ids($value);
                if ($this->ci->legal_case->fetch($value)) {
                }
                $moved_arr_value["field_name"] = implode(", ", array_column($cases_name, "name"));
            }
            if ($field == "us.user_id") {
                $this->ci->load->model("user", "userfactory");
                $this->ci->user = $this->ci->userfactory->get_instance();
                $moved_arr_value["field_name"] = "";
                $users = $this->ci->user->load_users($value);
                if ($users) {
                }
                $moved_arr_value["field_name"] = implode(", ", array_column($users, "name"));
            }
            if (!empty($function)) {
                $moved_arr_value["function"] = $function;
            }
            array_push($moved_arr["filters"], $moved_arr_value);
            array_push($board_saved_filter_move_arr["filters"], $moved_arr);
        }
    }
}

?>