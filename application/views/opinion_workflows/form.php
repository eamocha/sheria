<?php

echo "<script>\r\n    var availableTasks = ";
echo json_encode($opinion_types);
echo ";\r\n    var disabledOptions = ";
echo json_encode($workflows_types);
echo ";\r\n    jQuery(document).ready(function() {\r\n        jQuery('.selectize-type').selectize({\r\n            plugins: ['remove_button'],\r\n            valueField: 'id',\r\n            labelField: 'name',\r\n            searchField: ['name'],\r\n            options: availableTasks,\r\n            createOnBlur: true,\r\n            groups: [],\r\n            optgroupField: 'class',\r\n            render: {\r\n                option: function(item, escape) {\r\n                    if (jQuery.inArray(item.id, disabledOptions) !== -1) {\r\n                        return '<div style=\"pointer-events: none; color: #aaaaaa;\">' + escape(item.name) + '</div>';\r\n                    }\r\n                    return '<div>' + escape(item.name) + '</div>';\r\n                }\r\n            }\r\n        });\r\n    });\r\n</script>\r\n<div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\">\r\n    <div class=\"modal-dialog\" role=\"document\">\r\n        <div class=\"modal-content\">\r\n            <div class=\"modal-header\">\r\n                <h5 class=\"modal-title\">";
echo $title;
echo "</h5>\r\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">\r\n                    <span aria-hidden=\"true\">&times;</span>\r\n                </button>\r\n            </div>\r\n            <div class=\"modal-body\">\r\n                <div class=\"col-md-12\">\r\n                    ";
echo form_open(current_url(), "class=\"form-horizontal\" novalidate id=\"workflow-form\"");
echo "                    ";
echo form_input(["name" => "id", "id" => "id", "value" => $workflow["id"], "type" => "hidden"]);
echo "                    <div class=\"row\">\r\n                        <div class=\"form-group col-md-12 row\">\r\n                            <label class=\"control-label col-md-3 no-padding-right required padding-5\">";
echo $this->lang->line("name");
echo "</label>\r\n                            <div class=\"col-md-8\">\r\n                                ";
echo form_input("name", $workflow["name"], "id=\"name\" class=\"form-control first-input\"");
echo "                                <div data-field=\"name\" class=\"inline-error d-none\"></div>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"row\">\r\n                        <div class=\"form-group col-md-12 row\">\r\n                            <label class=\"control-label col-md-3 no-padding-right required padding-5\">";
echo $this->lang->line("opinion_type");
echo "</label>\r\n                            <div class=\"col-md-8\">\r\n                                <select name=\"opinion_type[]\" placeholder=\"";
echo $this->lang->line("select");
echo "\" class=\"selectize-type\" id='workflow_category' multiple=\"multiple\" tabindex=\"-1\">\r\n                                    ";
if (isset($opinion_types) && isset($selected_types) && !empty($selected_types)) {
    foreach ($opinion_types as $type) {
        if (in_array($type["id"], $selected_types)) {
            echo "                                                <option selected=\"selected\" value=\"";
            echo $type["id"];
            echo "\">";
            echo $type["name"];
            echo "</option>\r\n                                    ";
        }
    }
}
echo "                                </select>\r\n                                <div data-field=\"opinion_type\" class=\"inline-error d-none\"></div>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n                ";
echo form_close();
echo "            </div>\r\n            <div class=\"modal-footer\">\r\n                <span class=\"loader-submit\"></span>\r\n                ";
echo form_button("submitBtn", $this->lang->line("save"), "class=\"btn btn-primary\" id=\"form-submit\"");
echo "                <button type=\"button\" class=\"btn btn-light\" data-dismiss=\"modal\">";
echo $this->lang->line("cancel");
echo "</button>\r\n            </div>\r\n        </div>\r\n    </div>\r\n</div>";

?>