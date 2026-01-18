<div class="row col-md-12 no-padding no-margin row no-margin">

                                            <div class="form-group col-md-6 no-padding row no-margin">
                                                <label class="control-label col-md-4 no-padding flex-center-inline">
                                                    <?php
                                                    echo $this->lang->line("client_position");
                                                    $case_scenario_general = "scenario_case_client_positions";
                                                    ?>
                                                    <a href="javascript:;" onclick="quickAdministrationDialog('case_client_positions', jQuery('#newCaseFormDialog'), true, '<?php echo $case_scenario_type;?>')" class="icon-alignment btn btn-link px-0"><i class="fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                </label>
                                                <div class="row m-0 col-md-8 no-padding">
                                                    <?php    echo form_dropdown("legal_case_client_position_id", $clientPositions, $legalCase["legal_case_client_position_id"], 'id="legal_case_client_position_id" class="form-control" data-field="administration-case_client_positions"');
                                                    ?>
                                                    <div data-field="legal_case_client_position_id" class="inline-error d-none"></div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6 no-padding row no-margin">
                                                <label class="control-label col-md-4 no-padding">
                                                    <?php     echo $this->lang->line("client_name");
                                                    $companyLinkHref = site_url("companies/tab_company/" . $clientData["member_id"]??=0);
                                                    $clientLinkHref = $clientData["type"]== "Company" ? $companyLinkHref : site_url("contacts/edit/" . $clientData["member_id"]??=0);
                                                    ?>
                                                    <a href="<?php    echo $clientLinkHref;?>" id="clientLinkId" class="icon-alignment <?php    echo $clientData["member_id"] && $clientCompanyCategory != "Group" ? "" : "d-none";?>"><i class="fa fa-external-link"></i></a>
                                                </label>
                                                <div class="row m-0 col-md-8 no-padding">
                                                    <div class="col-md-3 no-padding-right">
                                                        <select name="clientType" id="client-type" class="form-control select-picker company-contact-select" tabindex="-1" data-iconBase="fa" data-tickIcon="fa-check">
                                                            <option data-content="<i class='fa fa-building purple_color' title='<?php echo $this->lang->line("company");?>'></i>" value="company" <?php    echo $clientData["type"] == "Company" ? "selected='selected'" : "";?>></option>
                                                            <option data-content="<i class='fa fa-user purple_color' title='<?php    echo $this->lang->line("contact");?>'></i>" value="contact" <?php    echo $clientData["type"] == "Person" ? "selected='selected'" : "";?>></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <?php
                                                        echo form_input(["name" => "contact_company_id", "id" => "contact-company-id", "value" => $clientData["member_id"], "type" => "hidden"]);
                                                        echo form_input(["name" => "", "id" => "client-lookup", "value" => $clientData["name"]??=null, "class" => "form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $clientData["foreignName"]??=null, "onblur" => "checkLookupValidity(jQuery(this), jQuery('#contact_company_id', '#legalCaseAddForm')); if (this.value === '') { jQuery('#clientLinkId').addClass('d-none');jQuery(this).attr('title', ''); }"]);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h5 class="d-flex justify-content-center">Vs.</h5>
                                        <div class="form-group col-md-12 no-padding" id="opponents-container">
                                            <?php    echo form_input(["name" => "opponentsCount", "id" => "opponents-count", "value" => count($relatedOpponentData), "type" => "hidden"]);    $count = 1;    foreach ($relatedOpponentData as $opponentData) {       ?>
                                                <div class="row m-0 col-md-12 no-padding opponent-div" id="opponent-<?php echo $count;?>">

                                                    <div class="row m-0 form-group col-md-6 no-padding">
                                                        <label class="control-label col-md-4 no-padding flex-center-inline">
                                                            <?php        echo $this->lang->line("opponent_position");       ?>
                                                            <a href="javascript:void(0)" onclick="quickAdministrationDialog('case_opponent_positions', jQuery('#newCaseFormDialog'), true, false, false, jQuery('[data-field-id=opponent-position-<?php echo $count;?>]'));" class="icon-alignment btn btn-link px-0 opponent-position-quick-add"><i class="icon fa fa-square-plus p-1 font-18 purple_color"> </i></a>
                                                        </label>
                                                        <div class="col-md-8 no-padding">
                                                            <?php
                                                            echo form_dropdown("opponent_position[]", $opponent_positions, $opponentData["opponent_position"], 'id="opponent-position" class="form-control select-picker" data-live-search="true" data-field="administration-case_opponent_positions" data-field-id="opponent-position-" . $count . "');
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="row m-0 form-group col-md-6 no-padding">
                                                        <label class="control-label col-md-4 no-padding">
                                                             <span class="opponent-label">
                                                                 <?php        echo $this->lang->line("opponent") . " (" . $count;?>)
                                                             </span>
                                                            <?php
                                                            $companyLinkHref = site_url("companies/tab_company/" . $opponentData["opponent_member_id"]);
                                                            $opponentLinkHref = $opponentData["opponent_member_type"] == "company" ? $companyLinkHref : site_url("contacts/edit/" . $opponentData["opponent_member_id"]);
                                                            ?>
                                                            <a href="<?php echo $opponentLinkHref;?>" class="icon-alignment opponentLinkId <?php  echo $opponentData["opponent_member_id"] && $opponentData["opponentCompanyCategory"] != "Group" ? "" : "d-none";?>"><i class="fa fa-external-link"></i></a>
                                                            <a href="javascript:void(0);" class="icon-alignment btn btn-link delete-opponent delete-icon no-padding-left no-padding-right no-padding-top" onclick="opponentDelete('<?php echo $count;?>', '#edit-legal-case-container', event);"><i class="fa fa-trash light_red-color"></i></a>
                                                        </label>
                                                        <div class="row m-0 col-md-8 no-padding">
                                                            <div class="col-md-3 no-padding-right">
                                                                <select name="opponent_member_type[]" id="opponent-member-type" class="form-control company-contact-select" tabindex="-1" data-iconBase="fa" data-tickIcon="fa-check">
                                                                    <option data-content="<i class='fa fa-building purple_color' title='<?php echo $this->lang->line("company");?>'></i>" value="company" <?php echo $opponentData["opponent_member_type"] == "company" ? "selected='selected'" : "";?>></option>
                                                                    <option data-content="<i class='fa fa-user purple_color' title='<?php echo $this->lang->line("contact");?> '></i>" value="contact" <?php echo $opponentData["opponent_member_type"] == "contact" ? "selected='selected'" : "";?>></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <?php echo form_input(["name" => "opponent_member_id[]", "id" => "opponent-member-id", "value" => $opponentData["opponent_member_id"], "type" => "hidden"]);
                                                                echo form_input(["name" => "", "id" => "opponent-lookup", "value" => $opponentData["opponentName"], "class" => "form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $opponentData["opponentForeignName"], "onblur" => "if (this.value === '') { jQuery('.opponentLinkId','#opponent-" . $count . "').addClass('d-none');jQuery(this).attr('title', ''); }"]);
                                                                 ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div data-field="opponent_member_id_<?php echo $count;?>" class="inline-error d-none"></div>
                                                <?php
                                                $count++;
                                            }   ?>
                                            <div class="col-md-12 no-padding">
                                                <a href="javascript:;" onclick="opponentAddContainer('#edit-legal-case-container', event, '<?php    echo $max_opponents;?>');" class=""><i class="fa fa-plus no-padding"> </i> <?php    echo $this->lang->line("add_another_opponent");    ?> </a>
                                            </div>
                                        </div>