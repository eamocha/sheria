<div id="grid-unscrollable" class="row no-margin">
 <div class="col-md-12 k-padding p-0">
         <div class="card card-default" id="filtersFormWrapper" style="display: none;">
             <div class="card-header padding-all-10">
                 <h4><?php echo $this->lang->line("conveyancing_search_filters");?></h4>
             </div>
             <?php echo form_open("", "name='searchFilters' id='searchFilters' method='post' class='form-horizontal'");?>
             <div class="card-body">
                 <?php echo form_input(["id" => "userId", "auth" => $authUserId, "value" => $assignedToFixedFilter, "type" => "hidden"]);
                 echo form_input(["name" => "take", "value" => "20", "type" => "hidden"]);
                 echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
                 echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
                 echo form_input(["name" => "pageSize", "value" => "20", "type" => "hidden"]);
                 echo form_input(["name" => "filter.logic", "id" => "defaultFilterLogic", "value" => "and", "type" => "hidden"]);
                 echo form_input(["name" => "quickSearch.logic", "id" => "", "value" => "and", "type" => "hidden"]);

                 echo form_input(["id" => "display-columns", "value" => implode(",", $selected_columns), "type" => "hidden"]);
                 echo form_input(["id" => "model", "value" => $model, "type" => "hidden"]);
                 ?>
                 <div class="col-md-12 no-margin">
                     <div class="d-none">
                         <div class="form-group">
                             <div class="col-md-3"><?php
                             echo form_input(["name" => "quickSearch.filters[0].filters[0].field", "id" => "quickSearchFilterSubjectField", "value" => "description", "type" => "hidden"]);
                             echo form_input(["name" => "quickSearch.filters[0].filters[0].operator", "id" => "quickSearchFilterSubjectOperator", "value" => "contains", "type" => "hidden"]);
                             echo form_input(["name" => "quickSearch.filters[0].filters[0].value", "id" => "quickSearchFilterSubjectValue", "class" => "form-control", "type" => "hidden"]);
                             ?>
                             </div>
                         </div>
                         <div class="form-group">
                             <div class="col-md-3">
                                 <?php echo form_input(["name" => "quickSearch.filters[1].filters[0].field", "id" => "quickSearchFilterAssignedToField", "value" => "assigned_to", "type" => "hidden"]);
                                 echo form_input(["name" => "quickSearch.filters[1].filters[0].operator", "id" => "quickSearchFilterAssignedToOperator", "value" => "eq", "type" => "hidden"]);
                                 echo form_input(["name" => "quickSearch.filters[1].filters[0].value", "id" => "quickSearchFilterAssignedToValue", "class" => "form-control", "value" => $assignedToFixedFilter, "type" => "hidden"]);
                                 ?>
                             </div>
                         </div>
                         <div class="form-group">
                             <div class="col-md-3">
                                 <?php echo form_input(["name" => "quickSearch.filters[2].filters[0].field", "id" => "quickSearchFilterOpinionTypeField", "value" => "conveyancing_instruments.instrument_type_id", "type" => "hidden"]);
                                 echo form_input(["name" => "quickSearch.filters[2].filters[0].operator", "id" => "quickSearchFilterOpinionTypeOperator", "value" => "neq", "type" => "hidden"]);
                                 echo form_input(["name" => "quickSearch.filters[2].filters[0].value", "id" => "quickSearchFilterOpinionTypeValue", "class" => "form-control", "type" => "hidden"]);
                                 ?>                            
                             </div>
                         </div>
                         <div class="form-group">
                             <div class="col-md-3">
                                 <?php
                             echo form_input(["name" => "quickSearch.filters[3].filters[0].field", "id" => "quickSearchFilterArchivedField", "value" => "conveyancing_instruments.archived", "type" => "hidden"]);
                             echo form_input(["name" => "quickSearch.filters[3].filters[0].operator", "id" => "quickSearchFilterArchivedOperator", "value" => "eq", "type" => "hidden"]);
                             echo form_input(["name" => "quickSearch.filters[3].filters[0].value", "id" => "quickSearchFilterArchivedValue", "class" => "form-control", "value" => $defaultArchivedValue, "type" => "hidden"]);
                             ?>
                             </div>
                         </div>
                         <div class="form-group">
                             <div class="col-md-3">  <?php
                             echo form_input(["name" => "quickSearch.filters[4].filters[0].field", "id" => "quickSearchFilterReporterField", "value" => "conveyancing_instruments.reporter", "type" => "hidden"]);
                             echo form_input(["name" => "quickSearch.filters[4].filters[0].operator", "id" => "quickSearchFilterReporterOperator", "value" => "eq", "type" => "hidden"]);
                             echo form_input(["name" => "quickSearch.filters[4].filters[0].value", "id" => "quickSearchFilterReporterValue", "class" => "form-control", "value" => $reported_by_me_auth, "type" => "hidden"]);
                             ?>
                             </div>
                         </div>
                         <div class="form-group">
                             <div class="col-md-3">
                                 <?php
                                 echo form_input(["name" => "quickSearch.filters[5].filters[0].field", "id" => "quickSearchFilterTitleField", "value" => "conveyancing_instruments.title", "type" => "hidden"]);
                                 echo form_input(["name" => "quickSearch.filters[5].filters[0].operator", "id" => "quickSearchFilterTitleOperator", "value" => "eq", "type" => "hidden"]);
                                 echo form_input(["name" => "quickSearch.filters[5].filters[0].value", "id" => "quickSearchFilterTitleValue", "class" => "form-control", "type" => "hidden"]);
                                 ?>
                             </div>
                         </div>
                     </div>
                     <div class="row no-margin">
                         <div class="form-group row col-md-6">
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("opinion_id");?></label>
                             <div class="col-md-3">
                                 <?php
                                 echo form_input(["name" => "filter.filters[1].filters[0].field", "value" => "conveyancing_instruments.id", "type" => "hidden"]);
                                 echo form_dropdown("filter.filters[1].filters[0].operator", $operators["number"], "contains", 'id="idOpertator" class="form-control sf-operator"');
                                 ?>
                             </div>
                             <div class="col-md-5">
                                 <?php echo form_input(["id" => "idValue", "name" => "filter.filters[1].filters[0].value", "value" => "", "class" => "form-control sf-value"]);                                 ?>
                             </div>
                         </div>
                         <div class="form-group row col-md-6">
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("opinion_status");?></label>
                             <div class="col-md-3">
                                 <?php
                                 echo form_input(["name" => "filter.filters[5].filters[0].field", "value" => "status", "type" => "hidden"]);
                                 echo form_dropdown("filter.filters[5].filters[0].operator", $operators["group_list"], "", 'id="statusOpertator" class="form-control sf-operator"');
                                 ?>
                             </div>
                             <div class="col-md-5">
                                 <?php echo form_dropdown("filter.filters[5].filters[0].value", $statuses, "", 'id="statusValue" multiple="multiple" class="multi-select form-control sf-value"'); ?>
                             </div>
                         </div>
                     </div>
                     <div class="row no-margin core-access">
                         <div class="form-group row col-md-6">
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("transaction_type");?></label>
                             <div class="col-md-3">
                                 <?php echo form_input(["name" => "filter.filters[18].filters[0].field", "value" => "conveyancing_instruments.transaction_type_id", "type" => "hidden"]);
                                 echo form_dropdown("filter.filters[18].filters[0].operator", $operators["number"], "contains", 'id="idOpertator" class="form-control sf-operator"');?>
                             </div>
                             <div class="col-md-5">
                                 <?php echo form_input(["id" => "caseIdValue", "name" => "filter.filters[18].filters[0].value", "value" => "", "class" => "form-control sf-value"]);?>
                             </div>
                         </div>
                         <div class="form-group row col-md-6">
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("related_case");?></label>
                             <div class="col-md-3"><?php
                             echo form_input(["name" => "filter.filters[3].filters[0].field", "id" => "lookup_type", "value" => "legal_case_id", "type" => "hidden"]);
                             echo form_dropdown("filter.filters[3].filters[0].operator", $operators["lookup"], "lookUp", 'id="caseSubjectOpertator" class="form-control  sf-operator" onchange="changeLookUp(value);"');?>
                             </div>
                             <div class="col-md-5">
                                     <?php echo form_input(["id" => "caseIdValueLookUp", "name" => "filter.filters[3].filters[0].value", "value" => "", "class" => "form-control lookup sf-value ", "placeholder" => $this->lang->line("client_or_matter_placeholder"), "title" => $this->lang->line("client_or_matter_placeholder")]);?>
                             </div>
                         </div>
                     </div>
                     <div class="row no-margin">
                         <div class="form-group row col-md-6 core-access">
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("title");?></label>
                             <div class="col-md-3">
                                 <?php echo form_input(["name" => "filter.filters[21].filters[0].field", "value" => "title", "type" => "hidden"]); 
                                 echo form_dropdown("filter.filters[21].filters[0].operator", $operators["text"], "contains", 'id="titleOpertator" class="form-control sf-operator"');?>
                             </div>
                             <div class="col-md-5">
                                 <?php echo form_input(["dir" => "auto", "id" => "typeValue", "name" => "filter.filters[21].filters[0].value", "value" => "", "class" => "form-control sf-value"]);?>
                             </div>
                         </div>
                         <div class="form-group row col-md-6 contract-access">
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("related_contract");?></label>
                             <div class="col-md-3">
                                 <?php echo form_input(["name" => "filter.filters[19].filters[0].field", "value" => "contract_id", "type" => "hidden"]);
                                 echo form_dropdown("filter.filters[19].filters[0].operator", $operators["number"], "contains", 'id="contractOperator" class="form-control sf-operator"');?>
                             </div>
                                 <div class="col-md-5">
                                     <?php echo form_input(["id" => "contractLookup", "value" => "", "class" => "form-control lookup sf-value", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]);
                                     echo form_input(["name" => "filter.filters[19].filters[0].value", "id" => "contract-value", "value" => "", "type" => "hidden", "class" => "form-control filter-value-inputs-change"]);
                                     ?>
                                 </div>
                         </div>
                     </div>
                     <div class="row no-margin">
                         <div class="form-group row col-md-6">
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("description");?></label>
                             <div class="col-md-3">
                                 <?php echo form_input(["name" => "filter.filters[6].filters[0].field", "value" => "description", "type" => "hidden"]);
                                 echo form_dropdown("filter.filters[6].filters[0].operator", $operators["text"], "contains", 'id="descriptionOpertator" class="form-control sf-operator"');                             ?>
                             </div>
                             <div class="col-md-5">
                                <?php echo form_input(["dir" => "auto", "id" => "typeValue", "name" => "filter.filters[6].filters[0].value", "value" => "", "class" => "form-control sf-value"]); ?>
                             </div>
                         </div>
                         <div class="form-group row col-md-6">
                             <?php $readonly = "";
                             if (!empty($assignedToFixedFilter)) {
                                 $readonly = "readonly=''";
                             } ?>
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("assigned_to"); ?></label>
                             <div <?php echo $readonly ? "" : "class='col-md-3'"; ?>><?php echo form_input(["name" => "filter.filters[7].filters[0].field", "value" => "assigned_to", "type" => "hidden"]);
                             if ($readonly) {
                                 echo form_input(["name" => "filter.filters[7].filters[0].operator", "value" => "eq", "class" => "form-control sf-operator", "readonly" => true, "type" => "hidden"]);
                             } else {
                                 echo form_dropdown("filter.filters[7].filters[0].operator", $operators["text"], "contains", 'id="assignedToOpertator" class="form-control sf-operator"');
                             }?>
                             </div>
                             <div <?php echo $readonly ? "class='col-md-8'" : "class='col-md-5'";?>>
                                 <?php
                                 $assignee_input_attributes = ["id" => "assignedToValue", "name" => "filter.filters[7].filters[0].value", "value" => $assignedToFixedFilter, "class" => "form-control lookup sf-value", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")];
                                 if ($readonly) {
                                     $assignee_input_attributes["readonly"] = true;
                                 }
                                 echo form_input($assignee_input_attributes); ?>                                
                             </div>
                         </div>
                     </div>
                     <div class="row no-margin">
                         <div class="form-group row col-md-6">
                             <label  class="control-label col-md-3 "><?php echo $this->lang->line("pfNo");?></label>
                             <div class="col-md-3">
                                 <?php echo form_input(["name" => "filter.filters[8].filters[0].field", "value" => "conveyancing_instruments.pfNo", "type" => "hidden"]);
                                 echo form_dropdown("filter.filters[8].filters[0].operator", $operators["number_only"], "", 'id="pfNoOpertator" class="form-control sf-operator"');                             ?>
                             </div>
                             <div class="col-md-5">
                                 <?php echo form_input(["id" => "estimatedEffortValue", "name" => "filter.filters[8].filters[0].value", "value" => "", "class" => "form-control sf-value", "data-validation-engine" => "validate[funcCall[validateHumanReadableTime]]"]);                                 ?>
                             </div>
                         </div>
                         <div class="form-group row col-md-6">
                             <?php $readonly = "";
                             if (!empty($reported_by_me_auth)) {
                                 $readonly = "readonly=''";
                             }?>
                             <label  class="control-label col-md-3"><?php echo $this->lang->line("requested_by"); ?></label>
                             <div <?php echo $readonly ? "" : "class='col-md-3'"?>>
                                 <?php echo form_input(["name" => "filter.filters[16].filters[0].field", "value" => "reporter", "class" => "form-control", "type" => "hidden"]);?>
                                 <?php if ($readonly) {
                                     echo form_input(["name" => "filter.filters[16].filters[0].operator", "value" => "eq", "class" => "form-control sf-operator", "readonly" => true, "type" => "hidden"]);
                                 } else {
                                     echo form_dropdown("filter.filters[16].filters[0].operator", $operators["text"], "contains", 'id="reporterOpertator" class="form-control sf-operator"');
                                 }?>
                             </div>
                                 <div <?php echo $readonly ? "class='col-md-8'" : "class='col-md-5'";?>>
                                     <?php $reporter_input_attributes = ["id" => "reporterValue", "name" => "filter.filters[16].filters[0].value", "value" => $reported_by_me_auth, "class" => "form-control lookup sf-value", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")];
                                     if ($readonly) {
                                         $reporter_input_attributes["readonly"] = true;
                                     }
                                     echo form_input($reporter_input_attributes);?>
                                 </div>
                             </div>
                         </div>
                         <div class="row no-margin">
                             <div class="form-group row col-md-6">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("priority");?></label>
                                 <div class="col-md-3">
                                     <?php echo form_input(["name" => "filter.filters[15].filters[0].field", "value" => "conveyancing_instruments.priority", "type" => "hidden"]);
                                     echo form_dropdown("filter.filters[15].filters[0].operator", $operators["group_list"], "", 'id="priorityOpertator" class="form-control sf-operator"');
                                     ?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_dropdown("filter.filters[15].filters[0].value", $priorityValues, "", 'id="priorityValue" multiple="multiple" class="multi-select form-control sf-value"'); ?>
                                 </div>
                             </div>
                             <div class="form-group row col-md-6">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("opinion_type");?></label>
                                 <div class="col-md-3">
                                     <?php echo form_input(["name" => "filter.filters[4].filters[0].field", "value" => "conveyancing_instruments.instrument_type_id", "type" => "hidden"]);
                                     echo form_dropdown("filter.filters[4].filters[0].operator", $operators["group_list"], "", 'id="conveyancing_instrumentOpertator" class="form-control sf-operator"');?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_dropdown("filter.filters[4].filters[0].value", $types, "", 'id="typeValue" multiple="multiple" class="multi-select form-control sf-value"');?>
                                 </div>
                             </div>
                         </div>
                     <div class="row">
                         <div class="col-md-6 no-padding-right">
                             <h4> <?php echo $this->lang->line("more_filters_criteria");?>
                                 <label class="control-label">  <a onclick="collapse('toggleDetails', 'advancedSearchFields', true);  fixFooterPosition();" id="toggleDetails" title="<?php echo $this->lang->line("show_details"); ?>" ><i class="dottedIcon"></i></a> </label>
                             </h4>
                         </div>
                     </div>
                     <div class="d-none" id="advancedSearchFields">
                         <div class="row no-margin">
                             <div class="form-group row col-md-6">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("created_by"); ?></label>
                                 <div class="col-md-3">
                                     <?php echo form_input(["name" => "filter.filters[10].filters[0].field", "value" => "conveyancing_instruments.createdBy", "type" => "hidden"]);
                                     echo form_dropdown("filter.filters[10].filters[0].operator", $operators["text"], "contains", 'id="createdByOpertator" class="form-control sf-operator"');
                                     ?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_input(["id" => "createdByValue", "name" => "filter.filters[10].filters[0].value", "value" => "", "class" => "form-control lookup sf-value", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]); ?>
                                 </div>
                             </div>
                             <div class="form-group row col-md-6" id="createdOnContainer">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("created_on");?></label>
                                 <div class="col-md-3 no-margin">
                                     <?php echo form_input(["name" => "filter.filters[11].filters[0].field", "value" => "conveyancing_instruments.createdOn", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[11].filters[0].operator", "value" => "cast_eq", "class" => "start-date-operator", "type" => "hidden"]);
                                     echo form_dropdown("dateOperator1", $operators["date"], "", 'id="dueDateOpertator" class="form-control  sf-operator" onchange="onchangeOperatorsFiltersDate(this, \"createdOnContainer\");"');?>
                                 </div>
                                     <div class="col-md-5">
                                         <?php echo form_input(["id" => "createdOnValue", "name" => "filter.filters[11].filters[0].value", "value" => "", "autocomplete" => "off", "class" => "form-control sf-value", "placeholder" => "YYYY-MM-DD"]);?>
                                     </div>
                                 <div class="col-md-5 offset-md-6 margin-top">
                                     <?php
                                     echo form_input(["name" => "filter.filters[11].logic", "value" => "and", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[11].filters[1].field", "value" => "conveyancing_instruments.createdOn", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[11].filters[1].operator", "value" => "cast_lte", "type" => "hidden"]);
                                     echo form_input(["id" => "createdOnEndValue", "name" => "filter.filters[11].filters[1].value", "value" => "", "autocomplete" => "off", "class" => "form-control d-none end-date-filter sf-value2", "placeholder" => "YYYY-MM-DD"]);
                                     ?>
                                 </div>
                             </div>
                         </div>
                         <div class="row no-margin">
                             <div class="form-group row col-md-6">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("modified_by");?></label>
                                 <div class="col-md-3">
                                     <?php echo form_input(["name" => "filter.filters[12].filters[0].field", "value" => "conveyancing_instruments.modifiedByName", "type" => "hidden"]);
                                     echo form_dropdown("filter.filters[12].filters[0].operator", $operators["text"], "contains", 'id="modifiedByOpertator" class="form-control sf-operator"');
                                     ?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_input(["id" => "modifiedByValue", "name" => "filter.filters[12].filters[0].value", "value" => "", "class" => "form-control lookup sf-value", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]);?>
                                 </div>
                             </div>
                             <div class="form-group row col-md-6" id="modifiedOnContainer">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("modified_on");?></label>
                                 <div class="col-md-3 no-margin">
                                     <?php echo form_input(["name" => "filter.filters[13].filters[0].field", "value" => "conveyancing_instruments.modifiedOn", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[13].filters[0].operator", "value" => "cast_eq", "class" => "start-date-operator", "type" => "hidden"]);
                                     echo form_dropdown("dateOperator1", $operators["date"], "", 'id="dueDateOpertator" class="form-control sf-operator" onchange="onchangeOperatorsFiltersDate(this, \"modifiedOnContainer\");"');?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_input(["id" => "modifiedOnValue", "name" => "filter.filters[13].filters[0].value", "autocomplete" => "off", "value" => "", "class" => "form-control sf-value", "placeholder" => "YYYY-MM-DD"]);?></div>
                                 <div class="col-md-5 offset-md-6 margin-top">
                                     <?php echo form_input(["name" => "filter.filters[13].logic", "value" => "and", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[13].filters[1].field", "value" => "conveyancing_instruments.modifiedOn", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[13].filters[1].operator", "value" => "cast_lte", "type" => "hidden"]);
                                     echo form_input(["id" => "modifiedOnEndValue", "name" => "filter.filters[13].filters[1].value", "value" => "", "autocomplete" => "off", "class" => "form-control d-none end-date-filter sf-value2", "placeholder" => "YYYY-MM-DD"]);
                                     ?></div>
                             </div>
                         </div>
                         <div class="row no-margin">
                             <div class="form-group row col-md-6">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("archived");?></label>
                                 <div class="col-md-3">
                                     <?php echo form_input(["name" => "filter.filters[14].filters[0].field", "value" => "conveyancing_instruments.archived", "type" => "hidden"]);
                                     echo form_dropdown("filter.filters[14].filters[0].operator", $operators["list"], "eq", 'id="archivedOpertator" class="form-control sf-operator"');?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_dropdown("filter.filters[14].filters[0].value", $archivedValues, $defaultArchivedValue, 'id="archivedValue" class="form-control sf-value"');?>
                                 </div>
                             </div>
                             <div class="form-group row col-md-6" id="dueDateContainer">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("due_date");?></label>
                                 <div class="col-md-3 no-margin">
                                     <?php echo form_input(["name" => "filter.filters[9].filters[0].field", "value" => "due_date", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[9].filters[0].operator", "value" => "cast_eq", "class" => "start-date-operator", "type" => "hidden"]);
                                     echo form_dropdown("dateOperator2", $operators["date"], "", 'id="dueDateOpertator" class="form-control sf-operator" onchange="onchangeOperatorsFiltersDate(this, \"dueDateContainer\");"');?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_input(["id" => "dueDateValue", "name" => "filter.filters[9].filters[0].value", "value" => "", "autocomplete" => "off", "class" => "form-control sf-value", "placeholder" => "YYYY-MM-DD"]); ?>
                                 </div>
                                 <div class="col-md-5 offset-md-6 margin-top">
                                     <?php echo form_input(["name" => "filter.filters[9].logic", "value" => "and", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[9].filters[1].field", "value" => "due_date", "type" => "hidden"]);
                                     echo form_input(["name" => "filter.filters[9].filters[1].operator", "value" => "cast_lte", "type" => "hidden"]);
                                     echo form_input(["id" => "dueDateEndValue", "name" => "filter.filters[9].filters[1].value", "value" => "", "autocomplete" => "off", "class" => "form-control d-none end-date-filter sf-value2", "placeholder" => "YYYY-MM-DD"]);?>
                                 </div>
                             </div>
                         </div>
                         <div class="row no-margin">
                             <div class="form-group row col-md-6">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("location");?></label>
                                 <div class="col-md-3">
                                     <?php echo form_input(["name" => "filter.filters[17].filters[0].field", "value" => "location", "type" => "hidden"]);
                                     echo form_dropdown("filter.filters[17].filters[0].operator", $operators["text"], "contains", 'id="locationOpertator" class="form-control sf-operator"');?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_input(["id" => "locationValue", "name" => "filter.filters[17].filters[0].value", "value" => "", "class" => "form-control lookup sf-value", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]); ?>
                                 </div>
                             </div>
                             <div class="form-group row col-md-6">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("effectiveEffort");?></label>
                                 <div class="col-md-3">
                                     <?php
                                     echo form_input(["name" => "filter.filters[16].filters[0].field", "value" => "conveyancing_instruments.effectiveEffort", "type" => "hidden"]);
                                     echo form_dropdown("filter.filters[16].filters[0].operator", $operators["number_only"], "", 'id="efftEffortOpertator" class="form-control sf-operator"');?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_input(["id" => "efftEffortValue", "name" => "filter.filters[16].filters[0].value", "value" => "", "class" => "form-control sf-value", "data-validation-engine" => "validate[funcCall[validateHumanReadableTime]]"]);?>
                                 </div>
                             </div>
                         </div>
                         <div class="row no-margin">
                             <div class="form-group row col-md-6">
                                 <label  class="control-label col-md-3"><?php echo $this->lang->line("contributors");?></label>
                                 <div class="col-md-3">
                                     <?php echo form_input(["name" => "filter.filters[20].filters[0].field", "value" => "opinion_contributors.user_id", "type" => "hidden"]);
                                     echo form_dropdown("filter.filters[20].filters[0].operator", $operators["group_list"], "", 'id="contributorsOpertator" class="form-control sf-operator"');?>
                                 </div>
                                 <div class="col-md-5">
                                     <?php echo form_dropdown("filter.filters[20].filters[0].value", $usersList, !empty($contributed_by_me_auth) ? $contributed_by_me_auth : "", 'id="opinion-contributors" multiple="multiple" class="multi-select form-control sf-value"');?>
                                 </div>
                             </div>
                         </div>
                         <div class="row no-margin">
                             <!-- Custom fields advanced search -->
                             <?php $this->load->view("custom_fields/advance_search_custom_field_template", ["custom_fields" => $custom_fields, "operators" => $operators]);?>
                         </div>
                     </div>
                     <div class="clear clearfix"></div>
                 </div>
             </div>
             <div class="card-footer padding-all-10">
                 <input type="button" value="<?php echo $this->lang->line("reset");?>" class="btn btn-default btn-link no-padding" onclick="resetFormFields();" />
                 <input type="reset" value="" id="resetBtnHidden" class="d-none" />
                 <input type="submit" value="<?php echo $this->lang->line("submit");?>" name="submit" id="submit" class="btn btn-outline-secondary" />
                 <input type="button" value="[<?php echo $this->lang->line("hide"); ?>]" onclick="hideAdvancedSearch()" class="btn btn-link pull-right no-padding" />
             </div>
             <?php echo form_close();?>
         </div>
     <?php echo form_open("", 'name="gridFormContent" id="gridFormContent" method="post" class="no-margin"');
     echo form_input(["value" => "", "name" => "legal_case_id", "type" => "hidden"]); ?>
     <div class="grid-header row gh-top m-0">
             <div class="k-padding nav-full-width-0">
                 <div class="row no-margin padding-15-horizontal">
<!--                 <div class="col-md-6 col-xs-7 gh-top-title no-margin">     -->
<!--                     <h3>--><?php //echo $my_opinions ? $this->lang->line("my_opinions") : ($reported_by_me ? $this->lang->line("reported_by_me") : ($contributed_by_me_opinions ? $this->lang->line("contributed_by_me_opinions") : $this->lang->line("all_opinions")));?><!--</h3>-->
<!--                     <div class="gh-types-buttons-container">-->
<!--                         --><?php
//                             if (!$my_opinions) { ?>
<!--                                 <a id="my-opinion-button" class="btn btn-default" href="--><?php //echo app_url("legal_opinions/my_opinions");?><!--"  title="--><?php //echo $this->lang->line("my_opinions");        ?><!--" >-->
<!--                                     --><?php //echo $this->lang->line("my_opinions"); ?>
<!--                                     </a> --><?php
//                             }
//                             if (!$reported_by_me) { ?>
<!--                             <a id="reported-by-me" class="btn btn-default" href="--><?php //echo app_url("legal_opinions/pro_reported_by_me");?><!--"  title="--><?php //echo $this->lang->line("reported_by_me"); ?><!--">--><?php //echo $this->lang->line("reported_by_me"); ?><!--   </a>-->
<!--                                 --><?php
//                             }
//                             if ($this->is_auth->check_uri_permissions("/legal_opinions/", "/legal_opinions/all_legal_opinions/", "core", true, true) && ($my_opinions || $reported_by_me || $contributed_by_me_opinions)) { ?>
<!--                                 <a class="btn btn-default" href="--><?php //echo app_url("legal_opinions/all_Proprietary_Rights_and_Obligations");  ?><!--" title="--><?php //echo $this->lang->line("all_Proprietary_Rights_and_Obligations"); ?><!--" >-->
<!--                                     --><?php //echo $this->lang->line("all_opinions"); ?>
<!--                                 </a>-->
<!--                                 --><?php
//                             }
//                             if (!$contributed_by_me_opinions) { ?>
<!--                                 <a id="contributed-by-me-opinion-button" class="btn btn-default" href="--><?php //echo app_url("legal_opinions/opinions_contributed_by_me");?><!--" title="--><?php //echo $this->lang->line("contributed_by_me_opinions"); ?><!--">--><?php //echo $this->lang->line("contributed_by_me_opinions");?><!--</a>-->
<!--                             --><?php //   }
//
//                         ?>
<!--                     </div>-->
<!--                 </div>-->
                     <div class="col-md-6 col-xs-5 pull-right gh-top-tools">
                     <ul class="operations pr-5">
                         <li class="btn-group nav opts-li" id="export-opinion-list">
                             <button type="button" class="btn btn-default dropdown-toggle gh-top-tools-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 <i class="fa-solid fa-file-export"></i></span> <?php echo $this->lang->line("export");?><span class="caret"></span>
                             </button>
                             <div class="dropdown-menu dropdown-menu-right" id="export-opinion-demo">
                                 <a class="dropdown-item" data-callexport= \"exportOpinionsToExcel();" href="javascript:;" ><?php echo $this->lang->line("export_to_excel") . $this->lang->line("current_fields");?></a>
                                 <a class="dropdown-item" data-callexport= \"exportOpinionsToExcel(true);" href="javascript:;" ><?php echo $this->lang->line("export_to_excel") . $this->lang->line("all_fields");?></a>
                             </div>
                         </li>
                         <li class="btn-group nav opts-li pr-2">
                             <button type="button" class="btn btn-default dropdown-toggle gh-top-tools-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 <i class="fa-solid fa-gear"></i></span><?php echo $this->lang->line("tools");?><span class="caret"></span>
                             </button>
                             <div class="dropdown-menu dropdown-menu-right">
                                 <span class="dropdown-item" id="archive_tooltip" data-title="<?php echo $this->lang->line("unarchive_opinion_title");?>" title="<?php echo $this->lang->line("unarchive_opinion_title");?>"><a id="unarchivedButtonId"  onclick="unarchivedSelectedOpinions()" class="disabled"  href="javascript:;"><?php echo $this->lang->line("unarchive");?></a></span>
                             </div>
                         </li>
                     </ul>
                 </div>
                 <div class="row m-0 col-md-12 col-xs-12 no-padding grid-header-section padding-15-horizontal">
                     <div class="col-md-5 col-xs-4 k-grid-info-refresh-top">
                         <a href="javascript:;" class="k-pager-refresh-top" title="<?php echo $this->lang->line("refresh");?>"><span class="k-icon k-i-refresh"></span></a>                         <span class="k-pager-info-top k-label-top"></span>
                     </div>
                     <div class="row no-margin no-padding col-md-6 col-xs-7 flex-end-item margin-bottom-10">
                         <div class="row col-md-12 no-padding-left grid-advanced-search pr-5">
                             <div class="col-xs-9 col-md-10">
                                 <?php echo form_input(["onkeyup" => "opinionQuickSearch(event.keyCode, this.value);", "id" => "opinionLookUp", "name" => "opinionLookUp", "placeholder" => $this->lang->line("search"), "class" => "form-control search quick-search-filter"]);?>
                             </div>
                             <div class="col-xs-3 col-md-2" id="column-picker-trigger-container"></div>
                             <div class="col-xs-9 col-md-10">
                                 <a href="javascript:;" id="advanced-filter-button" onclick="advancedSearchFilters();" class="btn btn-link" title="<?php echo $this->lang->line("switch_to_advanced_search");?>">
                                     <?php echo $this->lang->line("advanced");?>
                                 </a>
                                 <a id="opinion-workflow-filter" class="btn btn-link dropdown-togglebtn" data-toggle="dropdown" href="##" title="<?php echo $this->lang->line("opinion_status_filter");?>"><?php echo $this->lang->line("opinion_status_filter");?>
                                 </a>
                                 <div class="dropdown-menu" role="menu" aria-labelledby="dLabel" id="opinionStatusesList">
                                     <?php if ($conveyancing_instrumentStatusesSavedFilters) { ?>
                                         <a href="javascript:;" onclick="opinionStatusFilterClick(this, true);" class="dropdown-item checkbox" id="all-opinion-status-filter">
                                             <input type="checkbox" class="d-none"/><i class="checkbox-no-icon"></i> <?php    echo $this->lang->line("all");    ?>
                                         </a>
                                         <?php     foreach ($instrumentStatusValues as $opinion_status_id => $opinion_status_name) {?>
                                             <a href="javascript:;" onclick="opinionStatusFilterClick(this);" class="dropdown-item checkbox">
                                             <input type="checkbox" name="opinion_status_id[]" <?php echo in_array($opinion_status_id, $conveyancing_instrumentStatusesSavedFilters) ? "checked=''" : "value='". $opinion_status_id. "'"?> class="d-none opinion-status-group" onclick="return false;" /><i <?php echo in_array($opinion_status_id, $opinionStatusesSavedFilters) ? "class='icon checkbox-yes-icon'" : "class='icon checkbox-no-icon'"?>></i><?php echo $opinion_status_name; ?>
                                             </a><?php    }
                                     } else {?>
                                         <a href="javascript:;" onclick="opinionStatusFilterClick(this, true);" class="dropdown-item checkbox"><input type="checkbox" class="d-none" checked="" /><i class="checkbox-yes-icon"></i>
                                         <?php echo $this->lang->line("all");    ?></a><?php    foreach ($instrumentStatusValues as $opinion_status_id => $opinion_status_name) { ?>
                                             <a href="javascript:;" onclick="opinionStatusFilterClick(this);" class="dropdown-item checkbox"><input type="checkbox" name="opinion_status_id[]" checked="" value="<?php        echo $opinion_status_id;       ?>"class="d-none opinion-status-group" onclick="return false;" /><i class="icon checkbox-yes-icon"></i> <?php        echo $opinion_status_name;       ?></a>
                                         <?php }
                                     }
                                     ?>
                                     <a href="javascript:;" class="dropdown-item btn btn-xs btn-info apply-opinion-status-filters" onclick="opinionStatusQuickSearch()"> <?php echo $this->lang->line("apply");?> </a>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                     <div class="grid-header-hidden-background"></div>
                 </div>
             </div>
     </div>
     <div class="row no-margin" id="opinionsGridDemo">
         <div class="col-md-12 no-padding <?php echo $this->session->userdata("AUTH_language") == "arabic" ? "k-rtl" : "";?>">
             <div id="opinionsGrid" class="grid-container"></div>
         </div>
     </div>
     <?php echo form_close();?>
 </div>
</div>
<?php $this->load->view("excel/form");
$this->load->view("excel/exporting_module");
?>
<script>
    opinions = '<?php //echo $my_opinions;?>';
    var reportedByMe = '<?php //echo $reported_by_me;?>';
    gridSavedPageSize = '<?php echo isset($grid_saved_details["pageSize"]) ? $grid_saved_details["pageSize"] : false;?>';
    gridSavedColumnsSorting = '<?php echo isset($grid_saved_details["sort"]) ? $grid_saved_details["sort"] : false;?>';
    hasAccessToExport = "<?php echo $this->is_auth->check_uri_permissions("/export/", "/export/conveyancing/", "core", true, true);?>";
    // Safely parse with fallback to empty array
    var customFieldsNames = [];
    try {
        customFieldsNames = JSON.parse('<?php echo isset($grid_columns["custom_fields"]) && !empty($grid_columns["custom_fields"]) ? addslashes(json_encode($grid_columns["custom_fields"])) : "[]" ?>');
    } catch (e) {
        console.error("Error parsing custom fields:", e);
        customFieldsNames = [];
    }
    var businessWeekDays = '<?php echo $businessWeekDays;?>';
    var businessDayHours = '<?php echo $businessDayHours;?>';
    //editOpinionDemo();
    var contributedByMe = '<?php //echo $contributed_by_me_opinions;?>';
</script>
                     