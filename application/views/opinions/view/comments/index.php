<?php

echo "<div class=\"comments-content\">\r\n    ";
if (!empty($comments["records"])) {
    $visible_comments_count = count($comments["records"]);
    if ($visible_comments_count < $comments["count"]) {
        $older_comments_count = $comments["count"] - $visible_comments_count;
        if (0 < $older_comments_count) {
            echo "                <div class=\"message-container\">\r\n                    <a class=\"collapsed-comments\" onclick=\"opinionCommentsList('";
            echo $id;
            echo "', true);\">\r\n                        <span class=\"collapsed-comments-line\"></span>\r\n                        <span class=\"collapsed-comments-line\"></span>\r\n                        <span class=\"show-more-comments\">";
            echo $older_comments_count;
            echo " ";
            echo $this->lang->line("older_comments");
            echo "</span>\r\n                    </a>\r\n                </div>\r\n                ";
        }
    }
    foreach ($comments["records"] as $comment) {
        echo "            <div id=\"comment-";
        echo (int) $comment["id"];
        echo "\" class=\"comment-container\">\r\n                <div class=\"comment-head\">\r\n                    <span onclick=\"commentToggle('";
        echo $comment["id"];
        echo "', '#opinion-display-form')\">\r\n                        <a>\r\n                            <i class=\"fa fa-angle-down black_color font-18\"></i>\r\n                        </a>\r\n                        <span class=\"aui-avatar-inner\">\r\n                            <img class=\"img-circle\" width=\"30\" src=\"";
        echo "users/get_profile_picture/" . $comment["createdBy"] . "/1";
        echo "\" >\r\n                        </span>\r\n                        <span>";
        echo htmlentities($comment["created_by_name"]);
        echo " ";
        echo $this->lang->line("addedCommentOn");
        echo " ";
        echo $comment["createdOn"];
        echo "</span>\r\n                        ";
        if ($comment["edited"]) {
            echo "- <span class=\"label-red\">";
            echo $this->lang->line("edited");
            echo "</span> ";
        }
        echo "                    </span>\r\n                    <span class=\"pull-right\">\r\n                        <a href=\"javascript:;\" class=\"btn btn-link btn-sm no-padding\" onclick=\"opinionCommentForm('";
        echo $comment["opinion_id"];
        echo "', '";
        echo $comment["id"];
        echo "');\" title=\"";
        echo $this->lang->line("edit");
        echo "\">\r\n                            <i class=\"fa-solid fa-pen-to-square glyphicon-unmodified\"></i>\r\n                        </a>\r\n                        <a href=\"javascript:;\" class=\"btn btn-link btn-sm no-padding\" onclick=\"confirmationDialog('confirmation_delete_comment', {resultHandler: commentDelete, parm: {'id': '";
        echo $comment["id"];
        echo "'}});\" title=\"";
        echo $this->lang->line("delete");
        echo "\">\r\n                            <i class=\"fa-solid fa-trash-can glyphicon-unmodified red\"></i>\r\n                        </a>\r\n                    </span>\r\n                </div>\r\n                <div class=\"comment-body\">\r\n                    ";
        echo revert_comment_html(strip_tags($comment["comment"], $this->default_allowed_tags));
        echo "                </div>\r\n            </div>\r\n            ";
    }
    echo "        ";
} else {
    echo "        <span id=\"no-comments\">\r\n        ";
    echo $this->lang->line("noComments");
    echo "        </span>\r\n    ";
}
echo "</div>\r\n<div id=\"add-comment\" class=\"row d-none\">\r\n    <div class=\"container col-md-12\">\r\n        <div class=\"col-md-12 padding-top\">\r\n            <h4>";
echo $this->lang->line("comment");
echo "</h4>\r\n            ";
echo form_open(current_url(), "class=\"form-horizontal\" novalidate id=\"comment-form\"");
echo "            ";
echo form_input(["type" => "hidden", "name" => "opinion_id", "id" => "opinion-id", "value" => $id]);
echo "            ";
echo form_input(["name" => "send_notifications_email", "type" => "hidden", "id" => "send_notifications_email", "value" => $hide_show_notification]);
echo "            ";
echo form_textarea("comment", "", "id=\"comment\" class=\"form-control first-input\"  dir=\"auto\" ");
echo "            <div class=\"d-flex col-md-12 footer-comment\">\r\n                    ";
$this->load->view("templates/send_email_option_template", ["container" => "#comment-form", "hide_show_notification" => $hide_show_notification]);
echo "                    <span class=\"loader-submit\"></span>\r\n                    <div class=\"flex-end-item\">\r\n                        <button type=\"button\" class=\"btn btn-link pull-right\"  onclick=\"opinionCommentFormInline('',true);\" id=\"dismiss-comment\">";
echo $this->lang->line("cancel");
echo "</button>\r\n                        <button type=\"button\" class=\"btn pull-right  modal-save-btn \" disabled=\"disabled\" onclick=\"opinionCommentSubmit(jQuery('#add-comment'));\" id=\"save-comment\">";
echo $this->lang->line("add");
echo "</button>\r\n                    </div>\r\n            </div>\r\n            <div data-field=\"comment\" class=\"inline-error d-none\"></div>\r\n           ";
form_close();
echo "        </div>\r\n    </div>\r\n</div>";

?>