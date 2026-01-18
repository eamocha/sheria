 <div class="modal fade" id="accusedModal" tabindex="-1" role="dialog" aria-labelledby="accusedModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accusedModalLabel">Add/Edit Accused</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="accusedForm">
                        <div class="form-group">
                            <label for="accusedName">Name</label>
                            <input type="text" class="form-control" id="accusedName" name="accusedName" required>
                        </div>
                        <div class="form-group">
                            <label for="accusedAddress">Address</label>
                            <input type="text" class="form-control" id="accusedAddress" name="accusedAddress" required>
                        </div>
                        <!--add attachment -->
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

                       
                        <input type="hidden" id="accusedId" name="accusedId"> <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>