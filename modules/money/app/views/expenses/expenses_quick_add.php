<div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addExpenseModalLabel"><?php echo $this->lang->line('add_new_expense'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <?php echo form_open("vouchers/expenseQuickAdd", "novalidate class='form-horizontal col-md-12' id='expenseForm' enctype='multipart/form-data'"); ?>

            <div class="modal-body">
                <?php echo form_hidden("voucher_header_id", $expense["id"] ?? ''); ?>
                <?php echo form_hidden("organization_id", $this->session->userdata("organizationID")); ?>
                <?php echo form_hidden("case_id", isset($caseId) ? $caseId : ""); ?>
                <?php echo form_hidden("expense_status", isset($expense["status"]) && !empty($expense["id"]) ? $expense["status"] : ""); ?>
                <?php echo form_hidden("vendor_id", $expense["vendor_id"] ?? ''); ?>
                <?php echo form_hidden("isCasePreset", $isCasePreset ?? ''); ?>
                <?php echo form_hidden("referrer", $referrer ?? ''); ?>
                <?php echo form_hidden("paid_through", $paid_through ?? 0); ?>

                <div class="row">
                    <div class="form-group col-md-6">
                        <?php echo form_label($this->lang->line('reference_number') . ' *', 'referenceNum'); ?>
                        <?php echo form_input([
                            'name'        => 'referenceNum',
                            'id'          => 'referenceNum',
                            'class'       => 'form-control',
                            'type'        => 'text',
                            'required'    => 'required',
                            'value'       => set_value('referenceNum', $expense['referenceNum'] ?? '')
                        ]); ?>
                    </div>

                    <div class="form-group col-md-6">
                        <?php echo form_label($this->lang->line('expense_category') . ' *', 'expense_category'); ?>
                        <?php
                        $expense_category_options = ['' => $this->lang->line('select_category')];
                        foreach ($expense_categories as $category) {
                            $expense_category_options[$category['id']] = $category['name'];
                        }
                        echo form_dropdown('expense_category', $expense_category_options, set_value('expense_category', $expense['expense_category'] ?? ''), 'class="form-control" required');
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <?php echo form_label($this->lang->line('amount') . ' *', 'amount'); ?>
                        <?php echo form_input([
                            'name'        => 'amount',
                            'id'          => 'amount',
                            'class'       => 'form-control',
                            'type'        => 'number',
                            'step'        => '0.01',
                            'min'         => '0',
                            'required'    => 'required',
                            'value'       => set_value('amount', $expense['amount'] ?? '')
                        ]); ?>
                    </div>

                    <div class="form-group col-md-6">
                        <?php echo form_label($this->lang->line('date') . ' *', 'paidOn'); ?>
                        <?php echo form_input([
                            'name'        => 'paidOn',
                            'id'          => 'paidOn',
                            'class'       => 'form-control start',
                            'type'        => 'text',
                            'required'    => 'required',
                            'value'       => set_value('paidOn', $expense['paidOn'] ?? date('Y-m-d'))
                        ]); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo form_label($this->lang->line('related_external_counsel'), 'external_counsel_id'); ?>
                    <?php
                    $external_counsel_options = ['' => $this->lang->line('select_counsel')];
                    foreach ($external_counsels as $counsel) {
                        $external_counsel_options[$counsel['id']] = $counsel['name'] . ' (' . $counsel['firm'] . ')';
                    }
                    echo form_dropdown('external_counsel_id', $external_counsel_options, set_value('external_counsel_id', $expense['external_counsel_id'] ?? ''), 'class="form-control select2"');
                    ?>
                </div>

                <div class="form-group">
                    <?php echo form_label($this->lang->line('remarks'), 'remarks'); ?>
                    <?php echo form_textarea([
                        'name'        => 'remarks',
                        'id'          => 'remarks',
                        'class'       => 'form-control',
                        'rows'        => '3',
                        'value'       => set_value('remarks', $expense['remarks'] ?? '')
                    ]); ?>
                </div>

                <div class="form-group">
                    <?php echo form_label($this->lang->line('attachment'), 'uploadDoc'); ?>
                    <?php echo form_upload([
                        'name'        => 'uploadDoc',
                        'id'          => 'uploadDoc',
                        'class'       => 'form-control'
                    ]); ?>
                    <small class="text-muted"><?php echo $this->lang->line('max_file_size'); ?>: 10MB</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <?php echo form_submit('submitBtn2', $this->lang->line('save'), 'class="btn btn-primary" id="submitBtn2"'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        jQuery('#expenseForm').on('submit', function(e) {
            e.preventDefault();

            var form = this;
            var formData = new FormData(form);
            var submitBtn = jQuery('#submitBtn2');

            submitBtn.prop('disabled', true).text('Saving...');

            jQuery.ajax({
                url: jQuery(form).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    submitBtn.prop('disabled', false).text('<?php echo $this->lang->line('save'); ?>');
                    if (res.status === 'success') {
                        pinesMessage({ ty: "success", m: res.message || 'Saved successfully' });
                        jQuery('#addExpenseModal').modal('hide');
                        location.reload(); // Or refresh a section
                    } else {
                        pinesMessage({ ty: "error", m: res.message || 'Failed to save. Check input.' });

                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        });
    });
</script>

