<div class="modal fade" id="witnessModal" tabindex="-1" role="dialog" aria-labelledby="witnessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="witnessModalLabel">Add/Edit Witness</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="witnessForm">
                    <div class="form-group">
                        <label for="witnessName">Action Taken</label>

                        <textarea class="form-control" id="witnessContact" name="witnessContact" rows="2" placeholder="Enter contact details or remarks"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="witnessContact">Remarks</label>
                        <textarea class="form-control" id="witnessRemarks" name="witnessRemarks" rows="3"></textarea>

                    </div>
                    
                     <div class="clear clearfix clearfloat"></div>
                    <hr class="col-md-12 p-0"/>
                    <div class="p-0 row m-0" id="attachments-container">
                        <label class="control-label col-md-3 pr-0 col-xs-5"><i class="fa-solid fa-paperclip"></i>&nbsp;  <?php echo $this->lang->line("attach_file");?></label>
                        <div id="attachments" class="col-md-8 pr-0 col-xs-10 mb-10">
                            <div class="col-md-11">
                                <input id="attachment-0" name="attachment_0" type="file" value="" class="margin-top" />
                            </div>
                            <?php    echo form_input(["name" => "attachments[]", "value" => "attachment_0", "type" => "hidden"]);?>
                        </div>

                    </div>
                    <!-- arrest details, if arrested -->
                    <div class="form-group">
                        <label for="accusedId">Arrest Details if any, including police station/OB</label>
                        <textarea class="form-control" id="arrest_details" name="arrest_details" rows="3"></textarea>
                    </div>
                    <input type="hidden" id="witnessId" name="witnessId">
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>