<div class="modal fade" id="newConveyancingModal" tabindex="-1" role="dialog" aria-labelledby="newConveyancingModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newConveyancingModalLabel"><?php echo $title?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                echo form_open("", 'name="conveyancingForm" id="conveyancing-form" method="post" class="form-horizontal"');
                // Hidden fields
                echo form_input(["name" => "id", "value" => $conveyancingData["id"] ?? '', "type" => "hidden"]);
                echo form_input(["name" => "user_id", "value" => $conveyancingData["createdBy"] ?? '', "type" => "hidden"]);
                echo form_input(["name" => "archived", "value" => $conveyancingData["archived"]??'no', "type" => "hidden"]);
                echo form_input(["name" => "send_notifications_email", "value" => $hide_show_notification ?? 'no', "type" => "hidden"]);
                echo form_input(["name" => "clone", "value" => "no", "type" => "hidden"]);
                echo form_input(["name" => "status", "value" =>$conveyancingData["archived"]?? "pending", "type" => "hidden"]);
                echo form_input(["name" => "channel", "value" => "CP", "type" => "hidden"]);
                ?>

                <div class="form-group">
                    <label for="title">Title</label>
                    <?php echo form_input([
                        "name" => "title",
                        "id" => "title",
                        "class" => "form-control",
                        "value"=> $conveyancingData["title"] ?? '',
                        "placeholder" => "E.g., Purchase of Property ABC",
                        "required" => true
                    ]); ?>
                    <div data-field="title" class="inline-error d-none"></div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="instrument_type_id"><?php echo $this->lang->line("instrument_type")?></label>
                        <?php
                        echo form_dropdown(
                            "instrument_type_id",
                                $types,
                                $conveyancingData["instrument_type"] ?? "",
                            'id="instrument_type_id" class="form-control select-picker" data-live-search="true" required'
                        );
                        ?>
                        <div data-field="instrument_type_id" class="inline-error d-none"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="transaction_type_id"><?php echo $this->lang->line("transaction_type")?></label>
                        <?php
                        echo form_dropdown(
                            "transaction_type_id",
                            $transaction_types,
                            $conveyancingData["transaction_type_id"] ?? $system_preferences["transaction_type_id"],
                            'id="transaction_type_id" class="form-control select-picker" data-live-search="true" required'
                        );
                        ?>
                        <div data-field="transaction_type_id" class="inline-error d-none"></div>
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-3">
                        <label for="contact_type">Seller Type</label>
                        <select class="form-control" id="contact_type" name="contact_type">
                            <option value="contact" <?php echo ($conveyancingData["contact_type"] == 'contact') ? 'selected' : ''; ?>>
                                <?php echo $this->lang->line("contact"); ?>
                            </option>
                            <option value="company" <?php echo ($conveyancingData["contact_type"] == 'company') ? 'selected' : ''; ?>>
                                <?php echo $this->lang->line("company_or_group"); ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-9">
                        <label for="parties">Seller/Vendor</label>
                        <?php echo form_input([
                            "name" => "parties",
                            "id" => "parties",
                            "value" => $conveyancingData["party_name"] ?? '',
                            "class" => "form-control lookup",
                            "placeholder" => "E.g. XYZ Bank",
                            "required" => true
                        ]); ?>
                        <input type="hidden" name="parties_id" id="parties_id" value="<?php echo isset($conveyancingData['parties_id']) ? $conveyancingData['parties_id'] : ''; ?>" />
                        <div data-field="parties" class="inline-error d-none"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="initiated_by"><?php echo $this->lang->line("staffName")?>/Chargee</label>
                        <?php echo form_input([
                            "name" => "initiated_by",
                            "id" => "initiated_by",
                            "value" => $conveyancingData["staff"] ?? '',
                            "class" => "form-control lookup",
                            "required" => true
                        ]);
                        echo form_input(["name" => "initiated_by_id", "id"=>'initiated_by_id', "value" => $conveyancingData["initiated_by"] ?? '', "type" => "hidden"]);
                        ?>
                        <div data-field="initiated_by" class="inline-error d-none"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="staff_pf_no"><?php echo $this->lang->line("pfNo") ?></label>
                        <?php echo form_input([
                            "name" => "staff_pf_no",
                            "id" => "staff_pf_no",
                            "value"=> $conveyancingData["staff_pf_no"] ?? '',
                            "class" => "form-control",
                            "required" => true
                        ]); ?>
                        <div data-field="staff_pf_no" class="inline-error d-none"></div>
                    </div>
                </div>

                <div class="form-row d-none">
                    <div class="col-md-12 p-0" data-language="javascript" id="due-date-container">
                        <div class="col-md-12 form-group p-0 row m-0 mb-10">
                            <label class="control-label col-md-3 pr-0 required col-xs-5 restriction-tooltip" id="dueDateLabelId"><?php echo $this->lang->line("DateInitiated");?></label>
                            <div class="col-md-8 pr-0 col-xs-10 " id="due-date-wrapper">
                                <div class="row m-0">
                                    <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form-due-date">
                                        <?php echo form_input(["name" => "date_initiated", "id" => "date_initiated", "placeholder" => "YYYY-MM-DD",  "value" =>$conveyancingData['date_initiated']?? date("Y-m-d"), "class" => "date start form-control"]);?>
                                        <span class="input-group-addon input-group-text"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                    </div>
                                </div>
                                <div data-field="date_initiated" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div><div class="form-group col-md-6">
                        <label for="amount_requested"><?php echo $this->lang->line("amountRequested")?></label>
                        <?php
                        $requested=isset($conveyancingData['amount_requested'])&&$conveyancingData['amount_requested']>1?$conveyancingData['amount_requested']:1;
                        echo form_input([
                            "name" => "amount_requested",
                            "id" => "amount_requested",
                            "class" => "form-control",
                            "type" => "number",
                            "value"=>$requested,
                            "step" => "0.01",
                            "required" => true
                        ]); ?>
                        <div data-field="amount_requested" class="inline-error d-none"></div>
                    </div>

                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="property_value"><?php echo $this->lang->line("propertyValue") ?></label>
                        <?php echo form_input([
                            "name" => "property_value",
                            "id" => "property_value",
                            "class" => "form-control",
                            "type" => "number",
                            "value"=>$conveyancingData['property_value']?? 0,
                            "step" => "0.01",
                            "required" => true
                        ]); ?>
                        <div data-field="property_value" class="inline-error d-none"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="amount_approved"><?php echo $this->lang->line("amountApproved") ?></label>
                        <?php echo form_input([
                            "name" => "amount_approved",
                            "id" => "amount_approved",
                            "value"=>$conveyancingData["amount_approved"]?? 0,
                            "class" => "form-control",
                            "type" => "number",
                            "step" => "0.01"
                        ]); ?>
                        <div data-field="amount_approved" class="inline-error d-none"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Notes</label>
                    <?php echo form_textarea([
                        "name" => "description",
                        "id" => "description",
                        "class" => "form-control",
                        "value"=>$conveyancingData['description']??"",
                        "rows" => "3",
                        "required" => true
                    ]); ?>
                    <div data-field="description" class="inline-error d-none"></div>
                </div>
                <div class="form-group ">
                    <label for="reference_number"><?php echo $this->lang->line("ref_number")?></label>
                    <?php echo form_input([
                        "name" => "reference_number",
                        "id" => "reference_number",
                        "class" => "form-control",
                        "value"=>$conveyancingData['reference_number']??"",
                        "required" => true
                    ]); ?>
                    <div data-field="reference_number" class="inline-error d-none"></div>
                </div>

                <div class="form-group">
                    <label for="documents">Attach Documents</label>
                    <div class="custom-file">
                        <?php echo form_upload([
                            "name" => "documents[]",
                            "id" => "documents",
                            "class" => "custom-file-input",
                            "multiple" => true
                        ]); ?>
                        <label class="custom-file-label" for="documents">Choose files...</label>
                    </div>
                </div>

                <?php echo form_close()?>
            </div>

            <div class="modal-footer">
                <div>
                    <span class="loader-submit"></span>
                    <div class="btn-group">
                        <button type="button" class="btn btn-save btn-add-dropdown modal-save-btn" id="save-conveyancing-btn"><?php echo $this->lang->line("save");?></button>
                        <?php if (!isset($conveyancingData["id"]) || !$conveyancingData["id"]): ?>
                            <button type="button" class="btn btn-save dropdown-toggle btn-add-dropdown modal-save-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:;" onclick="cloneDialog(jQuery('#conveyancing-dialog'), ConveyancingFormSubmit);"><?php echo $this->lang->line("create_another"); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-link" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {

        $('.custom-file-input').on('change', function() {
            var files = $(this)[0].files;
            var label = files.length > 1 ? files.length + ' files selected' : files[0].name;
            $(this).next('.custom-file-label').html(label);
        });
    });
</script>