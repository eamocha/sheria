
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <?PHP echo form_open("users/login", "name=\"loginForm\" id=\"loginForm\" method=\"post\" class=\"needs-validation\" novalidate"); ?>
        <div class="text-center mb-4">
            <img id="image" alt="Sheria360" src="assets/images/a4l_logo_sign_in.png" class="img-fluid" style="max-height: 80px;">
        </div>

        <?php
        echo form_input(["name" => "hashPart", "id" => "hashPart", "type" => "hidden"]);
        echo form_input("redirect_to", !empty($redirect_to) ? $redirect_to : "", "class=\"d-none\"");
        $error = $this->is_auth->get_auth_error();
        $usernameError = form_error("username", "<span class=\"help-inline\">", "</span>");
        $passwordError = form_error("password", "<span class=\"help-inline\">", "</span>");
        ?>

        <div class="form-group mb-3">
            <?php
            $username_input_attributes = ["name" => "username", "id" => "username", "placeholder" => $this->lang->line("user_login"), "class" => "form-control form-control-lg"];
            if (!(isset($error) && $error) && !isset($_GET["email"])) {
                $username_input_attributes["autofocus"] = true;
            }
            echo form_input($username_input_attributes);
            ?>
            <?php echo !empty($usernameError) ? "<div class=\"invalid-feedback d-block\">" . $this->lang->line("user_login_is_required") . "</div>" : "";?>
        </div>

        <div class="form-group mb-3">
            <?php
            $password_input_attributes = ["name" => "password", "autocomplete" => "stop", "placeholder" => $this->lang->line("password"), "id" => "password", "onkeypress" => "capLock(event)", "class" => "form-control form-control-lg"];
            if (isset($_GET["email"]) && $_GET["email"]) {
                $password_input_attributes["autofocus"] = true;
            }
            if (isset($error) && $error) {
                $password_input_attributes["autofocus"] = true;
            }
            echo form_password($password_input_attributes);
            ?>
            <?php echo !empty($passwordError) ? "<div class=\"invalid-feedback d-block\">" . $this->lang->line("password_is_required") . "</div>" : ""; ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php if ($this->is_auth->stay_signed_in() == "yes") { ?>
                <div class="form-check">
                    <input class="form-check-input" name="remember" id="remember" type="checkbox" autocomplete="stop" />
                    <label class="form-check-label" for="remember">
                        <?php echo $this->lang->line("stay_signed_in"); ?>
                    </label>
                </div>
            <?php } ?>
            <div>
                <?php if ($this->cloud_installation_type && $forgot_password_url) { ?>
                    <a href="<?php echo $forgot_password_url; ?>" class="text-decoration-none">
                        <?php echo $this->lang->line("forgot_my_password"); ?>
                    </a>
                <?php } else { ?>
                    <a href="javascript:void(0);" onclick="forgotPassword();" class="text-decoration-none">
                        <?php echo $this->lang->line("forgot_my_password"); ?>
                    </a>
                <?php } ?>
            </div>
        </div>

        <div id="forgotPasswordMessage" class="alert d-none mt-3" role="alert">
        </div>

        <div class="mb-4">
            <button class="btn btn-primary btn-lg btn-block" type="submit" name="login">
                <?php echo $this->lang->line("sign_in"); ?>
            </button>
        </div>

        <?php if ($idp_enabled) { ?>
            <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1">
                <span class="mx-3 small text-muted">OR</span>
                <hr class="flex-grow-1">
            </div>
            <div class="d-grid gap-2 mb-4">
                <a id="login-with-azure" class="btn btn-outline-secondary btn-lg btn-block" href="<?php echo site_url("users/login_idps/azure_ad") ?>">
                    <i class="fa-brands fa-microsoft me-2"></i> <?php echo $this->lang->line("login_using_azure"); ?>
                </a>
                <a id="login-with-onelogin" class="btn btn-outline-secondary btn-lg btn-block" href="<?php echo site_url("users/login_idps/onelogin"); ?>">
                    <i class="fas fa-key me-2"></i> <?php echo $this->lang->line("login_using_onelogin"); ?>
                </a>
            </div>
        <?php } ?>

        <?php if (isset($response_message) && $response_message || (isset($error) && $error) || (isset($user_banned) && $user_banned)) { ?>
            <div class="alert <?php echo isset($response_message["success"]) && $response_message["success"] ? "alert-success" : "alert-danger"; ?> mt-4" role="alert">
                <?php
                if (isset($response_message) && $response_message) {
                    echo isset($response_message["success"]) ? $response_message["success"] : $response_message["error"];
                } else {
                    echo isset($error) && $error ? $error : (isset($user_banned) && $user_banned ? $this->lang->line("user_banned") : "");
                }
                ?>
            </div>
        <?php } ?>

        <?php if (isset($warningMessageOnLoginPage) && !empty($warningMessageOnLoginPage)) { ?>
            <div class="alert alert-warning text-center mt-4" role="alert">
                <?php echo $warningMessageOnLoginPage; ?>
            </div>
        <?php } ?>
        <?php echo form_close(); ?>

        <div class="text-center text-muted small mt-4 pt-3 border-top">
            <span>
                <?php //echo $this->lang->line("footer_all_rights_reserved"); echo " &copy; " . date("Y") . " - Sheria360";
                ?>
            </span>
        </div>
    </div>
