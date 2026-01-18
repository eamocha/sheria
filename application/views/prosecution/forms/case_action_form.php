<div class="modal fade" id="caseActionModal" tabindex="-1" role="dialog" aria-labelledby="caseActionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caseActionModalLabel-header">Take Action on Case</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="caseActionForm">
                    <div class="form-group">
                        <label for="caseActionRemarks">Remarks</label>
                        <textarea class="form-control" id="caseActionRemarks" name="caseActionRemarks" rows="3" placeholder="Enter your remarks here" required></textarea>
                    </div>
                    <input type="hidden" id="caseActionId" name="caseActionId" value="<?php echo $closure_recommendation['id']??0; ?>">
                </form>
            </div>
            <!-- add attachment here-->
              <div class="clear clearfix clearfloat"></div>
                    <hr class="col-md-12 p-0"/>
                    <div class="form-group col-md-12" id="attachments-container">
                        <label><i class="fa-solid fa-paperclip"></i>&nbsp;  <?php echo $this->lang->line("attach_file");?></label>
                        <div id="attachments" class="">
                            <div class="col-md-11">
                                <input id="attachment" name="attachment" type="file" value="" class="margin-top" />
                            </div>
                            
                        </div>

                    </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-success approval" id="approveCaseBtn" onclick="submitCaseAction('Approved','Approving')">Approve Case</button>
                <button type="button" class="btn btn-warning approval" id="recommendInvestigationBtn" onclick="submitCaseAction('Further action recommended','Approving')">Recommend Further Investigation</button>
                <button type="button" class="btn btn-danger approval" id="closeCaseBtn" onclick="submitCaseAction('Closed','Approving')">Close Case</button>

                <button type="button" class="btn btn-success recommendation" id="recommendCaseApprovalBtn" onclick="submitCaseAction('Recommend Approval','Recommending')">Recommend </button>
                <button type="button" class="btn btn-warning recommendation" id="recommendFurtherInvestigationBtn" onclick="submitCaseAction('Further action recommended','Recommending')"> Further Investigation</button>
                <button type="button" class="btn btn-danger recommendation" id="recommendCloseCaseBtn" onclick="submitCaseAction('Closed','Recommending')">Closure</button>

            </div>
        </div>
    </div>
</div>