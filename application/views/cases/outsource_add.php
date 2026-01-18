<div class="primary-style">
    <div id="outsource-modal" class="modal fade modal-container " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="title" class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal" >×</button>
                    </div><!-- /.modal-header -->
                    <div class="modal-body "><!-- /.modal-body -->
                       <?php 
                       echo form_open("", 'id="outsource-add-form" name="outsource-add-form" method="post" class="form-horizontal "' );
                       echo form_input(["name" => "action", "value" => "addOutsource", "type" => "hidden"]);
                       echo form_input(["value" => $id, "name" => "case_id", "id" => "caseId", "type" => "hidden"]);
                       echo form_input(["value" => "external lawyer", "name" => "outsource_relation_type", "id" => "outsource-relation-type", "type" => "hidden"]);
                       echo form_input(["value" => "", "name" => "roleChanged_OnTheFly", "class" => "roleChanged_OnTheFly", "type" => "hidden"]);
                       echo form_input(["name" => "legal_case_outsource_id", "id" => "legal-case-outsource-id", "type" => "hidden"]);
                       ?>
                        <div class="col-md-12 form-group no-padding">
                            <label class="required margin-bottom-5">
                                <?php echo $this->lang->line("company");?>
                            </label>
                            <?php echo form_input(["id" => "outsource-company-lookup", "data-validation-engine" => "validate[required]", "class" => "form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing"), "autocomplete" => "off"]);
                            echo form_input(["id" => "outsource-company-id", "name" => "outsource_company_id", "type" => "hidden"]);?>
                        </div>
                        <div class="col-md-12 form-group no-padding">
                            <label class="required margin-bottom-5">
                                <?php echo $this->lang->line("external_advisors");?>
                            </label>
                            <?php echo form_input(["id" => "outsource-company-contacts-lookup", "name" => "outsource_company_contacts", "data-validation-engine" => "validate[required]", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]);
                            ?>
                        </div>
                        <?php if (!$sharedDocumentsWithAdvisors) { ?>
                        <div id="share-documents-with-advisors" class="col-md-12 form-group no-padding"> 
                        <?php echo form_checkbox("share_documents_with_outsource", 1, true, 'class="inline"' );?>
                            <label class="margin-bottom-5 v-align-top"><?php    echo $this->lang->line("share_documents_with_outsource");
                            ?>
                            </label>
                            <span>
                                <span id="icon-alignment outsource-lookup-tooltip" title="<?php    echo $this->lang->line("share_documents_with_outsource_note");?>" class="tooltip-title purple_color"><i class="fa-solid fa-circle-question purple_color"></i></span>
                            </span>
                        </div><?php }?>
                        <!-- OLD BELOW -->
                        <?php echo form_close();?>
                    </div><!-- /.modal-body -->
                    <div class="d-flex modal-footer"><!-- /.modal-footer -->
                        <div>
                            <span class="loader-submit"></span>
                            <div class="btn-group">
                                <button id="outsource-dialog-save" type="button" class="save-button-blue btn-info"><?php echo $this->lang->line("save");?></button>
                            </div>
                        </div>
                        <button type="button" class="close_model no_bg_button pull-right text-align-right flex-end-item" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                    </div><!-- /.modal-footer -->
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<script>
    var container = jQuery("#outsource-modal");
        // jQuery('#legal_case_contact_role_id', container).selectpicker();
        // jQuery('#legal_case_company_role_id', container).selectpicker();
        // jQuery('#outsource-category-id', container).selectpicker();
        jQuery('.tooltip-title').tooltipster({
            contentAsHTML: true,
            timer: 22800,
            animation: 'grow',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
            maxWidth: 400,
            interactive: true,
        });
    </script>