<div class="row" id="grid-scrollable">
    <div class="col-md-12 k-padding">
        <div id="filtersFormWrapper" class="card" style="display: none;">
            <div class="card-header">
                <h4><?php echo $this->lang->line("contract_search_filters");?></h4>
            </div>
            <?php echo form_open("", 'name="Filters" id="contracts-filters" method="post" class="form-horizontal"');?>
            <div class="card-body"><?php echo form_input(["name" => "take", "value" => "20", "type" => "hidden"]);
            echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
            echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
            echo form_input(["name" => "pageSize", "value" => "20", "type" => "hidden"]);
            echo form_input(["name" => "filter.logic", "value" => "and", "id" => "defaultFilterLogic", "type" => "hidden"]);
            echo form_input(["name" => "quickSearch.logic", "value" => "and", "type" => "hidden"]);
            echo form_input(["value" => implode(",", $selected_columns), "id" => "display-columns", "type" => "hidden"]);
            echo form_input(["value" => "contract", "id" => "model", "type" => "hidden"]);
            ?>
                <div class="col-md-12 no-margin">
                    <div class="form-group row d-none">
                        <div class="controls"><?php
                            echo form_input(["name" => "quickSearch.filters[0].filters[0].field", "value" => "contract.name", "id" => "quickSearchFilter", "type" => "hidden"]);
                            echo form_input(["name" => "quickSearch.filters[0].filters[0].operator", "value" => "contains", "id" => "quickSearchFilterOperator", "type" => "hidden"]);
                            echo form_input(["name" => "quickSearch.filters[0].filters[0].value", "id" => "quickSearchFilterValue", "type" => "hidden"]);?>
                        </div>
                        <div class="controls"><?php
                            echo form_input(["name" => "quickSearch.filters[2].filters[0].field", "id" => "quickSearchFilterArchivedField", "value" => "contract.archived", "type" => "hidden"]);
                            echo form_input(["name" => "quickSearch.filters[2].filters[0].operator", "id" => "quickSearchFilterArchivedOperator", "value" => "eq", "type" => "hidden"]);
                            echo form_input(["name" => "quickSearch.filters[2].filters[0].value", "id" => "quickSearchFilterArchivedValue", "value" => $defaultArchivedValue, "type" => "hidden"]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="contract.id">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("contract_id");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[0].filters[0].field", "value" => "contract.id", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[0].filters[0].operator", $operators["number"], "", 'id="idOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_input(["name" => "filter.filters[0].filters[0].value", "class" => "sf-value form-control", "id" => "idValue", "autocomplete" => "off"]); ?>                            </div>
                        </div>
                        <div class="form-group row col-md-6" sf-field="contract.name">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("name");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[1].filters[0].field", "value" => "contract.name", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[1].filters[0].operator", $operators["text"], "contains", 'id="subjectOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_input(["name" => "filter.filters[1].filters[0].value", "class" => "sf-value form-control", "id" => "subjectValue", "autocomplete" => "off"]);?></div>
                        </div>
        
                            </div>
                            <div class="row">
                                <div class="form-group row col-md-6" sf-field="contract.type_id">
                                    <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("type");?></label>
                                    <div class="col-md-3"><?php
                                        echo form_input(["name" => "filter.filters[2].filters[0].field", "value" => "contract.type_id", "type" => "hidden"]);
                                        echo form_dropdown("filter.filters[2].filters[0].operator", $operators["group_list"], "contains", 'id="typeOpertator" class="sf-operator form-control"');
                                        ?>
                                    </div>
                                    <div class="col-md-5 p-0">   <?php echo form_dropdown("filter.filters[2].filters[0].value", $types, "", 'id="typeValue" multiple="multiple" class="multi-select sf-value form-control"');?>  </div>
                                </div>
                                <div class="form-group row col-md-6" sf-field="contract.sub_type_id"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("sub_type");?></label>
                                    <div class="col-md-3"><?php
                                        echo form_input(["name" => "filter.filters[3].filters[0].field", "value" => "contract.sub_type_id", "type" => "hidden"]);
                                        echo form_dropdown("filter.filters[3].filters[0].operator", $operators["list"], "eq", 'id="SubTypeOpertator" class="sf-operator form-control"');
                                        ?>
                                    </div>
                                    <div class="col-md-5 p-0">
                                        <?php echo form_dropdown("filter.filters[3].filters[0].value", $sub_type, "", 'id="SubTypeValue" class="sf-value form-control"');?>
                                    </div>
                                </div>
                            </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="contract.description">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("description");?></label>
                            <div class="col-md-3">  <?php
                                echo form_input(["name" => "filter.filters[4].filters[0].field", "value" => "description", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[4].filters[0].operator", $operators["text"], "contains", 'id="descriptionOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php
                                echo form_input(["name" => "filter.filters[4].filters[0].value", "class" => "sf-value form-control", "id" => "descriptionValue", "autocomplete" => "off"]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="contract.assigned_team_id">  <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("provider_group");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[17].filters[0].field", "value" => "contract.assigned_team_id", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[17].filters[0].operator", $operators["group_list"], "", 'id="assigned_team_idOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php
                                echo form_dropdown("filter.filters[17].filters[0].value", $assigned_teams, "", 'id="assigned_team_idValue" multiple="multiple" class="multi-select sf-value form-control"');?>
                            </div>
                        </div>
                        <div class="form-group row col-md-6" sf-field="contract.assignee"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("assignee");?></label>
                            <div class="col-md-3">
                                <?php echo form_input(["name" => "filter.filters[5].filters[0].field", "value" => "contract.assignee", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[5].filters[0].function", "value" => "assignee_field_value", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[5].filters[0].operator", $operators["text"], "contains", 'id="assigneeOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_input(["name" => "filter.filters[5].filters[0].value", "id" => "assigneeValue", "class" => "sf-value form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]);?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="contract.reference_number">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("reference_number");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[6].filters[0].field", "value" => "contract.reference_number", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[6].filters[0].operator", $operators["text"], "contains", 'id="reference_numberOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php
                                echo form_input(["name" => "filter.filters[6].filters[0].value", "id" => "reference_numberValue", "class" => "sf-value form-control", "dir" => "auto"]);
                                ?>
                            </div>
                        </div>
                        <div class="form-group row col-md-6" sf-field="contract.value"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("contract_value");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[7].filters[0].field", "value" => "contract.value", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[7].filters[0].operator", $operators["number_only"], "contains", 'id="valueOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0">
                                <?php echo form_input(["name" => "filter.filters[7].filters[0].value", "id" => "valueValue", "class" => "sf-value form-control", "dir" => "auto"]);?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="contract.status_id"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("workflow_status");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[8].filters[0].field", "value" => "contract.status_id", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[8].filters[0].operator", $operators["group_list"], "", 'id="StatusesOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_dropdown("filter.filters[8].filters[0].value", $statuses, "", 'id="StatusesValue" multiple="multiple" class="sf-value multi-select form-control"');?>                            </div>
                        </div>
                        <div class="form-group row col-md-6" id="dateContainer" sf-field="contract.contract_date">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("contract_date");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[10].filters[0].field", "value" => "contract.contract_date", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[10].filters[0].operator", "value" => "cast_eq", "class" => "sf-operator start-date-operator", "type" => "hidden"]);
                                echo form_dropdown("dateOperator1", $operators["date"], "", "id=\"dateOpertator\" class=\"sf-operator-list form-control\" onchange=\"onchangeOperatorsFiltersDate(this, 'dateContainer');\"");?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_input(["name" => "filter.filters[10].filters[0].value", "id" => "dateValue", "class" => "sf-value form-control", "placeholder" => "YYYY-MM-DD"]);?></div>
                            <div class=" col-md-5 offset-md-6 margin-top"><?php
                                echo form_input(["name" => "filter.filters[10].logic", "value" => "and", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[10].filters[1].field", "value" => "contract.contract_date", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[10].filters[1].operator", "value" => "cast_lte", "class" => "sf-operator2", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[10].filters[1].value", "id" => "dateEndValue", "class" => "sf-value2 form-control d-none end-date-filter", "placeholder" => "YYYY-MM-DD", "autocomplete" => "off"]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" id="start_dateContainer" sf-field="contract.start_date">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("start_date");?></label>
                            <div class="col-md-3">
                                <?php
                                echo form_input(["name" => "filter.filters[11].filters[0].field", "value" => "contract.start_date", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[11].filters[0].operator", "value" => "cast_eq", "class" => "sf-operator start-date-operator", "type" => "hidden"]);
                                echo form_dropdown("dateOperator1", $operators["date"], "", "id=\"start_dateOperator\" class=\"sf-operator-list form-control\" onchange=\"onchangeOperatorsFiltersDate(this, 'start_dateContainer');\"");
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_input(["name" => "filter.filters[11].filters[0].value", "id" => "start_dateValue", "class" => "sf-value form-control", "placeholder" => "YYYY-MM-DD"]);?>                            </div>
                            <div class=" col-md-5 offset-md-6 margin-top"><?php
                                echo form_input(["name" => "filter.filters[11].logic", "value" => "and", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[11].filters[1].field", "value" => "contract.start_date", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[11].filters[1].operator", "value" => "cast_lte", "class" => "sf-operator2", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[11].filters[1].value", "id" => "start_dateEndValue", "class" => "sf-value2 form-control d-none end-date-filter", "placeholder" => "YYYY-MM-DD", "autocomplete" => "off"]);?>
                            </div>
                        </div>
                        <div class="form-group row col-md-6" id="end_dateContainer" sf-field="contract.end_date"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("end_date");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[12].filters[0].field", "value" => "contract.end_date", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[12].filters[0].operator", "value" => "cast_eq", "class" => "sf-operator start-date-operator", "type" => "hidden"]);
                                echo form_dropdown("dateOperator1", $operators["date"], "", "id=\"end_dateOperator\" class=\"sf-operator-list form-control\" onchange=\"onchangeOperatorsFiltersDate(this, 'end_dateContainer');\"");?>
                            </div>
                            <div class="col-md-5 p-0"><?php
                                echo form_input(["name" => "filter.filters[12].filters[0].value", "id" => "end_dateValue", "class" => "sf-value form-control", "placeholder" => "YYYY-MM-DD"]);
                                ?>
                            </div>
                            <div class=" col-md-5 offset-md-6 margin-top"><?php
                                echo form_input(["name" => "filter.filters[12].logic", "value" => "and", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[12].filters[1].field", "value" => "contract.end_date", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[12].filters[1].operator", "value" => "cast_lte", "class" => "sf-operator2", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[12].filters[1].value", "id" => "end_dateEndValue", "class" => "sf-value2 form-control d-none end-date-filter", "placeholder" => "YYYY-MM-DD", "autocomplete" => "off"]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="amended.name">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("amendment_of");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[13].filters[0].field", "value" => "amended.name", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[13].filters[0].function", "value" => "amended_field_value", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[13].filters[0].operator", $operators["text"], "contains", 'id="amendedOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_input(["name" => "filter.filters[13].filters[0].value", "id" => "amendmentOfValue", "class" => "sf-value form-control", "autocomplete" => "off"]);?>                            </div>
                        </div>
                        <div class="form-group row col-md-6" sf-field="contract.status"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("status");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[14].filters[0].field", "value" => "contract.status", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[14].filters[0].operator", $operators["list"], "", 'id="ContractStatusesOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"> 
                                <?php echo  form_dropdown("filter.filters[14].filters[0].value", $statusValues, $defaultStatusValue, 'id="ContractStatusesValue" class="sf-value form-control"');
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="contract.country_id">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("country");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[15].filters[0].field", "value" => "contract.country_id", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[15].filters[0].operator", $operators["group_list"], "contains", 'id="countryOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"> <?php echo form_dropdown("filter.filters[15].filters[0].value", $countries, "", 'id="countryValue" multiple="multiple" class="multi-select sf-value form-control"');?> </div>
                        </div>
                        <div class="form-group row col-md-6" sf-field="contract.app_law_id"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("applicable_law");?></label>
                            <div class="col-md-3">   <?php
                                echo form_input(["name" => "filter.filters[16].filters[0].field", "value" => "contract.app_law_id", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[16].filters[0].operator", $operators["group_list"], "contains", 'id="app_law_idOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_dropdown("filter.filters[16].filters[0].value", $applicable_laws, "", 'id="app_law_idValue" multiple="multiple" class="multi-select sf-value form-control"');?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="contract.archived"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("archived");?></label>
                            <div class="col-md-3">
                                <?php
                                echo form_input(["name" => "filter.filters[20].filters[0].field", "value" => "contract.archived", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[20].filters[0].operator", $operators["list"], "eq", 'id="archivedOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_dropdown("filter.filters[20].filters[0].value", $archivedValues, $defaultArchivedValue, 'id="archivedValue" class="sf-value form-control"');?></div>
                        </div>
                        <div class="form-group row col-md-6" sf-field="contract.requester">
                            <label  class="col-form-label text-right col-md-3"><?php echo $this->lang->line("requested_by");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[21].filters[0].field", "value" => "contract.requester", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[21].filters[0].function", "value" => "requester_field_value", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[21].filters[0].operator", $operators["text"], "contains", 'id="requesterOpertator" class="sf-operator form-control"');?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_input(["name" => "filter.filters[21].filters[0].value", "id" => "requesterValue", "class" => "sf-value form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]);?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="parties"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("party");?> 1</label>
                            <div class="col-md-3">  <?php
                                echo form_dropdown("test", ["" => "", "companies" => $this->lang->line("company"), "contacts" => $this->lang->line("contact")], false, 'id="partyTypeOpertator" class="form-control"');
                                echo form_input(["name" => "filter.filters[22].filters[0].field", "value" => "parties", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[22].filters[0].function", "value" => "parties_field_value", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[22].filters[0].operator", "value" => "contains", "class" => "sf-operator form-control", "type" => "hidden"]);
                                ?>
                            </div>
                            <div class="col-md-5 p-0">
                                <?php echo form_input(["name" => "filter.filters[22].filters[0].value", "id" => "partyNameValue", "class" => "sf-value form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]); ?>
                            </div>
                        </div>
                        <div class="form-group row col-md-6" sf-field="parties">
                            <label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("party");?> 2 </label>
                            <div class="col-md-3">  <?php
                                echo form_dropdown("test", ["" => "", "companies" => $this->lang->line("company"), "contacts" => $this->lang->line("contact")], false, 'id="party2TypeOpertator" class="form-control"');
                                echo form_input(["name" => "filter.filters[23].filters[0].field", "value" => "parties", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[23].filters[0].function", "value" => "parties_field_value", "type" => "hidden"]);
                                echo form_input(["name" => "filter.filters[23].filters[0].operator", "value" => "contains", "class" => "sf-operator form-control", "type" => "hidden"]);
                                ?>
                            </div>
                            <div class="col-md-5 p-0"> <?php echo form_input(["name" => "filter.filters[23].filters[0].value", "id" => "party2NameValue", "class" => "sf-value form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]); ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="contract.category"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("category");?></label>
                            <div class="col-md-3">
                                <?php
                                echo form_input(["name" => "filter.filters[24].filters[0].field", "value" => "contract.category", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[24].filters[0].operator", $operators["list"], "eq", 'id="categoryOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_dropdown("filter.filters[24].filters[0].value", $categoryValues, $defaultCategoryValue, 'id="categoryValue" class="sf-value form-control"');?></div>
                        </div>
                        <div class="form-group row col-md-6" sf-field="contract.stage"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("contract_stage");?></label>
                            <div class="col-md-3">
                                <?php
                                echo form_input(["name" => "filter.filters[25].filters[0].field", "value" => "contract.stage", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[25].filters[0].operator", $operators["list"], "eq", 'id="stageOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0"><?php echo form_dropdown("filter.filters[24].filters[0].value", $stageValues, $defaultStageValue, 'id="stageValue" class="sf-value form-control"');?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6" sf-field="cpv.amount_paid_so_far"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("amount_paid_so_far");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[26].filters[0].field", "value" => "cpv.amount_paid_so_far", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[26].filters[0].operator", $operators["number_only"], "contains", 'id="amountPaidSoFarOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0">
                                <?php echo form_input(["name" => "filter.filters[26].filters[0].value", "id" => "amount_paid_so_far", "class" => "sf-value form-control", "dir" =>"auto"]);?>
                            </div>
                        </div>

                        <div class="form-group row col-md-6" sf-field="cpv.balance_due"><label class="col-form-label text-right col-md-3"><?php echo $this->lang->line("balance_due");?></label>
                            <div class="col-md-3"><?php
                                echo form_input(["name" => "filter.filters[27].filters[0].field", "value" => "cpv.balance_due", "type" => "hidden"]);
                                echo form_dropdown("filter.filters[27].filters[0].operator", $operators["number_only"], "contains", 'id="BalanceDueOpertator" class="sf-operator form-control"');
                                ?>
                            </div>
                            <div class="col-md-5 p-0">
                                <?php echo form_input(["name" => "filter.filters[27].filters[0].value", "id" => "balance_due", "class" => "sf-value form-control", "dir" => "auto"]);?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php $this->load->view("custom_fields/advance_search_custom_field_template", ["custom_fields" => $dataCustomFields, "operators" => $operators]);?>
                    </div>
                    <div class="clear clearfix"></div>
                </div>
            </div>
            <div class="card-footer" id="submitionActions"><input type="button" value="<?php echo $this->lang->line("reset");?>"  class="btn btn-link p-0" onclick="resetFormFields();"/>
                <input type="reset" value="" id="resetBtnHidden" class="d-none"/>  <input type="submit" value="<?php echo $this->lang->line("submit");?>" name="submit" id="submit" class="btn btn-outline-secondary"/>
                <input type="button" value="<?php echo $this->lang->line("submit_and_save_filter");?>"   id="submitAndSaveFilter" <?php echo !empty($gridSavedFilters) ? "class='btn btn-light'" : "class='btn btn-light d-none'";?> />
                <input type="button" value="[<?php echo $this->lang->line("hide");?>]" onclick="hideAdvancedSearch()"   class="btn btn-link float-right p-0"/>
            </div>
            <?php echo form_close();?>
        </div>
        <?php echo form_open("", 'name="gridFormContent" id="gridFormContent" method="post" class="no-margin"');?>
        <div class="grid-header row gh-top">
            <div class="k-padding nav-full-width-0">
                <div class="row no-margin padding-15-horizontal">
                    <div class="col-md-6 col-xs-7 gh-top-title" id="gridFiltersContainer"></div>
                    <div class="col-md-6 col-xs-5 float-right gh-top-tools">
                        <ul class="operations pr-4">
                            <li class="btn-group nav opts-li margin-right">
                                <div class="dropdown more float-right margin-right10">
                                    <button id="export-menu-link" type="button" class="btn btn-default dropdown-toggle gh-top-tools-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa-solid fa-file-export"></i><?php echo $this->lang->line("export");?>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="export-menu-link">
                                        <a data-callexport= "exportContractsToExcel();" href="javascript:;" class="dropdown-item"> <?php echo $this->lang->line("export_to_excel") . $this->lang->line("current_fields");?></a>
                                        <a data-callexport= "exportContractsToExcel(true);" href="javascript:;" class="dropdown-item"> <?php echo $this->lang->line("export_to_excel") . $this->lang->line("all_fields");?></a>
                                    </div>
                                </div>
                            </li>
                            <li class="btn-group nav opts-li margin-right">
                                <div class="dropdown more float-right margin-right10">
                                    <button id="legal-case-tools-menu-link" type="button" class="btn btn-default dropdown-toggle gh-top-tools-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa-solid fa-gear"></i> <?php echo $this->lang->line("tools");?>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="legal-case-tools-menu-link">
                                        <a class="dropdown-item" onclick="manageGridFilters('contract', 'contractsGrid', 'contracts-filters', 'advancedSearchFilters');" href="javascript:;" ><?php echo $this->lang->line("manage_filters");?></a>
                                        <a id="archive-button-id"  onclick="toolsActionsContract('fromSelection','archive');" class="dropdown-item disabled"  href="javascript:;"><?php echo $this->lang->line("archive");?></a>
                                        <a id="unarchive-button-id"  onclick="toolsActionsContract('fromSelection','unarchive');" class="dropdown-item disabled"  href="javascript:;"><?php echo $this->lang->line("unarchive");?></a>
                                        <a id="delete-all-button-id"  onclick="toolsActionsContract('fromSelection','delete');" class="dropdown-item disabled"  href="javascript:;"><?php echo $this->lang->line("delete");?></a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row no-margin col-md-12 col-xs-12 row no-margin grid-header-section">
                    <div class="col-md-6 col-xs-6 k-grid-info-refresh-top">
                        <a href="javascript:;" class="k-pager-refresh-top" title="<?php echo $this->lang->line("refresh");?>"><span class="k-icon k-i-refresh"></span></a>
                        <span class="k-pager-info-top k-label-top"></span>
                    </div>
                    <div class="col-md-6 col-xs-6 float-right p-0 margin-bottom-10">
                        <ul class="operations pr-5">
                            <li class="opts-li"><?php echo form_input(["onkeyup" => "contractQuickSearch(event.keyCode, this.value);", "id" => "contract-lookup", "name" => "", "placeholder" => $this->lang->line("search"), "class" => "form-control search quick-search-filter"]);?>                            </li>
                            <li class="opts-li margin-right"><a href="javascript:;" onclick="advancedSearchFilters();" class="btn btn-link"  title="<?php echo $this->lang->line("switch_to_advanced_search");?>"><?php echo $this->lang->line("advanced");?></a></li>
                            <li class="opts-li"><div id="column-picker-trigger-container"></div></li>
                        </ul>
                    </div>
                </div>
                <div class="grid-header-hidden-background"></div>
            </div>
        </div>
        <div class="row no-margin">
            <div class="col-md-12 p-0 <?php echo $this->session->userdata("AUTH_language") == "arabic" ? "k-rtl" : "";?>">
                <div id="contractsGrid" class="grid-container"></div>
            </div>
        </div>
        <?php echo form_close();?>
    </div>
    <div id="gridFiltersTempContainer" class="d-none"><h3><?php echo strtolower($defaultCategoryValue)=="mou"?$this->lang->line("mou_agreements"):$this->lang->line("contracts");?></h3>
        <select id="gridFiltersList" class="form-control gird-filters-list">
            <option value=""><?php echo $this->lang->line("all");?></option>
            <?php
            foreach ($gridSavedFilters as $gFilter) {
            echo "<option value='" . $gFilter["id"] . "' isGlobalFilter='" . $gFilter["isGlobalFilter"] . "' " . ($gFilter["id"] == $gridDefaultFilter["id"] ? "selected=\"selected\"" : "") . ">" . $gFilter["name"] . "</option>";
            }
            ?>
        </select>
        <button type="button" class="btn btn-default save-filter-as" aria-label="Add Filter"   onclick="addGridFilter('contract', 'contractsGrid', 'contracts-filters');"   title="<?php echo $this->lang->line("save_search_results_in_a_filter");?>">
            <?php echo $this->lang->line("save_as");?>
        </button>
    </div>
</div>
<?php
$this->load->view("excel/form");
$gridSavedFiltersParams = false;
$gridSavedPageSize = $gridSavedColumnsSorting = false;
if ($gridSavedFiltersData) {
    $gridSavedFiltersParams = $gridSavedFiltersData["gridFilters"];
}
if ($grid_saved_details) {
    $gridSavedPageSize = $grid_saved_details["pageSize"];
    $gridSavedColumnsSorting = $grid_saved_details["sort"];
}
$this->load->view("excel/exporting_module");
?>
<script type="text/javascript">
    contract_sla_feature = '<?php echo $contract_sla_feature;?>';
    loggedUserIsAdminForGrids = '<?php echo $loggedUserIsAdminForGrids; ?>';
    gridSavedFiltersParams = '<?php  echo $gridSavedFiltersParams;?>';
    gridSavedPageSize = '<?php echo $gridSavedPageSize;?>';
    gridSavedColumnsSorting = '<?php   echo $gridSavedColumnsSorting;?>';
    hasAccessToExport ='<?php echo $this->is_auth->check_uri_permissions("/export/", "/export/contracts/", "contract", true, true)?>';
    customFieldsNames = JSON.parse('<?php  echo isset($grid_columns["custom_fields"]) ? json_encode($grid_columns["custom_fields"]) : "";?>');
</script>

