<?php


echo "<div id=\"getting-started-container\">\r\n<div class=\"getting-started-title d-flex justify-content-center align-items-center \">\r\n        <div class=\"a4l-logo\"><img src=\"assets/images/a4l_logo.png\"/></div>\r\n        <div class=\"header\"> ";
echo $this->lang->line("getting_started_with_a4l");
echo "</div>\r\n    </div>\r\n    <div class=\"col-md-4 col-xs-4 no-padding\"><div class=\"gauge gauge-container\"></div></div>   \r\n    <div class=\"row\">\r\n        <div class=\"col-md-4 col-xs-4 no-padding\">\r\n            <div id=\"getting-started-helper-steps\" class='col-md-10 col-xs-11'>";
echo $this->lang->line("do_you_need_in_getting_started");
echo "</div>\r\n            <div id=\"request-demo-container\" class=\"col-md-10 col-xs-11\">\r\n                <a href=\"https://www.sheria360.com/en/company#contact\" class=\"btn btn-primary request-demo-button\">";
echo $this->lang->line("request_a_demo");
echo "</a>\r\n            </div>\r\n            <div class=\"a4l-related-products col-md-12 d-flex justify-content-between\">\r\n                <p>";
echo $this->lang->line("download_mobile_app");
echo "</p>\r\n                <div class='a4l-related-products-icons-section d-flex'>\r\n                    <a href=\"";
echo $this->config->item("apple_store_download_link");
echo "\" target=\"_blank\"><div class='a4l-related-products-icon apple-icon'></div></a>\r\n                    <a href=\"";
echo $this->config->item("google_play_download_link");
echo "\" target=\"_blank\"><div class='a4l-related-products-icon android-icon'></div></a>\r\n                </div>\r\n            </div>\r\n            <div class=\"a4l-related-products border-top justify-content-between col-md-12 d-flex\">\r\n                <p>";
echo sprintf($this->lang->line("connect_a4o_to_a4l_download_a4o_addon"), $this->config->item("ota_download_link"));
echo "</p>\r\n                <div class='a4l-related-products-icons-section padding-top-10'>\r\n                    <a href=\"";
echo $this->config->item("ota_download_link");
echo "\" target=\"_blank\"><div class='a4l-related-products-icon outlook-icon'></div></a>\r\n                </div>\r\n            </div>\r\n            <div class=\"a4l-related-products border-top justify-content-between col-md-12 d-flex \">\r\n                <p>";
echo sprintf($this->lang->line("check_out_a4l_cp"), $this->config->item("cp_documentation_link"));
echo "</p>\r\n                <div class='a4l-related-products-icons-section padding-top-10'>\r\n                    <a href=\"";
echo $this->config->item("cp_documentation_link");
echo "\" target=\"_blank\"><div class='a4l-related-products-icon cp-icon'></div></a>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        <div class=\"col-md-8 steps-section\">\r\n        <div class=\"row\">\r\n            ";
$need_help = false;
$helper_step = "";
$i = 1;
$count = 1;
foreach ($getting_started_steps as $step_name => $step_function) {
    if ($step_name === "avatar") {
        $is_avatar_step_done = isset($getting_started_settings["add_avatar_step_done"]) && $getting_started_settings["add_avatar_step_done"] ? true : false;
        echo "                    <div id='";
        echo $step_name;
        echo "' class=\"col-md-6 section avatar-section ";
        echo $is_avatar_step_done ? "step-done" : "";
        echo "\">\r\n                    <div class=\"col-md-9 float-right p-0\">\r\n                        <div class='col-md-10 p-0 section-label'><span onclick=\"";
        echo $step_function;
        echo "\">";
        echo $i;
        echo ". ";
        echo $this->lang->line("choose_your_avatar");
        echo "</span></div>\r\n                        <img class='done-sign ";
        echo $is_avatar_step_done ? "" : " d-none";
        echo "' src=\"assets/images/getting_started/correct_sign.png\"/>\r\n                        <img id=\"user-avatar\" class='section-img' src=\"";
        echo $this->session->userdata("AUTH_user_profilePicture") != "" && $this->session->userdata("AUTH_user_profilePicture") != NULL ? BASEURL . "users/get_profile_picture/" . $this->session->userdata("AUTH_user_id") : "assets/images/getting_started/default_avatar.png";
        echo "\" onclick=\"";
        echo $step_function;
        echo "\"/>\r\n                    </div>\r\n                    </div>  \r\n                    ";
    } else {
        $is_step_done = isset($getting_started_settings["add_" . $step_name . "_step_done"]) && $getting_started_settings["add_" . $step_name . "_step_done"] ? true : false;
        if (!$is_step_done && !$need_help && $is_avatar_step_done) {
            $need_help = true;
            $helper_step = $step_name;
        }
        echo "                    <div id='";
        echo $step_name;
        echo "' class=\"col-md-6 section ";
        echo $step_name;
        echo "-section  ";
        echo $is_step_done ? " step-done" : "";
        echo "\" >\r\n                       <div class=\"col-md-9 float-right p-0\">\r\n                        <div class='col-md-10  p-0 section-label'><span onclick=\"";
        echo $step_function;
        echo "\">";
        echo $i;
        echo ". ";
        echo sprintf($this->lang->line("add_your_first"), $step_name == "legal_matter" ? $this->lang->line("new_corporate_matter") : $this->lang->line("new_" . $step_name));
        echo "</span></div>\r\n                        <img class='done-sign ";
        echo $is_step_done ? "" : " d-none";
        echo "' src=\"assets/images/getting_started/correct_sign.png\"/>\r\n                        <img class='section-img' src=\"assets/images/getting_started/";
        echo $step_name;
        echo ".png\" onclick=\"";
        echo $step_function;
        echo "\"/>\r\n                    </div>\r\n                    </div>\r\n                ";
    }
    $count++;
    if ($count === 2) {
        $count = 0;
        echo "                    ";
    }
    $i++;
    if (!$is_avatar_step_done) {
        $helper_step = "avatar";
    }
}
echo "        </div>\r\n    </div>\r\n    </div>\r\n    <div class=\"col-md-12 col-xs-12\"><hr></div>\r\n    <div class=\"col-md-12 col-xs-12 dont-show-again-container\"><div class=\"dont-show-again \"><label for=\"dont-show-again-checkbox\">";
echo form_checkbox("private", "", "", "id=\"dont-show-again-checkbox\" onclick=\"\"");
echo $this->lang->line("dont_show_again");
echo "</label></div>\r\n        <div class=\"confirm-dont-show-again d-none\">";
echo $this->lang->line("are_you_sure");
echo "            <button type=\"button\" class=\"btn btn-primary\" onclick=\"hideGettingStarted();\">";
echo $this->lang->line("yes");
echo "</button>\r\n            <button type=\"button\" class=\"btn btn-primary\" onclick=\"jQuery('.confirm-dont-show-again').addClass('d-none');\r\n                    jQuery('.dont-show-again').removeClass('d-none');\">";
echo $this->lang->line("no");
echo "</button>\r\n        </div>\r\n    </div>\r\n    <iframe class=\"d-none\" id=\"hiddenFrameContainer\" name=\"hiddenFrameContainer\" src='' width=\"0\" height=\"0\"></iframe>\r\n</div>\r\n<script>\r\n    helperStep = '";
echo $helper_step;
echo "';\r\n    stepsDone = '";
echo $steps_done;
echo "';\r\n    openAvatarForm = '";
echo isset($getting_started_settings["auto_open_avatar_form"]) ? $getting_started_settings["auto_open_avatar_form"] : false;
echo "';\r\n    stepsNb = '";
echo count($getting_started_steps);
echo "';\r\n</script>";

?>