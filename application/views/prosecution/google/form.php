<div class="container">
    <h2>Case Registration Form</h2>
    <form>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="originOfMatter">Origin of Matter</label>
                <select id="originOfMatter" class="form-control" required>
                    <option value="">Choose...</option>
                    <option value="public">Public</option>
                    <option value="surveillance">Surveillance</option>
                    <option value="request">Request for Enforcement</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="dateOfComplaint">Date of Complaint</label>
                <input type="date" class="form-control" id="dateOfComplaint" required>
            </div>
        </div>

        <div class="form-group">
            <label for="natureOfCase">Nature of Case/Inquiry</label>
            <textarea class="form-control" id="natureOfCase" rows="3" required></textarea>
            <small class="form-text text-muted">
                Briefly describe the nature of the case or inquiry.
            </small>
        </div>

        <fieldset class="border p-2 mb-3">
            <legend class="w-auto px-2">Complainant Details</legend>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="complainantName">Name</label>
                    <input type="text" class="form-control" id="complainantName" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="complainantAddress">Address</label>
                    <input type="text" class="form-control" id="complainantAddress" required>
                </div>
            </div>
            <div class="form-group">
                <label for="complainantContact">Contact</label>
                <input type="text" class="form-control" id="complainantContact">
            </div>
        </fieldset>

        <fieldset class="border p-2 mb-3">
            <legend class="w-auto px-2">Suspect/Accused Details</legend>
            <div id="accusedContainer">
                <div class="accused-entry">
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="accusedName1">Name</label>
                            <input type="text" class="form-control accusedName" id="accusedName1" name="accusedName[]" required>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="accusedAddress1">Address</label>
                            <input type="text" class="form-control accusedAddress" id="accusedAddress1" name="accusedAddress[]" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-success add-accused">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <div class="form-group">
            <label for="briefOfCase">Brief of the case/matter</label>
            <textarea class="form-control" id="briefOfCase" rows="5" required></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="approvalEnforcementDate">Approval of Enforcement Date</label>
                <input type="date" class="form-control" id="approvalEnforcementDate">
            </div>
            <div class="form-group col-md-6">
                <label for="investigatingOfficer">Investigating Officer</label>
                <select id="investigatingOfficer" class="form-control" required>
                    <option value="">Choose...</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="attachments">Attachments</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="attachments" multiple>
                <label class="custom-file-label" for="attachments">Choose file</label>
            </div>
            <small class="form-text text-muted">
                Attach any relevant documents (e.g., complaint letter).
            </small>
        </div>

        <button type="submit" class="btn btn-primary">Register Case</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Dynamic Accused Fields
        let accusedCount = 1;
        $(".add-accused").click(function() {
            accusedCount++;
            $("#accusedContainer").append(`
        <div class="accused-entry mt-2">
          <div class="form-row">
             <div class="form-group col-md-5">
              <label for="accusedName${accusedCount}">Name</label>
              <input type="text" class="form-control accusedName" id="accusedName${accusedCount}" name="accusedName[]" required>
            </div>
            <div class="form-group col-md-5">
              <label for="accusedAddress${accusedCount}">Address</label>
              <input type="text" class="form-control accusedAddress" id="accusedAddress${accusedCount}" name="accusedAddress[]" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="button" class="btn btn-sm btn-danger remove-accused">Remove</button>
            </div>
          </div>
        </div>
      `);
        });

        $("#accusedContainer").on("click", ".remove-accused", function() {
            $(this).closest(".accused-entry").remove();
        });

        // File Input Label Update
        $('#attachments').on('change', function() {
            let fileNames = "";
            for (let i = 0; i < this.files.length; i++) {
                fileNames += this.files[i].name + ", ";
            }
            fileNames = fileNames.slice(0, -2); // Remove last comma and space
            $(this).next('.custom-file-label').html(fileNames || "Choose file");
        });

        // Fetch Investigating Officers (Example - Replace with your CodeIgniter/AJAX)
        // $.ajax({
        //   url: '/your-codeigniter-controller/get-officers', // Replace with your actual URL
        //   method: 'GET',
        //   dataType: 'json',
        //   success: function(data) {
        //     let officerSelect = $('#investigatingOfficer');
        //     officerSelect.empty().append('<option value="">Choose...</option>');
        //     $.each(data, function(key, value) {
        //       officerSelect.append('<option value="' + key + '">' + value + '</option>');
        //     });
        //   }
        // });

        // Typeahead (Example - Requires a library like jQuery UI Autocomplete)
        // $('#natureOfCase').autocomplete({
        //   source: '/your-codeigniter-controller/get-offences' // Replace with your actual URL
        // });
    });
</script>