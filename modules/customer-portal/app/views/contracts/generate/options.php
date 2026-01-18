<div class="row no-margin col-md-12 no-margin p-0 padding-10 list-options">
    <?php echo form_input(["name" => "option", "id" => "option", "value" => "", "type" => "hidden"]); ?>

    <div class="col-md-6">
        <div class="option-div option1-div" onclick="contractActionEvent('choose', '#contract-generate-container');">
            <img src="assets/images/contract/questionnaire.png" class="option" />
            <span class="img-label"><?php echo $this->lang->line("cp_create_mou_from_questionnaire"); ?></span>
        </div>
    </div>

    <div class="col-md-6">
        <div class="option-div option2-div" onclick="contractActionEvent('add', '#contract-generate-container');">
            <img src="assets/images/contract/metadata.png" class="option" />
            <span class="img-label"><?php echo $this->lang->line("cp_create_mou"); ?></span>
        </div>
    </div>
</div>

<div data-field="generate_contract" class="inline-error d-none"></div>