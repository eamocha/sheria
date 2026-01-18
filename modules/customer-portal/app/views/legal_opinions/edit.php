<div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog" aria-labelledby="editRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRequestModalLabel">Edit Request - LO-2023-001</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editRequestForm">
                    <div class="form-group">
                        <label for="editSubject">Subject</label>
                        <input type="text" class="form-control" id="editSubject" value="Contract Review for ABC Project" required>
                    </div>

                    <div class="form-group">
                        <label for="editBackground">Introduction/Background</label>
                        <textarea class="form-control" id="editBackground" rows="3" required>The ABC Project involves a partnership with an international firm. We need to review the contract terms to ensure compliance with local laws and protect our interests.</textarea>
                    </div>

                    <div class="form-group">
                        <label for="editDetails">Detailed Information</label>
                        <textarea class="form-control" id="editDetails" rows="5" required>The contract is for a 2-year engagement with deliverables phased quarterly. Key concerns include liability clauses, termination conditions, and intellectual property rights. The international partner is based in Germany, so we need to consider cross-border legal implications.</textarea>
                    </div>

                    <div class="form-group">
                        <label for="editQuestion">Legal Issue/Question</label>
                        <textarea class="form-control" id="editQuestion" rows="3" required>1. Are the liability clauses in section 4.2 compliant with our local regulations?
2. Do the termination conditions in section 7 provide adequate protection?
3. How should we handle the intellectual property rights specified in section 5.3?</textarea>
                    </div>

                    <div class="form-group">
                        <label for="editDueDate">Due Date</label>
                        <input type="date" class="form-control" id="editDueDate" value="2023-05-30" required>
                    </div>

                    <div class="form-group">
                        <label for="editPriority">Priority</label>
                        <select class="form-control" id="editPriority" required>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high" selected>High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>