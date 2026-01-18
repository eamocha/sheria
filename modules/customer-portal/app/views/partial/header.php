<?php
$customer_portal_exists = !empty($this->instance_data_array["customer_portal_logo"]) && file_exists(substr(COREPATH, 0, -12) . $this->instance_data_array["customer_portal_logo"]);
$licenses_validity = $this->session->userdata("licenses_validity");
$this->customer_portal_users->fetch($this->session->userdata("CP_user_id"));
$license_type = $this->customer_portal_users->get_field("type");
$cp_valid = $this->session->userdata("CP_logged_in") ? $this->licensor->check_license_date("customer-portal") : false;
$contract_valid = $this->session->userdata("CP_logged_in") ? $this->licensor->check_license_date("contract") : false;
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->pageTitle; ?></title>
    <base href="<?php echo BASEURL; ?>" />
    <meta name="viewport" content="width=device-width ,user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo BASEURL; ?>files/images/instance/main_default_theme/favicon.ico" />
    <link href="assets/bootstrap/css/bootstrap4.6.1.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/jquery/css/validationEngine.css" rel="stylesheet" type="text/css" />
    <link href="assets/customerPortal/clientPortal/css/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/customerPortal/clientPortal/css/main.css" rel="stylesheet" type="text/css" />
    <link href="assets/styles/common_main.css" rel="stylesheet" type="text/css" />
    <link href="assets/jquery/css/animate.css" rel="stylesheet" type="text/css" />
    <link href="assets/typeahead/css/typeahead.css" rel="stylesheet" type="text/css" />
    <link href="assets/bootstrap/select/css/bootstrap-select1.13.18.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/bootstrap/date-picker/css/bootstrap-datepicker1.9.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/jquery/css/jquery.timepicker.css" rel="stylesheet" type="text/css" />
    <link href="assets/fontawesome/css/all.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/bootstrap/datepicker/moment-with-locales.js"></script>
    <script src="assets/hijri/moment.min.js"></script>
    <script src="assets/jquery/jquery-3.6.0.min.js"></script>
    <script src="assets/jquery/jquery-migrate-3.3.2.min.js"></script>
    <script src="assets/compressedScripts/libraries1.min.js"></script>
    <script src="assets/jquery/jquery.tooltipster.min.js"></script>
    <link href="assets/jquery/css/tooltipster.css" rel="stylesheet" type="text/css" />
    <script src="assets/bootstrap/js/bootstrap4.6.1.bundle.min.js"></script>
    <script src="assets/jquery/jquery.noty.packaged.min.js"></script>
    <script src="assets/jquery/validationEngine.js"></script>
    <script src="assets/jquery/languages/validationEngine-english.js"></script>
    <script src="assets/scripts/lang.english.js"></script>
    <script src="assets/customerPortal/clientPortal/js/main.js"></script>
    <script src="assets/jquery/jquery.scrollTo.min.js"></script>
    <script src="assets/customerPortal/clientPortal/js/datatables.min.js"></script>
    <script src="assets/customerPortal/clientPortal/js/jquery.dataTables.min.js"></script>
    <script src="assets/customerPortal/clientPortal/js/dataTables.bootstrap.min.js"></script>
    <script src="assets/scripts/helpers.min.js"></script>
    <script src="assets/scripts/common.min.js"></script>
    <script src="assets/scripts/documents_general.js"></script>
    <script src="compressed_asset/pnotify_feedback.js"></script>
    <script src="assets/typeahead/typeahead.bundle.js"></script>
    <script src="assets/hijri/moment-hijri.js"></script>
    <script src="assets/bootstrap/date-picker/bootstrap-datepicker.js"></script>
    <script src="assets/bootstrap/date-picker/bootstrap-datepicker1.9.min.js"></script>
    <script src="assets/bootstrap/date-picker/bootstrap-datepicker.english.js"></script>
    <script src="assets/bootstrap/select/bootstrap-select1.13.18.min.js"></script>
    <script src="assets/fontawesome/js/all.min.js"></script>
    <script src="assets/jquery/datepair/datepair.js"></script>
    <script src="assets/jquery/datepair/jquery.datepair.js"></script>
    <script src="assets/jquery/jquery.timepicker.min.js"></script>
    <script type="text/javascript">
        //initializing bootstrapDP to be used instead of datepicker to avoid the conflict that occurs between jquery and bootstrap
        var datepicker = jQuery.fn.datepicker.noConflict();
        jQuery.fn.bootstrapDP = datepicker;
    </script>
    <script language="javascript" type="text/javascript">
        var style = document.createElement("style");
        var planExcludedFeatures = '<?php echo $this->plan_excluded_features; ?>';
        var planFeatureWarningMsgs = '<?php echo json_encode($this->plan_feature_warning_msgs); ?>';
        var systemPreferenceContact = '<?php echo $this->system_preference->get_key_groups()["CustomerPortalConfig"]["AllowAddContacts"]; ?>';
        style.type = "text/css";
        style.id = "antiClickjack";
        if ("cssText" in style) {
            style.cssText = "body{display:none !important;}";
        } else {
            style.innerHTML = "body{display:none !important;}";
        }
        document.head.appendChild(style);
        if (self === top) {
            var antiClickjack = document.getElementById("antiClickjack");
            antiClickjack.parentNode.removeChild(antiClickjack);
        } else {
            top.location = self.location;
        }
    </script>
    <?php print_css($this->css); ?>
    <?php print_js($this->js); ?>
    <link href="<?php echo BASEURL; ?>files/app_themes/<?php echo $this->instance_data_array["app_theme"]; ?>/<?php echo $this->instance_data_array["app_theme"]; ?>_customer_portal.css?v=<?php echo $this->instance_data_array["app_theme_version"]; ?>" rel="stylesheet">
