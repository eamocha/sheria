<?php

if ($docs && !empty($docs)) 
{?>
    <div class="header-contract col-md-12 row m-0"><?php
    echo form_input(["id" => "selected-doc-id", "value" => "", "type" => "hidden"]);
    if (1 < count($docs) && !(isset($document_id) && 0 < $document_id)) {       ?>
        <label class="col-form-label text-right col-md-1 required"><?php
            echo $this->lang->line("document");?></label>
        <div class="col-md-4 p-0"><?php
        echo form_dropdown("document_id", $docs, "", 'id="docs-id" class="form-control select-picker"');
        ?>
        </div>
        <?php }
    if (isset($show_share_button) && $show_share_button) {        ?>
        <div class="col-md-4 ">
        <button type="button" class="btn btn-primary" onclick="shareDocForm('contract', <?php echo $contract_id;  ?>);"><?php
            echo $this->lang->line("share");?><span class="glyphicon glyphicon-share"></span>
        </button>
        </div>
        <?php    }?>
    </div>
    <?php 
    }
if (isset($show_toolbar) && $show_toolbar){?>

<div id="toolbar">
    <button onclick="editdoc(<?=$document_id ?>,<?=$contract_id ?>,'contract',<?=$parent_lineage?>)"  class="btn btn-primary">Open On Document editor</button>
    <button onclick="saveDocument()"  class="btn btn-primary">Enable Signing</button>
</div>
<?php } ?>
<div class="row col-md-12 d-none" style="height:900px">
    <iframe id="document-editor-iframe" title="draft" width="100%" height="100%" frameborder="0">     </iframe>
</div>



<script>
    function editdoc(id,moduleId,module,parentLineage) {
        openLocation = 'appforlegal:' + encodeURIComponent('docId=' + id + '&docModuleType=' + module + '&moduleId=' + moduleId + '&lineage=' + parentLineage);
        window.location = openLocation;

    }
    //attach the save and open buttons
  function openDocument() {
    const iframe = document.getElementById("document-editor-iframe");
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.postMessage({ action: "openDocument" }, "*");
    } else {
        console.error("Iframe not found or inaccessible.");
    }
}

    function saveDocument() {
        const iframe = document.getElementById("document-editor-iframe").contentWindow;
        iframe.saveDocument && iframe.saveDocument(); // assume function exists in iframe
    }
    jQuery(document).ready(function() {
        jQuery('#docs-id', '#contracts-details').selectpicker();

        
        if (jQuery('#docs-id', '#contracts-details').length) {
            var selected = jQuery("#docs-id option:selected", '#contracts-details').text();
            var ext = selected.substr(selected.indexOf('.') + 1);
            loadiFrame(jQuery('#docs-id', '#contracts-details').val(), '<?php echo $contract_id;?>', ext, <?php echo isset($is_external_user) ? $is_external_user : false;?>);
            jQuery('#selected-doc-id', '#contracts-details').val(jQuery('#docs-id', '#contracts-details') .val());
            jQuery('#docs-id', '#contracts-details').on('change', function() {
                var selected = jQuery("#docs-id option:selected", '#contracts-details').text();
                var ext = selected.substr(selected.indexOf('.') + 1);
                loadiFrame(jQuery(this).val(), '<?php echo $contract_id;?>', ext, <?php echo isset($is_external_user) ? $is_external_user : false; ?>);
                jQuery('#selected-doc-id', '#contracts-details').val(jQuery(this).val());
            });
        } else {
            var id = '<?php echo isset($document_id) && 0 < $document_id ? $document_id : ($docs ? count($docs) == 1 ? key($docs) : false : false);?>';
            if (id) {
                var selected ='<?php echo $docs ? $docs[key($docs)] : false;?>';
                ext = selected.substr(selected.indexOf('.') + 1);
                loadiFrame(id, '<?php echo $contract_id;?>', ext, <?php echo isset($is_external_user) ? $is_external_user : false;?>);
            } else {
                loadiFrame(id, '<?php echo $contract_id;?>', 'docx', <?php echo isset($is_external_user) ? $is_external_user : false;?>);
            }
            jQuery('#selected-doc-id', '#contracts-details').val(id);
        }
        window.addEventListener("message", function(event) {" + "
            let payload = event.data.payload;
            if (typeof payload !== 'undefined') {
                let saved = payload["saved"];
                " +                    "
                if (saved) // check if the file has been saved successfully
                {
                    updateDocsCount('<?php echo $contract_id;?>');
                }
                let documentState = payload["documentState"]; // store the document state in a variable
                if (documentState !== undefined && jQuery('#docs-id', '#contracts-details').length) { // check if there is a document state
                    switch (documentState) {
                        case 'edit-mode':
                            jQuery('#docs-id', '#contracts-details').attr('disabled','disabled');
                            break;
                        case 'view-mode':
                            jQuery('#docs-id', '#contracts-details').removeAttr('disabled');
                            break;
                        default:
                            break;
                    }
                }
            }
        });
    });
</script>