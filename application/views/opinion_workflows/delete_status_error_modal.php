<?php

echo "<div class=\"primary-style\">\r\n    <div class=\"modal fade modal-container modal-resizable\">\r\n        <div class=\"modal-dialog\" >\r\n            <div class=\"modal-content\">\r\n                <div class=\"modal-header\">\r\n                    <h4 class=\"modal-title\">";
echo sprintf($this->lang->line("delete_record_failed"), "");
echo "</h4>\r\n                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\r\n                </div>\r\n                <div class=\"modal-body\">\r\n                <h5>";
echo $this->lang->line("status_not_deleted_error");
echo "</h5>\r\n                </div>\r\n                <div class=\"modal-footer\">\r\n                    <button type=\"button\" class=\"close_model no_bg_button pull-right text-align-right\" data-dismiss=\"modal\">";
echo $this->lang->line("cancel");
echo "</button>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </div>\r\n</div>\r\n";

?>