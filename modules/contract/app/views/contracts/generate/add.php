<div id="contract-add-container"  class="col-md-12 no-margin p-0 padding-10"><?php
 $hide_show_notification == "1" ? $hide_show_notification = "yes" : ($hide_show_notification = "");
 echo form_input(["id" => "all-users-provider-group", "value" => $assigned_team_id, "type" => "hidden"]);
 echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => $hide_show_notification, "type" => "hidden"]);?>
     <div class="row p-0 form-group row margin-bottom-10">
         <label class="col-form-label text-right col-md-3 pr-0 required col-xs-5"><?php echo $this->lang->line("type");?></label>
         <div class="d-flex col-md-8 pr-0 col-xs-12">
             <div class="col-md-8 p-0 col-xs-10"><?php echo form_dropdown("type_id", $types, "", "id=\"type\"  class=\"form-control select-picker\"  data-live-search=\"true\"  data-field=\"administration-contract_types\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>
                 <div data-field="type_id"  class="inline-error d-none"></div>
             </div>
             <div class="col-md-1 p-0 col-xs-2">
                 <a href="javascript:;"  onclick="quickAdministrationDialog('contract_types', jQuery('#contract-generate-container'), true, false, false, false, false, 'contract');"  class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"></i></a>
             </div>
         </div>
     </div>
    <div class="p-0 form-group row margin-bottom-10">
        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("sub_type");?></label>
        <div class="d-flex col-md-8 pr-0 col-xs-12">
            <div class="col-md-8 p-0 col-xs-10"><?php
                echo form_dropdown("sub_type_id", "", "", "id=\"sub-type\"  class=\"form-control select-picker\"  data-live-search=\"true\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?></div>
            <div class="col-md-1 p-0 col-xs-2">
                <a href="javascript:;" onclick="quickAdministrationDialog('sub_contract_types', jQuery('#contract-generate-container'), true, false, function(formContainer, response){contractSubTypeFormEvents(formContainer, response);}, false, false, 'contract', function(formContainer){contractSubTypeFormEvents(formContainer);});"  class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"></i></a>
            </div>
        </div>
    </div>
    <div class="p-0">
        <div class="form-group row col-md-12 p-0 col-xs-12">
            <label class="col-form-label text-right col-md-3 pr-0 required col-xs-5"><?php echo $this->lang->line("contract_name");?></label>
            <div class="col-md-8 pr-0">
                <?php echo form_input("name", "", 'id="name"  class="form-control first-input"  dir="auto"  autocomplete="stop"');?>
                <div data-field="name"  class="inline-error d-none"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 p-0">
        <div class="form-group row col-md-12 p-0 col-xs-12">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("description");?></label>
            <div class="col-md-8 pr-0"><?php echo form_textarea(["name" => "description", "value" => "", "id" => "description", "class" => "form-control", "rows" => "5", "cols" => "0", "dir" => "auto"]);?>
                <div data-field="description"  class="inline-error d-none"></div>
            </div>
        </div>
    </div>
    <div class="row no-margin col-md-12 p-0">
        <div class="form-group row col-md-12 p-0 col-xs-12">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("contract_value");?></label>
            <div class="row no-margin col-md-8 pr-0">
                <div class="col-md-3 pl-0">
                    <?php echo form_input("value", "", 'id="value"  class="form-control"  dir="auto"  autocomplete="stop"');?>
                    <div data-field="value"  class="inline-error d-none"></div>
                </div>
                <div class="col-md-3 pr-0 col-xs-10">
                    <?php echo form_dropdown("currency_id", $currencies, "", "id=\"currency\"  class=\"form-control select-picker\"  data-live-search=\"true\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>
                    <div data-field="currency_id"  class="inline-error d-none"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 p-0 requester-container">
        <div class="form-group row col-md-12 p-0 col-xs-12">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 required"><?php echo $this->lang->line("requester");?></label>
            <div class="col-md-8 pr-0">
                <div class="col-md-8 p-0 users-lookup-container margin-bottom-5"><?php
                    echo form_input(["name" => "requester_id", "id" => "requester-id", "value" => "", "type" => "hidden"]);
                    echo form_input(["name" => "requester_name", "id" => "requester-lookup", "value" => "", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);
                    ?>
                </div>
                <div data-field="requester_id"  class="inline-error d-none"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 p-0 form-group row margin-bottom-10">
        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("country");?></label>
        <div class="col-md-8 pr-0 col-xs-12">             <div class="col-md-8 p-0 col-xs-10"><?php echo form_dropdown("country_id", $countries, "", "id=\"country\"  class=\"form-control select-picker\"  data-live-search=\"true\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?> </div>
            <div data-field="country_id"  class="inline-error d-none"></div>
        </div>
    </div>
    <div class="col-md-12 p-0 form-group row margin-bottom-10">
        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("applicable_law");?></label>
        <div class="row no-margin col-md-8 pr-0 col-xs-12">
            <div class="col-md-8 p-0 col-xs-10"><?php echo form_dropdown("app_law_id", $applicable_laws, "", "id=\"applicable_law\"  class=\"form-control select-picker\"  data-live-search=\"true\"  data-field=\"administration-applicable_laws\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?> </div>
            <div class="col-md-1 p-0 col-xs-2">
                <a href="javascript:;" onclick="quickAdministrationDialog('applicable_laws', jQuery('#contract-generate-container'), true, false, false, false, false, 'contract');" class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"></i></a>
            </div>
            <div data-field="app_law_id"  class="inline-error d-none"></div>
        </div>
    </div>
    <div id="parties-container">
        <?php echo form_input(["name" => "contract_parties_count", "id" => "parties-count", "value" => "2", "type" => "hidden"]);?>
        <div class="parties-div"  id="parties-1">
            <div class="col-md-12 p-0 form-group row margin-bottom-10">
                <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 parties-label"><?php echo $this->lang->line("party") . "<span class='label-count'> (1)</span>"?></label>
                <div class="col-md-8 pr-0 col-xs-10">
                    <div class="col-md-6 p-0">
                        <select name="party_member_type[]"  id="parties-member-type"    class="form-control select-picker">
                            <option value="company"><?php echo $this->lang->line("company_or_group");?></option>
                            <option value="contact"><?php echo $this->lang->line("contact");?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row no-margin col-md-12 form-group row p-0 margin-bottom-5">
                <label class="col-form-label text-right pr-0 col-md-3 col-xs-12">&nbsp;</label>
                <div class="row no-margin col-md-8 pr-0 col-xs-10">
                    <div class="col-md-8 p-0">
                        <?php echo form_input(["name" => "party_member_id[]", "id" => "parties-member-id", "type" => "hidden"]);
                        echo form_input(["name" => "party_lookup[]", "id" => "parties-lookup", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);
                        ?>
                    </div>
                    <div class="col-sm-13 col-xs-4 col-md-1 pr-0 padding-10 delete-icon delete-parties">
                        <a href="javascript:;" class="delete-link-parties" onclick="objectDelete('parties', '1', '#contract-generate-container', event);"><i class="fa-solid fa-trash-can red"></i></a>
                    </div>
                    <div data-field="party_member_id_1"  class="inline-error d-none"></div>
                </div>
                <div class="row no-margin col-md-12 p-0 autocomplete-helper mb-3">
                    <div class="col-md-3 pr-0 col-xs-1">&nbsp;</div>
                    <div class="col-md-8 pr-0 col-xs-10">
                        <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete"); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 p-0 form-group row margin-bottom-10">
                <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 related-category"><?php echo $this->lang->line("category") . "<span class='label-count'> (1)</span>"?></label>
                <div class="row no-margin col-md-8 pr-0 col-xs-12">
                    <div class="col-md-8 p-0 col-xs-10">
                        <?php echo form_dropdown("party_category[]", $categories, "", "id=\"parties-category\"  class=\"form-control select-picker\"  data-live-search=\"true\"  data-field=\"administration-party_categories\"  data-field-id=\"category-1\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>
                    </div>
                    <div class="col-md-1 p-0 col-xs-2">
                        <a href="javascript:;" onclick="quickAdministrationDialog('party_categories', jQuery('#contract-generate-container'), true, false, false, jQuery('[data-field-id=category-1]'), false, 'contract');" class="btn btn-link parties-category-quick-add"><i  class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="parties-div"  id="parties-2">
            <div class="row no-margin col-md-12 p-0 form-group row margin-bottom-10">
                <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 parties-label"><?php echo $this->lang->line("party") . "<span class='label-count'> (2)</span>"?></label>
                <div class="row no-margin col-md-8 pr-0 col-xs-10">
                    <div class="col-md-6 p-0">
                        <select name="party_member_type[]"  id="parties-member-type" class="form-control select-picker">
                            <option value="company"><?php echo $this->lang->line("company_or_group");?></option>
                            <option value="contact"><?php echo $this->lang->line("contact");?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row no-margin col-md-12 form-group row p-0 margin-bottom-5">
                <label class="col-form-label text-right pr-0 col-md-3 col-xs-12">&nbsp;</label>
                <div class="row no-margin col-md-8 pr-0 col-xs-10">
                    <div class="col-md-8 p-0">
                        <?php echo form_input(["name" => "party_member_id[]", "id" => "parties-member-id", "type" => "hidden"]);
                        echo form_input(["name" => "party_lookup[]", "id" => "parties-lookup", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);?>
                    </div>
                    <div class="col-sm-12 col-xs-4 col-md-1 pr-0 padding-10 delete-icon delete-parties">
                        <a href="javascript:;" class="delete-link-parties"     onclick="objectDelete('parties', '2', '#contract-generate-container', event);"><i class="fa-solid fa-trash-can red"></i></a>
                    </div>
                    <div data-field="party_member_id_2"  class="inline-error d-none"></div>
                </div>
                <div class="row no-margin col-md-12 p-0 autocomplete-helper mb-3">
                    <div class="col-md-3 pr-0 col-xs-1">&nbsp;</div>
                    <div class="col-md-8 pr-0 col-xs-10">
                        <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 p-0 form-group row margin-bottom-10">
                <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 related-category"><?php echo $this->lang->line("category") . "<span class='label-count'> (2)</span>"?></label>
                <div class="row no-margin col-md-8 pr-0 col-xs-12">
                    <div class="col-md-8 p-0 col-xs-10">
                        <?php echo form_dropdown("party_category[]", $categories, "", "id=\"parties-category\"  class=\"form-control select-picker\"  data-live-search=\"true\"  data-field=\"administration-party_categories\"  data-field-id=\"category-2\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>  </div>
                    <div class="col-md-1 p-0 col-xs-2">
                        <a href="javascript:;" onclick="quickAdministrationDialog('party_categories', jQuery('#contract-generate-container'), true, false, false, jQuery('[data-field-id=category-2]'), false, 'contract');" class="btn btn-link parties-category-quick-add"><i class="fa-solid fa-square-plus p-1 font-18"> </i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 p-0 form-group row margin-bottom-5">
            <div class="col-md-3 pr-0 col-xs-1">&nbsp;</div>
            <div class="col-md-8 col-xs-10 add-more-link">
                <a href="javascript:;" onclick="objectContainerClone('parties', '#contract-generate-container', event);"><?php echo $this->lang->line("add_more");?></a>
            </div>
        </div>
        <div class="form-group row col-md-12 p-0"  id="contract-date-container">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5 required"><?php echo $this->lang->line("contract_date");?></label>
            <div class="col-md-8 pr-0 col-xs-10">
                <div class="input-group date col-md-4 col-xs-4 col-lg-5 p-0 date-picker"  id="contract-date"><?php echo form_input(["name" => "contract_date", "value" => "", "id" => "contract-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control"]);?>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                    </div>
                </div>
                <div data-field="contract_date"  class="inline-error d-none"></div>
            </div>
        </div>
        <div class="col-md-12 p-0 form-group row margin-bottom-10">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("renewal");?></label>
            <div class="col-md-8 pr-0 col-xs-10">
                <div class="col-md-4 col-xs-4 col-lg-5 p-0">
                    <?php echo form_dropdown("renewal_type", $renewals, "", "id=\"renewal\"  class=\"form-control select-picker\"  onchange=\"renewalEvents(jQuery('#contract-generate-container'));\"");?> </div>
                <div data-field="renewal_type"  class="inline-error d-none"></div>
            </div>
        </div>
        <div class="form-group row col-md-12 p-0"  id="start-date-container">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("start_date");?></label>
            <div class="col-md-8 pr-0 col-xs-10">
                <div class="input-group date col-md-4 col-xs-4 col-lg-5 p-0 date-picker" id="start-date">
                    <?php echo form_input(["name" => "start_date", "value" => "", "id" => "start-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control"]);?>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                    </div>
                </div>
                <div data-field="start_date"  class="inline-error d-none"></div>
            </div>
        </div>
        <div class="form-group row col-md-12 p-0"  id="end-date-container">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("end_date");?></label>
            <div class="col-md-8 pr-0 col-xs-10">
                <div class="row no-margin input-group date col-md-4 col-xs-4 col-lg-5 p-0 date-picker" id="end-date"> <?php echo form_input(["name" => "end_date", "value" => "", "id" => "end-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control"]);?>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                    </div>
                </div>
                <div data-field="end_date"  class="inline-error d-none"></div>
                <div class="row no-margin col-md-10 p-0"  id="notify-me-before-link">
                    <span class="assign-to-me-link-id-wrapper">
                        <a href="javascript:;" id="notify-me-link" onclick="notifyMeBeforeRenewal(jQuery('#contract-generate-container'));"><?php echo $this->lang->line("notify_me_before");?></a>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-12 p-0 form-group row d-none"  id="notify-me-before-container">
            <div class="col-md-12 p-0 form-group row">
                <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("notify_me_before");?></label>
                <div class="col-md-8 pr-0 col-xs-10"  id="notify-me-before">
                    <?php echo form_input(["name" => "notify_me_before[id]", "disabled" => true, "type" => "hidden"]);
                    echo form_input(["name" => "notify_me_before[time]", "class" => "form-control", "value" => "90", "id" => "notify-me-before-time", "disabled" => true]);
                    echo form_dropdown("notify_me_before[time_type]", $notify_me_before_time_types, "days", "class=\"form-control select-picker\"  id=\"notify-me-before-time-type\"  disabled");
                    ?>
                    <a href="javascript:;" onclick="hideRemindMeBefore(jQuery('#contract-generate-container'));" class="btn btn-link">
                        <i class="fa-solid fa-trash-can"></i>
                    </a>
                    <div data-field="notify_before"  class="inline-error d-none"></div>
                </div>
            </div>
            <div class="col-md-12 p-0 form-group row">
                <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("notify_users");?></label>
                <div class="col-md-8 pr-0">
                    <select name="notifications[emails][]"   placeholder="<?php echo $this->lang->line("select_users");?>"  id="notify-to-emails"  multiple="multiple"  tabindex="-1">
                    </select>
                    <div data-field="emails"  class="inline-error d-none"></div>
                </div>
            </div>
            <div class="col-md-12 p-0 form-group row">
                <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("notify_teams");?></label>
                <div class="col-md-8 pr-0">
                    <select name="notifications[teams][]" placeholder="<?php echo $this->lang->line("select_assigned_teams");?>" id="notify-to-teams"  multiple="multiple"  tabindex="-1">
                    </select>
                    <div data-field="teams"  class="inline-error d-none"></div>
                </div>
            </div>
         </div>
     </div>
     <div class="col-md-12 p-0">
         <div class="form-group row col-md-12 p-0 col-xs-12">
             <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("reference_number");?></label>
             <div class="col-md-6 pr-0">
                 <?php echo form_input("reference_number", "", 'id="ref-nb"  class="form-control"  dir="auto"  autocomplete="stop"');?>
                 <div data-field="reference_number"  class="inline-error d-none"></div>
             </div>
         </div>
     </div>
    <div class="form-group row col-md-12 p-0">
        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("provider_group");?></label>
        <div class="col-md-8 pr-0 col-xs-10">
            <?php echo form_dropdown("assigned_team_id", $assigned_teams, $assigned_team_id, "id=\"assigned-team-id\"  class=\"form-control select-picker\"  data-live-search=\"true\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>
            <div data-field="assigned_team_id"  class="inline-error d-none"></div>
        </div>
    </div>
    <div class="form-group row col-md-12 p-0">
        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("assignee");?></label>
        <div class="col-md-8 pr-0 col-xs-10">
            <?php  echo form_dropdown("assignee_id", $assignees, "", "id=\"assignee-id\"  data-live-search=\"true\"  class=\"form-control select-picker\"  data-size=\"" . $this->session->userdata("max_drop_down_length") . "\"");?>
            <div data-field="assignee_id"  class="inline-error d-none"></div>
        </div>
     </div>
     <div class="col-md-12 p-0"  id="contributors-container">
         <div class="form-group row col-md-12 p-0">
             <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("contributors");?></label>
             <div class="col-md-6 pr-0 col-xs-10 users-lookup-container">
                 <div class="input-group col-md-12 p-0 margin-bottom-5">
                     <?php echo form_input("contributors_lookup", "", 'id="contributors-lookup"  class="form-control users-lookup"');?>
                     <span class="input-group-addon bs-caret users-lookup-icon"   onclick="jQuery('#contributors-lookup').focus();"><span class="caret"></span></span>
                 </div>
                 <div id="selected-contributors"  class="height-auto no-margin"> </div>
                 <div data-field="contributors"  class="inline-error d-none"></div>
             </div>
         </div>
     </div>
    <div class="col-md-12 p-0 authorized-signatory-container">
        <div class="form-group row col-md-12 p-0 col-xs-12">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("authorized_signatory");?></label>
            <div class="col-md-8 pr-0">
                <div class="col-md-9 p-0 users-lookup-container margin-bottom-5">
                    <?php echo form_input(["name" => "authorized_signatory", "id" => "authorized-signatory-id", "value" => "", "type" => "hidden"]);
                    echo form_input(["name" => "authorized_signatory_name", "id" => "authorized-signatory-lookup", "value" => "", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);
                    ?>
                </div>
                <div data-field="authorized_signatory"  class="inline-error d-none"></div>
            </div>
        </div>
    </div>
    <div class="form-group row col-md-12 p-0">
        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("priority");?></label>
        <div class="col-md-8 pr-0 col-xs-10">
            <div class="col-md-4 col-xs-4 col-lg-5 p-0">
                <select name="priority"  class="form-control select-picker"  id="priority">
                    <?php $selected = "";
                    foreach ($priorities as $key => $value) {
                        if ($key == $default_priority) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }                     ?>
                        <option data-icon="priority-<?php    echo $key;  ?>"  <?php echo $selected;?> value="<?php echo $key;?>"><?php  echo $value; ?></option>
                        <?php
                    } ?>
                </select>
                <div data-field="priority"  class="inline-error d-none"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 p-0 margin-top-5 visible-in-cp-container">
        <div class="form-group row col-md-12 p-0 col-xs-12">
            <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("show_in_cp");?></label>
            <div class="col-md-8 pr-0">
                <div class="col-md-8 p-0 margin-bottom-5 chk-toggle margin-top-5">
                    <?php
                    echo form_input(["name" => "visible_in_cp", "value" => 0, "id" => "visible-in-cp", "type" => "hidden"]);
                    echo form_checkbox("", "", false, "id=\"visible-in-cp-checkbox\"  onclick=\"toggleCheckbox(jQuery('#visible-in-cp', jQuery('.visible-in-cp-container', '#contract-add-container')), jQuery(this));\"  class=\"checkbox\"");?>
                    <label for="visible-in-cp-checkbox"  class="label-success"></label>
                </div>
                <div data-field="visible_to_cp"  class="inline-error d-none margin-top-5"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 p-0">
        <div id="custom_fields_div">
            <?php $this->load->view("custom_fields/dialog_form_custom_field_template", ["custom_fields" => $custom_fields]);?>
            <div class="type-custum-fields-div"> </div>
        </div>
    </div>
    <div class="row no-margin col-md-12 p-0"  id="attachments-container">
        <label class="col-form-label text-right col-md-3 pr-0 col-xs-5"><i class="fa-solid fa-paperclip"></i>&nbsp;<?php echo $this->lang->line("upload");?></label>
        <div id="contract-attachments"  class="col-md-7 p-0 col-xs-10">
            <div class="col-md-10">
                <input id="contract-attachment-0"  name="contract_attachment_0"  type="file"  value=""  class="margin-top"/>
            </div>
            <?php echo form_input(["name" => "contract_attachments[]", "value" => "contract_attachment_0", "type" => "hidden"]);?>        </div>
        <div class="row no-margin col-md-12 p-0">
            <div class="offset-md-3 pr-0 col-xs-5 col-md-4">
                <div class="col-md-7 p-0 col-xs-10">
                    <div data-field="file"  class="inline-error d-none"></div>
                    <a href="javascript:;" onclick="dialogObjectAttachFile('contract', jQuery('#contract-generate-container'))" class="p-0"><?php echo $this->lang->line("add_more");?></a>
                </div>
            </div>
            <div data-field="files"  class="inline-error d-none"></div>
        </div>
    </div>
    <div>
        <!-- form2_csrf -->
        <input type="hidden"  name="<?php echo $this->security->get_csrf_token_name();?>"  value="<?php echo $this->security->get_csrf_hash();?>">  </div>
</div>

 <script type="text/javascript">
     availableEmails = <?php echo json_encode($users_emails);?>;
     availableAssignedTeams = <?php echo json_encode($assigned_teams_list);?>;
     selectedEmails = <?php echo json_encode(explode(";", $this->session->userdata("AUTH_email_address")));?>;
     selectedTeams = 'null';
     attachmentCount = 0;
 </script>