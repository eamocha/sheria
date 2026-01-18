<div class="modal fade" id="addDocumentModal" tabindex="-1" role="dialog" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addDocumentModalLabel">Add New Document</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addDocumentForm" enctype="multipart/form-data">
                    <input type="hidden" name="exhibit_id" id="documentExhibitId">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doc_name">Document Name*</label>
                                <input type="text" class="form-control" id="doc_name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="doc_type">Document Type*</label>
                                <select class="form-control" id="doc_type" name="type" required>
                                    <option value="">Select type</option>
                                    <option value="Report">Report</option>
                                    <option value="Statement">Statement</option>
                                    <option value="Affidavit">Affidavit</option>
                                    <option value="Court Order">Court Order</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="doc_date">Document Date*</label>
                                <input type="datetime-local" class="form-control" id="doc_date" name="date" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doc_description">Description</label>
                                <textarea class="form-control" id="doc_description" name="description" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="doc_file">Upload Document*</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="doc_file" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                    <label class="custom-file-label" for="doc_file">Choose file</label>
                                </div>
                                <small class="form-text text-muted">Max file size: 10MB. Allowed types: PDF, DOC, DOCX, JPG, PNG</small>
                            </div>

                            <div class="form-group">
                                <label for="doc_confidential">Confidentiality Level</label>
                                <select class="form-control" id="doc_confidential" name="confidential">
                                    <option value="Public">Public</option>
                                    <option value="Restricted">Restricted</option>
                                    <option value="Confidential">Confidential</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
