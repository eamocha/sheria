<?php



echo "<div id=\"comment-";
echo (int) $comment["id"];
echo "\" class=\"comment-container\">\r\n                <div class=\"comment-head\"> \r\n                <span onclick=\"commentToggle('";
echo $comment["id"];
echo "', '#opinion-display-form')\">\r\n                        <a>\r\n                            <i class=\"fa-solid fa-angle-down mx-2 icon\"></i>\r\n                        </a>\r\n                        <span class=\"aui-avatar-inner\">\r\n                            <img class=\"img-circle\" width=\"30\" src=\"";
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
echo revert_comment_html($comment["comment"]);
echo " \r\n                </div>\r\n            </div>";

?>