<?php
$actions_counter = 0;
$this->load->view("partial/header");
?>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a></li>
                <li class="breadcrumb-item"><a href="<?php echo site_url("case_types/index"); ?>"><?php echo $this->lang->line("case_type"); ?></a></li>
                <li class="breadcrumb-item active"><?php echo $this->lang->line("add_case_type"); ?></li>
            </ul>
        </div>
        <div id="action-counter" class="display-none">100</div>
        <div class="col-md-12">
            <?php echo form_open(current_url(), "novalidate id=\"caseTypeForm\""); ?>
            <div class="col-md-12 no-padding form-group">
                <h4><?php echo $this->lang->line("case_type"); ?></h4>
                <div class="form-group col-md-12 no-padding" style="margin-right: 10px; display: flex; justify-content: flex-end;">
                    <?php echo form_submit("submitBtn", $this->lang->line("save"), "class=\"btn btn-default btn-info\""); ?>
                    <?php echo form_reset("reset", $this->lang->line("reset"), "class=\"btn btn-default btn-link\""); ?>
                </div>
            </div>
            <?php echo form_input(["name" => "id", "value" => $this->case_type->get_field("id"), "id" => "id", "type" => "hidden"]); ?>
            <div class="col-md-12 no-padding row">
                <div class="form-group col-md-3 no-padding-left row">
                    <label class="control-label col-md-3 no-padding required"><?php echo $this->lang->line("name"); ?></label>
                    <div class="col-md-8 no-padding">
                        <?php echo form_input(["name" => "name", "value" => $this->case_type->get_field("name"), "id" => "name", "class" => "form-control", "placeholder" => $this->lang->line("name"), "data-rand-autocomplete" => "true", "maxlength" => "255", "required" => true, "data-validation-engine" => "validate[required]"]); ?>
                        <div class="margin-top">
                            <?php echo $this->case_type->get_error("name", "<div class=\"help-inline error\">", "</div>"); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 no-padding-left row">
                    <label class="control-label col-md-3 no-padding"><?php echo $this->lang->line("sla_optional"); ?></label>
                    <div class="col-md-8 no-padding">
                        <?php echo form_input(["name" => "litigationSLA", "value" => $this->case_type->get_field("litigationSLA"), "id" => "litigationSLA", "class" => "form-control", "data-validation-engine" => "validate[funcCall[validateInteger]]"]); ?>
                    </div>
                </div>
                <div class="form-group col-md-6 no-padding row">
                    <div class="checkbox col-md-12 no-padding">
                        <label class="col-md-3">
                            <input type="checkbox" id="corporate" name="corporate" value="<?php echo $this->case_type->get_field("corporate") ? $this->case_type->get_field("corporate") : "yes"; ?>"
                                <?php echo $this->case_type->get_field("corporate") == "yes" ? "checked='checked'" : ""; ?>
                                   onchange="jQuery('#corporate').val(this.checked ? 'yes' : 'no');">
                            <?php echo $this->lang->line("corporate_matter"); ?>
                        </label>
                        <label class="col-md-3">
                            <input type="checkbox" id="litigation" name="litigation" value="<?php echo $this->case_type->get_field("litigation") ? $this->case_type->get_field("litigation") : "yes"; ?>"
                                <?php echo $this->case_type->get_field("litigation") == "yes" ? "checked='checked'" : ""; ?>
                                   onchange="jQuery('#litigation').val(this.checked ? 'yes' : 'no');">
                            <?php echo $this->lang->line("litigation_case"); ?>
                        </label>
                        <label class="col-md-3">
                            <input type="checkbox" id="criminal" name="criminal" value="<?php echo $this->case_type->get_field("criminal") ? $this->case_type->get_field("criminal") : "yes"; ?>"
                                <?php echo $this->case_type->get_field("criminal") == "yes" ? "checked='checked'" : ""; ?>
                                   onchange="jQuery('#criminal').val(this.checked ? 'yes' : 'no');">
                            <?php echo $this->lang->line("criminal_case"); ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 less-field-divider">
                <hr>
            </div>
            <h4 class="col-md-12"><?php echo $this->lang->line("custome_due_date"); ?></h4>
            <div class="col-md-12 flex-row flex-wrap no-padding flex-stretch mt-20" id="add-due-condition">
                <?php foreach ($case_type_due_conditions as $key => $condition_value) { ?>
                    <?php $actions_counter = $actions_counter + 1; ?>
                    <div class="board-column col-md-2 form-group padding-all-10" id="new-action-container-<?php echo $actions_counter; ?>">
                        <div class="grey-box-container-confg-board">
                            <a onclick="jQuery(this).parent().parent().remove();" href="javascript:;" class="float-right mt-10">
                                <i class="icon-alignment fa fa-trash light_red-color pull-left-arabic font-15"></i>
                            </a>
                            <label class="mt-10"><?php echo $this->lang->line("priority"); ?></label>
                            <div class="" id="priority-wrapper-<?php echo $actions_counter; ?>">
                                <div class="">
                                    <div class="mt-10">
                                        <select name="conditions_data[priority][]" class="form-control select-picker" id="priority" data-live-search="true">
                                            <?php
                                            $selected = "";
                                            foreach ($priorities as $key => $value) {
                                                if ($condition_value["priority"] && $condition_value["priority"] == $key) {
                                                    $selected = "selected";
                                                } else {
                                                    if (!$condition_value["priority"] && $key == "medium") {
                                                        $selected = "selected";
                                                    } else {
                                                        $selected = "";
                                                    }
                                                }
                                                ?>
                                                <option data-icon="priority-<?php echo $key; ?>" <?php echo $selected; ?> value="<?php echo $key; ?>">
                                                    <?php echo $value; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <label class="mt-10"><?php echo $this->lang->line("due_in"); ?></label>
                            <div class="">
                                <div class="">
                                    <div class="">
                                        <?php echo form_input(["dir" => "auto", "name" => "conditions_data[due-in][]", "type" => "number", "id" => "due-in", "required" => "", "class" => "form-control", "value" => $condition_value["due_in"], "data-rand-autocomplete" => "true", "data-validation-engine" => "validate[required]"]); ?>
                                    </div>
                                    <div data-field="due-in" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>
                            <label class="mt-10"><?php echo $this->lang->line("client_name"); ?></label>
                            <div class="">
                                <div class="">
                                    <div class="">
                                        <select name="conditions_data[clientType][]" id="client-type<?php echo $actions_counter; ?>" class="form-control select-picker" tabindex="-1">
                                            <option value="all" <?php echo isset($condition_value["clientData"]["type"]) ? $condition_value["clientData"]["type"] == "all" ? "selected=\"selected\"" : "" : ""; ?>>
                                                <?php echo $this->lang->line("choose_all"); ?>
                                            </option>
                                            <option value="company" <?php echo isset($condition_value["clientData"]["type"]) ? $condition_value["clientData"]["type"] == "Company" ? "selected=\"selected\"" : "" : ""; ?>>
                                                <?php echo $this->lang->line("company_or_group"); ?>
                                            </option>
                                            <option value="contact" <?php echo isset($condition_value["clientData"]["type"]) ? $condition_value["clientData"]["type"] == "Person" ? "selected=\"selected\"" : "" : ""; ?>>
                                                <?php echo $this->lang->line("contact"); ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-10">
                                <div class="">
                                    <div class="">
                                        <?php echo form_input(["name" => "conditions_data[contact_company_id][]", "id" => "contact-company-id" . $actions_counter, "value" => $condition_value["clientData"]["member_id"] ?? "", "type" => "hidden"]); ?>
                                        <?php echo form_input(["name" => "conditions_data[clientLookup][]", "id" => "client-lookup" . $actions_counter, "value" => $condition_value["clientData"]["name"] ?? "", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]); ?>
                                        <div data-field="contact_company_id" class="inline-error d-none"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="">
                                    <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete"); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div></div>
                <div class="col-md-2 min-width-20vw" onclick="addNewCondition()" id="column-add">
                    <div class="add-new-column-board dashed-border flex-column">
                        <div class="add-icon-with-border">+</div>
                        <div><span><?php echo $this->lang->line("add"); ?></span></div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php $this->load->view("partial/footer"); ?>

<script>
    let actionsIdCounter = 100;
    let formIdArray = new Array();
    jQuery(document).ready(function () {
        let caseTypeData = <?php echo json_encode($case_type_due_conditions); ?>;
        jQuery('#caseTypeForm').validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        let addingActionCounterInDiv = jQuery('#action-counter').text();
        let addingActionCounterToPhp = parseInt(addingActionCounterInDiv) + 1;
        jQuery('.select-picker', jQuery('#main-container')).selectpicker({
            dropupAuto: false
        });
        for (let i = 1; i <= caseTypeData.length; i++) {
            clientInitializationCaseTypes(jQuery('#main-container'), { 'onselect': onCaseClientSelect }, i);
        }
    });

    function addNewCondition() {
        let addingActionCounterInDiv = jQuery('#action-counter').text();
        let addingActionCounterToPhp = parseInt(addingActionCounterInDiv) + 1;
        let counter = addingActionCounterToPhp;
        let actions_counter = addingActionCounterInDiv;
        var $chk = jQuery(`
            <div class="board-column col-md-2 form-group padding-all-10" id="new-action-container-${jQuery('#action-counter').text()}">
                <div class="grey-box-container-confg-board">
                    <a onclick="jQuery(this).parent().parent().remove();" href="javascript:;" class="float-right mt-10">
                        <i class="icon-alignment fa fa-trash light_red-color pull-left-arabic font-15"></i>
                    </a>
                    <label class="mt-10"><?php echo $this->lang->line("priority"); ?></label>
                    <div class="" id="priority-wrapper-${actions_counter}">
                        <div class="">
                            <div class="mt-10">
                                <select name="conditions_data[priority][]" class="form-control select-picker" id="priority${actions_counter}" data-live-search="true">
                                    <?php $selected = "";
                                    if (!isset($condition_value)) {
                                        $condition_value = ["priority" => "medium"];
                                    }
                                    foreach ($priorities as $key => $value) {
                                        if ($condition_value["priority"] && $condition_value["priority"] == $key) {
                                            $selected = "selected";
                                        } else {
                                            if (!$condition_value["priority"] && $key == "medium") {
                                                $selected = "selected";
                                            } else {
                                                $selected = "";
                                            }
                                        }
                                        ?>
                                        <option data-icon="priority-<?php echo $key; ?>" <?php echo $selected; ?> value="<?php echo $key; ?>">
                                            <?php echo $value; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <label class="mt-10"><?php echo $this->lang->line("due_in"); ?></label>
                        <div class="">
                            <div class="">
                                <?php echo form_input(["dir" => "auto", "name" => "conditions_data[due-in][]", "type" => "number", "id" => "due-in", "required" => "", "class" => "form-control", "value" => 0, "data-rand-autocomplete" => "true", "data-validation-engine" => "validate[required]"]); ?>
                            </div>
                            <div data-field="due-in" class="inline-error d-none padding-5"></div>
                        </div>
                    </div>
                    <div class="">
                        <label class="mt-10"><?php echo $this->lang->line("client_name"); ?></label>
                        <div class="">
                            <div class="">
                                <select name="conditions_data[clientType][]" id="client-type${actions_counter}" class="form-control select-picker" tabindex="-1">
                                    <option value="all" <?php echo true ? "selected=\"selected\"" : ""; ?>>
                                        <?php echo $this->lang->line("choose_all"); ?>
                                    </option>
                                    <option value="company" <?php echo false ? "selected=\"selected\"" : ""; ?>>
                                        <?php echo $this->lang->line("company_or_group"); ?>
                                    </option>
                                    <option value="contact" <?php echo false ? "selected=\"selected\"" : ""; ?>>
                                        <?php echo $this->lang->line("contact"); ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10">
                        <div class="">
                            <div class="">
                                <?php echo form_input(["name" => "conditions_data[contact_company_id][]", "id" => "contact-company-id" . $actions_counter, "value" => $condition_value["clientData"]["member_id"] ?? "", "type" => "hidden"]); ?>
                                <?php echo form_input(["name" => "conditions_data[clientLookup][]", "id" => "client-lookup" . $actions_counter, "value" => $condition_value["clientData"]["name"] ?? "", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]); ?>
                                <div data-field="contact_company_id" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="">
                            <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete"); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        `);
        $chk.insertBefore('#column-add');
        jQuery('.select-picker', jQuery('#main-container')).selectpicker({
            dropupAuto: false
        });
        clientInitializationCaseTypes(jQuery('#main-container'), { 'onselect': onCaseClientSelect }, addingActionCounterInDiv);
        actionsIdCounter = actionsIdCounter + 1;
        jQuery('#action-counter').html(actionsIdCounter);
    }

    function validateInteger(field, rules, i, options) {
        var val = field.val();
        var integerPattern = /^(?:[1-9]\d*|0)$/;
        if (!integerPattern.test(val)) {
            return _lang.integerAllowed;
        }
    }
</script>