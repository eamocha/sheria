<div class="container-fluid">
    <div class="row">
        <ul class="breadcrumb">
            <li class="active"><?php echo $this->lang->line("administration");?></li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-4 no-margin">
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/company.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("companies");?></label></div>
            <div class="offset-md-1 col-md-11 no-padding">
                <ul>
                    <li><a href="contact_company_categories"><?php echo $this->lang->line("contact_company_categories");?></a></li>
                    <li><a href="contact_company_sub_categories"><?php echo $this->lang->line("contact_company_sub_categories");?></a></li>
                    <li><a href="company_legal_types"><?php echo $this->lang->line("company_legal_types"); ?></a></li>
                    <li><a href="company_ss_discharge_types"><?php echo $this->lang->line("company_discharge_types");?></a></li>
                    <li><a href="board_member_roles"><?php echo $this->lang->line("board_members_roles");?></a></li>
                    <li><a href="custom_fields/companies"><?php echo $this->lang->line("custom_fields"); ?></a></li>
                    <li><a href="company_asset_types"><?php echo $this->lang->line("company_asset_types");?></a></li>
                    <li><a href="custom_fields/company_asset"><?php echo $this->lang->line("company_asset_custom_fields"); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block core-access">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/litigation.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("litigation_data");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding"><ul>
                    <li><a href="court_types"><?php echo $this->lang->line("court_types");?></a></li>
                    <li><a href="court_degrees"><?php echo $this->lang->line("court_degrees");?></a></li>
                    <li><a href="court_regions"><?php echo $this->lang->line("court_regions");?></a></li>
                    <li><a href="courts"><?php echo $this->lang->line("courts");?></a></li>
                    <li><a href="hearing_types"><?php echo $this->lang->line("hearing_types"); ?></a></li>
                    <li><a href="hearing_outcome_reasons"><?php echo $this->lang->line("reason_of_win_or_lose"); ?></a></li>
                    <li><a href="stage_statuses"><?php echo $this->lang->line("stage_statuses"); ?></a></li>
                    <li><a href="case_opponent_positions"><?php echo $this->lang->line("case_opponent_positions"); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/correspondences.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("correspondence");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="correspondence_types"><?php echo $this->lang->line("correspondence_types");?></a></li>
                    <li><a href="correspondence_document_types"><?php echo $this->lang->line("correspondence_document_types");?></a></li>
                    <li><a href="correspondence_statuses"><?php echo $this->lang->line("correspondence_statuses");?></a></li>
                    <li><a href="correspondence_workflows"><?php echo $this->lang->line("workflows");?></a></li>
                    <li><a href="correspondence_workflow_steps"><?php echo $this->lang->line("workflow_steps");?></a></li>

                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/tasks.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("conveyancing");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>

                    <li><a href="conveyancing_instrument_type"><?php echo $this->lang->line("instrument_types");?></a></li>
                     <li><a href="conveyancing_transactions_types"><?php echo $this->lang->line("conveyancing_transaction_types");?></a></li>
                    <li><a href="conveyancing_document_status"><?php echo $this->lang->line("document_status");?></a></li>
                    <li><a href="conveyancing_document_types"><?php echo $this->lang->line("conveyancing_document_types");?></a></li>
                    <li><a href="conveyancing/manage_workflows"><?php echo $this->lang->line("manage_workflows");?></a></li>

                </ul>
            </div>
        </div>

        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/reminders.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("reminders");?></label> </div>
            <div class="col-md-11 offset-md-1 no-padding"><ul>
                    <li><a href="reminder_types"><?php echo $this->lang->line("reminder_types");?></a></li>
                </ul>
            </div>
        </div>

        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/system_maintenance.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("system_maintenance");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding"><ul>    <?php if (!$this->cloud_installation_type) {?>
                        <li><a href="license_manager/install"><?php     echo $this->lang->line("license");    ?></a></li><?php }
                    if ($this->db->dbdriver == "mysqli") {?>
                        <li><a href="maintenance/backup"><?php     echo $this->lang->line("backup_database");    ?></a></li>
                            <!--<li><a href="maintenance/restore"><?php    echo $this->lang->line("restore_database");    ?></a></li>--><?php   }?>
                </ul></div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/system_preferences.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("system_preferences");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="system_preferences"><?php echo $this->lang->line("default_values");?></a></li>
                    <li><a href="system_preferences/email_notification_scheme"><?php echo $this->lang->line("notification_scheme");?></a></li>
                    <li><a href="email_templates"><?php echo $this->lang->line("email_templates");?></a></li>
                    <li><a href="look_feel"><?php echo $this->lang->line("look_feel");?></a></li>
                    <li><a href="integrations"><?php echo $this->lang->line("integrations");?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/sso.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("sso");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="saml_sso"><?php echo $this->lang->line("setup");?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/outlook.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("outlook_configuration");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="outlook_configurations/index"><?php echo $this->lang->line("setup");?></a></li>
                </ul>
            </div>
        </div>
        <?php if ($this->allowFeatureCustomerPortal) {?>
            <div class="home-block">
                <div class="col-md-9 mb-10"><img src="assets/images/icons/64/customer_portal.png" width="40" height="40" class="" hspace="6" /> <label ><?php     echo $this->lang->line("customer_portal");     ?></label></div>
                <div class="col-md-11 offset-md-1 no-padding">
                    <ul>
                        <li><a href="customer_portal/users"><?php     echo $this->lang->line("manageCustomers");    ?></a></li>
                        <li><a href="customer_portal/customers_by_companies"><?php     echo $this->lang->line("customers_by_companies");    ?></a></li>
                        <li class="core-access"><a href="request_type_categories"><?php    echo $this->lang->line("request_type_categories");    ?></a></li>
                        <li class="core-access"><a href="customer_portal/portal_screens"><?php     echo $this->lang->line("portal_screens");    ?></a></li>
                        <li class="core-access"><a href="customer_portal/portal_permissions"><?php     echo $this->lang->line("portal_permissions");    ?></a></li>
                        <li class="core-access"><a href="modules/contract/contract_request_type_categories"><?php     echo $this->lang->line("contract_request_type_categories");    ?></a></li>
                        <li class="contract-access"><a href="<?php     echo app_url("modules/contract/customer_portal/screens");?>"><?php     echo $this->lang->line("contracts_portal_screens");    ?></a></li>
                        <li class="contract-access"><a href="<?php     echo app_url("modules/contract/customer_portal/permissions");   ?>"><?php echo $this->lang->line("contracts_portal_permissions");     ?></a></li>
                    </ul>
                </div>
            </div>
        <?php }?>
    </div>
    <div class="col-md-4 no-margin">
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/contact.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("contacts");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding"><ul>
                    <li><a href="contact_company_categories"><?php echo $this->lang->line("contact_company_categories");?></a></li>
                    <li><a href="contact_company_sub_categories"><?php echo $this->lang->line("contact_company_sub_categories");?></a></li>
                    <li><a href="custom_fields/contacts"><?php echo $this->lang->line("custom_fields");?></a></li>
                    <li><a href="titles"><?php echo $this->lang->line("titles");?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/tasks.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("tasks");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="task_types"><?php echo $this->lang->line("task_types");?></a></li>
                    <li><a href="assignments/tasks"><?php echo $this->lang->line("task_assignment");?></a></li>
                    <li><a href="task_statuses"><?php echo $this->lang->line("task_statuses");?></a></li>
                    <li><a href="task_locations"><?php echo $this->lang->line("locations");?></a></li>
                    <li><a href="custom_fields/tasks"><?php echo $this->lang->line("custom_fields");?></a></li>
                    <li><a href="task_workflows/index"><?php echo $this->lang->line("manage_workflows");?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/tasks.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("legal_opinions");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="opinion_types"><?php echo $this->lang->line("opinion_types");?></a></li>
                    <li><a href="assignments/opinions"><?php echo $this->lang->line("opinion_assignment");?></a></li>
                    <li><a href="opinion_statuses"><?php echo $this->lang->line("opinion_statuses");?></a></li>
                    <li><a href="opinion_locations"><?php echo $this->lang->line("locations");?></a></li>
                    <li><a href="custom_fields/opinions"><?php echo $this->lang->line("custom_fields");?></a></li>
                    <li><a href="opinion_workflows/index"><?php echo $this->lang->line("manage_workflows");?></a></li>
                    <li><a href="opinion_document_types"><?php echo $this->lang->line("opinion_document_types");?></a></li>
                    <li><a href="opinion_document_statuses"><?php echo $this->lang->line("opinion_document_statuses");?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/meeting.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("dashboard_admin_meetings");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="task_locations"><?php echo $this->lang->line("locations");?></a></li>
                    <li><a href="event_types"><?php echo $this->lang->line("dashboard_admin_meeting_type");?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/time.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("time_tracking");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding"><ul>
                    <li><a href="time_types"><?php echo $this->lang->line("time_types");?></a></li>
                    <li><a href="time_internal_statuses"><?php echo $this->lang->line("time_internal_statuses");?></a></li>
                    <li><a href="manage_non_business_days"><?php echo $this->lang->line("manage_non_business_days");?></a></li>
                    </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/user_and_permissions.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("users_and_permissions");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="user_groups/"><?php echo $this->lang->line("user_groups");?></a></li>
                    <li><a href="users/"><?php echo $this->lang->line("manage_users");?></a></li>
                    <?php if ($adEnabled == 1) {?>
                        <li><a href="users/import_from_ad"><?php    echo $this->lang->line("manage_ad_users");    ?></a></li>
                    <?php } if ($enabled_idp) {   ?>
                        <li><a href="saml_sso/import_users"><?php     echo sprintf($this->lang->line("import_users_idp"), $this->lang->line($enabled_idp));    ?></a></li>
                    <?php }?>
                    <li><a href="user_groups/permissions"><?php echo $this->lang->line("user_groups_permissions");?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-11 mb-10"><img src="assets/images/icons/64/user_management_report.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("user_management_reports");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="users/audit_reports"><?php echo $this->lang->line("users_audit_report");?></a></li>
                    <li><a href="users/login_history_report"><?php echo $this->lang->line("login_history_report");?></a></li>
                    <li class="d-none"><a href="user_groups/management_report/"><?php echo $this->lang->line("user_group_management_report");?></a></li>
                    <li><a href="users/user_management_report"><?php echo $this->lang->line("user_management_report");?></a></li>
                    <?php if ($makerCheckerFeatureEnabled) {?>
                        <li><a href="users/maker_checker_report"><?php     echo $this->lang->line("maker_checker_users_report");    ?></a></li>
                        <li><a href="user_groups/maker_checker_report"><?php     echo $this->lang->line("maker_checker_user_groups_report");    ?></a></li>
                        <li><a href="user_groups/maker_checker_permissions_report"><?php     echo $this->lang->line("maker_checker_user_groups_permissions_report");    ?></a></li>
                    <?php }?>
                </ul>
            </div>
        </div>
        <div class="home-block">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/import.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("import_data");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="import_data/companies"><?php echo $this->lang->line("companies");?></a></li>
                    <li><a href="import_data/contacts"><?php echo $this->lang->line("contacts");?></a></li>
                    <li class="core-access"><a href="import_data/corporate_matters"><?php echo $this->lang->line("matter_case_in_menu");?></a></li>
                    <li class="core-access"><a href="import_data/intellectual_properties"><?php echo $this->lang->line("intellectual_properties");?></a></li>
                    <li class="core-access"><a href="import_data/litigation_cases"><?php echo $this->lang->line("litigation_cases");?></a></li>
                    <li class="core-access"><a href="import_data/matter_containers"><?php echo $this->lang->line("matter_containers");?></a></li>
                    <li><a href="import_data/tasks"><?php echo $this->lang->line("tasks");?></a></li>
                </ul>
            </div>
        </div>
        <?php if ($this->allowFeatureAdvisor) {?>
            <div class="home-block">
            <div class="col-md-9 mb-10">
                <img src="assets/images/icons/64/customer_portal.png" width="40" height="40" hspace="6"/>
                <label>   <?php    echo $this->lang->line("advisor_portal");?> </label>
            </div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="advisors/users"><?php    echo $this->lang->line("manage_advisors");?></a></li>
                    <li><a href="advisors/portal_permissions"><?php    echo $this->lang->line("advisor_workflow_permissions");?></a></li>
                    <li><a href="advisors/portal_task_workflow_permissions"> <?php   echo $this->lang->line("advisor_task_workflow_permissions");?></a></li>
                    <li><a href="advisors/manage_email_templates">  <?php   echo $this->lang->line("manage_advisors_email_templates");?>              </a></li>
                    <li> <a href="advisor_task_workflows/index">   <?php    echo $this->lang->line("manage_workflows");?> </a> </li>
                </ul>
            </div>
            </div><?php } ?>
    </div>
    <div class="col-md-4 no-margin">
        <div class="home-block core-access">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/case.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("cases");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding"><ul>
                    <li><a href="case_client_positions"><?php echo $this->lang->line("case_client_positions");?></a></li>
                    <li><a href="case_success_probabilities"><?php echo $this->lang->line("case_success_probabilities");?></a></li>
                    <li><a href="case_company_roles"><?php echo $this->lang->line("case_company_roles");?></a></li>
                    <li><a href="case_contact_roles"><?php echo $this->lang->line("case_contact_roles");?></a></li>
                    <li><a href="case_container_statuses"><?php echo $this->lang->line("case_container_statuses");?></a></li>
                    <li><a href="case_stages"><?php echo $this->lang->line("case_stages");?></a></li>
                    <li><a href="case_types"><?php echo $this->lang->line("SLAcase_types");?></a></li>
                    <li><a href="assignments/cases"><?php echo $this->lang->line("case_assignment");?></a></li>
                    <li><a href="custom_fields/cases"><?php echo $this->lang->line("custom_fields");?></a></li>
                    <li><a href="reports/case_value_tiers_configure"><?php echo $this->lang->line("case_value_tiers");?></a></li>
                    <li><a href="manage_workflows/statuses"><?php echo $this->lang->line("manage_workflows");?></a></li>
                    <?php if ($AllowFeatureSLAManagement) {?>
                        <li><a href="sla_management"><?php    echo $this->lang->line("sla_management");   ?></a></li>
                    <?php }?>
                    <li><a href="legal_case_event_types"><?php echo $this->lang->line("event_types");?></a></li>
                </ul>
            </div>
        </div>
        <div class="home-block contract-access">
            <div class="col-md-9 mb-10"><img src="assets/images/icons/64/case.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("contracts");?></label></div>
            <div class="col-md-11 offset-md-1 no-padding">
                <ul>
                    <li><a href="<?php echo app_url("modules/contract/contract_types");?>"><?php echo $this->lang->line("contract_types");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/dashboard/boards");?>"><?php echo $this->lang->line("contract_boards");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/contract_templates");?>"><?php echo $this->lang->line("contract_templates");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/sub_contract_types");?>"><?php echo $this->lang->line("sub_contract_types");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/party_categories");?>"><?php echo $this->lang->line("party_categories");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/contract_workflow_steps");?>"><?php echo $this->lang->line("contract_workflow_steps");?></a></li>
                        <li><a href="modules/contract/contract_workflows/index"><?php echo $this->lang->line("manage_workflows");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/approval_center/index");?>"><?php echo $this->lang->line("approval_center");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/signature_center/index");?>"><?php echo $this->lang->line("signature_center");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/custom_fields");?>"><?php echo $this->lang->line("custom_fields");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/applicable_laws");?>"><?php echo $this->lang->line("applicable_laws");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/contract_document_statuses");?>"><?php echo $this->lang->line("contract_document_status");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/contract_document_types");?>"><?php echo $this->lang->line("contract_document_type");?></a></li>
                        <li><a href="<?php echo app_url("modules/contract/contract_document_generator");?> "><?php echo $this->lang->line("contract_document_generator_templates_folder");?></a></li>
                    <li><a href="<?php echo app_url("modules/contract/folder_templates");?>"><?php echo $this->lang->line("folder_templates");?></a></li>
                    <li><a href="<?php echo app_url("modules/contract/clauses");?>"><?php echo $this->lang->line("contract_clauses");?></a></li>
                    <li><a href="<?php echo app_url("modules/contract/other_settings");?>"><?php echo $this->lang->line("other_settings");?></a></li>
                    <?php  if ($AllowContractSLAManagement) {?>
                        <li><a href="<?php     echo app_url("modules/contract/sla_management");?>"><?php    echo $this->lang->line("sla_management");    ?></a></li>
                        <?php }?>
                        <li><a href="javascript:;" onclick="docuSignGoLive();"><?php echo $this->lang->line("docusign_go_live_action");?></a></li>
                    </ul>
                </div>
            </div>
            <div class="home-block core-access">
                <div class="col-md-9 mb-10"><img src="assets/images/icons/64/ip.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("IP");?></label></div>
                <div class="col-md-11 offset-md-1 no-padding"><ul>
                        <li><a href="intellectual_property_rights"><?php echo $this->lang->line("IPR");?></a></li>
                        <li><a href="ip_classes"><?php echo $this->lang->line("ip_classes");?></a></li>
                        <li><a href="ip_subcategories"><?php echo $this->lang->line("ip_subcategories");?></a></li>
                        <li><a href="ip_statuses"><?php echo $this->lang->line("ip_statuses");?></a></li>
                        <li><a href="ip_names"><?php echo $this->lang->line("ip_names");?></a></li>
                        <li><a href="ip_petition_opposition_types"><?php echo $this->lang->line("petition_opposition_types");?></a></li>
                    </ul>
                </div>
            </div>
            <div class="home-block">
                <div class="col-md-9 mb-10"><img src="assets/images/icons/64/dashboard.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("manage_boards");?></label></div>
                <div class="col-md-11 offset-md-1 no-padding">
                    <ul>
                        <li class="core-access"><a href="dashboard/case_boards"><?php echo $this->lang->line("case_boards");?></a></li>
                        <li><a href="dashboard/task_boards"><?php echo $this->lang->line("task_boards");?></a></li>
                        <li class="core-access"><a href="dashboard/litigation_dashboard_config/1"><?php echo $this->lang->line("litigation_management_dashboard_1");?></a></li>
                        <li class="core-access"><a href="dashboard/litigation_dashboard_config/2"><?php echo $this->lang->line("litigation_management_dashboard_2");?></a></li>
                    </ul>
                </div>
            </div>
            <div class="home-block">
                <div class="col-md-9 mb-10"><img src="assets/images/icons/64/documents.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("documents");?></label></div>
                <div class="col-md-11 offset-md-1 no-padding">
                    <ul>
                        <li class="core-access"><a href="case_document_classifications"><?php echo $this->lang->line("case_document_classifications");?></a></li>
                        <li class="core-access"><a href="case_document_types"><?php echo $this->lang->line("case_document_types");?></a></li>
                        <li class="core-access"><a href="case_document_statuses"><?php echo $this->lang->line("case_document_statuses");?></a></li>
                        <li class="core-access"><a href="<?php echo site_url("legal_case_container_document_types");?>"><?php echo $this->lang->line("legal_case_container_document_types");?></a></li>
                        <li class="core-access"><a href="<?php echo site_url("legal_case_container_document_statuses");?>"><?php echo $this->lang->line("legal_case_container_document_statuses");?></a></li>
                        <li><a href="docs_document_types"><?php echo $this->lang->line("docs_document_types");?></a></li>
                        <li><a href="docs_document_statuses"><?php echo $this->lang->line("docs_document_statuses");?></a></li>
                        <li><a href="company_document_types"><?php echo $this->lang->line("company_document_types");?></a></li>
                        <li><a href="company_document_statuses"><?php echo $this->lang->line("company_document_statuses");?></a></li>
                        <li><a href="contact_document_types"><?php echo $this->lang->line("contact_document_types");?></a></li>
                        <li><a href="contact_document_statuses"><?php echo $this->lang->line("contact_document_statuses");?></a></li>
                        <li><a href="document_generator"><?php echo $this->lang->line("document_generator_templates_folder");?></a></li>
                        <li class="core-access"><a href="hearing_report_generator"><?php echo $this->lang->line("hearing_report_generator_templates_folder");?></a></li>
                        <li class="core-access"><a href="case_folder_templates"><?php echo $this->lang->line("case_folder_templates");?></a></li>
                    </ul>
                </div>
            </div>
            <div class="home-block">
                <div class="col-md-9 mb-10"><img src="assets/images/icons/64/teams.png" width="40" height="40" class="" hspace="6" /> <label ><?php echo $this->lang->line("teams");?></label></div>
                <div class="col-md-11 offset-md-1 no-padding">
                    <ul>
                        <li><a href="provider_groups"><?php echo $this->lang->line("provider_groups");?></a></li>
                        <li><a href="seniority_levels"><?php echo $this->lang->line("seniority_level");?></a></li>
                        <li><a href="departments"><?php echo $this->lang->line("departments");?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
