<?php

class Is_Auth
{
    public $product = "App4Legal";
    public $salt = "";
    public $login_record_ip = true;
    public $login_record_time = true;
    protected $_login_logging_enabled = true;
    protected $_login_logging_threshold = "4";
    protected $_login_logging_levels = ["LOG" => "0", "ERROR" => "1", "DEBUG" => "2", "INFO" => "3", "ALL" => "4"];
    protected $_login_logging_log_path;
    protected $_login_logging_date_fmt = "Y-m-d H:i:s";
    public $autologin_cookie_name = "bmCMSsys";
    public $autologin_cookie_life = 1728000;
    public $excempted_uri;
    public $stay_signed_in = "";
    private $_auth_error;
    public $ci;
    const OTP_FEATURE_ENABLED_KEY = 'mfaEnabled';
    const OTP_EXPIRY_MINUTES_KEY = 'otpExpiryMinutes';
    public function __construct($params = NULL)
    {
        $this->ci =& get_instance();
        $this->_login_logging_log_path = $this->ci->config->item("files_path") . "/logs/login-history/";
     //   $this->excempted_uri = ["/advisors/get_contact_related_companies/", "/advisors/get_advisor_companies_modal/", "/advisors/upload_email_image/", "/contacts/add_email/", "/contacts/delete_email/", "/maintenance/export_specific_language_variables/", "/users/login/", "/users/change_password/", "/users/logout/", "/integrations/choose_default_folder/", "/accounts/fetch_account/", "/accounts/fetch_client_accounts/", "/accounts/lookup_client/", "/accounts/lookup_supplier/", "/accounts/lookup_partner/", "/case_containers/autocomplete/", "/cases/autocomplete/", "/clients/autocomplete/", "/partners/autocomplete/", "/companies/autocomplete/", "/compressed_asset/", "/contacts/autocomplete/", "/contacts/fetch/", "/home/", "/dashboard/index/", "/users/fetch/", "/pages/", "/sla_management/get_practice_area_by_workflow/", "/provider_groups/autocomplete/", "/opponents/", "/opponents/autocomplete/", "/tasks/autocomplete/", "/top_controller/", "/app_controller/", "/user_groups/read_controllers_actions/", "/users/autocomplete/", "/users/change_password/", "/users/profile/", "/vendors/autocomplete/", "/opponents/autocomplete/", "/reminders/show_my_reminders/", "/companies/discharge_type_autocomplete/", "/cron_jobs/", "/user_groups/check_actions_changes/", "/time_tracking/my_time_logs/", "/time_tracking/my_time_entries/", "/tasks/my_tasks/", "/companies/load_documents/", "/contacts/load_documents/", "/cases/load_documents/", "/intellectual_properties/load_documents/", "/docs/load_documents/", "/case_types/get_case_type_due_conditions/", "/system_preferences/add_remove_matter_contributors_from_trigger/", "/vouchers/bill_load_documents/", "/vouchers/expense_load_documents/", "/vouchers/invoice_load_documents/", "/vouchers/quote_load_documents/", "/dashboard/getting_started/", "/vouchers/my_expenses_list/", "/tasks/tasks_reported_by_me/", "/users/get_profile_picture/", "/cases/my_hearings/", "/cases/hearing_bulk_update_summary_to_client/", "/home/about_us/", "/home/confirm_request/", "/reminders/reminders_list/", "/notifications/", "/base/", "/calendar_integrations/", "/users/avatar_uploader/", "/cases/move_status/", "/system_preferences/check_usernames_compatibility/", "/cases/fetch_case_client/", "/home/hijri_date_converter/", "/alternative_login/", "/home/whats_new_popup/", "/contracts/load_shareholders_per_party/", "/folder_templates/page_actions/", "/grid_saved_columns/", "/calendars/calendars_list/", "/calendars/integrations_list/", "/legal_case_event_types/actions/", "/invoices/", "/core_controller/", "/users/get_api_token_data/", "/users/initial_setup/", "/users/user_guide/", "/contracts/load_docs_count/", "/contacts/get_contacts_by_company/", "/cases/show_children_documents_in_ap/", "/cases/add_client_and_update_case/", "/cases/change_litigation_stage_status/", "/cases/update_court_external_ref/", "/cases/delete_court_external_ref/", "/cases/add_court_external_ref/", "/cases/events_autocomplete/", "/cases/hearings_autocomplete/", "/vouchers/relate_matters_to_invoice/", "/vouchers/relate_matters_to_quote/", "/vouchers/convert_expense_billingStatus_to_invoice/", "/vouchers/convert_expense_billingStatus_to_quote/", "/vouchers/get_quote_items/", "/vouchers/list_client_quotes/", "/accounts/lookup/", "/accounts/get_account_details/", "/clients/load_trust_data/", "/vouchers/invoice_export_options/", "/vouchers/bill_export_options/", "/vouchers/invoice_export_description_table/", "/system_preferences/reset_expense_notification_system_params/", "/vouchers/get_expenses_need_approved/", "/cases/load_litigation_stage_forms/", "/cases/matter_stage_metadata/", "/cases/return_litigation_stages/", "/case_containers/return_cases_details/", "/look_feel/load_theme/", "/look_feel/restore_default/", "/look_feel/save/", "/look_feel/add/", "/manage_workflows/get_area_of_practice_by_category/", "/manage_workflows/migrate_matter_status/", "/sla_management/get_status_by_workflow/", "/reports/report_builder_list/", "/reports/report_builder_excel/", "/reports/report_builder_pdf/", "/cases/delete_document_hearing/", "/integrations/create_folder/", "/integrations/delete_document/", "/integrations/download_file/", "/integrations/dropbox_auth_callback/", "/integrations/check_access_token/", "/integrations/load_documents/", "/integrations/rename_document/", "/integrations/revoke_access_token/", "/integrations/update_document_tab/", "/integrations/upload_file/", "/timers/", "/cases/transition_screen_fields/", "/clients/update_tax_number/", "/cases/get_last_comment/", "/tasks/upload_file/", "/tasks/return_doc_thumbnail/", "/tasks/load_documents/", "/tasks/download_file/", "/tasks/preview_document/", "/tasks/view_document/", "/integrations/disable/", "/integrations/enable/", "/document_generator/add_default_generator_template/", "/tasks/delete_document/", "/tasks/comments/", "/tasks/transition_screen_fields/", "/tasks/move_status/", "/look_feel/restore_remove_image/", "/dashboard/set_money_language/", "/dashboard/switch_organization/", "/vendors/update_tax_number/", "/task_workflows/migrate_statuses/", "/case_containers/link_matter/", "/case_containers/load_container_fields/", "/cases/check_folder_privacy/", "/companies/check_folder_privacy/", "/contacts/check_folder_privacy/", "/docs/check_folder_privacy/", "/intellectual_properties/check_folder_privacy/", "/assignments/delete/", "/assignments/return_rules/", "/assignments/save/", "/cases/view_document/", "/companies/view_document/", "/contacts/view_document/", "/docs/view_document/", "/intellectual_properties/view_document/", "/case_folder_templates/page_actions/", "/reports/my_time_tracking_kpi/", "/reports/my_time_tracking_kpi_micro/", "/export/export_my_time_tracking_kpi/", "/export/export_my_time_tracking_kpi_micro/", "/export/my_time_tracking_kpi_micro_pdf/", "/export/my_time_tracking_kpi_pdf/", "/zoom_meetings/", "/dashboard/get_filter_users/", "/users/delete_signature/", "/users/get_signature_picture/", "/money_preferences/has_related_user_group/", "/reminders/check_is_recurrence/", "/cases/get_case_rate_by_organization_id/", "/cases/get_add_case_rate_view/", "/cases/settings/", "/reminders/reminders_router/", "/vouchers/get_filtered_time_logs/", "/vouchers/switch_language/", "/accounts_types/", "/vouchers/invoice_export_to_word/", "/vouchers/invoice_payment_export_to_word/", "/case_types/get_case_types_by_case_category/", "/reports/client_statement_result", "/cases/delete_case_stage/", "/case_containers/check_folder_privacy/", "/case_containers/load_documents/", "/case_containers/view_document/", "/maintenance/export_language_variables/", "/maintenance/import_language_variables/", "/cases/get_case_outsource_by_category/", "/cases/check_custom_fields_relation/", "/custom_fields/get_practice_area_by_category/", "/custom_fields/is_cf_used/", "/customer_portal/return_related_fields/", "/customer_portal/user_approve/", "/users/login_idps/", "/users/login_sso_external/", "/cases/show_children_documents_in_cp/", "/cases/get_total_effort_time_logs_case/", "/case_containers/show_children_documents_in_cp/", "/cases/download_hearing_report/", "/time_tracking/validate_time_logs/", "/contracts/approvers_signees_autocomplete/", "/cases/hearing_prepare_report_to_client/", "/cases/hearing_submit_report_to_client/", "/companies/delete_signature_authority_document/", "/customer_portal/validate_data_combination/", "/customer_portal/lookup_collaborators/", "/tasks/return_matter_doc_thumbnail/", "/materialized_view_triggers/", "/contacts/manage_money_accounts/", "/dashboard/pie_charts_widgets/", "/dashboard/set_widgets_order/", "/clients/load_documents/", "/clients/upload_file/", "/clients/delete_document/", "/clients/download_file/", "/clients/return_doc_thumbnail/", "/reports/all_account_statement_result", "/contracts/awaiting_my_approvals/", "/contracts/awaiting_my_signatures/", "/contracts/return_doc_thumbnail/", "/contracts/show_children_documents_in_cp/", "/customer_portal/return_cf_related_fields/", "/contracts/load_summary/", "/contracts/get_signature_picture/", "/contracts/save_transition_screen_fields/", "/contract_workflows/migrate_statuses/", "/contracts/load_document_variables/", "/contracts/list_contract_docs/", "/contracts/check_folder_privacy/", "/contracts/move_status/", "/contracts/load_documents/", "/contracts/view_document/", "/contracts/autocomplete/", "/docusign_integration/authenticate/", "/docusign_integration/call_back/", "/docusign_integration/convert_to_live_process/", "/docusign_integration/events_by_webhook/", "/docusign_integration/index/", "/docusign_integration/integrate/", "/docusign_integration/request_signature_worker/", "/docusign_integration/sign/", "/webhooks_events/", "/docusign_integration/signed_callback/", "/docusign_integration/upload_signed_doc/", "/dashboard/bar_charts_widgets/", "/subscription/add_user_window/", "/subscription/user_add_response/", "/customer_portal/customer_portal_users_autocomplete/", "/cases/save_show_hide_customer_portal/", "/dashboard/download_dashboard_pdf/", "/time_tracking/split_time/", "/time_types/get_record_by_id/", "/items/get_sub_items/", "/users/add_signature/", "/users/set_default_signature/", "/cases/get_system_rate/", "/share_movements/check_transactions/", "/advisors/index/", "/saml_sso/get_details/", "/saml_sso/lookup_users/", "/dashboard/board_save_post_filter/", "/dashboard/board_save_task_post_filter/", "/money_dashboards/get_drilldown_lists/", "/money_dashboards/load_filter_type/", "/money_dashboards/load_widget_data/", "/cases/my_expenses/", "/cases/my_time_logs/", "/contracts/load_sub_contract_types/", "/users/customer_support/", "/contracts/load_templates_per_contract_types/", "/tasks/check_case_privacy/", "/contracts/add_contract_type_custom_fields/", "/approval_center/board_members_roles_form/", "/approval_center/save_board_member_role/", "/contracts/load_bm_collaborators_per_company/", "/contracts/load_board_members_per_party/", "/signature_center/board_members_roles_form/", "/signature_center/save_board_member_role/", "/external_actions/approve_contract/", "/tasks/tasks_contributed_by_me/", "/companies/load_company_note_details/", "/vouchers/open_file_viewer/", "/contracts/resend_approval_email/", "/contracts/autocomplete_party/", "/external_actions/", "/external_actions/download_file/", "/external_actions/draft_collaborate_otp/", "/contracts/autocomplete_party/", "/sla_management/get_contract_type_by_workflow/","/legal_opinions/add/"=> "/legal_opinions/add/",	"/legal_opinions/edit/"=> "/legal_opinions/edit/",	"/legal_opinions/load_data/"=> "/legal_opinions/add/",	"/task_locations/add/"=> "/task_locations/add/",	"/legal_opinions/view/"=> "/legal_opinions/view/",	"/legal_opinions/move_status/"=> "/legal_opinions/view/",	"/legal_opinions/add_comment/"=> "/legal_opinions/add_comment/",	"/legal_opinions/edit_comment/"=> "/legal_opinions/edit_comment/",	"/legal_opinions/comments/"=> "/legal_opinions/comments/",	"/legal_opinions/list_all_opinions/"=> "/legal_opinions/list_all_opinions/"	,"/users/verify_otp/"=> "/users/verify_otp/","/users/resend_otp/"=> "/users/resend_otp/"];
        $this->excempted_uri = [

            // --- Advisors ---
            "/advisors/get_contact_related_companies/",
            "/advisors/get_advisor_companies_modal/",
            "/advisors/upload_email_image/",
            "/advisors/index/",

            // --- Accounts ---
            "/accounts/fetch_account/",
            "/accounts/fetch_client_accounts/",
            "/accounts/lookup_client/",
            "/accounts/lookup_supplier/",
            "/accounts/lookup_partner/",
            "/accounts/lookup/",
            "/accounts/get_account_details/",

            // --- Assignments ---
            "/assignments/delete/",
            "/assignments/return_rules/",
            "/assignments/save/",

            // --- Approval Center ---
            "/approval_center/board_members_roles_form/",
            "/approval_center/save_board_member_role/",

            // --- Alternative Login / SSO ---
            "/alternative_login/",
            "/users/login_idps/",
            "/users/login_sso_external/",
            "/saml_sso/get_details/",
            "/saml_sso/lookup_users/",

            // --- Base / System / Maintenance ---
            "/base/",
            "/maintenance/export_language_variables/",
            "/maintenance/import_language_variables/",
            "/maintenance/export_specific_language_variables/",
            "/materialized_view_triggers/",
            "/system_preferences/check_usernames_compatibility/",
            "/system_preferences/add_remove_matter_contributors_from_trigger/",
            "/system_preferences/reset_expense_notification_system_params/",

            // --- Calendar / Reminders ---
            "/calendar_integrations/",
            "/calendars/calendars_list/",
            "/calendars/integrations_list/",
            "/reminders/show_my_reminders/",
            "/reminders/reminders_list/",
            "/reminders/reminders_router/",
            "/reminders/check_is_recurrence/",

            // --- Cases ---
            "/cases/autocomplete/",
            "/cases/add_client_and_update_case/",
            "/cases/add_court_external_ref/",
            "/cases/change_litigation_stage_status/",
            "/cases/check_folder_privacy/",
            "/cases/check_custom_fields_relation/",
            "/cases/delete_case_stage/",
            "/cases/delete_court_external_ref/",
            "/cases/delete_document_hearing/",
            "/cases/download_hearing_report/",
            "/cases/events_autocomplete/",
            "/cases/fetch_case_client/",
            "/cases/get_add_case_rate_view/",
            "/cases/get_case_outsource_by_category/",
            "/cases/get_case_rate_by_organization_id/",
            "/cases/get_last_comment/",
            "/cases/get_system_rate/",
            "/cases/hearing_bulk_update_summary_to_client/",
            "/cases/hearing_prepare_report_to_client/",
            "/cases/hearing_submit_report_to_client/",
            "/cases/hearings_autocomplete/",
            "/cases/load_documents/",
            "/cases/load_litigation_stage_forms/",
            "/cases/matter_stage_metadata/",
            "/cases/move_status/",
            "/cases/my_expenses/",
            "/cases/my_hearings/",
            "/cases/my_time_logs/",
            "/cases/return_litigation_stages/",
            "/cases/save_show_hide_customer_portal/",
            "/cases/settings/",
            "/cases/show_children_documents_in_ap/",
            "/cases/show_children_documents_in_cp/",
            "/cases/transition_screen_fields/",
            "/cases/update_court_external_ref/",
            "/cases/view_document/",
            "/cases/get_total_effort_time_logs_case/",

            // --- Case Containers ---
            "/case_containers/autocomplete/",
            "/case_containers/check_folder_privacy/",
            "/case_containers/link_matter/",
            "/case_containers/load_container_fields/",
            "/case_containers/load_documents/",
            "/case_containers/return_cases_details/",
            "/case_containers/show_children_documents_in_cp/",
            "/case_containers/view_document/",

            // --- Case Types / SLA / Workflows ---
            "/case_types/get_case_type_due_conditions/",
            "/case_types/get_case_types_by_case_category/",
            "/sla_management/get_practice_area_by_workflow/",
            "/sla_management/get_status_by_workflow/",
            "/sla_management/get_contract_type_by_workflow/",
            "/manage_workflows/get_area_of_practice_by_category/",
            "/manage_workflows/migrate_matter_status/",
            "/task_workflows/migrate_statuses/",
            "/contract_workflows/migrate_statuses/",

            // --- Clients ---
            "/clients/autocomplete/",
            "/clients/load_documents/",
            "/clients/upload_file/",
            "/clients/delete_document/",
            "/clients/download_file/",
            "/clients/return_doc_thumbnail/",
            "/clients/load_trust_data/",
            "/clients/update_tax_number/",

            // --- Companies ---
            "/companies/autocomplete/",
            "/companies/check_folder_privacy/",
            "/companies/delete_signature_authority_document/",
            "/companies/discharge_type_autocomplete/",
            "/companies/load_company_note_details/",
            "/companies/load_documents/",
            "/companies/view_document/",

            // --- Contacts ---
            "/contacts/add_email/",
            "/contacts/autocomplete/",
            "/contacts/check_folder_privacy/",
            "/contacts/delete_email/",
            "/contacts/fetch/",
            "/contacts/get_contacts_by_company/",
            "/contacts/load_documents/",
            "/contacts/manage_money_accounts/",
            "/contacts/view_document/",

            // --- Contracts ---
            "/contracts/add_contract_type_custom_fields/",
            "/contracts/approvers_signees_autocomplete/",
            "/contracts/autocomplete/",
            "/contracts/autocomplete_party/",
            "/contracts/awaiting_my_approvals/",
            "/contracts/awaiting_my_signatures/",
            "/contracts/check_folder_privacy/",
            "/contracts/get_signature_picture/",
            "/contracts/list_contract_docs/",
            "/contracts/load_bm_collaborators_per_company/",
            "/contracts/load_board_members_per_party/",
            "/contracts/load_docs_count/",
            "/contracts/load_document_variables/",
            "/contracts/load_documents/",
            "/contracts/load_sub_contract_types/",
            "/contracts/load_summary/",
            "/contracts/load_templates_per_contract_types/",
            "/contracts/move_status/",
            "/contracts/return_doc_thumbnail/",
            "/contracts/resend_approval_email/",
            "/contracts/save_transition_screen_fields/",
            "/contracts/show_children_documents_in_cp/",
            "/contracts/view_document/",

            // --- Custom Fields ---
            "/custom_fields/get_practice_area_by_category/",
            "/custom_fields/is_cf_used/",

            // --- Customer Portal ---
            "/customer_portal/customer_portal_users_autocomplete/",
            "/customer_portal/lookup_collaborators/",
            "/customer_portal/return_cf_related_fields/",
            "/customer_portal/return_related_fields/",
            "/customer_portal/user_approve/",
            "/customer_portal/validate_data_combination/",

            // --- Dashboard ---
            "/dashboard/index/",
            "/dashboard/getting_started/",
            "/dashboard/get_filter_users/",
            "/dashboard/set_money_language/",
            "/dashboard/switch_organization/",
            "/dashboard/bar_charts_widgets/",
            "/dashboard/pie_charts_widgets/",
            "/dashboard/set_widgets_order/",
            "/dashboard/board_save_post_filter/",
            "/dashboard/board_save_task_post_filter/",
            "/dashboard/download_dashboard_pdf/",

            // --- Docusign Integration ---
            "/docusign_integration/authenticate/",
            "/docusign_integration/call_back/",
            "/docusign_integration/convert_to_live_process/",
            "/docusign_integration/events_by_webhook/",
            "/docusign_integration/index/",
            "/docusign_integration/integrate/",
            "/docusign_integration/request_signature_worker/",
            "/docusign_integration/sign/",
            "/docusign_integration/signed_callback/",
            "/docusign_integration/upload_signed_doc/",

            // --- Docs ---
            "/docs/check_folder_privacy/",
            "/docs/load_documents/",
            "/docs/view_document/",

            // --- External Actions ---
            "/external_actions/",
            "/external_actions/approve_contract/",
            "/external_actions/download_file/",
            "/external_actions/draft_collaborate_otp/",

            // --- Grid / Look & Feel ---
            "/grid_saved_columns/",
            "/look_feel/load_theme/",
            "/look_feel/restore_default/",
            "/look_feel/restore_remove_image/",
            "/look_feel/save/",
            "/look_feel/add/",

            // --- Integrations ---
            "/integrations/choose_default_folder/",
            "/integrations/check_access_token/",
            "/integrations/create_folder/",
            "/integrations/delete_document/",
            "/integrations/disable/",
            "/integrations/dropbox_auth_callback/",
            "/integrations/download_file/",
            "/integrations/enable/",
            "/integrations/load_documents/",
            "/integrations/rename_document/",
            "/integrations/revoke_access_token/",
            "/integrations/update_document_tab/",
            "/integrations/upload_file/",

            // --- Intellectual Properties ---
            "/intellectual_properties/check_folder_privacy/",
            "/intellectual_properties/load_documents/",
            "/intellectual_properties/view_document/",

            // --- Legal Opinions ---
            "/legal_opinions/add/",
            "/legal_opinions/edit/",
            "/legal_opinions/load_data/",
            "/legal_opinions/view/",
            "/legal_opinions/move_status/",
            "/legal_opinions/add_comment/",
            "/legal_opinions/edit_comment/",
            "/legal_opinions/comments/",
            "/legal_opinions/list_all_opinions/",

            // --- Misc ---
            "/home/",
            "/home/about_us/",
            "/home/confirm_request/",
            "/home/hijri_date_converter/",
            "/home/whats_new_popup/",
            "/notifications/",
            "/compressed_asset/",
            "/cron_jobs/",
            "/pages/",
            "/providers_groups/autocomplete/",
            "/top_controller/",
            "/app_controller/",
            "/core_controller/",
            "/webhooks_events/",
            "/zoom_meetings/",
            "/timers/",

            // --- Reports / Exports ---
            "/reports/report_builder_list/",
            "/reports/report_builder_excel/",
            "/reports/report_builder_pdf/",
            "/reports/my_time_tracking_kpi/",
            "/reports/my_time_tracking_kpi_micro/",
            "/reports/client_statement_result/",
            "/reports/all_account_statement_result/",
            "/export/export_my_time_tracking_kpi/",
            "/export/export_my_time_tracking_kpi_micro/",
            "/export/my_time_tracking_kpi_micro_pdf/",
            "/export/my_time_tracking_kpi_pdf/",

            // --- Tasks ---
            "/tasks/autocomplete/",
            "/tasks/check_case_privacy/",
            "/tasks/comments/",
            "/tasks/delete_document/",
            "/tasks/download_file/",
            "/tasks/load_documents/",
            "/tasks/move_status/",
            "/tasks/my_tasks/",
            "/tasks/preview_document/",
            "/tasks/return_doc_thumbnail/",
            "/tasks/return_matter_doc_thumbnail/",
            "/tasks/tasks_contributed_by_me/",
            "/tasks/tasks_reported_by_me/",
            "/tasks/transition_screen_fields/",
            "/tasks/upload_file/",
            "/tasks/view_document/",

            // --- Time Tracking ---
            "/time_tracking/my_time_logs/",
            "/time_tracking/my_time_entries/",
            "/time_tracking/split_time/",
            "/time_tracking/validate_time_logs/",

            // --- Users ---
            "/users/login/",
            "/users/change_password/",
            "/users/logout/",
            "/users/fetch/",
            "/users/profile/",
            "/users/autocomplete/",
            "/users/add_signature/",
            "/users/set_default_signature/",
            "/users/delete_signature/",
            "/users/get_signature_picture/",
            "/users/get_profile_picture/",
            "/users/get_api_token_data/",
            "/users/initial_setup/",
            "/users/user_guide/",
            "/users/verify_otp/",
            "/users/resend_otp/",
            "/users/customer_support/",

            // --- Vendors ---
            "/vendors/autocomplete/",
            "/vendors/update_tax_number/",

            // --- Vouchers ---
            "/vouchers/bill_export_options/",
            "/vouchers/bill_load_documents/",
            "/vouchers/convert_expense_billingStatus_to_invoice/",
            "/vouchers/convert_expense_billingStatus_to_quote/",
            "/vouchers/expense_load_documents/",
            "/vouchers/get_expenses_need_approved/",
            "/vouchers/get_filtered_time_logs/",
            "/vouchers/get_quote_items/",
            "/vouchers/invoice_export_description_table/",
            "/vouchers/invoice_export_options/",
            "/vouchers/invoice_export_to_word/",
            "/vouchers/invoice_load_documents/",
            "/vouchers/invoice_payment_export_to_word/",
            "/vouchers/list_client_quotes/",
            "/vouchers/my_expenses_list/",
            "/vouchers/open_file_viewer/",
            "/vouchers/quote_load_documents/",
            "/vouchers/relate_matters_to_invoice/",
            "/vouchers/relate_matters_to_quote/",
            "/vouchers/switch_language/",

            // --- Misc Utilities ---
            "/money_preferences/has_related_user_group/",
            "/money_dashboards/get_drilldown_lists/",
            "/money_dashboards/load_filter_type/",
            "/money_dashboards/load_widget_data/",
            "/share_movements/check_transactions/",
            "/subscription/add_user_window/",
            "/subscription/user_add_response/",
        ];
        $this->ci->lang->load("is_auth");
        $this->ci->load->library("is_auth_event");
        $this->ci->load->model("system_preference");
        $systemPreferences = $this->ci->system_preference->get_values();
        $this->stay_signed_in = $systemPreferences["staySignedIn"];
        $channel = isset($params["channel"]) ? $params["channel"] : "A4L";
        $this->auto_login($channel);

        // Load system preferences for OTP configuration

        $this->system_preferences = $this->ci->system_preference->get_key_groups();

        // Load necessary libraries for sending OTP
        $this->ci->load->library('sms_gateway');
        $this->ci->load->library('email_notifications');
    }
    /**
     * Generates a random numeric OTP.
     * @param int $length The length of the OTP.
     * @return string The generated OTP.
     */
    private function _generate_otp($length = 6)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }
        return $otp;
    }

    /**
     * Sends an OTP to the user via SMS or Email.
     *
     * @param int $user_id The ID of the user.
     * @param string $phone_number The user's phone number for SMS.
     * @param string $email The user's email address for email.
     * @return bool True if OTP was sent successfully, false otherwise.
     */
    public function send_otp_for_login($user_id, $phone_number = null, $email = null)
    {
        $otp_code = $this->_generate_otp();
        $otp_expiry_minutes = $this->system_preferences['mfa'][self::OTP_EXPIRY_MINUTES_KEY] ?? 5; // Default to 5 minutes
        $otp_expiry_timestamp = date('Y-m-d H:i:s', strtotime('+' . $otp_expiry_minutes . ' minutes'));

        // Load User model to store OTP
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();

        // Store OTP in the user's record
        if (!$this->ci->user->set_otp($user_id, $otp_code, $otp_expiry_timestamp)) {
            $this->write_log("Is_Auth: Failed to store OTP for user ID: " . $user_id, "ERROR");
            return false;
        }

        $otp_sent = false;
        $otp_message = "Your One-Time Password (OTP) is: " . $otp_code . ". It is valid for " . $otp_expiry_minutes . " minutes.";

        // Attempt to send via SMS if phone number is available and SMS is enabled
        if (!empty($phone_number) && ($this->system_preferences['SMSGateway']['smsFeatureEnabled'] ?? 0)) {
            $sms_data = [
                'to' => $phone_number,
                'message' => $otp_message
            ];
            if ($this->ci->sms_gateway->notify($sms_data)) {
                $this->write_log("Is_Auth: OTP SMS sent to user ID: " . $user_id . ", Phone: " . $phone_number, "INFO");
                $otp_sent = true;
            } else {
                $this->write_log("Is_Auth: Failed to send OTP SMS to user ID: " . $user_id . ", Phone: " . $phone_number, "ERROR");
            }
        }

        // Attempt to send via Email if email is available
        if (!empty($email)) {
            $email_data = [
                'to' => $email,
                'subject' => 'Your One-Time Password (OTP)',
                'content' => $otp_message
            ];
            if ($this->ci->email_notifications->send_email($email,"Your One-Time Password (OTP)",$otp_message)) {
                $this->write_log("Is_Auth: OTP Email sent to user ID: " . $user_id . ", Email: " . $email, "INFO");
                $otp_sent = true;
            } else {
                $this->write_log("Is_Auth: Failed to send OTP Email to user ID: " . $user_id . ", Email: " . $email, "ERROR");
            }
        }
///add  timestamp to mfa_user_data session
 $mfa_data["otp_expiry_timestamp"]=strtotime($otp_expiry_timestamp);
  $this->ci->session->set_userdata('mfa_user_data',$mfa_data["otp_expiry_timestamp"] );
        return $otp_sent;
    }

    /**
     * Verifies the provided OTP against the stored one for a user.
     *
     * @param int $user_id The ID of the user.
     * @param string $otp_code The OTP code entered by the user.
     * @return array True if OTP is valid and not expired, false otherwise.
     */
    public function verify_otp($user_id, $otp_code)
    {
        $response=["result" => false, "message" => "","otp_expiry_timestamp" => ""];
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $this->ci->user->fetch($user_id);

        $stored_otp_data["otp_code"] =$this->ci->user->get_field('otp_code');
        $stored_otp_data["otp_expiry"] =$this->ci->user->get_field('otp_expiry');

        if (empty($stored_otp_data) || $stored_otp_data['otp_code'] !== $otp_code) {
            $this->write_log("Is_Auth: Invalid OTP provided for user ID: " . $user_id, "WARNING");
           $response["message"] = $this->ci->lang->line("otp_not_match");
            return $response;
        }

        $current_time = time();
        $expiry_time = strtotime($stored_otp_data['otp_expiry']);

        if ($current_time > $expiry_time) {
            $response["message"] = $this->ci->lang->line("otp_verification_time_expired");

            return $response;
        }

        // OTP is valid and not expired, clear it after successful verification
       $response["result"] = true;
        $response["otp_expiry_timestamp"]=strtotime($expiry_time);
        return $response;
    }

    /**
     * Conceptual method to perform MFA check after successful username/password login.
     * This method would be called by your login controller/method.
     *
     * @param object $user_data The user object/data after initial password validation.
     * @return bool True if OTP was sent and further verification is needed, false otherwise.
     */
    public function perform_login_mfa_check($user_data)
    {
        $otp_enabled = $this->system_preferences['mfa'][self::OTP_FEATURE_ENABLED_KEY] ?? 0;

        if ($otp_enabled && isset($user_data["id"])) {
            $user_id = $user_data["id"];
            $phone_number = $user_data["phone"] ?? null;
            $email = $user_data["email"] ?? null;

            if (empty($phone_number) && empty($email)) {
                $this->write_log("Is_Auth: OTP enabled but no contact information (phone/email) for user ID: " . $user_id, "ERROR");
                return false; // Cannot send OTP without contact info
            }

            if ($this->send_otp_for_login($user_id, $phone_number, $email)) {
                // Store user ID in session to retrieve for OTP verification later
                $this->ci->session->set_userdata('otp_user_id', $user_id);
                $this->write_log("Is_Auth: OTP sent for user ID: " . $user_id . ". Awaiting OTP verification.", "INFO");
                return true; // Indicate that OTP verification is pending
            } else {
                $this->write_log("Is_Auth: Failed to send OTP for user ID: " . $user_id . ". Proceeding without OTP or denying login.", "ERROR");
                return false; // OTP sending failed, you might want to deny login or proceed without OTP based on policy
            }
        }
        return false; // OTP not enabled or user data invalid
    }
    public function auto_login($channel)
    {
        if ($this->stay_signed_in() == "yes") {
            $session_user_id_keys = ["A4L" => "AUTH_user_id", "CP" => "CP_user_id"];
            $result = false;
            if (($auto = $this->ci->input->cookie($this->autologin_cookie_name)) && !$this->ci->session->userdata($session_user_id_keys[$channel])) {
                $auto = unserialize($auto);
                if (isset($auto["key_id"]) && $auto["key_id"] && $auto["user_id"]) {
                    $this->ci->load->model("user_autologin");
                    $user_autologin_info = ["key_id" => $auto["key_id"], "user_id" => $auto["user_id"], "channel" => $channel];
                    $query = $this->ci->user_autologin->get_key($user_autologin_info);
                    if ($result = $query->row()) {
                        $this->ci->session->set_userdata("license_flag", 1);
                        switch ($channel) {
                            case "CP":
                                $this->ci->load->model("customer_portal_users", "customer_portal_usersfactory");
                                $this->ci->customer_portal_users = $this->ci->customer_portal_usersfactory->get_instance();
                                $this->ci->customer_portal_users->fetch($result->id);
                                $user_data = ["CP_user_id" => $this->ci->customer_portal_users->get_field("id"), "CP_email_address" => $this->ci->customer_portal_users->get_field("email"), "CP_profileName" => $this->ci->customer_portal_users->get_field("firstName") . " " . $this->ci->customer_portal_users->get_field("lastName"), "CP_isAd" => $this->ci->customer_portal_users->get_field("isAd"), "CP_isA4Luser" => $this->ci->customer_portal_users->get_field("isA4Luser"), "CP_auth" => $this->ci->customer_portal_users->get_field("password"), "CP_logged_in" => true];
                                $this->ci->session->set_userdata($user_data);
                                break;
                            default:
                                $this->ci->load->model("user_temp");
                                $this->ci->load->model("user_profile");
                                $this->ci->load->model("user", "userfactory");
                                $this->ci->user = $this->ci->userfactory->get_instance();
                                $this->ci->user->fetch($result->id);
                                $user_data["id"] = $this->ci->user->get_field("id");
                                $this->ci->user_profile->fetch(["user_id" => $user_data["id"]]);
                                $is_banned = $this->ci->user->get_field("banned");
                                $is_inactive = $this->ci->user->get_field("status") == "Inactive";
                                if (!$is_banned && !$is_inactive) {
                                    $user_data["username"] = $this->ci->user->get_field("username");
                                    $user_data["email"] = $this->ci->user->get_field("email");
                                    $user_data["isAd"] = $this->ci->user->get_field("isAd");
                                    $user_data["user_group_id"] = $this->ci->user->get_field("user_group_id");
                                    $user_data["type"] = $this->ci->user->get_field("type");
                                    $user_data["workthrough"] = $this->ci->user->get_field("workthrough");
                                    $this->_set_session($user_data);
                                    $this->_set_last_ip_and_last_login($auto["user_id"], $this->ci->user);
                                    $this->ci->is_auth_event->user_logged_in($result->id);
                                }
                                $result = true;
                        }
                    }
                }
            }
            return $result;
        }
    }
    public function is_logged_in()
    {
        return $this->ci->session->userdata("AUTH_logged_in");
    }
    public function login($user_model, $login, $password, $remember = NULL)
    {
        $logged_in = false;
        if (!empty($user_model) && !empty($login)) {
            $user_id = $this->get_login_user_id($user_model, $login);
            if (!empty($user_id)) {
                $user_fetched = $user_model->fetch($user_id);
                if ($user_fetched) {
                    foreach ($user_model->login_banning_fields as $field_name => $baning_value) {
                        if ($user_model->get_field($field_name) == $baning_value) {
                            return $logged_in;
                        }
                    }
                    if (strpos($login, "sso|") !== false) {
                        $user_model->modelCode == "U" ? $this->ci->session->set_userdata("a4l_sso_authentication", true) : $this->ci->session->set_userdata("cp_sso_authentication", true);
                        $logged_in = true;
                    } else {
                        if (!empty($password)) {
                            $user_data = $user_model->get_fields();
                            if (isset($user_data["isAd"]) && $user_data["isAd"]) {
                                $this->ci->load->library("ActiveDirectory");
                                $ad = new ActiveDirectory();
                                if ($ad->bindAdmin() && $ad->searchUser($user_data["username"])) {
                                    if ($ad->check_user_login_capability($user_data["username"])) {
                                        if ($ad->authenticateUser($user_data["username"], $password)) {
                                            $system_preferences = $this->ci->system_preference->get_specified_system_preferences(["user_group_sync", "user_multiple_groups"]);
                                            if ($system_preferences["user_group_sync"] == "yes") {
                                                $fetched_groups = $ad->get_user_groups($user_data["username"]);
                                                if (!$fetched_groups) {
                                                    $this->_auth_error = $this->ci->lang->line("unable_to_fetch_user_groups");
                                                    return false;
                                                }
                                                if (is_array($fetched_groups)) {
                                                    if (count($fetched_groups) == 0) {
                                                        $this->_auth_error = $this->ci->lang->line("user_dont_belong_to_groups");
                                                        return false;
                                                    }
                                                    if (1 < count($this->check_exist_groups($fetched_groups)) && $system_preferences["user_multiple_groups"] == "no") {
                                                        $this->_auth_error = $this->ci->lang->line("user_belong_to_multiple_groups");
                                                        return false;
                                                    }
                                                }
                                                $validate_group = $this->validate_update_user_group($user_data["id"], $user_data["user_group_id"], $fetched_groups);
                                                if (!$validate_group["result"]) {
                                                    $this->_auth_error = $validate_group["msg"];
                                                    return false;
                                                }
                                                $logged_in = true;
                                            } else {
                                                $logged_in = true;
                                            }
                                        } else {
                                            $logged_in = false; // Authentication failed
                                        }
                                    } else {
                                        $this->_auth_error = $this->ci->lang->line("locked_from_active_directory");
                                    }
                                } else {
                                    $logged_in = false; // AD Search failed
                                }
                            } else {
                                if (substr($user_data["password"], 0, 7) !== "\$2y\$10\$") {
                                    $crypted_password = @crypt(@$this->_encode($password), $user_data["password"]);
                                    if ($crypted_password === $user_data["password"]) {
                                        $user_model->set_field("password", password_hash($password, PASSWORD_DEFAULT));
                                        $user_model->update();
                                        $logged_in = true;
                                    } else {
                                        $logged_in = false; // Password doesn't match
                                    }
                                } else {
                                    if (password_verify($password, $user_data["password"])) {
                                        $logged_in = true;
                                    } else {
                                        $logged_in = false; // Password verification failed
                                    }
                                }
                            }
                        } else {
                            $logged_in = false; // Password empty
                        }
                    }
                } else {
                    $logged_in = false; // User fetch failed
                }
            } else {
                $logged_in = false; // User ID not found
            }

            if ($logged_in) {
                // Fetch additional user data needed for MFA
                $this->ci->load->model("user", "userfactory");
                $this->ci->user = $this->ci->userfactory->get_instance();
                $this->ci->user->fetch($user_id); // $user_model->fetch($user_id); // Refetch to ensure we have the latest data
                $this->ci->load->model("user_profile");
                $this->ci->user_profile->fetch(["user_id" => $user_id]);
                $phone = $this->ci->user_profile->get_field("phone");
                $mobile=$this->ci->user_profile->get_field("mobile");

                $mfa_data = [
                    'id' => $user_id,
                    'email' => $this->ci->user->get_field("email"),
                    'phone' => $phone??$mobile,
                    'last_otp_verified_at' => $this->ci->user->get_field("last_otp_verified_at"),
                    'last_login_device_fingerprint' => $this->ci->user->get_field("last_login_device_fingerprint"),
                ];

                // Perform MFA check
                $mfa_required = $this->perform_login_mfa_check($mfa_data);

                if ($mfa_required) {
                    // Store user data in session for OTP verification
                    $this->ci->session->set_userdata('mfa_user_data', $mfa_data);
                    return 'mfa_required'; // Indicate MFA is required
                } else {
                    return true; // Login successful, no MFA needed
                }
            }
        }
        if (!$logged_in && empty($this->_auth_error) && strpos($login, "sso|") === false) {
            $this->_auth_error = isset($user_data["userDirectory"]) ? $user_data["userDirectory"] == "azure_ad" ? $this->ci->lang->line("azure_ad_auth_login_authentication_incorrect") : $this->ci->lang->line("auth_login_authentication_incorrect") : $this->ci->lang->line("auth_login_authentication_incorrect");
        } else {
            if ($remember) {
                $cp_flag = NULL;
                if ($user_model->model == "CP") {
                    $cp_flag = $user_model->model;
                }
                $this->_create_autologin($user_id, $cp_flag);
            }
        }
        return false; // Login failed
    }
    public function loginwithoutMFA($user_model, $login, $password, $remember = NULL)
    {
        $logged_in = false;
        if (!empty($user_model) && !empty($login)) {
            $user_id = $this->get_login_user_id($user_model, $login);
            if (!empty($user_id)) {
                $user_fetched = $user_model->fetch($user_id);
                if ($user_fetched) {
                    foreach ($user_model->login_banning_fields as $field_name => $baning_value) {
                        if ($user_model->get_field($field_name) == $baning_value) {
                            return $logged_in;
                        }
                    }
                    if (strpos($login, "sso|") !== false) {
                        $user_model->modelCode == "U" ? $this->ci->session->set_userdata("a4l_sso_authentication", true) : $this->ci->session->set_userdata("cp_sso_authentication", true);
                        $logged_in = true;
                    } else {
                        if (!empty($password)) {
                            $user_data = $user_model->get_fields();
                            if (isset($user_data["isAd"]) && $user_data["isAd"]) {
                                $this->ci->load->library("ActiveDirectory");
                                $ad = new ActiveDirectory();
                                if ($ad->bindAdmin() && $ad->searchUser($user_data["username"])) {
                                    if ($ad->check_user_login_capability($user_data["username"])) {
                                        if ($ad->authenticateUser($user_data["username"], $password)) {
                                            $system_preferences = $this->ci->system_preference->get_specified_system_preferences(["user_group_sync", "user_multiple_groups"]);
                                            if ($system_preferences["user_group_sync"] == "yes") {
                                                $fetched_groups = $ad->get_user_groups($user_data["username"]);
                                                if (!$fetched_groups) {
                                                    $this->_auth_error = $this->ci->lang->line("unable_to_fetch_user_groups");
                                                    return false;
                                                }
                                                if (is_array($fetched_groups)) {
                                                    if (count($fetched_groups) == 0) {
                                                        $this->_auth_error = $this->ci->lang->line("user_dont_belong_to_groups");
                                                        return false;
                                                    }
                                                    if (1 < count($this->check_exist_groups($fetched_groups)) && $system_preferences["user_multiple_groups"] == "no") {
                                                        $this->_auth_error = $this->ci->lang->line("user_belong_to_multiple_groups");
                                                        return false;
                                                    }
                                                }
                                                $validate_group = $this->validate_update_user_group($user_data["id"], $user_data["user_group_id"], $fetched_groups);
                                                if (!$validate_group["result"]) {
                                                    $this->_auth_error = $validate_group["msg"];
                                                    return false;
                                                }
                                                $logged_in = true;
                                            } else {
                                                $logged_in = true;
                                            }
                                        }
                                    } else {
                                        $this->_auth_error = $this->ci->lang->line("locked_from_active_directory");
                                    }
                                }
                            } else {
                                if (substr($user_data["password"], 0, 7) !== "\$2y\$10\$") {
                                    $crypted_password = @crypt(@$this->_encode($password), $user_data["password"]);
                                    if ($crypted_password === $user_data["password"]) {
                                        $user_model->set_field("password", password_hash($password, PASSWORD_DEFAULT));
                                        $user_model->update();
                                        $logged_in = true;
                                    }
                                } else {
                                    if (password_verify($password, $user_data["password"])) {
                                        $logged_in = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!$logged_in && empty($this->_auth_error) && strpos($login, "sso|") === false) {
                $this->_auth_error = isset($user_data["userDirectory"]) ? $user_data["userDirectory"] == "azure_ad" ? $this->ci->lang->line("azure_ad_auth_login_authentication_incorrect") : $this->ci->lang->line("auth_login_authentication_incorrect") : $this->ci->lang->line("auth_login_authentication_incorrect");
            } else {
                if ($remember) {
                    $cp_flag = NULL;
                    if ($user_model->model == "CP") {
                        $cp_flag = $user_model->model;
                    }
                    $this->_create_autologin($user_id, $cp_flag);
                }
            }
        }
        return $logged_in;
    }
    public function get_login_user_id($user_model, $login)
    {
        $user_id = NULL;
        if (!isset($this->ci->instance_data)) {
            $this->ci->load->model("instance_data");
        }
        $installation_type = $this->ci->instance_data->get_value_by_key("installationType");
        $system_preferences = $this->ci->system_preference->get_specified_system_preferences(["adEnabled", "loginWithoutDomain", "ssoApp4legal", "ssoApp4legalCustomerPortal"]);
        $sso_enabled = $user_model->modelCode == "U" && $system_preferences["ssoApp4legal"] || $user_model->modelCode == "CP" && $system_preferences["ssoApp4legalCustomerPortal"];
        $query["select"] = ["id"];
        $query["like"] = ["email", $login, "none"];
        if ($installation_type["keyValue"] == "on-server") {
            $query["or_like"][] = ["username", $login, "none"];
            if ($system_preferences["adEnabled"]) {
                $sso_authentication = $sso_enabled && strpos($login, "sso|") !== false;
                $active_directory_user_fetch = $sso_authentication || $system_preferences["loginWithoutDomain"];
                if ($active_directory_user_fetch) {
                    $active_directory_domain = $this->ci->system_preference->get_specified_system_preferences("domain");
                    if ($sso_authentication) {
                        $login = substr($login, strpos($login, "\\") + 1, strlen($login));
                    }
                    $query["or_like"][] = ["username", $login . "@" . $active_directory_domain, "none"];
                }
            }
        } else {
            if ($user_model->modelCode == "CP") {
                $query["or_like"][] = ["username", $login, "none"];
            }
        }
        $users_found = $user_model->load_all($query);
        if (count($users_found) == 1) {
            $user_id = $users_found[0]["id"];
        }
        return $user_id;
    }
    public function _encode($password)
    {
        $majorsalt = $this->salt;
        if (function_exists("str_split")) {
            $_pass = str_split($password);
        } else {
            $_pass = [];
            if (is_string($password)) {
                for ($i = 0; $i < strlen($password); $i++) {
                    array_push($_pass, $password[$i]);
                }
            }
        }
        foreach ($_pass as $_hashpass) {
            $majorsalt .= md5($_hashpass);
        }
        return md5($majorsalt);
    }
    public function generate_token($str)
    {
        $salt = $this->random_str(32 - strlen($str));
        return hash("sha256", $str . $salt);
    }
    private function random_str($length, $keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")
    {
        $pieces = [];
        $max = mb_strlen($keyspace, "8bit") - 1;
        for ($i = 0; $i < $length; $i++) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }
        return implode("", $pieces);
    }
    public function _set_session($data)
    {
        $user_group_data = $this->_get_user_group_data($data["user_group_id"]);
        $user = ["AUTH_user_id" => $data["id"], "AUTH_username" => $data["username"], "AUTH_email_address" => $data["email"], "AUTH_user_group_id" => $data["user_group_id"], "AUTH_user_group_name" => $user_group_data["user_group_name"], "AUTH_isAd" => $data["isAd"], "AUTH_access_type" => $data["type"], "AUTH_logged_in" => true];
        $this->ci->session->set_userdata($user);
        $this->ci->session->set_userdata("workthrough", $data["workthrough"]);
        $this->ci->session->set_userdata("AUTH_is_super_admin", $this->is_super_admin($data["user_group_id"]));
        $this->ci->session->set_userdata("AUTH_is_grid_admin", $this->userIsGridAdmin());
        $this->ci->load->model("language");
        $this->ci->session->set_userdata("languages", implode(",", $this->ci->language->loadAvailableLanguages()));
    }
    public function get_user_id()
    {
        return $this->ci->session->userdata("AUTH_user_id");
    }
    public function get_user_name()
    {
        return $this->ci->session->userdata("AUTH_username");
    }
    public function get_email_address()
    {
        return $this->ci->session->userdata("AUTH_email_address");
    }
    public function isAdUser()
    {
        return $this->ci->session->userdata("AUTH_isAd") ? true : false;
    }
    public function get_hashed_password()
    {
        return $this->ci->session->userdata("AUTH_encrypted_pass");
    }
    public function get_user_encypted_pass($username)
    {
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $this->ci->user->fetch(["username" => $username]);
        $pass = $this->ci->user->get_field("password");
        return $pass;
    }
    public function get_user_group_id()
    {
        return $this->ci->session->userdata("AUTH_user_group_id");
    }
    public function get_user_group_name()
    {
        return $this->ci->session->userdata("AUTH_user_group_name");
    }
    public function _create_autologin($user_id, $channel = "A4L")
    {
        $result = false;
        $key_id = substr(md5(uniqid(rand() . $this->ci->input->cookie($this->ci->config->item("sess_cookie_name")))), 0, 16);
        if (empty($this->ci->user_autologin)) {
            $this->ci->load->model("user_autologin");
        }
        $user_autologin_info = ["user_id" => $user_id, "channel" => $channel];
        $this->ci->user_autologin->prune_keys($user_autologin_info);
        $this->ci->user_autologin->clear_keys($user_autologin_info);
        $user_autologin_info["key_id"] = $key_id;
        $this->ci->load->model("user", "userfactory");
        $this->ci->load->model("user_profile");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $this->ci->user->fetch($user_id);
        $user_data["id"] = $this->ci->user->get_field("id");
        $this->ci->user_profile->fetch(["user_id" => $user_data["id"]]);
        $is_banned = $this->ci->user->get_field("banned");
        $is_inactive = $this->ci->user_profile->get_field("status") == "Inactive";
        if (!$is_banned && !$is_inactive && $this->ci->user_autologin->store_key($user_autologin_info)) {
            unset($user_autologin_info["channel"]);
            $this->_auto_cookie($user_autologin_info);
            $result = true;
        }
        return $result;
    }
    public function _array_in_array($needle, $haystack)
    {
        if (!is_array($needle)) {
            $needle = [$needle];
        }
        foreach ($needle as $pin) {
            if (in_array($pin, $haystack)) {
                return true;
            }
        }
        return false;
    }
    public function _auto_cookie($data)
    {
        if (!function_exists("set_cookie")) {
            $this->ci->load->helper("cookie");
        }
        $cookie = ["name" => $this->autologin_cookie_name, "value" => serialize($data), "expire" => $this->autologin_cookie_life];
        set_cookie($cookie);
    }
    public function _set_last_ip_and_last_login($user_id, $user_model)
    {
        $data = [];
        if (isset($user_model->_fields["user_guide"]) && is_null($user_model->_fields["user_guide"])) {
            $this->ci->session->set_userdata("user_guide", "");
        } else {
            $this->ci->session->set_userdata("user_guide", date("Y-m-d H:i:s", time()));
        }
        if ($this->login_record_ip) {
            $data["last_ip"] = $this->ci->input->ip_address();
        }
        if ($this->login_record_time) {
            $data["last_login"] = date("Y-m-d H:i:s", time());
        }
        if (!empty($data)) {
            $user_model->set_user($user_id, $data);
        }
    }
    public function _get_user_group_data($user_group_id)
    {
        $this->ci->load->model("user_group", "user_groupfactory");
        $this->ci->user_group = $this->ci->user_groupfactory->get_instance();
        $user_group_name = "";
        $parent_user_groups_id = [];
        $parent_user_groups_name = [];
        $permission = [];
        $parent_permissions = [];
        $query = $this->ci->user_group->get_user_group_by_id($user_group_id);
        if (0 < $query->num_rows()) {
            $user_group = $query->row();
            $user_group_name = $user_group->name;
        }
        $data["user_group_name"] = $user_group_name;
        return $data;
    }
    public function get_auth_error()
    {
        return $this->_auth_error;
    }
    public function stay_signed_in()
    {
        return $this->stay_signed_in;
    }
    public function logout($channel = "A4L")
    {
        $this->ci->is_auth_event->user_logging_out($this->ci->session->userdata("AUTH_user_id"));
        if ($this->ci->input->cookie($this->autologin_cookie_name)) {
            $this->_delete_autologin($channel);
        }
        $this->ci->session->sess_destroy();
    }
    public function _delete_autologin($channel)
    {
        if ($auto = $this->ci->input->cookie($this->autologin_cookie_name)) {
            $this->ci->load->helper("cookie");
            if (empty($this->ci->user_autologin)) {
                $this->ci->load->model("user_autologin");
            }
            $auto = unserialize($auto);
            $user_autologin_info = ["key_id" => $auto["key_id"], "user_id" => $auto["user_id"], "channel" => $channel];
            $this->ci->user_autologin->delete_key($user_autologin_info);
            set_cookie($this->autologin_cookie_name, "", -1);
        }
    }
    public function get_fullname($withTitle = false)
    {
        $title = $this->ci->session->userdata("AUTH_userTitle");
        if ($withTitle && $title) {
            return $title . " " . $this->ci->session->userdata("AUTH_userProfileName");
        }
        return $this->ci->session->userdata("AUTH_userProfileName");
    }
    public function is_super_admin($user_group_id = NULL)
    {
        $this->ci->load->model("user_group", "user_groupfactory");
        $this->ci->user_group = $this->ci->user_groupfactory->get_instance();
        $user_group_name = NULL;
        if (empty($user_group_id)) {
            $user_group_name = $this->ci->session->userdata("AUTH_user_group_name");
        } else {
            $user_group_data = $this->_get_user_group_data($user_group_id);
            $user_group_name = $user_group_data["user_group_name"];
        }
        return $user_group_name == $this->ci->user_group->get("superAdminInfosystaName");
    }
    public function check_uri_permissions($controller, $action, $module, $allow = true, $returnAccess = false)
    {
        if ($this->is_logged_in()) {
            if ($this->ci->session->userdata("AUTH_is_super_admin")) {
                return true;
            }
            if (!in_array($controller, $this->excempted_uri) && !in_array($action, $this->excempted_uri)) {
                $roles_allowed_uris = $this->get_permissions_value($module);
                $have_access = !$allow;
                if (isset($roles_allowed_uris[0]) && $this->_array_in_array("/", $roles_allowed_uris[0])) {
                    $have_access = $allow;
                } else {
                    foreach ($roles_allowed_uris as $allowed_uris) {
                        if ($allowed_uris != NULL && ($this->_array_in_array($controller, $allowed_uris) || $this->_array_in_array($action, $allowed_uris))) {
                            $have_access = $allow;
                        }
                    }
                }
                $this->ci->is_auth_event->checked_uri_permissions($this->get_user_id(), $have_access);
                if ($returnAccess) {
                    return $have_access;
                }
                if (!$have_access) {
                    $this->deny_access();
                }
            }
        }
    }
    public function deny_access($uri = "deny")
    {
        $request = "/" . $this->ci->uri->rsegment(1) . "/";
        $action = $this->ci->uri->rsegment(2);
        $action = empty($action) ? "index/" : $this->ci->uri->rsegment(2) . "/";
        if ($uri == "deny") {
            if ($request . $action != "/users/login/") {
                if ($this->ci->input->is_ajax_request()) {
                    exit("access_denied");
                }
                $this->ci->set_flashmessage("warning", $this->ci->lang->line("you_do_not_have_enough_previlages_to_access_the_requested_page"));
                if ($this->ci->session->userdata("cloudReferral")) {
                    redirect("dashboard");
                }
                redirect($this->ci->agent->is_referral() ? $this->ci->agent->referrer() : "dashboard");
            }
        } else {
            if ($uri == "login") {
                if ($request . $action != "/users/login/") {
                    if ($this->ci->input->is_ajax_request()) {
                        exit("login_needed");
                    }
                    redirect("users/login");
                }
            } else {
                if ($uri == "banned") {
                    redirect("auth/banned");
                } else {
                    if ($this->ci->input->is_ajax_request()) {
                        exit("accessDenied");
                    }
                    redirect("dashboard");
                }
            }
        }
    }
    public function get_permissions_value($key, $array_key = "default")
    {
        $result = [];
        $role_id = $this->get_user_group_id();
        $role_name = $this->get_user_group_name();
        $value = $this->get_permission_value($key, false);
        if ($array_key == "role_id") {
            $result[$role_id] = $value;
        } else {
            if ($array_key == "role_name") {
                $result[$role_name] = $value;
            } else {
                array_push($result, $value);
            }
        }
        $parent_permissions = $this->ci->session->userdata("DX_parent_permissions");
        $i = 0;
        if (is_array($parent_permissions)) {
            foreach ($parent_permissions as $permission) {
                if (array_key_exists($key, $permission)) {
                    $value = $permission[$key];
                }
                if ($array_key == "role_id") {
                    $result[$parent_roles_id[$i]] = $value;
                } else {
                    if ($array_key == "role_name") {
                        $result[$parent_roles_name[$i]] = $value;
                    } else {
                        array_push($result, $value);
                    }
                }
                $i++;
            }
        }
        $this->ci->is_auth_event->got_permissions_value($this->get_user_id(), $key);
        return $result;
    }
    public function get_permission_value($key, $check_parent = true)
    {
        $result = NULL;
        $this->ci->load->model("user_group_permission");
        $permission = $this->ci->user_group_permission->get_permission_data($this->ci->session->userdata("AUTH_user_group_id"));
        if (is_array($permission) && array_key_exists($key, $permission)) {
            $result = $permission[$key];
        }
        $this->ci->is_auth_event->got_permission_value($this->get_user_id(), $key);
        return $result;
    }
    public function write_log($level = "error", $msg, $php_error = false)
    {
        if ($this->_login_logging_enabled === false) {
            return false;
        }
        $level = strtoupper($level);
        if (!isset($this->_login_logging_levels[$level]) || $this->_login_logging_threshold < $this->_login_logging_levels[$level]) {
            return false;
        }
        $filepath = $this->_login_logging_log_path . "log-" . date("Y-m-d") . ".log";
        if (MODULE !== "core") {
            $filepath = "../../" . $filepath;
        }
        $message = "";
        if (!($fp = @fopen($filepath, FOPEN_WRITE_CREATE))) {
            return false;
        }
        $systemPreferences = $this->ci->system_preference->get_values();
        $timezone = isset($systemPreferences["systemTimezone"]) && $systemPreferences["systemTimezone"] ? $systemPreferences["systemTimezone"] : $this->ci->config->item("default_timezone");
        date_default_timezone_set($timezone);
        $message .= $level . "\t" . ($level == "INFO" ? "\t-" : "-") . "\t" . date($this->_login_logging_date_fmt) . "\t\t" . $msg . "\n";
        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod($filepath, FILE_WRITE_MODE);
        return true;
    }
    public function get_override_privacy()
    {
        return $this->ci->session->userdata("AUTH_userOverridePrivacy");
    }
    public function is_layout_rtl()
    {
        return in_array($this->ci->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]);
    }
    public function user_is_maker()
    {
        $sessionData = $this->ci->session->userdata();
        if ($sessionData["systemPreferences"]["makerCheckerFeatureStatus"] === "yes") {
            $makerGroups = $sessionData["systemPreferences"]["userMakerGroups"];
            if ($makerGroups) {
                $makerGroups = explode(", ", $makerGroups);
            } else {
                $makerGroups = [];
            }
            return in_array($this->ci->session->userdata("AUTH_user_group_id"), $makerGroups);
        }
        return false;
    }
    public function user_is_checker()
    {
        $sessionData = $this->ci->session->userdata();
        if ($sessionData["systemPreferences"]["makerCheckerFeatureStatus"] === "yes") {
            $checkerGroups = $sessionData["systemPreferences"]["userCheckerGroups"];
            if ($checkerGroups) {
                $checkerGroups = explode(", ", $checkerGroups);
            } else {
                $checkerGroups = [];
            }
            return in_array($this->ci->session->userdata("AUTH_user_group_id"), $checkerGroups);
        }
        return false;
    }
    public function userIsGridAdmin()
    {
        $systemPreferences = $this->ci->system_preference->get_values();
        $gridsAdminUserGroups = isset($systemPreferences["gridsAdminUserGroups"]) ? $systemPreferences["gridsAdminUserGroups"] : [];
        if (!empty($gridsAdminUserGroups)) {
            $gridsAdminUserGroups = explode(", ", $gridsAdminUserGroups);
        } else {
            $gridsAdminUserGroups = [];
        }
        return in_array($this->ci->session->userdata("AUTH_user_group_id"), $gridsAdminUserGroups);
    }
    public function sso_login($user_model, $username)
    {
        $query["select"] = ["id"];
        $query["like"] = ["email", $username, "none"];
        $users_found = $user_model->load_all($query);
        return 0 < $users_found;
    }
    public function is_sso_enabled($user_model)
    {
        $system_preferences = $this->ci->system_preference->get_specified_system_preferences(["adEnabled", "loginWithoutDomain", "ssoApp4legal", "ssoApp4legalCustomerPortal"]);
        $sso_enabled = $user_model->modelCode == "U" && $system_preferences["ssoApp4legal"] || $user_model->modelCode == "CP" && $system_preferences["ssoApp4legalCustomerPortal"];
        return $sso_enabled;
    }
    public function validate_update_user_group($user_id, $user_group_id, $groups)
    {
        $result = $this->check_exist_groups($groups);
        if (empty($result)) {
            return ["result" => false, "msg" => $this->ci->lang->line("user_groups_dont_matches")];
        }
        if (in_array($user_group_id, $result)) {
            return ["result" => true, "msg" => ""];
        }
        $new_group_id = reset($result);
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $this->ci->user->fetch($user_id);
        $this->ci->user->set_field("user_group_id", $new_group_id);
        if ($this->ci->user->update()) {
            return ["result" => true, "msg" => ""];
        }
        return ["result" => false, "msg" => $this->ci->lang->line("error")];
    }
    public function check_exist_groups($groups)
    {
        $this->ci->load->model("user_group", "user_groupfactory");
        $this->ci->user_group = $this->ci->user_groupfactory->get_instance();
        return $this->ci->user_group->get_user_groups_by_names($groups);
    }
}

?>