<?php
$module = $legalCase["category"] == "IP" ? "intellectual_properties" : "cases";
?>
<div class="row primary-style" id="edit-legal-case-expenses">
    <?php if ($legalCase["category"] == "IP") : ?>
        <?php $this->load->view("intellectual_properties/top_nav", ["is_edit" => 1, "main_tab" => false]); ?>
    <?php else : ?>
        <?php $this->load->view("cases/top_nav", ["is_edit" => 1, "main_tab" => false]); ?>
    <?php endif; ?>

    <div class="main-offcanvas main-offcanvas-left">
        <?php $this->load->view("partial/tabs_subnav_vertical", $tabsNLogs); ?>

        <div class="resp-main-body-width-70 no-padding flex-scroll-auto" id="main-content-side">
            <div class="main-content-section main-grid-container">
                <?php if ($legalCase["category"] == "IP") : ?>
                    <?php $this->load->view("intellectual_properties/object_header"); ?>
                <?php else : ?>
                    <?php $this->load->view("cases/object_header"); ?>
                <?php endif; ?>

                <div class="col-md-12 no-margin no-padding grid-section">
                    <div <?php echo $this->session->userdata("AUTH_language") == "arabic" ? "class=\"k-rtl\"" : "" ?>>
                        <div id="caseExpensesGrid" class="box-shadow_container grid-container kendo-desktop"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view("excel/form"); ?>

<script type="text/javascript">
    var disableMatter = '<?php echo $legalCase["archived"] == "yes" && isset($systemPreferences["disableArchivedMatters"]) && $systemPreferences["disableArchivedMatters"] ? true : false ?>';
    var caseId = '<?php echo $caseId ?>';
    var controller = '<?php echo $module ?>';
    var myExpenses = '<?php echo $legalCase["category"] == "IP" ? false : $my_expenses ?>';
</script>

<style>
    .main-grid-container .kendo-desktop .k-grid-content {
        height: calc(100vh - 375px) !important;
    }
</style>