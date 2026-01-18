<div class="primary-style">
    <div class="modal fade" id="addSuretyBondModal" tabindex="-1" aria-labelledby="addSuretyBondModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSuretyBondModalLabel">Surety Bond</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                 echo form_open('', 'id="suretyBondForm" class="form-horizontal" method="post"');
                 echo form_hidden('contract_id', isset($contract_id) ? htmlspecialchars($contract_id) : 0);
                 echo form_hidden('mode', isset($mode) ? htmlspecialchars($mode) : "edit");
                 echo form_hidden('id', isset($bond['id']) ? htmlspecialchars($bond['id']) : 0);
                echo form_hidden('createdOn', isset($bond['createdOn']) ? htmlspecialchars($bond['createdOn']) : date('Y-m-d H:i:s'));
                echo form_hidden('createdBy', isset($bond['createdBy']) ? htmlspecialchars($bond['createdBy']) : $this->session->userdata('AUTH_user_id'));
                echo form_hidden('modifiedOn', date('Y-m-d H:i:s'));
                echo form_hidden('modifiedBy', $this->session->userdata('AUTH_user_id'));
                ?>

                <div class="form-group">
                    <?php echo form_label('Bond Type', 'bondType'); ?>
                    <?php
                    $bond_type_options = [
                        '' => 'Select Bond Type',
                        'Performance Bond' => 'Performance Bond',
                        'Bid Bond' => 'Bid Bond',
                        'Payment Bond' => 'Payment Bond',
                        'Maintenance Bond' => 'Maintenance Bond',
                        'Advance Payment Bond' => 'Advance Payment Bond',
                        'Other' => 'Other',
                    ];
                    echo form_dropdown('bond_type', $bond_type_options, set_value('bond_type', $bond['bond_type'] ?? ''), 'class="form-control select-picker" id="bondType" required');
                    ?>
                    <div data-field="bond_type" class="inline-error d-none"></div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <?php echo form_label('Currency', 'currencyId');
                        echo form_dropdown('currency_id', $currencies, set_value('currency_id', $bond['currency_id'] ?? ''), 'class="form-control select-picker" id="currencyId" required');
                        ?>
                        <div data-field="currency_id" class="inline-error d-none"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <?php echo form_label('Bond Amount', 'bondAmount'); ?>
                        <?php echo form_input([
                            'name'        => 'bond_amount',
                            'id'          => 'bondAmount',
                            'class'       => 'form-control',
                            'type'        => 'number',
                            'step'        => '0.01',
                            'placeholder' => 'e.g., 1500000.00',
                            'required'    => 'required',
                            'value'       => set_value('bond_amount', $bond['bond_amount'] ?? '')
                        ]); ?>
                        <div data-field="bond_amount" class="inline-error d-none"></div>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo form_label('Surety Provider', 'suretyProvider'); ?>
                    <?php echo form_input([
                        'name'        => 'surety_provider',
                        'id'          => 'suretyProvider',
                        'class'       => 'form-control',
                        'type'        => 'text',
                        'placeholder' => 'e.g., XYZ Insurance Co.',
                        'required'    => 'required',
                        'value'       => set_value('surety_provider', $bond['surety_provider'] ?? '')
                    ]); ?>
                    <div data-field="surety_provider" class="inline-error d-none"></div>
                </div>

                <div class="form-group">
                    <?php echo form_label('Bond Number', 'bondNumber'); ?>
                    <?php echo form_input([
                        'name'        => 'bond_number',
                        'id'          => 'bondNumber',
                        'class'       => 'form-control',
                        'type'        => 'text',
                        'placeholder' => 'e.g., PB-2024-001',
                        'required'    => 'required',
                        'value'       => set_value('bond_number', $bond['bond_number'] ?? '')
                    ]); ?>
                    <div data-field="bond_number" class="inline-error d-none"></div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <?php echo form_label('Effective Date', 'effectiveDate'); ?>
                        <?php echo form_input([
                            'name'        => 'effective_date',
                            'id'          => 'effectiveDate',
                            'class'       => 'form-control datepicker',
                            'type'        => 'text',
                            'required'    => 'required',
                            'value'       => set_value('effective_date', $bond['effective_date'] ?? '')
                        ]); ?>
                        <div data-field="effective_date" class="inline-error d-none"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <?php echo form_label('Expiry Date', 'expiryDate'); ?>
                        <?php echo form_input([
                            'name'        => 'expiry_date',
                            'id'          => 'expiryDate',
                            'class'       => 'form-control datepicker',
                            'type'        => 'text',
                            'value'       => set_value('expiry_date', $bond['expiry_date'] ?? '')
                        ]); ?>
                        <div data-field="expiry_date" class="inline-error d-none"></div>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo form_label('Bond Status', 'bondStatus'); ?>
                    <?php
                    $bond_status_options = [
                        'Active' => 'Active',
                        'Expired' => 'Expired',
                        'Released' => 'Released',
                        'Claimed' => 'Claimed',
                        'Pending' => 'Pending',
                    ];
                    echo form_dropdown('bond_status', $bond_status_options, set_value('bond_status', $bond['bond_status'] ?? 'Active'), 'class="form-control select-picker" id="bondStatus" required');
                    ?>
                    <div data-field="bond_status" class="inline-error d-none"></div>
                </div>

                <div class="form-group">
                    <?php echo form_label('Released Date', 'releasedDate'); ?>
                    <?php echo form_input([
                        'name'        => 'released_date',
                        'id'          => 'releasedDate',
                        'class'       => 'form-control datepicker',
                        'type'        => 'text',
                        'value'       => set_value('released_date', $bond['released_date'] ?? '')
                    ]); ?>
                    <div data-field="released_date" class="inline-error d-none"></div>
                </div>
                <div class="clear clearfix clearfloat"></div>
                <div class="form-group">
                    <?php echo form_label('Attachment', 'bondAttachment'); ?>
                    <?php echo form_upload([
                        'name'        => 'bond_attachment',
                        'id'          => 'bondAttachment',
                      //  'class'       => 'custom-file-input'
                    ]); ?>
                    <small class="form-text text-muted">Upload a relevant document for the surety bond (e.g., the bond certificate).</small>
                    <div data-field="bond_attachment" class="inline-error d-none"></div>
                </div>

                <div class="form-group">
                    <?php echo form_label('Remarks', 'remarks'); ?>
                    <?php echo form_textarea([
                        'name'        => 'remarks',
                        'id'          => 'remarks',
                        'class'       => 'form-control',
                        'rows'        => '3',
                        'value'       => set_value('remarks', $bond['remarks'] ?? '')
                    ]); ?>
                    <div data-field="remarks" class="inline-error d-none"></div>
                </div>
                <div class="form-group col-md-6">
                    <?php echo form_label('Archived', 'archivedLable');
                    echo form_dropdown('archived', $archivedValues, set_value('archived', $bond['archived'] ?? $defaultArchivedValue), 'class="form-control select-picker" id="archived" required');
                    ?>
                    <div data-field="currency_id" class="inline-error d-none"></div>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer justify-content-between">
                <?php $this->load->view("templates/send_email_option_template", ["type" => "add_surety", "container" => "#surety-dialog", "hide_show_notification" => $hide_show_notification]);?>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <?php echo form_submit('submitSuretyBond', 'Save Surety Bond', 'class="btn btn-primary" id="form-submit"'); ?>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
<script>
    jQuery(document).ready(function() {
        jQuery('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
</script>