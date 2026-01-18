<?php $this->load->view("partial/header"); ?>

<div class="col-md-12 no-padding" id="add-edit-user-container">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?php echo site_url("users/index"); ?>"><?php echo $this->lang->line("search_users"); ?></a>
                </li>
                <li style="width:200px" class="breadcrumb-item active">
                    <?php echo $id ? $this->lang->line("edit_user") : $this->lang->line("add_user"); ?>
                </li>
            </ul>
            <?php $this->load->view("users/user_license_details"); ?>
        </div>

        <div class="col-md-12 no-padding">
            <?php echo form_open(base_url() . "users/" . (empty($id) ? "add" : "edit/" . $id), "novalidate class=\"form-horizontal container-fluid\" enctype=\"multipart/form-data\" id=\"userDetailsForm\""); ?>

            <div class="col-md-12 col-xs-12 no-padding margin-top">
                <div class="row offset-md-9 pull-left">
                    <?php if (empty($id) && $isUserMaker) { ?>
                        <span class="red-bold"><?php echo $this->lang->line("user_status_macker_msg"); ?></span>
                        <?php echo $this->user_profile->get_error("status", "<div class=\"help-block error\">", "</div>"); ?>
                    <?php } else { ?>
                        <label class="control-label col-md-3 no-padding-left"><?php echo $this->lang->line("status"); ?></label>
                        <div class="col-md-8 no-padding">
                            <?php echo form_dropdown("status", $statuses, empty($id) ? $this->lang->line("active") : $this->user_profile->get_field("status"), "id=\"status\" class=\"form-control\"" . ($id == $this->session->userdata("AUTH_user_id") ? " disabled=\"disabled\" " : "")); ?>

                            <?php
                            $validation_errors = $this->user_profile->get("validationErrors");
                            if (!empty($validation_errors) && !empty($validation_errors["status"]) && $validation_errors["status"] === "display_subscription") { ?>
                                <div class="help-block error">
                                    <?php echo sprintf($this->lang->line("subscription_validation_to_purchase_user"), $license["core"]["maxActiveUsers"]); ?>
                                </div>
                            <?php } else { ?>
                                <?php echo $this->user_profile->get_error("status", "<div class=\"help-block error\">", "</div>"); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="clear clearfix clearfloat">&nbsp;</div>

            <ul class="col-xs-12 nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" role="tab" data-toggle="tab" href="#basic_information_div">
                        <?php echo $this->lang->line("basic_information"); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" role="tab" data-toggle="tab" href="#personal_info_div">
                        <?php echo $this->lang->line("personal_information"); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" role="tab" data-toggle="tab" href="#user_address_div">
                        <?php echo $this->lang->line("address"); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" role="tab" data-toggle="tab" href="#comments_div">
                        <?php echo $this->lang->line("comments"); ?>
                    </a>
                </li>
            </ul>

            <div class="clear clearfix clearfloat">&nbsp;</div>

            <div class="tab-content">
                <!-- Basic Information Tab -->
                <div class="tab-pane active in col-md-12 <?php echo $id ? "" : "no-padding"; ?>" id="basic_information_div">
                    <div class="row">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                <?php echo $this->lang->line("first_name"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input([
                                        "name" => "firstName",
                                        "id" => "firstName",
                                        "placeholder" => $this->lang->line("first_name"),
                                        "maxlength" => "255",
                                        "required" => "required",
                                        "value" => $this->user_profile->get_field("firstName"),
                                        "class" => "form-control",
                                        "data-validation-engine" => "validate[required]",
                                        "data-rand-autocomplete" => "true"
                                ]); ?>
                                <?php echo $this->user_profile->get_error("firstName", "<div class=\"help-block error\">", "</div>"); ?>
                            </div>
                        </div>

                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                <?php echo $this->lang->line("last_name"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input([
                                        "name" => "lastName",
                                        "id" => "lastName",
                                        "placeholder" => $this->lang->line("last_name"),
                                        "maxlength" => "255",
                                        "required" => "required",
                                        "value" => $this->user_profile->get_field("lastName"),
                                        "class" => "form-control",
                                        "data-validation-engine" => "validate[required]",
                                        "data-rand-autocomplete" => "true"
                                ]); ?>
                                <?php echo $this->user_profile->get_error("lastName", "<div class=\"help-block error\">", "</div>"); ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($this->cloud_installation_type != "on-cloud") { ?>
                        <div class="row">
                            <div class="row form-group col-md-6">
                                <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                    <?php echo $this->lang->line("username"); ?>
                                </label>
                                <div class="col-lg-7 col-sm-7 col-xs-12">
                                    <?php
                                    $username_input_attributes = [
                                            "name" => "userData[username]",
                                            "id" => "username",
                                            "placeholder" => $this->lang->line("username"),
                                            "maxlength" => "255",
                                            "value" => $this->user->get_field("username"),
                                            "class" => "form-control",
                                            "data-validation-engine" => "validate[required]",
                                            "data-rand-autocomplete" => "true"
                                    ];
                                    if ($this->user->get_field("isAd") == "1" || $this->user->get_field("userDirectory")) {
                                        $username_input_attributes["disabled"] = true;
                                    }
                                    echo form_input($username_input_attributes);
                                    ?>
                                    <?php echo $this->user->get_error("username", "<div class=\"help-block error\">", "</div>"); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                <?php echo $this->lang->line("userCode"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 d-flex col-xs-12">
                                <div class="col-md-11 no-padding">
                                    <?php echo form_input([
                                            "name" => "user_code",
                                            "id" => "user-code",
                                            "placeholder" => $this->lang->line("userCode"),
                                            "maxlength" => "10",
                                            "value" => $this->user_profile->get_field("user_code") ? $this->user_profile->get_field("user_code") : $auto_user_code,
                                            "class" => "form-control",
                                            "data-validation-engine" => "validate[required]",
                                            "data-rand-autocomplete" => "true"
                                    ]); ?>
                                    <?php echo $this->user_profile->get_error("user_code", "<div class=\"help-block error\">", "</div>"); ?>
                                </div>
                                <div class="col-md-1 no-padding">
                                    <span tooltipTitle="<?php echo $this->lang->line("helper_user_code"); ?>" class="tooltipTable pull-right padding-5">
                                        <i class="fa-solid fa-circle-question purple_color"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                <?php echo $this->lang->line("email"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input([
                                        "name" => "userData[email]",
                                        "id" => "email",
                                        "placeholder" => $this->lang->line("email"),
                                        "maxlength" => "255",
                                        "value" => $this->user->get_field("email"),
                                        "class" => "form-control",
                                        "data-validation-engine" => "validate[required,funcCall[validateEmail]]",
                                        "data-rand-autocomplete" => "true"
                                ]); ?>
                                <?php echo $this->user->get_error("email", "<div class=\"help-block error\">", "</div>"); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php if ($this->user->get_field("isAd") != "1" || $this->user->get_field("userDirectory")) { ?>
                            <div class="row form-group col-md-6">
                                <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                    <?php echo $this->lang->line("password"); ?>
                                </label>
                                <div class="row col-lg-7 col-sm-7 col-xs-12">
                                    <div class="col-lg-6 col-sm-7 col-xs-12">
                                        <?php
                                        $password_input_attributes = [
                                                "name" => "userData[password]",
                                                "id" => "password",
                                                "placeholder" => $this->lang->line("password"),
                                                "class" => "form-control",
                                                "data-rand-autocomplete" => "true"
                                        ];
                                        if ($this->user->get_field("isAd") == "1" || $this->user->get_field("userDirectory")) {
                                            $password_input_attributes["disabled"] = true;
                                        }
                                        if (empty($id)) {
                                            $password_input_attributes["data-validation-engine"] = "validate[required,funcCall[validatePassword]]";
                                        } else {
                                            $password_input_attributes["data-validation-engine"] = "validate[funcCall[validatePassword]]";
                                        }
                                        echo form_password($password_input_attributes);
                                        ?>
                                    </div>
                                    <div class="col-lg-6 col-sm-7 col-xs-12 no">
                                        <?php
                                        $confirm_password_input_attributes = [
                                                "name" => "userData[confirmPassword]",
                                                "id" => "confirmPassword",
                                                "placeholder" => $this->lang->line("confirm_password"),
                                                "class" => "form-control",
                                                "data-rand-autocomplete" => "true",
                                                "data-validation-engine" => "validate[condRequired[password], equals[password]]",
                                                "title" => $this->lang->line("confirm_password")
                                        ];
                                        if ($this->user->get_field("isAd") == "1" || $this->user->get_field("userDirectory")) {
                                            $confirm_password_input_attributes["disabled"] = true;
                                        }
                                        echo form_password($confirm_password_input_attributes);
                                        ?>
                                    </div>
                                    <?php echo $this->user->get_error("password", "<div class=\"help-block error\">", "</div>"); ?>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                <?php echo $this->lang->line("user_group"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_dropdown("userData[user_group_id]", $userGroups, $this->user->get_field("user_group_id"), " id=\"user_group_id\" class=\"form-control scenario_user_groups\"  data-validation-engine=\"validate[required]\""); ?>
                                <?php echo $this->user->get_error("user_group_id", "<div class=\"help-block error\">", "</div>"); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="row form-group col-md-6 <?php echo $this->licensor->license_package; ?>-access">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                <?php echo $this->lang->line("access_type"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php
                                $access_type = $this->user->get_field("type");
                                echo form_checkbox("access_type[]", "core", $access_type ? $access_type == "core" || $access_type == "both" ? true : false : ($this->licensor->license_package == "core" ? true : false), "");
                                echo "<span class=\"margin-right-10\">" . $this->lang->line("core") . "</span>";
                                echo form_checkbox("access_type[]", "contract", $access_type ? $access_type == "contract" || $access_type == "both" ? true : false : ($this->licensor->license_package == "contract" ? true : false), "");
                                echo "<span class=\"margin-right-10\">" . $this->lang->line("contract") . "</span>";
                                ?>
                                <?php echo $this->user->get_error("type"); ?>
                            </div>
                        </div>

                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12 required">
                                <?php echo $this->lang->line("seniority_level"); ?>
                            </label>
                            <div class="col-lg-7 d-flex col-sm-7 col-xs-12">
                                <div class="col-md-11 no-padding">
                                    <select name="seniority_level_id" id="seniority_level_id" class="form-control scenario_seniority_levels" data-validation-engine="validate[required]">
                                        <option value=""><?php echo $this->lang->line("choose_seniority_level"); ?></option>
                                        <?php foreach ($seniority_Levels as $key => $value) {
                                            $selected = "";
                                            if ($this->user->get_field("id")) {
                                                if ($this->user_profile->get_field("seniority_level_id") == $key) {
                                                    $selected = " selected=\"selected\"";
                                                }
                                            } else {
                                                if (isset($default_seniority_Level) && $default_seniority_Level == $key) {
                                                    $selected = " selected=\"selected\"";
                                                }
                                            }
                                            echo "<option value=\"" . $key . "\" " . $selected . ">" . $value . "</option>";
                                        } ?>
                                    </select>
                                    <?php echo $this->user->get_error("seniority_level_id", "<div class=\"help-block error\">", "</div>"); ?>
                                </div>
                                <div class="col-md-1 no-padding">
                                    <a href="javascript:;" onclick="onthefly_Ajax('seniority_levels', 'seniority_levels');" class="btn btn-link">
                                        <i class="fa-solid fa-square-plus p-1 font-18"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Tab -->
                <div class="tab-pane col-md-12 <?php echo $id ? "" : "no-padding"; ?>" id="personal_info_div">
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("contact_title"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_dropdown("title", $titles, $this->user_profile->get_field("title"), "id=\"title\" class=\"form-control\""); ?>
                                <?php echo $this->user_profile->get_error("title", "<div class=\"help-block error\">", "</div>"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("gender"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_dropdown("gender", $genders, $this->user_profile->get_field("gender"), "id=\"gender\" class=\"form-control\""); ?>
                                <?php echo $this->user_profile->get_error("gender", "<div class=\"help-block error\">", "</div>"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("job_title"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "jobTitle", "id" => "jobTitle", "placeholder" => $this->lang->line("job_title"), "maxlength" => "255", "value" => $this->user_profile->get_field("jobTitle"), "class" => "form-control"]); ?>
                                <?php echo $this->user_profile->get_error("jobTitle"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12"></label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <input name="isLawyerCheck" id="isLawyerCheck" type="checkbox" value="yes" title="<?php echo $this->lang->line("lawyer_user_title"); ?>" onchange="jQuery('#isLawyer').val(this.checked ? 'yes' : 'no')" <?php echo $this->user_profile->get_field("isLawyer") == "yes" ? " checked='checked'" : ""; ?> />
                                <?php echo form_input(["name" => "isLawyer", "id" => "isLawyer", "value" => $this->user_profile->get_field("isLawyer") == "yes" ? "yes" : "no", "type" => "hidden"]); ?>
                                <?php echo $this->lang->line("is_lawyer"); ?>
                            </div>
                            <?php echo $this->user_profile->get_error("isLawyer"); ?>
                        </div>
                    </div>
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("foreign_first_name"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "foreign_first_name", "id" => "foreign-first-name", "placeholder" => $this->lang->line("foreign_first_name"), "maxlength" => "255", "value" => $this->user_profile->get_field("foreign_first_name"), "class" => "form-control"]); ?>
                                <?php echo $this->user_profile->get_error("foreign_first_name"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("foreign_last_name"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "foreign_last_name", "id" => "foreign-last-name", "placeholder" => $this->lang->line("foreign_last_name"), "maxlength" => "255", "value" => $this->user_profile->get_field("foreign_last_name"), "class" => "form-control"]); ?>
                                <?php echo $this->user_profile->get_error("foreign_last_name"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("father"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "father", "id" => "father", "placeholder" => $this->lang->line("father"), "maxlength" => "255", "value" => $this->user_profile->get_field("father"), "class" => "form-control"]); ?>
                                <?php echo $this->user_profile->get_error("father"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("mother"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "mother", "id" => "mother", "placeholder" => $this->lang->line("mother"), "maxlength" => "255", "value" => $this->user_profile->get_field("mother"), "class" => "form-control"]); ?>
                                <?php echo $this->user_profile->get_error("mother"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("employee_id"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "employeeId", "id" => "employeeId", "placeholder" => $this->lang->line("employee_id"), "maxlength" => "255", "value" => $this->user_profile->get_field("employeeId"), "class" => "form-control"]); ?>
                                <?php echo $this->user_profile->get_error("employeeId"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("date_of_birth"); ?>
                            </label>
                            <div class="input-group date col-md-5 col-lg-7 col-sm-7 col-xs-12 date-picker" id="date-of-birth-date-container">
                                <?php echo form_input("start_date", $this->user_profile->get_field("dateOfBirth"), "id=\"dateOfBirth\" class=\"date start form-control\" style=\"height:31px;\""); ?>
                                <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("provider_group_users"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <div class="margin-bottom">
                                    <?php echo form_input(["id" => "lookupProviderGroups", "class" => "form-control lookup search", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing")]); ?>
                                </div>
                                <div id="selected_provider_groups" class="row height-99 no-margin flex-column-no-padding">
                                    <?php foreach ($provider_groups_users as $pgId => $pgName) { ?>
                                        <div class="row multi-option-selected-items no-margin" id="user_provider_group<?php echo $pgId; ?>">
                                            <span id="<?php echo $pgId; ?>"><?php echo $pgName; ?></span>
                                            <?php echo form_input(["name" => "provider_groups_users[]", "value" => $pgId, "type" => "hidden"]); ?>
                                            <input value="x" type="button" class="btn btn-default btn-xs pull-right flex-end-item" onclick="jQuery(this.parentNode).remove()" />
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("fax"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "fax", "id" => "fax", "autocomplete" => "stop", "maxlength" => "255", "value" => $this->user_profile->get_field("fax"), "class" => "form-control", "data-rand-autocomplete" => "true"]); ?>
                                <?php echo $this->user_profile->get_error("fax"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("phone"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "phone", "id" => "phone", "placeholder" => $this->lang->line("phone"), "maxlength" => "255", "value" => $this->user_profile->get_field("phone"), "class" => "form-control", "data-rand-autocomplete" => "true"]); ?>
                                <?php echo $this->user_profile->get_error("phone"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("website"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "website", "id" => "website", "placeholder" => $this->lang->line("website"), "maxlength" => "255", "value" => $this->user_profile->get_field("website"), "class" => "form-control", "data-validation-engine" => "validate[funcCall[validateWebsite]]"]); ?>
                                <?php echo $this->user_profile->get_error("website"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("nationality"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_dropdown("nationality", $Countries, $this->user_profile->get_field("nationality"), " id=\"nationality\" class=\"form-control\""); ?>
                                <?php echo $this->user_profile->get_error("nationality"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("mobile"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "mobile", "id" => "mobile", "placeholder" => $this->lang->line("mobile"), "maxlength" => "255", "value" => $this->user_profile->get_field("mobile"), "class" => "form-control", "data-rand-autocomplete" => "true"]); ?>
                                <?php echo $this->user_profile->get_error("mobile"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12 no-padding">
                        <div class="row form-group col-md-6">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php   echo $this->lang->line("department"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "department", "id" => "department", "placeholder" => $this->lang->line("department"), "maxlength" => "255", "value" => $this->user_profile->get_field("department"), "class" => "form-control lookup"]); ?>
                                <?php echo $this->user_profile->get_error("department"); ?>
                                <?php echo form_input(["name" => "department_id", "id" => "department_id", "value" => $this->user_profile->get_field("department_id"), "type" => "hidden"]); ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 no-padding">
                        <?php if ($this->user->get_field("isAd") == "1" || $this->user->get_field("userDirectory")) { ?>
                            <div class="form-group col-md-6">
                                <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12"></label>
                                <div class="col-lg-7 col-sm-7 col-xs-12">
                                    <?php echo $this->lang->line("ActiveDirectoryUser"); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Address Tab -->
                <div class="tab-pane col-md-12 <?php echo $id ? "" : "no-padding"; ?>" id="user_address_div">
                    <div class="col-md-9 col-xs-12">
                        <div class="row form-group">
                            <label class="control-label  no-padding-right col-lg-2 col-md-1 col-sm-3 col-xs-12">
                                <?php echo $this->lang->line("address_1"); ?>
                            </label>
                            <div class="col-md-10 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "address1", "id" => "address1", "placeholder" => $this->lang->line("address_1"), "maxlength" => "255", "value" => $this->user_profile->get_field("address1"), "class" => "form-control", "data-rand-autocomplete" => "true"]); ?>
                                <?php echo $this->user_profile->get_error("address1"); ?>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="control-label  no-padding-right col-lg-2 col-md-1 col-sm-3 col-xs-12">
                                <?php echo $this->lang->line("address_2"); ?>
                            </label>
                            <div class="col-md-10 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "address2", "id" => "address2", "placeholder" => $this->lang->line("address_2"), "maxlength" => "255", "value" => $this->user_profile->get_field("address2"), "class" => "form-control", "data-rand-autocomplete" => "true"]); ?>
                                <?php echo $this->user_profile->get_error("address2"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row form-group col-md-6 no-padding">
                            <label class="control-label  no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("city"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "city", "id" => "city", "placeholder" => $this->lang->line("city"), "maxlength" => "255", "value" => $this->user_profile->get_field("city"), "class" => "form-control", "data-rand-autocomplete" => "true"]); ?>
                                <?php echo $this->user_profile->get_error("city"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6 no-padding">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("state"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "state", "id" => "state", "placeholder" => $this->lang->line("state"), "maxlength" => "255", "value" => $this->user_profile->get_field("state"), "class" => "form-control", "data-rand-autocomplete" => "true"]); ?>
                                <?php echo $this->user_profile->get_error("state"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row form-group col-md-6 no-padding">
                            <label class="control-label  no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("country"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_dropdown("country", $Countries, $this->user_profile->get_field("country"), " id=\"country\" class=\"form-control\""); ?>
                                <?php echo $this->user_profile->get_error("country"); ?>
                            </div>
                        </div>
                        <div class="row form-group col-md-6 no-padding">
                            <label class="control-label no-padding-right col-lg-3 col-sm-2 col-xs-12">
                                <?php echo $this->lang->line("zip"); ?>
                            </label>
                            <div class="col-lg-7 col-sm-7 col-xs-12">
                                <?php echo form_input(["name" => "zip", "id" => "zip", "placeholder" => $this->lang->line("zip"), "maxlength" => "32", "value" => $this->user_profile->get_field("zip"), "class" => "form-control", "data-rand-autocomplete" => "true"]); ?>
                                <?php echo $this->user_profile->get_error("zip"); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments Tab -->
                <div class="tab-pane col-md-12  comments-container <?php echo $id ? "" : "no-padding"; ?>" id="comments_div">
                    <div class="col-md-9 col-xs-12 no-padding">
                        <div class="row form-group">
                            <label class="control-label no-padding-right col-lg-2 col-md-5 col-sm-4 col-xs-12">
                                <?php echo $this->lang->line("comments"); ?>
                            </label>
                            <div class="col-lg-10 col-md-11 col-sm-7 col-xs-12">
                                <?php echo form_textarea("comments", $this->user_profile->get_field("comments"), ["id" => "comments", "rows" => "5", "placeholder" => $this->lang->line("comments"), "class" => "form-control"]); ?>
                                <?php echo $this->user_profile->get_error("comments"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row no-margin">
                <?php echo form_submit("submitBtn2", $this->lang->line("save"), "class=\"btn btn-default btn-info\""); ?>
                <?php echo form_input(["name" => "id", "id" => "id", "value" => $this->user->get_field("id"), "type" => "hidden"]); ?>
                <?php if ($this->user->get_field("id")) { ?>
                    &nbsp; <a href="javascript:;" onclick="cloneUserForm('<?php echo $this->user->get_field("id"); ?>')" class="btn btn-default btn-link">
                        <?php echo $this->lang->line("clone"); ?>
                    </a>
                <?php } ?>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div id="subscription-temp">
    <?php echo $this->load->view("subscription/purchase_additional_user_form", NULL, true); ?>
</div>

<?php
$this->load->view("provider_groups/quick_form");
$this->load->view("partial/footer");
?>

<script>
    var canAddUser = '<?php echo $can_add_user; ?>';
    jQuery("#userDetailsForm").keypress(function(event) {
        if (event.which == '13') {
            return false;
        }
    });
    var id = <?php echo $id; ?>;

    function validatePassword(field, rules, i, options) {
        var val = field.val();
        var length = <?php echo $this->user->minPassword; ?>;
        if (val.length < length)
            return _lang.shortPasswordMsg.sprintf([length]);
        <?php if ($this->user->complexPassword) { ?>
        var passwordPattern = /^(?=.*[a-zA-Z])(?=.*[!@#\$%^&*()\-_+=\\\/?<>|:;.\"'\]\[{}])(?=\S+$).{2,}$/;
        if (!passwordPattern.test(val)) {
            return _lang.complexPasswordMsg;
        }
        <?php } ?>
        if (val === jQuery('#email').val()) {
            return _lang.PasswordShouldNotBeEqualToEmailAddress
        }
    }

    jQuery(document).on('click', '.tt-quick-add', function() {
        // Get the dynamic parameters from the data attributes set in the 'empty' template
        const $this = jQuery(this);
        const controller = $this.data('controller');
        onthefly_Ajax(controller, controller);
    });

    initializeAutocompleteField({
        inputSelector: '#department',//input name field
        hidden_idSelector: '#department_id',
        controller: 'departments', // This is used for the URL and data attribute
        nameProperty: 'name'       // The property to display and search by
    });
</script>