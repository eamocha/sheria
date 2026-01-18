<div class="container h-100 d-flex align-items-center justify-content-center">
    <div class="row w-100">
        <div class="col-md-6 offset-md-3">
            <div class="text-center mb-4">
                <img id="image" alt="Sheria360"
                     class="img-fluid"
                     style="max-height: 100px;"
                     src="<?php echo !empty($this->instance_data_array["customer_portal_login_logo"])
                         ? $this->instance_data_array["customer_portal_login_logo"]
                         : $this->config->item("files_path") . "/images/instance/a4l_logo_sign_in.png"; ?>" />
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4"><?php echo $this->lang->line("sign_in"); ?></h5>

                    <?php echo form_open("users/login", "id=\"portalLoginForm\" role=\"form\" method=\"post\""); ?>

                    <div class="form-group">
                        <?php
                        $username_input_attributes = [
                            "class" => "form-control",
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
                            <small class="form-text text-danger">
                                <?php echo $this->lang->line("user_login_is_required"); ?>
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <?php
                        $password_input_attributes = [
                            "type" => "password",
                            "class" => "form-control",
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
                            <small class="form-text text-danger">
                                <?php echo $this->lang->line("password_is_required"); ?>
                            </small>
                        <?php endif; ?>
                    </div>

                    <?php if ($this->is_auth->stay_signed_in() == "yes"): ?>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember" />
                            <label class="form-check-label" for="remember">
                                <?php echo $this->lang->line("stay_signed_in"); ?>
                            </label>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary btn-block">
                        <?php echo $this->lang->line("sign_in"); ?>
                    </button>

                    <div class="text-center mt-3">
                        <small>
                            <?php echo $this->lang->line("dont_have_an_account"); ?>
                            <a href="javascript:;" onClick="signupForm()"><?php echo $this->lang->line("signup"); ?></a>
                        </small>
                    </div>

                    <?php if ($idp_enabled): ?>
                        <div class="text-center my-4">
                            <hr>
                            <small class="text-muted">Or sign in using</small><br>
                            <a href="<?php echo site_url("users/login_idps/azure_ad"); ?>" title="Azure AD">
                                <img src="assets/images/azure_ad-logo.png" class="mx-2" style="max-height: 30px;" />
                            </a>
                            <a href="<?php echo site_url("users/login_idps/onelogin"); ?>" title="OneLogin">
                                <img src="assets/images/onelogin-logo.png" class="mx-2" style="max-height: 30px;" />
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($validationErrors)): ?>
                        <div class="alert alert-danger mt-3" role="alert">
                            <?php echo $validationErrors; ?>
                        </div>
                    <?php endif; ?>

                    <?php echo form_close(); ?>
                </div>
            </div>
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
            var loginCardHeight = jQuery(".card").outerHeight();
            var offset = (windowHeight - loginCardHeight) / 5;
            jQuery(".container").css("padding-top", offset + "px");
        }
    });
</script>