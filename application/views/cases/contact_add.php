<div id="add-case-contact-container" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo htmlspecialchars($title) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <?php echo form_open("", 'id="contactAddForm" name="caseContactAddForm" method="post" class="form-horizontal no-margin"') ?>
                <?php echo form_input(['name' => 'action', 'value' => 'addContact', 'type' => 'hidden']) ?>
                <?php echo form_input(['name' => 'case_id', 'value' => $id, 'type' => 'hidden']) ?>
                <?php echo form_input(['name' => $type, 'id' => 'contactType', 'type' => 'hidden']) ?>
                <?php echo form_input(['name' => 'roleChanged_OnTheFly', 'class' => 'roleChanged_OnTheFly', 'type' => 'hidden']) ?>

                <div class="col-md-12 form-group no-padding" id="contact-lookup-container">
                    <label class="required margin-bottom-5"><?php echo htmlspecialchars($field_name) ?></label>
                    <?php echo form_input(['name'  => $field_name_id, 'id'    => 'contactId', 'type'  => 'hidden', 'value' => $model_data["id"]]) ?>

                    <?php
                    $fullName = '';
                    if (!empty($model_data["firstName"])) {
                        $fullName .= $model_data["firstName"];
                        if (!empty($model_data["father"])) $fullName .= " " . $model_data["father"];
                        $fullName .= " " . $model_data["lastName"];
                    }
                    ?>

                    <?php echo form_input(['name'  => '', 'id'    => 'contactName', 'value' => $fullName, 'class' => 'form-control lookup', 'placeholder' => $this->lang->line("start_typing"), 'title' => $this->lang->line("start_typing"), 'data-validation-engine' => "validate[required]"]) ?>

                    <div data-field="<?php echo htmlspecialchars($field_name_id) ?>" class="inline-error d-none"></div>
                </div>

                <div class="col-md-12 form-group no-padding">
                    <label class="margin-bottom-5"><?php echo $this->lang->line("role") ?></label>

                    <?php if ($type === "companyType"){ ?>
                        <a href="javascript:;" onclick="quickAdministrationDialog('case_company_roles', $('#add-case-contact-container'), true, '', newComapnyRecordAdded);" class="icon-alignment btn btn-link">
                            <i class="fa-solid fa-square-plus p-1 font-18"></i>
                        </a>
                        <?php echo form_dropdown(
                            "legal_case_company_role_id",
                            $contactRoles,
                            $model_data["legal_case_company_role_id"],
                            'id="role" class="form-control scenario_case_company_roles" data-field="administration-case_company_roles"'
                        ) ?>
                    <?php } elseif ($type === "contactType"){ ?>
                        <a href="javascript:;" onclick="quickAdministrationDialog('case_contact_roles', $('#add-case-contact-container'), true, '', newRecordAdded);" class="icon-alignment btn btn-link">
                            <i class="fa-solid fa-square-plus p-1 font-18"></i>
                        </a>
                        <?php echo form_dropdown("legal_case_contact_role_id", $contactRoles, $model_data["legal_case_contact_role_id"], 'id="role" class="form-control" data-field="administration-case_contact_roles" data-live-search="true"'
                    ) ?>
                        <div data-field="legal_case_contact_role_id" class="inline-error d-none"></div>
                    <?php }; ?>
                </div>

                <div class="col-md-12 form-group no-padding">
                    <label class="margin-bottom-5"><?php echo $this->lang->line("comments") ?></label>
                    <?php echo form_textarea(['name' => 'comments', 'id' => 'comments', 'class' => 'form-control', 'dir' => 'auto', 'rows' => '4', 'cols' => '0', 'value' => $model_data["comments"]
                    ]) ?>
                    <div data-field="comments" class="inline-error d-none"></div>
                </div>

                <?php echo form_close() ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line("cancel") ?></button>
                <button id="add-contact-dialog-submit" type="button" class="btn btn-primary"><?php echo $this->lang->line("save") ?></button>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($contactId)): ?>
    <script>
        disableFields('contact-lookup-container');
    </script>
<?php endif; ?>
