<?php



echo "<div id=\"activity-module-heading\" class=\"d-flex\" onclick=\"collapse('activity-module-heading', 'activity-module-body');\">\r\n    <a href=\"javascript:;\" class=\"toggle-title p-1\">\r\n        <i class=\"fa fa-angle-down black_color font-18\">&nbsp;</i>\r\n    </a>\r\n    <h4 class=\"toggle-title px-2\">";
echo $this->lang->line("activity");
echo "</h4>\r\n</div>\r\n<div class=\"mod-content\" id=\"activity-module-body\">\r\n    <div class=\"mod-tabs\">\r\n        <ul class=\"nav nav-tabs\" id=\"opinion-post-filter-tabs\" role=\"tablist\">\r\n            <li class=\"nav-item\" role=\"presentation\">\r\n                <a onclick=\"opinionCommentsList('";
echo $opinion_data["id"];
echo "');\" class=\"nav-link active\" id=\"comment-opinion-view-tab\" data-toggle=\"tab\" href=\"#comments-container\" role=\"tab\" aria-controls=\"comment-opinion-view\" aria-selected=\"true\">";
echo $this->lang->line("comments");
echo "</a>\r\n            </li>\r\n        </ul>\r\n    </div>\r\n    <div id=\"comments-container\" class=\"col-md-12\">\r\n    </div>\r\n</div>";

?>