<div class="primary-style">
    <div class="modal fade modal-container modal-resizable" id="document-dialog-container">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $this->lang->line("documents"); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="download-table-container">
                        <table class="reference-table-style" id="download-table">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line("document"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($documents as $document): ?>
                                <tr>
                                    <td id="document-item-<?php echo $document["documents_id"]; ?>">
                                            <span title="<?php echo $document["full_name"]; ?>" class="trim-width-250 tooltip-title">
                                                <?php echo $document["full_name"]; ?>
                                            </span>
                                        <i class="spr3 blue_trash pull-right cursor-pointer-click circular-icon"
                                           onclick="confirmationDialog('confirm_delete_record', {
                                                   resultHandler: deleteMilestoneDocument,
                                                   parm: {
                                                   'documentId': '<?php echo $document["documents_id"]; ?>',
                                                   'milestoneId': '<?php echo $milestone_id; ?>',
                                                   'contractId': '<?php echo $contract_id; ?>'
                                                   }
                                                   })">
                                        </i>
                                        <a class="pull-right mr-10" href="modules/contract/contracts/download_file/<?php echo $document["documents_id"]; ?>">
                                            <i class="spr3 download pull-right circular-icon"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="close_model no_bg_button pull-right text-align-right"
                            data-dismiss="modal">
                        <?php echo $this->lang->line("cancel"); ?>
                    </button>
                </div>
            </div></div></div></div>