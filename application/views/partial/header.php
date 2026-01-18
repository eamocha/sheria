<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
$lang = $this->session->userdata("AUTH_language");
$html_lang = $lang == "spanish" ? "es" : substr($lang, 0, 2);
$current_url = current_url() . "/";
?>
<html lang="<?php echo $html_lang; ?>" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo  str_replace("App4Legal", $this->instance_data_array["app_name"], $this->pageTitle); ?></title>

    <base href="<?php echo BASEURL; ?>"/>
    <meta name="viewport" content="width=device-width ,user-scalable=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <link rel="shortcut icon"
          href="<?php echo $this->instance_data_array["app_favicon"] . "?v=" . $this->instance_data_array["app_theme_version"]; ?>"
          type="image/icon"/>
    <?php if (!$this->session->userdata("set_access_token")) { ?>
        <?php $this->session->set_userdata("set_access_token", "1"); ?>
        <script type="text/javascript">localStorage.removeItem("api-access-token");</script>
    <?php } ?>
    <script language="javascript" type="text/javascript">
        var style = document.createElement("style");
        style.type = "text/css";
        style.id = "antiClickjack";
        if ("cssText" in style)
            style.cssText = "body{display:none !important;}";
        else
            style.innerHTML = "body{display:none !important;}";
        document.head.appendChild(style);
        if (self === top) {
            var antiClickjack = document.getElementById("antiClickjack");
            antiClickjack.parentNode.removeChild(antiClickjack)
        } else {
            top.location = self.location
        }
        var licenseHasExpired = '<?php echo $this->license_availability === false; ?>';
        var contractLicenseHasExpired = '<?php echo $this->contract_license_availability === false; ?>';
        var clientLicenseType = '<?php echo $this->cloud_installation_type ? $this->instance_client_type : "on-server"; ?>';
        var contactUsLink = '<?php echo $this->config->item("sheria360_website_feedback_page"); ?>';
        var notificationRefreshInterval = '<?php echo $this->notificationRefreshInterval; ?>';
        var remindersRefreshInterval = '<?php echo $this->remindersRefreshInterval; ?>';
        var authIdLoggedIn = '<?php echo $this->is_auth->get_user_id(); ?>';
        var sqlsrv2008 = '<?php echo $this->sqlsrv_2008; ?>';
        var planExcludedFeatures = '<?php echo $this->plan_excluded_features; ?>';
        var planFeatureWarningMsgs = '<?php echo json_encode($this->plan_feature_warning_msgs); ?>';
        var licensePackage = '<?php echo $this->license_package; ?>';
        var recentVisitedLang = '<?php echo $this->lang->line("recent_visited"); ?>';
        var userGuide = '<?php echo $this->user_guide; ?>';
        <?php if (isset($this->workthrough)) { ?>
        var workthrough = <?php echo is_null($this->workthrough) ? "[]" : json_encode(unserialize($this->workthrough)); ?>;
        <?php } else { ?>
        var workthrough = [];
        <?php } ?>;
        var firstSignIn = '<?php echo $this->first_sign_in; ?>';
        if(firstSignIn == 'NULL') firstSignIn = '';
        var APPENVIRONMENT = '<?php echo ENVIRONMENT; ?>';
        var isloggedIn = '<?php echo $this->is_auth->is_logged_in() && !$this->input->is_ajax_request() ? "logged" : "noLogged"; ?>';
        var userLanguage = '<?php echo $this->session->userdata("AUTH_language"); ?>';
        <?php $this->load->helper("encrypt_decrypt_helper"); ?>
        var _tokenGlobal = '<?php echo $this->is_auth->is_logged_in() ? encrypt_string($this->session->userdata("AUTH_email_address")) : ""; ?>';
        var allowedDecimalFormatGlobal = <?php echo $this->config->item("allowed_decimal_format"); ?>;
        var chatbotAnable = false;
        // Insert default generator template
        function addDefaultGeneratorTemplate(reference) {
            let url = getBaseURL().concat('document_generator/add_default_generator_template');
            jQuery.ajax({
                url: url,
                data: {reference: reference},
                dataType: 'JSON',
                type: 'POST',
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                success: function (response) {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.templateAddedSuccessfully});
                }, complete: function () {
                    jQuery('#loader-global').hide();
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    </script>
    <?php print_css($this->css); ?>
    <?php print_js($this->js); ?>
    <script type="text/javascript">
        //initializing bootstrapDP to be used instead of datepicker to avoid the conflict that occurs between jquery and bootstrap
        var datepicker = jQuery.fn.datepicker.noConflict();
        jQuery.fn.bootstrapDP = datepicker;
        initApiAccessToken();
    </script>
    <link
            href="<?php echo BASEURL; ?>files/app_themes/<?php echo $this->instance_data_array["app_theme"]; ?>/<?php echo $this->instance_data_array["app_theme"]; ?>.css?v=<?php echo $this->instance_data_array["app_theme_version"]; ?>"
            rel="stylesheet">
    <?php
    $trial_period = $this->licensor->extendExpiredInstance + $this->licensor->expiration_days;
    $instance_data_id = $this->instance_data_array["instanceID"] ?? 0;
    if ($this->cloud_installation_type && 0 < $instance_data_id) {
        ?>
        <script type="text/javascript">

            var Tawk_API = Tawk_API || {},
                Tawk_LoadStart = new Date();
            (function () {
                var s1 = document.createElement("script"),
                    s0 = document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src = 'https://embed.tawk.to/60d2f09365b7290ac6377001/1f8rvngcu';
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s0.parentNode.insertBefore(s1, s0);
            })();
            chatbotAnable = true;
        </script>
    <?php } ?>
    <?php if ($this->config->item("csrf_protection")) { ?>
        <script type="text/javascript">
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
            var csrfValue = '<?php echo $this->security->get_csrf_hash(); ?>';
            jQuery.ajaxSetup({data: {<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'}});
        </script>
    <?php } ?>
</head>


<body>
<div id="universal-search-slide" style="width: 0">
    <div id="universal-search-container">
        <div id="quick-search" class="navbar-form1 navbar-left no-padding width-100 padding-15">
            <div class="form-group">
                <label for="search" class="sr-only">Search</label><?php echo form_input(["placeholder" => $this->lang->line("universal_search"), "id" => "universal-search-input", "name" => "universal_search", "class" => "form-control line", "onkeyup" => "universalSearch(jQuery(this), event.keyCode);"]);?>
                <span class="form-control-feedback cursor-pointer-click" onclick="universalSearch(jQuery('#universal-search-input'), event.keyCode, true)"><i class="fa fa-search purple_color font-16"></i></span>
            </div>
        </div>
        <div id="search-related-object-view">
            <div class="col-md-12">
                <h4 class="mb-15 no-margin-top"><strong><?php echo $this->lang->line("recent_visited");?></strong></h4>
            </div>
            <div id="universal-loading"></div>
        </div>
    </div>
</div>
<div id="universal-search-slide-wrapper" class="d-none"></div>
<?php $display_whats_new = $this->session->userdata("display_whats_new");
$this->session->unset_userdata("display_whats_new");
echo form_input(["id" => "display-whats-new", "value" => $display_whats_new, "type" => "hidden"]);
?>    <div id="loader-global">
    <div class="loader">
        <div>
            <div></div>
        </div>
        <div>
            <div></div>
        </div>
        <div>
            <div></div>
        </div>
        <div>
            <div></div>
        </div>
    </div>
</div>
<div id="wrap" class="no-margin">
    <?php if ($this->is_auth->is_logged_in()) {
        $systemPreferences = $this->session->userdata("systemPreferences");
        $correspondenceEnabled = isset($systemPreferences["EnableCorrespondenceModule"]) && $systemPreferences["EnableCorrespondenceModule"] == "yes";
        $timeFeatureEnabled = isset($systemPreferences["EnableTimeFeature"]) && $systemPreferences["EnableTimeFeature"] == "yes";
        $opinionsModuleEnabled = isset($systemPreferences["EnableOpinionsModule"]) && $systemPreferences["EnableOpinionsModule"] == "yes";
        $conveyancingModuleEnabled = isset($systemPreferences["EnableConveyancingModule"]) && $systemPreferences["EnableConveyancingModule"] == "yes";
        $intellectualPropertyEnabled = isset($systemPreferences["EnableIntellectualPropertyModule"]) && $systemPreferences["EnableIntellectualPropertyModule"] == "yes";
        $prosecutionEnabled = isset($systemPreferences["EnableProsecutionModule"]) && $systemPreferences["EnableProsecutionModule"] == "yes";
        $otherAgreementsEnabled = isset($systemPreferences["EnableOtherAgreementsModule"]) && $systemPreferences["EnableOtherAgreementsModule"] == "yes";
        $legislativeDrafting = isset($systemPreferences["EnableLegislativeDraftingModule"]) && $systemPreferences["EnableLegislativeDraftingModule"] == "yes";
        ?>
        <div class="nav-full-width">
            <nav class="navbar navbar-expand-lg navbar-light top2 fixed-top" id="header_div">
                <div class="no-padding nav-full-width d-flex">
                    <a tabindex="-1" href="<?php     echo app_url($this->is_auth->is_logged_in() ? "dashboard" : "users/login");?>" class="app-logo navbar-brand page-scroll pull-left" style="background-image:url('<?php echo $this->instance_data_array["app_logo"] . "?v=" . $this->instance_data_array["app_theme_version"];?>');"> &nbsp; </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#top-links-navbar-collapse-2" aria-controls="top-links-navbar-collapse-2" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="top-links-navbar-collapse-2">
                        <?php     echo form_input(["id" => "loggedin-user-id", "value" => $this->is_auth->get_user_id(), "type" => "hidden"]);?>
                        <ul class="nav navbar-nav me-auto mb-2 mb-lg-0" id="top-nav-item-list">
                            <li class="nav-item dropdown" id="dashboard-menu-demo-open">
                                <a class="nav-link dropdown-toggle <?php     echo $current_url == app_url("dashboard") ? "active-title" : "";?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php     echo $this->lang->line("dashboard_in_menu");?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item <?php     echo $current_url == app_url("dashboard") ? "active-title" : "";    ?>" tabindex="-1" href="<?php    echo app_url("dashboard");?>"><?php    echo $this->lang->line("my_dashboard");?>    </a></li>
                                   <?php if($correspondenceEnabled){?><li><a class="dropdown-item core-access <?php    echo $current_url == app_url("front_office") ? "active-title" : "";    ?>" href="<?php    echo app_url("front_office/dashboard");   ?>"><?php     echo $this->lang->line("front_office_dashboard");?></a></li><?php }?>
                                    <li><a class="dropdown-item contract-access <?php     echo $current_url == app_url("modules/contract/dashboard") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("modules/contract/dashboard");?>"><?php     echo $this->lang->line("dashboard_contract");    ?></a></li>
                                    <li><a class="dropdown-item  <?php    echo $current_url == app_url("dashboard/time_tracking_dashboard") ? "active-title" : ""; ?>" href="<?php  echo app_url("dashboard/time_tracking_dashboard");  ?>"><?php    echo $this->lang->line("time_tracking_dashboard");    ?></a></li>
                                    <li><a class="dropdown-item core-access <?php     echo $current_url == app_url("dashboard/management") ? "active-title" : "";?>" href="<?php     echo app_url("dashboard/management");?>"><?php     echo $this->lang->line("dashboard_management");?></a></li>
                                    <li><a class="dropdown-item core-access <?php     echo $current_url == app_url("dashboard/litigation_dashboard/1") ? "active-title" : "";?>" href="<?php     echo app_url("dashboard/litigation_dashboard/1");?> "><?php echo $this->lang->line("litigation_management_dashboard_1");    ?></a></li>
                                    <li><a class="dropdown-item core-access <?php     echo $current_url == app_url("dashboard/litigation_dashboard/2") ? "active-title" : "";    ?>" href="<?php     echo app_url("dashboard/litigation_dashboard/2");   ?>"><?php     echo $this->lang->line("litigation_management_dashboard_2");?></a></li>
                                    <li><a class="dropdown-item core-access <?php     echo $current_url == app_url("dashboard/criminal_litigation_dashboard") ? "active-title" : "";    ?>" href="<?php     echo app_url("dashboard/criminal_litigation_dashboard/");   ?>"><?php     echo $this->lang->line("criminal_litigation_dashboard");?></a></li>
                                    <li id="dashboard-menu-list-demo-kanban" style="width: 100%;position: absolute;">
                                    <li><a class="dropdown-item core-access <?php     echo $current_url == app_url("dashboard/cases") ? "active-title" : "";    ?>" href="<?php    echo app_url("dashboard/cases");   ?>"><?php     echo $this->lang->line("case_board");    ?></a></li>
                                    <li><a class="dropdown-item contract-access <?php echo $current_url == site_url("modules/contract/dashboard/contracts") ? "active-title" : "";?>" href="<?php  echo app_url("modules/contract/dashboard/contracts"); ?>"><?php    echo $this->lang->line("contract_board");   ?></a></li>
                                    <li><a class="dropdown-item <?php echo $current_url == app_url("dashboard/tasks") ? "active-title" : ""; ?>" href="<?php echo app_url("dashboard/tasks"); ?>"><?php echo $this->lang->line("task_board");?></a></li>
                                </ul>
                            </li>
                            <?php if ($correspondenceEnabled)
                            {?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle  <?php echo $current_url == app_url("front_office") || $current_url == app_url("front_office/view") ? "active-title" : "";?><!--" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $this->lang->line("front_office_in_menu");?></a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="contacts-menu-demo-open">
                                    <li id="incoming-menu-demo"><a class="dropdown-item <?php echo $current_url == app_url("front_office/incoming") ? "active-title" : "";?><!--" tabindex="-1" href="<?php echo app_url("front_office/index/incoming");  ?> "><?php echo $this->lang->line("incoming");  ?></a>
                                    </li>
                                    <li id="outgoing-menu-demo"><a class="dropdown-item <?php   echo $current_url == app_url("front_office/outgoing") ? "active-title" : "";?>" tabindex="-1" href="<?php   echo app_url("front_office/index/outgoing"); ?>"><?php  echo $this->lang->line("outgoing");    ?></a>
                                    </li>
                                </ul>
                            </li>
                            <?php } else{?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle  <?php echo $current_url == app_url("contacts") || $current_url == app_url("companies") ? "active-title" : "";?>" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $this->lang->line("contact_in_menu");?></a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown" id="contacts-menu-demo-open">
                                        <li id="contacts-menu-demo"><a class="dropdown-item <?php echo $current_url == app_url("contacts") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("contacts");  ?>"><?php echo $this->lang->line("contacts");  ?></a></li>
                                        <li id="companies-menu-demo"><a class="dropdown-item <?php     echo $current_url == app_url("companies") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("companies"); ?>"><?php    echo $this->lang->line("companies");    ?></a></li>
                                    </ul>
                                </li>
                            <?php }?>

                            <li class="nav-item dropdown core-access" id="cases-menu-demo-open">
                                <a class="nav-link dropdown-toggle  <?php echo $current_url == app_url("contacts") || $current_url == app_url("companies") ? "active-title" : "";    ?>" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php    echo $this->lang->line("case_in_menu");?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li id="matter-menu-list-demo"><a class="dropdown-item <?php echo $current_url == app_url("cases/legal_matter/") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("cases/legal_matter/"); ?>"><?php echo $this->lang->line("matter_case_in_menu");   ?></a></li>
                                    <li id="litigation-menu-list-demo"><a class="dropdown-item <?php     echo $current_url == app_url("cases/litigation_case") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("cases/litigation_case");    ?>"><?php     echo $this->lang->line("civil_cases_in_menu");   ?></a></li>
                                    <li class="dropdown-divider "></li>

                                    <li class="dropdown-submenu external-links-dropdown">
                                        <a href="#" class="dropdown-item dropdown-toggle <?php echo $current_url == app_url("cases/criminal_case") || $current_url == app_url("cases/complaints_inquiries") || $current_url == app_url("cases/surveillance_detection") || $current_url == app_url("cases/investigation_enforcement") || $current_url == app_url("cases/master_register") || $current_url == app_url("exhibits") ? "active-title" : ""; ?>" role="button" aria-haspopup="true" aria-expanded="false">
                                            <span class="nav-label"><?php echo $this->lang->line("criminal_cases_in_menu"); ?></span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-to-right" style="min-width: 250px">
                                            <li><a class="dropdown-item <?php echo $current_url == app_url("cases/criminal_case") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("cases/criminal_case"); ?>"><?php echo $this->lang->line("criminal_cases_pbc"); ?></a></li>
                                            <li><a class="dropdown-item <?php echo $current_url == app_url("cases/investigation_enforcement") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("cases/investigation_enforcement"); ?>"><?php echo $this->lang->line("investigation_and_surveillance"); ?></a></li>
                                            <li><a class="dropdown-item <?php echo $current_url == app_url("cases/complaints_inquiries") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("cases/complaints_inquiries"); ?>"><?php echo $this->lang->line("complaints_and_enquiries"); ?></a></li>
                                            <li><a class="dropdown-item <?php echo $current_url == app_url("cases/master_register") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("cases/master_register"); ?>"><?php echo $this->lang->line("master_register"); ?></a></li>
                                            <li><a class="dropdown-item <?php echo $current_url == app_url("exhibits") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("exhibits"); ?>"><?php echo $this->lang->line("exhibits_list"); ?></a></li>
                                            <li><a class="dropdown-item <?php echo $current_url == app_url("cases/enforcement") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("cases/enforcement"); ?>"><?php echo $this->lang->line("enforcement"); ?></a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown-divider"></li>
                                    <li><a class="dropdown-item <?php echo $current_url == app_url("case_containers") ? "active-title" : "";?>" tabindex="-1" href="<?php   echo app_url("case_containers"); ?>"><?php  echo $this->lang->line("list_case_containers");    ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("cases/my_hearings") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("cases/my_hearings"); ?>"><?php echo $this->lang->line("diary_in_menu"); ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("cases/external_counsel_expenses") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("cases/external_counsel_expenses"); ?>"><?php echo $this->lang->line("fee_notes"); ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("cases/external_counsel_expenses") ? "active-title" : "";?>" tabindex="-1" href="<?php echo app_url("orders_decrees"); ?>"><?php echo $this->lang->line("orders_decrees"); ?></a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown" id="contracts-menu-demo-open">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $this->lang->line("contracts");    ?></a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item <?php  echo $current_url == app_url("modules/contract/") ? "active-title" : "";   ?>" tabindex="-1" href="<?php echo app_url("modules/contract/");   ?>"><?php echo $this->lang->line("list_contracts"); ?></a></li>
                                    <li class="dropdown-divider "></li>
                                    <div class="ml-2 text-bold">Under development</div>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("modules/contract/contracts/awaiting_approvals") ? "active-title" : "";  ?>" tabindex="-1" href="<?php    echo app_url("modules/contract/contracts/awaiting_approvals");   ?>"><?php    echo $this->lang->line("awaiting_approvals");  ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("modules/contract/contracts/awaiting_signatures") ? "active-title" : "";    ?>" tabindex="-1" href="<?php    echo app_url("modules/contract/contracts/awaiting_signatures");   ?>"><?php    echo $this->lang->line("awaiting_signatures");    ?></a></li>
                                    <li class="dropdown-divider "></li>
                                    <div class="ml-2 text-bold">Executed Contracts</div>
                                    <li><a class="dropdown-item <?php  echo $current_url == app_url("modules/contract/") ? "active-title" : "";   ?>" tabindex="-1" href="<?php echo app_url("modules/contract/contracts/active_contracts");   ?>"><?php echo $this->lang->line("active"); ?></a></li>
                                    <li><a class="dropdown-item <?php  echo $current_url == app_url("modules/contract/") ? "active-title" : "";   ?>" tabindex="-1" href="<?php echo app_url("modules/contract/contracts/suspended_contracts");   ?>"><?php echo $this->lang->line("suspended"); ?></a></li>
                                    <li><a class="dropdown-item <?php  echo $current_url == app_url("modules/contract/") ? "active-title" : "";   ?>" tabindex="-1" href="<?php echo app_url("modules/contract/contracts/expired_contracts");   ?>"><?php echo $this->lang->line("expired"); ?></a></li>
                                    <li class="dropdown-divider "></li>
                                    <li><a class="dropdown-item <?php  echo $current_url == app_url("modules/contract/") ? "active-title" : "";   ?>" tabindex="-1" href="<?php echo app_url("modules/contract/surety_bonds");   ?>"><?php echo $this->lang->line("performance_bond_tracker"); ?></a></li>

                                </ul>
                            </li>
                           <?php if ($otherAgreementsEnabled){?>
                            <li class="nav-item dropdown" id="agreements-menu-demo-open">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php    echo $this->lang->line("other_agreements_in_menu");    ?></a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("modules/contract/contracts/mou") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("modules/contract/contracts/mou");    ?>"><?php     echo $this->lang->line("all_agreements_in_menu");   ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("modules/contract/contracts/mou") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("modules/contract/contracts/mou");    ?>"><?php     echo $this->lang->line("list_mous");   ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("modules/contract/contracts/tca") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("modules/contract/contracts/tca");    ?>"><?php     echo $this->lang->line("tcas_in_menu");   ?></a></li>

                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("modules/contract/contracts/dtp_responsibilities") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("modules/contract/contracts/dtp_responsibilities");    ?>"><?php     echo $this->lang->line("dtp_in_menu");   ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("modules/contract/contracts/pp_agreements") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("modules/contract/contracts/pp_agreements");    ?>"><?php     echo $this->lang->line("pp_in_menu");   ?></a></li>

                                    <li><a class="dropdown-item <?php     echo $current_url == app_url("modules/contract/clauses") ? "active-title" : "";?>" tabindex="-1" href="<?php    echo app_url("modules/contract/std_agreements");   ?>"><?php    echo $this->lang->line("standard_mou_clauses");    ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("modules/contract/contracts/awaiting_approvals") ? "active-title" : "";?>" tabindex="-1" href="<?php    echo app_url("modules/contract/contracts/awaiting_approvals"); ?>"><?php    echo $this->lang->line("awaiting_approvals");  ?></a></li>
                                    <li><a class="dropdown-item <?php     echo $current_url == app_url("modules/contract/contracts/awaiting_signatures") ? "active-title" : "";?>" tabindex="-1" href="<?php    echo app_url("modules/contract/contracts/awaiting_signatures");?>"><?php     echo $this->lang->line("awaiting_signatures");?></a></li>
                                </ul>
                            </li><?php
                           }
                          if($legislativeDrafting){?><li class="nav-item"> <a class="nav-link <?php echo $current_url == app_url("conveyancing") ? "active-title" : "";?>" href="<?php  echo app_url("ld");   ?>" tabindex="-1" aria-disabled="true">Legislative Drafting</a> </li><?php }
                            if($conveyancingModuleEnabled){?><li class="nav-item"> <a class="nav-link <?php echo $current_url == app_url("conveyancing") ? "active-title" : "";?>" href="<?php  echo app_url("conveyancing");   ?>" tabindex="-1" aria-disabled="true"><?php    echo $this->lang->line("conveyancing");    ?></a> </li><?php }?>
                            <?php if($opinionsModuleEnabled){?><li class="nav-item"> <a class="nav-link <?php    echo $current_url == app_url("legal_opinions/my_opinions") ? "active-title" : ""; ?>" href="<?php   echo app_url("legal_opinions/my_opinions");  ?>" tabindex="-1" aria-disabled="true"><?php   echo $this->lang->line("legal_opinions_menu"); ?></a> </li><?php }?>
                            <?php     $activeInstalledModules = $this->activeInstalledModules;
                            $activeInstalledModules["money"] = "money";
                            unset($activeInstalledModules["outlook"]);
                            unset($activeInstalledModules["customer-portal"]);
                            unset($activeInstalledModules["A4G"]);
                            unset($activeInstalledModules["microsoft-teams"]);
                            unset($activeInstalledModules["configuration"]);
                            unset($activeInstalledModules["advisor-portal"]);
                            unset($activeInstalledModules["contract"]);

                            foreach ($activeInstalledModules as $module => $moduleName) {
                                $class_active_module = current_url() == app_url("modules/" . $module) ? "active-title" : "";?>
                                <li class="nav-item">
                                    <a class="nav-link nav-li-hover core-access <?php     echo $class_active_module; ?>" href="<?php   echo app_url("modules/" . $module);?>" tabindex="-1" aria-disabled="true"><?php         echo $this->lang->line("billing_in_menu");        ?></a></li>
                            <?php }?>
                           <li class="nav-item dropdown">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="showOnMobileText">More</span> <i class="header-drop-down-2-arrows font-12px fa-angle-double-override"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li class="dropdown-item"> <a id="docs-menu-demo" class="nav-link <?php echo $current_url == app_url("docs") ? "active-title" : "";?>" href="<?php  echo app_url("docs");?>" tabindex="-1"><?php  echo $this->lang->line("docs");?></a> </li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("reports") ? "active-title" : "";    ?>" tabindex="-1" href="<?php    echo app_url("reports");   ?>"><?php    echo $this->lang->line("reports");    ?></a></li>
                                    <li> <a class="dropdown-item" <?php    echo $current_url == app_url("tasks/my_tasks") ? "active-title" : "";?> tabindex="-1" href="<?php echo app_url("tasks/my_tasks");?>"><?php echo $this->lang->line("task_in_menu");?></a></li>
                                    <?php if($timeFeatureEnabled){?><li><a class="dropdown-item core-access <?php  echo $current_url == app_url("time_tracking/my_time_entries") ? "active-title" : "";  ?>" tabindex="-1" href="<?php  echo app_url("time_tracking/my_time_entries");  ?>"><?php   echo $this->lang->line("time_tracking_in_menu");  ?></a></li><?php }?>
                                    <?php if($intellectualPropertyEnabled){?><li><a class="dropdown-item core-access <?php    echo $current_url == app_url("intellectual_properties") ? "active-title" : "";    ?>" tabindex="-1" href="<?php    echo app_url("intellectual_properties");   ?>"><?php    echo $this->lang->line("intellectual_properties");    ?></a></li><?php }?>
                                    <li class="dropdown-divider"></li>
                                    <li><div class="dropdown-item favorites"><?php  echo $this->lang->line("favorites");?></div></li>
                                    <li><a class="dropdown-item core-access <?php    echo $current_url == app_url("modules/money/vouchers/my_expenses_list") ? "active-title" : "";   ?>" tabindex="-1" href="<?php    echo app_url("modules/money/vouchers/my_expenses_list");   ?>"><?php    echo $this->lang->line("my_expenses");    ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("my_reminders") ? "active-title" : ""; ?>" tabindex="-1" href="reminders/show_my_reminders"><?php echo $this->lang->line("my_reminders");   ?></a></li>
                                    <li><a class="dropdown-item <?php    echo $current_url == app_url("documents_search") ? "active-title" : ""; ?>" tabindex="-1" href="documents_search"><?php echo $this->lang->line("documents_search");   ?></a></li>

                                    <?php if ($correspondenceEnabled){?>
                                    <li class="dropdown-divider"></li>
                                    <li class="dropdown-submenu external-links-dropdown">
                                        <a <?php echo $current_url == app_url("contacts") || $current_url == app_url("companies") ? "active-title" : "";?> href="#" class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span class="nav-label"><?php echo $this->lang->line("contact_in_menu");?></span></a>
                                        <ul class="dropdown-menu dropdown-to-right">
                                            <a id="contacts-menu-demo" class="dropdown-item <?php echo $current_url == app_url("contacts") ? "active-title" : "";?>" href="<?php echo app_url("contacts");  ?>"><?php echo $this->lang->line("contacts");  ?></a>
                                            <a id="companies-menu-demo" class="dropdown-item <?php     echo $current_url == app_url("companies") ? "active-title" : "";?>" href="<?php     echo app_url("companies"); ?>"><?php echo $this->lang->line("companies");  ?></a>
                                        </ul>
                                    </li><?php }?>
                                    <li class="dropdown-divider"></li>
                                    <li class="dropdown-submenu external-links-dropdown">
                                        <a href="#" class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span class="nav-label"><?php echo $this->lang->line("ExternalLinksTitle");?></span></a>
                                        <?php
                                        $custom_link_count = 0;?>
                                        <ul class="dropdown-menu dropdown-to-right">
                                            <?php
                                            for ($i = 1; $i <= 10; $i++) {
                                                $menu_url = "menu_url_" . $i;
                                                $custom_link = $this->lang->line("custom_link") . "&nbsp;" . $i;
                                                $systemPreferences[$menu_url] = (array) (empty($systemPreferences[$menu_url]) ? array_fill_keys(["title", "target"], "") : unserialize($systemPreferences[$menu_url]));
                                                if ($systemPreferences[$menu_url]["target"] != "") {
                                                    $custom_link_count = 1;?>
                                                    <a class="dropdown-item" href="<?php echo $systemPreferences[$menu_url]["target"];?>" target="_blank"><?php echo $systemPreferences[$menu_url]["title"] == "" ? $custom_link : $systemPreferences[$menu_url]["title"];  ?></a><?php
                                                }
                                            }
                                            if ($custom_link_count === 0) {?>
                                                <a class="favorites dropdown-item disabled"><?php echo $this->lang->line("empty"); ?></a>
                                            <?php    }?>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown header-add-tab" id="top-menu-add-list">
                                <button id="top-menu-add-button" type="button" name="quickAddButton" class="nav-link btn add-header-button menu_add_new" data-bs-toggle="dropdown" id0="navbarDropdownNew" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="v-align-top d-inline-block"><?php echo $this->lang->line("new");?></span> <span class="fa fa-plus"></span><span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu quick-add-container" aria-labelledby="navbarDropdownNew">
                                    <div class="row justify-content-md-center">
                                        <div class="col-md-6">
                                            <div><a class="dropdown-item" id="top-menu-add-button-company" href="javascript:;" onclick="companyAddForm();"><div class="sprite sprite-company"></div><?php echo $this->lang->line("new_company");    ?></a></div>
                                            <div><a class="dropdown-item" id="top-menu-add-button-contact"  href="javascript:;" onclick="contactAddForm(true);"><div class="sprite sprite-contact"></div><?php   echo $this->lang->line("new_contact"); ?></a></div>
                                            <div class="dropdown-divider"></div>
                                            <div id="top-menu-add-button-matter" class="core-access"><a class="dropdown-item" href="javascript:;" onclick="legalMatterAddForm();"><div class="sprite sprite-legal-matter"></div><?php     echo $this->lang->line("corporate_matter");  ?></a></div>
                                            <div class="dropdown-divider core-access"></div>
                                            <div class="core-access" id="top-menu-add-button-litigation"><a class="dropdown-item" href="javascript:;" onclick="litigationCaseAddForm();"><div class="sprite sprite-litigation-cases"></div><?php    echo $this->lang->line("civil_case");    ?></a></div>
                                            <div class="core-access" id="top-menu-add-button-litigation"><a class="dropdown-item" href="javascript:;" onclick="litigationCriminalCaseAddForm();"><div class="sprite sprite-litigation-cases"></div><?php echo $this->lang->line("criminal_case");?></a></div>
                                            <div class="core-access"><a class="dropdown-item" href="javascript:;" onclick="legalCaseHearingForm();"><div class="sprite sprite-hearing"></div><?php  echo $this->lang->line("hearing"); ?></a></div>
                                            <div class="dropdown-divider core-access"></div>
                                            <div class="core-access"><a class="dropdown-item" href="javascript:;" onclick="caseContainerForm();"><div class="sprite sprite-matter-container"></div><?php echo $this->lang->line("case_container"); ?></a></div>
                                            <div class="dropdown-divider core-access"></div>
                                            <div class="contract-access"><a class="dropdown-item" href="javascript:;" onclick="contractGenerate('choose');"><div class="sprite sprite-contract"></div><?php   echo $this->lang->line("contract_from_template");    ?></a></div>
                                             <div class="contract-access"><a class="dropdown-item" href="<?php echo app_url("modules/contract/contracts/add");?>"><div class="sprite sprite-contract"></div><?php     echo $this->lang->line("upload_contract");    ?></a></div>
                                            <div class="core-access"><a class="dropdown-item" href="javascript:;" onclick="contractGenerate('choose','mou');"><div class="sprite fas fa-file-signature"></div><?php     echo $this->lang->line("add_mou");    ?></a></div>
                                            <div class="dropdown-divider core-access"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="core-access"><a class="dropdown-item" href="javascript:;" onclick="intellectualPropertyAddForm();"><div class="sprite sprite-intellectual-property"></div><?php     echo $this->lang->line("IP");    ?></a></div>
                                            <div class="dropdown-divider core-access"></div>
                                            <div class="core-access"><a class="dropdown-item" href="javascript:;" onclick="loadCorrespondenceForm();"><div  class="sprite fas fa-file-invoice"></div><?php echo $this->lang->line("correspondence"); ?></a></div>
                                            <div class="dropdown-divider core-access"></div>
                                            <div class="core-access"><a class="dropdown-item" href="javascript:;" onclick="conveyancingAddForm();"><div  class="sprite fas fa-stamp"></div><?php echo $this->lang->line("conveyancing"); ?></a></div>
                                            <div class="dropdown-divider core-access"></div>
                                            <div class="core-access"><a class="dropdown-item" href="javascript:;" onclick="opinionAddForm();"><div  class="sprite fa-solid fa-gavel"></div><?php echo $this->lang->line("legal_opinion");     ?></a></div>
                                            <div class="dropdown-divider"></div>
                                            <div id="top-menu-add-button-task"><a class="dropdown-item" href="javascript:;" onclick="taskAddForm();"><div class="sprite sprite-task"></div><?php  echo $this->lang->line("new_task");  ?></a></div>
                                            <?php if($timeFeatureEnabled){?><div><a href="javascript:;" class="dropdown-item" onclick="logActivityDialog();"><div class="sprite sprite-log-time"></div><?php   echo $this->lang->line("log_time"); ?></a></div><?php }?>
                                            <div class="core-access"><a class="dropdown-item" href="<?php echo app_url("modules/money/vouchers/expense_add");?>"><div class="sprite sprite-expense"></div><?php echo $this->lang->line("expense");   ?></a></div>
                                            <div><a class="dropdown-item" href="javascript:;" onclick="meetingForm();"><div class="sprite sprite-meeting"></div><?php  echo $this->lang->line("meeting");?></a></div>
                                            <div><a class="dropdown-item" href="javascript:;" onclick="reminderForm();"><div class="sprite sprite-reminder"></div><?php echo $this->lang->line("new_reminder");?></a></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="d-flex flex-end-item">
                            <form class="d-flex">
                                <div id="quick-search" class="navbar-form1 navbar-left no-padding margin-top-5">
                                    <div class="col-md-12 col-xs-12 no-padding">
                                        <div class="col-md-11 col-xs-11 no-padding" id="search-input">
                                            <div class="has-feedback position-relative">
                                                <label for="search" class="sr-only">Search</label><?php  echo form_input(["placeholder" => $this->lang->line("universal_search"), "id" => "universal_search", "name" => "universal_search", "class" => "form-control form-control-override", "onfocus" => "universalSearchFocus(jQuery(this), event.keyCode);", "onkeypress" => "return false;"]);?>
                                                <i class="fa fa-search search-universal-search-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="navbar-right no-padding-right">
                                <ul class="nav navbar-nav icons-list"><?php $system_preferences = $this->session->userdata("systemPreferences");?>
                                    <li class="nav-item dropdown">
                                        <a rel="popover" tabindex="-1" href="calendars/view">
                                            <i class="fa fa-calendar-days"></i>
                                        </a>
                                    </li><?php    $totalRemindersNotifications = $this->totalRemindersNotifications;
                                    $pendingNotifications = $totalRemindersNotifications["pendingNotifications"];
                                    $pendingReminders = $totalRemindersNotifications["pendingReminders"];
                                    ?>
                                    <li class="nav-item dropdown">
                                        <a href="javascript:" rel="popover">
                                            <i class="fa fa-comment-alt"></i>
                                            <span id="pendingNotifications" style="display:<?php echo $pendingNotifications < 1 ? " none" : " inline-block";?>;"> <?php    echo 1 <= $pendingNotifications ? $pendingNotifications : "";?> </span>
                                        </a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a href="javascript:" rel="popover">
                                            <i class="fa fa-bell"></i>
                                            <span id="pendingReminders" style="display:<?php echo $pendingReminders < 1 ? " none" : " inline-block";?>"><?php  echo 1 <= $pendingReminders ? $pendingReminders : "";?>   </span>
                                        </a>
                                    </li>
                                  <?php if ($timeFeatureEnabled){?>
                                    <li class="nav-item dropdown" id="timer-button-action-wrapper">
                                        <a id="timer-button-action" href="javascript:;" onclick="timerList();" class="timer-up" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-clock header-timer-icon <?php    echo $this->userActivityLogTimer ? "timer-active-icon" : "";    ?>"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right dropdown-menu-timer">
                                            <div class="loading">&nbsp;</div>
                                        </ul>
                                    </li><?php }
                                  ?>
                                    <?php  if (ENVIRONMENT === "production") {?>
                                        <li id="support-button-action-wrapper" class="nav-item dropdown">
                                        <a href="javascript:;" onclick="customerSupport();" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa-solid fa-bullhorn"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-customer-support" <?php echo $this->session->userdata("AUTH_language") == "arabic" ? 'style="margin-right: -480px;"' : 'style="margin-left: -480px;left:auto;"';?>>
                                            <iframe id="customer-support" title="Customer Support" width="500" height="450" frameBorder="0"></iframe>
                                        </ul>
                                        </li><?php    }?>
                                    <li class="nav-item dropdown">
                                        <a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  <i class="fa fa-cog"></i>   </a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" style="margin: 0 -100px;">
                                            <li><a tabindex="-1" class="dropdown-item"  href="<?php    echo app_url("dashboard/admin");   ?>"><?php  echo $this->lang->line("system_settings");?></a></li>
                                            <?php   if ($this->cloud_installation_type && $this->instance_client_type === "lead") {?>
                                                <li><a tabindex="-1" href="javascript:;" class="dropdown-item"           onClick="subscribe();"><?php  echo $this->lang->line("subscribe_now");  ?></a></li>
                                            <?php     }
                                            if ($this->instance_subscription) {?>
                                                <li><a tabindex="-1" class="dropdown-item" href="<?php  echo app_url("subscription/details");?>"><?php echo $this->lang->line("subscription");      ?></a> </li><?php
                                            } else {
                                                if ($this->cloud_installation_type && $this->instance_client_type !== "lead") {?>
                                                    <li><a tabindex="-1" class="dropdown-item" href="license_manager/install"><?php  echo $this->lang->line("subscription");     ?></a>      </li>
                                                <?php    }                                        }?>
                                            <li class="dropdown-divider"></li>
                                            <li><a tabindex="-1" class="dropdown-item"  href="<?php    echo app_url("users");   ?>"><?php   echo $this->lang->line("manage_users");   ?></a> </li>
                                            <li><a tabindex="-1" class="dropdown-item" href="javascript:;" onclick="addUserDiablog()"><?php    echo $this->lang->line("add_user");  ?></a> </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown" id="tour_Administration">
                                        <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-image-override dropdown-toggle" href="#" id="loggedinusertag" username="<?php echo $this->is_auth->get_user_name();?>"><span class="showOnMobileText"></span> <img width="40px" height="38px" title="<?php echo $this->session->userdata("AUTH_userFirstName"); ?>" class="border-white header-image" src="<?php  echo $this->session->userdata("AUTH_user_profilePicture") != "" && $this->session->userdata("AUTH_user_profilePicture") != NULL ? BASEURL . "users/get_profile_picture/" . $this->session->userdata("AUTH_user_id") . "/1" : "assets/images/icons/avatar.png";  ?>"> </a>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="navbarDropdown">
                                            <li><a class="dropdown-item" tabindex="-1" href="<?php  echo app_url("users/profile"); ?>"><?php echo $this->lang->line("my_profile");   ?></a> </li>
                                            <?php   if (!$this->session->userdata("AUTH_isAd") && !$this->session->userdata("a4l_sso_login")) {?>
                                                <li><a class="dropdown-item" tabindex="-1" href="<?php         echo app_url("users/change_password"); ?>"><?php echo $this->lang->line("change_password"); ?></a></li>
                                            <?php   }?>

                                            <li class="dropdown-divider"></li>
                                            <?php    if ($this->cloud_installation_type && $this->session->userdata("show_getting_started"))
                                            {?>
                                                <li><a class="dropdown-item" tabindex="-1" href="<?php echo app_url("dashboard/getting_started"); ?>"><?php echo $this->lang->line("getting_started");  ?></a> </li>
                                                <?php
                                            }
                                            if ($system_preferences["hijriCalendarConverter"]) {
                                                ?>
                                                <li><a class="dropdown-item" tabindex="-1" href="javascript:" onclick="HijriConverter();"><?php  echo $this->lang->line("hijri_date_converter");  ?></a> </li>
                                            <?php     }
                                            if ($this->cloud_installation_type && $this->instance_client_type === "lead") {
                                                ?>
                                                <li><a class="dropdown-item" tabindex="-1" href="javascript:" onClick="subscribe();"><?php echo $this->lang->line("subscribe_now");  ?></a> </li>
                                            <?php    }    if ($this->instance_subscription) {?>
                                                <li><a class="dropdown-item" tabindex="-1"    href="<?php      echo app_url("subscription/details");  ?>"><?php  echo $this->lang->line("subscription"); ?></a>      </li>
                                            <?php     } else {
                                                if ($this->cloud_installation_type && $this->instance_client_type !== "lead") {
                                                    ?>
                                                    <li><a class="dropdown-item" tabindex="-1" href="license_manager/install"><?php      echo $this->lang->line("subscription");      ?></a>   </li>
                                                <?php       }    }?>
                                            <li><a class="dropdown-item" tabindex="-1" href="https://collaboration.sheria360.com/servicedesk/customer/portal/4"   target="_blank"><?php  echo $this->lang->line("get_help");?></a> </li>
                                            <li><a class="dropdown-item" tabindex="-1" href="javascript:;"      onclick="userGuideObject.userGuideSetup(true)"><?php  echo $this->lang->line("user_guide");?></a> </li>
                                            <li><a class="dropdown-item" tabindex="-1" href="javascript:;"   onclick="userGuideObject.manageWalkthrough()"><?php echo $this->lang->line("manage_walkthrough");?></a></li>
                                            <?php if (!$this->session->userdata("a4l_sso_authentication") || $this->session->userdata("a4l_sso_login") && $this->session->userdata("is_cloud")) {?>
                                                <li class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" tabindex="-1" href="<?php echo app_url("users/logout/");?>"><?php  echo $this->lang->line("sign_out");  ?></a> </li>
                                            <?php   }?>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    <?php }    else {
        if ($this->app_main_logo_name && $this->instance_data_array["installationType"] == "on-server") {?>
            <nav class="navbar navbar-default">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header col-md-12 login-header"> <img src="<?php   echo $this->app_main_logo_name;?>" class="pull-left login-page-second-logo">                    </div>
            </nav><?php    }    }?> <!-- Begin page content -->
    <div <?php echo $this->is_auth->is_logged_in() ? 'class="container background-style col-md-12 no-padding container-default-height" style="margin-top:56px;"' : 'class="sign-in-container col-md-12 no-padding" style="margin-top:40px;"';?>>
        <?php $this->activeInstalledModules = isset($this->licensor) ? $this->licensor->get_installed_modules() : [];
        $cp_valid_license = $this->licensor->check_license_date("customer-portal");
        if (strcmp($this->licensor->get_license_message(), "") && ($this->session->userdata("AUTH_access_type") == "core" || $this->session->userdata("AUTH_access_type") == "both") || strcmp($this->licensor->get_license_message("contract"), "") && ($this->session->userdata("AUTH_access_type") == "contract" || $this->session->userdata("AUTH_access_type") == "both") || strcmp($this->licensor->get_license_message("customer-portal"), "")) {
            $inline_style = $this->currentTopNavItem == "pages" && $this->is_auth->stay_signed_in() == "yes" ? "style='padding-top:25px;'" : "";
            if ($this->is_auth->is_logged_in() && $this->session->userdata("license_flag") == 1 && $this->is_auth->check_uri_permissions("/dashboard/", "/dashboard/management/", "core", true, true)) { ?>
                <div <?php echo $inline_style; ?> class="centered-text lisence-message"><?php if ($this->license_package == "core_contract") { ?>
                    <span class="font-16"> <?php echo $this->licensor->get_license_message("core");?></span><?php if (strcmp($this->licensor->get_license_message("core"), "") && strcmp($this->licensor->get_license_message("contract"), "")) { ?>
                        </br>  <?php
                    } ?>
                    <span class="font-16"> <?php echo $this->licensor->get_license_message("contract");?></span> <?php
                } else {?>
                    <span class="font-16"><?php   echo $this->licensor->get_license_message($this->license_package);?></span><?php
                } if ((strcmp($this->licensor->get_license_message("core"), "") || strcmp($this->licensor->get_license_message("contract"), "")) && strcmp($this->licensor->get_license_message("customer-portal"), "")) { ?>
                    </br>             <?php
                }?>
                <span class="font-16"> <?php echo $this->licensor->get_license_message("customer-portal");?></span>
                <?php $allow_exit = true;
                if ($this->cloud_installation_type) {
                    switch ($this->instance_client_type) {
                        case "lead":
                            if ($this->licensor->expiration_days <= 0) {
                                $allow_exit = false; ?>
                                <a href="javascript:" onClick="subscribe();" class="btn license-button"><?php echo $this->lang->line("subscribe_now");?></a>
                                <a href="<?php  echo $this->config->item("sheria360_website_feedback_page");?>"  class="btn btn-link"><?php     echo $this->lang->line("contact_us");    ?></a>
                                <?php
                            } else {
                                if ($this->licensor->expiration_days <= 10) { ?>
                                    <a href="javascript:" onClick="subscribe();"  class="btn license-button"><?php echo $this->lang->line("subscribe_now"); ?></a>
                                    <?php if ($this->licensor->expiration_days === 1) { ?>
                                        <a href="<?php echo $this->config->item("sheria360_website_feedback_page");    ?>" class="btn btn-link"><?php  echo $this->lang->line("request_extension"); ?></a>
                                    <?php }
                                }
                            }
                            break;
                        case "customer":?>
                            <a href="javascript:" onclick="upgradeSubscription();"  class="btn license-button"><?php echo $this->lang->line("renew"); ?></a>
                            <?php
                            break;
                        default: ?>
                            <a href="<?php   echo $this->config->item("sheria360_website_feedback_page");  ?>" class="btn license-button"><?php   echo $this->lang->line("contact_us");?></a>
                        <?php }
                } else { ?>
                    <a href="<?php   echo $this->config->item("sheria360_website_feedback_page");   ?>"   class="btn license-button"><?php echo $this->lang->line("contact_us"); ?></a>
                <?php }
                if ($allow_exit) {    ?>
                    <a href="javascript:" class="btn pull-right" onclick="hideLicenseMessage();"><i class="far fa-times-circle font-18 purple_color"></i></a>
                <?php } ?>
                </div><?php
            }
        } ?>
        <div class="col-md-12 col-xs-12 margin-bottom money-container-override" id="main-container">
