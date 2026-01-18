<div class="modal fade" id="editExhibitModal" tabindex="-1" role="dialog" aria-labelledby="editExhibitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editExhibitModalLabel">Edit Exhibit Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editExhibitForm">
                    <input type="hidden" id="edit_exhibitId" name="id">

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Basic Information -->
                            <div class="form-group">
                                <label for="edit_sno">Exhibit Number*</label>
                                <input type="text" class="form-control" id="edit_sno" name="sno" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_label_name">Label/Name*</label>
                                <input type="text" class="form-control" id="edit_label_name" name="label_name" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_description_of_exhibit">Description*</label>
                                <textarea class="form-control" id="edit_description_of_exhibit" name="description_of_exhibit" rows="3" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="edit_current_location">Current Location*</label>
                                <select class="form-control" id="edit_current_location" name="current_location" required>
                                    <option value="">Select location</option>
                                    <option value="Evidence Room A">Evidence Room A</option>
                                    <option value="Evidence Room B">Evidence Room B</option>
                                    <option value="Lab">Lab</option>
                                    <option value="Court">Court</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Case Information -->
                            <div class="form-group">
                                <label for="edit_caseReference">Case Reference*</label>
                                <input type="text" class="form-control" id="edit_caseReference" name="caseReference" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_date_received">Date Received*</label>
                                <input type="datetime-local" class="form-control" id="edit_date_received" name="date_received" required>
                            </div>

                            <div class="form-group">
                                <label for="edit_officers_involved">Officers Involved</label>
                                <select class="form-control select2-multiple" id="edit_officers_involved" name="officers_involved[]" multiple>
                                    <!-- Options will be populated via JS -->
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="edit_attachments">Attachments</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="edit_attachments" name="attachments[]" multiple>
                                    <label class="custom-file-label" for="edit_attachments">Choose files</label>
                                </div>
                                <small class="form-text text-muted">Max 10MB per file. Allowed types: PDF, JPG, PNG, DOCX</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>