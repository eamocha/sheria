<?php

echo "<div class=\"primary-style\">\r\n    <div class=\"modal fade modal-container modal-resizable\" data-backdrop=\"false\">\r\n        <div class=\"modal-dialog\">\r\n            <div class=\"modal-content\">\r\n                <div class=\"modal-header\">\r\n                    <h4 class=\"modal-title\">";
echo $this->lang->line("change_opinion_statuses");
echo "</h4>\r\n                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\r\n                </div>\r\n                <div class=\"modal-body\">\r\n                    ";
echo form_open(current_url(), "class=\"form-horizontal\" novalidate id=\"status-migration-form\"");
echo "                    <div class=\"col-md-12\">\r\n                        <p>";
echo sprintf($this->lang->line("statuses_migration"), $this->lang->line("opinion"));
echo " </p>\r\n                    </div>\r\n                    <div class=\"col-md-12 padding-top-10\">\r\n                        <table class=\"table table-bordered table-hover table-striped text-align-left\">\r\n                            <tr>\r\n                                <th>\r\n                                    <div class=\"row m-0 col-md-12 p-0\">\r\n                                        <div class=\"col-md-4\">";
echo $this->lang->line("current_status");
echo "</div>\r\n                                        <div class=\"col-md-2\"></div>\r\n                                        <div class=\"col-md-6\">";
echo $this->lang->line("new_status");
echo "</div>\r\n                                    </div>\r\n                                </th>\r\n                            </tr>\r\n                            ";
echo form_input(["name" => "workflow_id", "value" => $workflow_id, "type" => "hidden"]);
echo "                            ";
foreach ($related_opinions as $data) {
    echo "                                <tr>\r\n                                    <td>\r\n                                        <div class=\"row m-0 col-md-12 p-0\">\r\n                                            <div class=\"col-md-4\">\r\n                                                <h5>";
    echo $data["name"];
    echo " <span class=\"badge badge-light\">";
    echo $data["opinions_count"];
    echo "</span></h5>\r\n                                                ";
    echo form_input(["name" => "old_statuses[]", "value" => $data["status_id"], "type" => "hidden"]);
    echo "                                                ";
    echo form_input(["name" => "type[]", "value" => $data["type_id"], "type" => "hidden"]);
    echo "                                            </div>\r\n                                            <div class=\"col-md-2 padding-top-10\">\r\n                                                <i class=\"purple_color fa-solid fa-arrow-right\"></i>\r\n                                            </div>\r\n                                            <div class=\"col-md-6\">\r\n                                                ";
    echo form_dropdown("new_statuses[]", $statuses, "", "class=\"select-picker\"");
    echo "                                            </div>\r\n                                        </div>\r\n                                    </td>\r\n                                </tr>\r\n                            ";
}
echo "                        </table>\r\n                    </div>\r\n                </div>\r\n                <div class=\"modal-footer\">\r\n                    <div>\r\n                        <span class=\"loader-submit\"></span>\r\n                        <button type=\"button\" class=\"btn btn-save save-button\" id=\"form-submit\">";
echo $this->lang->line("associate");
echo "</button>\r\n                    </div>\r\n                    <button type=\"button\" class=\"btn-group close_model no_bg_button pull-right text-align-right flex-end-item\" data-dismiss=\"modal\">";
echo $this->lang->line("cancel");
echo "</button>\r\n                </div>\r\n                ";
echo form_close();
echo "            </div><!-- /.modal-content -->\r\n        </div><!-- /.modal-dialog -->\r\n    </div><!-- /.modal -->\r\n</div>\r\n";

?>