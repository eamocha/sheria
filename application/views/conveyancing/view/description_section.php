<?php



echo "<div id=\"description-module-heading\" class=\"d-flex\" onclick=\"collapse('description-module-heading', 'description-module-body');\">\r\n    <a href=\"javascript:;\" class=\"toggle-title p-1\" >\r\n        <i class=\"fa fa-angle-down black_color font-18\">&nbsp;</i>\r\n    </a>\r\n    <h4 class=\"toggle-title px-2\">";
echo $this->lang->line("detailed_info");
echo "</h4>\r\n</div>\r\n<div class=\"mod-content\" id=\"description-module-body\">\r\n    <div class=\"col-md-12 long-text tinymce-content\" id=\"description-val\"><p> ";
echo revert_comment_html(strip_tags($opinion_data["detailed_info"], $this->default_allowed_tags));
echo "</p></div>\r\n</div>";

?>