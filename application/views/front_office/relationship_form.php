<div class="primary-style">
    <div class="modal fade modal-container show" id="relationshipModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <?php echo form_open('front_office/save_relationship', ['id' => 'relationship-link-form', 'class' => 'form-horizontal']); ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">Link Correspondence</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="col-md-12 padding-10">
                        <?php echo form_input(['name' => 'base_id', 'id' => 'id', 'type' => 'hidden', 'value' => $base_id]); ?>

                       <div class="form-group row">
    <label class="control-label col-md-4">Link To Module</label>
    <div class="col-md-8">
        <?php 
            $modules = [
                'correspondence' => 'Correspondence',
                'cases'          => 'Legal Case',
                'contracts'      => 'Contract',
                'legal_opinions' => 'Legal Opinion',
                'conveyancing'   => 'Conveyancing'
            ];
            echo form_dropdown('target_type', $modules, 'correspondence', 'id="target_type" class="form-control select-picker"'); 
        ?>
    </div>
</div> <div class="form-group row">
                            <label class="control-label col-md-4 required">Related To</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <?php 
                                        echo form_input(["name" => "target_id", "id" => "target_id", "type" => "hidden", "required" => true]);
                                        echo form_input(["name" => "target_lookup", "id" => "target_lookup", "class" => "form-control lookup", "placeholder" => "Search Ref or Subject..."]);
                                    ?>                                     
                                </div>
                                <div data-field="target_id" class="inline-error d-none"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-4 required">Link Type</label>
                            <div class="col-md-8">
                                <?php 
                                    $options = [
                                        'reply' => 'Reply to',
                                        'follow_up' => 'Follow-up',
                                        'attachment' => 'Attachment to',
                                        'reference' => 'General Reference'
                                    ];
                                    echo form_dropdown('rel_type', $options, 'reference', 'class="form-control select-picker"'); 
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-md-4">Remarks</label>
                            <div class="col-md-8">
                                <?php echo form_textarea(['name' => 'remarks', 'class' => 'form-control', 'rows' => 2]); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="relationship-form-submit" class="btn btn-primary modal-save-btn">
                        <i class="fa fa-save"></i> Save Link
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>