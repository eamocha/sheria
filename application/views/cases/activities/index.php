<div class="row primary-style" id="case-events-container">
 <?php $actions_view = $this->load->view("cases/actions/stages_actions", "", true);
 $this->load->view("cases/top_nav", ["is_edit" => 1, "main_tab" => false, "actions" => $actions_view]);?>
    <div class="main-offcanvas main-offcanvas-left">
        <?php $this->load->view("partial/tabs_subnav_vertical", $tabsNLogs);?>
        <div class="resp-main-body-width-70 no-padding flex-scroll-auto" id="main-content-side">
            <div class="main-content-section main-grid-container"><?php $this->load->view("cases/object_header");?>
                <div id="stages-page-container"></div>
            </div>
         </div>
     </div>
</div>
<script>
    var caseId = '<?php echo $case_id;?>';
    var latestDevelpementHidden = <?php echo json_encode($legalCase["latest_development"]);?>;
</script>