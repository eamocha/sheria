<?php

echo "<div class=\"container-fluid\" id=\"opinion-workflows-management\">\r\n    <div class=\"row\">\r\n        <div class=\"col-md-12\">\r\n            <ul class=\"breadcrumb\">\r\n                <li class=\"breadcrumb-item\"><a href=\"dashboard/admin\">";
echo $this->lang->line("administration");
echo "</a></li>\r\n                <li class=\"breadcrumb-item\">";
echo $this->lang->line("opinion_workflows");
echo "</li>\r\n                <li class=\"breadcrumb-item\"><a href=\"opinion_statuses\">";
echo $this->lang->line("opinion_statuses");
echo "</a></li>\r\n            </ul>\r\n        </div>\r\n    </div>\r\n    <div class=\"row\">\r\n        ";
if (empty($workflows)) {
    echo "            <div class=\"col-md-12\">\r\n                ";
    echo $this->lang->line("there_are_no_workflows");
    echo "                <a onclick=\"workflowForm();\" href=\"javascript:;\" title=\"";
    echo $this->lang->line("click_to_add");
    echo "\"> <i class=\"icon fa fa-plus-circle fa-lg\"></i></a>\r\n            </div>\r\n        ";
} else {
    echo "            <div class=\"col-md-10\">\r\n                <div class=\"margin-bottom\">\r\n                    <label class=\"board-title\">";
    echo $this->lang->line("opinion_workflows");
    echo "&nbsp;&nbsp;</label>\r\n                    <a onclick=\"workflowForm();\" href=\"javascript:;\" title=\"";
    echo $this->lang->line("click_to_add");
    echo "\"> <i class=\"icon fa fa-plus-circle fa-lg\"></i></a>\r\n                </div>\r\n            </div>\r\n            <div class=\"col-md-12\" id=\"tabs\">\r\n                <ul class=\"col-md-2 row\" id=\"tabs-li\">\r\n                    ";
    foreach ($workflows as $workflow) {
        echo "                        <li class=\"col-md-12\">\r\n                            <a href='opinion_workflows/index";
        echo $workflow_id ? "/" . $workflow_id . "#" . $workflow["id"] : "#" . $workflow["id"];
        echo "'>\r\n                                ";
        echo $workflow["name"];
        echo "                            </a>\r\n                        </li>\r\n                    ";
    }
    echo "                </ul>\r\n                <div class=\"col-md-10 col-xs-12 row m-0\">\r\n                    ";
    foreach ($workflows as $workflow) {
        echo "                        <div id=\"";
        echo $workflow["id"];
        echo "\" class=\"col-md-12 no-padding\">\r\n                            <div class=\"col-md-10 no-padding\">\r\n                                ";
        if ($workflow["type"] != "system") {
            echo "                                    <div class=\"control-label col-md-10 no-padding padding-top7\">\r\n                                        <b>";
            echo $this->lang->line("opinion_type");
            echo "</b>:&nbsp;&nbsp;\r\n                                        ";
            echo $workflow["opinion_types_names"];
            echo "                                    </div>\r\n                                ";
        }
        echo "\r\n                            </div>\r\n                            <div class=\"pull-right col-md-2 margin-bottom-5 no-padding-right padding-top7\">\r\n                                <div class=\"dropdown more pull-right\">\r\n                                    <a href=\"\" data-toggle=\"dropdown\" class=\"dropdown-toggle btn btn-default btn-xs\">\r\n                                        <i class=\"fa fa-cog\"></i> <span class=\"caret no-margin\"></span>\r\n                                    </a>\r\n                                    <div aria-labelledby=\"dLabel\" role=\"menu\" class=\"dropdown-menu dropdown-menu-right\">\r\n                                        ";
        if ($workflow["type"] != "system") {
            echo "                                            <a class=\"dropdown-item\" onclick=\"workflowForm('";
            echo $workflow["id"];
            echo "')\" href=\"javascript:;\">";
            echo $this->lang->line("edit_workflow");
            echo "</a>\r\n                                            <a class=\"dropdown-item\" onclick=\"confirmationDialog('confirm_delete_record', {resultHandler: deleteWorkflow, parm: '";
            echo $workflow["id"];
            echo "'})\" href=\"javascript:;\">";
            echo $this->lang->line("delete_workflow");
            echo "</a>\r\n                                        ";
        }
        echo "                                        <a class=\"dropdown-item\" id=\"\" href=\"export/opinion_workflow/";
        echo $workflow["id"];
        echo "\">";
        echo $this->lang->line("export_to_excel");
        echo "</a>\r\n                                    </div>\r\n                                </div>\r\n                            </div>\r\n                            <div class=\"clearfix\">&nbsp;</div>\r\n                            ";
        if (!empty($records[$workflow["id"]]["statuses"])) {
            echo "                                <div class=\"col-md-10 no-padding form-group\">\r\n                                    <a onclick=\"workflowStatusForm('";
            echo $workflow["id"];
            echo "');\" href=\"javascript:;\">\r\n                                        ";
            echo $this->lang->line("add_new_workflow_status");
            echo "                                    </a>\r\n                                </div>\r\n                                <div class=\"col-md-12 no-padding\">\r\n                                    <table class=\"table table-bordered table-striped table-hover\">\r\n                                        <tr>\r\n                                            <th width=\"5%\">&nbsp;</th>\r\n                                            <th>";
            echo $this->lang->line("status");
            echo "</th>\r\n                                            <th>";
            echo $this->lang->line("type");
            echo "</th>\r\n                                            <th>";
            echo $this->lang->line("transitions");
            echo "</th>\r\n                                        </tr>\r\n                                        ";
            foreach ($records[$workflow["id"]]["statuses"] as $status) {
                $is_global = $status["isGlobal"] == 1 ? true : false;
                echo "                                            <tr id=\"workflow-";
                echo $workflow["id"] . "-status-" . $status["id"];
                echo "\">\r\n                                                <td>\r\n                                                    ";
                if ($status["start_point"] == 0) {
                    echo "                                                        <div class=\"dropdown more pull-right\">\r\n                                                            <a href=\"\" data-toggle=\"dropdown\" class=\"dropdown-toggle btn btn-default btn-xs\">\r\n                                                                <i class=\"fa fa-cog\"></i> <span class=\"caret no-margin\"></span>\r\n                                                            </a>\r\n                                                            <div aria-labelledby=\"dLabel\" role=\"menu\" class=\"dropdown-menu dropdown-menu-right\">\r\n                                                                <a class=\"dropdown-item\" href=\"javascript:;\" onclick=\"setAsStartPoint('";
                    echo $workflow["id"];
                    echo "', '";
                    echo $status["id"];
                    echo "');\">\r\n                                                                    ";
                    echo $this->lang->line("set_as_start_point");
                    echo "                                                                </a>\r\n                                                                <a class=\"dropdown-item\" href=\"javascript:;\" onclick=\"confirmationDialog('confirm_delete_opinion_workflow_status', {resultHandler: deleteWorkflowStatus, parm: '";
                    echo $status["id"];
                    echo "'})\">\r\n                                                                    ";
                    echo $this->lang->line("delete");
                    echo "                                                                </a>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    ";
                }
                echo "                                                </td>\r\n                                                <td>";
                echo $status["name"] . (0 < $status["start_point"] ? "&nbsp;(" . $this->lang->line("start_point") . ")" : "");
                echo "&nbsp;</td>\r\n                                                <td> ";
                echo $is_global ? $this->lang->line("global_status") : $this->lang->line("transitional_status");
                echo "</td>\r\n                                                <td> ";
                if (!$is_global) {
                    echo "                                                        <div class=\"col-md-12 no-padding margin-bottom\">\r\n                                                            <a href=\"";
                    echo site_url("opinion_workflows/add_transition/" . $workflow["id"] . "/" . $status["id"]);
                    echo "\" title=\"";
                    echo $this->lang->line("add_transition");
                    echo "\">\r\n                                                                <i class=\"icon fa fa-plus\"></i>\r\n                                                            </a>&nbsp;\r\n                                                            <a href=\"javascript:;\" onclick=\"statusTransitionsViewForm('";
                    echo $status["id"];
                    echo "', '";
                    echo $workflow["id"];
                    echo "');\" title=\"";
                    echo $this->lang->line("view_transitions");
                    echo "\">\r\n                                                                <i class=\"icon fa fa-list\"></i>\r\n                                                            </a>\r\n                                                        </div>\r\n                                                        ";
                }
                if (!empty($records[$workflow["id"]]["transitions"])) {
                    foreach ($records[$workflow["id"]]["transitions"] as $transitions) {
                        if ($transitions["from_step"] === $status["id"]) {
                            echo "                                                                <div class=\"col-md-12 no-padding\" id=\"transition-";
                            echo $transitions["id"];
                            echo "\">\r\n                                                                    <span><b>";
                            echo $transitions["name"];
                            echo "</b>:</span>&nbsp;\r\n                                                                    <span>";
                            echo $transitions["from_step_name"];
                            echo "</span>&nbsp;\r\n                                                                    <span class=\"fa fa-arrow-right\"></span>&nbsp;\r\n                                                                    <span>";
                            echo $transitions["to_status_name"];
                            echo "</span>&nbsp;&nbsp;\r\n                                                                    <span>\r\n                                                                        <a href=\"";
                            echo site_url("opinion_workflows/edit_transition/" . $transitions["id"]);
                            echo "\" title=\"";
                            echo $this->lang->line("edit_transition");
                            echo "\">\r\n                                                                            <i class=\"icon fa fa-edit\"></i>\r\n                                                                        </a>&nbsp;\r\n                                                                        <a href=\"javascript:;\" onclick=\"confirmationDialog('confirm_delete_record', {resultHandler: deleteTransition, parm: ";
                            echo $transitions["id"];
                            echo "} )\" title=\"";
                            echo $this->lang->line("delete_transition");
                            echo "\">\r\n                                                                            <i class=\"icon fa fa-remove\"></i>\r\n                                                                        </a>\r\n                                                                    </span>\r\n                                                                </div>\r\n                                                    ";
                        }
                    }
                }
                echo "                                                </td>\r\n                                            </tr>\r\n                                        ";
            }
            echo "                                    </table>\r\n                                </div>\r\n                            ";
        } else {
            echo "                                <div class='col-md-10 no-padding center'>\r\n                                    ";
            echo $this->lang->line("no_workflow_statuses");
            echo "                                    <a onclick=\"workflowStatusForm('";
            echo $workflow["id"];
            echo "');\" href=\"javascript:;\">";
            echo $this->lang->line("click_to_add");
            echo "</a>\r\n                                </div>\r\n                            ";
        }
        echo "\r\n                        </div>\r\n                    ";
    }
    echo "\r\n\r\n                </div>\r\n            </div>\r\n        ";
}
echo "    </div>\r\n\r\n</div>";

?>