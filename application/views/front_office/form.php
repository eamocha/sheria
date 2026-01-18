<div class="primary-style">
    <div class="modal fade modal-container modal-resizable show" id="correspondenceModal" tabindex="-1" role="dialog" aria-labelledby="correspondenceModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo form_open_multipart('', ['id' => 'correspondence-form', 'class' => 'form-horizontal']); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="correspondenceModalLabel"><?php echo $this->lang->line("new_correspondence_entry"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div id="CorrespondenceContainer" class="col-md-12 m-0 p-0 padding-10">
                           <!-- Serial Number (Maps to 'reference_number' in DB) -->
                        <div class="form-group row">
                            <label class="control-label reference_number  col-md-3"><?php echo $this->lang->line("ref_number"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_input(['name' => 'reference_number', 'id' => 'reference_number',  'class' => 'form-control', 'value'=>$next_ref_number, 'readonly' => true]); ?>
                                <div data-field="reference_number" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>

                        <!-- Correspondence Category -->
                        <div class="form-group row">
                            <label class="control-label col-md-3 required"><?php echo $this->lang->line("correspondence_category"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_dropdown('category', $category_options, '', 'id="correspondence_category" class="form-control select-picker" required'); ?>
                                <div data-field="category" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>

                        <!-- Correspondence Type -->
                        <div class="form-group row">
                            <label class="control-label col-md-3 required"><?php echo $this->lang->line("correspondence_type"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_dropdown('correspondence_type_id', $type_options, '', 'id="correspondence_type_id" class="form-control select-picker" required'); ?>
                                <div data-field="correspondence_type_id" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>

                        <!-- Subject (Maps to 'subject' in DB) -->
                        <div class="form-group row">
                            <label class="control-label col-md-3 required"><?php echo $this->lang->line("subject"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_input(['name' => 'subject', 'id' => 'subject', 'class' => 'form-control', 'required' => true]); ?>
                                <div data-field="subject" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>
                        <!-- Body (Maps to 'body' in DB) -->
                        <div class="form-group row">
                            <label class="control-label col-md-3 required"><?php echo $this->lang->line("description"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_textarea(['name' => 'body', 'id' => 'body', 'class' => 'form-control', 'rows' => 3]); ?>
                                <div data-field="body" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>
                        <!-- Source/Sender (Maps to 'sender' in DB) -->
                        <div class="form-group col-md-12 p-0 row m-0 mb-10 incoming-fields">
                            <label class="control-label col-md-3 pr-0 col-xs-5 required"><?php echo $this->lang->line("source_sender");?></label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0">
                                    <select name="sender_contact_type" id="sender-type" class="form-control select-picker" tabindex="-1">
                                        <option value="company" <?php //echo isset($selected_values["container_common_fields"]["client_data"]["type"]) ? $selected_values["container_common_fields"]["client_data"]["type"] == "Company" ? "selected='selected'" : "" : "";?>><?php echo $this->lang->line("company_or_group");?></option>
                                        <option value="contact" <?php //echo isset($selected_values["container_common_fields"]["client_data"]["type"]) ? $selected_values["container_common_fields"]["client_data"]["type"] == "Person" ? "selected='selected'" : "" : "";?>><?php echo $this->lang->line("contact");?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group p-0 row m-0 mb-10 incoming-fields">
                            <label class="control-label pr-0 col-md-3 col-xs-12">&nbsp;</label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0">
                                    <?php echo form_input(["name" => "sender", "id" => "sent-by", "value" => $defaultCompany_id ?? "", "type" => "hidden"]);?>
                                    <?php echo form_input(["name" => "sender_name_display", "id" => "lookup-sent-by", "value" => $defaultCompany_name ?? "", "class" => "form-control lookup", 'required' => true, "title" => $this->lang->line("start_typing")]);?>
                                    <div data-field="sender" class="inline-error d-none"></div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 d-flex autocomplete-helper">
                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Addressee (Maps to 'recipient' in DB) -->
                        <div class="form-group col-md-12 p-0 row m-0 mb-10 outgoing-fields">
                            <label class="control-label col-md-3 pr-0 col-xs-5 required"><?php echo $this->lang->line("addressee_recipient");?></label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0">
                                    <select name="recipient_contact_type" id="addressee-type" class="form-control select-picker" tabindex="-1">
                                        <option value="company" <?php //echo isset($selected_values["container_common_fields"]["client_data"]["type"]) ? $selected_values["container_common_fields"]["client_data"]["type"] == "Company" ? "selected='selected'" : "" : "";?>><?php echo $this->lang->line("company_or_group");?></option>
                                        <option value="contact" <?php //echo isset($selected_values["container_common_fields"]["client_data"]["type"]) ? $selected_values["container_common_fields"]["client_data"]["type"] == "Person" ? "selected='selected'" : "" : "";?>><?php echo $this->lang->line("contact");?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group p-0 row m-0 mb-10 outgoing-fields">
                            <label class="control-label pr-0 col-md-3 col-xs-12">&nbsp;</label>
                            <div class="col-md-8 pr-0 col-xs-10">
                                <div class="col-md-12 p-0">
                                    <?php echo form_input(["name" => "recipient", "id" => "addressee_id", "value" => $defaultCompany_id ?? "", "type" => "hidden"]);?>
                                    <?php echo form_input(["name" => "recipient_name_display", "id" => "addressee-lookup", "value" => $defaultCompany_name ?? "", "class" => "form-control lookup", 'required' => true,  "title" => $this->lang->line("start_typing")]);?>
                                    <div data-field="recipient" class="inline-error d-none"></div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 d-flex autocomplete-helper">
                                <div class="col-md-3 pr-0 col-xs-1" >&nbsp;</div>
                                <div class="col-md-8 pr-0 col-xs-10">
                                    <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                                </div>
                            </div>
                        </div>
                        <!-- Document Type (Maps to 'document_type_id' in DB) -->
                        <div class="form-group row">
                            <label class="control-label col-md-3" required="required"><?php echo $this->lang->line("document_type"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_dropdown('document_type_id', $document_type_options, '', 'id="document_type" required class="form-control select-picker"'); ?>
                                <div data-field="document_type_id" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label file_reference_number  col-md-3"><?php echo $this->lang->line("file_reference_number"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_input(['name' => 'file_reference_number', 'id' => 'file_reference_number',  'class' => 'form-control', 'value'=>'']); ?>
                                <div data-field="file_reference_number" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>
                        <!-- Document Date (Maps to 'document_date' in DB) -->
                        <div class="form-group row">
                            <label class="control-label col-md-3 required"><?php echo $this->lang->line("document_date"); ?></label>
                            <div class="col-md-9">
                                <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form-document-date">
                                    <?php echo form_input([
                                        'name' => 'document_date',
                                        'id' => 'document_date',
                                        'class' => 'form-control datepicker',
                                        'required' => true,
                                        'value' => date('Y-m-d')
                                    ]); ?>
                                    <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                </div>
                                <div data-field="document_date" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>
                        <!-- Due Date (Not in provided DB columns, kept as is) -->
                        <div class="form-group row">
                            <label class="control-label col-md-3 required"><?php echo $this->lang->line("due_date"); ?></label>
                            <div class="col-md-9">
                                <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form-due_date">
                                    <?php echo form_input([
                                        'name' => 'due_date',
                                        'id' => 'form-due-date-input',
                                        'class' => 'date start form-control',
                                        'required' => true,
                                        'value' => date('Y-m-d')
                                    ]); ?>
                                    <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                </div>
                                <div data-field="due_date" class="inline-error d-none padding-5"></div>
                                <a href="javascript:;" id="toggle-reminder" class="d-block mt-1">Set reminder</a>
                                <div id="reminder-section" class="form-inline d-none mt-2">
                                    <?php echo form_label('Notify Me Before', 'notify_me_before', ['class' => 'mr-2 mb-0']); ?>
                                    <?php echo form_input(['name' => 'notify_me_before', 'id' => 'notify_me_before', 'class' => 'form-control col-md-2', 'placeholder' => 'e.g. 2']); ?>
                                    <?php echo form_dropdown('notify_unit', ['hours' => 'Hours', 'days' => 'Days', 'weeks' => 'Weeks'], 'days', 'id="notify_unit" class="form-control col-md-3 ml-2"'); ?>
                                </div>
                            </div>
                        </div>
                        <!-- Action Required (Maps to 'action_required' in DB) -->
                        <div class="form-group row">
                            <label class="control-label col-md-3"><?php echo $this->lang->line("action_required"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_dropdown('action_required', $action_required_options, '', 'id="action_required" class="select-picker form-control"'); ?>
                                <div data-field="action_required" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>
                         <div class="form-group row">
                            <label class="control-label col-md-3"><?php echo $this->lang->line("requires_signature"); ?></label>
                            <div class="col-md-9">
                                <?php echo form_dropdown('requires_signature', $signature_options, '', 'id="requires_signature" class="select-picker form-control"'); ?>
                                <div data-field="requires_signature" class="inline-error d-none padding-5"></div>
                            </div>
                        </div>
                          <!-- Assigned To (Maps to 'assigned_to' in DB) -->
                            <div class="col-md-12 p-0 assignee-container">
                                <div class="form-group col-md-12 p-0 row m-0 mb-10" id="assignee-wrapper">
                                    <label class="control-label col-md-3 pr-0 required col-xs-5 restriction-tooltip"  id="assignedToLabelId"><?php echo $this->lang->line("person_to_notify");?> </label>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <div class="input-group col-md-12 p-0 users-lookup-container mb-3" id="assigne-to-id-wrapper">
                                            <?php
                                            echo form_input(["name" => "assigned_to", "id" => "assignedToId", "value" => "", "type" => "hidden"]);
                                            echo form_input(["name" => "assignedToLookUp", "id" => "assignedToLookUp", "value" => "", "class" => "form-control users-lookup", "title" => $this->lang->line("start_typing")]);?>
                                            <span class="input-group-addon bs-caret users-lookup-icon" onclick="jQuery('#assignedToLookUp').focus();"><span class="caret"></span></span>
                                        </div>
                                        <span class="assign-to-me-link-id-wrapper">
                            <a href="javascript:;" id="assignToMeLinkId" onclick="addMe({hidden_id: jQuery('#assignedToId', '#correspondence-form'), lookup_field: jQuery('#assignedToLookUp', '#correspondence-form'), lookup_container: jQuery('.assignee-container', '#correspondence-form'), container: jQuery('#correspondence-form')});"><?php echo $this->lang->line("assign_to_me");?></a>
                        </span>
                                        <div data-field="assigned_to" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>


                        <!-- hide more fields -->
                        <div class="col-md-12 p-0 show-rest-fields">
                            <div class="form-group col-md-12 p-0 row m-0">
                                <div class="col-md-3 pr-0">&nbsp;</div>
                                <div class="col-md-8 pr-0">
                                    <a href="javascript:;" onclick="showMoreFields(jQuery('#correspondence-form'), jQuery('.record_date', '#correspondence-form'));"><?php echo $this->lang->line("more_fields");?></a>
                                </div>
                            </div>
                        </div>

                        <div  id="more-fields" class="d-none container-hidden-fields">
                            <div class="col-md-12 less-field-divider"> <hr>      </div>
                         

                            <!-- Record Date (Not in provided DB columns, kept as is) -->
                            <div class="form-group row">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("bring_up"); ?></label>
                                <div class="col-md-9">
                                    <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form-record_date">
                                        <?php echo form_input(['name' => 'record_date', 'id' => 'record_date', 'value' => date('Y-m-d'), 'class' => 'form-control datepicker record_date']); ?>
                                        <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                    </div>
                                    <div data-field="record_date" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>

                            <!-- Date Received (Maps to 'date_received' in DB) -->
                            <div class="form-group row incoming-fields">
                                <label class="control-label  col-md-3"><?php echo $this->lang->line("date_received"); ?></label>
                                <div class="col-md-9">
                                    <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form_date_recieved">
                                        <?php echo form_input(['name' => 'date_received', 'id' => 'date_received', 'class' => 'form-control datepicker', 'value' => date('Y-m-d')]); ?>
                                        <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                    </div>
                                    <div data-field="date_received" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>

                            <!-- Date Dispatched (Not in provided DB columns, kept as is) -->
                            <div class="form-group row outgoing-fields">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("date_dispatched"); ?></label>
                                <div class="col-md-9">
                                    <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form-date_dispatched">
                                        <?php echo form_input(['name' => 'date_dispatched', 'id' => 'date_dispatched', 'class' => 'form-control datepicker']); ?>
                                        <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                    </div>
                                    <div data-field="date_dispatched" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("status"); ?></label>
                                <div class="col-md-9">
                                    <?php echo form_dropdown('status_id', $status_options, '', 'id="status_id" class="form-control select-picker"'); ?>
                                    <div data-field="status_id" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>

                            <!-- Mode of Receipt (Not in provided DB columns, kept as is) -->
                            <div class="form-group row incoming-fields">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("mode_of_receipt"); ?></label>
                                <div class="col-md-9">
                                    <?php echo form_dropdown('mode_of_receipt', $receipt_modes, '', 'id="mode_of_receipt" class="form-control select-picker"'); ?>
                                    <div data-field="mode_of_receipt" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>

                            <!-- Mode of Dispatch (Maps to 'mode_of_dispatch' in DB) -->
                            <div class="form-group row outgoing-fields">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("mode_of_dispatch"); ?></label>
                                <div class="col-md-9">
                                    <?php echo form_dropdown('mode_of_dispatch', $dispatch_modes, '', 'id="mode_of_dispatch" class="form-control select-picker"'); ?>
                                    <div data-field="mode_of_dispatch" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>

                            <!-- Related To (Not in provided DB columns, kept as is) -->
                            <div class="form-group row">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("related_to"); ?></label>
                                <div class="col-md-9">
                                    <?php echo form_dropdown('related_to_object', $relatedTo_options, '', 'id="related_to_object" class="form-control select-picker"'); ?>
                                    <div data-field="related_to" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>

                          
                            <!-- Priority (Maps to 'priority' in DB) -->
                            <div class="form-group row">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("priority"); ?></label>
                                <div class="col-md-9">
                                    <?php echo form_dropdown('priority', $priority_options, '', 'id="priority" class="form-control select-picker"'); ?>
                                    <div data-field="priority" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>

                            <!-- Notes (Maps to 'comments' in DB) -->
                            <div class="form-group row">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("comments"); ?></label>
                                <div class="col-md-9">
                                    <?php echo form_textarea(['name' => 'comments', 'id' => 'comments', 'class' => 'form-control', 'rows' => 2]); ?>
                                    <div data-field="comments" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>
                         
                            <div class="form-group row">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("instructions"); ?></label>
                                <div class="col-md-9">
                                   <?php echo form_textarea(['name' => 'instructions', 'id' => 'instructions', 'class' => 'form-control', 'rows' => 2]); ?><div data-field="instructions" class="inline-error d-none padding-5"></div>
                                   <div data-field="instructions" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>


                            <!-- Attachments (Maps to 'filename' and 'document_id' in DB, handled separately) -->
                            <div class="form-group row">
                                <label class="control-label col-md-3"><?php echo $this->lang->line("attachments"); ?></label>
                                <div class="col-md-9">
                                    <?php echo form_upload(['name' => 'attachments[]', 'id' => 'attachments', 'class' => 'form-control-file', 'multiple' => true]); ?>
                                    <div data-field="attachments" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>
                        </div> <!-- End of more-fields -->
                        <div class="col-md-12 p-0 hide-rest-fields d-none">
                            <div class="form-group p-0 row m-0 ">
                                <div class="col-md-3 p-0">&nbsp;</div>
                                <div class="col-md-8 p-0">
                                    <a href="javascript:;" onclick="showLessFields(jQuery('#correspondence-form'));"><i class="fa fa-angle-double-up"></i>&nbsp;<?php echo $this->lang->line("less_fields");?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End of CorrespondenceContainer -->

                <div class="modal-footer">
                    <div class="form-group row w-100 px-2">
                        <div class="col-md-12">
                            <label class="d-flex align-items-center">
                                <?php echo form_checkbox('send_notifications_email', '1', true, 'id="send_notifications_email" class="mr-2"'); ?>
                                <?php echo $this->lang->line("notify_user"); ?>
                            </label>
                        </div>
                    </div>
                    <a  href="javascript:;"  id="correspondence-form-submit" class="btn btn-primary"><?php echo $this->lang->line("save"); ?></a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line("cancel"); ?></button>


                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
 
<style>
    .datepicker-dropdown.datepicker-orient-top:after , .datepicker-dropdown.datepicker-orient-left:before{
        bottom: auto !important;
    }
</style>
<script>
    jQuery(function () {
        jQuery('#toggle-reminder').on('click', function () {
            jQuery('#reminder-section').toggleClass('d-none');
        });

        // Hide fields when category is "incoming"
        jQuery('#correspondence_category').on('change', function () {
    var val = jQuery(this).val() ? jQuery(this).val().toLowerCase() : '';
    
    if (val === 'incoming') {
        jQuery('.outgoing-fields').hide().find('input, select').prop('required', false);
        jQuery('.incoming-fields').show().find('input.lookup').prop('required', true);
    } else if (val === 'outgoing') {
        jQuery('.incoming-fields').hide().find('input, select').prop('required', false);
        jQuery('.outgoing-fields').show().find('input.lookup').prop('required', true);
    } else {
        // Show both for General or other categories
        jQuery('.incoming-fields, .outgoing-fields').show();
    }
}).trigger('change');
    });
</script>