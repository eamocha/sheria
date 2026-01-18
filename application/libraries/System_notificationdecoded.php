<?php

class System_Notification
{
    public $ci;
    public $caseNotificationsOptions = ["add_litigation_case", "add_matter_case", "edit_litigation_case", "edit_matter_case", "add_note_case", "add_hearing", "hearing_verify_summary", "hearing_save_summary_to_notify_managers"];
    public $caseAddNotificationsOptions = ["add_litigation_case", "add_matter_case"];
    public $caseEditNotificationsOptions = ["edit_litigation_case", "edit_matter_case"];
    public $hearingVerificationNotificationsOptions = ["hearing_verify_summary", "hearing_save_summary_to_notify_managers"];
    public $taskNotificationsOptions = ["add_tasks", "edit_task", "edit_task_status", "add_task_note"];
    public $opinionNotificationsOptions = ["add_opinions", "edit_opinion", "edit_opinion_status", "add_opinion_note"];
    public $meeting_notifications_options = ["add_meeting", "edit_meeting"];
    public $ipNotificationsOptions = ["add_ip_case", "edit_ip_case"];
    public $case_event_notification_options = ["add_case_event", "edit_case_event"];
    public $expense_notification_options = ["add_expense", "edit_expense", "delete_expense", "expense_status_to_open", "expense_status_to_approved", "expense_status_to_needs_revision", "expense_status_to_cancelled"];
    public $contract_notification_options = ["add_contract", "add_contract_inform_assignee", "edit_contract", "add_contract_comment", "amend_contract", "renew_contract", "contract_approved", "contract_signed", "needs_approval", "contract_awaiting_signature", "negotiation_requested", "negotiation_comment_added", "negotiation_completed", "negotiation_forwarded"];
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model("notification", "notificationfactory");
        $this->ci->notification = $this->ci->notificationfactory->get_instance();
    }
    public function notification_add($notificationsData)
    {
        extract($notificationsData);
        $data = [];
        $result = false;
        $data["status"] = "unseen";
        $data["message"] = $this->get_object_template($notificationsData);
        if (isset($notificationsData["createdBy"])) {
            $data["createdBy"] = $notificationsData["createdBy"];
            $data["createdOn"] = $notificationsData["createdOn"];
            $data["modifiedBy"] = $notificationsData["modifiedBy"];
            $data["modifiedOn"] = $notificationsData["modifiedOn"];
        }
        $toIds = isset($toIds) ? $toIds : [];
        $ccIds = isset($ccIds) ? $ccIds : [];
        if ($object == "add_note_case" && $to == $targetUser) {
            return true;
        }
        if (in_array($object, $this->taskNotificationsOptions) && isset($targetUser) && $targetUser == $this->ci->is_auth->get_user_id() && ($key = array_search($targetUser, $toIds)) !== false) {
            unset($toIds[$key]);
        }
	
        if ($targetUser != $this->ci->is_auth->get_user_id() || $object == "add_meeting" || $object == "add_note_case" || in_array($object, $this->taskNotificationsOptions)|| in_array($object, $this->opinionNotificationsOptions) || in_array($object, $this->expense_notification_options)) {
            if (isset($to) && !is_array($to)) {
                $data["user_id"] = $to;
                $result = $this->ci->notification->add($data);
            }
            $secondTargetUser = isset($secondTargetUser) ? $secondTargetUser : false;
            if (isset($secondTargetUser) && $secondTargetUser == $this->ci->is_auth->get_user_id() && ($key = array_search($secondTargetUser, $ccIds)) !== false) {
                unset($ccIds[$key]);
            }
            foreach ($toIds as $userId) {
                $data["user_id"] = $userId;
                $result = $this->ci->notification->add($data);
            }
            foreach ($ccIds as $user) {
                if (!in_array($user, $toIds)) {
                    if ($user) {
                        $data["user_id"] = $user;
                        $this->ci->notification->add($data);
                    }
                }
            }
        }
    }
    public function get_object_template($notificationsData)
    {
        extract($notificationsData);
        $caseSubject = isset($caseSubject) ? $caseSubject : "";
        $userLoggedInName = $user_logged_in_name ?? $this->ci->is_auth->get_fullname();
        $defaultTemplate = "";
        if (!in_array($object, $this->meeting_notifications_options)) {
            $attributes = "class=\"notify-links\" objectClearID=\"" . $object_id . "\" objectID=\"" . $objectModelCode . $object_id . "\"";
        }
        if (in_array($object, $this->caseNotificationsOptions)) {
            $objectType = "cases";
            if (in_array($object, $this->caseAddNotificationsOptions)) {
                $object = "addCase";
            } else {
                if (in_array($object, $this->caseEditNotificationsOptions)) {
                    $object = "editCase";
                }
            }
            $attributes .= " objectType=\"" . $objectType . "\" ";
            $addCaseTemplate = $this->ci->lang->line("a_new_case") . " <a href=\"cases/edit/%s\" objectName=\"" . $objectName . "\" " . $attributes . ">%s</a> " . $this->ci->lang->line("has_been_assigned_to_you");
            $editCaseTemplate = $this->ci->lang->line("the_case") . " <a href=\"cases/edit/%s\" objectName=\"" . $objectName . "\" " . $attributes . ">%s</a> " . sprintf($this->ci->lang->line("has_been_updated_by"), $userLoggedInName);
            $add_note_caseTemplate = $this->ci->lang->line("a_new_note_added") . " <a href=\"cases/edit/%s\" objectName=\"" . $objectName . "\" " . $attributes . ">%s</a>.";
            $add_hearingTemplate = $this->ci->lang->line("hearings_notification_template") . " <a href=\"cases/edit/%s\" objectName=\"" . $objectName . "\" " . $attributes . ">%s</a>.";
            if (in_array($object, $this->hearingVerificationNotificationsOptions)) {
                $hearing_verify_summaryTemplate = sprintf($this->ci->lang->line("hearing_verify_summary_notification_template"), $actionMaker);
                $hearing_save_summary_to_notify_managersTemplate = sprintf($this->ci->lang->line("hearing_save_summary_to_notify_managers_notification_template"), $hearingID);
            }
        } else {
            if (in_array($object, $this->taskNotificationsOptions)) {
                $attributes .= " objectType=\"tasks\" ";
                if ($object === "add_tasks") {
                    $add_tasksTemplate = $this->ci->lang->line("a_new_task") . " <a href=\"tasks/view/" . $object_id . "\" objectName=\"task\" " . $attributes . ">" . $objectModelCode . $object_id . "</a> " . sprintf($this->ci->lang->line("has_been_assigned_to"), $taskData["assignee"]);
                }
                $edit_taskTemplate = $this->ci->lang->line("the_task") . " <a href=\"tasks/view/" . $object_id . "\" objectName=\"task\" " . $attributes . ">" . $objectModelCode . $object_id . "</a> " . $this->ci->lang->line("has_been_updated");
                $edit_task_statusTemplate = $this->ci->lang->line("the_task") . " <a href=\"tasks/view/" . $object_id . "\" objectName=\"task\" " . $attributes . ">" . $objectModelCode . $object_id . "</a> " . $this->ci->lang->line("has_been_updated");
                $add_task_noteTemplate = $this->ci->lang->line("a_new_note_added") . " <a href=\"tasks/view/" . $object_id . "\" objectName=\"task\" " . $attributes . ">" . $objectModelCode . $object_id . "</a>";
            } else {
				if (in_array($object, $this->opinionNotificationsOptions)) {
                $attributes .= " objectType=\"opinions\" ";
                if ($object === "add_opinions") {
                    $add_opinionsTemplate = $this->ci->lang->line("a_new_opinion") . " <a href=\"legal_opinions/view/" . $object_id . "\" objectName=\"opinion\" " . $attributes . ">" . $objectModelCode . $object_id . "</a> " . sprintf($this->ci->lang->line("has_been_assigned_to"), $opinionData["assignee"]);
                }
                $edit_opinionTemplate = $this->ci->lang->line("the_opinion") . " <a href=\"legal_opinions/view/" . $object_id . "\" objectName=\"opinion\" " . $attributes . ">" . $objectModelCode . $object_id . "</a> " . $this->ci->lang->line("has_been_updated");
                $edit_opinion_statusTemplate = $this->ci->lang->line("the_opinion") . " <a href=\"legal_opinions/view/" . $object_id . "\" objectName=\"opinion\" " . $attributes . ">" . $objectModelCode . $object_id . "</a> " . $this->ci->lang->line("has_been_updated");
                $add_opinion_noteTemplate = $this->ci->lang->line("a_new_note_added") . " <a href=\"legal_opinions/view/" . $object_id . "\" objectName=\"opinion\" " . $attributes . ">" . $objectModelCode . $object_id . "</a>";
            } else {
                if (in_array($object, $this->meeting_notifications_options)) {
                    $objectType = "meetings";
                    $add_meetingTemplate = $this->ci->lang->line("you_are_invited_to_a_new_meeting") . " " . $object_title;
                    $edit_meetingTemplate = $this->ci->lang->line("the_meeting") . " " . $object_title . " " . $this->ci->lang->line("has_been_updated");
                } else {
                    if (in_array($object, $this->ipNotificationsOptions)) {
                        $attributes .= " objectType=\"cases\" ";
                        $add_ip_caseTemplate = $this->ci->lang->line("a_new_case") . " <a href=\"intellectual_properties/edit/%s\" objectName=\"ip\" " . $attributes . ">%s</a> " . $this->ci->lang->line("has_been_assigned_to_you");
                        $edit_ip_caseTemplate = $this->ci->lang->line("the_case") . " <a href=\"intellectual_properties/edit/%s\" objectName=\"ip\" " . $attributes . ">%s</a> " . sprintf($this->ci->lang->line("has_been_updated_by"), $userLoggedInName);
                    } else {
                        if (in_array($object, $this->case_event_notification_options)) {
                            $add_case_eventTemplate = sprintf($this->ci->lang->line("add_case_event_notification"), $subject, $action_maker);
                            $edit_case_eventTemplate = sprintf($this->ci->lang->line("edit_case_event_notification"), $subject);
                        } else {
                            if (in_array($object, $this->contract_notification_options)) {
                                $add_contractTemplate = sprintf($this->ci->lang->line("add_contract_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"], $userLoggedInName);
                                $amend_contractTemplate = sprintf($this->ci->lang->line("amend_contract_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"], $userLoggedInName);
                                $renew_contractTemplate = sprintf($this->ci->lang->line("renew_contract_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"], $userLoggedInName);
                                $contract_approvedTemplate = sprintf($this->ci->lang->line("contract_approved_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"], $userLoggedInName);
                                $contract_signedTemplate = sprintf($this->ci->lang->line("contract_signed_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"], $userLoggedInName);
                                $add_contract_inform_assigneeTemplate = sprintf($this->ci->lang->line("add_contract_inform_assignee_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"], $userLoggedInName);
                                $edit_contractTemplate = sprintf($this->ci->lang->line("edit_contract_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"], $userLoggedInName);
                                $add_contract_commentTemplate = sprintf($this->ci->lang->line("add_contract_comment_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"], $userLoggedInName);
                                $needs_approvalTemplate = sprintf($this->ci->lang->line("needs_approval_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"]);
                                $contract_awaiting_signatureTemplate = sprintf($this->ci->lang->line("contract_awaiting_signature_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"]);
                                $negotiation_requestedTemplate = sprintf($this->ci->lang->line("negotiation_requested_notification"), $userLoggedInName, " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"]);
                                $negotiation_comment_addedTemplate = sprintf($this->ci->lang->line("negotiation_comment_added_notification"), $userLoggedInName, " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"]);
                                $negotiation_completedTemplate = sprintf($this->ci->lang->line("negotiation_completed_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"]);
                                $negotiation_forwardedTemplate = sprintf($this->ci->lang->line("negotiation_forwarded_notification"), " <a href=\"modules/contract/contracts/view/" . $object_id . "\">" . $objectModelCode . $object_id . "</a> ", $contract_data["name"]);
                            } else {
                                if (in_array($object, $this->expense_notification_options)) {
                                    $objectType = "expenses";
                                    $attributes .= " objectType=\"expenses\" ";
                                    $delete_expenseTemplate = sprintf($this->ci->lang->line("notification_expense_deleted_by"), $object_title, $userLoggedInName);
                                    $expense_url = "<a href=\"" . app_url("modules/money/vouchers/expense_edit/" . $object_id) . "\" objectName=\"" . $objectName . "\" " . $attributes . ">" . $object_title . "</a>";
                                    $edit_expenseTemplate = sprintf($this->ci->lang->line("notification_expense_updated_by"), $expense_url, $userLoggedInName);
                                    $add_expenseTemplate = sprintf($this->ci->lang->line("notification_expense_added_by"), $expense_url, $userLoggedInName);
                                    $expense_status_to_openTemplate = sprintf($this->ci->lang->line("notification_expense_to_open"), $expense_url, $userLoggedInName);
                                    $expense_status_to_approvedTemplate = sprintf($this->ci->lang->line("notification_expense_to_approved"), $expense_url, $userLoggedInName);
                                    $expense_status_to_needs_revisionTemplate = sprintf($this->ci->lang->line("notification_expense_to_needs_revision"), $expense_url, $userLoggedInName);
                                    $expense_status_to_cancelledTemplate = sprintf($this->ci->lang->line("notification_expense_to_cancelled"), $expense_url, $userLoggedInName);
                                }
                            }
                        }
                    }
                }
            }
        }
        $template = isset(${$object . "Template"}) ? ${$object . "Template"} : $defaultTemplate;
        if ($this->ci->db->dbdriver === "mysqli") {
            $object_id = str_pad($object_id, 8, "0", STR_PAD_LEFT);
        }
        $template = sprintf($template, $object_id, mb_substr($caseSubject, 0, 14));
        return $template;
    }
}

}