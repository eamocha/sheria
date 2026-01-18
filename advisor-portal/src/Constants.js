export const FORMS_NAMES = {
    advisorTimeLogAddForm: 'advisor-time-log-add-form',
    advisorTimeLogImportForm: 'advisor-time-log-import-form',
    advisorTimeLogEditForm: 'advisor-time-log-edit-form',
    advisorTaskAddForm: 'advisor-task-add-form',
    advisorTaskEditForm: 'advisor-task-edit-form',
    AdvisorTaskNoteAddForm: 'advisor-task-note-add-form',
    AdvisorTaskNoteEditForm: 'advisor-task-note-edit-form',
    litigationCaseEditForm: 'litigation-case-edit-form',
    litigationCaseCustomFieldsEditForm: 'litigation-case-custom-fields-edit-form',
    legalCaseNoteAddForm: 'legal-case-note-add-form',
    legalCaseNoteEditForm: 'legal-case-note-edit-form',
    litigationCaseStageEditForm: 'litigation-case-stage-edit-form',
    litigationCaseStageChangeForm: 'litigation-case-stage-change-form',
    corporateMatterEditForm: 'corporate-matter-edit-form',
    litigationCaseHearingAddForm: 'litigation-case-hearing-add-form',
    APDocumentAddFolderForm: 'ap-document-add-folder-form',
    APDocumentEditFolderForm: 'ap-document-edit-folder-form',
    APDocumentAddFileForm: 'ap-document-add-file-form',
    APDocumentEditFileForm: 'ap-document-edit-file-form',
    hearingAddForm: 'hearing-add-form',
    hearingEditForm: 'hearing-edit-form',
    hearingJudgementForm: 'hearing-judgement-form',
    timerAddform: 'timer-add-form',
    timerEditform: 'timer-edit-form',
    activeTimersform: 'active-timers-form'
};

export const FORMS_MODAL_TITLES = {
    advisorTimeLogAddForm: 'log_time',
    advisorTimeLogEditForm: 'edit_time_log',
    advisorTaskAddForm: 'add_task',
    advisorTaskEditForm: 'edit_task',
    advisorTaskNoteAddForm: 'add_note',
    advisorTaskNoteEditForm: 'edit_note',
    litigationCaseEditForm: 'edit_litigation_case',
    litigationCaseCustomFieldsEditForm: 'edit_litigation_case_custom_fields',
    legalCaseNoteAddForm: 'add_note',
    legalCaseNoteEditForm: 'edit_note',
    litigationCaseStageEditForm: 'edit_stage',
    litigationCaseStageChangeForm: 'change_stage',
    corporateMatterEditForm: 'edit_corporate_matter',
    litigationCaseHearingAddForm: 'add_hearing',
    APDocumentAddFolderForm: 'create_folder',
    APDocumentEditFolderForm: 'edit_folder',
    APDocumentAddFileForm: 'upload_file',
    APDocumentEditFileForm: 'edit_file',
    hearingAddForm: 'add_hearing',
    hearingEditForm: 'edit_hearing',
    hearingJudgementForm: 'set_judgment_hearing',
    importLogsData: 'import_logs_data',
    advisorTimers: 'advisor_timers',
    addTimer: 'add_timer',
    pauseTime: 'pause_timer',
    resumeTime: 'resume_timer',
    endTimer: 'end_timer'
}

export const MAIN_MENU_TABS_NAMES = {
    profilePage: 'profile-page',
    litigationCases: 'litigation-cases',
    corporateMatters: 'corporate-matters',
    hearings: 'hearings',
    advisorTasks: 'advisor-tasks',
    advisorTimeLogs: 'advisor-time-logs',
    litigationCaseEditForm: 'litigation-case-edit-form',
    corporateMatterEditForm: 'corporate-matter-edit-form',
};

export const PAGES_TITLES = {
    homePage: "dashboard",
    litigationCasesPage: "litigation_cases",
    corporateMattersPage: "corporate_matters",
    hearingsPage: "hearings",
    advisorTasksPage: 'tasks',
    timeLogsPage: 'my_time_logs',
    advisorUserPreferences: 'user_preferences',
    startTimer: 'start_timer',
};

export const PAGES_IDS = {
    homePage: "home-page",
    profilePage: "profile-page",
    litigationCasesPage: "litigation-cases-page",
    litigationCasePage: "litigation-case-page",
    corporateMattersPage: "corporate-matters-page",
    corporateMatterPage: "corporate-matter-page",
    hearingsPage: "hearings-page",
    advisorTasksPage: 'advisor-tasks-page',
    advisorTaskPage: 'advisor-task-page',
    timeLogsPage: 'time-logs-page',
    timeLogPage: 'time-log-page',
    advisorUserPreferences: 'advisor-user-preferences',
};

export const LEGAL_CASES_CATEGORIES = {
    litigationCases: 'Litigation',
    corporateMatters: 'Matter',
};

export const PRIORITY_OPTIONS = [
    {
        title: 'low',
        value: "low"
    },
    {
        title: 'medium',
        value: "medium"
    },
    {
        title: 'high',
        value: "high"
    },
    {
        title: 'critical',
        value: "critical"
    }
];

export const PUBLIC_URL = `${process.env.PUBLIC_URL}`;

export const SESSION_KEYS = {
    user: 'A4L-AP-user',
    refreshToken: 'A4L-AP-refreshToken',
    accessToken: 'A4L-AP-accessToken'
};

export const BROADCAST_CHANNEL = {
    channelName: 'A4L-AP-BC',
    requestUserAuthenticationData: 'requestUserAuthenticationData',
    responseUserAuthenticationData: 'responseUserAuthenticationData',
    logoutUser: 'logoutUser'
};

export const PREFERENCES_KEYS = {
    dashboardWidgets: 'dashboard_widgets',
    activityLogTimer: 'activityLogTimer',
};

export const DEFAULT_PAGE_SIZE = 10;

export const DEFAULT_AUTOCOMPLETE_PAGE_SIZE = 10;

export const ALLOWED_UPLOAD_EXT = ['doc','docx','xls','xlsx','pps','ppt','pptx','pdf','tif','tiff','jpg','png','gif','jpeg','bmp','html','htm','txt','msg','eml','vcf','zip','rar','mpg','mp3','mp4','flv','mov','wav','3gp','avi','pages','dwg','dwf','rtf','ogg','wmv','aiff','aif','m4a','m4v','au','xlt','xltx','docm','xlsm','xltm','pptm','slk','sylk','jfif'];

export const DATA_GRIDS = {
    timeLogs: 'time-logs',
    hearings: 'hearings',
    tasks: 'tasks'
};
