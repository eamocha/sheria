<?php

$this->load->view("partial/header");
echo "<div class=\"col-md-12\">\r\n    <div class=\"row\">\r\n        <div class=\"col-md-12\">\r\n            <ul class=\"breadcrumb\">\r\n                <li class=\"breadcrumb-item\"><a href=\"dashboard/admin\">";
echo $this->lang->line("administration");
echo "</a></li>\r\n                <li class=\"breadcrumb-item active\">";
echo $this->lang->line("location");
echo "</li>\r\n                <li class=\"breadcrumb-item\"><a href=\"";
echo site_url("opinion_locations/add");
echo "\">";
echo $this->lang->line("add_location");
echo "</a></li>\r\n            </ul>\r\n        </div>\r\n        <div class=\"col-md-12 no-padding\">\r\n            <div class=\"col-md-12 form-group row\" id=\"pagination\"><div class=\"col-md-6 no-padding col-xs-12\"><h4>";
echo $this->lang->line("total_records");
echo ": ";
echo $this->opinion_location->get("paginationTotalRows");
echo "</h4></div>";
$links = $this->opinion_location->get("paginationLinks");
if (!empty($links)) {
    echo "<div class=\"col-md-6 no-padding col-xs-12\" id=\"pagination\"><ul class=\"pagination pull-right\">";
    echo $links;
    echo "</ul></div>";
}
unset($links);
echo "            </div>\r\n\t\t\t";
if (0 < count($records)) {
    echo "\t\t\t\t<div class=\"col-md-12 form-group\">\r\n\t\t\t\t\t<div class=\"dropdown more pull-right margin-right10\">\r\n\t\t\t\t\t\t<a href=\"\" data-toggle=\"dropdown\" class=\"dropdown-toggle btn btn-default btn-xs\">\r\n\t\t\t\t\t\t\t<i class=\"icon fa fa-cog\"></i> <span class=\"caret no-margin\"></span>\r\n\t\t\t\t\t\t</a>\r\n\t\t\t\t\t\t<div aria-labelledby=\"dLabel\" role=\"menu\" class=\"dropdown-menu dropdown-menu-right\">\r\n\t\t\t\t\t\t\t<a class=\"dropdown-item\" href=\"";
    echo site_url("export/opinion_locations");
    echo "\">";
    echo $this->lang->line("export_to_excel");
    echo "</a>\r\n\t\t\t\t\t\t</div>\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</div>\r\n\t            <div class=\"col-md-12 table-responsive\">\r\n\t                <table class=\"table table-bordered table-striped table-hover\">\r\n\t                    <tr>\r\n\t                        <th>";
    echo $this->lang->line("name");
    echo "&nbsp;</th>\r\n\t                        <th>";
    echo $this->lang->line("edit");
    echo "&nbsp;</th>\r\n\t                        <th>";
    echo $this->lang->line("delete");
    echo "&nbsp;</th>\r\n\t                    </tr>\r\n\t\t\t\t\t\t";
    foreach ($records as $record) {
        echo "\t\t\t\t\t\t\t<tr id=\"tl_";
        echo $record["id"];
        echo "\">\r\n\t\t\t\t\t\t\t\t<td>";
        echo $record["name"];
        echo "&nbsp;</td>\r\n\t\t\t\t\t\t\t\t<td><a href=\"";
        echo site_url("opinion_locations/edit/" . $record["id"]);
        echo "\"><i class=\"fa fa-edit fa-lg\"></i></a>&nbsp;</td>\r\n\t\t\t\t\t\t\t\t<td><a href=\"javascript:;\" onclick=\"deleteTaskLocationSelectedRow(";
        echo $record["id"];
        echo ")\"><i class=\"fa fa-trash fa-lg\"></i></a>&nbsp;</td>\r\n\t\t\t\t\t\t\t</tr>\r\n\t\t\t\t\t\t";
    }
    echo "\t                </table>\r\n\t            </div>\r\n\t\t\t";
}
echo "        </div>\r\n    </div>\r\n</div>\r\n";
$this->load->view("partial/footer");

?>