</head>
<body>
<div id="loader-global">
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
<div class="row-fluid">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- Brand and toggle get grouped for better mobile display -->
        <?php if (!$this->session->userdata("CP_logged_in")) { ?>
            <?php if ($customer_portal_exists) { ?>
                <img src="<?php echo $this->instance_data_array["customer_portal_logo"]; ?>" width="60px" class="float-left login-page-second-logo img-fluid" />
            <?php } ?>
            <div class="col-md-4 cp-title">
                <h3><?php echo $this->session->userdata("cpAppTitle"); ?></h3>
            </div>
        <?php } else { ?>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <a class="navbar-brand<?php echo $customer_portal_exists ? " with-logo" : "60px"; ?>" href="<?php echo $licenses_validity["client"] && ($license_type == "client" || $license_type == "both") ? site_url("home") : site_url("contracts"); ?>" <?php echo $customer_portal_exists ? "style=\"background-image: url('" . $this->instance_data_array["customer_portal_logo"] . "');\"" : ""; ?>>
                    <?php if (!$customer_portal_exists) { ?>
                        <?php echo $this->session->userdata("cpAppTitle"); ?>
                    <?php } ?>
                </a>
                <ul class="navbar-nav mr-auto">
                    <?php if ($licenses_validity["client"] && $cp_valid && ($license_type == "client" || $license_type == "both")) { ?>
                        <li class="active nav-item" id="cp-tab-home">
                            <a class="nav-link" href="<?php echo site_url("home"); ?>">
                                <?php echo $this->lang->line("home"); ?>
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item" id="cp-tab-tickets">
                            <a class="nav-link" href="<?php echo site_url("tickets"); ?>">
                                <?php echo $this->Config["cpFormLabel"]; ?>
                            </a>
                        </li>
                    <?php } ?>
                    <?php /* if ($licenses_validity["client"] && $cp_valid && ($license_type == "client" || $license_type == "both")) { ?>
                            <li class="nav-item" id="cp-tab-containers">
                                <a class="nav-link" href="<?php echo site_url("containers"); ?>">
                                    <?php echo $this->Config["cp_container_form_label"]; ?>
                                </a>
                            </li>
                        <?php } */ ?>
                    <?php if ($licenses_validity["collaborator"] && $contract_valid) { ?>
                        <li class="nav-item" id="cp-tab-contracts">
                            <a class="nav-link" href="<?php echo site_url("contracts"); ?>">
                                <?php //echo $this->Config["cp_contract_form_label"];
                                echo $this->lang->line("contracts_in_menu"); ?>
                            </a>
                        </li>
                    <?php } ?>
                    <?php  if ($licenses_validity["collaborator"] && $contract_valid) { ?>
                            <li class="nav-item" id="cp-tab-contracts">
                                <a class="nav-link" href="<?php echo site_url("conveyancing"); ?>">
                                    <?php echo $this->lang->line("conveyancing"); ?>
                                </a>
                            </li>
                        <?php }  ?>
                    <?php if ($licenses_validity["collaborator"] && $contract_valid) //&& $this->Config["enable_cplegal_opinions"] == "yes")
                    { ?>
                        <li class="nav-item" id="cp-tab-legal-opinions">
                            <a class="nav-link" href="<?php echo site_url("legal_opinions"); ?>">
                                <?php echo $this->lang->line("legal_opinions"); ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <div class="btn-group float-right" style="padding-top: 10px;">
                        <button type="button" class="btn dropdown-toggle bg-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $this->session->userdata("CP_profileName"); ?> <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if ($this->session->userdata("CP_isAd") != 1 && !$this->session->userdata("CP_sso_login")) { ?>
                                <a class="dropdown-item" href="<?php echo site_url("users/changePassword"); ?>">
                                    <?php echo $this->lang->line("change_password"); ?>
                                </a>
                            <?php } ?>
                            <?php if ($this->session->userdata("CP_isAd") != 1) { ?>
                                <a class="dropdown-item" href="<?php echo site_url("users/signature"); ?>">
                                    <?php echo $this->lang->line("signature"); ?>
                                </a>
                            <?php } ?>
                            <a class="dropdown-item" href="<?php echo $this->config->item("cp_help_url"); ?>" target="_blank">
                                <?php echo $this->lang->line("help"); ?>
                            </a>
                            <?php if (!$this->session->userdata("cp_sso_authentication")) { ?>
                                <a class="dropdown-item" href="<?php echo site_url("users/signout"); ?>">
                                    <?php echo $this->lang->line("sign_out"); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
    </nav>
    <script type="text/javascript">
        function aboutPortal() {
            alert('App4Legal Customer Portal');
        }
    </script>
</div>
</body>
</html>