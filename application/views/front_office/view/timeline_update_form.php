<!-- Update Progress Modal -->
<div class="primary-style">
    <div class="modal fade modal-container modal-resizable">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProgressModalLabel">Update Process Stage</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="progressForm">
                        <div class="form-group" id="stageSelectContainer">
                            <label id="process_id" for="process_id">Timeline stage</label>
                            <?php echo  form_dropdown("stage_process_id", $stages, "", 'id="stageSelect" data-live-search="true" class="form-control select-picker" data-size="'. $this->session->userdata("max_drop_down_length").'"');?>
                        </div>
                        <div data-field="stageSelect" class="inline-error d-none"></div>
                        <div class="form-group">
                            <label for="stageStatus">Status</label>
                            <select class="form-control" id="stageStatus" required>
                                <option value="">Select status</option>
                                <option value="completed">Completed</option>
                                <option value="current">Mark as Current</option>
                                <option value="pending">Pending</option>
                            </select>
                            <div data-field="stageStatus" class="inline-error d-none"></div>
                        </div>
                        <div class="form-group">
                            <label for="stageDetails">Update Details</label>
                            <textarea class="form-control" id="stageDetails" rows="3" required></textarea>
                        </div>
                        <div data-field="stageDetails" class="inline-error d-none"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveProgressBtn">Save Update</button>
                </div>
            </div>
        </div>
    </div>
</div>