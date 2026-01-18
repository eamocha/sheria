<?php

$cp_class = $legalCase["channel"] == "CP" || $legalCase["visibleToCP"] == "1" ? " id=\"legal-case-top-header-profile\" class=\"tooltip-title light_green cursor-pointer-click\" title=\"" . $this->lang->line("visibleFromCP") . "\"" : " id=\"legal-case-top-header-profile\" class=\"tooltip-title label-normal-style cursor-pointer-click\" title=\"" . $this->lang->line("invisibleFromCP") . "\"";
$cp_class_icon = $legalCase["channel"] == "CP" || $legalCase["visibleToCP"] == "1" ? "client-portal-blue" : "client-portal-grey";
?>
<nav class="navbar navbar-default no-margin top-header-page" role="navigation">
      <div class="col-md-12 no-padding-left flex-item-box">
      <div class="d-flex col-md-5 main-title" id="legal-case-top-header-container">
              <div class="profile-image-matter pull-left ma">
                  <?php $cat_title= strtolower($legalCase["category"]);
                  $edit_page_title=$this->lang->line("corporate_case_matter_edit_page_title");
                  if($cat_title == "litigation" ) {
                      echo  $this->lang->line("l");
                      $edit_page_title=$this->lang->line("litigation_case_matter_edit_page_title");
                  } else if($cat_title=="criminal") {
                      echo  $this->lang->line("c");
                      $edit_page_title=$this->lang->line("criminal_case_matter_edit_page_title");
                  } else  echo  $this->lang->line("m");;?>
              </div>
          <div class="pt-5 matter">
              <h4 class="sub-title-matter">
                  <a class="matter-code" href="<?php  echo base_url() . "cases/edit/" . $this->legal_case->get_field("id"); ?>" data-id="<?php echo $this->legal_case->get_field("id");?>"><?php echo $this->legal_case->get("modelCode") . $this->legal_case->get_field("id");?></a>
                  <bdi>
                      <span id="matter-title" class="trim-matter-title tooltip-title matter-subject" title="<?php echo "&lt;span dir='auto' &gt " . htmlspecialchars($this->legal_case->get_field("subject")) . "&lt;/span&gt";?>" dir="auto">
                          <?php echo htmlspecialchars($this->legal_case->get_field("subject"));
                          echo $this->session->userdata("AUTH_language") == "arabic" ? "&lrm;" : "&rlm;";?>
                      </span>
                  </bdi>
              </h4>
              <h4 class="sub-title-matter"><span class="text-gray"><?php echo $edit_page_title ?><span<?php echo $cp_class ?>>&nbsp;&nbsp;&nbsp;<span id="cp-icon" onclick="showMatterInCustomerPortal(<?php echo (int) $legalCase["id"] ?>, event);" class="big-title-text-font-size <?php echo  $cp_class_icon ?>" aria-hidden="true"></i></span><?php ?></h4>
          </div>
      </div><?php
          if (isset($actions)) {
              echo $actions;
          }
          if ($main_tab) {?>
              <div class="flex-end-item">
                  <div class="flex-item-box pull-right no-padding pt-5 status-top-header-width padding-15-mobile">
                      <div class="no-padding" id="status-top-nav-container">
                          <div class="col-md-12 no-padding mobile-no-margin">
                              <div class="pull-right d-flex"><?php
                                  $currentStatus = $Case_Statuses[$legalCase["case_status_id"]];
                                  unset($Case_Statuses[$legalCase["case_status_id"]]);
                                  $firstStatuses = array_slice($Case_Statuses, 0, 3, true);
                                  $otherStatuses = array_slice($Case_Statuses, 3, NULL, true);
                                  foreach ($firstStatuses as $statusId => $statusName) {
                                      $transitionName = $statusName;
                                      foreach ($statusTransitions as $transition) {
                                          if ($transition["toStep"] == $statusId) {
                                              $transitionName = $transition["name"];
                                              $transitionDescription = $transition["comments"];
                                              $transition_id = $transition["id"];
                                          }
                                      }
                                      if (isset($transition_id) && $transition_id) {         ?>
                                          <a href="javascript:;" onclick="screenTransitionForm('<?php echo $legalCase["id"];?>', '<?php  echo $transition_id;   ?>', 'cases');" title=" <?php echo isset($transitionDescription) && $transitionDescription !== "" ? $transitionDescription : $transitionName;?>"  class="case-move-status-link btn btn-default btn-status">
                                              <?php echo $transitionName; ?></a>&nbsp;&nbsp;<?php
                                      } else {?>
                                          <a href="<?php echo site_url("cases/move_status/" . $legalCase["id"] . "/" . $statusId);?>"  title=" <?php echo isset($transitionDescription) && $transitionDescription !== "" ? $transitionDescription : $transitionName;  ?>"  class="case-move-status-link btn btn-default btn-status submit-with-loader"> <?php echo $transitionName; ?> </a>&nbsp;&nbsp <?php
                                      }
                                      $transitionDescription = "";
                                      $transition_id = "";
                                  } ?>
                                  <div class="caseStatusesVisibleList">
                                      <div class="dropdown"><?php if (!empty($otherStatuses)) { ?>
                                              <button class="btn btn-default dropdown-toggle btn-status" type="button" id="dropdownMenuMoreStatuses" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                  <?php echo $this->lang->line("more");     ?><span class="caret"></span>
                                              </button>
                                              <div class="dropdown-menu dropdownMenuMoreStatusesList" aria-labelledby="dropdownMenuMoreStatuses"><?php
                                                  foreach ($otherStatuses as $statusId => $statusName) {
                                                      $transitionName = $statusName;
                                                      foreach ($statusTransitions as $transition) {
                                                          if ($transition["toStep"] == $statusId) {
                                                              $transitionName = $transition["name"];
                                                              $transition_id = $transition["id"];
                                                          }
                                                      }
                                                      if (isset($transition_id) && $transition_id) {?>
                                                          <a class="dropdown-item" href="javascript:;" onclick="screenTransitionForm('<?php echo $legalCase["id"];?>', '<?php  echo $transition_id;?>', 'cases');" title=" <?php echo $transitionName;?>"><?php echo 45 <= mb_strlen($transitionName) ? mb_substr($transitionName, 0, 41) . "..." : $transitionName; ?> </a>
                                                      <?php } else {?>
                                                          <a class="dropdown-item" href="<?php echo site_url("cases/move_status/" . $legalCase["id"] . "/" . $statusId); ?>" title="<?php echo $transitionName; ?>"><?php echo 45 <= mb_strlen($transitionName) ? mb_substr($transitionName, 0, 41) . "..." : $transitionName; ?></a>
                                                      <?php } $transition_id = "";
                                                  } ?>
                                              </div>
                                              <?php
                                      } ?>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="top-header-icons-container">
                          <div class="no-padding mt-7 notification-icon-margin flex-grow">
                              <div class="url-for-customers-container label-notification-checkbox ">
                                  <span class="checkbox no-padding-left no-margin notification-send-email-matter btn btn-disable-archived-matter borderless"><?php $this->load->view("templates/send_email_option_template", ["type" => $category == "litigation_" ? "edit_litigation_case" : "edit_matter_case", "container" => "#legalCaseAddForm", "loader" => "<img class=\"form-submit-loader\" src=\"assets/images/icons/16/loader-submit.gif\"/>", "hide_show_notification" => $hide_show_notification_edit_legal_case, "hide_label" => false]); ?></span>
                              </div>
                          </div>
                          <div id="top-header-note-container" class="no-padding flex-grow">
                              <div class="url-for-customers-container cursor-pointer-click">
                                  <i class="fa fa-comments purple_color font-15 tooltip-title btn btn-disable-archived-matter borderless" title="<?php echo $this->lang->line("add_note");?>" onclick="addCaseDocument('<?php echo $legalCase["id"];?>')"></i>
                              </div>
                          </div>
                      </div>
                      <div class="no-padding no-margin-left">
                          <div class="navbar-custom-menu pull-right">
                              <?php
                              if (isset($is_edit)) {     ?>
                                  <ul class="flex-center-inline navbar-actions center-block primary-style d-flex no-padding m-0">
                                      <li class="top-right-save-btn">
                                          <button type="button" id="legal-case-add-form" class="btn save-button button-blue-color btn-info">
                                              <i class="icon-alignment fa-solid fa-floppy-disk white-text padding-0-5-important"></i><?php echo $this->lang->line("save"); ?>
                                          </button>
                                      </li>
                                      <li>
                                          <div class="pull-right">
                                              <div class="dropdown more pull-right">
                                                  <button data-toggle="dropdown" class="save-button-blue btn button-blue-color btn-info" id="cases-actions-button">
                                                      <i class="icon-alignment fa fa-sliders white-text padding-0-5-important"></i><?php echo $this->lang->line("actions");?>
                                                  </button>
                                                  <div aria-labelledby="dLabel" role="menu" class="dropdown-menu dropdown-menu-right sub-menu-option">
                                                      <a class="dropdown-item" href="<?php echo site_url("cases/export_to_word/" . $legalCase["id"]);?>" onclick="return isFormChanged();"><?php  echo $this->lang->line("export_to_word"); ?></a><?php
                                                      if ($partnersCommissions == "yes") {?>
                                                      <a class="dropdown-item" href="javascript:;" onclick="caseCommissions('<?php echo $legalCase["id"];?>', event);"><?php echo $this->lang->line("partners_shares");?></a>
                                                      <?php }
                                                      if ($slaFeature == "yes") {?>
                                                      <a class="dropdown-item" href="javascript:;" onclick="slaShowLogs('<?php   echo $legalCase["id"];?>', event);"><?php  echo $this->lang->line("show_sla_elapsed_time"); ?></a>
                                                      <?php  }
                                                      if ($legalCase["category"] == "Matter") { ?>
                                                      <a class="dropdown-item" href="javascript:;" onclick="convertToLitigation('<?php  echo $legalCase["id"];?>', '<?php   echo $legalCase["case_type_id"];?>', '<?php echo $legalCase["legal_case_stage_id"]; ?>', false, true);"><?php echo $this->lang->line("convert_to_litigation"); ?></a><?php
                                                      }
                                                      if ($legalCase["channel"] != "CP") {?>
                                                      <a class="dropdown-item" href="javascript:;" id="show-hide-btn" onclick="showMatterInCustomerPortal('<?php echo $legalCase["id"];?>', event);"><?php echo $legalCase["visibleToCP"] ? $this->lang->line("hide_matter_in_customer_portal") : $this->lang->line("show_matter_in_customer_portal"); ?></a><?php
                                                      } ?>
                                                      <a class="dropdown-item" id="archive-unarchive-btn" href="javascript:;" onclick="archiveUnarchiveCase('<?php echo $legalCase["id"];?>', '<?php  echo $legalCase["archived"];?>');" ><?php echo $legalCase["archived"] == "no" ? $this->lang->line("archive") : $this->lang->line("unarchive"); ?></a>
                                                      <a class="dropdown-item" id="delete-case" href="javascript:;" onclick="recommendMatterClosure('<?php echo $legalCase["id"]; ?>',event);"><?php  echo $this->lang->line("recommend_closure");        ?></a>
                                                      <a class="dropdown-item" id="delete-case" href="javascript:;" onclick="deleteCaseRecord('<?php echo $legalCase["id"];?>');"><?php echo $this->lang->line("delete"); ?></a>
                                                  </div>
                                              </div>
                                          </div>
                                      </li>
                                  </ul>
                                  <?php
                              } ?>
                          </div>
                      </div>          
                  </div>          
              </div>
          <?php } ?>
      </div>
</nav>          
<script>          
      let positionMatterTitle = 'top-left';          
      if (_lang.languageSettings['langDirection'] === 'rtl'){          
          positionMatterTitle = 'top-right';          
      }          
      jQuery('#matter-title').tooltipster({          
          position: positionMatterTitle,          
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
</script>          
