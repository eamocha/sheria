<?php

echo "<div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\">\r\n  <div class=\"modal-dialog\" role=\"document\">\r\n    <div class=\"modal-content\">\r\n      <div class=\"modal-header\">\r\n        <h5 class=\"modal-title\">";
echo $title;
echo "</h5>\r\n        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">\r\n          <span aria-hidden=\"true\">&times;</span>\r\n        </button>\r\n      </div>\r\n      <div class=\"modal-body\">\r\n        ";
echo form_open(current_url(), "novalidate id=\"opinion-status-form\" class=\"form-horizontal\"");
echo "        ";
echo form_input(["name" => "id", "id" => "id", "value" => $status["id"], "type" => "hidden"]);
echo "        ";
if ($status["id"]) {
    echo "          <div class=\"alert alert-info\">";
    echo $this->lang->line("tooltip_edit_status");
    echo "</div>\r\n        ";
}
echo "        <div class=\"col-md-12 form-group no-padding row\">\r\n          <label class=\"control-label no-padding-right col-md-3 col-xs-12 required\">";
echo $this->lang->line("name");
echo "</label>\r\n          <div class=\"col-md-8 no-padding-right\">\r\n            ";
echo form_input("name", $status["name"], "id=\"field-name\"  class=\"form-control first-input\"");
echo "            <div data-field=\"name\" class=\"inline-error d-none\"></div>\r\n          </div>\r\n        </div>\r\n        <div class=\"col-md-12 form-group no-padding row\">\r\n          <label class=\"control-label no-padding-right col-md-3 col-xs-12 required\">";
echo $this->lang->line("category");
echo "</label>\r\n          <div class=\"col-md-8 no-padding-right\">\r\n            ";
echo form_dropdown("category", ["open" => $this->lang->line("open"), "in progress" => $this->lang->line("in_progress"), "done" => $this->lang->line("done"), "cancelled" => $this->lang->line("cancelled")], $status["category"], "class=\"form-control drop-down\"");
echo "          </div>\r\n        </div>\r\n        <div class=\"col-md-12 form-group no-padding row\">\r\n          <div class=\"col-md-3 no-padding-right\"></div>\r\n          <div class=\"col-md-8 no-padding-right col-xs-10\">\r\n            ";
echo form_input(["name" => "isGlobal", "id" => "is-global", "value" => $status["isGlobal"], "type" => "hidden"]);
echo "            <label> ";
echo form_checkbox("", "", $status["isGlobal"] == 1 ? true : false, "id=\"field-name\" onchange=\"jQuery('#is-global','#opinion-status-container').val(this.checked ? 1 : 0);\" ");
echo "              ";
echo $this->lang->line("global");
echo "</label>\r\n            <span class=\"tooltip-title cursor-pointer-click mr-2 ml-2\" title=\"";
echo $this->lang->line("tooltip_global_status");
echo "\" data-toggle=\"tooltip\"><i class=\"fa fa-question-circle fa-lg\"></i></span>\r\n          </div>\r\n        </div>\r\n        ";
echo form_close();
echo "      </div>\r\n      <div class=\"modal-footer\">\r\n        <span class=\"loader-submit\"></span>\r\n        ";
echo form_button("submitBtn", $this->lang->line("save"), "class=\"btn btn-primary\" id=\"form-submit\"");
echo "        <button type=\"button\" class=\"btn btn-link\" data-dismiss=\"modal\">";
echo $this->lang->line("cancel");
echo "</button>\r\n      </div>\r\n    </div>\r\n  </div>\r\n</div>\r\n<script>\r\n  jQuery('.tooltip-title').tooltipster({\r\n    timer: 22800,\r\n    animation: 'grow',\r\n    delay: 200,\r\n    theme: 'tooltipster-default',\r\n    touchDevices: false,\r\n    trigger: 'hover',\r\n    maxWidth: 350,\r\n    interactive: true\r\n  });\r\n</script>";

?>