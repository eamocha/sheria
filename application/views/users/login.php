<div class="container-fluid vh-100 d-flex justify-content-center align-items-center bg-light">
    <div class="row w-100" style="max-width: 1200px; height: 80vh; min-height: 500px;">
        <div class="col-md-7 d-none d-md-flex flex-column justify-content-center bg-white p-5 rounded-start shadow-lg overflow-hidden">
            <div class="text-center mb-5">
                <h1 class="display-3 fw-bold mb-3 text-dark animate__animated animate__fadeInDown">Welcome to <?php echo $app_title=  str_replace("App4Legal", $this->instance_data_array["app_name"], $this->pageTitle); ?>!</h1>
                <p class="lead mb-0 text-secondary animate__animated animate__fadeInUp">
                    Your comprehensive legal service management solution, <b>where efficiency meets smart management and collaboration.</b>

                </p>
            </div>

            <div class="px-4 mb-5 animate__animated animate__fadeIn animate__delay-1s">
                <h4 class="fw-bold mb-4 text-dark text-center">Boosting Legal Team Efficiency, One Workflow at a Time</h4>
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <i class="fas fa-gavel fa-3x mb-3 text-primary"></i> <h6 class="fw-bold text-dark">Comprehensive Management</h6>
                        <p class="small text-secondary">Handle litigation, contracts, and advisory seamlessly.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <i class="fas fa-handshake fa-3x mb-3 text-primary"></i> <h6 class="fw-bold text-dark">Enhanced Collaboration</h6>
                        <p class="small text-secondary">Centralize data for better teamwork and communication.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i> <h6 class="fw-bold text-dark">Unmatched Efficiency</h6>
                        <p class="small text-secondary">Automate processes, reduce manual effort, boost productivity.</p>
                    </div>
                </div>
            </div>

            <div class="px-4 overflow-auto animate__animated animate__fadeIn animate__delay-2s" style="max-height: 25vh;">
                <h5 class="fw-bold mb-3 text-dark">Key Features Include:</h5>
                <ul class="list-unstyled small text-secondary">
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle mt-1 me-2 text-info"></i> <span><strong>Contracts & MoU:</strong> Efficient drafting, tracking, and renewals.</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle mt-1 me-2 text-info"></i>
                        <span><strong>Litigation & ADR:</strong> Streamlined court case and dispute resolution management.</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle mt-1 me-2 text-info"></i>
                        <span><strong>Conveyancing & Insurance:</strong> End-to-end property transactions and claims processing.</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle mt-1 me-2 text-info"></i>
                        <span><strong>Advisory & Compliance:</strong> Comprehensive legal opinions and regulatory coordination.</span>
                    </li>
                    <li class="mb-2 d-flex align-items-start">
                        <i class="fas fa-check-circle mt-1 me-2 text-info"></i>
                        <span><strong>Office & Board Management:</strong> Organized file management and board resolutions.</span>
                    </li>
                </ul>
                <div class="text-center mb-5">
                    Login to the <a href="<?php echo base_url("modules/customer-portal/") ;?>" target="_blank" ?>client portal</a> to submit your a request.
                </div>
            </div>

            <div class="border-top pt-3 mt-4 text-center text-dark animate__animated animate__fadeInUp animate__delay-3s">
                <p class="lead mb-0 fw-bold">Step into the future of legal operations with <?php echo $app_title?>. Log in now!</p>
            </div>
        </div>

        <div class="col-md-5 d-flex align-items-center justify-content-center bg-white p-5 rounded-right shadow-lg">
            <div class="w-100" style="max-width: 380px;">
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
                // PHP side: Populate the general message container on page load if there are messages
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
                    echo "    msgContainer.innerHTML = '" . addslashes($messageContent) . "';"; // Escape for JS
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
                        <?php echo $this->lang->line("footer_all_rights_reserved"); echo " &copy; " . date("Y") . " -" . $app_title;
                        ?>
                    </span>
                </div>
            </div>
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