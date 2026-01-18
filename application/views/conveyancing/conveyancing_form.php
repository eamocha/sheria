
    <?php echo form_open('conveyancing/submit', ['id' => 'conveyancingForm']); ?>

    <!-- Modal -->
    <div class="modal fade" id="conveyancingModal" tabindex="-1" role="dialog" aria-labelledby="conveyancingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Conveyancing Management Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo form_open('conveyancing/submit'); ?>

                    <!-- Conveyancing Type -->
                    <div class="form-group">
                        <?php echo form_label('Type of Conveyancing', 'conveyancingType', ['class' => 'control-label']); ?>
                        <?php echo form_dropdown('conveyancing_type', [  '' => 'Select Type',
                            'Mortgage' => 'Mortgage',
                            'Leases' => 'Leases',
                            'Acquisitions' => 'Acquisitions'
                        ], '', ['class' => 'form-control', 'id' => 'conveyancingType', 'required' => 'required']); ?>
                    </div>

                    <!-- Panel of Lawyers -->
                    <div class="form-group">
                        <?php echo form_label('Select External Lawyer', 'lawFirm', ['class' => 'control-label']); ?>
                        <?php echo form_dropdown('law_firm', [
                            '' => 'Select a Lawyer',
                            'Law Firm A' => 'Law Firm A',
                            'Law Firm B' => 'Law Firm B',
                            'Law Firm C' => 'Law Firm C'
                        ], '', ['class' => 'form-control', 'id' => 'lawFirm', 'required' => 'required']); ?>
                    </div>

                    <!-- Required Details -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <?php echo form_label('Name', 'name', ['class' => 'control-label']); ?>
                            <?php echo form_input(['name' => 'name', 'id' => 'name', 'class' => 'form-control', 'required' => 'required']); ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <?php echo form_label('PF Number', 'pfNo', ['class' => 'control-label']); ?>
                            <?php echo form_input(['name' => 'pf_no', 'id' => 'pfNo', 'class' => 'form-control', 'required' => 'required']); ?>
                        </div>
                    </div>

                    <!-- Additional Fields -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <?php echo form_label('Amount Approved', 'amountApproved', ['class' => 'control-label']); ?>
                            <?php echo form_input(['name' => 'amount_approved', 'id' => 'amountApproved', 'class' => 'form-control', 'required' => 'required']); ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <?php echo form_label('Value of Property', 'valueProperty', ['class' => 'control-label']); ?>
                            <?php echo form_input(['name' => 'value_property', 'id' => 'valueProperty', 'class' => 'form-control', 'required' => 'required']); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <?php echo form_label('Transaction Type', 'transactionType', ['class' => 'control-label']); ?>
                            <?php echo form_dropdown('transaction_type', [
                                '' => 'Select Type',
                                'House Purchase Loan' => 'House Purchase Loan',
                                'House Construction Loan' => 'House Construction Loan',
                                'House Refinancing Loan' => 'House Refinancing Loan',
                                'Discharge of Charge' => 'Discharge of Charge'
                            ], '', ['class' => 'form-control', 'id' => 'transactionType', 'required' => 'required']); ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <?php echo form_label('Date', 'date', ['class' => 'control-label']); ?>
                            <?php echo form_input(['type' => 'date', 'name' => 'date', 'id' => 'date', 'class' => 'form-control', 'required' => 'required']); ?>
                        </div>
                    </div>

                    <!-- Attachments with Type and Status -->
                    <div class="form-group">
                        <?php echo form_label('Attachments', '', ['class' => 'control-label']); ?>
                        <div id="attachmentSection">
                            <div class="input-group mb-2">
                                <?php echo form_upload('attachments[]', '', ['class' => 'form-control-file']); ?>
                                <?php echo form_dropdown('attachment_type[]', [
                                    '' => 'Select Type',
                                    'Valuation Report' => 'Valuation Report',
                                    'ERP Approval Screenshot' => 'ERP Approval Screenshot',
                                    'Sale Agreement' => 'Sale Agreement',
                                    'Original Title' => 'Original Title'
                                ], '', ['class' => 'form-control ml-2']); ?>
                                <?php echo form_dropdown('attachment_status[]', [
                                    '' => 'Select Status',
                                    'Pending' => 'Pending',
                                    'Submitted' => 'Submitted',
                                    'Approved' => 'Approved'
                                ], '', ['class' => 'form-control ml-2']); ?>
                                <button type="button" class="btn btn-danger ml-2 removeAttachment">X</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" id="addAttachment">Add More</button>
                    </div>

                    <div class="text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <?php echo form_submit('submit', 'Submit', ['class' => 'btn btn-primary']); ?>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('#conveyancingForm').submit(function(e) {
                e.preventDefault();
                alert('Form submitted successfully!');
                $('#conveyancingModal').modal('hide');
            });

            $('#addAttachment').click(function() {
                let attachmentRow = `
                <div class="input-group mb-2">
                    <input type="file" class="form-control-file" required>
                    <select class="form-control ml-2" required>
                        <option value="">Select Type</option>
                        <option value="Valuation Report">Valuation Report</option>
                        <option value="ERP Approval Screenshot">ERP Approval Screenshot</option>
                        <option value="Sale Agreement">Sale Agreement</option>
                        <option value="Original Title">Original Title</option>
                    </select>
                    <select class="form-control ml-2" required>
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Submitted">Submitted</option>
                        <option value="Approved">Approved</option>
                    </select>
                    <button type="button" class="btn btn-danger ml-2 removeAttachment">X</button>
                </div>`;
                $('#attachmentSection').append(attachmentRow);
            });

            $(document).on('click', '.removeAttachment', function() {
                $(this).parent().remove();
            });
        });
    </script>
