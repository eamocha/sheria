<div class="primary-style">
    <div class="modal fade modal-container show" id="correspondenceRelationshipModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <?php echo form_open('correspondence/save_relationship', ['id' => 'relationship-form', 'class' => 'form-horizontal']); ?>
                
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $this->lang->line("link_correspondence"); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="col-md-12 padding-10">
                        
                        <?php echo form_input(['name' => 'base_correspondence_id', 'type' => 'hidden', 'value' => $current_id]); ?>

                        <div class="form-group row">
                            <label class="control-label col-md-4 required"><?php echo $this->lang->line("select_correspondence"); ?></label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <?php 
                                        echo form_input(["name" => "related_correspondence_id", "id" => "relatedCorresId", "type" => "hidden", "required" => true]);
                                        echo form_input(["name" => "relatedCorresLookUp", "id" => "relatedCorresLookUp", "class" => "form-control correspondence-lookup", "placeholder" => "Search by Ref or Subject..."]);
                                    ?>
                                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                </div>
                                <div data-field="related_correspondence_id" class="inline-error d-none"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-4 required"><?php echo $this->lang->line("relationship_type"); ?></label>
                            <div class="col-md-8">
                                <?php 
                                    $rel_options = [
                                        '' => '--- Select Type ---',
                                        'reply' => 'Is a Reply to',
                                        'follow_up' => 'Follow-up to',
                                        'appendix' => 'Appendix/Attachment to',
                                        'duplicate' => 'Duplicate of',
                                        'related' => 'General Reference'
                                    ];
                                    echo form_dropdown('relationship_type', $rel_options, '', 'id="relationship_type" class="form-control select-picker" required'); 
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-4"><?php echo $this->lang->line("relationship_details"); ?></label>
                            <div class="col-md-8">
                                <?php echo form_textarea(['name' => 'relationship_remarks', 'id' => 'relationship_remarks', 'class' => 'form-control', 'rows' => 3, 'placeholder' => 'Explain why these are linked...']); ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line("link_now"); ?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line("cancel"); ?></button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>