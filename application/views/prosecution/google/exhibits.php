<div class="">
    <h2>Exhibit Management</h2>

   
 <div class="mb-3">
        <button class="btn btn-success btn-sm" id="addNewExhibit">Add New Exhibit</button>
        <button class="btn btn-secondary btn-sm" id="refreshExhibits">Refresh List</button>
    </div>

    <div class="card">
        <div class="card-header btn-primary text-white">
            Exhibit List
        </div>
        <div class="card-body">
            <table id="exhibitTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr>
                    <th>S/NO.</th>
                    <th>CASE REFERENCE (FILE NO)</th>
                    <th>COURT (& court no)</th>
                    <th>PARTIES</th>
                    <th>DESCRIPTION OF EXHIBIT AND IDENTIFYING MARKINGS</th>
                    <th>DATE RECEIVED</th>
                    <th>TEMPORARY REMOVALS </th>
                    <th>MANNER OF DISPOSAL </th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <th>S/NO.</th>
                    <th>CASE REFERENCE (FILE NO)</th>
                    <th>COURT (& court no)</th>
                    <th>PARTIES</th>
                    <th>DESCRIPTION OF EXHIBIT AND IDENTIFYING MARKINGS</th>
                    <th>DATE RECEIVED</th>
                    <th>TEMPORARY REMOVALS (REASON AND DATE)</th>
                    <th>MANNER OF DISPOSAL (including dates)</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- exhibit modal -->
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
                            <input type="date" class="form-control" id="exhibitDateReceived" name="exhibitDateReceived">
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
                        <!-- attachments -->
                        <div class="form-group">
                            <label for="exhibitAttachments">Attachments</label>
                            <input type="file" class="form-control-file" id="uploadDoc" name="uploadDoc[]" multiple>
                        </div>  
                        
                        <input type="hidden" id="exhibitId" name="exhibitId">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        let exhibitTable = jQuery('#exhibitTable').DataTable({
            "responsive": true,
            "processing": true,
            // "serverSide": true, // Consider for large datasets
            // "ajax": {
            //   "url": "/your-codeigniter-controller/get-exhibits",
            //   "type": "POST"
            // },
            "columns": [
                { "data": "sno" },
                { "data": "caseReference" },
                { "data": "court" },
                { "data": "parties" },
                { "data": "description" },
                { "data": "dateReceived" },
                { "data": "temporaryRemovals" },
                { "data": "disposal" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return '<a href=<?php echo base_url("cases/exhibit_details/' + row.caseReference + '")?> class="btn btn-sm btn-info"> Details</a>'; // Adapt URL
                    }
                }
            ],
            "data": [
                { "sno": 1, "caseReference": "CF E517/2022", "court": "Kisumu Law Courts", "parties": "Republic vs. AUSTIN ACTION JOHN", "description": "Establishing FM Station without a valid license", "dateReceived": "2022-03-10", "temporaryRemovals": "", "disposal": "" }
            ]
        });

        jQuery('#addNewExhibit').on('click', function() {
           // alert('Functionality to add new exhibits will is within the Case Details screen. This will me moved');
            //show the modal to add a new exhibit
          jQuery('#exhibitModalLabel').text('Add Exhibit');
            jQuery('#exhibitForm')[0].reset(); // Clear form
            jQuery('#exhibitModal').modal('show');
        });

        jQuery('#refreshExhibits').on('click', function() {
            exhibitTable.ajax.reload(); // If using server-side processing
            // or
            // window.location.reload();
        });
    });
</script>