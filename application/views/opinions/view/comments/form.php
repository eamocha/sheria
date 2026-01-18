<?php



echo "<div class=\"primary-style\">\r\n    <div class=\"modal fade modal-container modal-resizable\" data-backdrop=\"false\" id=\"opinion-comment-form\">\r\n        <div class=\"modal-dialog\">\r\n            <div class=\"modal-content\">\r\n                <div class=\"modal-header\">\r\n                    <h4 class=\"modal-title\">";
echo $title;
echo "</h4>\r\n                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\r\n                </div>\r\n                <div class=\"modal-body\">\r\n                    ";
echo form_open(current_url(), "class=\"form-horizontal\" novalidate id=\"comment-form\"");
echo "                    <div class=\"container col-md-12\">\r\n                        <div class=\"col-md-12 no-padding\">\r\n                            ";
echo form_input(["name" => "id", "id" => "comment-id", "value" => $comment["id"], "type" => "hidden"]);
echo "                            ";
echo form_input(["name" => "opinion_id", "id" => "opinion-id", "value" => $comment["opinion_id"], "type" => "hidden"]);
echo "                            ";
echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => isset($hide_show_notification) ? $hide_show_notification : "", "type" => "hidden"]);
echo "                            <div class=\"row form-group m-0\">\r\n                                <label class=\"control-label col-md-3 col-xs-7 no-padding-left required\">";
echo $this->lang->line("comment");
echo "</label>\r\n                                <div class=\"col-md-9 col-xs-12 no-padding-left\">\r\n                                    ";
echo form_textarea("comment", revert_comment_html($comment["comment"]), "id=\"comment\" class=\"form-control  dir=\"auto\" first-input\"");
echo "                                    <div data-field=\"comment\" class=\"inline-error d-none\"></div>\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                    ";
form_close();
echo "                </div>\r\n                <div class=\"modal-footer\">\r\n                    <div>\r\n                        <span class=\"loader-submit\"></span>\r\n                        <button type=\"button\" class=\"btn btn-save modal-save-btn save-button\" id=\"form-submit\">";
echo $this->lang->line("save");
echo "</button>\r\n                    </div>\r\n                    <button type=\"button\" class=\"btn-group close_model no_bg_button pull-right text-align-right flex-end-item\" data-dismiss=\"modal\">";
echo $this->lang->line("cancel");
echo "</button>\r\n                </div>\r\n            </div><!-- /.modal-content -->\r\n        </div><!-- /.modal-dialog -->\r\n    </div><!-- /.modal -->\r\n</div>\r\n<script>\r\n    attachmentCount = 0;\r\n</script>";

?>