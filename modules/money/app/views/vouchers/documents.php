<?php
$this->load->view("partial/tabs_subnav", $tabsNLogs);?>
<div class="col-md-12 ">
    <div class="col-md-12 no-padding">
        <div id="dropbox">
            <div id="drag_file_info" class="col-md-11 no-margin d-none"></div>
            <div id="dragAndDrop" class=" container dragAndDrop d-none" style="margin: 6em 0 0;"></div>
            <div id="voucherDocumentsTabs">
                <div class="attachments">
                    <?php echo form_open("", 'name="voucherAttachmentSearchFilters" id="voucherAttachmentSearchFilters" method="post" class="form-horizontal d-none"');
                    echo form_input(["id" => "module", "name" => "module", "value" => $module, "type" => "hidden"]);
                    echo form_input(["id" => "module-record-id", "name" => "module_record_id", "value" => $module_record_id, "type" => "hidden"]);
                    echo form_input(["id" => "term", "name" => "term", "value" => "", "type" => "hidden"]);
                    echo form_close(); ?>
                    <div <?php echo $this->session->userdata("AUTH_language") == "arabic" ? "class='k-rtl'" : "";?>>
                        <div id="voucherAttachmentGrid"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<iframe id="hidden_upload" name="hidden_upload" src='' width="0" height="0" class="d-none"></iframe>
<object id="winFirefoxPlugin" type="application/x-sharepoint" width="0" height="0" style="visibility: hidden;"></object>
<div class="d-none" id="attachmentDialog"> <?php $this->load->view("vouchers/attachment_add", compact("data"));?>
</div><?php $this->load->view("documents_management_system/document_editor_modal", []);
$this->load->view("documents_management_system/document_editor_installation_modal", []);
?>
<script type="text/javascript">
    var $documentConfig = {
        attachmentDocumentGrid: '#voucherAttachmentGrid',
        attachmentDocumentForm: '#voucherAttachmentForm',
        attachmentDocumentSearchFilters: 'voucherAttachmentSearchFilters',
        filterModuleAttachmentIdValue: '#filterContactAttachmentIdValue',
        moduleAttachmentId: '#voucherAttachmentId',
        objName: '<?php echo $objName;?>',
        allowed_upload_size_megabite: '<?php echo $this->config->item("allowed_upload_size_megabite");?>',
        moduleController: '<?php echo $module_controller;?>',
        module: '<?php echo $module;?>',
        moduleVersion: '<?php echo $module_version;?>',
    };
    var allowedUploadSizeMegabite = '<?php echo $this->config->item("allowed_upload_size_megabite");?>';
    var moduleController = '<?php echo $module_controller;?>';
    var installationType = '<?php echo $this->instance_data_array["installationType"];?>';
    var module = '<?php echo $module;?>';
    var modelName = 'contract';
    var moduleRecordId = '<?php echo $module_record_id;?>';
    var viewableExtensions = <?php echo json_encode($this->document_management_system->viewable_documents_extensions);?>;
    var isCloudInstance = <?php echo $this->db->dbdriver === "sqlsrv" ? "false" : "true";?>;
    var documentEditorDownloadURL = '<?php echo !empty($this->document_editor_download_url) ? $this->document_editor_download_url : "";?>';
    var showDocumentEditorInstallationModal = <?php echo isset($show_document_editor_installation_modal) ? "true" : "false";?>;
</script>