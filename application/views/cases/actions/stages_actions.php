<div class="flex-item-box pull-right no-padding pt-5 padding-15-mobile flex-end-item">
   <div class="no-padding margin-left-8">
          <div class="navbar-custom-menu pull-right">
              <ul class="navbar-actions center-block primary-style d-flex no-padding m-0">
                  <li>
                      <div class="dropdown more pull-right action-add-btn">
                          <button id="stages-options-options" data-toggle="dropdown" class="save-button-blue btn button-blue-color btn-info">
                              <i class="fa fa-sliders icon-alignment white-text padding-0-5-important"></i>
                              <?php echo $this->lang->line("actions");?>                       </button>
                          <div aria-labelledby="stages-options-options" class="dropdown-menu dropdown-menu-right sub-menu-option" role="menu" aria-labelledby="dLabel">
                              <a class="dropdown-item disable-anchor" href="javascript:;" class="" onclick="changeLitigationStage('case-events-container');"><?php echo $this->lang->line("change_litigation_stage");?></a>
                              <a class="dropdown-item disable-anchor" href="javascript:;" class="" onclick="legalCaseHearingForm(0, false, '', true, function (){ legalCaseEvents.openHearingTab('null','<?php echo $case_id;?>', true)});"><?php echo $this->lang->line("add_a_hearing");?></a>
                              <a class="dropdown-item disable-anchor" href="javascript:;" class="" onclick="eventForm(false, false,function (stageId){ legalCaseEvents.goToPage('events', {stageId: (stageId ? stageId : null), id: <?php echo (int) $case_id;?>}) });"><?php echo $this->lang->line("add_event");?></a>
                              <a class="dropdown-item disable-anchor" href="javascript:;" class="" onclick="taskAddForm('<?php echo $case_id;?>', false, function (){ legalCaseEvents.openTaskTab('null','<?php echo $case_id;?>', true) });"><?php echo $this->lang->line("add_tasks");?></a>
                              <a class="dropdown-item disable-anchor" href="javascript:;" class="" onclick="reminderForm(false,false,'<?php echo $case_id;?>', false, function (){ legalCaseEvents.openRemindersTab('null','<?php echo $case_id;?>', true) });"><?php echo $this->lang->line("add_reminder");?></a>
                              <a class="dropdown-item" href='export/case_events/<?php echo $case_id;?>'><?php echo $this->lang->line("export_to_excel");?></a>
                          </div>
                      </div>
                  </li>
              </ul>
          </div>
   </div>
</div>
<style>
    #case-events-container .dropdown{
        display: block;
      }
</style>
<script>
    var disableMatter = '<?php echo $legalCase["archived"] == "yes" && isset($systemPreferences["disableArchivedMatters"]) && $systemPreferences["disableArchivedMatters"] ? true : false;?>';
    let actionAddBtn = jQuery('.action-add-btn');
    if('undefined' !== typeof(disableMatter) && disableMatter){
        disableAnchors(actionAddBtn);
              }
</script>