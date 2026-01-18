<div class="primary-style">
    <div class="modal fade " data-backdrop="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php echo $this->lang->line("attach_signed_opinion");?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body"><?php echo form_open("legal_opinion/add_legal_opinion_file", 'id="attach-advisory-form" name="advisoryAttachmentForm" method="post" enctype="multipart/form-data" target="hearing_hidden_upload" class="form-horizontal"');
                    echo form_input(["name" => "action", "value" => "submitFile", "type" => "hidden"]);
                        echo form_input(["id" => "lineage", "name" => "lineage", "type" => "hidden"]);

                    echo form_input(["name" => "id", "value" => $id, "id" => "id", "type" => "hidden"]);?>
                        <div class="col-md-12 no-padding">
                            <div class="row m-0 form-group col-md-12 no-padding">
                                <label class="control-label "><?php echo $this->lang->line("attach_signed_opinion_file");?></label>
                                <input type='file' name='file' id='file' class='form-control col-md-12 mb-1' >
                                <input type='hidden' name='opinion_file' id='opinion_file' value='<?php echo $id?>' >
                                <label class="control-label "><?php echo $this->lang->line("status");?></label>
                                <select  name='file_status' id='file_status' class='form-control' ><option>Draft</option><option>Awaiting Signature</option><option>Approved</option><option>Signed</option></select>

                                <label class="control-label  "><?php echo $this->lang->line("description");?></label>
                                <div class="col-md-12 no-padding-right">
                                    <?php echo form_textarea(["name" => "summary", "id" => "summary", "rows" => "6", "cols" => "0", "class" => "form-control mb-1", "value" => ""]);?>                            </div>
                            </div>
                        </div>
                        <?php echo form_close();?>
                    </div><!-- /.modal-body -->
                    <div class="modal-footer">

                        <button type="button" class="btn save-button btn-info btn-add-dropdown modal-save-btn" id="form-opinion_file-submit"><?php echo $this->lang->line("save");?></button>
                        <button type="button" class="close_model no_bg_button pull-right text-align-right" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<script>
        jQuery(document).ready(function(){
            jQuery('.tooltip-title').tooltipster({
                contentAsHTML: true,
                timer: 22800,
                animation: 'grow',
                delay: 200,
                theme: 'tooltipster-default',
                touchDevices: false,
                trigger: 'hover',
                maxWidth: 350,
                interactive: true
            });
        });
    </script>