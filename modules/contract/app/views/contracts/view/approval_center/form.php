<?php

echo "<div class=\"modal fade modal-container modal-resizable\">\r\n    <div class=\"modal-dialog modal-lg\">\r\n        <div class=\"modal-content\">\r\n            <div class=\"modal-header\">\r\n                <h4 class=\"modal-title\">";
echo htmlspecialchars($title);
echo "</h4>\r\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>\r\n            </div>\r\n            <div class=\"modal-body\">\r\n                <div id=\"approval-form-container\" class=\"col-md-12 no-margin p-0 padding-10\">\r\n                    ";
echo form_open(current_url(), "class=\"form-horizontal\" novalidate id=\"approval-form\"");
echo form_input(["name" => "contract_id", "id" => "contract-id", "type" => "hidden"]);
echo form_input(["name" => "contract_approval_status_id", "id" => "contract-approval-status-id", "type" => "hidden"]);
echo form_input(["name" => "status", "id" => "status", "type" => "hidden"]);
echo "<div >"; echo $this->lang->line("contract_approval_confirm_text1");
echo "</div>             <div class=\"form-group row col-md-12 p-0\" id=\"approval-date-container\">\r\n                       <label class=\"col-form-label text-right col-lg-2 col-md-3 col-xs-4 pr-0 col-xs-5 required\">";
echo $this->lang->line("on");
echo "</label>\r\n                        <div class=\"col-md-9 pr-0 col-xs-10\">\r\n                            <div class=\"input-group date col-md-5 col-xs-8 col-lg-5 p-0 date-picker\"\r\n                                 id=\"approval-date\">\r\n                                ";
echo form_input(["name" => "done_on", "value" => $today, "id" => "approval-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control"]);
echo "                                <div class=\"input-group-append\">\r\n                                    
<span class=\"input-group-text\"><i\r\n                                           
 class=\"fa-solid fa-calendar-days\"></i></span>\r\n                                </div>\r\n                            </div>\r\n                            
 <div data-field=\"done_on\" class=\"inline-error d-none\"></div>\r\n                        </div>\r\n                    </div>\r\n                    
 <div >  ";
echo $this->lang->line("contract_approval_confirm_text2");
echo "</div>
 <div class=\"col-md-12 p-0\">\r\n                        <div class=\"form-group row col-md-12 p-0 col-xs-12\">\r\n                            <label class=\"col-form-label text-right col-lg-2 col-md-3 col-xs-4 pr-0 col-xs-5 ";
echo $approve ? "" : "required";
echo "\">";
echo $this->lang->line("comment");
echo "</label>\r\n                            <div class=\"col-md-9 pr-0\">\r\n                                ";
echo form_textarea(["name" => "comment", "value" => "", "id" => "comment", "class" => "form-control", "rows" => "5", "cols" => "0", "dir" => "auto"]);
echo "                                <div data-field=\"comment\" class=\"inline-error d-none\"></div>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                    <!--                    if the user is rejecting => can choose if he needs to re approve the past approvals-->\r\n                    ";
if (!$approve && isset($previous_ranks) && !empty($previous_ranks)) {
    echo "                        <div class=\"col-md-12 p-0\" id=\"enforce-previous-approvals-container\">\r\n                            <div class=\"form-group row col-md-12 p-0 col-xs-12\">\r\n                                <label class=\"col-form-label text-right col-lg-2 col-md-3 col-xs-4 pr-0 col-xs-5\">";
    echo $this->lang->line("enforce_approval");
    echo "</label>\r\n                                <div class=\"col-md-3 pr-0\">\r\n                                    ";
    echo form_dropdown("enforce_previous_approvals", $previous_ranks, "", "id=\"enforce-previous-approvals\" class=\"form-control select-picker\"");
    echo "                                    <div data-field=\"enforce_previous_approvals\" class=\"inline-error d-none\"></div>\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                    ";
} else {
    echo form_input(["name" => "enforce_previous_approvals", "value" => "0", "id" => "enforce-previous-approvals", "type" => "hidden"]);
}
if ($approve) {
    echo "                        <div class=\"col-md-10 p-0 offset-md-2\">\r\n                            <a href=\"javascript:\"\r\n                               class=\"approve-with-signature-link\"\r\n                               onclick=\"approveWithSignature('#approval-form-container');\"\r\n                               title=\"";
    echo $this->lang->line("approve_with_signature");
    echo "\">";
    echo $this->lang->line("approve_with_signature");
    echo "</a>\r\n                        </div>\r\n                        ";
    if (isset($signatures) && !empty($signatures)) {
        echo "                            <div id=\"signature-rows-container\" class=\"padding-top-20 col-md-12 d-none\">\r\n                                <h5><b>";
        echo $this->lang->line("choose_signature");
        echo "</b></h5>\r\n                                ";
        foreach ($signatures as $signature) {
            echo "                                    <div class=\"col-lg-12 col-sm-12 col-xs-12 p-0 section\">\r\n                                        <div class=\"col-lg-5 col-sm-3 col-xs-12 checkbox label-section\">\r\n                                        <span> ";
            echo form_radio("signature_id", $signature["id"], $signature["is_default"] == "1" ? true : false, "class=\"signature-doc-checkbox float-left\" disabled=\"disabled\"");
            echo "\r\n                                           </span> <span class=\"padding-15\">";
            echo $signature["label"];
            echo "</span>\r\n                                        </div>\r\n                                        <div class=\"col-lg-7 col-sm-9 col-xs-12 file-container\">\r\n                                            ";
            $src = app_url($signature_path . $signature["id"]);
            echo "                                            <img src=\"";
            echo $src;
            echo "\" class=\"thumb-logo\"/>\r\n                                        </div>\r\n                                    </div>\r\n                                ";
        }
        echo "                            </div>\r\n                    ";
    }
}
form_close();
echo "                </div>\r\n            </div><!-- /.modal-body -->\r\n            <div class=\"modal-footer\">\r\n                <span class=\"loader-submit\"></span>\r\n                <button type=\"button\" class=\"btn btn-save\"\r\n                        id=\"form-submit\">";
echo $title;
echo "</button>\r\n                <button type=\"button\" class=\"btn btn-link\"\r\n                        data-dismiss=\"modal\">";
echo $this->lang->line("cancel");
echo "</button>\r\n            </div>\r\n        </div><!-- /.modal-content -->\r\n    </div><!-- /.modal-dialog -->\r\n</div><!-- /.modal -->\r\n";

?>