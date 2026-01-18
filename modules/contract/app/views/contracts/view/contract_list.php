<div class="primary-style">
    <div class="modal fade modal-container modal-resizable">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title"><?php echo htmlspecialchars($title);?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div id="contract-docs-container" class="col-md-12 no-margin p-0 padding-10">
                            <?php foreach ($docs as $doc) { ?>
                                <div class="col-md-12">
                                    <h5><?php    if ($module == "contract") {?>
                                            <a href="javascript:;" onclick="editDocument('<?php echo $doc["document_id"];?>', '<?php  echo $module;?>', '<?php echo $doc["contract_id"];?>', '<?php echo $doc["parent_lineage"]; ?>', '<?php echo $doc["extension"]; ?>');"><?php  echo $doc["full_name"];  ?>
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                        <?php    } else {?>
                                            <a href="javascript:;"  onclick="downloadFile('<?php   echo $doc["document_id"];?>', false, 'customer-portal', 'contracts');" ><?php  echo $doc["full_name"];?>
                                            <i class="fa-solid fa-circle-down"></i>
                                            </a><?php    }?>
                                    </h5>
                                </div>  <?php }?>
                        </div>
                    </div><!-- /.modal-body -->
                    <div class="modal-footer">
                        <span class="loader-submit"></span>
                        <button type="button" class="btn btn-link" data-dismiss="modal"><?php echo $this->lang->line("cancel");?>   </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<script>
    var viewableExtensions = <?php echo json_encode($this->document_management_system->viewable_documents_extensions);?>
</script>