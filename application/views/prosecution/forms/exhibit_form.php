 <div class="modal fade" id="exhibitModal" tabindex="-1" role="dialog" aria-labelledby="exhibitModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exhibitModalLabel">Add Exhibit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exhibitForm">
                         <input type="hidden" name="case_id" id="case_id" value="<?=$id ?>">
                        <div class="form-group">
                            <label for="exhibitName">Name</label>
                            <input type="text" class="form-control" id="exhibitName" name="exhibitName" required>
                        </div>
                        <div class="form-group">
                            <label for="exhibitDescription">Description</label>
                            <textarea class="form-control" id="exhibitDescription" name="exhibitDescription" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="exhibitDateReceived">Date Received</label>
                            <input type="text" class="form-control date datepicker" id="exhibitDateReceived" name="exhibitDateReceived">
                        </div>

                        <div class="form-group">
                            <label for="exhibitDisposal">Disposal</label>
                            <select class="form-control" id="exhibit
                            Disposal" name="exhibitDisposal" required>
                                <option value="">Choose...</option>
                                <option value="Confiscated">Confiscated</option>
                                <option value="Returned">Returned</option>
                                <option value="Destroyed">Destroyed</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exhibitTemporaryRemoval">Temporary Removal</label>
                            <input type="text" class="form-control" id="exhibitTemporaryRemoval" name="exhibitTemporaryRemoval" placeholder="Reason for temporary removal">
                        </div>
                        <div class="form-group">
                            <label for="exhibitStatus">Status</label>
                            <select class="form-control" id="exhibitStatus" name="exhibitStatus" required>
                                <option value="">Choose...</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group">
    <label for="uploadDoc">Attach Files</label>
    <input type="file" class="form-control-file" id="uploadDoc" name="uploadDoc[]" multiple>
</div>

                        <input type="hidden" id="exhibitId" name="exhibitId">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script type="text/javascript">
    jQuery(document).ready(function () {
    jQuery('#exhibitForm').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
       // formData.append("case_id", CURRENT_CASE_ID); 

        jQuery('#loader-global').show();

        jQuery.ajax({
            url: "cases/process_exhibit", // Adjust this endpoint as needed
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
              
                if (response.status) {
                    pinesMessageV2({ ty: 'success', m: "Exhibit saved successfully" });

                    jQuery('#exhibitModal').modal('hide');
                    jQuery('#exhibitForm')[0].reset();
                    // Optionally reload exhibit list
                } else {
                    pinesMessageV2({ ty: 'error', m: "Failed to save exhibit" });

                }
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
               error: defaultAjaxJSONErrorsHandler
        
        });
    });
});
</script>




<script>
    loadCaseExhibits(<?php echo $id;?>)
function renderExhibitTable(data) {
  const tbody = document.getElementById("exhibitTableBody");
  tbody.innerHTML = "";
  data.forEach((item, index) => {
    tbody.innerHTML += `
      <tr>
        <td>${index + 1}</td>
        <td>${item.exhibit_label}</td>
        <td>${item.description.replace(/\r?\n/g, "<br>")}</td>
        <td>${item.date_received}</td>
        <td>${item.manner_of_disposal}</td>
        <td>${item.temporary_removals}</td>
        <td>${item.creator_name}</td>
        <td>${item.createdOn}</td>
      </tr>`;
  });
}


/////
function loadCaseExhibits(caseId) {
jQuery.ajax({
    url:  "cases/get_case_exhibits", // adjust to your actual endpoint
    method: "POST",
    data: { case_id: caseId },
    dataType: "json",
    beforeSend: function () {
    jQuery("#loader-global").show();
    },
    success: function (response) {
    jQuery("#loader-global").hide();
      if (response.status && response.exhibits) {
        renderExhibitTable(response.exhibits);
      } else {
      jQuery("#exhibitTableBody").html('<tr><td colspan="8">No exhibits found.</td></tr>');
      }
    },
    error: function () {
    jQuery("#loader-global").hide();
      alert("An error occurred while loading exhibits.");
    },
  });
}

</script>