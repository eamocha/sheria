<?php


echo "<div id=\"matter-attachments-module-heading\" class=\"d-flex\"\r\n     onclick=\"collapse('matter-attachments-module-heading', 'matter-attachments-module-body', false,'fast','fa-solid fa-angle-down', 'fa-solid fa-angle-right', false, true);\">\r\n    <a href=\"javascript:;\" class=\"toggle-title p-1 pt-3\">\r\n        <i class=\"fa fa-angle-down black_color font-18\">&nbsp;</i>\r\n    </a>\r\n    <h4 class=\"toggle-title p-2\">";
echo $this->lang->line("matter_attachments");
echo "        <i title=\"";
echo $this->lang->line("matter_attachment_helper");
echo "\" class=\"fa fa-question-circle tooltip-title\"></i>\r\n    </h4>\r\n</div>\r\n<div class=\"mod-content attachments-drop-zone dragAndDrop\" id=\"matter-attachments-module-body\">\r\n    ";
echo form_open("", "id=\"matter-attachments-form\" method=\"post\" class=\"form-horizontal\" role=\"form\" accept-charset=\"utf-8\"");
echo "    ";
echo form_input(["id" => "module", "name" => "module", "value" => "case", "type" => "hidden"]);
echo "    ";
echo form_input(["id" => "module-controller", "name" => "module", "value" => "legal_opinions", "type" => "hidden"]);
echo "    ";
echo form_input(["id" => "module-record-id", "name" => "module_record_id", "value" => $opinion_data["legal_case_id"], "type" => "hidden"]);
echo "    ";
echo form_input(["id" => "lineage", "name" => "lineage", "type" => "hidden"]);
echo "    ";
echo form_input(["id" => "term", "name" => "term", "type" => "hidden"]);
echo "    <div class=\"zone-div\">\r\n        <span class=\"zone-text\">\r\n            <i class=\"zone-drop-icon fa-solid fa-upload\"></i>&nbsp;";
echo $this->lang->line("drop_files");
echo " ";
echo $this->lang->line("or");
echo "             <button type=\"button\" class=\"zone-button\">";
echo $this->lang->line("browse");
echo ".</button>\r\n        </span>\r\n    </div>\r\n    ";
echo form_close();
echo "    <ol id=\"attachment_thumbnails\" class=\"item-attachments\"></ol>\r\n</div>";

?>