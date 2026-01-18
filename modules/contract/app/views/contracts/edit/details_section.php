<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="edit-container">
    <div class="contract-name-edit">
        <h4><?php echo $this->lang->line('contract_name'); ?>:</h4>
        <div class="form-group row">
            <?php echo form_input('name', $contract['name'], 'id="name" class="form-control first-input" dir="auto" autocomplete="off"'); ?>
            <div data-field="name" class="inline-error d-none"></div>
        </div>
    </div>
    <!-- add hidden input stage value $contract['stage'] -->
    <?php echo form_input(['name' => 'stage', 'id' => 'stage', 'value' => ucfirst($contract['stage']), 'type' => 'hidden']); ?>
    <!-- add category hidden input value $contract['category_id'] -->
    <?php echo form_input(['name' => 'category', 'id' => 'category', 'value' => $contract['category'], 'type' => 'hidden']); ?>
    <div class="contract-details-edit">
        <h4><?php echo $this->lang->line('contract_details'); ?></h4>
        <ul class="row no-margin p-0 clearfix info-section col-lg-12">
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('type'); ?>:</b>
                <div class="d-flex">
                    <?php echo form_dropdown('type_id', $types, $contract['type_id'], 'id="type" class="form-control select-picker" data-live-search="true" data-field="administration-contract_types"'); ?>
                    <a href="javascript:;" onclick="quickAdministrationDialog('contract_types', jQuery('#edit-container'), true, false, false, false, false, 'contract');" class="btn btn-link"><i class="fa-solid fa-circle-plus"></i></a>
                </div>
                <div data-field="type_id" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('sub_type'); ?>:</b>
                <?php echo form_dropdown('sub_type_id', isset($sub_types) ? $sub_types : [], isset($contract['sub_type_id']) && $contract['sub_type_id'] != '' && isset($sub_types[$contract['sub_type_id']]) ? $contract['sub_type_id'] : '', 'id="sub-type" class="form-control select-picker" data-live-search="true"'); ?>
            </li>
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('priority'); ?>:</b>
                <select name="priority" class="form-control select-picker" id="priority">
                    <?php foreach ($priorities as $key => $value) {
                        $selected = ($key == $contract['priority']) ? 'selected' : '';
                        echo "<option data-icon=\"priority-$key\" $selected value=\"$key\">$value</option>";
                    } ?>
                </select>
                <div data-field="priority" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('contract_value'); ?>:</b>
                <?php echo form_input('value', $contract['value'], 'id="value" class="form-control" dir="auto" autocomplete="off"'); ?>
                <div data-field="value" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('currency'); ?>:</b>
                <?php echo form_dropdown('currency_id', $currencies, $contract['currency_id'], 'id="currency" class="form-control select-picker" data-live-search="true"'); ?>
                <div data-field="currency_id" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('country'); ?>:</b>
                <?php echo form_dropdown('country_id', $countries, $contract['country_id'], 'id="country-id" class="form-control select-picker" data-live-search="true"'); ?>
                <div data-field="country_id" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('department'); ?>:</b>
                <?php echo form_dropdown('department_id', $departments, $contract['department_id'], 'id="department-id" class="form-control select-picker" data-live-search="true"'); ?>
                <div data-field="department_id_id" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('applicable_law'); ?>:</b>
                <?php echo form_dropdown('app_law_id', $applicable_laws, $contract['app_law_id'], 'id="applicable-law" class="form-control select-picker" data-live-search="true"'); ?>
                <div data-field="app_law_id" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-6 col-sm-12">
                <b><?php echo $this->lang->line('advance_payment_guarantee'); ?>:</b>
                <?php echo form_input('advance_payment_guarantee', $contract['advance_payment_guarantee'], 'id="advance-payment-guarantee" class="form-control" dir="auto" autocomplete="off" maxlength="100"'); ?>
                <div data-field="advance_payment_guarantee" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-12 col-sm-12">
                <b><?php echo $this->lang->line('letter_of_credit_details'); ?>:</b>
                <?php echo form_textarea(['name' => 'letter_of_credit_details', 'value' => $contract['letter_of_credit_details'], 'id' => 'letter-of-credit-details', 'class' => 'form-control', 'rows' => '3', 'dir' => 'auto']); ?>
                <div data-field="letter_of_credit_details" class="inline-error d-none"></div>
            </li>
            <li class="edit-inputs col-md-12 col-sm-12">
                <b><?php echo $this->lang->line('amendment_of'); ?>:</b>
                <?php echo form_input(['name' => 'amendment_of', 'id' => 'lookup-contract-id', 'value' => $contract['amendment_of'], 'type' => 'hidden']); ?>
                <?php echo form_input(['name' => 'amendment_of_lookup', 'id' => 'lookup-contract', 'title' => htmlspecialchars($contract['amendment_of_name'] ?? ''), 'value' => htmlspecialchars($contract['amendment_of_name'] ?? ''), 'class' => 'form-control search']); ?>
                <div data-field="amendment_of" class="inline-error d-none"></div>
            </li>
            <?php if (!empty($custom_fields['main'])) {
                foreach ($custom_fields['main'] as $i => $field) {
                    $col_class = ($field['type'] !== 'long_text') ? 'col-md-6' : 'col-md-12';
                    ?>
                    <li class="edit-inputs <?php echo $col_class; ?> col-sm-12">
                        <b><?php echo htmlspecialchars($field['customName']); ?>:</b>
                        <?php echo $field['hidden_custom_field_id']; ?>
                        <?php echo $field['hidden_value_id']; ?>
                        <?php echo $field['hidden_record_id']; ?>
                        <?php echo $field['custom_field']; ?>
                    </li>
                    <?php
                    unset($custom_fields['main'][$i]);
                }
            } ?>
        </ul>
    </div>
    <hr>
    <div class="contract-description-edit col-md-12 col-sm-12 p-0">
        <h4><?php echo $this->lang->line('description'); ?></h4>
        <div class="col-md-12 col-sm-12">
            <?php echo form_textarea(['name' => 'description', 'value' => $contract['description'], 'id' => 'description', 'class' => 'form-control', 'rows' => '5', 'cols' => '0', 'dir' => 'auto']); ?>
            <div data-field="description" class="inline-error d-none"></div>
        </div>
    </div>
    <hr>
    <div class="contract-details-edit" id="parties-container">
        <h4><?php echo $this->lang->line('parties'); ?></h4>
        <?php $count = 1; ?>
        <?php if (!empty($parties)) { ?>
            <?php echo form_input(['name' => 'contract_parties_count', 'id' => 'parties-count', 'value' => count($parties), 'type' => 'hidden']); ?>
            <?php foreach ($parties as $data) { ?>
                <ul class="d-flex clearfix parties-div p-0" id="parties-<?php echo $count; ?>">
                    <li class="party-type">
                        <b class="parties-label"><?php echo $this->lang->line('party'); ?><span class="label-count"> (<?php echo $count; ?>)</span>:</b>
                        <select name="party_member_type[]" id="parties-member-type" class="form-control select-picker">
                            <option value="company" <?php echo $data['party_member_type'] == 'company' ? 'selected="selected"' : ''; ?>><?php echo $this->lang->line('company_or_group'); ?></option>
                            <option value="contact" <?php echo $data['party_member_type'] == 'contact' ? 'selected="selected"' : ''; ?>><?php echo $this->lang->line('contact'); ?></option>
                        </select>
                    </li>
                    <li class="party-name">
                        <b><?php echo $this->lang->line('party_name'); ?>:</b>
                        <?php echo form_input(['name' => 'party_member_id[]', 'value' => $data['party_member_id'], 'id' => 'parties-member-id', 'type' => 'hidden']); ?>
                        <?php echo form_input(['name' => 'party_lookup[]', 'value' => htmlspecialchars($data['party_name']), 'id' => 'parties-lookup', 'class' => 'form-control lookup', 'title' => $this->lang->line('start_typing')]); ?>
                        <div data-field="party_member_id_<?php echo $count; ?>" class="inline-error d-none"></div>
                    </li>
                    <li class="party-category">
                        <b><?php echo $this->lang->line('party_category'); ?><span class="label-count"> (<?php echo $count; ?>)</span>:</b>
                        <div class="d-flex">
                            <?php echo form_dropdown('party_category[]', $categories, $data['party_category_id'], 'id="parties-category" class="form-control select-picker" data-live-search="true"'); ?>
                            <div data-field="party_category" class="inline-error d-none"></div>
                            <a href="javascript:;" class="delete-parties delete-link-parties mt-2 <?php echo $count > 1 ? '' : 'd-none'; ?>" onclick="objectDelete('parties', <?php echo $count; ?>, '#details-section', event);"><img src="assets/images/contract/remove.svg" height="14" width="14"></a>
                        </div>
                    </li>
                </ul>
                <?php $count++; ?>
            <?php } ?>
            <ul>
                <li>
                    <a href="javascript:;" onclick="objectContainerClone('parties', '.contract-container', event);" class="clone-contract-link"><?php echo $this->lang->line('add_more'); ?></a>
                </li>
            </ul>
        <?php } else { ?>
            <?php echo form_input(['name' => 'contract_parties_count', 'id' => 'parties-count', 'value' => 1, 'type' => 'hidden']); ?>
            <ul class="d-flex clearfix parties-div" id="parties-1">
                <li class="party-type">
                    <b class="parties-label"><?php echo $this->lang->line('party'); ?><span class="label-count"> (<?php echo $count; ?>)</span>:</b>
                    <select name="party_member_type[]" id="parties-member-type" class="form-control select-picker">
                        <option value="company"><?php echo $this->lang->line('company_or_group'); ?></option>
                        <option value="contact"><?php echo $this->lang->line('contact'); ?></option>
                    </select>
                </li>
                <li class="party-name">
                    <b><?php echo $this->lang->line('party_name'); ?>:</b>
                    <?php echo form_input(['name' => 'party_member_id[]', 'id' => 'parties-member-id', 'type' => 'hidden']); ?>
                    <?php echo form_input(['name' => 'party_lookup[]', 'id' => 'parties-lookup', 'class' => 'form-control lookup', 'title' => $this->lang->line('start_typing')]); ?>
                    <div data-field="party_member_id_1" class="inline-error d-none"></div>
                </li>
                <li class="party-category">
                    <b><?php echo $this->lang->line('party_category'); ?><span class="label-count"> (<?php echo $count; ?>)</span>:</b>
                    <div class="d-flex">
                        <?php echo form_dropdown('party_category[]', $categories, '', 'id="parties-category" class="form-control select-picker" data-live-search="true"'); ?>
                        <div data-field="party_category" class="inline-error d-none"></div>
                        <a href="javascript:;" class="delete-parties delete-link-parties pt-1 d-none" onclick="objectDelete('parties', <?php echo $count; ?>, '#details-section', event);"><img src="assets/images/contract/remove.svg" height="14" width="14"></a>
                    </div>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="javascript:;" onclick="objectContainerClone('parties', '.contract-container', event);" class="clone-contract-link"><?php echo $this->lang->line('add_more'); ?></a>
                </li>
            </ul>
        <?php } ?>
    </div>
</div>