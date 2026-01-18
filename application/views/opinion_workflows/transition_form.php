<?php

echo "<div class=\"container-fluid\" id=\"workflow-transition-container\">\r\n    <div class=\"row\">\r\n        <div class=\"col-md-12\">\r\n            <ul class=\"breadcrumb\">\r\n                <li class=\"breadcrumb-item\"><a href=\"dashboard/admin\">";
echo $this->lang->line("administration");
echo "</a></li>\r\n                <li class=\"breadcrumb-item\"><a href=\"opinion_workflows/index/";
echo $workflow_id;
echo "\">";
echo $this->lang->line("opinion_workflows");
echo "</a> </li>\r\n                <li class=\"breadcrumb-item active\">";
echo $transition["id"] ? $this->lang->line("edit_transition") : $this->lang->line("add_transition");
echo "</li>\r\n            </ul>\r\n        </div>\r\n    </div>\r\n    <div class=\"row\">\r\n        ";
echo form_open("", "novalidate id=\"workflow-transition-form\"");
echo "        ";
echo form_input(["name" => "id", "id" => "id", "value" => $transition["id"] ? $transition["id"] : "", "type" => "hidden"]);
echo "        ";
echo form_input(["name" => "workflow_id", "id" => "workflow-id", "value" => $workflow_id, "type" => "hidden"]);
echo "        ";
echo form_input(["name" => "from_step", "value" => $transition["from_step"], "type" => "hidden"]);
echo "        <div class=\"form-group col-3\">\r\n            <label class=\"control-label required\">";
echo $this->lang->line("name");
echo "</label>\r\n            <div>\r\n                ";
echo form_input(["name" => "name", "id" => "name", "placeholder" => $this->lang->line("name"), "class" => "form-control", "maxlength" => "255", "value" => $transition["name"]]);
echo "                <div data-field=\"name\" class=\"inline-error d-none\"></div>\r\n            </div>\r\n        </div>\r\n        <div class=\"form-group col-3\">\r\n            <label class=\"control-label\">";
echo $this->lang->line("fromStatus");
echo "</label>\r\n            <div>\r\n                ";
echo form_input(["placeholder" => $from_step_name, "class" => "form-control", "maxlength" => "255", "disabled" => true]);
echo "                <div data-field=\"from_step\" class=\"inline-error d-none\"></div>\r\n            </div>\r\n        </div>\r\n        <div class=\"form-group col-md-3\">\r\n            <label class=\"control-label required\">";
echo $this->lang->line("toStatus");
echo "</label>\r\n            <div>\r\n                ";
echo form_dropdown("to_step", $to_steps, $transition["to_step"], "id=\"to-step\" class=\"form-control select-picker\"");
echo "                <div data-field=\"to_step\" class=\"inline-error d-none\"></div>\r\n            </div>\r\n        </div>\r\n        <div class=\"form-group col-md-10\">\r\n            <label class=\"control-label\">";
echo $this->lang->line("description");
echo "</label>\r\n            <div>\r\n                ";
echo form_textarea("comments", $transition["comments"], ["class" => "form-control", "rows" => "3"]);
echo "            </div>\r\n            <div data-field=\"comments\" class=\"inline-error d-none\"></div>\r\n        </div>\r\n        <div class=\"row\" id=\"permissions-container\">\r\n            <div class=\"form-group col-md-12 no-padding\">\r\n                <div class=\"col-md-12\">\r\n                    <h4>";
echo $this->lang->line("permissions");
echo ":</h4>\r\n                </div>\r\n            </div>\r\n            ";
if ($allow_advanced_settings) {
    echo "                <div class=\"form-group col-md-10\">\r\n                    <table class=\"table table-striped permissions-table-header permissions-table\">\r\n                        <tr>\r\n                            <th width=\"50%\">";
    echo $this->lang->line("users");
    echo "</th>\r\n                            <th width=\"50%\">";
    echo $this->lang->line("user_groups");
    echo "</th>\r\n                        </tr>\r\n                        <tr>\r\n                            <th width=\"45%\">\r\n                                <select name=\"permissions[users][]\" placeholder=\"";
    echo $this->lang->line("select_users");
    echo "\" class=\"users-selectized\" multiple=\"multiple\" tabindex=\"-1\">\r\n                                    ";
    if (is_array($users_permitted)) {
        foreach ($users_permitted as $key => $val) {
            echo "                                            <option selected=\"selected\" value=\"";
            echo $val["id"];
            echo "\">";
            echo $val["name"];
            echo "</option>\r\n                                    ";
        }
    }
    echo "                                </select>\r\n                            </th>\r\n                            <th width=\"45%\">\r\n                                <select name=\"permissions[user_groups][]\" placeholder=\"";
    echo $this->lang->line("select_user_groups");
    echo "\" class=\"user-groups-selectized\" multiple=\"multiple\" tabindex=\"-1\">\r\n                                    ";
    if (is_array($user_groups_permitted)) {
        foreach ($user_groups_permitted as $key => $val) {
            echo "                                            <option selected=\"selected\" value=\"";
            echo $val["id"];
            echo "\">";
            echo $val["name"];
            echo "</option>\r\n                                    ";
        }
    }
    echo "                                </select>\r\n                            </th>\r\n                        </tr>\r\n                    </table>\r\n                </div>\r\n            ";
} else {
    echo "                <div class=\"row\">\r\n                    <div class=\"col-md-6\">\r\n                        <div class=\"alert alert-warning margin-top-15\" role=\"alert\">\r\n                            ";
    echo $plan_feature_warning_msg;
    echo "                        </div>\r\n                    </div>\r\n                </div>\r\n            ";
}
echo "        </div>\r\n        ";
if ($allow_advanced_settings) {
    echo "            ";
    $this->load->view("workflows/transition/screen_fields", ["object" => "opinion"]);
    echo "        ";
} else {
    echo "            <div class=\"row\" id=\"permissions-container\">\r\n                <div class=\"form-group col-md-12 no-padding\">\r\n                    <div class=\"col-md-12\">\r\n                        <h4>";
    echo $this->lang->line("screen_workflow");
    echo ":</h4>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            <div class=\"row\">\r\n                <div class=\"col-md-6\">\r\n                    <div class=\"alert alert-warning margin-top-15\" role=\"alert\">\r\n                        ";
    echo $plan_feature_warning_msg;
    echo "                    </div>\r\n                </div>\r\n            </div>\r\n        ";
}
echo "        <div class=\"form-group col-md-12\">\r\n            <span class=\"loader-submit\"></span>\r\n            ";
echo form_button("", $this->lang->line("save"), "class=\"btn btn-default btn-info\" id=\"form-submit\"");
echo "        </div>\r\n        ";
echo form_close();
echo "    </div>\r\n</div>\r\n<script>\r\n    var availableUsers = ";
echo json_encode($users_list);
echo ";\r\n    var availableUserGroups = ";
echo json_encode($user_groups_list);
echo ";\r\n</script>";

?>