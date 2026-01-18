<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Amendment Details Modal -->
<div class="modal fade modal-container modal-resizable" id="amendmentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="amendmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="amendmentDetailsModalLabel"><?php echo $this->lang->line('amendment_details'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="amendment-details">
                    <?php if (!empty($amendment_details)): ?>
                        <div class="table-responsive">
                            <table class="table table-borderless" aria-label="Contract Amendment Details">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo $this->lang->line('id'); ?></th>
                                        <th scope="col">AmendmentID</th>
                                        <th scope="col"><?php echo $this->lang->line('contract_id'); ?></th>
                                        <th scope="col"><?php echo $this->lang->line('field_name'); ?></th>
                                        <th scope="col"><?php echo $this->lang->line('old_value'); ?></th>
                                        <th scope="col"><?php echo $this->lang->line('new_value'); ?></th>
                                        <th scope="col"><?php echo $this->lang->line('created_on'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($amendment_details as $detail): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($detail['id']); ?></td>
                                            <td><?php echo htmlspecialchars($detail['amendment_history_id']); ?></td>
                                            <td>
                                                
                                                    <?php echo htmlspecialchars("CT" . $detail['contract_id']); ?>
                                           
                                            </td>
                                            <td><?php echo htmlspecialchars($detail['field_name']); ?></td>
                                            <td><?php echo htmlspecialchars($detail['old_value'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($detail['new_value'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($detail['createdOn']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        
                    <?php else: ?>
                        <p id="no-details"><?php echo $this->lang->line('no_data'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to initialize modal -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#amendmentDetailsModal').on('show.bs.modal', function (e) {
            // Optional: Adjust modal content dynamically if needed
        });
    });
</script>