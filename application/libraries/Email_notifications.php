<?php

use SendGrid\Mail\Mail;

class Email_notifications
{
    public $ci = null;
    public $mailer = null;
    public $system_preferences = null;
    public $caseAddNotificationsOptions = array('add_litigation_case', 'add_criminal_case','add_matter_case', 'add_ip_case');
    public $caseEditNotificationsOptions = array('edit_litigation_case', 'edit_matter_case', 'edit_ip_case');
    public $taskNotificationsOptions = array('add_tasks', 'edit_task', 'add_task_note', 'edit_task_status');
    public $opinionNotificationsOptions = array('add_opinions', 'edit_opinion', 'add_opinion_note', 'edit_opinion_status');
    public $meeting_notifications_options = array('add_meeting', 'edit_meeting');
    public $case_event_notification_options = array('add_case_event', 'edit_case_event');
    public $matter_container_notification_options = array('add_matter_container', 'edit_matter_container');
    public $contracts_notification_options = array('negotiation_completed', 'negotiation_comment_added', 'negotiation_requested', 'contract_rejected','add_contract_comment', 'cp_add_contract', 'edit_contract_status', 'edit_contract', 'contract_signed', 'contract_approved', 'renew_contract', 'amend_contract', 'add_contract_inform_assignee', 'add_contract', 'contract_awaiting_signature', 'needs_approval', 'contract_notify_requested_by_watchers_cp', 'notify_breached_contract_slas');

    public function __construct(){
        $this->ci = &get_instance();
        $this->ci->load->model('system_preference');
        $this->system_preferences = $this->ci->system_preference->get_key_groups();
        $outgoing_mail = $this->system_preferences['OutgoingMail'];
        $outgoing_mail_mailer = $outgoing_mail['outgoingMailMailer'];
        $this->ci->load->library($outgoing_mail_mailer, $outgoing_mail);
        $this->mailer = $this->ci->{$outgoing_mail_mailer};

        $this->ci->load->model('instance_data');
        $instance_data_values = $this->ci->instance_data->get_values();
        $this->instance_name = $instance_data_values['app_title'] ?? '';
    }

    public function notify($notificationsData)
    {
        extract($notificationsData);
        $templates = $this->get_templates($notificationsData);
        $to = is_array($to)? $to: array($to);
        $cc = is_array($cc)? $cc: array($cc);
        return $this->send_email(array_filter($to), $templates['subject'], $templates['content'], array_filter($cc), (isset($attachments) ? $attachments : array()));
    }


