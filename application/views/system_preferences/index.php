<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard/admin">
                        <?php echo $this->lang->line("administration"); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <?php echo $this->lang->line("default_values"); ?>
                </li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="col-md-12 no-padding">
                <?php echo form_open("", "name=\"systemPreferencesForm\" id=\"systemPreferencesForm\" method=\"post\" class=\"form-inline\""); ?>
                <div class="col-md-12" id="sysConfTabs">
                    <ul class="col-md-2 row">
                        <?php foreach ($sysPreferences as $groupName => $key) { ?>
                            <li class="col-md-12">
                                <a class="trim-width-250" href="system_preferences#<?php echo url_title($groupName); ?>">
                                    <?php echo $this->lang->line($groupName); ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    <div class="col-md-10 col-xs-12 table-responsive" style="height: 540px;">
                        <?php
                        $plan_execluded_features = $this->plan_excluded_features ? explode(",", $this->plan_excluded_features) : [];
                        foreach ($sysPreferences as $groupName => $keysArr) { ?>
                            <div id="<?php echo url_title($groupName); ?>" class="col-md-12 no-padding">
                                <p>
                                <table class="table table-bordered table-striped table-hover filterable margin-top">
                                    <thead>
                                    <tr>
                                        <th>
                                            <?php echo $this->lang->line("property"); ?>&nbsp;
                                        </th>
                                        <th>
                                            <?php echo $this->lang->line("default_value"); ?>
                                        </th>
                                        <th>
                                            <?php echo $this->lang->line("actions"); ?>&nbsp;
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($groupName === "MenuExternalLinks") {
                                        uksort($keysArr, "strnatcmp");
                                    }
                                    foreach ($keysArr as $key => $val) {
                                        $property_label = $this->lang->line($key);
                                        if (in_array($key,["hijriCalendarFeature", "password", "outgoingMailSmtpPass", "caseMaxOpponents", "webhook_url_1", "webhook_url_2", "webhooks_enabled", "AllowFeatureHearingVerificationProcess", "HearingVerificationProcessUserGroups", "allowTimeEntryLoggingRule", "AllowAddContacts", "defaultCompany", "smsAuthToken", "EnableCorrespondenceModule", "EnableTimeFeature", "EnableOpinionsModule", "EnableConveyancingModule", "EnableIntellectualPropertyModule", "EnableProsecutionModule", "EnableOtherAgreementsModule"])) {
                                            $title = $this->lang->line($key . "_more_info");
                                            $property_label .= "&nbsp;&nbsp;&nbsp;<i tooltipTitle=\"" . $title . "\" class=\"fa fa-question-circle tooltipTable\"></i>";
                                        }
                                        if (in_array($key, ["adEnabled"]) && !empty($plan_execluded_features)) {
                                            $disable_feature = false;
                                            if (in_array("LDAP-User-Management-Integration", $plan_execluded_features)) {
                                                $title = $this->plan_feature_warning_msgs["LDAP-User-Management-Integration"] ?? $this->lang->line("you_do_not_have_enough_previlages_to_access_the_requested_feature");
                                                $property_label .= "&nbsp;&nbsp;&nbsp;<i tooltipTitle=\"" . $title . "\" class=\"fa fa-question-circle tooltipTable\"></i>";
                                            }
                                        }
                                        if (in_array($key, ["AllowFeatureAzureAd", "AllowFeatureAzureAdLogoutEnable"]) && !empty($plan_execluded_features)) {
                                            $disable_feature = false;
                                            if (in_array("Azure-User-Management-Integration", $plan_execluded_features)) {
                                                $title = $this->plan_feature_warning_msgs["Azure-User-Management-Integration"] ?? $this->lang->line("you_do_not_have_enough_previlages_to_access_the_requested_feature");
                                                $property_label .= "&nbsp;&nbsp;&nbsp;<i tooltipTitle=\"" . $title . "\" class=\"fa fa-question-circle tooltipTable\"></i>";
                                            }
                                        }
                                        if (in_array($key, ["menu_url_1", "menu_url_2", "menu_url_3", "menu_url_4", "menu_url_5", "menu_url_6", "menu_url_7", "menu_url_8", "menu_url_9", "menu_url_10"])) {
                                            $custom_link_number = mb_substr($key, mb_strrpos($key, "_") + 1);
                                            $property_label = $this->lang->line("custom_link") . "&nbsp;" . $custom_link_number;
                                        } ?>
                                        <tr>
                                            <td class="labelsText"><?php echo $property_label; ?></td>
                                            <td class="input-div"><?php echo $formHTML[$key]; ?></td>
                                            <td class="actions-div">
                                                <input type="button" name="btnSave" tabindex="-1" value="<?php echo $this->lang->line("save"); ?>" onclick="saveSystemDefaultValue('<?php echo $key; ?>');" class="btn btn-default btn-info btn-sm" />
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                </p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-actions mt-10 mx-1">
                    <input type="button" name="btnSubmit" value="<?php echo $this->lang->line("save_all"); ?>" class="btn btn-default btn-info" onclick="saveAllConfg();" />
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var hasPendingChangesInUsersGroupsData = '<?php echo $hasPendingChangesInUsersGroupsData; ?>';
    cloudInstallation = '<?php echo $this->cloud_installation_type; ?>';

    function saveAllConfg() {
        if (jQuery('#systemPreferencesForm').validationEngine('validate')) {
            if (jQuery('#valuemakerCheckerFeatureStatus').val() != 'yes' && hasPendingChangesInUsersGroupsData) {
                pinesMessage({ty: 'warning', m: _lang.disableMakerCheckerControlMsg});
            } else {
                jQuery('#systemPreferencesForm').submit();
            }
        }
    }
</script>

<style>
    .ui-widget-content {
        border: 0;
    }
    .ui-tabs-nav {
        background: transparent;
    }
</style>