<?php

echo "<div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\">\r\n    <div class=\"modal-dialog\" role=\"document\">\r\n        <div class=\"modal-content\">\r\n            <div class=\"modal-header\">\r\n                <h5 class=\"modal-title\">";
echo $title;
echo "</h5>\r\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">\r\n                    <span aria-hidden=\"true\">&times;</span>\r\n                </button>\r\n            </div>\r\n            <div class=\"modal-body\">\r\n                ";
echo form_open(current_url(), "novalidate id=\"workflow-status-form\" class=\"form-horizontal\"");
echo "                ";
echo form_input(["name" => "workflow_id", "id" => "id", "value" => $workflow_id, "type" => "hidden"]);
echo "                <div class=\"row m-0\">\r\n                    <label class=\"control-label required\">";
echo $this->lang->line("name");
echo "</label>\r\n                    <div class=\"col-8\">\r\n                        ";
echo form_dropdown("status_id", $statuses, "", "id=\"status-id\" class=\"form-control select-picker\" data-live-search=\"true\" data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");
echo "                    </div>\r\n                    <div class=\"col-1 d-flex align-items-end\">\r\n                        <a href=\"javascript:;\" onclick=\"opinionStatusForm('','";
echo $workflow_id;
echo "');\" class=\"btn btn-link\">\r\n                            <i class=\"icon fa fa-plus-circle fa-lg\"> </i>\r\n                        </a>\r\n                    </div>\r\n                    <div data-field=\"status_id\" class=\"inline-error d-none\"></div>\r\n                </div>\r\n                ";
echo form_close();
echo "            </div>\r\n            <div class=\"modal-footer\">\r\n                <span class=\"loader-submit\"></span>\r\n                ";
echo form_button("submitBtn", $this->lang->line("save"), "class=\"btn btn-primary\" id=\"form-submit\"");
echo "                <button type=\"button\" class=\"btn btn-link\" data-dismiss=\"modal\">";
echo $this->lang->line("cancel");
echo "</button>\r\n            </div>\r\n        </div>\r\n    </div>\r\n</div>";

?>