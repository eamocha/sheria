<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="row w-100 shadow-lg rounded-lg overflow-hidden" style="max-width: 960px;">

        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-secondary text-white p-4 p-md-5">
            <div class="w-100" style="max-width: 500px;">
                <h2 class=" text-left">Sheria360 Portal</h2> <p class="lead text-white mb-4 text-left">Your secure portal for streamlined legal and contract lifecycle management. Designed for efficiency and seamless collaboration.</p> <ul class="list-unstyled text-left my-4">
                    <li class="mb-3">
                        <i class="fa fa-check mr-2"></i> <strong class="text-white">Legal Opinions:</strong> Submit & track easily.
                    </li>
                    <li class="mb-3">
                        <i class="fa fa-check mr-2"></i> <strong class="text-white">Conveyancing:</strong> Efficient document management.
                    </li>
                    <li class="mb-3">
                        <i class="fa fa-check mr-2"></i> <strong class="text-white">Contracts:</strong> Track all lifecycle stages.
                    </li>
                    <li class="mb-3">
                        <i class="fa fa-check mr-2"></i> <strong class="text-white">Case Files:</strong> Secure access & documents.
                    </li>
                    <li class="mb-3">
                        <i class="fa fa-check mr-2"></i> <strong class="text-white">Collaboration:</strong> Connect with your legal team.
                    </li>
                </ul>

                <p class="mt-4 text-white-50 small text-left">For clients, lawyers, and property managers.</p> </div>
        </div>

        <div class="col-md-6 bg-white p-4 p-md-5">
            <div class="text-center mb-3">
                <img src="<?php echo !empty($this->instance_data_array["customer_portal_login_logo"])
                    ? $this->instance_data_array["customer_portal_login_logo"]
                    : $this->config->item("files_path") . "/images/instance/a4l_logo_sign_in.png"; ?>"
                     alt="Sheria360" class="img-fluid mb-3" style="max-height: 100px;">
            </div>



            <?php echo form_open("users/login", "id=\"portalLoginForm\" role=\"form\" method=\"post\""); ?>

            <div class="form-group mb-4">
                <?php
                $username_input_attributes = [
                    "class" => "form-control form-control-lg",
                    "placeholder" => $this->lang->line("user_login"),
                    "name" => "username",
                    "autocomplete" => "off",
                    "value" => set_value("username"),
                    "required" => "required"
                ];
                if (empty($validationErrors)) {
                    $username_input_attributes["autofocus"] = true;
                }
                echo form_input($username_input_attributes);
                ?>
                <?php if (!empty(form_error("username"))): ?>
                    <small class="text-danger mt-1"><?php echo $this->lang->line("user_login_is_required"); ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group mb-4">
                <?php
                $password_input_attributes = [
                    "type" => "password",
                    "class" => "form-control form-control-lg",
                    "placeholder" => $this->lang->line("password"),
                    "name" => "password",
                    "required" => "required"
                ];
                if (!empty($validationErrors)) {
                    $password_input_attributes["autofocus"] = true;
                }
                echo form_password($password_input_attributes);
                ?>
                <?php if (!empty(form_error("password"))): ?>
                    <small class="text-danger mt-1"><?php echo $this->lang->line("password_is_required"); ?></small>
                <?php endif; ?>
            </div>

            <?php if ($this->is_auth->stay_signed_in() == "yes"): ?>
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember" />
                    <label class="form-check-label" for="remember"><?php echo $this->lang->line("stay_signed_in"); ?></label>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary btn-block btn-lg mt-4"><?php echo $this->lang->line("sign_in"); ?></button>

            <div class="text-center mt-4">
                <small>
                    <?php echo $this->lang->line("dont_have_an_account"); ?>
                    <a href="javascript:;" onClick="signupForm()" class="text-decoration-none"><?php echo $this->lang->line("signup"); ?></a>
                </small>
            </div>

            <?php if ($idp_enabled): ?>
                <hr class="my-4">
                <div class="text-center">
                    <small class="text-muted d-block mb-3">Or sign in using</small>
                    <div class="d-flex justify-content-center align-items-center">
                        <a href="<?php echo site_url("users/login_idps/azure_ad"); ?>" class="mx-3" title="Azure AD">
                            <img src="assets/images/azure_ad-logo.png" style="max-height: 40px;" alt="Azure AD" />
                        </a>
                        <a href="<?php echo site_url("users/login_idps/onelogin"); ?>" class="mx-3" title="OneLogin">
                            <img src="assets/images/onelogin-logo.png" style="max-height: 40px;" alt="OneLogin" />
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($validationErrors)): ?>
                <div class="alert alert-danger mt-4" role="alert">
                    <?php echo $validationErrors; ?>
                </div>
            <?php endif; ?>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        positionTop();

        jQuery("form#portalLoginForm").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });

        jQuery(window).resize(function () {
            positionTop();
        });

        function positionTop() {
            var windowHeight = jQuery(window).height();
            var loginCardHeight = jQuery(".row.shadow-lg").outerHeight();
            var offset = (windowHeight - loginCardHeight) / 5;

            if (offset > 0) {
                jQuery(".container-fluid.d-flex").css("padding-top", offset + "px");
                jQuery(".container-fluid.d-flex").css("padding-bottom", offset + "px");
            } else {
                jQuery(".container-fluid.d-flex").css("padding-top", "0px");
                jQuery(".container-fluid.d-flex").css("padding-bottom", "0px");
            }
        }
    });
</script>