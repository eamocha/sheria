<div class="container">
    <h2>Exhibit Details</h2>

    <div class="mb-3">
        <a href=<?php echo base_url("cases/exhibits")?> class="btn btn-secondary btn-sm">Back to exhibit list</a>
    </div>

    <div class="card">
        <div class="card-header btn-primary text-white">
            Exhibit information
        </div>
        <div class="card-body">
            <form id="exhibitDetailsForm">
                <div class="form-group">
                    <label for="caseReference">Case reference (file no)</label>
                    <input type="text" class="form-control" id="caseReference" value="CF E517/2022" readonly>
                </div>
                <div class="form-group">
                    <label for="court">Court (& court no)</label>
                    <input type="text" class="form-control" id="court" value="Kisumu Law Courts" readonly>
                </div>
                <div class="form-group">
                    <label for="parties">Parties</label>
                    <input type="text" class="form-control" id="parties" value="Republic vs. AUSTIN ACTION JOHN" readonly>
                </div>
                <div class="form-group">
                    <label for="description">Description of exhibit and identifying markings</label>
                    <textarea class="form-control" id="description" rows="3">Establishing FM Station without a valid license</textarea>
                </div>
                <div class="form-group">
                    <label for="dateReceived">Date received</label>
                    <input type="text" class="form-control date date-picker" id="date_received" value="<?php echo date("Y-m-d")?>" >
                </div>
                <div class="form-group">
                    <label for="temporaryRemovals">Temporary removals (reason and date)</label>
                    <textarea class="form-control" id="temporaryRemovals" rows="2"></textarea>
                </div>


                <div class="form-group">
                    <label for="disposal">Manner of disposal (including dates)</label>
                    <textarea class="form-control" id="disposal" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="recipientSignature">Upload signature of recipient</label>
                    <input type="file" class="form-control" id="recipientSignature" placeholder="Upload Signature">

                </div>
                <div class="form-group">
                    <label for="disposingOfficerSignature">Upload signature of officer disposing the exhibit</label>
                    <input type="file" class="form-control" id="disposingOfficerSignature" placeholder="Upload Signature">
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // --- Load Exhibit Details (Example - Adapt to your CodeIgniter backend) ---
        function loadExhibitDetails(caseReference) {
            // jQuery.ajax({
            //   url: '/your-codeigniter-controller/get-exhibit/' + caseReference,
            //   method: 'GET',
            //   dataType: 'json',
            //   success: function(data) {
            //     jQuery('#caseReference').val(data.caseReference);
            //     jQuery('#court').val(data.court);
            //     jQuery('#parties').val(data.parties);
            //     jQuery('#description').val(data.description);
            //     jQuery('#dateReceived').val(data.dateReceived);
            //     jQuery('#temporaryRemovals').val(data.temporaryRemovals);
            //     jQuery('#disposal').val(data.disposal);
            //     jQuery('#recipientSignature').val(data.recipientSignature);
            //     jQuery('#disposingOfficerSignature').val(data.disposingOfficerSignature);
            //   },
            //   error: function(xhr, status, error) {
            //     console.error('Error loading exhibit details:', error);
            //     alert('Failed to load exhibit details.');
            //   }
            // });
            // Dummy data from the provided snippet
            const exhibitData = {
                caseReference: "CF E517/2022",
                court: "Kisumu Law Courts",
                parties: "Republic vs. AUSTIN ACTION JOHN",
                description: "Establishing FM Station without a valid license",
                dateReceived: "", // Date not explicitly provided in this format
                temporaryRemovals: "",
                disposal: "",
                recipientSignature: "",
                disposingOfficerSignature: ""
            };
            jQuery('#caseReference').val(exhibitData.caseReference);
            jQuery('#court').val(exhibitData.court);
            jQuery('#parties').val(exhibitData.parties);
            jQuery('#description').val(exhibitData.description);
            jQuery('#dateReceived').val(exhibitData.dateReceived);
            jQuery('#temporaryRemovals').val(exhibitData.temporaryRemovals);
            jQuery('#disposal').val(exhibitData.disposal);
            jQuery('#recipientSignature').val(exhibitData.recipientSignature);
            jQuery('#disposingOfficerSignature').val(exhibitData.disposingOfficerSignature);
        }

        // Get caseReference from the URL (you'll need to implement your routing accordingly)
        const caseReference = window.location.pathname.split('/').pop(); // Example: /exhibits/CF E517/2022
        if (caseReference) {
            loadExhibitDetails(caseReference);
        }

        jQuery('#exhibitDetailsForm').submit(function(event) {
            event.preventDefault();
            const formData = jQuery(this).serialize();
            // AJAX call to save exhibit details
            // jQuery.ajax({
            //   url: '/your-codeigniter-controller/save-exhibit/' + caseReference,
            //   method: 'POST',
            //   data: formData,
            //   dataType: 'json',
            //   success: function(response) {
            //     alert('Exhibit details saved successfully!');
            //   },
            //   error: function(xhr, status, error) {
            //     console.error('Error saving exhibit details:', error);
            //     alert('Failed to save exhibit details.');
            //   }
            // });
            alert('Exhibit details saving functionality will be implemented here (backend).');
        });
    });
</script>