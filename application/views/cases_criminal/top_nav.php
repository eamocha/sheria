<?php


$cp_class = $legalCase["channel"] == "CP" || $legalCase["visibleToCP"] == "1" ? " id=\"legal-case-top-header-profile\" class=\"tooltip-title light_green cursor-pointer-click\" title=\"" . $this->lang->line("visibleFromCP") . "\"" : " id=\"legal-case-top-header-profile\" class=\"tooltip-title label-normal-style cursor-pointer-click\" title=\"" . $this->lang->line("invisibleFromCP") . "\"";
$cp_class_icon = $legalCase["channel"] == "CP" || $legalCase["visibleToCP"] == "1" ? "client-portal-blue" : "client-portal-grey";
echo "<nav class=\"navbar navbar-default no-margin top-header-page\" role=\"navigation\">\r\n    <div class=\"col-md-12 no-padding-left flex-item-box\">\r\n        <div class=\"d-flex col-md-5 main-title\" id=\"legal-case-top-header-container\">\r\n            <div class=\"profile-image-matter pull-left ma\">\r\n                ";
echo strtolower($legalCase["category"]) == "criminal" ? $this->lang->line("c") : $this->lang->line("l");
echo "            </div>\r\n            <div class=\"pt-5 matter\">\r\n                <h4 class=\"sub-title-matter\">\r\n                    <a class=\"matter-code\" href=\"";
echo base_url() . "cases/edit/" . $this->legal_case->get_field("id");
echo "\" data-id=\"";
echo $this->legal_case->get_field("id");
echo "\">";
echo $this->legal_case->get("modelCode") . $this->legal_case->get_field("id");
echo "</a>\r\n                    <bdi>\r\n                        <span id=\"matter-title\" class=\"trim-matter-title tooltip-title matter-subject\" title=\"";
echo "&lt;span dir='auto' &gt " . htmlspecialchars($this->legal_case->get_field("subject")) . "&lt;/span&gt";
echo "\" dir=\"auto\">\r\n                            ";
echo htmlspecialchars($this->legal_case->get_field("subject"));
echo $this->session->userdata("AUTH_language") == "arabic" ? "&lrm;" : "&rlm;";
echo "                        </span>\r\n                    </bdi> \r\n                </h4>\r\n                <h4 class=\"sub-title-matter\">\r\n                    ";
echo "<span class=\"text-gray\">" . (strtolower($legalCase["category"]) == "criminal" ? $this->lang->line("criminal_case_matter_edit_page_title") : $this->lang->line("litigation_matter_edit_page_title")) . "<span " . $cp_class . ">";
echo "                    ";
echo "&nbsp;&nbsp;&nbsp;<span id=\"cp-icon\" onclick=\"showMatterInCustomerPortal(" . (int) $legalCase["id"] . ", event);\" class=\"big-title-text-font-size " . $cp_class_icon . "\" aria-hidden=\"true\"></i></span>";
echo "                </h4>\r\n            </div>\r\n        </div>\r\n        ";
if (isset($actions)) {
    echo "            ";
    echo $actions;
    echo "        ";
}
echo "        ";
if ($main_tab) {
    echo "            <div class=\"flex-end-item\">\r\n                <div class=\"flex-item-box pull-right no-padding pt-5 status-top-header-width padding-15-mobile\">\r\n                    <div class=\"no-padding\" id=\"status-top-nav-container\">\r\n                        <div class=\"col-md-12 no-padding mobile-no-margin\">\r\n                            <div class=\"pull-right d-flex\">\r\n                                ";
    $currentStatus = $Case_Statuses[$legalCase["case_status_id"]];
    unset($Case_Statuses[$legalCase["case_status_id"]]);
    $firstStatuses = array_slice($Case_Statuses, 0, 3, true);
    $otherStatuses = array_slice($Case_Statuses, 3, NULL, true);
    echo "                                ";
    foreach ($firstStatuses as $statusId => $statusName) {
        echo "                                    ";
        $transitionName = $statusName;
        echo "                                    ";
        foreach ($statusTransitions as $transition) {
            echo "                                        ";
            if ($transition["toStep"] == $statusId) {
                $transitionName = $transition["name"];
                $transitionDescription = $transition["comments"];
                $transition_id = $transition["id"];
                echo "                                        ";
            }
            echo "                                    ";
        }
        echo "                                    ";
        if (isset($transition_id) && $transition_id) {
            echo "                                        <a href=\"javascript:;\"\r\n                                           onclick=\"screenTransitionForm('";
            echo $legalCase["id"];
            echo "', '";
            echo $transition_id;
            echo "', 'cases');\"\r\n                                           title=\" ";
            echo isset($transitionDescription) && $transitionDescription !== "" ? $transitionDescription : $transitionName;
            echo "\"\r\n                                           class=\"case-move-status-link btn btn-default btn-status\">\r\n                                            ";
            echo $transitionName;
            echo "                                        </a>&nbsp;&nbsp;\r\n                                    ";
        } else {
            echo "                                        <a href=\"";
            echo site_url("cases/move_status/" . $legalCase["id"] . "/" . $statusId);
            echo "\"\r\n                                           title=\" ";
            echo isset($transitionDescription) && $transitionDescription !== "" ? $transitionDescription : $transitionName;
            echo "\"\r\n                                           class=\"case-move-status-link btn btn-default btn-status submit-with-loader\">\r\n                                            ";
            echo $transitionName;
            echo "                                        </a>&nbsp;&nbsp;\r\n                                    ";
        }
        echo "                                    ";
        $transitionDescription = "";
        $transition_id = "";
        echo "                                ";
    }
    echo "                                <div class=\"caseStatusesVisibleList\">\r\n                                    <div class=\"dropdown\">\r\n                                        ";
    if (!empty($otherStatuses)) {
        echo "                                            <button class=\"btn btn-default dropdown-toggle btn-status\" type=\"button\"\r\n                                                    id=\"dropdownMenuMoreStatuses\"\r\n                                                    data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"true\">\r\n                                                ";
        echo $this->lang->line("more");
        echo "                                                <span class=\"caret\"></span>\r\n                                            </button>\r\n                                            <div class=\"dropdown-menu dropdownMenuMoreStatusesList\" aria-labelledby=\"dropdownMenuMoreStatuses\">\r\n                                                ";
        foreach ($otherStatuses as $statusId => $statusName) {
            echo "                                                    ";
            $transitionName = $statusName;
            echo "                                                    ";
            foreach ($statusTransitions as $transition) {
                echo "                                                        ";
                if ($transition["toStep"] == $statusId) {
                    $transitionName = $transition["name"];
                    $transition_id = $transition["id"];
                }
                echo "                                                    ";
            }
            echo "                                                    ";
            if (isset($transition_id) && $transition_id) {
                echo "                                                        <a class=\"dropdown-item\" href=\"javascript:;\"\r\n                                                               onclick=\"screenTransitionForm('";
                echo $legalCase["id"];
                echo "', '";
                echo $transition_id;
                echo "', 'cases');\"\r\n                                                               title=\" ";
                echo $transitionName;
                echo "\">\r\n                                                                ";
                echo 45 <= mb_strlen($transitionName) ? mb_substr($transitionName, 0, 41) . "..." : $transitionName;
                echo "                                                        </a>\r\n                                                    ";
            } else {
                echo "                                                        <a class=\"dropdown-item\" href=\"";
                echo site_url("cases/move_status/" . $legalCase["id"] . "/" . $statusId);
                echo "\"\r\n                                                           title=\"";
                echo $transitionName;
                echo "\">";
                echo 45 <= mb_strlen($transitionName) ? mb_substr($transitionName, 0, 41) . "..." : $transitionName;
                echo "</a>\r\n                                                    ";
            }
            echo "                                                    ";
            $transition_id = "";
            echo "                                                ";
        }
        echo "                                            </div>\r\n                                        ";
    }
    echo "                                    </div>\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"top-header-icons-container\">\r\n                        <div class=\"no-padding mt-7 notification-icon-margin flex-grow\">\r\n                            <div class=\"url-for-customers-container label-notification-checkbox \">\r\n                        <span class=\"checkbox no-padding-left no-margin notification-send-email-matter btn btn-disable-archived-matter borderless\">\r\n                        ";
    $this->load->view("templates/send_email_option_template", ["type" => $category == "litigation_" ? "edit_litigation_case" : "edit_matter_case", "container" => "#legalCaseAddForm", "loader" => "<img class=\"form-submit-loader\" src=\"assets/images/icons/16/loader-submit.gif\"/>", "hide_show_notification" => $hide_show_notification_edit_legal_case, "hide_label" => false]);
    echo "                        </span>\r\n                            </div>\r\n                        </div>\r\n                        <div id=\"top-header-note-container\" class=\"no-padding flex-grow\">\r\n                            <div class=\"url-for-customers-container cursor-pointer-click\">\r\n                                <i class=\"fa fa-comments purple_color font-15 tooltip-title btn btn-disable-archived-matter borderless\" title=\"";
    echo $this->lang->line("add_note");
    echo "\" onclick=\"addCaseDocument('";
    echo $legalCase["id"];
    echo "')\"></i>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                    <div class=\"no-padding no-margin-left\">\r\n                        <div class=\"navbar-custom-menu pull-right\">\r\n                            ";
    if (isset($is_edit)) {
        echo "                                <ul class=\"flex-center-inline navbar-actions center-block primary-style d-flex no-padding m-0\">\r\n                                    <li class=\"top-right-save-btn\">\r\n                                        <button type=\"button\" id=\"legal-case-add-form\" class=\"btn save-button button-blue-color btn-info\">\r\n                                            <i class=\"icon-alignment fa-solid fa-floppy-disk white-text padding-0-5-important\"></i>\r\n                                            ";
        echo $this->lang->line("save");
        echo "                                        </button>\r\n                                    </li>\r\n                                    <li>\r\n                                        <div class=\"pull-right\">\r\n                                            <div class=\"dropdown more pull-right\">\r\n                                                <button data-toggle=\"dropdown\" class=\"save-button-blue btn button-blue-color btn-info\" id=\"cases-actions-button\">\r\n                                                    <i class=\"icon-alignment fa fa-sliders white-text padding-0-5-important\"></i>\r\n                                                    ";
        echo $this->lang->line("actions");
        echo "                                                </button>\r\n                                                <div aria-labelledby=\"dLabel\" role=\"menu\" class=\"dropdown-menu dropdown-menu-right sub-menu-option\">\r\n                                                    <a class=\"dropdown-item\" href=\"";
        echo site_url("cases/export_to_word/" . $legalCase["id"]);
        echo "\" onclick=\"return isFormChanged();\">";
        echo $this->lang->line("export_to_word");
        echo "</a>\r\n                                                    ";
        if ($partnersCommissions == "yes") {
            echo "                                                        <a class=\"dropdown-item\" href=\"javascript:;\" onclick=\"caseCommissions('";
            echo $legalCase["id"];
            echo "', event);\">";
            echo $this->lang->line("partners_shares");
            echo "</a>\r\n                                                    ";
        }
        echo "                                                    ";
        if ($slaFeature == "yes") {
            echo "                                                        <a class=\"dropdown-item\" href=\"javascript:;\" onclick=\"slaShowLogs('";
            echo $legalCase["id"];
            echo "', event);\">";
            echo $this->lang->line("show_sla_elapsed_time");
            echo "</a>\r\n                                                    ";
        }
        echo "                                                    ";
        if ($legalCase["category"] == "Matter") {
            echo "                                                        <a class=\"dropdown-item\" href=\"javascript:;\" onclick=\"convertToLitigation('";
            echo $legalCase["id"];
            echo "', '";
            echo $legalCase["case_type_id"];
            echo "', '";
            echo $legalCase["legal_case_stage_id"];
            echo "', false, true);\">";
            echo $this->lang->line("convert_to_litigation");
            echo "</a>\r\n                                                    ";
        }
        echo "                                                    ";
        if ($legalCase["channel"] != "CP") {
            echo "                                                        <a class=\"dropdown-item\" href=\"javascript:;\" id=\"show-hide-btn\" onclick=\"showMatterInCustomerPortal('";
            echo $legalCase["id"];
            echo "', event);\">";
            echo $legalCase["visibleToCP"] ? $this->lang->line("hide_matter_in_customer_portal") : $this->lang->line("show_matter_in_customer_portal");
            echo "</a>\r\n                                                    ";
        }
        echo "                                                    <a class=\"dropdown-item\" id=\"archive-unarchive-btn\" href=\"javascript:;\" onclick=\"archiveUnarchiveCase('";
        echo $legalCase["id"];
        echo "', '";
        echo $legalCase["archived"];
        echo "');\" >";
        echo $legalCase["archived"] == "no" ? $this->lang->line("archive") : $this->lang->line("unarchive");
        echo "</a>\r\n                                                    <a class=\"dropdown-item\" id=\"delete-case\" href=\"javascript:;\" onclick=\"recommendMatterClosure('";        echo $legalCase["id"];        echo "',event);\">"; 
        echo $this->lang->line("recommend_closure");
        echo "</a>\r\n  
         <a class=\"dropdown-item\" id=\"delete-case\" href=\"javascript:;\" onclick=\"deleteCaseRecord('";
        echo $legalCase["id"];
        echo "');\">";
        echo $this->lang->line("delete");
        echo "</a>\r\n                                                </div>\r\n                                            </div>\r\n                                        </div>\r\n                                    </li>\r\n                                </ul>\r\n                            ";
    }
    echo "                        </div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        ";
}
echo "    </div>\r\n</nav>\r\n<script>\r\n    let positionMatterTitle = 'top-left';\r\n    if (_lang.languageSettings['langDirection'] === 'rtl'){\r\n        positionMatterTitle = 'top-right';\r\n    }\r\n    jQuery('#matter-title').tooltipster({\r\n        position: positionMatterTitle,\r\n        contentAsHTML: true,\r\n        timer: 22800,\r\n        animation: 'grow',\r\n        delay: 200,\r\n        theme: 'tooltipster-default',\r\n        touchDevices: false,\r\n        trigger: 'hover',\r\n        maxWidth: 350,\r\n        interactive: true\r\n    });\r\n</script>\r\n";

?>