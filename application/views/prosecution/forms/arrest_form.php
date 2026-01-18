<div class="modal fade" id="recordArrestModal" tabindex="-1" role="dialog" aria-labelledby="recordArrestModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="recordArrestForm" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="recordArrestModalLabel">Record Arrest</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <?php
            // $case_id and $arrestDetails should be set by the controller
            $arrestDetails = isset($arrestDetails) ? $arrestDetails : [];
          ?>
          <?php echo form_input([
            'type' => 'hidden',
            'name' => 'case_id',
            'id' => 'case_id',
            'value' => isset($case_id) ? $case_id : ''
          ]); ?>

          

          <!-- Name of Arrested Person (hidden id + visible name) -->
          <div class="form-group">
            <?php echo form_input([
              'type' => 'hidden',
              'name' => 'arrested_contact_id',
              'id' => 'arrested_contact_id',
              'value' => isset($arrestDetails['arrested_contact_id']) ? $arrestDetails['arrested_contact_id'] : 0
            ]); ?>
            <label for="arrested_contact_id" class="control-label required">Name of Arrested Person</label>
            <?php echo form_input([
              'type' => 'text',
              'class' => 'form-control lookup',
              'id' => 'arrested_name',
              'name' => 'arrested_name',
              'value' => isset($arrestDetails['arrested_name']) ? $arrestDetails['arrested_name'] : '',
              'required' => 'required'
            ]); ?>
            <div data-field="arrested_contact_id" class="inline-error d-none"></div>
          </div>

          <!-- Gender -->
          <div class="form-group">
            <label for="arrested_gender" class="control-label">Gender</label>
            <?php
              $gender_options = [
                '' => 'Select Gender',
                'male' => 'Male',
                'female' => 'Female',
                'other' => 'Other'
              ];
              echo form_dropdown(
                'arrested_gender',
                $gender_options,
                isset($arrestDetails['arrested_gender']) ? $arrestDetails['arrested_gender'] : '',
                'class="form-control" id="arrested_gender"'
              );
            ?>
            <div data-field="arrested_gender" class="inline-error d-none"></div>
          </div>

          <!-- Age -->
          <div class="form-group">
            <label for="arrested_age" class="control-label">Age</label>
            <?php echo form_input([
              'type' => 'number',
              'class' => 'form-control',
              'id' => 'arrested_age',
              'name' => 'arrested_age',
              'min' => '0',
              'value' => isset($arrestDetails['arrested_age']) ? $arrestDetails['arrested_age'] : ''
            ]); ?>
            <div data-field="arrested_age" class="inline-error d-none"></div>
          </div>
          <!-- Date of Arrest -->
          <div class="form-group">
            <label for="arrest_date" class="control-label required">Date of Arrest</label>
            <div class="input-group date col-md-5 p-0 date-picker" id="arrest-date-add-new">
              <?php echo form_input([
                'type' => 'text',
                'class' => 'form-control datepicker',
                'id' => 'arrest_date',
                'name' => 'arrest_date',
                'value' => isset($arrestDetails['arrest_date']) ? $arrestDetails['arrest_date'] : '',
                'required' => 'required'
              ]); ?>
              <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
            </div>
            <div data-field="arrest_date" class="inline-error d-none"></div>
          </div>
        <!-- Location of Arrest -->
        <div class="form-group">
            <label for="arrest_location" class="control-label required">Location of Arrest</label>
            <?php echo form_input([
                'type' => 'text',
                'class' => 'form-control',
                'id' => 'arrest_location',
                'name' => 'arrest_location',
                'value' => isset($arrestDetails['arrest_location']) ? $arrestDetails['arrest_location'] : '',
                'required' => 'required'
            ]); ?>
            <div data-field="arrest_location" class="inline-error d-none"></div>
        </div>

        <!-- Bail Status -->
        <div class="form-group">
            <label for="bail_status" class="control-label">Bail Status</label>
            <?php
                $bail_status_options = [
                    '' => 'Select Bail Status',
                    'granted' => 'Granted',
                    'not_granted' => 'Not Granted',
                    'pending' => 'Pending'
                ];
                echo form_dropdown(
                    'bail_status',
                    $bail_status_options,
                    isset($arrestDetails['bail_status']) ? $arrestDetails['bail_status'] : '',
                    'class="form-control" id="bail_status"'
                );
            ?>
            <div data-field="bail_status" class="inline-error d-none"></div>
        </div>

          <!-- Police Station -->
          <div class="form-group">
            <label for="arrest_police_station" class="control-label required">Police Station</label>
            <?php echo form_input([
              'type' => 'text',
              'class' => 'form-control',
              'id' => 'arrest_police_station',
              'name' => 'arrest_police_station',
              'value' => isset($arrestDetails['arrest_police_station']) ? $arrestDetails['arrest_police_station'] : '',
              'required' => 'required'
            ]); ?>
            <div data-field="arrest_police_station" class="inline-error d-none"></div>
          </div>

          <!-- OB Number -->
          <div class="form-group">
            <label for="arrest_ob_number" class="control-label">OB Number</label>
            <?php echo form_input([
              'type' => 'text',
              'class' => 'form-control',
              'id' => 'arrest_ob_number',
              'name' => 'arrest_ob_number',
              'value' => isset($arrestDetails['arrest_ob_number']) ? $arrestDetails['arrest_ob_number'] : ''
            ]); ?>
            <div data-field="arrest_ob_number" class="inline-error d-none"></div>
          </div>

          <!-- Case File Number -->
          <div class="form-group">
            <label for="arrest_case_file_number" class="control-label">Case File Number</label>
            <?php echo form_input([
              'type' => 'text',
              'class' => 'form-control',
              'id' => 'arrest_case_file_number',
              'name' => 'arrest_case_file_number',
              'value' => isset($arrestDetails['arrest_case_file_number']) ? $arrestDetails['arrest_case_file_number'] : ''
            ]); ?>
            <div data-field="arrest_case_file_number" class="inline-error d-none"></div>
          </div>

         

          <!-- Remarks -->
          <div class="form-group">
            <label for="arrest_remarks" class="control-label">Remarks</label>
            <?php echo form_textarea([
              'class' => 'form-control',
              'id' => 'arrest_remarks',
              'name' => 'arrest_remarks',
              'rows' => 2,
              'value' => isset($arrestDetails['arrest_remarks']) ? $arrestDetails['arrest_remarks'] : ''
            ]); ?>
            <div data-field="arrest_remarks" class="inline-error d-none"></div>
          </div>
           <!-- Attachments -->
          <div class="form-group">
            <label for="arrest_attachments" class="control-label">Attachment</label>
            <input type="file" class="margin-top" id="arrest_attachments" name="arrest_attachments[]" multiple>
            <div data-field="arrest_attachments" class="inline-error d-none"></div>
          </div>
        </div>
        <div class="modal-footer">
          <span class="loader-submit"></span>
          <button type="submit" class="btn btn-primary">Save Arrest</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">

    jQuery(document).ready(function () {
        var container=jQuery('#recordArrestForm');
  
        var lookupDetailsArrestedPerson = {
        'lookupField': jQuery('#arrested_name', container),
        'errorDiv': 'arrested_contact_id',
        'hiddenId': '#arrested_contact_id',
        'resultHandler': setArrestedPersonToForm
    };
    lookUpContacts(lookupDetailsArrestedPerson, container);


 
    });


    function setArrestedPersonToForm(record, container) {
    // Compose full name (with father name if available)
    var name = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#arrested_name', container).val(name);
    jQuery('#arrested_contact_id', container).val(record.id);

    // Re-initialize typeahead/autocomplete if present
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#arrested_name", container).typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#arrested_name', container),
            'errorDiv': 'arrested_contact_id',
            'hiddenId': '#arrested_contact_id',
            'resultHandler': setArrestedPersonToForm
        };
        lookUpContacts(lookupDetails, jQuery(container));
    }

    // Optionally, show a link to edit/view the contact if you have such a UI element
    if (jQuery('#arrestedPersonLinkId', container).length && record.id > 0) {
        jQuery('#arrestedPersonLinkId', container)
            .attr('href', getBaseURL() + 'contacts/edit/' + record.id)
            .removeClass('d-none');
    } else {
        jQuery('#arrestedPersonLinkId', container).addClass('d-none');
    }
}
</script>