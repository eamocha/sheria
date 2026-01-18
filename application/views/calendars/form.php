<?php
$system_preferences = $this->session->userdata("systemPreferences");
$select_options_name = "attendees";
?>
<div class="primary-style">
    <div id="meeting-dialog" class="modal fade modal-container modal-resizable">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $title ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?php echo form_open(current_url(), 'class="form-horizontal" id="meeting-form" novalidate') ?>
                    <?php echo form_input(['name' => 'id', 'value' => $meeting_data['id'], 'id' => 'event-id', 'type' => 'hidden']) ?>
                    <?php echo form_input(['name' => 'send_notifications_email', 'value' => $hide_show_notification, 'id' => 'send_notifications_email', 'type' => 'hidden']) ?>
                    <?php echo form_input(['name' => 'clone', 'value' => 'no', 'id' => 'clone', 'type' => 'hidden']) ?>
                    <?php echo form_input(['name' => 'meeting_from', 'value' => 'meeting', 'id' => 'meeting_from', 'type' => 'hidden']) ?>
                    <?php echo form_input(['name' => 'task_location_id', 'value' => $meeting_data['task_location_id'], 'id' => 'task_location_id', 'type' => 'hidden']) ?>

                    <div class="p-0">
                        <div class="form-group row p-0">
                            <label class="control-label col-md-3 pr-0 required col-xs-5">
                                <?php echo $this->lang->line('title') ?>
                            </label>
                            <div class="col-md-8 pr-0">
                                <?php echo form_input('title', $meeting_data['title'], 'id="title" class="form-control" dir="auto"') ?>
                                <div data-field="title" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div id="date-pair">
                        <div class="p-0" id="start-date-container">
                            <div class="form-group row p-0">
                                <label class="control-label col-md-3 pr-0 col-xs-5 required">
                                    <?php echo $this->lang->line('from') ?>
                                </label>
                                <div class="col-md-9 pr-0 d-flex col-xs-12">
                                    <div class="input-group date col-md-5 col-xs-8 p-0 date-picker" id="start-date">
                                        <?php echo form_input('start_date', $meeting_data['start_date'], 'id="start-date-input" class="date start form-control"') ?>
                                        <span class="input-group-addon">
                                                <i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i>
                                            </span>
                                    </div>
                                    <div class="col-md-3 col-xs-4">
                                        <?php echo form_input('start_time', $meeting_data['start_time'], 'id="start-time" class="time start form-control"') ?>
                                    </div>
                                    <?php if ($system_preferences['hijriCalendarConverter']){ ?>
                                        <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date">
                                            <a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#start-date','#meeting-container'));"
                                               title="<?php echo $this->lang->line('hijri_date_converter') ?>" class="btn btn-link">
                                                <?php echo $this->lang->line('hijri') ?>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <div data-field="start_date" class="inline-error d-none"></div>
                                    <div data-field="start_time" class="inline-error d-none"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row p-0" id="end-date-container">
                            <label class="control-label col-md-3 pr-0 col-xs-5 required">
                                <?php echo $this->lang->line('to') ?>
                            </label>
                            <div class="col-md-9 pr-0 d-flex col-xs-12">
                                <div class="input-group date col-md-5 col-xs-8 p-0 date-picker" id="end-date">
                                    <?php echo form_input('end_date', $meeting_data['end_date'], 'id="end-date-input" class="date end form-control"') ?>
                                    <span class="input-group-addon">
                                            <i class="fa fa-calendar purple_color p-9 cursor-pointer-click"></i>
                                        </span>
                                </div>
                                <div class="col-md-3 col-xs-4">
                                    <?php echo form_input('end_time', $meeting_data['end_time'], 'id="end-time" class="time end form-control"') ?>
                                </div>
                                <?php if ($system_preferences['hijriCalendarConverter']){ ?>
                                    <div class="col-md-3 p-0 col-xs-2 visualize-hijri-date">
                                        <a href="javascript:;" id="date-conversion" onclick="HijriConverter(jQuery('#end-date','#meeting-container'));"
                                           title="<?php echo $this->lang->line('hijri_date_converter') ?>" class="btn btn-link">
                                            <?php echo $this->lang->line('hijri') ?>
                                        </a>
                                    </div>
                                <?php } ?>
                                <div data-field="end_date" class="inline-error d-none"></div>
                                <div data-field="end_time" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-0">
                        <div class="form-group row p-0">
                            <label class="control-label col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('related_case') ?>
                            </label>
                            <div class="col-md-8 col-xs-10 pr-0">
                                <?php echo form_input(['name' => 'legal_case_id', 'value' => isset($meeting_data['legal_case_id']) && $meeting_data['legal_case_id'] ? $meeting_data['legal_case_id'] : '', 'id' => 'legal-case-id', 'type' => 'hidden']) ?>
                                <?php
                                $case_value = '';
                                if (isset($meeting_data['legal_case_id']) && $meeting_data['legal_case_id']) {
                                    $case_value = $case_model_code . $meeting_data['legal_case_id'] . ': ' .
                                            (strlen($meeting_data['case_subject']) > 39 ?
                                                    mb_substr($meeting_data['case_subject'], 0, 39) . '...' :
                                                    $meeting_data['case_subject']);
                                } elseif (isset($case_subject) && $case_subject) {
                                    $case_value = $case_subject;
                                }
                                ?>
                                <?php echo form_input('related_case', $case_value, 'placeholder="' . $this->lang->line('client_or_matter_placeholder') . '" id="case-lookup" class="form-control search" title="' . $meeting_data['case_subject'] . '"') ?>
                                <div data-field="legal_case_id" class="inline-error d-none"></div>
                            </div>
                            <?php if ($meeting_data['id']){ ?>
                                <div id="case-subject" class="help-inline col-md-1 p-0 <?php echo $meeting_data['legal_case_id'] ? '' : 'd-none' ?>">
                                    <a target="_blank" id="case-link" class="btn btn-link"
                                       href="<?php echo $meeting_data['case_category'] == 'IP' ? 'intellectual_properties/edit/' : 'cases/edit/' ?><?php echo $meeting_data['legal_case_id'] ?>">
                                        <i class="purple_color <?php echo $this->is_auth->is_layout_rtl() ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' ?>"> </i>
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="col-md-3 pr-0 col-xs-2">&nbsp;</div>
                            <div class="col-md-9 pr-0 col-xs-10">
                                <div class="inline-text"><?php echo $this->lang->line('helper_case_autocomplete') ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-0">
                        <div class="form-group row p-0">
                            <label class="control-label col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('description') ?>
                            </label>
                            <div class="col-md-8 pr-0">
                                <?php echo form_textarea(['name' => 'description', 'value' => $meeting_data['description'], 'id' => 'description', 'class' => 'form-control', 'rows' => '5', 'cols' => '0', 'dir' => 'auto']) ?>
                                <div data-field="description" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row p-0">
                        <div class="offset-md-3">
                            <div class="checkbox ml-3">
                                <label>
                                    <?php echo form_checkbox('private', $meeting_data['private'], $meeting_data['private'] == 'yes', 'id="private" onclick="enableDisablePrivacy(this,jQuery(\'#meeting-container\'));"') ?>
                                    <?php echo $this->lang->line('set_as_private') ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="p-0 attendees-container" id="attendees-container">
                        <div class="form-group row p-0">
                            <label class="control-label col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('attendees') ?>
                            </label>
                            <div class="col-md-7 pr-0 col-xs-10 users-lookup-container">
                                <div class="input-group col-md-12 p-0 margin-bottom-5">
                                    <?php echo form_input('look_up_attendees', '', 'id="attendees-lookup" class="form-control users-lookup"') ?>
                                    <span class="input-group-addon bs-caret users-lookup-icon" onclick="jQuery('#attendees-lookup').focus();">
                                            <span class="caret"></span>
                                        </span>
                                </div>
                                <div id="selected-attendees" class="height-auto m-0">
                                    <?php if (!empty($attendees)){ ?>
                                        <?php foreach ($attendees as $key => $value){ ?>
                                            <div class="row multi-option-selected-items m-0"
                                                 onmouseout="checkParticipant('<?php echo $value['id'] ?>')"
                                                 onmouseover="showParticipant('<?php echo $value['id'] ?>')"
                                                 id="<?php echo $select_options_name . $value['id'] ?>">
                                                <img class="img-circle" width="30" src="users/get_profile_picture/<?php echo $value['id'] ?>/1">
                                                <span id="<?php echo $value['id'] ?>"><?php echo htmlentities($value['name']) ?></span>
                                                <?php echo form_input(['name' => $select_options_name . '[]', 'value' => $value['id'], 'type' => 'hidden']) ?>
                                                <?php echo form_input(['name' => 'mandatory[]', 'value' => $value['mandatory'], 'id' => 'input_attend_status_' . $value['id'], 'type' => 'hidden']) ?>
                                                <?php echo form_input(['name' => 'participant[]', 'value' => $value['participant'], 'id' => 'input_participant_' . $value['id'], 'type' => 'hidden']) ?>

                                                <?php if ($value['id'] !== intval($meeting_data['createdBy'])){ ?>
                                                    <a href="javascript:;" class="btn btn-default btn-xs btn-link pull-right remove-button remove-button-event" tabindex="-1"
                                                       onclick="removeBoxElement(jQuery(this.parentNode),'#selected-attendees','attendees-container','#meeting-container');">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </a>
                                                <?php } ?>

                                                <a class="attend_status" href="javascript:;" id="attend_status_<?php echo $value['id'] ?>"
                                                   data-type="<?php echo $value['mandatory'] == '1' ? 'optional' : 'mandatory' ?>"
                                                   onclick="changeAttendStatus('<?php echo $value['id'] ?>');">
                                                    <?php
                                                    if ($value['mandatory'] == '1') {
                                                        $image_name = 'mark_mandatory.png';
                                                        $text = $this->lang->line('mark_optional');
                                                    } else {
                                                        $image_name = 'mark_optional.png';
                                                        $text = $this->lang->line('mark_required');
                                                    }
                                                    ?>
                                                    <img width="25" class="tooltipTable" tooltipTitle="<?php echo $text ?>" src="assets/images/icons/<?php echo $image_name ?>">
                                                </a>
                                                &nbsp;
                                                <a class="attend_status <?php echo $value['participant'] != 1 ? 'd-none' : '' ?>"
                                                   id="participant_status_<?php echo $value['id'] ?>"
                                                   onclick="changeParticipantStatus('<?php echo $value['id'] ?>');" href="javascript:;">
                                                    <?php
                                                    $text_participant = $value['participant'] == '1' ?
                                                            $this->lang->line('mark_non_participant') :
                                                            $this->lang->line('mark_participant');
                                                    ?>
                                                    <img class="img-circle tooltipTable" tooltipTitle="<?php echo $text_participant ?>" src="assets/images/icons/unparticipant.png">
                                                </a>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div data-field="attendees" class="inline-error d-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-0">
                        <div class="row form-group p-0">
                            <label class="control-label col-md-3 pr-0 col-xs-5">
                                <?php echo $this->lang->line('location') ?>
                            </label>
                            <div class="col-md-8 d-flex pr-0 col-xs-10">
                                <div class="col-md-11 p-0">
                                    <?php echo form_input(['name' => 'location', 'value' => $meeting_data['location'], 'id' => 'location', 'class' => 'form-control lookup', 'data-field' => 'administration-task_locations', 'title' => $this->lang->line('start_typing')]) ?>
                                    <div class="inline-text"><?php echo $this->lang->line('helper_autocomplete') ?></div>
                                    <div data-field="task_location_id" class="inline-error d-none"></div>
                                </div>
                                <div class="col-md-1 p-0 col-xs-2">
                                    <a href="javascript:;" onclick="quickAdministrationDialog('task_locations', jQuery('#meeting-form', '#meeting-dialog'), true);" class="btn btn-link">
                                        <i class="fa-solid fa-square-plus p-1 font-18"> </i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row p-0">
                        <label class="control-label col-md-3 pr-0 col-xs-5">
                            <?php echo $this->lang->line('priority') ?>
                        </label>
                        <div class="col-md-8 pr-0 col-xs-10">
                            <div class="col-md-12 p-0">
                                <select name="priority" class="form-control" id="priority" data-live-search="true">
                                    <?php foreach ($priorities as $key => $value){ ?>
                                        <option data-icon="priority-<?php echo $key ?>"
                                                <?php echo $key == $meeting_data['priority'] ? 'selected' : '' ?>
                                                value="<?php echo $key ?>">
                                            <?php echo $value ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row p-0">
                        <label class="control-label pr-0 col-md-3 col-xs-12">
                            <?php echo $this->lang->line('meeting_type') ?>
                        </label>
                        <div class="col-md-8 d-flex pr-0">
                            <div class="col-md-11 p-0">
                                <?php echo form_dropdown('event_type_id', $meeting_data['meeting_types'], $meeting_data['event_type_id'], 'id="event_types" class="form-control select-picker" data-live-search="true" data-field="administration-event_types" data-size="' . $this->session->userdata('max_drop_down_length') . '"') ?>
                            </div>
                            <div class="col-md-1 p-0 col-xs-2">
                                <a href="javascript:;" onclick="quickAdministrationDialog('event_types', jQuery('#meeting-form', '#meeting-dialog'),true);" class="btn btn-link">
                                    <i class="fa-solid fa-square-plus p-1 font-18"> </i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close() ?>
                </div>

                <div class="modal-footer justify-content-between">
                    <?php $this->load->view('templates/send_email_option_template', ['type' => $meeting_data['id'] ? 'edit_meeting' : 'add_meeting', 'container' => '#meeting-container', 'hide_show_notification' => $hide_show_notification
                    ]) ?>
                    <div>
                        <span class="loader-submit"></span>
                        <div class="btn-group">
                            <button type="button" class="btn btn-save btn-add-dropdown modal-save-btn" id="event-save">
                                <?php echo $this->lang->line('save') ?>
                            </button>
                            <?php if (!$meeting_data['id']){ ?>
                                <button type="button" class="btn btn-save dropdown-toggle btn-add-dropdown modal-save-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a class="dropdown-item" href="javascript:;" onclick="cloneDialog(jQuery('#meeting-container'), meetingFormSubmit);">
                                            <?php echo $this->lang->line('create_another') ?>
                                        </a>
                                    </li>
                                </ul>
                            <?php } ?>
                        </div>
                        <button type="button" class="btn btn-link" data-dismiss="modal">
                            <?php echo $this->lang->line('cancel') ?>
                        </button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript" charset="utf-8">
    var authIdLoggedIn = '<?php echo $this->is_auth->get_user_id() ?>';
</script>