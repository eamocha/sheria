<?php
$hide_show_notification == "1" ? $hide_show_notification = "yes" : ($hide_show_notification = "");
?>
<div class="row-fluid cp-container">
    <div class=" col-md-10 col-md-offset-1" id="opinion-modal">
    <div class="opinion-dialog ">
          <div class="modal-content col-md-10 justify-content-center"  >
           
              <div class="modal-body" id="opinion-modal-wrapper">
                  <div id="opinionContainer" class="col-md-12 m-0 p-0 padding-10">
                     <?php
                     echo form_open("", 'name="opinionForm" id="opinion-form" method="post" class="form-horizontal "');
                     echo form_input(["name" => "id", "id" => "id", "value" => $opinionData["id"],  "type" => "hidden"]);
                      echo form_input(["name" => "user_id", "value" => $opinionData["user_id"], "type" => "hidden"]);
                     echo form_input(["name" => "archived", "value" => $opinionData["archived"], "type" => "hidden"]);
                     echo form_input(["name" => "opinion_location_id", "id" => "opinion_location_id", "value" => $opinionData["opinion_location_id"], "type" => "hidden"]);
                     echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => $hide_show_notification, "type" => "hidden"]);
                     echo form_input(["name" => "clone", "id" => "clone", "value" => "no", "type" => "hidden"]);
                     echo form_input(["name" => "assignment_id", "id" => "assignment-id", "value" => $assignments["id"], "type" => "hidden"]);
                     echo form_input(["name" => "user_relation", "id" => "user-relation", "value" => $assignments["user"]["id"], "type" => "hidden"]);?>
                      <div class="col-md-12 p-0" id="required-fields-opinion-demo">
                          <div class="col-md-12 p-0">
                               <div class="form-group col-md-12 p-0 row m-0 mb-10">
                                   <label class="control-label required "><?php echo $this->lang->line("title");?></label>
                                   <?php echo form_input(["name" => "title", "value" => isset($opinionData["title"]) ? $opinionData["title"] : NULL, "id" => "title", "class" => "m-0 form-control", "autofocus" => "autofocus"]);?>
                                       <div data-field="title" class="inline-error d-none padding-5"></div>
                               </div>
                           </div>
                           <div class="col-md-12 p-0">
                               <div class="form-group col-md-12 p-0 row m-0 mb-10" id="type-wrapper">
                                   <label class="control-label required restriction-tooltip"><?php echo $this->lang->line("opinion_type");?></label>
                                     <?php echo form_dropdown("opinion_type_id", $type, $opinionData["opinion_type_id"] ? $opinionData["opinion_type_id"] : $system_preferences["opinionTypeId"], 'id="type" class="form-control select-picker" data-live-search="true" data-field="administration-opinion_types" data-size="" . $this->session->userdata("max_drop_down_length") . "');?>
                                       <div data-field="opinion_type_id" class="inline-error d-none padding-5"></div>
                                </div>
                            </div>
                           <div class="col-md-12 p-0">
                               <div class="form-group col-md-12 p-0 row m-0 mb-10" id="description-wrapper">
                                   <label class="control-label required restriction-tooltip" ><?php echo $this->lang->line("instructions");?></label>
                                       <?php echo form_textarea("instructions", $opinionData["instructions"], ["id" => "instructions", "class" => "form-control", "rows" => "2", "dir" => "auto"]);?>
                                       <div data-field="instructions" class="inline-error d-none"></div>
                                  
                               </div>
                           </div>
                          
                          <div class="col-md-12 p-0">
                              <div class="form-group col-md-12 p-0 row m-0 mb-10" id="priority-wrapper">
                                  <label class="control-label  required "><?php echo $this->lang->line("priority");?></label>
                                 
                                          <select name="priority" class="form-control select-picker" id="priority" data-live-search="true">
                                              <?php 
                                              $selected = "";
                                              foreach ($priorities as $key => $value) {
                                                  if ($opinionData["priority"] && $opinionData["priority"] == $key) {
                                                      $selected = "selected";
                                                  } else {
                                                      if (!$opinionData["priority"] && $key == "medium") {
                                                          $selected = "selected";
                                                      } else {
                                                          $selected = "";
                                                      }
                                                  }
                                                  ?>
                                              <option data-icon="priority-<?php echo $key;?>" <?php    echo $selected;?> value="<?php echo $key;?>"><?php    echo $value;?></option>
                                              <?php 
                                              }?>
                                          </select>
                                    
                              </div>
                          </div>
                          <div class="datepair col-md-12 p-0" data-language="javascript" id="due-date-container">
                              <div class="form-group col-md-12 p-0 row m-0 mb-10">
                                  <label class="control-label  restriction-tooltip" id="dueDateLabelId"><?php  echo $this->lang->line("due_date");?></label>
                                  <div class="col-md-8 pr-0 col-xs-10" id="due-date-wrapper">
                                      <div class="row m-0">
                                          <div class="form-group input-group date date-picker col-md-9 p-0 mb-3" id="form-due-date">
                                              <?php echo form_input(["name" => "due_date", "id" => "form-due-date-input", "placeholder" => "YYYY-MM-DD", "value" => $cloned_date ?? $opinionData["due_date"], "class" => "date start form-control"]);
                                              ?>
                                              <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                                          </div>
                                          <?php
                                                                                    if ($system_preferences["hijriCalendarConverter"]) { ?>                                          <div class="col-md-3 mt-3 col-xs-2 visualize-hijri-date">
                                              <span class="assign-to-me-link-id-wrapper">
                                                  <a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#form-due-date', '#opinion-dialog'));" title="<?php    echo $this->lang->line("hijri_date_converter");    ?>"><?php    echo $this->lang->line("hijri");?></a>
                                              </span>
                                          </div>
                                          <?php
                                          }?>
                                      </div>
                                      <div class="row col-md-10 <?php echo $opinionData["due_date"] && !$notify_before ? "" : "d-none";?>" id="notify-me-before-link">
                                         <span class="assign-to-me-link-id-wrapper">
                                            <a href="javascript:;" id="notify-me-link" onclick="notifyMeBefore(jQuery('#opinion-form'));"><?php
                                            echo $this->lang->line("notify_me_before"); ?></a>
                                           </span>
                                      </div>
                                      <div data-field="due_date" class="inline-error d-none"></div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-12 p-0 d-none mb-10" id="notify-me-before-container">
                              <div class="form-group col-md-12 p-0 row m-0">
                                  <label class="control-label col-md-3 pr-0 col-xs-5"><?php echo $this->lang->line("notify_me_before");
                                  ?></label>
                                  <div class="col-md-8 d-flex justify-content-between col-xs-10" id="notify-me-before">
                                      <?php
                                      echo form_input(["name" => "notify_me_before[id]", "value" => $notify_before["id"], "disabled" => true, "type" => "hidden"]);
                                      echo form_input(["name" => "notify_me_before[time]", "class" => "form-control", "value" => isset($notify_before["time"]) ? $notify_before["time"] : $system_preferences["reminderIntervalDate"], "id" => "notify-me-before-time", "disabled" => true]);
                                      echo form_dropdown("notify_me_before[time_type]", $notify_me_before_time_types, $notify_before["time_type"], 'class="form-control select-picker" id="notify-me-before-time-type" disabled');
                                      ?>
                                      <label class="control-label"><?php
                                      echo $this->lang->line("reminder_by");
                                      ?></label>
                                      <?php 
                                      echo form_dropdown("notify_me_before[type]", $notify_me_before_types, $notify_before["type"], 'class="form-control select-picker" id="notify-me-before-type" disabled"');?>
                                        <a href="javascript:;" onclick="hideRemindMeBefore(jQuery('#opinion-form'));" class="btn btn-link my-auto">
                                        <i class="fa-solid fa-xmark"></i>
                                        </a>
                                        <div data-field="notify_before" class="inline-error d-none"></div>
                                        </div>
                              </div>
                          </div>
                           <div class="col-md-12 p-0 related-case-container core-access">
                               <?php 
                               echo form_input(["name" => "legal_case_id", "id" => "caseLookupId", "value" => $opinionData["legal_case_id"], "data-field" => "case_id", "type" => "hidden"]);?>
                               <div class="form-group col-md-12 p-0 row m-0">
                                   <label class="control-label col-md-3 pr-0 col-xs-5">
                                      <?php echo $this->lang->line("related_case");?> </label>
                                      <?php echo form_input(["value" => $allowOpinionPrivacy, "id" => "allow-opinion-privacy", "type" => "hidden"]);?>
                                   <div class="col-md-8 pr-0 col-xs-10">
                                   <?php echo form_input(["name" => "caseLookup", "id" => "caseLookup", "title" => isset($case_full_subject) && $case_full_subject ? $case_full_subject : $opinionData["caseSubject"], "value" => isset($opinionData["legal_case_id"]) && $opinionData["legal_case_id"] ? $case_model_code . $opinionData["legal_case_id"] . ": " . (42 < strlen($opinionData["caseSubject"]) ? mb_substr($opinionData["caseSubject"], 0, 42) . "..." : $opinionData["caseSubject"]) : (isset($case_subject) && $case_subject ? $case_subject : ""), "class" => "form-control search", "placeholder" => $this->lang->line("internalReference") . ", " . $this->lang->line("client_or_matter_placeholder")]);?>
                                    <div data-field="legal_case_id" class="inline-error d-none"></div>
                                    </div><?php
                                    if ($opinionData["id"]) {
                                        ?>
                                       <div id="case-subject" class="help-inline col-md-1 p-0 <?php  echo $opinionData["legal_case_id"] ? "" : "d-none";?>" >
                                       <a target="_blank" id="case-link" class="btn btn-link" href="<?php    echo $opinionData["caseCategory"] == "IP" ? "intellectual_properties/edit/" : "cases/edit/";
                                       echo $opinionData["legal_case_id"]; ?>">
                                       <i class="purple_color  <?php    echo $this->is_auth->is_layout_rtl() ? "fa-solid fa-arrow-left" : "fa-solid fa-arrow-right";?>"> </i>
                                       </a>
                                       </div><?php 
                                    }?>
                                   <div class="col-md-3 pr-0 col-xs-2" >&nbsp;</div>
                                   <div class="col-md-8 pr-0 col-xs-10">
                                       <div class="inline-text"><?php echo $this->lang->line("helper_case_autocomplete");?></div>
                                   </div>
                               </div>
                           </div>
                      </div>
                      <div class="col-md-12 p-0 related-contract-container contract-access">
                          <div class="form-group col-md-12 p-0 row m-0">
                              <label class="control-label col-md-3 pr-0 col-xs-5">
                                  <?php echo $this->lang->line("related_contract");?>
                              </label>
                              <div class="col-md-8 pr-0 col-xs-10">
                                  <?php echo form_input(["name" => "contract_id", "id" => "lookup-contract-id", "value" => $opinionData["contract_id"], "type" => "hidden"]);
                                  echo form_input(["name" => "contractLookup", "id" => "lookup-contract", "title" => isset($contract_full_name) && $contract_full_name ? $contract_full_name : $opinionData["contract_name"], "value" => isset($opinionData["contract_id"]) && $opinionData["contract_id"] ? $contract_model_code . $opinionData["contract_id"] . ": " . (42 < strlen($opinionData["contract_name"]) ? mb_substr($opinionData["contract_name"], 0, 42) . "..." : $opinionData["contract_name"]) : (isset($contract_name) && $contract_name ? $contract_name : ""), "class" => "form-control search"]);
                                  ?>
                                  <div data-field="contract_id" class="inline-error d-none"></div>   </div>
                                  <?php if ($opinionData["id"]) {
                                      ?>
                                      <div id="contract-name" class="help-inline col-md-1 p-0 <?php    echo $opinionData["contract_id"] ? "" : "d-none";?>" >
                                      <a target="_blank" id="contract-link" class="btn btn-link" href="<?php    echo site_url("modules/contract/contracts/view/" . $opinionData["contract_id"]);    ?>">
                                          <i class="purple_color  <?php    echo $this->is_auth->is_layout_rtl() ? "fa-solid fa-arrow-left" : "fa-solid fa-arrow-right";    ?>"> </i>
                                      </a>
                                      </div>
                              <?php 
                                  }
                                  ?>
                              <div class="col-md-3 pr-0 col-xs-2" >&nbsp;</div>
                              <div class="col-md-8 pr-0 col-xs-10">
                                  <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete");?></div>
                              </div>
                          </div>
                      </div>
                      <div id="stage-div">
                          <?php echo isset($litigation_stage_html) ? $litigation_stage_html : "";?>
                      </div>
                      <div class="col-md-12 p-0 show-rest-fields">
                          <div class="form-group col-md-12 p-0 row m-0">
                              <div class="col-md-3 pr-0">&nbsp;</div>
                              <div class="col-md-8 pr-0">
                                  <a href="javascript:;" onclick="showMoreFields(jQuery('#opinion-form'), jQuery('.estimated_effort', '#opinion-form'));"><?php echo $this->lang->line("more_fields");?></a>
                              </div>
                          </div>
                      </div>
                      <div class="d-none container-hidden-fields">
                          <div class="col-md-12 less-field-divider">
                              <hr>
                          </div>
                          <div class="col-md-12 p-0">
                              <div class="form-group p-0 row ">
                                  <label class="control-label col-md-3 pr-0 estimated_effort col-xs-5" title="<?php echo $this->lang->line("estimated_effort");?>"><?php
                                      echo $this->lang->line("est_effort");
                                      ?>
                                  </label>
                                  <div class="col-md-8  d-flex pr-0 col-xs-10">
                                      <div class="col-md-8 pl-0"><?php 
                                          echo form_input(["name" => "estimated_effort", "value" => $opinionData["estimated_effort"], "id" => "estimatedEffortHour", "class" => "m-0 form-control", "onblur" => "convertMinsToHrsMins(jQuery(this));"]);
                                          ?>
                                      </div>
                                      <div class="col-md-4 col-sm-5 col-xs-12 estimated-effort-hour vertical-aligned-tooltip-icon-container p-0 d-flex">
                                          <span>(e.g. 1h 20m)</span>
                                          <span role="button" class="tooltip-title effective-effort-tooltip ml-1 align-middle" title="<?php echo $this->lang->line("supported_time_units");?>"><i class="fa-solid fa-circle-question purple_color"></i></span>
                                      </div>
                                      <div data-field="estimated_effort" class="inline-error d-none padding-5"></div>
                                  </div>
                                  <?php
                                  if ($opinionData["id"] && isset($opinionData["effectiveEffort"]) && "" != $opinionData["effectiveEffort"]) {
                                      ?>
                                  <div class="col-md-12 p-0">
                                      <div class="col-md-3 pr-0">&nbsp;</div>
                                      <div class="col-md-8 pr-0">
                                          <span class="eff-effort">
                                              <?php 
                                              echo $this->lang->line("effectiveEffort");    echo ": ";  echo $this->timemask->timeToHumanReadable($opinionData["effectiveEffort"]); 
                                              $caseIdFilter = $opinionData["legal_case_id"] ? $opinionData["legal_case_id"] : 0;
                                              ?>
                                              <a href="<?php    echo site_url("time_tracking/my_time_logs/" . $caseIdFilter . "/" . $opinionData["id"]);   ?> " target="_blank" class="btn btn-default btn-link font-12"><?php    echo $this->lang->line("show_details");    ?></a>
                                          </span>
                                      </div>
                                  </div>
                                  <?php 
                                  }
                                  ?>
                              </div>
                          </div>
                          <div class="col-md-12 p-0" id="contributors-container">
                              <div class="form-group p-0 row">
                                  <label class="control-label col-md-3 pr-0 col-xs-5">
                                      <?php echo $this->lang->line("contributors");?>
                                  </label>
                                  <div class="col-md-8 pr-0 col-xs-10 users-lookup-container">
                                      <div class="input-group col-md-12 p-0 margin-bottom-5">
                                          <?php echo form_input("contributors_lookup", "", 'id="contributors-lookup" class="form-control users-lookup"');?>
                                          <span class="input-group-addon bs-caret users-lookup-icon" onclick="jQuery('#contributors-lookup').focus();"><span class="caret"></span></span>
                                      </div>
                                      <div id="selected-contributors" class="height-auto m-0"><?php
                                      if (!empty($contributors)) {
                                          $select_options_name = "contributors";
                                          foreach ($contributors as $key => $value) {
                                              ?>
                                          <div class="row multi-option-selected-items m-0" id="<?php echo $select_options_name;   echo $value["id"];?>">
                                          <span id="<?php echo $value["id"]; ?>">
                                              <?php echo $value["status"] === "Inactive" ? $value["name"] . "(" . $this->lang->line("Inactive") . ")" : $value["name"]; ?>
                                          </span>
                                          <?php echo form_input(["value" => $value["id"], "name" => $select_options_name . "[]", "type" => "hidden"]);?>
                                              <a href="javascript:;" class="btn btn-default btn-xs btn-link pull-right remove-button" tabindex="-1" onclick="removeBoxElement(jQuery(this.parentNode), '#selected-contributors', 'contributors-container', '#opinion-dialog');"><i class="fa-solid fa-trash-can red"></i></a>
                                          </div><?php
                                          }
                                      }?>
                                      </div>
                                      <div data-field="contributors" class="inline-error d-none"></div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-12 p-0">
                              <div class=" form-group p-0 row mb-10">
                                  <label class="control-label pr-0 col-md-3 col-xs-5"><?php
                                      echo $this->lang->line("location");
                                      ?>
                                  </label>
                                  <div class="col-md-8 p-0 d-flex col-xs-10">
                                      <div class="col-md-11">
                                          <?php
                                          echo form_input(["name" => "location", "id" => "location", "class" => " form-control  lookup", "data-field" => "administration-opinion_locations", "title" => $this->lang->line("start_typing"), "value" => $opinionData["location"]]);
                                          ?>
                                      </div>
                                      <div class="col-md-1 p-0 col-xs-1">
                                          <a href="javascript:;" onclick="quickAdministrationDialog('opinion_locations', jQuery('#opinion-form', '#opinion-dialog'), true);"  class="btn btn-link"><i class="fa-solid fa-square-plus p-1 font-18"></i></a>
                                      </div>
                                      <div data-field="opinion_location_id" class="inline-error d-none"></div>
                                  </div>
                                  <div class="col-md-12 p-0 row m-0">
                                      <div class="col-md-3 p-0 col-xs-2" >&nbsp;</div>
                                      <div class="col-md-8 p-0 col-xs-10">
                                          <div class="inline-text m-0"><?php
                                              echo $this->lang->line("helper_autocomplete");
                                              ?>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-12 p-0">
                              <div id="custom_fields_div"><?php
                            //  $this->load->view("custom_fields/dialog_form_custom_field_template", ["custom_fields" => $custom_fields]);?>
                              </div>
                          </div>
                          <div class="col-md-12 p-0 users-lookup-container">
                              <div class="form-group p-0 row m-0">
                                  <label class="control-label col-md-3 pr-0 col-xs-5"></label>
                                  <div class="col-md-8 p-0 col-xs-10">
                                      <div class="checkbox" >
                                          <label>
                                              <?php
                                              echo form_checkbox("private", "yes", $opinionData["private"] == "yes", 'id="private" onclick="enableDisableOpinionUsersLookup()"');
                                              echo $this->lang->line("set_as_private");?>
                                          </label>
                                      </div>
                                      <div class="d-none opinion-users-container margin-top-5">
                                          <label id="opinionUsersLabelId"><?php echo $this->lang->line("shared_with");?>
                                          </label>
                                          <div class="margin-bottom">
                                              <?php echo form_input(["id" => "lookupOpinionUsers", "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]);
                                              ?>
                                          </div>
                                          <div id="selected_opinion_users" class="row height-99 m-0">
                                              <?php if (!empty($opinionUsers)) {
                                                  $select_options_name = "Opinion_Users";
                                                  foreach ($opinionUsers as $user) {
                                                      ?>                                              
                                              <div class="row multi-option-selected-items m-0" id="<?php  echo $select_options_name;  echo $user["id"];        ?>">
                                                  <span id="<?php   echo $user["id"];        ?>">
                                                      <?php        echo $user["status"] === "Inactive" ? $user["name"] . "(" . $this->lang->line("Inactive") . ")" : $user["name"];
                                                      ?>
                                                  </span>
                                                  <?php
                                                  echo form_input(["value" => $user["id"], "name" => $select_options_name . "[]", "type" => "hidden"]);        ?>
                                                  <a href ="javascript:;" class ="btn btn-default btn-xs btn-link pull-right" tabindex ="-1" onclick ="unsetNewCaseMultiOption(this.parentNode);">
                                                      <i class = "fa-solid fa-trash-can red"></i></a>
                                              </div>
                                                      <?php
                                                  }
                                              }
                                              ?>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <?php if (!$opinionData["id"]) {    ?>
                          <div class="clear clearfix clearfloat"></div>
                          <hr class="col-md-12 p-0"/>
                          <div class="p-0 row m-0" id="attachments-container">
                              <label class="control-label col-md-3 pr-0 col-xs-5"><i class="fa-solid fa-paperclip"></i>&nbsp;<?php
                                  echo $this->lang->line("attach_file");    ?>
                              </label>
                              <div id="opinion-attachments" class="col-md-8 pr-0 col-xs-10 mb-10">
                                  <div class="col-md-11">
                                      <input id="opinion-attachment-0" name="opinion_attachment_0" type="file" value="" class="margin-top" />
                                  </div>
                                  <?php
                                  echo form_input(["name" => "opinion_attachments[]", "value" => "opinion_attachment_0", "type" => "hidden"]);
                                  ?>
                              </div>
                              <div class="col-md-12">
                                  <div class="offset-md-3 pr-0 col-xs-5">
                                      <div class="col-md-7 col-xs-10">
                                          <div data-field="file"class="inline-error d-none"></div>
                                          <a href="javascript:;" onclick="dialogObjectAttachFile('opinion', jQuery('#opinion-dialog'))" class="btn-link"><?php
                                              echo $this->lang->line("add_more");    ?>
                                          </a>
                                      </div>
                                  </div>
                                  <div data-field="files" class="inline-error d-none"></div>
                              </div>
                          </div>
                          <?php
                          }
                          ?>
                      </div>
                      <div class="col-md-12 p-0 hide-rest-fields d-none">
                          <div class="form-group p-0 row m-0 ">
                              <div class="col-md-3 p-0">&nbsp;</div>
                              <div class="col-md-8 p-0">
                                  <a href="javascript:;" onclick="showLessFields(jQuery('#opinion-form'));"><i class="fa fa-angle-double-up"></i>&nbsp;<?php echo $this->lang->line("less_fields");?></a>
                              </div>
                          </div>
                      </div>
                  </div>
                  <?php
                  form_close();?>
              </div>
              <div class="modal-footer justify-content-between"><?php
                 // $this->load->view("templates/send_email_option_template", ["container" => "#opinionContainer", "hide_show_notification" => $hide_show_notification]);
                  ?>
                  <div>
                      <span class="loader-submit"></span>
                      <div class="btn-group">
                          <button type="button" class="btn btn-save btn-add-dropdown modal-save-btn" id="save-opinion-btn"><?php
                              echo $this->lang->line("save");
                              ?>
                          </button>
                          <?php
                          if (!$opinionData["id"]) {
                              ?>
                              <button type="button" class="btn btn-save dropdown-toggle btn-add-dropdown modal-save-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <span class="caret"></span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right">
                                  <a class="dropdown-item" href="javascript:;" onclick="cloneDialog(jQuery('#opinion-dialog'), opinionFormSubmit);"><?php
                                      echo $this->lang->line("create_another");    ?>
                                  </a>
                              </div>
                          <?php }?>
                      </div>
                      <button type="button" class="btn btn-link" data-dismiss="modal"><?php echo $this->lang->line("cancel"); ?></button>
                  </div>
              </div><!-- /.modal-footer -->
          </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  </div>
  <script type="text/javascript">
      userLoggedInName = "<?php echo addslashes($this->is_auth->get_fullname());?>";
      authIdLoggedIn = '<?php echo $this->is_auth->get_user_id();?>';
      attachmentCount = 0;
      <?php if ($notify_before) { ?>
      notifyMeBefore(jQuery('#opinion-dialog'));
      <?php
      }
      ?>
      jQuery(document).ready(function () {
          jQuery('.effective-effort-tooltip').tooltipster({
              contentAsHTML: true,
              timer: 22800,
              animation: 'grow',
              delay: 200,
              theme: 'tooltipster-default',
              touchDevices: false,
              trigger: 'hover',
              maxWidth: 350,
              interactive: true
          });
          if(jQuery('#allow-opinion-privacy','#opinion-modal').val() == 1){
              jQuery('#caseLookup','#opinion-modal').bind('typeahead:select', function (ev, suggestion) {
              jQuery.ajax({
                  dataType: 'JSON',
                  url: getBaseURL() + 'opinions/check_case_privacy',
                  type: 'GET',
                  data: {case_id: suggestion.id},
                  success: function (response) {
                      if (response.result) {
                          jQuery('#private','#opinion-modal').prop( "checked", true );
                          enableDisableOpinionUsersLookup()
                          var select_options_name = 'Opinion_Users';
                          var legal_case_users = '';
                          for (var user of response.users) {
                              var username = user.status === 'Inactive' ? (user.name + '(' + _lang.custom.Inactive + ')') : user.name
                              legal_case_users += '<div class="row multi-option-selected-items m-0" id="' + select_options_name + user.id + '">' +
                              '<span id="' + user.id + '">' + username + '</span>' +
                              '<input type="hidden" value="'+ user.id +'" name="' + select_options_name + '[]">'+
                              '<a href = \"javascript:;" class = \"btn btn-default btn-xs btn-link pull-right" tabindex = \"-1" onclick = \"unsetNewCaseMultiOption(this.parentNode);">'+
                              '<i class = \"fa-solid fa-trash-can red"></i></a>'+
                              '</div>'                      
                          }
                          jQuery('#selected_opinion_users','#opinion-modal').html( legal_case_users);
                          pinesMessage({ty: 'information', m: response.msg});
                          showMoreFields(jQuery('#opinion-form', '#opinion-modal'))
                      } else {
                          jQuery('#private','#opinion-modal').prop( "checked", false );
                          jQuery('#selected_opinion_users','#opinion-modal').empty();
                          enableDisableOpinionUsersLookup()
                          showLessFields(jQuery('#opinion-form', '#opinion-modal'))
                      }
                                
                  },
                  error: defaultAjaxJSONErrorsHandler
              }); 
          });
          }
      });
      //addOpinionDemo();
  </script>
  <style>
      .datepicker-dropdown.datepicker-orient-top:after , .datepicker-dropdown.datepicker-orient-left:before{
          bottom: auto !important;
      }
  </style>
