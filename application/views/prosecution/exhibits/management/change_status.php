<div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeModalLabel">Change Exhibit Status</h5>
                <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <?= form_open('', ['id' => 'statusChangeForm']) ?>
                <?php echo form_input([
                    'type' => 'hidden',
                    'name' => 'exhibit_id',
                    'id' => 'exhibit_id',
                    'value'=>$id
                ]); ?>

                <div class="form-group">
                    <?= form_label('New Status*', 'new_status') ?>
                    <?= form_dropdown('new_status', [
                        '' => 'Select status',
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                        'Disposed' => 'Disposed',
                        'Returned' => 'Returned to Owner',
                        'Destroyed' => 'Destroyed',
                        'Archived' => 'Archived'
                    ], '', ['id' => 'new_status', 'class' => 'form-control', 'required' => true]) ?>
                </div>

                <div class="form-group">
                    <?= form_label('Effective Date/Time*', 'status_date') ?>
                    <?= form_input([
                        'type' => 'text', 
                        'name' => 'status_date',
                        'id' => 'statusDateTimeInput', 
                        'class' => 'form-control datetimepicker',
                        'required' => true
                    ]) ?>
                </div>

                <div class="form-group">
                    <?= form_label('Reason/Notes*', 'status_reason') ?>
                    <?= form_textarea([
                        'name' => 'status_reason',
                        'id' => 'status_reason',
                        'class' => 'form-control',
                        'rows' => '3',
                        'required' => true
                    ]) ?>
                </div>

                <div class="form-group">
                    <?= form_label('Supporting Documents', 'status_attachments') ?>
                    <div class="custom-file">
                        <?= form_upload([
                            'name' => 'attachments[]',
                            'id' => 'status_attachments',
                            'class' => 'custom-file-input',
                            'multiple' => true
                        ]) ?>
                        <label class="custom-file-label" for="status_attachments">Choose files</label>
                    </div>
                    <small class="form-text text-muted">Upload any supporting documents for this status change</small>
                </div>

                <div id="disposalFields" class="d-none">
                    <div class="form-group">
                        <?= form_label('Date Approved for Disposal', 'date_approved_for_disposal') ?>
                        <?= form_input([
                            'type' => 'text', // Change type to text
                            'name' => 'date_approved_for_disposal',
                            'id' => 'dateApprovedDisposalInput', // Unique ID for Flatpickr
                            'class' => 'form-control'
                        ]) ?>
                    </div>

                    <div class="form-group">
                        <?= form_label('Date Disposed', 'date_disposed') ?>
                        <?= form_input([
                            'type' => 'text', // Change type to text
                            'name' => 'date_disposed',
                            'id' => 'dateDisposedInput', // Unique ID for Flatpickr
                            'class' => 'form-control'
                        ]) ?>
                    </div>

                    <div class="form-group">
                        <?= form_label('Manner of Disposal', 'manner_of_disposal') ?>
                        <?= form_input([
                            'type' => 'text',
                            'name' => 'manner_of_disposal',
                            'id' => 'manner_of_disposal',
                            'class' => 'form-control',
                            'placeholder' => 'e.g., Returned to owner, Destroyed'
                        ]) ?>
                    </div>

                    <div class="form-group">
                        <?= form_label('Disposal Remarks', 'disposal_remarks') ?>
                        <?= form_textarea([
                            'name' => 'disposal_remarks',
                            'id' => 'disposal_remarks',
                            'class' => 'form-control',
                            'rows' => '3'
                        ]) ?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Status</button>
                </div>

                <?= form_close() ?>

            </div>
        </div>
    </div>
</div>


<script>
    jQuery(document).ready(function () {
        const disposalStatuses = ['Disposed', 'Destroyed', 'Returned'];

        flatpickr("#statusDateTimeInput", {
             enableTime: true,
                 dateFormat: "Y-m-d H:i",
                     time_24hr: true
        });

        flatpickr("#dateApprovedDisposalInput", {
           dateFormat: "Y-m-d H:i",
            time_24hr: true,
            enableTime: true,
        });

   
        flatpickr("#dateDisposedInput", {
               dateFormat: "Y-m-d H:i",
            time_24hr: true,
            enableTime: true,
        });


        jQuery('#new_status').on('change', function () {
            const selected = jQuery(this).val();
            if (disposalStatuses.includes(selected)) {
                jQuery('#disposalFields').removeClass('d-none');
                jQuery('#dateApprovedDisposalInput').prop('required', true);
                jQuery('#dateDisposedInput').prop('required', true);
            } else {
                jQuery('#disposalFields').addClass('d-none');
                jQuery('#disposalFields').find('input, textarea').val('');
                jQuery('#dateApprovedDisposalInput').prop('required', false);
                jQuery('#dateDisposedInput').prop('required', false);
            }
        });

        // Reset fields on modal open
        jQuery('#statusChangeModal').on('show.bs.modal', function () {
            jQuery('#disposalFields').addClass('d-none');
            jQuery('#disposalFields').find('input, textarea').val('');
            jQuery('#dateApprovedDisposalInput').prop('required', false);
            jQuery('#dateDisposedInput').prop('required', false);
            jQuery('#new_status').val('').trigger('change'); // Reset status dropdown
            // Reset Flatpickr dates
            //flatpickr("#statusDateTimeInput").clear();
          //  flatpickr("#dateApprovedDisposalInput").clear();
        //    flatpickr("#dateDisposedInput").clear();
        });

        // Handle file input label update
        jQuery('.custom-file-input').on('change', function() {
            let fileName = jQuery(this).val().split('\\').pop();
            jQuery(this).next('.custom-file-label').html(fileName || 'Choose files');
        });
    });
</script>