<div class="modal fade" id="logEntryModal" tabindex="-1" role="dialog" aria-labelledby="logEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logEntryModalLabel">Add Investigation Log Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="logEntryForm"  method="post" action="" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" name="case_id" id="case_id" value="<?=$id ?>">

                    <div class="form-group" id="entryDate">
                        <label for="logEntryDate">Date</label>
                        <input type="text" class="form-control date datepicker" id="logEntryDate" name="logEntryDate" required>
                    </div>

                    <div class="form-group">
                        <label for="actionTaken">Action Taken</label>
                        <select name="actionTaken" id="actionTaken" class="form-control" required>
                            <option value="">-- Select Action Taken --</option>
                            <option value="statement_recorded">Statement Recorded</option>
                            <option value="scene_visited">Scene Visited</option>
                            <option value="suspect_interrogated">Suspect Interrogated</option>
                            <option value="evidence_collected">Evidence Collected</option>
                            <option value="witness_interviewed">Witness Interviewed</option>
                            <option value="report_filed">Report Filed</option>
                            <option value="referral_made">Referral Made</option>
                            <option value="caution_issued">Caution Issued</option>
                            <option value="arrest_made">Arrest Made</option>
                            <option value="case_handed_over">Case Handed Over</option>
                            <option value="follow_up_scheduled">Follow-Up Scheduled</option>
                            <option value="search_conducted">Search Conducted</option>
                            <option value="review_meeting_held">Review Meeting Held</option>
                            <option value="charges_recommended">Charges Recommended</option>
                            <option value="case_closed">Case Closed</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="logEntryDetails">Details</label>
                        <textarea class="form-control" id="logEntryDetails" name="logEntryDetails" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="uploadDoc">Attachments</label>
                        <input type="file" class="form-control-file" id="uploadDoc" name="uploadDoc" multiple>
                    </div>

                    <input type="hidden" id="logEntryId" name="logEntryId">
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
   jQuery(document).ready(function () {
    var case_id=<?=$id ?>;
    setDatePicker('#logEntryDate', jQuery("#logEntryForm"));

       jQuery('#logEntryForm').on('submit', function (e) {
            let isValid = true;
            let errorMessages = [];

            // Validate Date
            const date =jQuery('#logEntryDate').val().trim();
            if (!date) {
                isValid = false;
                errorMessages.push("Date is required.");
            }

            // Validate Action Taken
            const actionTaken =jQuery('#actionTaken').val();
            if (!actionTaken) {
                isValid = false;
                errorMessages.push("Please select an action taken.");
            }

            // Validate Details
            const details =jQuery('#logEntryDetails').val().trim();
            if (!details) {
                isValid = false;
                errorMessages.push("Details are required.");
            }

            if (!isValid) {
                e.preventDefault(); // Stop form submission
                alert(errorMessages.join("\n"));
            }
        });

        //send request

           jQuery('#logEntryForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission
                let form =jQuery('#logEntryForm')[0];
                let formData = new FormData(form);


               // Show global loader
                   jQuery('#loader-global').show();

               jQuery.ajax({
                    url: 'cases/process_investigation_log',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        jQuery('#loader-global').hide();
                          pinesMessage({ ty: 'success', m: "Log entry saved successfully!" });
                       
                        jQuery('#logEntryModal').modal('hide');
                       // load_investigation_log(case_id);
                        jQuery('#logEntryForm')[0].reset();
                    },
                    error: function (xhr) {
                        jQuery('#loader-global').hide();
                         pinesMessage({ ty: 'error', m: "Error saving log entry!" + xhr.responseText });
                       
                    }
                });
            });
        });

</script>
