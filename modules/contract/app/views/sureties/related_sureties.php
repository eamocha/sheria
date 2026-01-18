<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1>Surety Bonds/Securities</h1>
            <button type="button" class="btn btn-primary rounded-pill px-4" onclick="suretyForm(<?php echo $contract_id??0; ?>,0,'loadForm',true)">
                <i class="fas fa-plus-circle mr-2"></i> Add New Bond
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Bond Type</th>
                                <th>Amount</th>
                                <th>Provider</th>
                                <th>Bond No.</th>
                                <th>Effective Date</th>
                                <th>Expiry Date</th>
                                <th>Released Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($surety_bonds)):
                                foreach ($surety_bonds as $bond):
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($bond['id']); ?></td>

                                        <td><?php echo htmlspecialchars($bond['bond_type']); ?></td>
                                        <td><?php echo htmlspecialchars($bond['currency_code'] . ' ' . number_format($bond['bond_amount'], 2)); ?></td>
                                        <td><?php echo htmlspecialchars($bond['surety_provider']); ?></td>
                                        <td><?php echo htmlspecialchars($bond['bond_number']); ?></td>
                                        <td><?php echo htmlspecialchars($bond['effective_date']); ?></td>
                                        <td><?php echo htmlspecialchars($bond['expiry_date'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($bond['released_date'] ?? 'N/A'); ?></td>
                                        <td>
                                        <span class="badge status-badge
                                            <?php
                                        switch ($bond['bond_status']) {
                                            case 'Active': echo 'badge-success'; break;
                                            case 'Expired': echo 'badge-danger'; break;
                                            case 'Released': echo 'badge-info'; break;
                                            case 'Claimed': echo 'badge-warning'; break;
                                            case 'Pending': echo 'badge-secondary'; break;
                                            default: echo 'badge-light'; break;
                                        }
                                        ?>
                                        "><?php echo htmlspecialchars($bond['bond_status']); ?></span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="javascript:;" class="" title="Edit Bond"   onclick="suretyForm(<?php echo $contract_id??0; ?>,<?php echo $bond['id']; ?>,'edit',true)"><i class="icon-alignment fa fa-pencil purple_color for-editcursor-pointer-click"></i></a>
                                                <a href="<?php echo base_url('surety_bonds/view_document/' . $bond['document_id']); ?>" class="" title="View Document" target="_blank" <?php echo empty($bond['document_id']) ? 'disabled' : ''; ?>><i class="fas fa-file-alt"></i></a>
                                                <a href="javascript:;" class="" title="Delete Bond" onclick="confirmDelete(<?php echo $bond['id']; ?>)"><i class="icon-alignment fa fa-trash light_red-color"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                endforeach;
                            else:
                                ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No surety bonds found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(bondId) {
      if (confirm('Are you sure you want to delete this surety bond? This action cannot be undone.')) {
            window.location.href = '<?php echo base_url('surety_bonds/delete/'); ?>' + bondId;
        }
    }
</script>

