<?php


echo "<div class=\"primary-style\">
<div id=\"document-editor-modal-inner-container\">
        <div class=\"modal fade modal-container modal-resizable vertically-centered-modal\">
            <div class=\"modal-dialog\">
                <div class=\"modal-content\">
                    <div class=\"modal-header\">
                        <h4 id=\"title\" class=\"modal-title\">";
echo $this->lang->line("inline_edit_installation");
echo "</h4>
                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>
                    </div>
                    <div class=\"modal-body\">
                        <div class=\"col-md-12 no-margin content-container\">
                            <p>
                                ";
echo $this->lang->line("inline_edit_installation_description");
echo "                            </p>
                            <p class=\"installation-steps-title\">
                                ";
echo $this->lang->line("inline_edit_installation_steps_title");
echo "                            </p>
                            <ul>
                                <li>
                                    ";
echo $this->lang->line("inline_edit_installation_step_1");
echo "                                </li>
                                <li>
                                    ";
echo $this->lang->line("inline_edit_installation_step_2");
echo "                                </li>
                                <li>
                                    ";
echo $this->lang->line("inline_edit_installation_step_3");
echo "                                </li>
                            </ul>
                            <p>
                                ";
echo sprintf($this->lang->line("inline_edit_installation_support"), "https://documentation.sheria360.com/display/A4L/sheria360+Document+Editor", "https://collaboration.sheria360.com/servicedesk/customer/portal/4");
echo "                            </p>
                            <div class=\"col-md-12\">
                                <div class=\"alert alert-warning no-margin\">
                                    ";
echo $this->lang->line("already_installed");
echo "                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-body -->
                    <div class=\"modal-footer\">
                        <div>
                            <div class=\"btn-group\">
                                <button type=\"button\" class=\"btn btn-save btn-add-dropdown modal-save-btn\" id=\"install-inline-edit\" onclick=\"updateInlineEditingToolCookie();\">";
echo $this->lang->line("install");
echo "</button>
                            </div>
                        </div>
                        <button type=\"button\" class=\"btn btn-link close_model no_bg_button pull-right text-align-right flex-end-item\" data-dismiss=\"modal\">";
echo $this->lang->line("cancel");
echo "</button>
                    </div><!-- /.modal-footer -->
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
</div>";

?>