    public function get_templates($notificationsData)
    {
        extract($notificationsData);
        if ($object == 'add_ip_case' || $object == 'edit_ip_case') {
            $id = (int) $object_id;
        } else {
            $id = isset($objectModelCode) && $objectModelCode ? ($objectModelCode . (int) $object_id): (int) $object_id;
        }
        $mail_subject_prefix = $this->system_preferences['OutgoingMail']['outgoingMailSubjectPrefix'];
        $mail_subject_prefix_text = !empty($mail_subject_prefix) ? $mail_subject_prefix . ' - ' : '';
        $cp_mail_subject_prefix = $this->system_preferences['CustomerPortalConfig']['cpAppTitle'];
        $cp_mail_subject_prefix_text = !empty($cp_mail_subject_prefix) ? $cp_mail_subject_prefix . ' - ' : '';
        // used when $object is not sent to this function
        $default_subject_template = '[' . $mail_subject_prefix . '] - ' . $this->ci->lang->line('system_notifications');
        $default_content_template = '[Sheria360] - ' . $this->ci->lang->line('system_notifications');

        $default_content_footer = '<br /><br /><p style="font-size: 11px;">' . sprintf($this->ci->lang->line('template_notification_email_footer_content'), $mail_subject_prefix) . '</p>';
        $this->ci->load->helper('revert_comment_html');
        switch ($object) {
            case (in_array($object, $this->caseAddNotificationsOptions)):
                $templates = array();
                $tmp = $object;
                if ($object == 'add_litigation_case') {
                    $object = $this->ci->lang->line('litigation_case');
                } elseif ($object == 'add_criminal_case') {
                    $object = $this->ci->lang->line('criminal_case');
                } elseif ($object == 'add_matter_case') {
                    $object = $this->ci->lang->line('corporate_matter');
                } else {
                    $object = $this->ci->lang->line('IP');
                }
                if ($tmp == 'add_ip_case') {
                    $add_case_subject_template = $mail_subject_prefix_text . $id . "-" . $this->ci->lang->line('new_ip') . ': ' . htmlentities($caseSubject);
                    $add_case_content_template = sprintf($this->ci->lang->line('new_ip_added_content_email'), 'width:100%', $object, htmlentities($created_by), '<a href="' . site_url('intellectual_properties/edit/' . $object_id) . '" target="_blank">' . $id . '</a> ', '<a href="' . site_url('intellectual_properties/edit/' . $object_id) . '" target="_blank">' . htmlentities($caseSubject) . '</a> ', htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference), htmlentities($description));
                } else {
                    $add_case_subject_template = $mail_subject_prefix_text . $id . "-" . $this->ci->lang->line('new_case') . ': ' . htmlentities($caseSubject);
                    $add_case_content_template = sprintf($this->ci->lang->line('new_case_added_content_email'), 'width:100%', $object, htmlentities($created_by), '<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . $id . '</a> ', '<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . htmlentities($caseSubject) . '</a> ', htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference),htmlentities($filed_on),htmlentities($due_date), htmlentities($litigation_case_court_activity_purpose),htmlentities($description));
                }
                $object = 'add_case';
                break;
            case (in_array($object, $this->caseEditNotificationsOptions)):
                $templates = array();
                $edit_case_subject_template = $mail_subject_prefix_text . $id . "-" . $this->ci->lang->line('matter_update') . ': ' . htmlentities($caseSubject);
                if ($object == 'edit_ip_case') {
                    $objectName = $this->ci->lang->line('IP');
                    $edit_case_subject_template = $mail_subject_prefix_text . $id . "-" . $this->ci->lang->line('ip_update') . ': ' . htmlentities($caseSubject);
                    $edit_case_content_template = sprintf($this->ci->lang->line('edit_ip_content_email'), 'width:100%', ucfirst($objectName), htmlentities($modified_by), ' <a href="' . site_url('intellectual_properties/edit/' . $id) . '" target="_blank">' . $id . '</a>', '<a href="' . site_url('intellectual_properties/edit/' . $id) . '" target="_blank">' . htmlentities($caseSubject) . '</a> ', htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference));
                } else {
                    $objectName = $this->ci->lang->line($objectName);
                    $edit_case_content_template = sprintf($this->ci->lang->line('edit_case_content_email'), 'width:100%', ucfirst($objectName), htmlentities($modified_by), ' <a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . $id . '</a>', '<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . htmlentities($caseSubject) . '</a> ', htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference));
                }
                $object = 'edit_case';
                break;
            case (in_array($object, $this->taskNotificationsOptions)):
                extract($taskData);
                if($object === 'add_task_note'){ //add task note
                    $add_task_note_subject_template = $mail_subject_prefix_text . $id . '-' . $this->ci->lang->line('new_note_task');
                    $add_task_note_content_template = sprintf($this->ci->lang->line('new_task_note_added_email_content'), ' <a href="' . site_url('tasks/view/' . $task_id) . '" target="_blank">' . $id . '</a>', htmlentities($created_by), default_html_email_strip($description), default_html_email_strip($comment));
                    break;
                }
                if($object === 'edit_task_status'){ //edit task status
                    $edit_task_status_subject_template = $mail_subject_prefix_text . $id . '-' . $this->ci->lang->line('edit_task_status_subject_email');
                    $edit_task_status_content_template = sprintf($this->ci->lang->line('edit_task_status_email_content'), ' <a href="' . site_url('tasks/view/' . $task_id) . '" target="_blank">' . $id . '</a>', htmlentities($created_by), $old_status, $new_status, $assignee, $taskType, $dueDate, $this->ci->lang->line($priority), default_html_email_strip($taskDescription));
                    break;
                }
                // add task email content
                $add_tasks_subject_template = $mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('new_task_subject'), $taskData['assignee'], $taskData['dueDate']);
                $add_tasks_content_template = sprintf($this->ci->lang->line('task_notification_template_body'), ' <a href="' . site_url('tasks/view/' . $taskID) . '" target="_blank">' . $id . '</a>', $taskType, $taskStatus, $this->ci->lang->line($priority), (isset($legal_case_id) ? ' <a href="' . site_url('cases/edit/' . $legal_case_id) . '" target="_blank">'. $legal_case.'</a>' : $this->ci->lang->line('no_related_matters')) ,$dueDate, default_html_email_strip($taskDescription));
                //edit task mail  content
                $edit_task_subject_template = $mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('edit_task_subject_email'), htmlentities($modifiedBy));
                $edit_task_content_template = $this->ci->lang->line('the_task') . ' ' . ' <a href="' . site_url('tasks/view/' . $taskID) . '" target="_blank">' . $id . '</a>' . ' ' . sprintf($this->ci->lang->line('has_been_updated_by'), htmlentities($modifiedBy)) . '<br /><br /><b>' . $this->ci->lang->line('task_type') . '</b>: ' . $taskType . '<br /><b>' . $this->ci->lang->line('task_status') . '</b>: ' . $taskStatus . '<br /><b>' . $this->ci->lang->line('priority') . '</b>: ' . $this->ci->lang->line($priority) . '<br /><b>' . $this->ci->lang->line('related_case') . '</b>: ' . ( isset($legal_case_id) ? ' <a href="' . site_url('cases/edit/' . $legal_case_id) . '" target="_blank">' : $this->ci->lang->line('no_related_matters') ) . $legal_case . '</a>'  . '<br /> <b>' . $this->ci->lang->line('due_date') . '</b>: ' . $dueDate . '<br /> <b>' . $this->ci->lang->line('description') . '</b>: ' . default_html_email_strip($taskDescription);
                break;
            case (in_array($object, $this->opinionNotificationsOptions)):
                extract($opinionData);
                if($object === 'add_opinion_note'){ //add opinion note
                    $add_opinion_note_subject_template = $mail_subject_prefix_text . $id . '-' . $this->ci->lang->line('new_note_opinion');
                    $add_opinion_note_content_template = sprintf($this->ci->lang->line('new_opinion_note_added_email_content'), ' <a href="' . site_url('legal_opinions/view/' . $opinion_id) . '" target="_blank">' . $id . '</a>', htmlentities($created_by), default_html_email_strip($instructions), default_html_email_strip($comment));
                    break;
                }
                if($object === 'edit_opinion_status'){ //edit opinion status
                    $edit_opinion_status_subject_template = $mail_subject_prefix_text . $id . '-' . $this->ci->lang->line('edit_opinion_status_subject_email');
                    $edit_opinion_status_content_template = sprintf($this->ci->lang->line('edit_opinion_status_email_content'), ' <a href="' . site_url('legal_opinions/view/' . $opinion_id) . '" target="_blank">' . $id . '</a>', htmlentities($created_by), $old_status, $new_status, $assignee, $opinionType, $dueDate, $this->ci->lang->line($priority), default_html_email_strip($opinionInstructions));
                    break;
                }
                // add opinion email content
                $add_opinions_subject_template = $mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('new_opinion_subject'), $opinionData['assignee'], $opinionData['dueDate']);
                $add_opinions_content_template = sprintf($this->ci->lang->line('opinion_notification_template_body'), ' <a href="' . site_url('legal_opinions/view/' . $opinionID) . '" target="_blank">' . $id . '</a>', $opinionType, $opinionStatus, $this->ci->lang->line($priority), (isset($legal_case_id) ? ' <a href="' . site_url('cases/edit/' . $legal_case_id) . '" target="_blank">'. $legal_case.'</a>' : $this->ci->lang->line('no_related_matters')) ,$dueDate, default_html_email_strip($opinionInstructions));
                //edit opinion mail  content
                $edit_opinion_subject_template = $mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('edit_opinion_subject_email'), htmlentities($modifiedBy));
                $edit_opinion_content_template = $this->ci->lang->line('the_opinion') . ' ' . ' <a href="' . site_url('legal_opinions/view/' . $opinionID) . '" target="_blank">' . $id . '</a>' . ' ' . sprintf($this->ci->lang->line('has_been_updated_by'), htmlentities($modifiedBy)) . '<br /><br /><b>' . $this->ci->lang->line('opinion_type') . '</b>: ' . $opinionType . '<br /><b>' . $this->ci->lang->line('opinion_status') . '</b>: ' . $opinionStatus . '<br /><b>' . $this->ci->lang->line('priority') . '</b>: ' . $this->ci->lang->line($priority) . '<br /><b>' . $this->ci->lang->line('related_case') . '</b>: ' . ( isset($legal_case_id) ? ' <a href="' . site_url('cases/edit/' . $legal_case_id) . '" target="_blank">' : $this->ci->lang->line('no_related_matters') ) . $legal_case . '</a>'  . '<br /> <b>' . $this->ci->lang->line('due_date') . '</b>: ' . $dueDate . '<br /> <b>' . $this->ci->lang->line('instructions') . '</b>: ' . default_html_email_strip($opinionInstructions);
                break;
            case (in_array($object, $this->meeting_notifications_options)):
                extract($meeting_data);
                // add meeting email content
                $add_meeting_subject_template = $mail_subject_prefix_text . $id .': '.sprintf($this->ci->lang->line('new_meeting_subject_email'), $start_date);
                $add_meeting_content_template = $this->ci->lang->line('a_new_meeting_request_sent_to_you') . '<br /><br /> <b>' . $this->ci->lang->line('meeting_id') . '</b>: ' . $id . '<br /><b>' . $this->ci->lang->line('from') . '</b>: ' . $start_date . '<br /><b> ' . $this->ci->lang->line('to') . '</b>: ' . $to . '<br /><b> ' . $this->ci->lang->line('by') . '</b>: ' . htmlentities($modified_by) . '<br /><b> ' . $this->ci->lang->line('description') . '</b>: ' . default_html_email_strip($description);
                //edit meeting mail  content
                $edit_meeting_subject_template = $mail_subject_prefix_text . $id .': '. sprintf($this->ci->lang->line('edit_meeting_subject_email'), htmlentities($modified_by));
                $edit_meeting_content_template = sprintf($this->ci->lang->line('edit_meeting_content_email'), $id, htmlentities($modified_by)) . '<br /><br /> <b>' . $this->ci->lang->line('priority') . '</b>: ' . $this->ci->lang->line($priority) . '<br /><b> ' . $this->ci->lang->line('from') . '</b>: ' . $start_date . '<br /><b> ' . $this->ci->lang->line('to') . '</b>: ' . $to . '<br /><b> ' . $this->ci->lang->line('description') . '</b>: ' . default_html_email_strip($description);
                break;
            case 'add_user':
                //add user email template
                $add_user_subject_template = sprintf($this->ci->lang->line('invite_new_user_subject'), $department_core);
                $add_user_content_template =  sprintf($this->ci->lang->line('invite_new_user_body'), $link_to_account);
                break;
            case 'add_hearing':
                //add hearing email template
                //old with IDs $add_hearing_subject_template = $mail_subject_prefix_text . $legal_case_object_id . '-' . $hearingID . ': ' . sprintf($this->ci->lang->line('new_hearing_subject_email'), htmlentities($fromLoggedUser));
                $add_hearing_subject_template = $mail_subject_prefix_text . $hearingID . ' - ' .htmlentities($caseSubject) .". ". sprintf($this->ci->lang->line('new_hearing_subject_email'), htmlentities($fromLoggedUser));
                $add_hearing_content_template = sprintf($this->ci->lang->line('hearings_notification_template_body'), '<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . $legal_case_object_id . '</a>', '<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . htmlentities($caseSubject) . '</a>') . '<br /><br /> <b>' . $this->ci->lang->line('subject') . '</b>: ' .htmlentities($caseSubject) . '<br /><b>' . $this->ci->lang->line('hearing') . '</b>: ' . $hearingID . '<br /><b>' . $this->ci->lang->line('date') . '</b>: ' . $date . '<br /><b>'. $this->ci->lang->line('assigned_to') . '</b>: ' . $lawyers . '<br /><b>'. $this->ci->lang->line('comments') . '</b>: ' . $comments . '<br /><b>'. $this->ci->lang->line('summary_internal') . '</b>: ' . $summary . '<br /><b>'. $this->ci->lang->line('summary_to_client') . '</b>: ' . $summaryToClient . '<br /><b>' . $this->ci->lang->line('judgment') . '</b>: ' . $judgment;
                break;
            case 'hearing_verify_summary':
                // verify hearing summary
                $hearing_verify_summary_subject_template = $mail_subject_prefix_text . $legal_case_object_id . '-' . $hearingID . ': ' . sprintf($this->ci->lang->line('hearing_verify_summary_notification_email_subject'), $actionMaker);
                $hearing_verify_summary_content_template = sprintf($this->ci->lang->line('hearing_verify_summary_notification_template'), $actionMaker). '<br /><br /> <b> ' . $this->ci->lang->line('case') . '</b>: ' .'<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . htmlentities($caseSubject) . '</a>' . '<br /><b>' . $this->ci->lang->line('hearing') . '</b>: ' . $hearingID . '<br /><b>' . $this->ci->lang->line('date') . '</b>: ' . $date . '<br /><b>'. $this->ci->lang->line('assigned_to') . '</b>: ' . $lawyers . '<br /><b>'. $this->ci->lang->line('comments') . '</b>: ' . $comments . '<br /><b>'. $this->ci->lang->line('summary_internal') . '</b>: ' . $summary . '<br /><b>'. $this->ci->lang->line('summary_to_client') . '</b>: ' . $summaryToClient . '<br /><b>' . $this->ci->lang->line('judgment') . '</b>: ' . $judgment;
                break;
            case 'hearing_save_summary_to_notify_managers':
                // verify hearing summary
                $hearing_save_summary_to_notify_managers_subject_template = $mail_subject_prefix_text . $legal_case_object_id . '-' . $hearingID . ': ' . sprintf($this->ci->lang->line('hearing_save_summary_to_notify_managers_notification_email_subject'), $hearingID);
                $hearing_save_summary_to_notify_managers_content_template = sprintf($this->ci->lang->line('hearing_save_summary_to_notify_managers_notification_template'), $hearingID) . '<br /><br /> ' . '<b>' . $this->ci->lang->line('case') . '</b>: ' .'<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . htmlentities($caseSubject) . '</a>' . '<br /><br /> <b>' . $this->ci->lang->line('hearing') . '</b>: ' . $hearingID . '<br /><b>' . $this->ci->lang->line('date') . '</b>: ' . $date . '<br /><b>'. $this->ci->lang->line('assigned_to') . '</b>: ' . $lawyers . '<br /><b>'. $this->ci->lang->line('comments') . '</b>: ' . $comments . '<br /><b>'. $this->ci->lang->line('summary_internal') . '</b>: ' . $summary . '<br /><b>'. $this->ci->lang->line('summary_to_client') . '</b>: ' . $summaryToClient . '<br /><b>' . $this->ci->lang->line('judgment') . '</b>: ' . $judgment;
                break;
            case 'add_note_case':
                //add case note
                $add_note_case_subject_template = $mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('new_note_matter'), $this->ci->lang->line($objectName)) . ': ' . htmlentities($caseSubject);
                $add_note_case_content_template = sprintf($this->ci->lang->line('a_new_note_added_email_content'), '<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . $id . '</a>', htmlentities($created_by), '<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . htmlentities($caseSubject) . '</a> ', htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference), default_html_email_strip($caseNote));
                break;
            case 'legal_add_comment':
                //add note created by a4l user for a case requested from cp
                $legal_add_comment_subject_template = $mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('new_note_matter'), $this->ci->lang->line($objectName)) . ': ' . htmlentities($caseSubject);
                $legal_add_comment_content_template = sprintf($this->ci->lang->line('a_new_note_added_email_content'),  '<a href="' . base_url() . 'modules/customer-portal/tickets/view/' . $object_id . '" target="_blank">' . $id . '</a>', htmlentities($created_by),  htmlentities($caseSubject), htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference), default_html_email_strip($caseNote));
                break;
            case 'cp_add_comment':
                //add note created by cp user for a case requested from cp
                $cp_add_comment_subject_template = $cp_mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('new_note_matter'), $this->ci->lang->line($objectName)) . ' ' . $this->ci->lang->line('from_cp') . ': ' . htmlentities($caseSubject);
                $cp_add_comment_content_template = sprintf($this->ci->lang->line('a_new_note_added_email_content'),  '<a href="' . BASEURL . 'cases/edit/' . $object_id . '" target="_blank">' . $id . '</a>', htmlentities($created_by),  htmlentities($caseSubject), htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference), default_html_email_strip($caseNote));
                break;
            case 'legal_edit_ticket':
                //edit case status by a4l user for a  case originated form cp
                extract($content);
                $legal_edit_ticket_subject_template = $cp_mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('update_ticket_status_subject_email'), htmlentities($subject));
                $legal_edit_ticket_content_template = sprintf($this->ci->lang->line('update_ticket_status_content_email'),  '<a href="' . base_url() . 'modules/customer-portal/tickets/view/' . $object_id . '" target="_blank">' . $id . '</a>',$userProfile, $new_status_name, $old_status_name, htmlentities($subject), htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference));
                break;
            case 'cp_edit_ticket':
                //edit case status by cp user for a  case originated form cp
                extract($content);
                $cp_edit_ticket_subject_template = $cp_mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('update_ticket_status_subject_email_cp'), htmlentities($subject));
                $cp_edit_ticket_content_template = sprintf($this->ci->lang->line('update_ticket_status_content_email'), '<a href="' . BASEURL . 'cases/edit/' . $object_id . '" target="_blank">' . $id . '</a>',$userProfile, $new_status_name, $old_status_name, htmlentities($subject), htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference));
                break;
            case 'cp_add_ticket':
                //add case from cp
                $object_name_lang = $this->ci->lang->line($objectName);
                $cp_add_ticket_subject_template = $cp_mail_subject_prefix_text . $object_id . '-' . sprintf($this->ci->lang->line('cp_add_ticket_subject_email'), htmlentities($caseSubject));
                $cp_add_ticket_content_template = sprintf($this->ci->lang->line('cp_add_ticket_content_email'),$object_name_lang, $CpProfileName, ' <a href="' . BASEURL . 'cases/edit/' . $object_id . '" target="_blank">' . $objectModelCode . $object_id . '</a> ', $subject, $assignee, $client_name, $file_reference, $description);
                break;
            case 'request_type_notification_tab':
                $object_name_lang = $this->ci->lang->line($objectName);
                // send email to users that are set in the Notification Tab in the Request Type Form when adding a ticket from CP
                $request_type_notification_tab_subject_template = $cp_mail_subject_prefix_text . $object_id . '-' . sprintf($this->ci->lang->line('request_type_notification_tab_subject_email'), htmlentities($caseSubject));
                $request_type_notification_tab_content_template = sprintf($this->ci->lang->line('request_type_notification_tab_content_email'),$object_name_lang, $CpProfileName, ' <a href="' . BASEURL . 'cases/edit/' . $object_id . '" target="_blank">' . $objectModelCode . $object_id . '</a> ', $subject, $assignee, $client_name, $file_reference, $description);
                break;
            case 'cp_add_ticket_inform_assignee':
                // send email to assignee when adding a ticket from CP
                $cp_add_ticket_inform_assignee_subject_template = $cp_mail_subject_prefix_text . $object_id . '-' . sprintf($this->ci->lang->line('cp_add_ticket_inform_assignee_subject_email'), htmlentities($caseSubject));
                $cp_add_ticket_inform_assignee_content_template = sprintf($this->ci->lang->line('cp_add_ticket_inform_assignee_content_email'),$object_name_lang, $CpProfileName, ' <a href="' . BASEURL . 'cases/edit/' . $object_id . '" target="_blank">' . $objectModelCode . $object_id . '</a> ', $subject, $assignee, $client_name, $file_reference, $description);
                break;
            case 'ip_add_renewal':
                //add ip renewal
                $ip_add_renewal_subject_template = $mail_subject_prefix_text . $object_id . '-' . sprintf($this->ci->lang->line('ip_add_renewal_subject_email'), htmlentities($subject));
                $ip_add_renewal_content_template = sprintf($this->ci->lang->line('ip_add_renewal_content_email'), ' <a href="' . site_url('intellectual_properties/edit/' . $object_id) . '" target="_blank">' . $object_id . '</a> ', $created_by)  . '<br/><br/><b>'. $this->ci->lang->line('renewal_date') . '</b>: ' . $renewalDate. '<br/><b>'. $this->ci->lang->line('expiry_date') . '</b>: ' . $expiryDate . '<br/><b>' . $this->ci->lang->line('ip') . ' ' .$this->ci->lang->line('subject') . '</b>: ' .  $subject;
                break;
            case (in_array($object, $this->case_event_notification_options)):
                if ($object == 'add_case_event') {
                    $add_case_event_subject_template = $mail_subject_prefix_text . $legal_case_object_id . '-' . $object_id . ': ' . sprintf($this->ci->lang->line('add_case_event_subject_email'), $action_maker);
                    $add_case_event_content_template = sprintf($this->ci->lang->line('add_case_event_content_email'), $object_id, $action_maker, $created_on, '<a href="' . BASEURL . 'cases/edit/' . $object_id . '" target="_blank">' . $objectModelCode . $object_id . '-' . htmlentities($legal_case_subject). '</a>', htmlentities($subject));
                } else {
                    $edit_case_event_subject_template = $mail_subject_prefix_text . $legal_case_object_id . '-' . $object_id . ': ' .  sprintf($this->ci->lang->line('edit_case_event_subject_email'), $action_maker);
                    $edit_case_event_content_template = sprintf($this->ci->lang->line('edit_case_event_content_email'), $action_maker, htmlentities($legal_case_subject), htmlentities($subject), $modified_on);
                }
                break;
            case 'edit_case_status':
                //add case status
                extract($content);
                $edit_case_status_subject_template = $mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('edit_case_status_subject_email'), htmlentities($caseSubject));
                $edit_case_status_content_template = sprintf($this->ci->lang->line('edit_case_status_email_content'), ' <a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . $id . '</a>', htmlentities($modifier), $to, $from, htmlentities($caseSubject), htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference));
                break;
            case 'notify_breached_slas':
                //notify breached slas for cases
                extract($content);
                $notify_breached_slas_subject_template = $mail_subject_prefix_text . $id . '-' . sprintf($this->ci->lang->line('notify_breached_slas_subject_email'), $subject);
                $notify_breached_slas_content_template = sprintf($this->ci->lang->line('notify_breached_slas_email_content'), ' <a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . $id . '</a>', $time_spent, $subject, $assignee, $client_name, $file_reference);
                break;
            case (in_array($object, $this->matter_container_notification_options)):
                if ($object == 'add_matter_container') {
                    $add_matter_container_subject_template = $mail_subject_prefix_text . $id . '- '. sprintf($this->ci->lang->line('add_matter_container_subject_email'), $name);
                    $add_matter_container_content_template = sprintf($this->ci->lang->line('add_matter_container_content_email'), $fromLoggedUser, ' <a href="' . site_url('case_containers/edit/' . $object_id) . '" target="_blank">' . $id . '</a>', $practice_area, $name, default_html_email_strip($description));
                } else {
                    $edit_matter_container_subject_template = $mail_subject_prefix_text . $id . '- '. sprintf($this->ci->lang->line('edit_matter_container_subject_email'), $name);
                    $edit_matter_container_content_template = sprintf($this->ci->lang->line('edit_matter_container_content_email'), ' <a href="' . site_url('case_containers/edit/' . $object_id) . '" target="_blank">' . $id . '</a>', $fromLoggedUser, $practice_area, $name, default_html_email_strip($description));
                }
                break;
            case 'notify_new_client_portal_user':
                //send email to automatically created client portal user
                $notify_new_client_portal_user_subject_template = sprintf($this->ci->lang->line('notify_new_cp_user_subject_email'), $department_cp);
                $notify_new_client_portal_user_content_template = sprintf($this->ci->lang->line('notify_new_cp_user_content_email'), site_url('modules/customer-portal'), $user_email, $user_password);
                break;
            case 'core_user_assigned_case':
                $core_user_assigned_case_subject_template = 'Matter Assignment - ' . $this->instance_name;
                $this->ci->load->model('instance_data');
                $instance_data = $this->ci->instance_data->get_values();



                $apurl = "";
                if($instance_data['installationType'] == "on-cloud") {
                    $config = parse_ini_file(INSTANCE_PATH . '../config.ini');
                    $apurl = $config['advisor_portal_base_url'] . "/".($instance_data['instanceID'] ?? 0) ."/matter/" . $object_id;
                }
                else{
                    $apurl =   site_url('advisor-portal/matter/' . $object_id);
                }
                $core_user_assigned_case_content_template = 'Dear ' . $to_name . ',<br/><br/>You have been assigned to the matter <a href="' . $apurl . '" target="_blanc">' . $id . '</a> by ' . $fromLoggedUser . '.';
                break;
            case 'core_user_add_comment':
                //if this case is outsourced
                $core_user_add_comment_subject_template =  $mail_subject_prefix_text . $this->ci->lang->line('new_note') . ' ' . $this->ci->lang->line('on_case') . ' ' . htmlentities($caseSubject);
                $core_user_add_comment_content_template = sprintf($this->ci->lang->line('a_new_note_added_email_content'),  '<a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . $id . '</a>', htmlentities($created_by),  htmlentities($caseSubject), htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference), $caseNote);
                break;
            case 'core_user_edit_case_status':
                extract($content);
                $core_user_edit_case_status_subject_template = $mail_subject_prefix_text . $id . '-' . $this->ci->lang->line('edit_case_status_subject_email');
                $core_user_edit_case_status_content_template = sprintf($this->ci->lang->line('edit_case_status_email_content'), htmlentities($modifier), ' <a href="' . site_url('cases/edit/' . $object_id) . '" target="_blank">' . $id . '</a>', $from, $to, $on, htmlentities($assignee), htmlentities($client_name), htmlentities($file_reference), $this->ci->lang->line($priority));
                break;
            case 'notify_requested_by_watchers_cp':
                //send email to requested by and wathers
                $notify_requested_by_watchers_cp_subject_template = $cp_mail_subject_prefix_text . $id . ' (CP): ' . sprintf($this->ci->lang->line('notify_requested_by_watchers_cp_subject_email'), $category_cp, $department_cp);
                $notify_requested_by_watchers_cp_content_template = sprintf($this->ci->lang->line('notify_requested_by_watchers_cp_content_email'), $category_cp, $requested_by_name_cp, '<a href="' . BASEURL . 'modules/customer-portal/'.$controller.'/view/' . $object_id . '" target="_blank">' . $id . '</a> ');
                break;
            case 'contract_notify_requested_by_watchers_cp':
                //send email to requested by and wathers
                $contract_notify_requested_by_watchers_cp_subject_template = $cp_mail_subject_prefix_text . $id . ' (CP): ' . sprintf($this->ci->lang->line('notify_requested_by_watchers_cp_subject_email'), $category_cp, $department_cp);
                $email['content'] = sprintf($this->ci->lang->line('notify_requested_by_watchers_cp_content_email'), $category_cp, $requested_by_name_cp, '<a href="' . BASEURL . 'modules/customer-portal/'.$controller.'/view/' . $object_id . '" target="_blank">' . $id . '</a> ');
                break;
            case 'needs_approval':
                $contract_data['object_id'] = $object_id;
                $contract_data['id'] = $id;
                $needs_approval_subject_template =  $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('approval_needed_email_subject'), $contract_data['name']);
                $email['content'] = $this->ci->load->view("email_notifications/needs_approval_email_content", $contract_data, true);;
                break;
            case 'contract_awaiting_signature':
                extract($contract_data);
                $contract_awaiting_signature_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('contract_awaiting_signature_subject_template'), $contract_data['name']);
                $email['content'] = sprintf($this->ci->lang->line('contract_awaiting_signature_content_template'), '<a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']), $contract_data['name'], $contract_data['description']);
                break;
            case 'add_contract':
                extract($contract_data);
                $add_contract_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('add_contract_subject_email'), $contract_data['name']);
                $email['content'] = sprintf($this->ci->lang->line('add_contract_content_email'), '<a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $contract_data['name'], $fromLoggedUser, $contract_data['createdOn'], $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']),$contract_data['description']);
                break;
            case 'add_contract_inform_assignee':
                extract($contract_data);
                $add_contract_inform_assignee_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('add_contract_subject_email'), $contract_data['name']);
                $email['content'] =  sprintf($this->ci->lang->line('add_contract_inform_assignee_content_email'), '<a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $contract_data['name'], $fromLoggedUser, $contract_data['createdOn'], $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']),$contract_data['description']);
                break;
            case 'amend_contract':
                extract($contract_data);
                $amend_contract_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('amend_contract_subject_email'), $contract_data['original_contract_name']);
                $email['content'] = sprintf($this->ci->lang->line('amend_contract_content_email'), ' <a href="' . site_url('contracts/view/' . $contract_data['original_contract_id']) . '" target="_blank">' . $objectModelCode . $contract_data['original_contract_id'] . '</a>', $contract_data['original_contract_name'], $fromLoggedUser, date('Y-m-d H:i:s'), ' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $contract_data['name'], $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']), $contract_data['description']);
                break;
            case 'renew_contract':
                extract($contract_data);
                $renew_contract_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('renew_contract_subject_email'), $contract_data['name']);
                $email['content'] = sprintf($this->ci->lang->line('renew_contract_content_email'),' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $fromLoggedUser, date('Y-m-d H:i:s'), $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']), $contract_data['name'], $contract_data['description']);
                break;
            case 'contract_approved':
                extract($contract_data);
                $contract_approved_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('contract_approved_subject_email'), $fromLoggedUser);
                $email['content'] = sprintf($this->ci->lang->line('contract_approved_content_email'),' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $fromLoggedUser, date('Y-m-d H:i:s'), $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']), $contract_data['name'], $contract_data['description']);
                break;
            case 'contract_signed':
                extract($contract_data);
                $contract_signed_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('contract_signed_subject_email'), $mail_subject_prefix, $id, $contract_data['name']);
                $email['content'] = sprintf($this->ci->lang->line('contract_signed_content_email'),' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' .  $id . '</a>', $fromLoggedUser, date('Y-m-d H:i:s'), $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']), $contract_data['name'], $contract_data['description']);
                break;
            case 'edit_contract':
                extract($contract_data);
                $edit_contract_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('edit_contract_subject_email'), $contract_data['name']);
                $email['content'] = sprintf($this->ci->lang->line('edit_contract_content_email'), '<a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $contract_data['name'], $fromLoggedUser, $contract_data['createdOn'], $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']),$contract_data['description']);
                break;
            case 'edit_contract_status':
                extract($contract_data);
                $edit_contract_status_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('edit_contract_status_subject_email'), $mail_subject_prefix, $id);
                $email['content'] = sprintf($this->ci->lang->line('edit_contract_status_notification_body'), '<a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $contract_data['name'], $old_status, $status,$fromLoggedUser, $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']));
                break;
            case 'cp_add_contract':
                //add case from cp
                $cp_add_contract_subject_template = $cp_mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('cp_add_contract_subject_email'), htmlentities($contract_data['name']));
                $email['content'] = sprintf($this->ci->lang->line('cp_add_contract_content_email_body'), ' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $contract_data['name'], $fromLoggedUser, date('Y-m-d H:i:s'), $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'] ,$this->ci->lang->line($contract_data['priority']), $contract_data['description']);
                break;
            case 'add_contract_comment':
                $add_contract_comment_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('add_contract_comment_subject_email'), $contract_data['name']);
                $email['content'] = sprintf($this->ci->lang->line('add_contract_comment_content_email'), '<a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $contract_data['name'], $fromLoggedUser, $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'], $this->ci->lang->line($contract_data['priority']), default_html_email_strip($contract_data['comment'], "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p>"));
                break;
            case 'contract_rejected':
                $contract_rejected_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('contract_rejected_subject_email'), $contract_data['name']);
                $email['content'] = sprintf($this->ci->lang->line('contract_rejected_content_email'), ' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $fromLoggedUser, date('Y-m-d H:i:s'), $contract_data['type'], $contract_data['name']);
                break;
            case 'negotiation_requested':
                $negotiation_requested_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('negotiation_requested_subject_email'), $fromLoggedUser);
                $email['content'] = sprintf($this->ci->lang->line('negotiation_requested_content_email'),  $fromLoggedUser, ' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', date('Y-m-d H:i:s'), default_html_email_strip($contract_data['comment'], "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p>"), $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'] ,$this->ci->lang->line($contract_data['priority']), $contract_data['name']);
                break;
            case 'negotiation_comment_added':
                $negotiation_comment_added_subject_template = $mail_subject_prefix_text . $id .'-'.  sprintf($this->ci->lang->line('negotiation_comment_added_subject_email'), $fromLoggedUser);
                $email['content'] = sprintf($this->ci->lang->line('negotiation_comment_added_content_email'), ' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $fromLoggedUser, date('Y-m-d H:i:s'), default_html_email_strip($contract_data['comment'], "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p>"), $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'] ,$this->ci->lang->line($contract_data['priority']), $contract_data['name']);
                break;
            case 'negotiation_completed':
                $negotiation_completed_subject_template = $mail_subject_prefix_text . $id .'-'. $this->ci->lang->line('negotiation_completed_subject_email');
                $email['content'] = sprintf($this->ci->lang->line('negotiation_completed_content_email'), ' <a href="' . site_url('contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $fromLoggedUser, $contract_data['type'], $contract_data['contract_date'], $contract_data['end_date'] ,$this->ci->lang->line($contract_data['priority']), $contract_data['name']);
                break;
            case 'notify_breached_contract_slas':
                extract($content);
                $notify_breached_contract_slas_subject_template = $mail_subject_prefix_text . $id .'-'. sprintf($this->ci->lang->line('notify_breached_contract_slas_subject_email'), $name);
                $email['content'] = sprintf($this->ci->lang->line('notify_breached_contract_slas_content_email'), ' <a href="' . site_url('modules/contract/contracts/view/' . $object_id) . '" target="_blank">' . $id . '</a>', $time_spent, $name, $contract_date, $priority);
                break;
            case 'add_expense':
                $add_expense_subject_template = $mail_subject_prefix_text . '-' . sprintf($this->ci->lang->line('notification_expense_added'), $expenseId);
                $add_expense_content_template = sprintf($this->ci->lang->line('notification_expense_added_by'), $expenseId, $userLoggedInName) . '<br /><br />' . '<b>' . $this->ci->lang->line('expense_id') . '</b>: ' . '<a href="' . site_url('vouchers/expense_edit/' . $object_id) . '" target="_blank">' . htmlentities($expenseId) . '</a>' . '<br /> <b>' . $this->ci->lang->line('createdBy') . '</b>: ' . $userLoggedInName . '<br /> <b>' . $this->ci->lang->line('createdOn') . '</b>: ' . $createdOn;
                break;
            case 'expense_status_to_open':
                $expense_status_to_open_subject_template = $mail_subject_prefix_text . '-' . sprintf($this->ci->lang->line('notification_expense_updated'), $expenseId);
                $expense_status_to_open_content_template = sprintf($this->ci->lang->line('notification_expense_to_open'), $expenseId, $userLoggedInName) . '<br /><br />' . '<b>' . $this->ci->lang->line('expense_id') . '</b>: ' . '<a href="' . site_url('vouchers/expense_edit/' . $object_id) . '" target="_blank">' . htmlentities($expenseId) . '</a>' . '<br /> <b>' . $this->ci->lang->line('createdBy') . '</b>: ' . $userLoggedInName . '<br /> <b>' . $this->ci->lang->line('modifiedOn') . '</b>: ' . $modifiedOn;
                break;
            case 'expense_status_to_approved':
                $expense_status_to_approved_subject_template = $mail_subject_prefix_text . '-' . sprintf($this->ci->lang->line('notification_expense_updated'), $expenseId);
                $expense_status_to_approved_content_template = sprintf($this->ci->lang->line('notification_expense_to_approved'), $expenseId, $userLoggedInName) . '<br /><br />' . '<b>' . $this->ci->lang->line('expense_id') . '</b>: ' . '<a href="' . site_url('vouchers/expense_edit/' . $object_id) . '" target="_blank">' . htmlentities($expenseId) . '</a>' . '<br /> <b>' . $this->ci->lang->line('createdBy') . '</b>: ' . $userLoggedInName . '<br /> <b>' . $this->ci->lang->line('modifiedOn') . '</b>: ' . $modifiedOn;
                break;
            case 'expense_status_to_needs_revision':
                $expense_status_to_needs_revision_subject_template = $mail_subject_prefix_text . '-' . sprintf($this->ci->lang->line('notification_expense_updated'), $expenseId);
                $expense_status_to_needs_revision_content_template = sprintf($this->ci->lang->line('notification_expense_to_needs_revision'), $expenseId, $userLoggedInName) . '<br /><br />' . '<b>' . $this->ci->lang->line('expense_id') . '</b>: ' . '<a href="' . site_url('vouchers/expense_edit/' . $object_id) . '" target="_blank">' . htmlentities($expenseId) . '</a>' . '<br /> <b>' . $this->ci->lang->line('createdBy') . '</b>: ' . $userLoggedInName . '<br /> <b>' . $this->ci->lang->line('modifiedOn') . '</b>: ' . $modifiedOn;
                break;
            case 'expense_status_to_cancelled':
                $expense_status_to_cancelled_subject_template = $mail_subject_prefix_text . '-' . sprintf($this->ci->lang->line('notification_expense_updated'), $expenseId);
                $expense_status_to_cancelled_content_template = sprintf($this->ci->lang->line('notification_expense_to_cancelled'), $expenseId, $userLoggedInName) . '<br /><br />' . '<b>' . $this->ci->lang->line('expense_id') . '</b>: ' . '<a href="' . site_url('vouchers/expense_edit/' . $object_id) . '" target="_blank">' . htmlentities($expenseId) . '</a>' . '<br /> <b>' . $this->ci->lang->line('createdBy') . '</b>: ' . $userLoggedInName . '<br /> <b>' . $this->ci->lang->line('modifiedOn') . '</b>: ' . $modifiedOn;
                break;
            case 'delete_expense':
                $delete_expense_subject_template = $mail_subject_prefix_text . '-' . sprintf($this->ci->lang->line('notification_expense_deleted'), $expenseId);
                $delete_expense_content_template = sprintf($this->ci->lang->line('notification_expense_deleted_by'), $expenseId, $userLoggedInName) . '<br /><br />' . '<b>' . $this->ci->lang->line('expense_id') . '</b>: ' . $expenseId . '<br /> <b>' . $this->ci->lang->line('createdBy') . '</b>: ' . $userLoggedInName . '<br /> <b>' . $this->ci->lang->line('modifiedOn') . '</b>: ' . $modifiedOn;
                break;
            default:
                break;
        }
        if (in_array($object, $this->contracts_notification_options)){
            ${$object . '_content_template'}= $this->ci->load->view('templates/email', $email,true);
        }
        $templates['subject'] = isset(${$object . '_subject_template'}) ? ${$object . '_subject_template'} : $default_subject_template;
        $templates['content'] = (isset(${$object . '_content_template'})) ? ${$object . '_content_template'} . $default_content_footer : $default_content_template;
        return $templates;
    }

    public function send_email($to, $subject, $content, $cc = array(), $attachments = array(), $reply_to = array())
    {
        if ($this->ci->is_auth->is_layout_rtl())
        {
            $content = '<div style="direction:rtl;">' . $content . '</div>';
        }
        if($this->system_preferences['OutgoingMail']['use_a4l_smtp'] == 'yes'){
            $to_emails=[];
            $cc_emails=[];
            $to = !is_array($to) ? array($to) : $to;
            foreach($to as $value)
            {
                $to_emails[$value] ="";
            }
            $cc = !is_array($cc) ? array($cc) : $cc;
            foreach($cc as $value)
            {
                if(isset($value) && $value != '') {
                    $cc_emails[$value] ="";
                }
            }
            $this->ci->load->model('instance_data');
            $instance_data = $this->ci->instance_data->get_values();
            if ($instance_data['installationType'] === 'on-cloud' && isset($instance_data['instanceID'])) {
                $config = parse_ini_file(INSTANCE_PATH . '../config.ini');
                require COREPATH . '/libraries/sendgrid/vendor/autoload.php';
                $email = new Mail();
                $email->setFrom($config['email_noreply_from_address'], $this->system_preferences['OutgoingMail']['outgoingMailSubjectPrefix']);
                $email->setSubject($subject);
                $email->addTos($to_emails);
                if(!empty($cc_emails)) {
                    $email->addCcs($cc_emails);
                }
                $email->addContent("text/html", $content);
                if(!empty($attachments)){
                    foreach ($attachments as $attachment) {
                        if (strcmp($attachment['path'], "") && is_file($attachment['path'])) {
                            $extension = pathinfo($attachment['name'], PATHINFO_EXTENSION);
                            $f = finfo_open();
                            $file_encoded = file_get_contents($attachment['path']);
                            $email->addAttachment(
                                $file_encoded,
                                finfo_buffer($f, file_get_contents($attachment['path']), FILEINFO_MIME_TYPE),
                                $attachment['name'],
                                "attachment"
                            );
                        }
                    }

                }
                $sendgrid = new SendGrid($config['sg.api-key']);
                $sendgrid->send($email);
            }
        }else{
            $outgoing_mail = $this->system_preferences['OutgoingMail'];
            if (empty($outgoing_mail['outgoingMailFromAddress']) || empty($outgoing_mail['outgoingMailSmtpHost'])) {
                return false;
            }
            $this->mailer->set_from($outgoing_mail['outgoingMailFromAddress'], $outgoing_mail['outgoingMailFromName']);
            $this->mailer->set_address($to);
            if (!empty($cc)) {
                $this->mailer->add_cc($cc);
            }
            $this->mailer->set_subject(stripslashes(html_entity_decode($subject, ENT_QUOTES, 'UTF-8' )));
            if(!empty($attachments)){
                foreach ($attachments as $attachment) {
                    if (strcmp($attachment['path'], "") && is_file($attachment['path'])) {
                        $this->mailer->add_attachment($attachment['path'], $attachment['name']);
                    }
                }
            }
            if (is_array($reply_to) && !empty($reply_to)) {
                foreach($reply_to as $user){
                    $this->mailer->add_reply_to($user['email'], $user['name']);
                }
            }
            $this->mailer->set_message($content);
            if (!@$this->mailer->send_mail()) {
                return false;
            }
        }
        return true;
    }
}
