<!-- Add Legal Case Risk Modal -->
<div class="modal fade" id="addCaseRiskModal" tabindex="-1" role="dialog" aria-labelledby="addCaseRiskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="addCaseRiskModalLabel">
                    <i class="fa fa-exclamation-triangle"></i> <?php echo $title; ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Form -->
            <form id="caseRiskForm" method="post" action="<?php echo base_url('cases/save_case_risk'); ?>">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $risk['id'] ?? ''; ?>">
                    <input type="hidden" name="case_id" value="<?php echo $risk['case_id'] ?? ''; ?>">

                    <div class="form-row">
                        <!-- Risk Category -->
                        <div class="form-group col-md-6">
                            <label for="riskCategory">Risk Category <span class="text-danger">*</span></label>
                            <select class="form-control" id="riskCategory" name="risk_category" required>
                                <option value="">-- Select Category --</option>
                                <?php
                                $categories = ['Legal','Financial','Operational','Reputational','Strategic','Compliance','Other'];
                                foreach ($categories as $cat) {
                                    $selected = (!empty($risk['risk_category']) && $risk['risk_category'] === $cat) ? 'selected' : '';
                                    echo "<option value=\"$cat\" $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Risk Level -->
                        <div class="form-group col-md-6">
                            <label for="riskLevel">Risk Level <span class="text-danger">*</span></label>
                            <select class="form-control" id="riskLevel" name="riskLevel" required>
                                <option value="">-- Select Level --</option>
                                <?php
                                $levels = ['Low','Medium','High','Critical'];
                                foreach ($levels as $lvl) {
                                    $selected = (!empty($risk['riskLevel']) && $risk['riskLevel'] === $lvl) ? 'selected' : '';
                                    echo "<option value=\"$lvl\" $selected>$lvl</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Risk Description -->
                    <div class="form-group">
                        <label for="details">Risk Description</label>
                        <textarea class="form-control" id="details" name="details" rows="3" placeholder="Describe the risk..."><?php echo $risk['details'] ?? ''; ?></textarea>
                    </div>

                    <!-- Mitigation / Action Plan -->
                    <div class="form-group">
                        <label for="mitigation">Mitigation / Action Plan</label>
                        <textarea class="form-control" id="mitigation" name="mitigation" rows="3" placeholder="How will this risk be managed?"><?php echo $risk['mitigation'] ?? ''; ?></textarea>
                    </div>
                    <?php
                    if ($risk['id'] && $risk['id']>0 ){?>

                    <div class="form-group">
                        <label for="mitigation">Action Taken</label>
                        <textarea class="form-control" id="actionTaken" name="actionTaken" rows="3" placeholder="Update on resolution"><?php echo $risk['actionTaken'] ?? ''; ?></textarea>
                    </div>
                    <?php } ?>
                    <div class="form-row">
                        <!-- Responsible Person -->
                        <div class="form-group col-md-6">
                            <label for="responsible">Responsible Person</label>
                            <input type="text" class="form-control lookup" id="responsible"
                                   name="responsible" placeholder="Enter responsible person"
                                   value="<?php echo $risk['responsible_name'] ?? ''; ?>">
                            <input type="hidden" name="responsible_actor_id"
                                   value="<?php echo $risk['responsible_actor_id'] ?? 0; ?>">
                        </div>

                        <!-- Status -->
                        <div class="form-group col-md-6">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <?php
                                $statuses = ['Open','In Progress','Resolved','Closed'];
                                foreach ($statuses as $st) {
                                    $selected = (!empty($risk['status']) && $risk['status'] === $st) ? 'selected' : '';
                                    echo "<option value=\"$st\" $selected>$st</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveLegalCaseRiskBtn">
                        <i class="fa fa-save"></i> Save</button>
                </div>
            </form>

        </div>
    </div>
</div>
