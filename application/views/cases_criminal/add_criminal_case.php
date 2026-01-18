<!-- Modal -->
<div class="primary-style">
    <div class="modal fade modal-container modal-resizable">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php  echo $this->lang->line("case_intake_form");?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
            <div class="modal-body ">
                <div id="litigation-add-form-container" class="col-md-12 m-0 p-0 padding-10">
                   <?php
                   echo form_open(current_url(), 'class="form-horizontal" novalidate id="litigation-add-form"');
                    echo form_input(["id" => "action", "value" => "add_criminal_case", "type" => "hidden"]);
                    echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => $hide_show_notification, "type" => "hidden"]);
                    echo form_input(["id" => "all-users-provider-group", "value" => $allUsersProviderGroupId, "type" => "hidden"]);
                    echo form_input(["name" => "externalizeLawyers", "id" => "externalizeLawyers", "value" => "no", "type" => "hidden"]);
                    echo form_input(["name" => "category", "id" => "category", "value" => $selected_values["category"], "type" => "hidden"]);
                    echo form_input(["id" => "arrival-date-hidden", "value" => $selected_values["today"], "type" => "hidden"]);
                    echo form_input(["id" => "filed-on-hidden", "type" => "hidden"]);
                    echo form_input(["id" => "due-date-hidden", "value" => isset($selected_values["dueDate"]) ? $selected_values["dueDate"] : "", "type" => "hidden"]);
                    echo form_input(["name" => "assignment_id", "id" => "assignment-id", "value" => $assignments["id"]??=0, "type" => "hidden"]);
                    echo form_input(["name" => "assignment_relation", "id" => "assignment-relation", "value" => $assignments["assignment_relation"]??=0, "type" => "hidden"]);
                    echo form_input(["name" => "user_relation", "id" => "user-relation", "value" => $assignments["user_id"]??=0, "type" => "hidden"]);
                    echo form_input(["id" => "related-assigned-team", "value" => $assignments["assigned_team"], "type" => "hidden"]);
                    ?>
                    <!-- Category / Broad Nature of Cases -->
                    <div class="form-group">
                        <label  class="control-label required" for="caseCategory">Category / Broad Nature of Cases</label>
                        <select id="caseCategory" class="form-control select-picker" required>
                            <option value="" disabled selected>Select Category</option>
                            <option value="Telecommunications offences">Telecommunications offences</option>
                            <option value="SIM Card offences">SIM Card offences</option>
                            <option value="Type Approval/Equipment offences">Type Approval/Equipment offences</option>
                            <option value="Electronic Transactions/Systems">Electronic Transactions/Systems</option>
                            <option value="Radio Communication/Frequency">Radio Communication/Frequency</option>
                            <option value="Broadcasting offences">Broadcasting offences</option>
                            <option value="Postal/Courier offences">Postal/Courier offences</option>
                            <option value="Consumer Protection Offences">Consumer Protection Offences</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <!-- Case Type Selection -->
                    <div class="form-group">
                        <label  class="control-label required" for="caseType">Case Type</label>
                        <select id="caseType" class="form-control select-picker"  onchange="updateForm()" required>
                            <option value="" disabled selected>Select Case Type</option>
                            <option value="Complaints/Inquiry">Complaints/Inquiry</option>
                            <option value="Surveillance/Detection">Surveillance/Detection</option>
                            <option value="Investigations & Enforcement">Investigations & Enforcement</option>
                        </select>
                    </div>

                    <!-- Common Fields -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label  class="control-label required" for="subject">Subject</label>
                            <input type="text" id="subject" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label  class="control-label" for="date">Date</label>
                            <input type="date" id="date" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label  class="control-label" for="natureOfCase">Nature of Case</label>
                            <input type="text" id="natureOfCase" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label  class="control-label" for="suspectName">Suspect/Accused Name</label>
                            <input type="text" id="suspectName" class="form-control">
                        </div>
                        <div class="col-12 form-group">
                            <label  class="control-label" for="caseSummary">Case Summary</label>
                            <textarea id="caseSummary" class="form-control" required></textarea>
                        </div>
                    </div>

                    <!-- Dynamic Fields Container -->
                    <div id="dynamicFields" class="row"></div>

                        <div class="modal-footer justify-content-between">
                            <?php $this->load->view("templates/send_email_option_template", ["type" => "add_litigation_case", "container" => "#litigation-dialog", "hide_show_notification" => $hide_show_notification]);?>
                            <div>
                                <span class="loader-submit"></span>
                                <button type="button" class="btn save-button modal-save-btn btn-info" id="case-submit"><?php  echo $this->lang->line("save");?></button>
                                <button type="button" class="close_model no_bg_button" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                            </div>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    function updateForm() {
        const caseType = document.getElementById("caseType").value;
        const dynamicFields = document.getElementById("dynamicFields");
        dynamicFields.innerHTML = "";

        if (caseType === "Complaints/Inquiry") {
            dynamicFields.innerHTML = `
                <div class="col-md-6 form-group">
                    <label  class="control-label" for="complainant">Complainant</label>
                    <input type="text" id="complainant" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label  class="control-label" for="caseNumber">Case/Inquiry No.</label>
                    <input type="text" id="caseNumber" class="form-control">
                </div>
                <div class="col-12 form-group">
                    <label  class="control-label" for="remarks">Remarks</label>
                    <textarea id="remarks" class="form-control"></textarea>
                </div>
            `;
        }

        if (caseType === "Surveillance/Detection") {
            dynamicFields.innerHTML = `
                <div class="col-md-6 form-group">
                    <label  class="control-label" for="operator">Operator/Service Provider Name</label>
                    <input type="text" id="operator" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label  class="control-label" for="contactPerson">Contact Person</label>
                    <input type="text" id="contactPerson" class="form-control">
                </div>
                <div class="col-12 form-group">
                    <label  class="control-label" for="findings">Findings/Current Status</label>
                    <textarea id="findings" class="form-control"></textarea>
                </div>
            `;
        }

        if (caseType === "Investigations & Enforcement") {
            dynamicFields.innerHTML = `
                <div class="col-12 form-group">
                    <label  class="control-label" for="allegationDetails">Allegation/Complaint Details</label>
                    <textarea id="allegationDetails" class="form-control" required></textarea>
                </div>
                <div class="col-md-6 form-group">
                    <label  class="control-label" for="suspectAddress">Suspect Address</label>
                    <input type="text" id="suspectAddress" class="form-control">
                </div>
                <div class="col-12 form-group">
                    <label  class="control-label" for="briefFacts">Brief Facts</label>
                    <textarea id="briefFacts" class="form-control"></textarea>
                </div>
            `;
        }
    }
</script>