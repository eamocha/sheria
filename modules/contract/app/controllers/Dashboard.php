<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Dashboard extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
    }
    public function index()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("dashboard"));
        if ($this->input->is_ajax_request()) {
            $response = [];
            $response["expiring_contracts_this_month"] = $this->contract->load_expiring_contracts_this_month();
            $response["expiring_contracts_this_quarter"] = $this->contract->load_expiring_contracts_this_quarter();
            $response["expiring_contracts_next_quarter"] = $this->contract->load_expiring_contracts_next_quarter();
            $response["received_contracts"] = $this->contract->load_received_contracts_this_month();
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            $this->load->model("contract_type_language", "contract_type_languagefactory");
            $this->contract_type_language = $this->contract_type_languagefactory->get_instance();
            $data["contract_types"] = $this->contract_type_language->load_list_per_language();
            $data["contract_types"][""] = $this->lang->line("all");
            $this->includes("contract/dashboard", "js");
            $this->includes("jquery/apexcharts/apexcharts.min", "js");
            $this->includes("jquery/apexcharts/polyfill.min", "js");
            $this->includes("jquery/apexcharts/classlist", "js");


            $this->load->view("partial/header");
            $this->load->view("dashboard/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function pie_charts_widgets()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $filters = $this->input->get("filters");
        $response = [];
        if (isset($filters["contracts_per_status"])) {
            $contracts_per_status = $this->contract->load_contracts_per_status($filters["contracts_per_status"]);
            $response["pie_charts"] = ["index" => $contracts_per_status["statuses"] ?: ["Status"], "values" => $contracts_per_status["values"] ?: [0]];
        }
        if (isset($filters["contracts_per_party"])) {
            $contracts_per_party = $this->contract->load_contracts_per_party($filters["contracts_per_party"]);
            $response["pie_charts"] = ["index" => $contracts_per_party["indexes"] ?: ["Party 1"], "values" => $contracts_per_party["values"] ?: [0]];
        }
        if (isset($filters["contracts_per_department"])) {
            $contracts_per_department = $this->contract->load_contracts_per_department($filters["contracts_per_department"]);
            $response["pie_charts"] = ["index" => $contracts_per_department["indexes"] ?: ["departments"], "values" => $contracts_per_department["values"] ?: [0]];
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function bar_charts_widgets()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
       // $response["bar_charts"]["contracts_per_value"] = $this->contract->load_contracts_per_value();/* this is for currencies*/
        $response["bar_charts"]["contracts_per_value"] = $this->contract->load_contracts_per_month();
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function boards()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_boards"));
        $this->load->model("contract_board_column", "contract_board_columnfactory");
        $this->contract_board_column = $this->contract_board_columnfactory->get_instance();
        $data = [];
        $data["boards"] = $this->contract_board_column->get_board_columns();
        $this->load->view("partial/header");
        $this->load->view("boards/index", $data);
        $this->load->view("partial/footer");
    }
    public function delete_boards()
    {
        $this->load->model("contract_board");
        $this->load->model("contract_board_column", "contract_board_columnfactory");
        $this->contract_board_column = $this->contract_board_columnfactory->get_instance();
        $this->load->model("contract_board_post_filter", "contract_board_post_filterfactory");
        $this->contract_board_post_filter = $this->contract_board_post_filterfactory->get_instance();
        $this->load->model("contract_board_grid_saved_filters_users");
        if ($this->input->is_ajax_request()) {
            $response["result"] = true;
            $board_id = $this->input->post("id");
            $this->load->model("contract_board_column_option");
            if ($this->contract_board_column->delete_related_cols($board_id)) {
                $this->contract_board_post_filter->delete(["where" => ["board_id", $board_id]]);
                $this->contract_board_grid_saved_filters_users->delete(["where" => ["board_id", $board_id]]);
                $this->contract_board->delete(["where" => ["id", $board_id]]);
                delete_cookie($this->is_auth->get_user_id() . "board_id");
            } else {
                if ($this->contract_board_post_filter->delete(["where" => ["board_id", $board_id]])) {
                    $this->contract_board->delete(["where" => ["id", $board_id]]);
                    delete_cookie($this->is_auth->get_user_id() . "board_id");
                } else {
                    if ($this->contract_board_grid_saved_filters_users->delete(["where" => ["board_id", $board_id]])) {
                        $this->contract_board->delete(["where" => ["id", $board_id]]);
                        delete_cookie($this->is_auth->get_user_id() . "board_id");
                    } else {
                        if ($this->contract_board->delete(["where" => ["id", $board_id]])) {
                            delete_cookie($this->is_auth->get_user_id() . "board_id");
                        } else {
                            $response["result"] = false;
                        }
                    }
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function board_config($id = "0")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_board"));
        $this->load->model(["contract_board", "contract_board_column_option"]);
        $this->load->model("contract_board_column", "contract_board_columnfactory");
        $this->contract_board_column = $this->contract_board_columnfactory->get_instance();
        if ($id) {
            $this->contract_board->fetch($id);
            $data["board_name"] = $this->contract_board->get_field("name");
            $data["board_columns"] = $this->contract_board_column->load_all(["where" => ["board_id", $id], "order_by" => ["column_order", "asc"]]);
            $contract_statuses = $this->contract_board_column->load_all_options($id);
            $data["column_options"] = [];
            foreach ($contract_statuses as $contract_status) {
                $data["column_options"][$contract_status["board_column_id"]] = explode("|", $contract_status["status_id"]);
            }
            unset($contract_statuses);
            unset($contract_status);
        }
        $data["board_id"] = $id;
        $this->load->model("contract_workflow", "contract_workflowfactory");
        $this->contract_workflow = $this->contract_workflowfactory->get_instance();
        $this->load->model("contract_status_language", "contract_status_languagefactory");
        $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
        $data["min_columns"] = $this->contract_board_column->get("min_columns");
        $data["max_columns"] = $this->contract_board_column->get("max_columns");
        $data["contract_statuses"] = $this->contract_status_language->load_list_per_language();
        unset($data["contract_statuses"][""]);
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("jquery/spectrum", "js");
        $this->includes("contract/board_config", "js");
        $this->load->view("partial/header");
        $this->load->view("boards/config", $data);
        $this->load->view("partial/footer");
    }
    public function save_board_config()
    {
        $this->load->model(["contract_board", "contract_board_column_option"]);
        $this->load->model("contract_board_column", "contract_board_columnfactory");
        $this->contract_board_column = $this->contract_board_columnfactory->get_instance();
        if ($this->input->post(NULL)) {
            $response = $this->validate_board_config();
            if (isset($response["board"])) {
                $board_id = $response["board"];
                if ($response["result"]) {
                    $post_data = $this->input->post();
                    $this->contract_board_column->delete_related_cols($board_id);
                    foreach ($post_data["columns"] as $cindex => $column) {
                        $this->contract_board_column->reset_fields();
                        $this->contract_board_column->set_field("board_id", $board_id);
                        $this->contract_board_column->set_field("column_order", $cindex);
                        $this->contract_board_column->set_field("name", $column["name"]);
                        $this->contract_board_column->set_field("color", $column["color"]);
                        if ($this->contract_board_column->insert()) {
                            $board_column_id = $this->contract_board_column->get_field("id");
                            foreach ($post_data["options"][$cindex]["status_id"] as $status_id) {
                                $this->contract_board_column_option->reset_fields();
                                $this->contract_board_column_option->set_field("board_column_id", $board_column_id);
                                $this->contract_board_column_option->set_field("status_id", $status_id);
                                $this->contract_board_column_option->insert();
                            }
                        } else {
                            $response["validation_errors"]["columns"][$cindex] = $this->contract_board_column->get("validationErrors");
                        }
                    }
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function validate_board_config()
    {
        $board_post = $this->input->post("board");
        $columns_post = $this->input->post("columns");
        $options_post = $this->input->post("options");
        if ($board_post["id"]) {
            $this->contract_board->fetch($board_post["id"]);
        }
        $this->contract_board->set_fields($board_post);
        if ($this->contract_board->validate()) {
            $result = $board_post["id"] ? $this->contract_board->update() : $this->contract_board->insert();
            $board_id = $this->contract_board->get_field("id");
            $response["board"] = $board_id;
            if ($columns_post && 2 <= count($columns_post)) {
                foreach ($columns_post as $cindex => $column) {
                    $this->contract_board_column->reset_fields();
                    $this->contract_board_column->set_field("board_id", $board_id);
                    $this->contract_board_column->set_field("column_order", $cindex);
                    $this->contract_board_column->set_field("name", $column["name"]);
                    $this->contract_board_column->set_field("color", $column["color"]);
                    if ($this->contract_board_column->validate()) {
                        $board_column_id = $cindex;
                        if (isset($options_post[$cindex]["status_id"])) {
                            foreach ($options_post[$cindex]["status_id"] as $status_id) {
                                $this->contract_board_column_option->reset_fields();
                                $this->contract_board_column_option->set_field("board_column_id", $board_column_id);
                                $this->contract_board_column_option->set_field("status_id", $status_id);
                                if (!$this->contract_board_column_option->validate()) {
                                    $result = false;
                                    $response["validation_errors"]["options"][$cindex] = $this->contract_board_column_option->get("validationErrors");
                                }
                            }
                        } else {
                            $result = false;
                            $response["validation_errors"]["options"][$cindex]["status_id"] = $this->lang->line("cannot_be_blank_rule");
                        }
                    } else {
                        $result = false;
                        $response["validation_errors"]["columns"][$cindex] = $this->contract_board_column->get("validationErrors");
                    }
                }
            } else {
                $result = false;
                $response["display_message"] = $this->lang->line("board_columns_required");
            }
            if (!$result && !$board_post["id"]) {
                $this->db->where("id", $board_id)->delete("contract_boards");
            }
        } else {
            $result = false;
            $response["validation_errors"]["board"] = $this->contract_board->get("validationErrors");
        }
        $response["result"] = $result;
        return $response;
    }
    public function board_post_filters($board_id = "")
    {
        $response["result"] = false;
        if ($board_id) {
            $this->load->model("contract_board_post_filter", "contract_board_post_filterfactory");
            $this->contract_board_post_filter = $this->contract_board_post_filterfactory->get_instance();
            $data["filters"] = $this->contract_board_post_filter->load_all_post_filters($board_id);
            $data["fields_details"] = $this->contract_board_post_filter->load_selected_fields();
            $data["board_id"] = $board_id;
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["contain"] = $this->get_filter_operators("text");
            $response["html"] = $this->load->view("boards/post_filters_list", $data, true);
        } else {
            $response["display_message"] = $this->lang->line("board_required");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_edit_board_post_filters($board_id = "", $filter_id = "")
    {
        $this->load->model("contract_board_column", "contract_board_columnfactory");
        $this->contract_board_column = $this->contract_board_columnfactory->get_instance();
        $this->load->model("contract_board_post_filter", "contract_board_post_filterfactory");
        $this->contract_board_post_filter = $this->contract_board_post_filterfactory->get_instance();
        $data = [];
        $data["fields_details"] = $this->contract_board_post_filter->load_fields();
        $data["fields_data"] = $this->contract_board_post_filter->get("fields_details");
        if ($this->input->post(NULL)) {
            if ($this->input->post("board_id")) {
                $response = $this->save_board_post_filter();
            } else {
                $response["status"] = false;
                $response["display_message"] = $this->lang->line("board_required");
            }
        } else {
            $data["board_id"] = $board_id;
            $data["title"] = $this->lang->line("add_new_post_filter");
            $data["fields_filter"] = $this->contract_board_post_filter->fields_details;
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["contain"] = $this->get_filter_operators("text");
            $data["operator_options"] = [];
            if (!empty($filter_id)) {
                if ($this->contract_board_post_filter->fetch($filter_id)) {
                    $data["contract_post_filters_data"] = $this->contract_board_post_filter->get_fields();
                    $data["title"] = $this->lang->line("edit_new_post_filter");
                    foreach ($data["fields_filter"] as $fields_filter) {
                        if ($fields_filter["db_value"] == $data["contract_post_filters_data"]["field"] && isset($data["operators"][$fields_filter["operator_type"]])) {
                            $data["operator_options"] = $data["operators"][$fields_filter["operator_type"]];
                        }
                    }
                }
                $data["filter_id"] = $filter_id;
            }
            $response["status"] = true;
            $response["html"] = $this->load->view("boards/post_filter_form", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function save_board_post_filter()
    {
        $this->load->model("contract_board_post_filters_user", "contract_board_post_filters_userfactory");
        $this->contract_board_post_filters_user = $this->contract_board_post_filters_userfactory->get_instance();
        $post_data = $this->input->post(NULL);
        if (empty($post_data["post_filter_id"])) {
            $this->contract_board_post_filter->set_fields($post_data);
            if ($this->contract_board_post_filter->insert()) {
                $response["status"] = true;
                $response["display_message"] = sprintf($this->lang->line("record_added_successfull"), $this->lang->line("post_filter"));
            } else {
                $response["status"] = false;
                $response["validation_errors"] = $this->contract_board_post_filter->get("validationErrors");
            }
        } else {
            if ($this->input->post("toggleFilter")) {
                $response["status"] = false;
                if ($post_data["active"] == 0) {
                    $pf_user_data = [];
                    $pf_user_data["user_id"] = $this->is_auth->get_user_id();
                    $pf_user_data["contract_board_post_filter_id"] = $post_data["post_filter_id"];
                    $condition_board = ["contract_board_post_filter_id" => $post_data["post_filter_id"], "user_id" => $this->is_auth->get_user_id()];
                    if (!$this->contract_board_post_filters_user->fetch($condition_board)) {
                        $this->contract_board_post_filters_user->set_fields($pf_user_data);
                        if ($this->contract_board_post_filters_user->insert($pf_user_data)) {
                            $response["status"] = true;
                        }
                    } else {
                        $response["display_message"] = $this->lang->line("already_exists");
                    }
                } else {
                    if ($this->contract_board_post_filters_user->delete_filter_per_user($post_data["post_filter_id"])) {
                        $response["status"] = true;
                    }
                }
            } else {
                if ($this->contract_board_post_filter->fetch($post_data["post_filter_id"])) {
                    $this->contract_board_post_filter->set_fields($post_data);
                    if ($this->contract_board_post_filter->update()) {
                        $response["status"] = true;
                        $response["display_message"] = sprintf($this->lang->line("record_save_successfull"), $this->lang->line("post_filter"));
                    } else {
                        $response["status"] = false;
                        $response["validation_errors"] = $this->contract_board_post_filter->get("validationErrors");
                    }
                } else {
                    $response["status"] = false;
                    $response["display_message"] = $this->lang->line("invalid_record");
                }
            }
        }
        return $response;
    }
    public function delete_board_post_filter($filter_post_id = "")
    {
        $this->load->model("contract_board_post_filter", "contract_board_post_filterfactory");
        $this->contract_board_post_filter = $this->contract_board_post_filterfactory->get_instance();
        $response = [];
        if (!empty($filter_post_id)) {
            $response["status"] = false;
            if ($this->contract_board_post_filter->board_delete_post_filter($filter_post_id)) {
                $response["status"] = true;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function contracts($board_id = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_board"));
        $this->load->model("contract_board_grid_saved_filters_users");
        $this->load->model("contract_board_column", "contract_board_columnfactory");
        $this->contract_board_column = $this->contract_board_columnfactory->get_instance();
        $board_id = !empty($board_id) ? $board_id : $this->input->post("board_id");
        $filter_id = $this->input->post("filter_id");
        if (!empty($board_id)) {
            $this->contract_board_grid_saved_filters_users->set_default_filter($board_id, $this->is_auth->get_user_id(), $filter_id);
        } else {
            $board_id = $this->get_board_id($board_id, $filter_id);
        }
        if ($this->input->is_ajax_request()) {
            $response["status"] = false;
            $action = $this->input->post("action");
            $quick_filter = $this->input->post("quickFilter");
            switch ($action) {
                case "load_columns":
                    $data = $this->get_columns_data($board_id, $this->input->post("filter_id"), $quick_filter);
                    $data["model_code"] = $this->contract->get("modelCode");
                    $response["board_options_columns"] = $data["board_options"]["columns"];
                    $response["board_options"] = $data["board_options"]["contracts"];
                    $response["status"] = true;
                    $response["filter_id"] = $filter_id;
                    $response["html"] = $this->load->view("boards/kanban/list_columns", $data, true);
                    break;
                case "filter":
                    $data = $this->get_filter_data($board_id);
                    $response["status"] = true;
                    $response["filter_id"] = $filter_id;
                    $response["html"] = $this->load->view("boards/kanban/filters_form", $data, true);
                    break;
                default:}

            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
        else {
            $data = $this->get_filter_data($board_id);
            $data["filter_id"] = $filter_id;
            $data["board_id"] = $board_id;
            $this->includes("contract/boards", "js");
            $this->includes("dragula/dragula.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("styles/ltr/fixes", "css");
            $this->includes_footer("dragula/dragula.min", "js");
            $this->includes_footer("dragula/dom-autoscroller.min", "js");
            $this->load->view("partial/header");
            $this->load->view("boards/kanban/index", $data);
            $this->load->view("partial/footer");
        }
    }
    private function get_board_id($board_id, &$filter_id)
    {
        $this->load->model(["contract_board", "provider_group"]);
        $boards_list = $this->contract_board->load_list();
        $default_filter = $this->contract_board_grid_saved_filters_users->get_default_filter($this->is_auth->get_user_id());
        if ($default_filter) {
            if ($this->contract_board->fetch($default_filter["board_id"])) {
                $board_id = $default_filter["board_id"];
                $filter_id = $default_filter["filter_id"];
            }
        } else {
            if (sizeof($boards_list) == 1) {
                $only_board = array_keys($boards_list);
                $board_id = $only_board[0];
            }
        }
        return $board_id;
    }
    private function get_columns_data($board_id, $filter_id, $quick_filter)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_board"));
        $data["board_id"] = $board_id;
        $saved_filters = [];
        if ($this->input->post(NULL) && !empty($filter_id)) {
            $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
            $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
            if ($this->grid_saved_filter->fetch($filter_id)) {
                $gridSavedData = $this->grid_saved_filter->load_data($filter_id);
                $saved_filters = unserialize($gridSavedData["formData"]);
            }
        }
        $data["board_options"] = $this->contract_board_column->get_board_column_options_data($board_id, [], $saved_filters, $quick_filter);
        $data["columns_counts"] = count($data["board_options"]["columns"]);
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
        $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
        $data["possible_transitions"] = $this->contract_workflow_status_transition->load_workflows_transitions();
        return $data;
    }
    private function get_filter_data($board_id)
    {
        $data = [];
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("contract_board_post_filter", "contract_board_post_filterfactory");
        $this->contract_board_post_filter = $this->contract_board_post_filterfactory->get_instance();
        $this->load->model("provider_group");
        $data["filterId"] = "";
        $systemPreferences = $this->session->userdata("systemPreferences");
        $filters = ["providerGroupsList" => [], "usersList" => [], "showList" => [], "contractTypeId" => [], "startDate" => NULL, "endDate" => "", "party_list" => [], "priority" => NULL];
        $data["assigned_teams"] = $this->provider_group->load_list(["where" => ["allUsers !=", 1]]);
        $provider_group_id = !empty($filters["provider_groups_list"]) ? $filters["provider_groups_list"] : NULL;
        $data["users_list"] = $this->user->load_users_list($provider_group_id);
        $data["show_list"] = ["" => $this->lang->line("all") . ": " . $this->lang->line("assigned"), $this->lang->line("only_assigned"), $this->lang->line("only_unassigned")];
        $data["priority"] = ["" => $this->lang->line("priority") . ": " . $this->lang->line("all")] + array_combine($this->contract->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $data["filters"] = $filters;
        $data["board_id"] = $board_id;
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $data["contract_model_code"] = $this->contract->get("modelCode");
        $data["portal_channel"] = $this->contract->get("portalChannel");
        $this->load->model("contract_status_language", "contract_status_languagefactory");
        $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
        $this->load->model("contract_type_language", "contract_type_languagefactory");
        $this->contract_type_language = $this->contract_type_languagefactory->get_instance();
        $data["statuses"] = $this->contract_status_language->load_list_per_language();
        $data["types"] = $this->contract_type_language->load_list_per_language();
        unset($data["types"][""]);
        $this->load->model(["contract_board"]);
        $data["boards_list"] = $this->contract_board->load_list();
        $contract_filter = $this->get_filter_by_model("contract");
        $data["grid_saved_filters"] = $contract_filter["gridSavedFilters"];
        $data["post_filters"] = $this->contract_board_post_filter->load_all_post_filters($board_id);
        return $data;
    }
    private function get_filter_by_model($category)
    {
        $data = [];
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $data["model"] = $category;
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($category, $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($category, $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category, $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category));
        }
        return $data;
    }
    public function board_save_post_filter()
    {
        $this->load->model("contract_board_post_filters_user", "contract_board_post_filters_userfactory");
        $this->contract_board_post_filters_user = $this->contract_board_post_filters_userfactory->get_instance();
        $this->load->model("contract_board_post_filters_user", "contract_board_post_filters_userfactory");
        $this->contract_board_post_filters_user = $this->contract_board_post_filters_userfactory->get_instance();
        $post_data = $this->input->post(NULL);
        if (empty($post_data["post-filter-board-id"])) {
            $this->contract_board_post_filters_user->set_fields($post_data);
            if ($this->contract_board_post_filters_user->insert()) {
                $response["status"] = true;
                $response["message"] = sprintf($this->lang->line("record_added_successfull"), $this->lang->line("post_filter"));
            } else {
                $response["status"] = false;
                $response["validationErrors"] = $this->contract_board_post_filters_user->get("validationErrors");
            }
        } else {
            if ($this->input->post("toggleFilter")) {
                $response["status"] = false;
                if ($post_data["active"] == 0) {
                    $pf_user_data = [];
                    $pf_user_data["user_id"] = $this->is_auth->get_user_id();
                    $pf_user_data["board_post_filters_id"] = $post_data["post-filter-board-id"];
                    $condition_board = ["board_post_filters_id" => $post_data["post-filter-board-id"], "user_id" => $this->is_auth->get_user_id()];
                    if (!$this->contract_board_post_filters_user->fetch($condition_board)) {
                        $this->contract_board_post_filters_user->set_fields($pf_user_data);
                        if ($this->contract_board_post_filters_user->insert($pf_user_data)) {
                            $response["status"] = true;
                        }
                    } else {
                        $response["message"] = $this->lang->line("already_exists");
                    }
                } else {
                    if ($this->contract_board_post_filters_user->delete_filter_per_user($post_data["post-filter-board-id"])) {
                        $response["status"] = true;
                    }
                }
            } else {
                if ($this->contract_board_post_filters_user->fetch($post_data["post-filter-board-id"])) {
                    $this->contract_board_post_filters_user->set_fields($post_data);
                    if ($this->contract_board_post_filters_user->update()) {
                        $response["status"] = true;
                        $response["message"] = sprintf($this->lang->line("record_save_successfull"), $this->lang->line("post_filter"));
                    } else {
                        $response["status"] = false;
                        $response["validationErrors"] = $this->contract_board_post_filters_user->get("validationErrors");
                    }
                } else {
                    $response["status"] = false;
                    $response["message"] = $this->lang->line("invalid_record");
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function contracts_result($board_id = "")
    {
        if ($this->input->post(NULL)) {
            $contract_id = $this->input->post("contract_id");
            $contract_status_id = $this->input->post("new_status");
            $old_status = $this->input->post("old_status");
            if (!empty($contract_id) && !empty($contract_status_id)) {
                $this->load->model("contract_status");
                $this->load->model("contract_status_language", "contract_status_languagefactory");
                $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
                $this->load->model("contract_fields", "contract_fieldsfactory");
                $this->contract_fields = $this->contract_fieldsfactory->get_instance();
                $this->contract->fetch($contract_id);
                $this->contract_fields->load_all_fields($this->contract->get_field("type_id"));
                $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
                $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
                $response = ["result" => true, "display_message" => ""];
                $workflow_applicable = 0 < $this->contract->get_field("workflow_id") ? $this->contract->get_field("workflow_id") : 1;
                $this->contract_workflow_status_transition->fetch(["workflow_id" => $workflow_applicable, "from_step" => $old_status, "to_step" => $contract_status_id]);
                $transition = $this->contract_workflow_status_transition->get_field("id");
                if ($this->input->post("action") == "return_screen") {
                    if (!$this->contract_status->check_transition_allowed($contract_id, $contract_status_id, $this->is_auth->get_user_id())) {
                        $response["result"] = false;
                        $response["display_message"] = $this->lang->line("transition_not_allowed");
                    } else {
                        $data = $this->contract_fields->return_screen_fields($contract_id, $transition);
                        if ($data) {
                            $data["title"] = $this->contract_workflow_status_transition->get_field("name");
                            $response["transition_id"] = $transition;
                            $response["screen_html"] = $this->load->view("templates/screen_fields", $data, true);
                        } else {
                            if (!$this->update_contract_status($contract_id, $contract_status_id, $old_status, $transition)) {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("workflowActionInvalid");
                            }
                        }
                    }
                } else {
                    $validation = $this->contract_fields->validate_fields($this->input->post("transition"));
                    $response["result"] = $validation["result"];
                    if (!$validation["result"]) {
                        $response["validation_errors"] = $validation["errors"];
                    } else {
                        if ($this->update_contract_status($contract_id, $contract_status_id, $old_status, $transition)) {
                            if (!$this->contract_fields->save_fields($contract_id)) {
                                $response["result"] = false;
                                $response["display_message"] = $this->lang->line("records_not_saved");
                            }
                        } else {
                            $response["result"] = false;
                            $response["display_message"] = $this->lang->line("workflowActionInvalid");
                        }
                    }
                }
                $this->load->model("contract_board_column", "contract_board_columnfactory");
                $this->contract_board_column = $this->contract_board_columnfactory->get_instance();
                $quick_filter = $this->input->post("quickFilter");
                $filter_id = $this->input->post("saved_filter_value");
                $filter_id = $filter_id ? $filter_id : "";
                $data["board_id"] = $board_id;
                $saved_filters = [];
                if ($this->input->post(NULL) && !empty($filter_id)) {
                    $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
                    $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
                    if ($this->grid_saved_filter->fetch($filter_id)) {
                        $grid_saved_data = $this->grid_saved_filter->load_data($filter_id);
                        $saved_filters = unserialize($grid_saved_data["formData"]);
                    }
                }
                $response["contractBoardColumnOptions"] = $this->contract_board_column->get_board_column_options_data($board_id, [], $saved_filters, $quick_filter);
                $result = [];
                if (!empty($response["contractBoardColumnOptions"]["contracts"])) {
                    foreach ($response["contractBoardColumnOptions"]["contracts"] as $key => $value) {
                        $result[$key] = sizeof($value);
                    }
                }
                $this->load->model("language");
                $lang_id = $this->language->get_id_by_session_lang();
                $this->contract_status_language->fetch(["status_id" => $contract_status_id, "language_id" => $lang_id]);
                $response["newStatusName"] = $this->contract_status_language->get_field("name");
                $response["contract_id"] = $contract_id;
                $response["data"] = $result;
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
        }
    }
    private function update_contract_status($contract_id = 0, $status_id = 0, $old_status = 0, $transition = 0)
    {
        $this->load->model("contract_sla_management", "contract_sla_managementfactory");
        $this->contract_sla_management = $this->contract_sla_managementfactory->get_instance();
        $this->contract->fetch($contract_id);
        $this->contract->set_field("status_id", $status_id);
        $this->contract->set_field("modifiedByChannel", $this->contract->get("webChannel"));
        if (!$this->contract->update()) {
            return false;
        }
        $this->load->model("approval", "approvalfactory");
        $this->approval = $this->approvalfactory->get_instance();
        $this->approval->workflow_status_approval_events($contract_id, $this->contract->get_field("workflow_id"), $status_id);
        $this->contract_sla_management->contract_sla($contract_id, $this->is_auth->get_user_id());
        $this->notify_users($contract_id, $status_id, $old_status, $transition);
        return true;
    }
    private function notify_users($contract_id, $status_id, $old_status, $transition = 0)
    {
        $this->contract->fetch($contract_id);
        $this->load->model("contract_contributor", "contract_contributorfactory");
        $this->contract_contributor = $this->contract_contributorfactory->get_instance();
        $contributors = $this->contract_contributor->load_contributors($contract_id);
        $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
        $notify["logged_in_user"] = $this->is_auth->get_fullname();
        $this->load->model("language");
        $lang_id = $this->language->get_id_by_session_lang();
        $this->contract_status_language->fetch(["status_id" => $status_id, "language_id" => $lang_id]);
        $new_status_name = $this->contract_status_language->get_field("name");
        $this->contract_status_language->fetch(["status_id" => $old_status, "language_id" => $lang_id]);
        $old_status_name = $this->contract_status_language->get_field("name");
        $this->contract->send_notifications("edit_contract_status", $notify, ["id" => $contract_id, "status" => $new_status_name, "old_status" => $old_status_name]);
        return true;
    }
    public function archiving($board_id = false)
    {
        $data["board_id"] = $board_id;
        $data["title"] = $this->lang->line("archive_hide_contracts");
        $this->load->model("contract_status");
        $this->load->model("contract_status_language", "contract_status_languagefactory");
        $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
        if (!empty($board_id)) {
            $data["statuses_list"] = $this->contract_status_language->load_list_per_language();
            $data["tooltip_hide_contracts_from_board"] = $this->lang->line("tooltip_hide_contracts_from_board");
            $data["tooltip_archive_contracts_from_board"] = $this->lang->line("tooltip_archive_contracts_from_board");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $this->load->model("system_preference");
            $system_preferences = $this->system_preference->get_key_groups();
            $data["archive_contract_status"] = isset($system_preferences["ContractDefaultValues"]["archiveContractStatus"]) && !empty($system_preferences["ContractDefaultValues"]["archiveContractStatus"]) ? explode(",", $system_preferences["ContractDefaultValues"]["archiveContractStatus"]) : [];
            $response["html"] = $this->load->view("boards/kanban/contract_archiving", $data, true);
            $response["status"] = true;
        } else {
            $post_data = $this->input->post();
            if (isset($post_data["archiveAction"]) && !empty($post_data["archiveAction"])) {
                $archive_contract_status_ids = implode(", ", array_values($post_data["formData"]["archiving_status"]));
                $response["result"] = $this->contract->archived_contracts_total_number($archive_contract_status_ids, $post_data["formData"], true, $post_data["formData"]["archiving_type"] === "hide");
                $response["message"] = $response["result"] ? sprintf($this->lang->line("contracts_archived_hidden_successfully"), $post_data["formData"]["archiving_type"] === "hide" ? $this->lang->line("hidden") : $this->lang->line("archived")) : sprintf($this->lang->line("contracts_could_not_be_archived_hidden"), $post_data["formData"]["archiving_type"] === "hide" ? $this->lang->line("hidden") : $this->lang->line("archived"));
            } else {
                if (empty($post_data["archiving_type"])) {
                    $response["validationErrors"]["archiving_type"] = sprintf($this->lang->line("required_rule"), $this->lang->line("type"));
                }
                if (empty($post_data["archiving_status"])) {
                    $response["validationErrors"]["archiving_status"] = sprintf($this->lang->line("required_rule"), $this->lang->line("status"));
                }
                if (empty($response["validationErrors"])) {
                    $response["status"] = true;
                    $archive_contract_status_ids = implode(", ", array_values($post_data["archiving_status"]));
                    $archive_contract_status = $this->contract_status->load_list_statuses_per_ids($archive_contract_status_ids);
                    $archive_contract_status_str = implode(", ", array_values($archive_contract_status));
                    $affected_Rows = $this->contract->archived_contracts_total_number($archive_contract_status_ids, $post_data);
                    $response["affected_Rows"] = $affected_Rows;
                    if (0 < $affected_Rows) {
                        $response["archive_contract_status_message"] = sprintf($this->lang->line("confirmation_message_to_archive_cases_tasks_contracts_affected_rows"), $post_data["archiving_type"] === "hide" ? $this->lang->line("hide") : $this->lang->line("archive"), $this->lang->line("contracts"), $archive_contract_status_str, $affected_Rows);
                    }
                } else {
                    $response["status"] = false;
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}

?>