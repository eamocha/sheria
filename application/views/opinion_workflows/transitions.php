<?php

echo "<div class=\"col-md-12 workflow-status-transitions\">\r\n    <div class=\"row\">\r\n        <div class=\"col-md-12\">\r\n            <ul class=\"breadcrumb\">\r\n                <li><a href=\"dashboard/admin\">";
echo $this->lang->line("administration");
echo "</a></li>\r\n                <li><a href=\"";
echo site_url("opinion_workflows/statuses/" . $workflowId);
echo "\">";
echo $this->lang->line("opinion_workflows");
echo "</a></li>\r\n                <li class=\"active\">";
echo $this->lang->line("transitions");
echo "</li>\r\n                <li><a href=\"";
echo site_url("opinion_workflows/add_status_transition/" . $workflowId . "/" . $fromStep);
echo "\">";
echo $this->lang->line("add_transition");
echo "</a></li>\r\n            </ul>\r\n        </div>\r\n        ";
if (empty($transitions)) {
    echo "            <div class=\"col-md-12\">\r\n            ";
    echo $this->lang->line("there_are_no_workflow_transitions");
    echo "            </div>\r\n            ";
} else {
    echo "            <div class=\"col-md-12\">\r\n                <div class=\"col-xs-6 no-padding col-md-6 board-title margin-bottom\">";
    echo sprintf($this->lang->line("list_transitions_for"), $fromStepName);
    echo "</div>\r\n            </div>\r\n            <div class=\"col-md-12 table-responsive\">\r\n                <table class=\"table table-bordered table-striped table-hover\">\r\n                    <tr>\r\n                        <th>";
    echo $this->lang->line("name");
    echo "&nbsp;</th>\r\n                        <th>";
    echo $this->lang->line("transition");
    echo "&nbsp;</th>\r\n                        <th>";
    echo $this->lang->line("description");
    echo "&nbsp;</th>\r\n                        <th class=\"col-md-7 no-padding\">\r\n                    <table class=\"table table-striped permissions-table-header permissions-table\">\r\n                        <tr class=\"header-table\">\r\n                            <th colspan=\"2\">";
    echo $this->lang->line("permissions");
    echo "</th>\r\n                            <th width=\"10%\">&nbsp;</th>\r\n                        </tr>\r\n                        <tr>\r\n                            <th width=\"45%\">";
    echo $this->lang->line("users");
    echo "</th>\r\n                            <th width=\"45%\">";
    echo $this->lang->line("user_groups");
    echo "</th>\r\n                            <th width=\"10%\">&nbsp;</th>\r\n                        </tr>\r\n                    </table>\r\n                    </th>\r\n                    <th>";
    echo $this->lang->line("edit");
    echo "&nbsp;</th>\r\n                    <th>";
    echo $this->lang->line("delete");
    echo "&nbsp;</th>\r\n                    </tr>\r\n    ";
    foreach ($transitions as $record) {
        echo "                        <tr>\r\n                            <td class=\"col-md-1 col-xs-2\">";
        echo $record["name"];
        echo "&nbsp;</td>\r\n                            <td class=\"col-md-3\">\r\n                                ";
        if ($this->is_auth->is_layout_rtl()) {
            echo "                                    <div class=\"col-md-12 no-padding\" style=\"direction: ltr;\">\r\n                                        <div class=\"col-md-4 no-padding\">\r\n                                            <span>";
            echo $record["fromStepName"];
            echo "</span>\r\n                                        </div>\r\n                                        <div class=\"col-md-1\">\r\n                                            <span class=\"purple_color fa-solid fa-arrow-left\"></span>\r\n                                        </div>\r\n                                        <div class=\"col-md-6 no-padding\" style=\"margin-right: 12px;\">\r\n                                            <span>";
            echo $record["toStepName"];
            echo "</span>\r\n                                        </div>\r\n                                    </div>\r\n                                ";
        } else {
            echo "                                    <div class=\"col-md-12 no-padding\">\r\n                                        <div class=\"col-md-4 no-padding\">\r\n                                            <span>";
            echo $record["fromStepName"];
            echo "</span>\r\n                                        </div>\r\n                                        <div class=\"col-md-1\">\r\n                                            <span class=\" fa-solid fa-arrow-right\"></span>\r\n                                        </div>\r\n                                        <div class=\"col-md-6 no-padding\" style=\"margin-left: 15px;\">\r\n                                            <span>";
            echo $record["toStepName"];
            echo "</span>\r\n                                        </div>\r\n                                    </div>\r\n        ";
        }
        echo "                            </td>\r\n                            <td class=\"col-md-3 col-xs-2\">";
        echo $record["comments"];
        echo "&nbsp;</td>\r\n                            <td class=\"col-md-6 no-padding\" id=\"transition-";
        echo $record["id"];
        echo "\">\r\n                                <table class=\"table table-striped permissions-table\">\r\n        ";
        echo form_open(current_url(), "class='form-horizontal' novalidate id='transition-" . $record["id"] . "'");
        echo "                                    ";
        echo form_input(["name" => "transition", "value" => $record["id"], "type" => "hidden"]);
        echo "\r\n                                    <tr>\r\n                                        <th width=\"45%\">\r\n                                            <select name=\"users[]\" placeholder=\"";
        echo $this->lang->line("select_users");
        echo "\" class=\"users-selectized\" id='allow-transition-";
        echo $record["id"];
        echo "-to-users' multiple=\"multiple\" tabindex=\"-1\" >\r\n                                                ";
        foreach ($users_permitted[$record["id"]] as $key => $val) {
            echo "                                                    <option selected=\"selected\" value=\"";
            echo $val["id"];
            echo "\">";
            echo $val["name"];
            echo "</option>\r\n            ";
        }
        echo "                                            </select>\r\n                                        </th>\r\n                                        <th width=\"45%\">\r\n                                            <select name=\"user_groups[]\" placeholder=\"";
        echo $this->lang->line("select_user_groups");
        echo "\" class=\"user-groups-selectized\" id='allow-transition-";
        echo $record["id"];
        echo "-to-user-groups' multiple=\"multiple\" tabindex=\"-1\" >\r\n                                                ";
        foreach ($user_groups_permitted[$record["id"]] as $key => $val) {
            echo "                                                    <option selected=\"selected\" value=\"";
            echo $val["id"];
            echo "\">";
            echo $val["name"];
            echo "</option>\r\n            ";
        }
        echo "                                            </select>\r\n                                        </th>\r\n                                        <th><button type=\"button\" class=\"btn btn-save\" onclick=\"allowTransitionTo(jQuery('form#transition-";
        echo $record["id"];
        echo "'), '";
        echo $workflowId;
        echo "');\">";
        echo $this->lang->line("save");
        echo "</button></th>\r\n                                    </tr>\r\n        ";
        echo form_close();
        echo "\r\n                                </table>\r\n                            </td>\r\n                            <td class=\"padding-top\"><a href=\"";
        echo site_url("opinion_workflows/edit_status_transition/" . $record["id"]);
        echo "\">";
        echo $this->lang->line("edit");
        echo "</a>&nbsp;</td>\r\n                            <td class=\"padding-top\"><a href=\"javascript:;\" onclick=\"confirmationDialog('confirm_delete_record', {resultHandler: deleteTransitionStatus, parm: ";
        echo $record["id"];
        echo "} )\">";
        echo $this->lang->line("delete");
        echo "</a>&nbsp;</td>\r\n                        </tr>\r\n    ";
    }
    echo "                </table>\r\n            </div>\r\n            ";
}
echo "    </div>\r\n</div>\r\n<script>\r\n    var availableUsers = ";
echo json_encode($users_list);
echo ";\r\n    var availableUserGroups = ";
echo json_encode($user_groups_list);
echo ";\r\n    var fromStep = '";
echo $fromStep;
echo "'\r\n    initializeSelectPermissions();\r\n</script>";

?>