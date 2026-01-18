<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
        <?PHP echo form_open("users/login", "name=\"loginForm\" id=\"loginForm\" method=\"post\" class=\"user-centered-form\""); ?>
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
            $username_input_attributes = ["name" => "username", "id" => "username", "placeholder" => $this->lang->line("user_login"), "class" => "form-control form-control-lg", "value" => $_GET["email"] ?? set_value("username")];
            if (!(isset($error) && $error) && !isset($_GET["email"])) {
                $username_input_attributes["autofocus"] = true;
            }
            echo form_input($username_input_attributes);
            ?>
        </div>
        <?php echo !empty($usernameError) ? "<div class=\"text-danger small mb-3\">" . $this->lang->line("user_login_is_required") . "</div>" : "";?>

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
        </div>
        <?php
        echo !empty($passwordError) ? "<div class=\"text-danger small mb-3\">" . $this->lang->line("password_is_required") . "</div>" : "";
        ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php
            if ($this->is_auth->stay_signed_in() == "yes") {
                ?>
                <div class="form-check">
                    <input class="form-check-input" name="remember" id="remember" type="checkbox" autocomplete="stop" />
                    <label class="form-check-label" for="remember">
                        <?php echo $this->lang->line("stay_signed_in"); ?>
                    </label>
                </div>
                <?php
            }
            ?>
            <div>
                <?php
                if ($this->cloud_installation_type && $forgot_password_url) { ?>
                    <a href="<?php echo $forgot_password_url; ?> " class="text-decoration-none">
                        <?php echo $this->lang->line("forgot_my_password"); ?>
                    </a>
                    <?php
                } else {
                    ?>
                    <a href="javascript:void(0);" onclick="forgotPassword();" class="text-decoration-none">
                        <?php echo $this->lang->line("forgot_my_password"); ?>
                    </a>
                <?php } ?>
            </div>
        </div>

        <div id="generalMessageContainer" class="alert mt-3 mb-4" role="alert" style="display: none;">
        </div>

        <div class="mb-4">
            <button class="btn btn-primary btn-lg btn-block" type="submit" name="login"><?php echo $this->lang->line("sign_in"); ?></button>
        </div>

        <?php
        if ($idp_enabled) {
            ?>
            <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1"><span class="mx-3 small text-muted">OR</span><hr class="flex-grow-1">
            </div>
            <div class="d-grid gap-2 mb-4">
                <a id="login-with-other-idps" class="btn btn-outline-secondary btn-lg btn-block" href="<?php echo site_url("users/login_idps/azure_ad") ?>">
                    <i class="fa-brands fa-microsoft me-2"></i> <?php echo $this->lang->line("login_using_azure"); ?>
                </a>
                <a id="login-with-other-idps" class="btn btn-outline-secondary btn-lg btn-block" href="<?php echo site_url("users/login_idps/onelogin"); ?>">
                    <i class="fas fa-key me-2"></i> <?php echo $this->lang->line("login_using_onelogin"); ?>
                </a>
            </div>
        <?php } ?>

        <?php

        if (isset($response_message) && $response_message || (isset($error) && $error) || (isset($user_banned) && $user_banned)) {
            $alertClass = (isset($response_message["success"]) && $response_message["success"]) ? "alert-success" : "alert-danger";
            $messageContent = "";
            if (isset($response_message) && $response_message) {
                $messageContent = isset($response_message["success"]) ? $response_message["success"] : $response_message["error"];
            } else {
                $messageContent = isset($error) && $error ? $error : (isset($user_banned) && $user_banned ? $this->lang->line("user_banned") : "");
            }
            echo "<script>";
            echo "document.addEventListener('DOMContentLoaded', function() {";
            echo "    var msgContainer = document.getElementById('generalMessageContainer');";
            echo "    msgContainer.classList.add('" . $alertClass . "');";
            echo "    msgContainer.innerHTML = '" . addslashes($messageContent) . "';";
            echo "    msgContainer.style.display = 'block';";
            echo "});";
            echo "</script>";
        }
        ?>

        <?php
        if (isset($warningMessageOnLoginPage) && !empty($warningMessageOnLoginPage)) { ?>
            <div class="alert alert-warning text-center mt-4" role="alert">
                <?php echo $warningMessageOnLoginPage; ?>
            </div>
            <?php
        }
        echo form_close();
        ?>
        <div class="text-center text-muted small mt-4 pt-3 border-top">
            <span>
                <?php echo $this->lang->line("footer_all_rights_reserved"); echo " &copy; " . date("Y") . " - Sheria360";
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
            if (((kc >= 65 && kc <= 90) && !sk) || ((kc >= 97 && kc <= 122) && sk)) {
                 pinesMessage({
                    ty: 'warning',
                    m: _lang.feedback_messages.capsLockOn
                });
            }
        } else if (e.which == 13) {
            jQuery('#loginForm').submit();
        }
    }


    function forgotPassword() {
        var username = jQuery('#username').val();
        var messageContainer = jQuery('#generalMessageContainer');


        messageContainer.text('');
        messageContainer.removeClass('alert-success alert-danger alert-warning').hide(); // Remove alert types and hide

        if (!username) {

            messageContainer.addClass('alert-danger').text(_lang.feedback_messages.missingUserLogin).show();
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
                    messageContainer.text('').removeClass('alert-success alert-danger alert-warning').hide();
                    jQuery('.login-form-error-message-span').text('');
                },
                success: function (response) {
                    jQuery("#loader-global").hide();
                    if (response.isAdUser) {
                        messageContainer.addClass('alert-danger').text(_lang.feedback_messages.ADUserCantChangePass + _lang.feedback_messages.activeDirectory).show();
                    } else if (response.isIdpUser) {
                        messageContainer.addClass('alert-danger').text(_lang.feedback_messages.ADUserCantChangePass + _lang.feedback_messages.azureAd).show();
                    } else if (response.bannedUser) {
                        messageContainer.addClass('alert-danger').text(_lang.feedback_messages.bannedUserResetPass).show();
                    } else if (response.unAvailableUserLogin) {
                        messageContainer.addClass('alert-danger').text(_lang.feedback_messages.unAvailableUserLogin).show();
                    } else if (!response.emailSent) {
                        messageContainer.addClass('alert-danger').text(_lang.feedback_messages.emailNotSent).show();
                    } else if (response.emailSent) {
                        messageContainer.addClass('alert-success').text(_lang.feedback_messages.resetPasslEmailSent).show();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    jQuery("#loader-global").hide();

                    messageContainer.addClass('alert-danger').text('An error occurred: ' + textStatus).show();
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