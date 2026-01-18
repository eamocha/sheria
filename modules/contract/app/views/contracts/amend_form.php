<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal fade modal-container modal-resizable">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo htmlspecialchars($title); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div id="contract-amend-form-container" class="col-md-12 no-margin p-0 padding-10">
                    <?php
                    $hide_show_notification = ($hide_show_notification == "1") ? "yes" : "";
                    echo form_open(current_url(), ['class' => 'form-horizontal', 'novalidate' => true, 'id' => 'contract-amend-form']);
                    echo form_input(['id' => 'all-users-provider-group', 'value' => $assigned_team_id, 'type' => 'hidden']);
                    echo form_input(['name' => 'original_contract_id', 'value' => $contract['id'], 'type' => 'hidden']);
                    echo form_input(['name' => 'send_notifications_email', 'id' => 'send_notifications_email', 'value' => $hide_show_notification, 'type' => 'hidden']);
                    ?>
                    <?php if ($contract['status'] == 'Active') { ?>
                        <div class="col-md-12 p-0 form-group row margin-bottom-10 d-none"><!-- This is hidden . CA does not want it to be visible -->
                            <div class="col-md-7 col-xs-10 offset-md-3">
                                <?php echo $this->lang->line('deactivate_original_contract'); ?>
                                <span id="original-contract-status-label"></span>
                                <input id="original-contract-status" class="float-right" check   name="deactivate_original_contract" type="checkbox"/>
                                <!-- <label for="original-contract-status" class="label-success"></label> -->
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($contract['archived'] == 'no') { ?>
                        <div class="col-md-12 p-0 form-group row margin-bottom-10 d-none"><!-- This is hidden . CA does not want it to be visible -->
                            <div class="col-md-7 col-xs-10 offset-md-3">
                                <?php echo $this->lang->line('archive_original_contract'); ?>
                                <input id="archive_original_contract" class="float-right" checked
                                       name="archive_original_contract" type="checkbox"/>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($approval_center) { ?>
                        <div class="col-md-12 p-0 form-group row margin-bottom-10 d-none"><!-- This is hidden . CA does not want it to be visible -->
                            <div class="col-md-7 col-xs-10 offset-md-3">
                                <?php echo $this->lang->line('inherit_previous_approval_center'); ?>
                                <span id="original-contract-sc-label"></span>
                                <input id="original-contract-sc" class="float-right" checked name="inherit_ac"
                                       type="checkbox"/>
                                       <!-- <label for="original-contract-sc" class="label-success"></label> -->
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($signature_center) { ?>
                        <div class="col-md-12 p-0 form-group row margin-bottom-10 d-none"><!-- This is hidden . CA does not want it to be visible -->
                            <div class="col-md-7 col-xs-10 offset-md-3">
                                <?php echo $this->lang->line('inherit_previous_signature_center'); ?>
                                <span id="original-contract-ac-label"></span>
                                <input id="original-contract-ac" class="float-right" checked name="inherit_sc"
                                       type="checkbox"/>
                                      <!-- <label for="original-contract-ac" class="label-success"></label> -->
                            </div>
                        </div>
                    <?php } ?>

                    <div class="p-0 form-group row margin-bottom-10">
                        <label class="col-form-label text-right col-md-3 pr-0 required col-xs-5">
                            <?php echo $this->lang->line('type'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-12 d-flex">
                            <div class="col-md-11 p-0 col-xs-10">
                                <?php echo form_dropdown('type_id', $types, $contract['type_id'], [
                                    'id' => 'type',
                                    'class' => 'form-control select-picker',
                                    'data-live-search' => 'true',
                                    'data-field' => 'administration-contract_types',
                                    'data-size' => $this->session->userdata('max_drop_down_length')
                                ]); ?>
                            </div>
                            <div class="col-md-2 p-0 col-xs-2">
                                <a href="javascript:;"
                                   onclick="quickAdministrationDialog('contract_types', jQuery('#contract-amend-form-container'), true, false, false, false, false, 'contract');"
                                   class="btn btn-link"><i class="fa-solid fa-circle-plus"></i></a>
                            </div>
                            <div data-field="type_id" class="inline-error d-none"></div>
                        </div>
                    </div>

                    <div class="p-0 form-group row margin-bottom-10">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('sub_type'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-12">
                            <?php echo form_dropdown('sub_type_id', $sub_types ?? [], $contract['sub_type_id'], [
                                'id' => 'sub-type',
                                'class' => 'form-control select-picker',
                                'data-live-search' => 'true',
                                'data-size' => $this->session->userdata('max_drop_down_length')
                            ]); ?>
                        </div>
                    </div>

                    <div class="col-md-12 p-0">
                        <div class="form-group row p-0 col-xs-12">
                            <label class="col-form-label text-right col-md-3 pr-0 required col-xs-5">
                                <?php echo $this->lang->line('name'); ?>
                            </label>
                            <div class="col-md-8 pr-0">
                                <?php echo form_input('name', $contract['name'], [
                                    'id' => 'name',
                                    'class' => 'form-control first-input',
                                    'dir' => 'auto',
                                    'autocomplete' => 'stop'
                                ]); ?>
                                <div data-field="name" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0">
                        <div class="form-group row col-md-12 p-0 col-xs-12">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('description'); ?>
                            </label>
                            <div class="col-md-8 pr-0">
                                <?php echo form_textarea([
                                    'name' => 'description',
                                    'value' => $contract['description'],
                                    'id' => 'description',
                                    'class' => 'form-control',
                                    'rows' => '5',
                                    'cols' => '0',
                                    'dir' => 'auto'
                                ]); ?>
                                <div data-field="description" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0">
                        <div class="form-group row p-0 col-xs-12">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('contract_value'); ?>
                            </label>
                            <div class="row col-md-8 pr-0">
                                <div class="col-md-6">
                                    <?php echo form_input('value', $contract['value'], [
                                        'id' => 'value',
                                        'class' => 'form-control',
                                        'dir' => 'auto',
                                        'autocomplete' => 'stop'
                                    ]); ?>
                                    <div data-field="value" class="inline-error d-none"></div>
                                </div>
                                <div class="col-md-6 pr-0 col-xs-10">
                                    <?php echo form_dropdown('currency_id', $currencies, $contract['currency_id'], [
                                        'id' => 'currency',
                                        'class' => 'form-control select-picker',
                                        'data-live-search' => 'true',
                                        'data-size' => $this->session->userdata('max_drop_down_length')
                                    ]); ?>
                                    <div data-field="currency_id" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0 requester-container">
                        <div class="form-group row p-0 col-xs-12">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('requester'); ?>
                            </label>
                            <div class="col-md-8 pr-0">
                                <div class="col-md-12 p-0 users-lookup-container margin-bottom-5">
                                    <?php echo form_input([
                                        'name' => 'requester_id',
                                        'id' => 'requester-id',
                                        'value' => $contract['requester_id'],
                                        'type' => 'hidden'
                                    ]); ?>
                                    <?php echo form_input([
                                        'name' => 'requester_name',
                                        'id' => 'requester-lookup',
                                        'value' => $contract['requester'],
                                        'class' => 'form-control lookup',
                                        'title' => $this->lang->line('start_typing')
                                    ]); ?>
                                </div>
                                <div data-field="requester_id" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div id="parties-container">
                        <?php
                        $count = 1;
                        if (!empty($parties)) {
                            echo form_input([
                                'name' => 'contract_parties_count',
                                'id' => 'parties-count',
                                'value' => count($parties),
                                'type' => 'hidden'
                            ]);
                            foreach ($parties as $data) { ?>
                                <div class="parties-div" id="parties-<?php echo $count; ?>">
                                    <div class="p-0 form-group row margin-bottom-10">
                                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 parties-label">
                                            <?php echo $this->lang->line('party') . '<span class="label-count"> (' . $count . ')</span>'; ?>
                                        </label>
                                        <div class="col-md-8 pr-0 col-xs-10">
                                            <select name="party_member_type[]" id="parties-member-type"
                                                    class="form-control select-picker">
                                                <option value="company" <?php echo ($data['party_member_type'] == 'company') ? 'selected="selected"' : ''; ?>>
                                                    <?php echo $this->lang->line('company_or_group'); ?>
                                                </option>
                                                <option value="contact" <?php echo ($data['party_member_type'] == 'contact') ? 'selected="selected"' : ''; ?>>
                                                    <?php echo $this->lang->line('contact'); ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 form-group row p-0 margin-bottom-5">
                                        <label class="col-form-label text-right pr-0 col-md-3 col-xs-12"> </label>
                                        <div class="col-md-8 pr-0 col-xs-10 d-flex">
                                            <div class="col-md-11 p-0">
                                                <?php echo form_input([
                                                    'name' => 'party_member_id[]',
                                                    'value' => $data['party_member_id'],
                                                    'id' => 'parties-member-id',
                                                    'type' => 'hidden'
                                                ]); ?>
                                                <?php echo form_input([
                                                    'name' => 'party_lookup[]',
                                                    'value' => $data['party_name'],
                                                    'id' => 'parties-lookup',
                                                    'class' => 'form-control lookup',
                                                    'title' => $this->lang->line('start_typing')
                                                ]); ?>
                                            </div>
                                            <div class="col-sm-13 col-xs-4 col-md-1 pr-0 padding-10 delete-icon delete-parties d-none">
                                                <a href="javascript:;" class="delete-link-parties"
                                                   onclick="objectDelete('parties', '1', '#contract-amend-form-container', event);">
                                                    <i class="fa-solid fa-trash-can red"></i>
                                                </a>
                                            </div>
                                            <div data-field="party_member_id_<?php echo $count; ?>" class="inline-error d-none"></div>
                                        </div>
                                        <div class="col-md-12 p-0 offset-md-3 autocomplete-helper">
                                            <div class="inline-text"><?php echo $this->lang->line('helper_autocomplete'); ?></div>
                                        </div>
                                    </div>
                                    <div class="p-0 form-group row margin-bottom-10">
                                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 related-category">
                                            <?php echo $this->lang->line('category') . '<span class="label-count"> (' . $count . ')</span>'; ?>
                                        </label>
                                        <div class="col-md-8 pr-0 col-xs-12 d-flex">
                                            <div class="col-md-11 p-0 col-xs-10">
                                                <?php echo form_dropdown('party_category[]', $categories, $data['party_category_id'], [
                                                    'id' => 'parties-category',
                                                    'class' => 'form-control select-picker',
                                                    'data-live-search' => 'true',
                                                    'data-field' => 'administration-party_categories',
                                                    'data-field-id' => 'category-1',
                                                    'data-size' => $this->session->userdata('max_drop_down_length')
                                                ]); ?>
                                            </div>
                                            <div class="col-md-1 p-0 col-xs-2">
                                                <a href="javascript:;"
                                                   onclick="quickAdministrationDialog('party_categories', jQuery('#contract-amend-form-container'), true, false, false, jQuery('[data-field-id=category-1]'), false, 'contract');"
                                                   class="btn btn-link parties-category-quick-add">
                                                    <i class="fa-solid fa-circle-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $count++;
                            } ?>
                            <div class="col-md-12 p-0 form-group row margin-bottom-5">
                                <div class="col-md-3 pr-0 col-xs-1"> </div>
                                <div class="col-md-8 col-xs-10 add-more-link">
                                    <a href="javascript:;"
                                       onclick="objectContainerClone('parties', '#contract-amend-form-container', event);">
                                        <?php echo $this->lang->line('add_more'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php } else {
                            echo form_input([
                                'name' => 'contract_parties_count',
                                'id' => 'parties-count',
                                'value' => '2',
                                'type' => 'hidden'
                            ]); ?>
                            <div class="parties-div" id="parties-1">
                                <div class="p-0 form-group row margin-bottom-10">
                                    <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 parties-label">
                                        <?php echo $this->lang->line('party') . '<span class="label-count"> (1)</span>'; ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <select name="party_member_type[]" id="parties-member-type"
                                                class="form-control select-picker">
                                            <option value="company"><?php echo $this->lang->line('company_or_group'); ?></option>
                                            <option value="contact"><?php echo $this->lang->line('contact'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 form-group row p-0 margin-bottom-5">
                                    <label class="col-form-label text-right pr-0 col-md-3 col-xs-12"> </label>
                                    <div class="col-md-8 pr-0 col-xs-10 d-flex">
                                        <div class="col-md-12 p-0">
                                            <?php echo form_input([
                                                'name' => 'party_member_id[]',
                                                'id' => 'parties-member-id',
                                                'type' => 'hidden'
                                            ]); ?>
                                            <?php echo form_input([
                                                'name' => 'party_lookup[]',
                                                'id' => 'parties-lookup',
                                                'class' => 'form-control lookup',
                                                'title' => $this->lang->line('start_typing')
                                            ]); ?>
                                        </div>
                                        <div class="col-sm-13 col-xs-4 col-md-1 pr-0 padding-10 delete-icon delete-parties">
                                            <a href="javascript:;" class="delete-link-parties"
                                               onclick="objectDelete('parties', '1', '#contract-amend-form-container', event);">
                                                <i class="fa-solid fa-trash-can red"></i>
                                            </a>
                                        </div>
                                        <div data-field="party_member_id_1" class="inline-error d-none"></div>
                                    </div>
                                    <div class="col-md-12 p-0 autocomplete-helper">
                                        <div class="col-md-3 pr-0 col-xs-1"> </div>
                                        <div class="col-md-9 pr-0 col-xs-10">
                                            <div class="inline-text"><?php echo $this->lang->line('helper_autocomplete'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 p-0 form-group row margin-bottom-10">
                                    <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 related-category">
                                        <?php echo $this->lang->line('category') . '<span class="label-count"> (1)</span>'; ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-12 d-flex">
                                        <div class="col-md-11 p-0 col-xs-10">
                                            <?php echo form_dropdown('party_category[]', $categories, '', [
                                                'id' => 'parties-category',
                                                'class' => 'form-control select-picker',
                                                'data-live-search' => 'true',
                                                'data-field' => 'administration-party_categories',
                                                'data-field-id' => 'category-1',
                                                'data-size' => $this->session->userdata('max_drop_down_length')
                                            ]); ?>
                                        </div>
                                        <div class="col-md-1 p-0 col-xs-2">
                                            <a href="javascript:;"
                                               onclick="quickAdministrationDialog('party_categories', jQuery('#contract-amend-form-container'), true, false, false, jQuery('[data-field-id=category-1]'), false, 'contract');"
                                               class="btn btn-link parties-category-quick-add">
                                                <i class="fa-solid fa-circle-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="parties-div" id="parties-2">
                                <div class="col-md-12 p-0 form-group row margin-bottom-10">
                                    <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 parties-label">
                                        <?php echo $this->lang->line('party') . '<span class="label-count"> (2)</span>'; ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-10">
                                        <div class="col-md-6 p-0">
                                            <select name="party_member_type[]" id="parties-member-type"
                                                    class="form-control select-picker">
                                                <option value="company"><?php echo $this->lang->line('company_or_group'); ?></option>
                                                <option value="contact"><?php echo $this->lang->line('contact'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 form-group row p-0 margin-bottom-5">
                                    <label class="col-form-label text-right pr-0 col-md-3 col-xs-12"> </label>
                                    <div class="col-md-8 pr-0 col-xs-10 d-flex">
                                        <div class="col-md-11 p-0">
                                            <?php echo form_input([
                                                'name' => 'party_member_id[]',
                                                'id' => 'parties-member-id',
                                                'type' => 'hidden'
                                            ]); ?>
                                            <?php echo form_input([
                                                'name' => 'party_lookup[]',
                                                'id' => 'parties-lookup',
                                                'class' => 'form-control lookup',
                                                'title' => $this->lang->line('start_typing')
                                            ]); ?>
                                        </div>
                                        <div class="col-sm-13 col-xs-4 col-md-1 pr-0 padding-10 delete-icon delete-parties">
                                            <a href="javascript:;" class="delete-link-parties"
                                               onclick="objectDelete('parties', '2', '#contract-amend-form-container', event);">
                                                <i class="fa-solid fa-trash-can red"></i>
                                            </a>
                                        </div>
                                        <div data-field="party_member_id_2" class="inline-error d-none"></div>
                                    </div>
                                    <div class="col-md-12 p-0 autocomplete-helper">
                                        <div class="col-md-3 pr-0 col-xs-1"> </div>
                                        <div class="col-md-9 pr-0 col-xs-10">
                                            <div class="inline-text"><?php echo $this->lang->line('helper_autocomplete'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 p-0 form-group row margin-bottom-10">
                                    <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 related-category">
                                        <?php echo $this->lang->line('category') . '<span class="label-count"> (2)</span>'; ?>
                                    </label>
                                    <div class="col-md-8 pr-0 col-xs-12 d-flex">
                                        <div class="col-md-11 p-0 col-xs-10">
                                            <?php echo form_dropdown('party_category[]', $categories, '', [
                                                'id' => 'parties-category',
                                                'class' => 'form-control select-picker',
                                                'data-live-search' => 'true',
                                                'data-field' => 'administration-party_categories',
                                                'data-field-id' => 'category-2',
                                                'data-size' => $this->session->userdata('max_drop_down_length')
                                            ]); ?>
                                        </div>
                                        <div class="col-md-1 p-0 col-xs-2">
                                            <a href="javascript:;"
                                               onclick="quickAdministrationDialog('party_categories', jQuery('#contract-amend-form-container'), true, false, false, jQuery('[data-field-id=category-2]'), false, 'contract');"
                                               class="btn btn-link parties-category-quick-add">
                                                <i class="fa-solid fa-circle-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 p-0 form-group row margin-bottom-5">
                                <div class="col-md-3 pr-0 col-xs-1"> </div>
                                <div class="col-md-9 col-xs-10 add-more-link">
                                    <a href="javascript:;"
                                       onclick="objectContainerClone('parties', '#contract-amend-form-container', event);">
                                        <?php echo $this->lang->line('add_more'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="form-group row col-md-12 p-0" id="contract-date-container">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 required">
                            <?php echo $this->lang->line('contract_date'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <div class="input-group date col-md-4 col-xs-4 col-lg-5 p-0 date-picker" id="contract-date">
                                <?php echo form_input([
                                    'name' => 'contract_date',
                                    'value' => $contract['contract_date'],
                                    'id' => 'contract-date-input',
                                    'placeholder' => 'YYYY-MM-DD',
                                    'class' => 'form-control'
                                ]); ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div>
                            <div data-field="contract_date" class="inline-error d-none"></div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0 form-group row margin-bottom-10">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('renewal'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <div class="col-md-4 col-xs-4 col-lg-5 p-0">
                                <?php echo form_dropdown('renewal_type', $renewals, $contract['renewal_type'], [
                                    'id' => 'renewal',
                                    'class' => 'form-control select-picker',
                                    'onchange' => 'renewalEvents(jQuery(\'#contract-amend-form\'));'
                                ]); ?>
                            </div>
                            <div data-field="renewal_type" class="inline-error d-none"></div>
                        </div>
                    </div>

                    <div class="form-group row col-md-12 p-0" id="start-date-container">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('start_date'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <div class="input-group date col-md-4 col-xs-4 col-lg-5 p-0 date-picker" id="start-date">
                                <?php echo form_input([
                                    'name' => 'start_date',
                                    'value' => $contract['start_date'],
                                    'id' => 'start-date-input',
                                    'placeholder' => 'YYYY-MM-DD',
                                    'class' => 'form-control'
                                ]); ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div>
                            <div data-field="start_date" class="inline-error d-none"></div>
                        </div>
                    </div>

                    <div class="form-group row col-md-12 p-0" id="end-date-container">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('end_date'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <div class="input-group date col-md-4 col-xs-4 col-lg-5 p-0 date-picker" id="end-date">
                                <?php echo form_input([
                                    'name' => 'end_date',
                                    'value' => $contract['end_date'],
                                    'id' => 'end-date-input',
                                    'placeholder' => 'YYYY-MM-DD',
                                    'class' => 'form-control'
                                ]); ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div>
                            <div data-field="end_date" class="inline-error d-none"></div>
                            <div class="col-md-10 p-0 d-none" id="notify-me-before-link">
                                <span class="assign-to-me-link-id-wrapper">
                                    <a href="javascript:;" id="notify-me-link"
                                       onclick="notifyMeBefore(jQuery('#contract-amend-form'));">
                                        <?php echo $this->lang->line('notify_me_before'); ?>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0 form-group row d-none" id="notify-me-before-container">
                        <div class="col-md-12 p-0 form-group row">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('notify_me_before'); ?>
                            </label>
                            <div class="col-md-8 pr-0 col-xs-10" id="notify-me-before">
                                <?php echo form_input([
                                    'name' => 'notify_me_before[id]',
                                    'value' => $notify_before['id'] ??"",
                                    'disabled' => true,
                                    'type' => 'hidden'
                                ]); ?>
                                <?php echo form_input([
                                    'name' => 'notify_me_before[time]',
                                    'class' => 'form-control',
                                    'value' => $notify_before['time'] ??'90',
                                    'id' => 'notify-me-before-time',
                                    'disabled' => true
                                ]); ?>
                                <?php echo form_dropdown('notify_me_before[time_type]', $notify_me_before_time_types, $notify_before['time_type'] ?? 'days', [
                                    'class' => 'form-control select-picker',
                                    'id' => 'notify-me-before-time-type',
                                    'disabled' => true
                                ]); ?>
                                <a href="javascript:;" onclick="hideRemindMeBefore(jQuery('#contract-amend-form'));"
                                   class="btn btn-link">
                                    <i class="red fa-solid fa-trash-can"></i>
                                </a>
                                <div data-field="notify_before" class="inline-error d-none"></div>
                            </div>
                        </div>
                        <div class="col-md-12 p-0 form-group row">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('notify_users'); ?>
                            </label>
                            <div class="col-md-8 pr-0">
                                <select name="notifications[emails][]"
                                        placeholder="<?php echo $this->lang->line('select_users'); ?>"
                                        id="notify-to-emails" multiple="multiple" tabindex="-1">
                                </select>
                                <div data-field="emails" class="inline-error d-none"></div>
                            </div>
                        </div>
                        <div class="col-md-12 p-0 form-group row">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('notify_teams'); ?>
                            </label>
                            <div class="col-md-8 pr-0">
                                <select name="notifications[teams][]"
                                        placeholder="<?php echo $this->lang->line('select_assigned_teams'); ?>"
                                        id="notify-to-teams" multiple="multiple" tabindex="-1">
                                    <?php if (is_array($notifications['teams'])) {
                                        foreach ($notifications['teams'] as $key => $val) { ?>
                                            <option selected="selected" value="<?php echo $val['id']; ?>">
                                                <?php echo $val['name']; ?>
                                            </option>
                                        <?php }
                                    } ?>
                                </select>
                                <div data-field="teams" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0">
                        <div class="form-group row col-md-12 p-0 col-xs-12">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('reference_number'); ?>
                            </label>
                            <div class="col-md-6 pr-0">
                                <?php echo form_input('reference_number', $contract['reference_number'], [
                                    'id' => 'ref-nb',
                                    'class' => 'form-control',
                                    'dir' => 'auto',
                                    'autocomplete' => 'stop'
                                ]); ?>
                                <div data-field="reference_number" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row col-md-12 p-0">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('provider_group'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <?php echo form_dropdown('assigned_team_id', $assigned_teams, $contract['assigned_team_id'], [
                                'id' => 'assigned-team-id',
                                'class' => 'form-control select-picker',
                                'data-live-search' => 'true',
                                'data-size' => $this->session->userdata('max_drop_down_length')
                            ]); ?>
                            <div data-field="assigned_team_id" class="inline-error d-none"></div>
                        </div>
                    </div>

                    <div class="form-group row col-md-12 p-0">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('assignee'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <?php echo form_dropdown('assignee_id', $assignees, $contract['assignee_id'], [
                                'id' => 'assignee-id',
                                'class' => 'form-control select-picker',
                                'data-live-search' => 'true',
                                'data-size' => $this->session->userdata('max_drop_down_length')
                            ]); ?>
                            <div data-field="assignee_id" class="inline-error d-none"></div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0" id="contributors-container">
                        <div class="form-group row col-md-12 p-0">
                            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('contributors'); ?>
                            </label>
                            <div class="col-md-7 pr-0 col-xs-10 users-lookup-container">
                                <div class="input-group col-md-12 p-0 margin-bottom-5">
                                    <?php echo form_input('contributors_lookup', '', [
                                        'id' => 'contributors-lookup',
                                        'class' => 'form-control users-lookup'
                                    ]); ?>
                                    <span class="input-group-addon bs-caret users-lookup-icon"
                                          onclick="jQuery('#contributors-lookup', '#contract-amend-form-container').focus();">
                                        <span class="caret"></span>
                                    </span>
                                </div>
                                <div id="selected-contributors" class="height-auto no-margin">
                                    <?php if (!empty($contributors)) {
                                        $select_options_name = 'contributors';
                                        foreach ($contributors as $key => $value) { ?>
                                            <div class="row multi-option-selected-items no-margin"
                                                 id="<?php echo $select_options_name . $value['id']; ?>">
                                                <span id="<?php echo $value['id']; ?>">
                                                    <?php echo ($value['status'] === 'Inactive') ? $value['name'] . '(' . $this->lang->line('Inactive') . ')' : $value['name']; ?>
                                                </span>
                                                <?php echo form_input([
                                                    'value' => $value['id'],
                                                    'name' => $select_options_name . '[]',
                                                    'type' => 'hidden'
                                                ]); ?>
                                                <a href="javascript:;" class="btn btn-light btn-sm btn-link float-right remove-button"
                                                   tabindex="-1"
                                                   onclick="removeBoxElement(jQuery(this.parentNode), '#selected-contributors', 'contributors-container', '#contract-amend-container');">
                                                    <i class="red fa-solid fa-trash-can"></i>
                                                </a>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                                <div data-field="contributors" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row col-md-12 p-0">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('priority'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <div class="col-md-4 col-xs-4 col-lg-5 p-0">
                                <select name="priority" class="form-control select-picker" id="priority">
                                    <?php
                                    $selected = '';
                                    foreach ($priorities as $key => $value) {
                                        $selected = ($key == $contract['priority']) ? 'selected' : '';
                                        ?>
                                        <option data-icon="priority-<?php echo $key; ?>" <?php echo $selected; ?>
                                                value="<?php echo $key; ?>">
                                            <?php echo $value; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <div data-field="priority" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 p-0">
                        <div id="custom_fields_div">
                            <?php $this->load->view('custom_fields/dialog_form_custom_field_template', ['custom_fields' => $custom_fields]); ?>
                        </div>
                    </div>
                    <!-- add amendmenrt approval status dropdown using form_dropdown(..)--> 
                    <div class="form-group row col-md-12 p-0">
                        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('amendment_approval_status'); ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <?php $amendment_approval_statuses = [
                                'Pending Approval' => $this->lang->line('pending_approval'),
                                'Approved' => $this->lang->line('approved'),
                                'Rejected' => $this->lang->line('rejected')
                            ];
                             echo form_dropdown('amendment_approval_status', $amendment_approval_statuses,"Pending Approval", [
                                'id' => 'amendment-approval-status',
                                'class' => 'form-control select-picker',
                                'data-live-search' => 'true',
                                'data-size' => $this->session->userdata('max_drop_down_length')
                            ]); ?>
                            <div data-field="amendment_approval_status" class="inline-error d-none"></div>
                        </div>
                    </div>
                 

                    <!-- add attachment input box to attach files -->
                    <div class="col-md-12 p-0">
    <div class="p-0 row m-0" id="attachments-container">
        <label class="control-label col-md-3 pr-0 col-xs-5">
            <i class="fa-solid fa-paperclip"></i>&nbsp;<?php echo $this->lang->line("attach_file"); ?>
        </label>
        <div id="contract-attachments" class="col-md-8 pr-0 col-xs-10 mb-10">
            <!-- Initial file input -->
            <div class="col-md-12 p-0 margin-bottom-10" id="attachment-row-0">
                <div class="row m-0">
                    <div class="col-md-11 p-0">
                        <input id="contract-attachment-0" name="contract_attachment[]" type="file" class="form-control-file margin-top" />
                    </div>
                    <div class="col-md-1 p-0">
                        <!-- Remove button for additional attachments (hidden for first) -->
                        <a href="javascript:;" onclick="removeAttachment(0)" class="btn btn-link text-danger d-none">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Additional attachments will be added here -->
            <div id="additional-attachments"></div>
            
            <!-- Add more link -->
            <div class="col-md-12 p-0">
                <a href="javascript:;" onclick="addMoreAttachment()" class="btn-link">
                    <i class="fa-solid fa-plus"></i> <?php echo $this->lang->line("add_more"); ?>
                </a>
            </div>
        </div>
        <div data-field="files" class="inline-error d-none"></div>
    </div>
</div>

                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="save-button" id="form-submit">
                    <?php echo $this->lang->line('save'); ?>
                </button>
                <span class="label-notification-checkbox pt-10 d-inline-block v-al-n-5">
                    <?php $this->load->view('notifications/wrapper', [
                        'hide_show_notification' => $hide_show_notification,
                        'container' => '#contract-amend-form-container',
                        'hide_label' => false
                    ]); ?>
                </span>
                <button type="button" class="close_model no_bg_button float-right text-right" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <span class="loader-submit"></span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    availableEmails = <?php echo json_encode($users_emails); ?>;
    availableAssignedTeams = <?php echo json_encode($assigned_teams_list); ?>;
    var selectedEmails = <?php echo $notifications['emails'] ? json_encode(explode(';', $notifications['emails'])) : json_encode(explode(';', $this->session->userdata('AUTH_email_address'))); ?>;
    <?php if ($notify_before) { ?>
        notifyMeBeforeRenewal(jQuery('#contract-amend-form', '#contract-amend-container'));
    <?php } ?>
    let attachmentCounter = 0;

function addMoreAttachment() {
    attachmentCounter++;
    var newAttachmentHtml = 
        '<div class="col-md-12 p-0 margin-bottom-10" id="attachment-row-' + attachmentCounter + '">' +
        '    <div class="row m-0">' +
        '        <div class="col-md-11 p-0">' +
        '            <input name="contract_attachment[]" type="file" class="form-control-file margin-top" />' +
        '        </div>' +
        '        <div class="col-md-1 p-0">' +
        '            <a href="javascript:;" onclick="removeAttachment(' + attachmentCounter + ')" class="btn btn-link text-danger">' +
        '                <i class="fa-solid fa-trash-can"></i>' +
        '            </a>' +
        '        </div>' +
        '    </div>' +
        '</div>';
    
    $('#additional-attachments').append(newAttachmentHtml);
}

function removeAttachment(id) {
    $('#attachment-row-' + id).remove();
}
</script>