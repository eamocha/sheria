<!-- Fee Notes Modal -->
<div class="modal fade" id="feeNotesModal" tabindex="-1" role="dialog" aria-labelledby="feeNotesModalLabel" >
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header  ">
                <h5 class="modal-title" id="feeNotesModalLabel">Fee Notes</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="">
                        <tr>
                            <th>Ref No</th>
                            <th>Dated</th>
                            <th>Due Date</th>
                            <th>Description</th>
                            <th>Supplier</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Payments Made</th>
                            <th class="text-right">Balance</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($feeNotes)): ?>
                            <?php foreach ($feeNotes as $note): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($note['referenceNum']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($note['dated'])); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($note['dueDate'])); ?></td>
                                    <td><?php echo htmlspecialchars($note['description'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($note['supplierName']); ?></td>
                                    <td class="text-right">
                                        <?php echo number_format((float)$note['total'], 2); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo $note['payemntsMade'] ? number_format((float)$note['payemntsMade'], 2) : '0.00'; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo number_format((float)$note['balanceDue'], 2); ?>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No fee notes available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>

        </div>
    </div>
</div>