</div>
<script language="Javascript">

    var redirectUrl = document.location.href;
    var urlHash = redirectUrl.split("#")[1];
    jQuery('#hashPart').val(urlHash);

    function capLock(e) {
        var data = jQuery('#password').val();
        if (data.length === 0) {
            kc = e.keyCode ? e.keyCode : e.which;
            sk = e.shiftKey ? e.shiftKey : ((kc == 16) ? true : false);
            if (((kc >= 65 && kc <= 90) && !sk) || ((kc >= 97 && kc <= 122) && sk))
                pinesMessage({
                    ty: 'warning',
                    m: _lang.feedback_messages.capsLockOn
                });
        } else if (e.which == 13) {
            jQuery('#loginForm').submit();
        }
    }

    /*
     * Forget Password Actions
     * On click on forget password link will send the valid email address of the login input a reset password email(if the email is not banned neither an AD user)
     * An inline message will be displayed for response messages according to different conditions
     * */
    function forgotPassword() {
        var username = jQuery('#username').val();
        if (!username) {
            if (jQuery('.success-msg').length > 0) {
                jQuery('.success-msg').text('');
                jQuery('.form-error', '#loginForm').removeClass('success-msg').addClass('response-msg');
            }
            jQuery('.response-msg').text(_lang.feedback_messages.missingUserLogin);
        } else {
            jQuery.ajax({
                url: getBaseURL() + 'pages/reset_password',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    username: username
                },
                beforeSend: function () {
                    jQuery("#loader-global").show();
                    if (jQuery('.success-msg').length > 0) {
                        jQuery('.success-msg').text('');
                        jQuery('.form-error', '#loginForm').removeClass('success-msg').addClass('response-msg');
                    } else {
                        jQuery('.response-msg').text('');
                    }
                    jQuery('.login-form-error-message-span').text('');
                },
                success: function (response) {
                    jQuery("#loader-global").hide();
                    if (response.isAdUser) { //active directory user
                        jQuery('.response-msg').text(_lang.feedback_messages.ADUserCantChangePass).sprintf(_lang.feedback_messages.activeDirectory);
                    } else if (response.isIdpUser) { //banned user
                        jQuery('.response-msg').text(_lang.feedback_messages.ADUserCantChangePass).sprintf(_lang.feedback_messages.azureAd);
                    } else if (response.bannedUser) { //banned user
                        jQuery('.response-msg').text(_lang.feedback_messages.bannedUserResetPass);
                    } else if (response.unAvailableUserLogin) { //user login is not available
                        jQuery('.response-msg').text(_lang.feedback_messages.unAvailableUserLogin);
                    } else if (!response.emailSent) { //email not sent
                        jQuery('.response-msg').text(_lang.feedback_messages.emailNotSent);
                    } else if (response.emailSent) { //email sent
                        jQuery('.response-msg').text(_lang.feedback_messages.resetPasslEmailSent).addClass('success-msg');
                    }
                }
            });
        }
    }
    jQuery(document).ready(function () {
        logoPosition();
        jQuery("form#loginForm").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        jQuery(window).resize(function () {
            jQuery("#image").width(jQuery(".logo").width());
            logoPosition();

        });
    });
</script>