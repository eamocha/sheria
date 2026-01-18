<div class="primary-style">
    <div class="modal fade modal-container modal-resizable" id="recommend-case-closure-modal">
        <div class="modal-dialog recommend-closure-dialog-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="title" class="modal-title"><?php echo $this->lang->line("recommend_case_closure")?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" >
                    <?php echo form_open("", 'name="case_closure_form" id="case_closure_form" method="post" class="form-horizontal"');?>
                    <div id="recommend-case-closure-fields" class="col-md-12 no-margin no-padding">
                        <label class="required margin-bottom-5"><?php echo $this->lang->line("requested_by");?></label>
                        <div class="col-md-12 form-group no-padding">
                            <?php echo form_input(["name" => "closureRequestedBy", "id" => "closure-requested-by-hidden", "value" => $legal_case["requestedBy"], "type" => "hidden"]);?>
                            <?php echo form_input(["name" => "closureRequestedByName", "id" => "lookup-closure-requested-by", "value" => $legal_case["requestedByName"], "class" => "lookup form-control", "title" => $this->lang->line("start_typing")]);?>
                            <div data-field="closureRequestedBy" class="inline-error d-none"></div>
                            <div class="col-md-12 no-padding autocomplete-helper">
                                <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 no-padding clear-right-left-margin margin-bottom">
                            <label class="margin-bottom-5" ><?php echo $this->lang->line("comments");?></label>
                            <div class="col-md-12 no-padding">
                                <?php echo form_textarea(["name" => "comments", "id" => "comments", "class" => "form-control min-height-120 resize-vertical-only", "rows" => "5", "cols" => "0", "value" => ""]);
                                ?>
                                <div data-field="comments" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>
                    <?php form_close();?>
                </div>
                <div class="d-flex modal-footer">
                    <div>
                        <span class="loader-submit"></span>
                        <div class="btn-group">
                           
                            <button name="case_closure_save_button" type="submit" class="save-button btn-info" id="case_closure_save_button"  data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line("recommend_case_closure_tooltip");?>">
                                <?php echo $this->lang->line("change_litigation_stage");?></button>
                        </div>
                    </div>
                    <button type="button" id="close_file_btn" class="btn btn-danger pull-right text-align-right flex-end-item"><?php echo $this->lang->line("close_file");?></button>
                </div><!-- /.modal-footer -->
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<script>
    /** 
     *  * On ready Events
     *  */
    jQuery(document).ready(function () {
        jQuery('[data-toggle="tooltip"]').tooltip();

        var container = jQuery('#recommend-case-closure-fields');
        var lookupDetails = {
            'lookupField': jQuery('#lookup-closure-requested-by', container),
            'errorDiv': 'closureRequestedBy',
            'hiddenId': '#closure-requested-by-hidden',
          //  'resultHandler': recommendCaseClosure.requestedByLookupCallback,
            //'onEraseLookup': recommendCaseClosure.onEraseRequestedByLookup,
            //'onChangeEvent': recommendCaseClosure.onChangeRequestedByLookup
            };
        lookUpContacts(lookupDetails, container);

    });

    /**
     * function to handle saving of the case closure recommendation 
     * onsuccessful saving of the recommendation, run litigationCaseAddForm(null, 'Appeal', <?php echo $current_case_id; ?>);
    */
    jQuery('#case_closure_save_button').on('click', function (e) {
        e.preventDefault();
        var form = jQuery('#case_closure_form');
        var legalCaseId = <?php echo json_encode($legal_case['id']); ?>;
        // Add legalCaseId to the form data before submitting
        if (form.find('input[name="legalCaseId"]').length === 0) {
            form.append('<input type="hidden" name="legalCaseId" value="' + legalCaseId + '">');
        } else {
            form.find('input[name="legalCaseId"]').val(legalCaseId);
        }
        if (form[0].checkValidity()) {
            jQuery('.loader-submit').show();
            // Validate required fields before submitting
            var closureRequestedByName = form.find('input[name="closureRequestedByName"]').val().trim();
            var comments = form.find('textarea[name="comments"]').val().trim();
            var hasError = false;

            // Clear previous errors
            form.find('.inline-error').addClass('d-none').text('');

            if (!closureRequestedByName) {
                form.find('[data-field="closureRequestedBy"]').removeClass('d-none').text('This field is required.');
                hasError = true;
            }
            if (!comments) {
                form.find('[data-field="comments"]').removeClass('d-none').text('This field is required.');
                hasError = true;
            }
            if (hasError) {
                return;
            }

            var serializedData = form.serialize();
            jQuery.ajax({
                url: getBaseURL()+"cases/recommend_case_closure",
                type: "POST",
                data: serializedData,
                success: function (response) {
                    jQuery('.loader-submit').hide();
                    if (response.result) {
                        
                        // Hide and remove the modal completely, including the backdrop
                        jQuery('#recommend-case-closure-modal').modal('hide');
                        jQuery('.modal-backdrop').remove();
                        jQuery('#recommend-case-closure-modal').remove();
                        jQuery('body').removeClass('modal-open').css('padding-right', '');
                        // Run the litigation case add form with the new stage
                        litigationCaseAddForm(null, 'Appeal', <?php echo $legal_case['id']; ?>);
                    } else {
                        // Handle errors
                        alert(response.message);
                    }
                },
                error: function () {
                    jQuery('.loader-submit').hide();
                    alert("An error occurred while processing your request.");
                }
            });
        } else {
            form[0].reportValidity();
        }
    });

    /**
     * function to handle closing the file
     * This function will send an AJAX request to close the file for the legal case.the comments field is required for closing the file.
     * @param {object} e - event object
     * @param {number} legalCaseId - ID of the legal case to close
     * * This function will hide the modal and remove it completely, including the backdrop.
     * * @returns {void}
     * check if the comments field is not empty before closing the file
     * If the comments field is empty, show an alert and do not proceed with closing the file.
     */
    function closeFile(e, legalCaseId) {
        e.preventDefault();
        var comments = jQuery('#comments').val().trim();
        
              
        jQuery('.loader-submit').show();
        jQuery.ajax({
            url: getBaseURL()+"cases/close_file",
            type: "POST",
            data: { legalCaseId: legalCaseId, comments: comments },
            success: function (response) {
                jQuery('.loader-submit').hide();
                if (response.result) {
                    // Hide and remove the modal completely, including the backdrop
                    jQuery('#recommend-case-closure-modal').modal('hide');
                    jQuery('.modal-backdrop').remove();
                    jQuery('#recommend-case-closure-modal').remove();
                    jQuery('body').removeClass('modal-open').css('padding-right', '');
                    // Show success message
                    pinesMessage({ ty: 'success', m: response.message });
                    // Optionally, you can refresh the page or redirect
                    location.reload();

                } else {
                      pinesMessage({ ty: 'error', m: response.message });
                 
                }
            },
            complete: function () {
                jQuery('.loader-submit').hide();
            },
            error: function () {
                jQuery('.loader-submit').hide();
                alert("An error occurred while processing your request.");
            }
        });
    }

    jQuery('#close_file_btn').on('click', function (e) {
        closeFile(e, <?php echo $legal_case['id']; ?>);
    });

</script>
