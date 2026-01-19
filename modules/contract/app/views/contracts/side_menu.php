<div id="sub-menu-sidebar" class="resp-main-body-width-10 no-margin col-md-2 side-menu">
    <ul class="left-menu w-100 nav nav-tabs  navbar-nav no-margin pt-0 box-shadow-panel">
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style active" href="modules/contract/contracts/development/<?php echo $contract["id"];?>">
            <img src="assets/images/contract/edit.svg" height="25" width="25" class="filter-color mr-2"><?php echo (strtolower($contract["category"])=="mou") ? $this->lang->line("mou_development") : $this->lang->line("contract_development"); ?> </a>
        </li>
           <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style active" href="modules/contract/contracts/view/<?php echo $contract["id"];?>">
                <img src="assets/images/contract/contract.svg" height="25" width="25" class="filter-color mr-2"><?php  echo strtolower($contract["category"])=="mou"?$this->lang->line("mou_details"):$this->lang->line("contract_details_in_menu"); ?> </a>
        </li>
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style" href="javascript:;" id="docs-tab" onclick='docsTab("<?php echo $contract["id"];?>")'>
                <img src="assets/images/contract/document.svg" height="25" width="25" class="filter-color mr-2"><?php echo $this->lang->line("contract_related_documents");?>
                <div id="related-documents-count" class="total-related-documents badge badge-pill documents-count float-right"><?php echo $related_documents_count;?> </div>
            </a>
        </li>
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style" href="javascript:;" id="collaborate-tab" onclick='draftCollaborateTab("<?php echo $contract["id"];?>")'>
                <img src="assets/images/contract/collaborate.svg" height="25" width="25" class="filter-color mr-2"><?php echo $this->lang->line("draft_and_collaborate"); ?> </a>
        </li>
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style" href="javascript:;" id="approval-center-tab" onclick='approvalCenterTab("<?php echo $contract["id"];?>");'>
                <img src="assets/images/contract/history.svg" height="25" width="25" class="filter-color mr-2"><?php echo $this->lang->line("approval_center");?>
                <img id="approval-status-icon" class ="float-right no-margin icon-tooltip-link <?php echo $overall_approval_status == "" ? "d-none" : "";?>" src="<?php echo $overall_approval_status != "" ? "assets/images/contract/" . $overall_approval_status : "//:0";?>.svg" height="19.25" width="19.25" title="<?php echo $overall_approval_status != "" ? $this->lang->line($overall_approval_status) : "";?>"></a>
        </li>
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style" href="javascript:;" id="signature-center-tab" onclick='signatureCenterTab("<?php echo $contract["id"];?>");'>
                <img src="assets/images/contract/stamp.svg" height="25" width="25" class="filter-color mr-2"><?php echo $this->lang->line("signature_center"); ?>
                <img id="signature-status-icon" class ="float-right no-margin icon-tooltip-link <?php echo $overall_signature_status == "" ? "d-none" : "";?>" src="<?php echo $overall_signature_status != "" ? "assets/images/contract/" . $overall_signature_status : "//:0";?>.svg" height="19.25" width="19.25" title="<?php echo $overall_signature_status != "" ? $this->lang->line($overall_signature_status) : "";?>"> </a>
        </li>
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style" href="javascript:;" onclick='relatedRemindersTab("<?php echo $contract["id"]; ?>");' id="related-reminders-tab"><img src="assets/images/contract/reminder.svg" height="25" width="25"  class="filter-color mr-2"><?php echo $this->lang->line("reminders");?></a>
        </li>
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style" href="javascript:;" onclick='relatedTasksTab("<?php echo $contract["id"]; ?>");' id="related-tasks-tab"><img src="assets/images/contract/list.svg" height="25" width="25" class="filter-color mr-2"> <?php echo $this->lang->line("related_tasks"); ?> </a>
        </li>

        <li role="presentation" class="sub-menu core-access">
            <a class="nav-link panel-text-nav-style" href="javascript:;" onclick='relatedCasesTab("<?php echo $contract["id"]; ?>");' id="related-cases-tab">
                <img src="assets/images/contract/legal_matter.png" height="25" width="25" class="filter-color mr-2">
                <?php echo $this->lang->line("related_matters"); ?> </a>
        </li>
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style" href="javascript:;" onclick='relatedContractsTab("<?php echo $contract["id"]; ?>");' id="related-contract-tab">
                <img src="assets/images/contract/contract.svg" height="25" width="25" class="filter-color mr-2"><?php echo strtolower($contract["category"])=="mou"?$this->lang->line("related_mous"): $this->lang->line("related_contracts"); ?></a>
        </li>

       <li role="presentation" class="sub-menu">
             <a class="nav-link panel-text-nav-style" href="javascript:;" onclick='milestone("<?php echo $contract["id"]; ?>");' id="related-milestones-tab" ><img src="assets/images/contract/milestone.svg" height="25" width="25" class="filter-color">
             <?php echo $this->lang->line("related_milestones"); ?></a>
              </li>

           <li role="presentation" class="sub-menu">
                  <a class="nav-link panel-text-nav-style" href="javascript:;" onclick='relatedSuretiesTab("<?php echo $contract["id"]; ?>");' id="related-sureties-tab" ><img src="assets/images/contract/expensive.svg" height="25" width="25" class="filter-color">
                 <?php echo $this->lang->line("related_sureties"); ?>
                    </a>
             </li>
        <li role="presentation" class="sub-menu">
            <a class="nav-link panel-text-nav-style" href="javascript:;" onclick='relatedLegalOpinionsTab("<?php echo $contract["id"]; ?>");' id="related-legal-opinions-tab">
                <img src="assets/images/contract/opinion.svg" height="25" width="25" class="filter-color"/>
                <?php echo $this->lang->line("related_legal_opinions"); ?>
            </a>
        </li>
    </ul>
</div>