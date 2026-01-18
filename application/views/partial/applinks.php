<?php

$current_url = current_url() . "/";
if ($this->is_auth->is_logged_in()) {
    echo "
      
          <ul class=\"nav navbar-nav\"  id=\"top-nav-item-list\">
      <li class=\"nav-item navBorderRight nav-li-hover\" id=\"dashboard-menu-demo-open\" >
                  <a id=\"dashboard-menu-demo\" class=\"parent-active-title dashboard-menu-demo\" aria-expanded=\"false\" aria-haspopup=\"true\" role=\"button\" data-toggle=\"dropdown\" class=\"dropdown-toggle\" href=\"#\">";
    echo $this->lang->line("dashboard_in_menu");
    echo "&nbsp; <i class=\"fa fa-caret-down\"></i></a>
                  <ul id=\"dashboard-menu-list-demo\" class=\"dropdown-menu dashboard-menu-list-demo\">
                      <li>
                          <a class=\"";
    echo $current_url == app_url("dashboard") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("dashboard");
    echo "\">";
    echo $this->lang->line("my_dashboard");
    echo "</a>
                      </li>
                      <li><a class=\"contract-access ";
    echo $current_url == app_url("modules/contract/dashboard") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("modules/contract/dashboard");
    echo "\">";
    echo $this->lang->line("dashboard_contract");
    echo "</a></li>
                      <li><a class=\"";
    echo $current_url == app_url("dashboard/time_tracking_dashboard") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("dashboard/time_tracking_dashboard");
    echo "\">";
    echo $this->lang->line("time_tracking_dashboard");
    echo "</a></li>
                      <li><a class=\"core-access ";
    echo $current_url == app_url("dashboard/management") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("dashboard/management");
    echo "\">";
    echo $this->lang->line("dashboard_management");
    echo "</a></li>
                      <li><a class=\"core-access ";
    echo $current_url == app_url("dashboard/litigation_dashboard/1") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("dashboard/litigation_dashboard/1");
    echo "\">";
    echo $this->lang->line("litigation_management_dashboard_1");
    echo "</a></li>
                      <li><a class=\"core-access ";
    echo $current_url == app_url("dashboard/litigation_dashboard/2") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("dashboard/litigation_dashboard/2");
    echo "\">";
    echo $this->lang->line("litigation_management_dashboard_2");
    echo "</a></li>
                      <li id=\"dashboard-menu-list-demo-kanban\" style=\"width: 100%;position: absolute;\">
                          <li><a class=\"core-access ";
    echo $current_url == app_url("dashboard/cases") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("dashboard/cases");
    echo "\">";
    echo $this->lang->line("case_board");
    echo "</a></li>
                          <li><a class=\"contract-access ";
    echo $current_url == site_url("modules/contract/dashboard/contracts") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("modules/contract/dashboard/contracts");
    echo "\">";
    echo $this->lang->line("contract_board");
    echo "</a></li>
                          <li><a class=\"";
    echo $current_url == app_url("dashboard/tasks") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("dashboard/tasks");
    echo "\">";
    echo $this->lang->line("task_board");
    echo "</a></li>
                      </li>
                  </ul>
              </li>
              <li class=\"dropdown navBorderRight nav-li-hover\" id=\"contacts-menu-demo-open\">
                  <a aria-expanded=\"false\" aria-haspopup=\"true\" role=\"button\" data-toggle=\"dropdown\" class=\"dropdown-toggle\" href=\"#\" class=\"";
    echo $current_url == app_url("contacts") || $current_url == app_url("companies") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("contacts");
    echo "\">";
    echo $this->lang->line("contact_in_menu");
    echo "&nbsp; <i class=\"fa fa-caret-down\"></i></a>
                  <ul class=\"dropdown-menu\">
                      <li id=\"contacts-menu-demo\"><a class=\"";
    echo $current_url == app_url("contacts") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("contacts");
    echo "\">";
    echo $this->lang->line("contacts");
    echo "</a></li>
                      <li id=\"companies-menu-demo\"><a class=\"";
    echo $current_url == app_url("companies") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("companies");
    echo "\">";
    echo $this->lang->line("companies");
    echo "</a></li>
                  </ul>
              </li>
              <li class=\"dropdown navBorderRight nav-li-hover core-access\" id=\"matter-menu-demo-open\">
                  <a class=\"parent-active-title\" aria-expanded=\"false\" aria-haspopup=\"true\" role=\"button\" data-toggle=\"dropdown\" class=\"dropdown-toggle\" href=\"#\">";
    echo $this->lang->line("case_in_menu");
    echo "&nbsp; <i class=\"fa fa-caret-down\"></i></a>
                  <ul class=\"dropdown-menu\">
                      <li id=\"matter-menu-list-demo\"><a class=\"";
    echo $current_url == app_url("cases/legal_matter/") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("cases/legal_matter/");
    echo "\">";
    echo $this->lang->line("matter_case_in_menu");
    echo "</a></li>
                      <li id=\"litigation-menu-list-demo\"><a class=\"";
    echo $current_url == app_url("cases/litigation_case") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("cases/litigation_case");
    echo "\">";
    echo $this->lang->line("litigation_cases");
    echo "</a></li>
                      <li><a class=\"";
    echo $current_url == app_url("case_containers") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("case_containers");
    echo "\">";
    echo $this->lang->line("list_case_containers");
    echo "</a></li>
                      <li class=\"divider\"></li>
                      <li><a class=\"";
    echo $current_url == app_url("cases/my_hearings") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("cases/my_hearings");
    echo "\">";
    echo $this->lang->line("hearings");
    echo "</a></li>
                  </ul>
              </li>
              <li class=\"dropdown navBorderRight nav-li-hover contract-access\">
                  <a class=\"parent-active-title\" aria-expanded=\"false\" aria-haspopup=\"true\" role=\"button\" data-toggle=\"dropdown\" class=\"dropdown-toggle parent-active-title\" href=\"#\">";
    echo $this->lang->line("contracts");
    echo "&nbsp; <i class=\"fa fa-caret-down\"></i></a>
                  <ul class=\"dropdown-menu\">
                      <li><a class=\"";
    echo current_url() == app_url("modules/contract/") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("modules/contract/");
    echo "\">";
    echo $this->lang->line("list_contracts");
    echo "</a></li>
                      <li><a class=\"";
    echo $current_url == app_url("modules/contract/clauses") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("modules/contract/clauses");
    echo "\">";
    echo $this->lang->line("clauses_library");
    echo "</a></li>
                      <li><a class=\"";
    echo $current_url == app_url("modules/contract/contracts/awaiting_approvals") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("modules/contract/contracts/awaiting_approvals");
    echo "\">";
    echo $this->lang->line("awaiting_approvals");
    echo "</a></li>
                      <li><a class=\"";
    echo $current_url == app_url("modules/contract/contracts/awaiting_signatures") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("modules/contract/contracts/awaiting_signatures");
    echo "\">";
    echo $this->lang->line("awaiting_signatures");
    echo "</a></li>
                      </ul>
                                                          </li> ";
                                    ?>  
                        <li class="dropdown navBorderRight nav-li-hover contract-access">
                  <a class="parent-active-title" aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle parent-active-title" href="#">
				  <?php    echo $this->lang->line("contracts");?>&nbsp; <i class="fa fa-caret-down"></i></a>
				  <ul class="dropdown-menu">
				  <li><a class="<?php     echo current_url() == app_url("modules/contract/") ? "active-title" : "";    ?>"  tabindex="-1" href="<?php    echo app_url("modules/contract/");?>"><?php    echo $this->lang->line("list_contracts");?></a></li>
				  <li><a class="<?php    echo $current_url == app_url("modules/contract/clauses") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("modules/contract/clauses");?>"><?php     echo $this->lang->line("clauses_library");?></a></li>
				  <li><a class="<?php     echo $current_url == app_url("modules/contract/contracts/awaiting_approvals") ? "active-title" : "";?>" tabindex="-1" href="<?php     echo app_url("modules/contract/contracts/awaiting_approvals");    ?>"><?php     echo $this->lang->line("awaiting_approvals");?></a></li>
				  <li><a class="<?php    echo $current_url == app_url("modules/contract/contracts/awaiting_signatures") ? "active-title" : "";?>" tabindex="-1" href="<?php    echo app_url("modules/contract/contracts/awaiting_signatures");    ?>"><?php    echo $this->lang->line("awaiting_signatures");  ?></a></li>
				  </ul>
				  </li>
                  
     <?php echo "       <li id=\"time-menu-demo-open\"><a class=\"";
    echo $current_url == app_url("time_tracking/my_time_entries") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("time_tracking/my_time_entries");
    echo "\">";
    echo $this->lang->line("time_tracking_in_menu");
    echo "</a></li>
              <li class=\"navBorderRight nav-li-hover\"> <a id=\"task-menu-demo\" class=\"";    echo $current_url == app_url("tasks/my_tasks") ? "active-title" : "";    echo "\" href=\"";    echo app_url("tasks/my_tasks");    echo "\">";   echo $this->lang->line("task_in_menu");    echo "</a>              </li>              ";
    $activeInstalledModules = $this->activeInstalledModules;
    $activeInstalledModules["money"] = "money";
    unset($activeInstalledModules["outlook"]);
    unset($activeInstalledModules["customer-portal"]);
    unset($activeInstalledModules["A4G"]);
    unset($activeInstalledModules["microsoft-teams"]);
    unset($activeInstalledModules["configuration"]);
    unset($activeInstalledModules["advisor-portal"]);
    unset($activeInstalledModules["contract"]);
    foreach ($activeInstalledModules as $module => $moduleName) {
        $class_active_module = current_url() == app_url("modules/" . $module) ? "active-title" : "";
        echo "<li class=\"navBorderRight nav-li-hover core-access\"><a class=\"" . $class_active_module . "\" tabindex=\"-1\" href=\"" . app_url("modules/" . $module) . "\">" . $this->lang->line($module) . "</a></li>";
    }
    echo "        <li class=\"navBorderRight nav-li-hover\">
                  <a id=\"docs-menu-demo\" class=\"";
    echo $current_url == app_url("docs") ? "active-title" : "";
    echo "\" href=\"";
    echo app_url("docs");
    echo "\">";
    echo $this->lang->line("docs");
    echo "</a>
              </li>
              <li class=\"dropdown navBorderRight nav-li-hover\">
                  <a aria-expanded=\"false\" aria-haspopup=\"true\" role=\"button\" data-toggle=\"dropdown\" class=\"dropdown-toggle dropdown-nav-more-override font-size21 parent-active-title\" href=\"#\"><span class=\"showOnMobileText\">More</span> <i class=\"fa fa-angle-double-override header-drop-down-2-arrows\"></i></a>
                  <ul class=\"dropdown-menu\">
                      <li><a class=\"";
    echo $current_url == app_url("reports") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("reports");
    echo "\">";
    echo $this->lang->line("reports");
    echo "</a></li>
                      <li><a class=\"core-access ";
    echo $current_url == app_url("intellectual_properties") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("intellectual_properties");
    echo "\">";
    echo $this->lang->line("IP");
    echo "</a></li>
                      <li class=\"divider\"></li>";
		//<!-------- additional menu  --!>
	echo "<li><a class=\"core-access ";
    echo $current_url == app_url("legal_opinions") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("legal_opinion");
    echo "\">";
    echo $this->lang->line("legal_opinion");
    echo "</a></li> <li class=\"divider\"></li>" ;
	
	echo "<li><a class=\"core-access ";
    echo $current_url == app_url("conveyancing") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("legal_opinion");
    echo "\">";
    echo $this->lang->line("convenyancing");
    echo "</a></li> <li class=\"divider\"></li>" ;
	
	echo "<li><div  class=\"favorites\">";
    echo $this->lang->line("favorites");
    echo "</div></li>
                      <li><a class=\"core-access ";
    echo $current_url == app_url("modules/money/vouchers/my_expenses_list") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"";
    echo app_url("modules/money/vouchers/my_expenses_list");
    echo "\">";
    echo $this->lang->line("my_expenses");
    echo "</a></li>
                      <li><a class=\"";
    echo $current_url == app_url("my_reminders") ? "active-title" : "";
    echo "\" tabindex=\"-1\" href=\"reminders/show_my_reminders\">";
    echo $this->lang->line("my_reminders");
    echo "</a></li>
                      <li class=\"divider\"></li>
                      <li class=\"dropdown-submenu\">
                          <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\"><span class=\"nav-label\">";
    echo $this->lang->line("ExternalLinksTitle");
    echo "</span></a>
                          ";
    $systemPreferences = $this->session->userdata("systemPreferences");
    echo "                    ";
    $custom_link_count = 0;
    echo "                    <ul class=\"dropdown-menu dropdown-to-right\">
                          ";
    for ($i = 1; $i <= 10; $i++) {
        echo "                        ";
        $menu_url = "menu_url_" . $i;
        echo "                        ";
        $custom_link = $this->lang->line("custom_link") . "&nbsp;" . $i;
        echo "                        ";
        $systemPreferences[$menu_url] = (array) (empty($systemPreferences[$menu_url]) ? array_fill_keys(["title", "target"], "") : unserialize($systemPreferences[$menu_url]));
        echo "                        ";
        if ($systemPreferences[$menu_url]["target"] != "") {
            echo "                            ";
            $custom_link_count = 1;
            echo "                            <li><a href=\"";
            echo $systemPreferences[$menu_url]["target"];
            echo "\" target=\"_blank\">";
            echo $systemPreferences[$menu_url]["title"] == "" ? $custom_link : $systemPreferences[$menu_url]["title"];
            echo "</a></li>
                              ";
        }
        echo "                    ";
    }
    echo "                    ";
    if ($custom_link_count === 0) {
        echo "                        <li><div class=\"favorites\">";
        echo $this->lang->line("empty");
        echo "</div></li>
                          ";
    }
    echo "                    </ul>
                      </li>
                  </ul>
              </li>
      
              <li class=\"dropdown header-add-tab\" id=\"top-menu-add-list\">
                  <button id=\"top-menu-add-button\" type=\"button\" name=\"quickAddButton\" class=\"btn dropdown-toggle add-header-button menu_add_new\" data-toggle=\"dropdown\">";
    echo $this->lang->line("new");
    echo " <span class=\"fa fa-plus\"></span><span class=\"sr-only\">Toggle Dropdown</span></button>
                  <ul class=\"dropdown-menu quick-add-container\">
                      <li><a id=\"top-menu-add-button-company\" href=\"javascript:;\" onclick=\"companyAddForm();\"><div class=\"sprite sprite-company\"></div>";
    echo $this->lang->line("new_company");
    echo "</a></li>
                      <li><a id=\"top-menu-add-button-contact\"  href=\"javascript:;\" onclick=\"contactAddForm(true);\"><div class=\"sprite sprite-contact\"></div>";
    echo $this->lang->line("new_contact");
    echo "</a></li>
                      <li class=\"divider\"></li>
                      <li id=\"top-menu-add-button-matter\" class=\"core-access\"><a href=\"javascript:;\" onclick=\"legalMatterAddForm();\"><div class=\"sprite sprite-legal-matter\"></div>";
    echo $this->lang->line("corporate_matter");
    echo "</a></li>
                      <li class=\"divider core-access\"></li>
                      <li class=\"core-access\" id=\"top-menu-add-button-litigation\"><a href=\"javascript:;\" onclick=\"litigationCaseAddForm();\"><div class=\"sprite sprite-litigation-cases\"></div>";
    echo $this->lang->line("civil_case");
    echo "</a></li>
         <li class=\"divider core-access\"></li>
                      <li class=\"core-access\" id=\"top-menu-add-button-litigation\"><a href=\"javascript:;\" onclick=\"litigationCaseAddForm();\"><div class=\"sprite sprite-litigation-cases\"></div>";
    echo $this->lang->line("criminal_case");
    echo "</a></li>
                      <li class=\"core-access\"><a href=\"javascript:;\" onclick=\"legalCaseHearingForm();\"><div class=\"sprite sprite-hearing\"></div>";
    echo $this->lang->line("hearing");
    echo "</a></li>
                      <li class=\"divider core-access\"></li>
                      <li class=\"core-access\"><a href=\"javascript:;\" onclick=\"caseContainerForm();\"><div class=\"sprite sprite-matter-container\"></div>";
    echo $this->lang->line("case_container");
    echo "</a></li>
                      <li class=\"divider core-access\"></li>
                      <li class=\"contract-access\"><a href=\"javascript:;\" onclick=\"contractGenerate();\"><div class=\"sprite sprite-contract\"></div>";
    echo $this->lang->line("menu_contract");
    echo "</a></li>
                      <li class=\"divider core-access\"></li>
                      <li class=\"core-access\"><a href=\"javascript:;\" onclick=\"intellectualPropertyAddForm();\"><div class=\"sprite sprite-intellectual-property\"></div>";
    echo $this->lang->line("IP");
    echo "</a></li>
                      <li class=\"divider\"></li>
                      <li id=\"top-menu-add-button-task\"><a href=\"javascript:;\" onclick=\"taskAddForm();\"><div class=\"sprite sprite-task\"></div>";
    echo $this->lang->line("new_task");
    echo "</a></li>
                      <li><a href=\"javascript:;\" onclick=\"logActivityDialog();\"><div class=\"sprite sprite-log-time\"></div>";
    echo $this->lang->line("log_time");
    echo "</a></li>
                      <li class=\"core-access\"><a href=\"";
    echo app_url("modules/money/vouchers/expense_add");
    echo "\"><div class=\"sprite sprite-expense\"></div>";
    echo $this->lang->line("expense");
    echo "</a></li>
                      <li><a href=\"javascript:;\" onclick=\"meetingForm();\"><div class=\"sprite sprite-meeting\"></div>";
    echo $this->lang->line("meeting");
    echo "</a></li>
                      <li><a href=\"javascript:;\" onclick=\"reminderForm();\"><div class=\"sprite sprite-reminder\"></div>";
    echo $this->lang->line("new_reminder");
    echo "</a></li>
                  </ul>
              </li>
          </ul>
      
          ";
}

