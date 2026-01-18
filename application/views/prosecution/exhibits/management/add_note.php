<div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="addNoteModalLabel" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="addNoteModalLabel">Add Exhibit Note</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addNoteForm" enctype="multipart/form-data">
                    <input type="hidden" name="exhibit_id" id="noteExhibitId" value=<?php echo $id?>>

                    <div class="row">
                        <div class="col-md-8">
                             <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="" required >
                                <small class="form-text text-muted">What is the subject</small>
                            </div>
                            <!-- Note Content -->
                            <div class="form-group">
                                <label for="note_content">Note Text*</label>
                                <textarea class="form-control" id="note_content" name="remarks" rows="6" required placeholder="Enter detailed notes about the exhibit..."></textarea>
                            </div>

                            <!-- Attachments -->
                            <div class="form-group">
                                <label for="note_attachments">Attachments</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="note_attachments" name="attachments[]" multiple>
                                    <label class="custom-file-label" for="note_attachments">Choose files</label>
                                </div>
                                <small class="form-text text-muted">Max 5MB per file. Allowed types: PDF, JPG, PNG, DOCX</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Metadata -->
                            <div class="form-group">
                                <label for="note_date">Date/Time*</label>
                                <input type="datetime-local" class="form-control" id="note_date" name="createdOn" required value="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>

                            <div class="form-group">
                                <label for="note_type">Note Type*</label>
                                <select class="form-control" id="note_type" name="note_type" required>
                                    <option value="General">General Note</option>
                                    <option value="Condition">Condition Update</option>
                                    <option value="Discrepancy">Discrepancy Report</option>
                                    <option value="Action">Action Taken</option>
                                    <option value="Legal">Legal Note</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="note_priority">Priority</label>
                                <select class="form-control" id="note_priority" name="priority">
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="note_tags">Tags</label>
                                <input type="text" class="form-control" id="note_tags" name="tags" placeholder="e.g., damage, analysis, court">
                                <small class="form-text text-muted">Separate with commas</small>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="note_requires_followup" name="requires_followup">
                                <label class="form-check-label" for="note_requires_followup">Requires Follow-up</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
