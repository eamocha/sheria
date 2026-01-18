import Api from 'Api'
import Lookup from  'Lookup'
import Loader from 'Loader'
import { trimHtmlTags } from "Utils";

const { useConfirm } = primevue.useconfirm;
const { usePrimeVue  } = primevue.config ;

export default {
    name: 'Form',
    props: ['date', 'to-date', 'repeat', 'time-entry-edit', 'time-entry-copy', 'edit-clicked', 'copy-clicked'],
    components: {
        'loader': Loader,
        'lookup': Lookup,
        'p-button': primevue.button,
        'p-inputtext': primevue.inputtext,
        'p-dropdown': primevue.dropdown,
        'p-checkbox': primevue.checkbox,
        'p-accordion': primevue.accordion,
        'p-accordiontab': primevue.accordiontab,
        'p-radiobutton': primevue.radiobutton,
        'p-overlaypanel': primevue.overlaypanel,
        'p-textarea': primevue.textarea,
        'p-calendar': primevue.calendar,
        "p-dialog": primevue.dialog,
    },
    setup(props, context) {
        const { ref, watch} = Vue;
        const useprimevue = usePrimeVue();
        const confirm = useConfirm();
        const showLoader = ref(true)
        const nonBillable = ref(false);
        const repeat = ref(false);
        const edit = ref(false);
        const userAutocomplete = ref(null);
        const matterAutocomplete = ref(null);
        const taskAutocomplete = ref(null);
        const clientAutocomplete = ref(null);
        const timeEntry = ref({
            user_id: parseFloat(userIDGlobal),
            legal_case_id: null,
            task_id: null,
            logDate: props.date,
            effectiveEffort: "",
            timeStatus: 'billable',
            istaskormatter: 'matter',
            client_id: "",
            rate_system: "",
            rate: "",
            time_type_id: "",
            time_internal_status_id: "",
            comments: "",
            is_repeat: 0,
            repeat_until: props.toDate,
        });
        const notValidated = ref({
            user_id: false,
            task_legal_case_id: false,
            effectiveEffort: false,
            client_id: false,
            comments: false,
        });
        const error = ref({
            form: false,
            date: false,
        });
        const selectedLookup = ref({
            user: userNameGlobal,
            matter: '',
            task: '',
        })
        const disabled = ref({
            user: false,
            matter: false,
            task: false,
            effort: false,
            user_rate: false,
            rate: false,
        })
        const categories = ref([
            {name: _lang.case, key: '1', disable: false}, 
            {name: _lang.task, key: '2', disable: false}, 
        ]);
        const client = ref({id: '', name: ''});
        const userRate = ref([
            {name: _lang.none, id: null},
            {name: _lang.systemRate, id: 'system_rate'},
            {name: _lang.fixedRate, id: 'fixed_rate'},
        ]);
        const timeTypes = ref();
        const organizations = ref();
        const selectedOrganization = ref();
        const timeInternalStatuses = ref();
        const selectedCategory = ref(categories.value[0]);
        const showRateFields = ref(1);
        const showRateField = ref(0);
        const showClientField = ref(1);
        const showCancelButton = ref(0);
        const disabledRate = ref(false);
        const disabledClient = ref(true);
        const userActivityLogId = ref();
        const taskRelatedLegalCaseId = ref(null);
        const unauthorized = ref(0);
        const op = ref();
        const displayTimeEntryRuleConfirmation = ref(false);
        const rule = ref();
        const rtl = ref(_lang.languageSettings['langDirection'] === 'rtl' ? 1 : 0);
        const spFr = ref((languageGlobal === 'spanish' || languageGlobal === 'french') ? 1 : 0);
        watch(() => timeEntry.value.rate_system, (newValue, oldValue) => {
            if(newValue === 'system_rate'){
                disabledRate.value = true;
                showRateField.value = 1;
                getSystemRate();
            } else if (newValue === 'fixed_rate') {
                disabledRate.value = false;
                showRateField.value = 1;
            } else {
                disabledRate.value = false;
                showRateField.value = 0;
            }
        });
        watch(() => timeEntry.value.time_type_id, (newValue, oldValue) => {
            if (newValue && timeTypes.value) {
                let selectedTimeType = timeTypes.value.filter(timeType => timeType.id === newValue);
                if (selectedTimeType[0].default_comment) timeEntry.value.comments = selectedTimeType[0].default_comment;
                if (selectedTimeType[0].default_time_effort) timeEntry.value.effectiveEffort = selectedTimeType[0].default_time_effort;
            }
        });
        const changeDateOnClick = (logDate, toDate, isRepeat) => {
            timeEntry.value.logDate = logDate;
            timeEntry.value.repeat_until = toDate;
            timeEntry.value.is_repeat = isRepeat;
            repeat.value = isRepeat ? true : false;
        }
        watch(() => timeEntry.value.logDate, (newValue, oldValue) => {
            if (timeEntry.value.is_repeat){
               validateDate();   
            }
        });
        watch(() => timeEntry.value.repeat_until, (newValue, oldValue) => {
            if (timeEntry.value.is_repeat){
                validateDate(); 
            }
        });
        const handleSelectedClient = (item) => {
            timeEntry.value.client_id = item.id;
            client.value.name = item.name;
            client.value.id = item.id;
        }
        const handleSelectedMatter = (item, lastLoggedMatter = false) => {
            timeEntry.value.legal_case_id = item.id;
            if (item.client_id !== null) {
                client.value.name = lastLoggedMatter ? item.client : item.client.name;
                client.value.id = item.client_id;
                timeEntry.value.client_id = item.client_id;
                disabledClient.value = true;
            } else {
                disabledClient.value = false;
                client.value = {id: '', name: ''};
                timeEntry.value.client_id = "";
            }
            if (timeEntry.value.rate_system === 'system_rate') getSystemRate();
        }
        const handleSelectedTask = (item) => {
            if (item.legal_case_id === null) {
                showClientField.value = 0;
                showRateFields.value = 0;
                client.value = {id: '', name: ''};
            } else {
                taskRelatedLegalCaseId.value = item.legal_case_id;
                getMatterRelatedToTask(item.legal_case_id);
            }
            timeEntry.value.task_id = item.id;
        }
        const handleSelectedUser = (item) => {
            timeEntry.value.user_id = item.id;
            selectedLookup.value.user = userAutocomplete.value.validateSelected();
            if (timeEntry.value.rate_system === 'system_rate') getSystemRate();
            selectedLookup.value.user = item.name;
        }
        const handleCheckboxChange = (value) => {
            timeEntry.value.timeStatus = value ? 'internal' : 'billable';
        }
        const handleRepeatCheckboxChange = (value) => {
            if (value) {
                timeEntry.value.is_repeat = 1;
            } else {
                timeEntry.value.repeat_until = '';
                timeEntry.value.is_repeat = 0;
                error.value.date = false;
            }
        }
        const handleRadioButtonChange = (value) => {
            showRateFields.value = selectedCategory.value.key === '2' ? 0 : 1;
            showClientField.value = 1;
            if(selectedCategory.value.key === '2') {
                timeEntry.value.istaskormatter = 'task';
                timeEntry.value.legal_case_id = null; 
                selectedLookup.value.matter = '';
                getmyLastLoggedTask();
            } else{
                timeEntry.value.istaskormatter = 'matter';
                timeEntry.value.task_id = null;
                selectedLookup.value.task = '';
                getmyLastLoggedMatter();
            } 
        }
        const handleEntityChange = (event) => {
            getSystemRate()        }
        const changeEntity = (event) => {
            op.value.toggle(event);
        }
        const addNewModal = (toBeAdded) => {
            quickAdministrationDialog(toBeAdded, jQuery('#add-edit-time-log'),true);
        }
        watch(() => props.timeEntryEdit, (newValue, oldValue) => {
            notValidated.value = {
                user_id: 0,
                task_legal_case_id: 0,
                effectiveEffort: 0,
                client_id: 0,
                comments: 0,
            };
            error.value.date = false;
            error.value.form = false;
            if(newValue !== '') {
                userActivityLogId.value = newValue.id;
                addValuesToFields(newValue)
                if(newValue.billing_status){
                    disabled.value.user = true;
                    disabled.value.matter = true;
                    disabled.value.task = true;
                    disabled.value.effort = true;
                    disabled.value.user_rate = true;
                    disabled.value.rate = true;
                    if(newValue.legal_case_id){
                        categories.value[1].disable = true;
                    } else {
                        categories.value[0].disable = true;
                    }
                }
            }
            showCancelButton.value = newValue === '' ? 0 : 1;
            edit.value = newValue === '' ? false : true;
        });
        watch(() => props.editClicked, (newValue, oldValue) => {
            edit.value = true;
        });
        watch(() => props.timeEntryCopy, (newValue, oldValue) => {
            if(newValue !== '') addValuesToFields(newValue)
            showCancelButton.value = newValue === '' ? 0 : 1;
            userActivityLogId.value = '';
            error.value.date = false;
            error.value.form = false;
            notValidated.value = {
                user_id: 0,
                task_legal_case_id: 0,
                effectiveEffort: 0,
                client_id: 0,
                comments: 0,
            };
        });
        watch(() => props.copyClicked, (newValue, oldValue) => {
            edit.value = false;
        });
        const addValuesToFields = (newValue) => {
            timeEntry.value.logDate = newValue.log_date
            timeEntry.value.istaskormatter = newValue.task_id === null ? 'matter' : 'task';
            selectedCategory.value = newValue.task_id === null ? categories.value[0] : categories.value[1];
            timeEntry.value.user_id = parseFloat(newValue.user.id);
            selectedLookup.value.user = newValue.user.name;
            selectedLookup.value.matter = newValue.task_id === null ? 'M00000' + newValue.legal_case_id + ': ' + newValue.name : '';
            selectedLookup.value.task = newValue.task_id === null ? '' : newValue.title + ': ' + newValue.name;
            timeEntry.value.effectiveEffort = newValue.effective_effort;
            timeEntry.value.timeStatus = newValue.time_status;
            nonBillable.value = newValue.time_status === 'internal' ? true : false;
            timeEntry.value.rate_system = newValue.rate_system;
            if(newValue.rate_system === 'fixed_rate') timeEntry.value.rate = newValue.rate;
            timeEntry.value.time_type_id = newValue.time_type_id ? parseInt(newValue.time_type_id) : '',
            timeEntry.value.time_internal_status_id = newValue.time_internal_status !== null ? newValue.time_internal_status.id : '',
            timeEntry.value.comments = newValue.comments;
            timeEntry.value.legal_case_id = newValue.legal_case_id;
            timeEntry.value.task_id = newValue.task_id;
            timeEntry.value.is_repeat = 0;
            timeEntry.value.repeat_until = '';
            repeat.value = false;
            if (newValue.task_id !== null) {
                timeEntry.value.client_id = newValue.client_id;
                handleSelectedTask({id: newValue.task_id, legal_case_id: newValue.task.legal_case_id})
            } else {
                timeEntry.value.client_id = newValue.client_id ? newValue.client_id : (newValue.legal_case.client_id ? newValue.legal_case.client_id : '');
                client.value.name = newValue.client ? newValue.client.name : (newValue.legal_case.client ? newValue.legal_case.client : '');
                client.value.id = newValue.client ? newValue.client_id : (newValue.legal_case.client_id ? newValue.legal_case.client_id : '');
            }
        }
        const cancel = ($edit = false) => {
            if ($edit) cancelEdit();
            getmyLastLoggedMatter();
            showCancelButton.value = 0;
            userActivityLogId.value = '';
            timeEntry.value.user_id = parseFloat(userIDGlobal);
            timeEntry.value.legal_case_id = null;
            timeEntry.value.task_id = null;
            timeEntry.value.effectiveEffort = "";
            timeEntry.value.timeStatus = 'billable';
            timeEntry.value.is_repeat = 0;
            timeEntry.value.istaskormatter = 'matter';
            timeEntry.value.client_id = "";
            timeEntry.value.rate_system = "";
            timeEntry.value.rate = "";
            timeEntry.value.time_type_id = "";
            timeEntry.value.time_internal_status_id = "";
            timeEntry.value.comments = "";
            timeEntry.value.logDate = new Date().toISOString().slice(0, 10);
            client.value.name = "";
            client.value.id = "";
            selectedLookup.value.user = userNameGlobal;
            selectedLookup.value.matter = '';
            selectedLookup.value.task = '';
            selectedCategory.value = categories.value[0];
            taskRelatedLegalCaseId.value = null;
            nonBillable.value = false;
            repeat.value = false;
            disabled.value.user = false;
            disabled.value.matter = false;
            disabled.value.task = false;
            disabled.value.effort = false;
            disabled.value.user_rate = false;
            disabled.value.rate = false;
            categories.value[1].disable = false;   
            categories.value[0].disable = false;
            showRateFields.value = 1;
            showClientField.value = 1;
        }
        const cancelEdit = () => {
            context.emit('handlecanceledit', '');
            edit.value = false;
        }
        const loader = (action) => showLoader.value = action
        const getmyLastLoggedMatter = () => {
            loader(true)
            axios.get(Api.getApiBaseUrl('core') + '/my-last-logged-matter', Api.getInitialHeaders())
                .then(response => {
                    loader(false)
                    defineMyLastLoggedMatter(response.data.legal_case)
                }).catch((error) => {
                    loader(false)
                });
        }
        const defineMyLastLoggedMatter = (legalCase) => {
            if(legalCase.length > 0){
                selectedLookup.value.matter = legalCase[0] ? 'M00000' + legalCase[0].id + ': ' + legalCase[0].name : 0;
                timeEntry.value.legal_case_id = legalCase[0].id;
                timeEntry.value.task_id = null;
                handleSelectedMatter(legalCase[0], true);
            } else {
                client.value.name = "";
                client.value.id = "";
            }
        }
        const getmyLastLoggedTask = () => {
            loader(true)
            axios.get(Api.getApiBaseUrl('core') + '/my-last-logged-task', Api.getInitialHeaders())
                .then(response => {
                    loader(false)
                    if(response.data.task){
                        selectedLookup.value.task = response.data.task ? 'T00000' + response.data.task[0].id + ': ' + response.data.task[0].name : 0;
                        timeEntry.value.task_id = response.data.task[0].id;
                        timeEntry.value.legal_case_id = null;
                        handleSelectedTask(response.data.task[0]);
                    } else {
                        client.value.name = "";
                        client.value.id = "";
                    }
                }).catch((error) => {
                    loader(false)
                });
        }
        const defineTimeTypes = (alltimeTypes) => {
            if(alltimeTypes.length > 0){
                timeTypes.value = alltimeTypes;
            } else {
                timeTypes.value = [];
            }
        }
        const defineInternalStatuses = (alltimeInternalStatuses, defaulttimeInternalStatus) => {
            if(alltimeInternalStatuses.length > 0){
                timeEntry.value.time_internal_status_id = defaulttimeInternalStatus ? parseFloat(defaulttimeInternalStatus) : "";
                timeInternalStatuses.value = alltimeInternalStatuses
            } else {
                timeInternalStatuses.value = [];
            }
        }
        const defineOrganizations = (allOrganizations) => {
            selectedOrganization.value = organizationIDGlobal ? parseFloat(organizationIDGlobal) : 1;
            if(allOrganizations.length > 0){
                organizations.value = allOrganizations.map(organization => ({id: parseFloat(organization.id), name: organization.name + ' (' + organization.currency + ')'}))
            } else {
                organizations.value = [];
            }
        }
        const defineStatus = (status) => {
            if(status){
                nonBillable.value = parseFloat(status) ? true : false;
                handleCheckboxChange(nonBillable.value)
            }
        }
        const getMyTimeEntryFormData = () => {
            loader(true)
            axios.get(Api.getApiBaseUrl('core') + '/my-time-log-form-data', Api.getInitialHeaders())
                .then(response => {
                    loader(false)
                    if(response.data.time_entry_data){
                        defineMyLastLoggedMatter(response.data.time_entry_data.legal_case)
                        defineTimeTypes(response.data.time_entry_data.time_types);
                        defineInternalStatuses(response.data.time_entry_data.time_internal_statuses, response.data.time_entry_data.default_time_internal_status)
                        defineOrganizations(response.data.time_entry_data.organizations)
                        defineStatus(response.data.time_entry_data.default_time_log_status)
                    } else {
                        organizations.value = [];
                        timeInternalStatuses.value = [];
                        timeTypes.value = [];
                        client.value.name = "";
                        client.value.id = "";
                    }
                }).catch((error) => {
                    loader(false)
                    if (error.response.data.message === 'unauthorizedUser') unauthorized.value = 1;
                });
        }
        const getSystemRate = () => {
            loader(true)
            var organizationIdParam = 'organization_id=' + selectedOrganization.value;
            var caseIdParam = timeEntry.value.legal_case_id ? '&case_id=' + timeEntry.value.legal_case_id : '&case_id=' + taskRelatedLegalCaseId.value;
            var userIdParam = '&user_id=' + timeEntry.value.user_id;
            axios.get(Api.getApiBaseUrl('core') + '/system-rate?' + organizationIdParam + caseIdParam + userIdParam, Api.getInitialHeaders())
                .then(response => {
                    loader(false)
                    if(response.data.system_rate){
                        timeEntry.value.rate = response.data.system_rate.rate;
                    }
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error?.response?.data.message ? error.response.data.message + ' ' + _lang.feedback_messages.tryAgain : _lang.feedback_messages.error });
                    if (error?.response?.status == 401) {
                        localStorage.removeItem('api-access-token');
                        setTimeout(() => window.location = getBaseURL() + 'time_tracking/my_time_logs/', 700);
                    }
                    loader(false)
                });
        }
        const checkDateFormat = () => {
            let dateRegex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
            let validLogDateFormat = dateRegex.test(timeEntry.value.logDate);
            if (!validLogDateFormat) {
                timeEntry.value.logDate = new Date(timeEntry.value.logDate)
                timeEntry.value.logDate.setDate(timeEntry.value.logDate.getDate() + 1)
                timeEntry.value.logDate = timeEntry.value.logDate.toISOString().split('T')[0]
            }
            if (timeEntry.value.is_repeat){
                let validRepeatUntilFormat = dateRegex.test(timeEntry.value.repeat_until);
                if (!validRepeatUntilFormat) {
                    timeEntry.value.repeat_until = new Date(timeEntry.value.repeat_until)
                    timeEntry.value.repeat_until.setDate(timeEntry.value.repeat_until.getDate() + 1)
                    timeEntry.value.repeat_until = timeEntry.value.repeat_until.toISOString().split('T')[0]
                }
            }
        }
        const validateDate = () => {
            let dateRegex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
            let validLogDateFormat = dateRegex.test(timeEntry.value.logDate);
            let validRepeatUntilFormat = dateRegex.test(timeEntry.value.repeat_until);
            let fromDate = validLogDateFormat ? new Date(timeEntry.value.logDate) : timeEntry.value.logDate;
            let toDate = validRepeatUntilFormat ? new Date(timeEntry.value.repeat_until) : timeEntry.value.repeat_until;
            error.value.date = fromDate < toDate ? false : true;
        }
        const validateForm = () => {
            var obj = timeEntry.value;
            let regEx1 = /^([0-9]+h\s?)?((([0-5][0-9])|([1-9]))m\s?)?$/;
            let regEx2 = /^([0-9]+\s?):((([0-5][0-9]))\s?)?$/;
            let regEx3 = /^[0-9]+\s?$/;
            selectedLookup.value.user = userAutocomplete.value.validateSelected();
            selectedLookup.value.task = selectedCategory.value.key == 2 ? taskAutocomplete.value.validateSelected() : "";
            selectedLookup.value.matter = selectedCategory.value.key == 1 ? matterAutocomplete.value.validateSelected() : "";
            client.value.name = (!nonBillable.value && showClientField.value === 1 ) ? clientAutocomplete.value.validateSelected() : '';
            let result = obj.effectiveEffort !== "" ? (regEx1.test(obj.effectiveEffort) || regEx2.test(obj.effectiveEffort) || regEx3.test(obj.effectiveEffort)): false;
            notValidated.value.user_id = (obj.user_id === "" || selectedLookup.value.user === "") ? 1 : 0;
            notValidated.value.task_legal_case_id = ((obj.legal_case_id === null || selectedLookup.value.matter === "") && (obj.task_id === null || selectedLookup.value.task === "")) ? 1 : 0;
            notValidated.value.logDate = obj.logDate === "" ? 1 : 0; 
            notValidated.value.effectiveEffort = result ? 0 : 1;
            notValidated.value.client_id = (!nonBillable.value && showClientField.value === 1 && (obj.client_id === "" || client.value.name === "")) ? 1 : 0;
            if (obj.comments) notValidated.value.comments = (obj.comments.length > 2 || obj.comments.length === 0) ? 0 : 1;
            error.value.form = !Object.keys(notValidated.value).every((k) => !notValidated.value[k]);
        }
        const loggingRuleConfirmation = (button) => {
            if (button === 'add') {
                displayTimeEntryRuleConfirmation.value = false;
                timeEntry.value.logDate = rule.value.last_date;
                timeEntry.value.repeat_until = rule.value.log_date;
                timeEntry.value.is_repeat = 1;
                repeat.value = true;
                userActivityLogId.value = '';
                edit.value = false; 
                showCancelButton.value = 1;
            } else if (button === 'settings') {
                window.open("system_preferences#SystemValues", '_blank').focus();
            } else {
                displayTimeEntryRuleConfirmation.value = false;
                cancel();
            }
        }
        const saveTimeLog = () => {
            if(!unauthorized.value){
                validateForm();
                if (!error.value.form) {
                    checkDateFormat();
                    if (timeEntry.value.is_repeat) {
                        validateDate();
                    }
                    if (!timeEntry.value.is_repeat || !error.value.date){
                        if (timeEntry.value.timeStatus === 'internal') {
                            delete timeEntry.value.client_id
                            timeEntry.value.rate_system = '';
                            timeEntry.value.rate = '';
                        }
                        if (!timeEntry.value.rate_system) {
                            timeEntry.value.rate_system = '';
                            timeEntry.value.rate = '';
                        }
                        loader(true)
                        if (userActivityLogId.value > 0) {
                            axios.put(Api.getApiBaseUrl('core') + '/time-log/' + userActivityLogId.value, timeEntry.value, Api.getInitialHeaders()).then((response) => {
                                loader(false)
                                if (response.data.time_log) {
                                    pinesMessageV2({ ty: 'success', m: _lang.timeEntryEditedSuccessfully });
                                    if(response.data['warning message']=== 'capAmountWarning') pinesMessageV2({ ty: 'warning', m: _lang.capAmountWarning });
                                    response.data.time_log.name = replaceHtmlCharacter(response.data.time_log.name);
                                    context.emit('handleeditchange', response.data.time_log)
                                    cancel(true);
                                } else if (response.data.dates) {
                                    rule.value = response.data.dates;
                                    var date1 = new Date(response.data.dates.last_date);
                                    var date2 = new Date(response.data.dates.log_date);
                                    var diffTime = Math.abs(date2 - date1);
                                    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                                    rule.value.days = diffDays;
                                    rule.value.allowGoToSettings = response.data.allow_go_to_settings;
                                    displayTimeEntryRuleConfirmation.value = true;
                                } else {
                                    pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
                                }
                            }).catch((error) => {
                                pinesMessageV2({ ty: 'error', m: error?.response?.data.message ? (error.response.data.message in _lang ? _lang[error.response.data.message] : error.response.data.message + ' ' + _lang.feedback_messages.tryAgain) : _lang.feedback_messages.error });
                                if (error?.response?.status == 401) {
                                    localStorage.removeItem('api-access-token');
                                    setTimeout(() => window.location = getBaseURL() + 'time_tracking/my_time_logs/', 700);
                                }
                                loader(false)
                            });
                        } else {
                            axios.post(Api.getApiBaseUrl('core') + '/time-log', Api.toFormData(timeEntry.value), Api.getInitialHeaders("multipart/form-data")).then((response) => {
                                loader(false)
                                if (response.data.time_logs) {
                                    pinesMessageV2({ ty: 'success', m: _lang.timeEntrySavedSuccessfully });
                                    if(response.data['warning message']=== 'capAmountWarning') pinesMessageV2({ ty: 'warning', m: _lang.capAmountWarning });
                                    cancel();
                                    response.data.time_logs.name = replaceHtmlCharacter(response.data.time_logs.name);
                                    context.emit('handlechange', response.data.time_logs)
                                    showCancelButton.value = 0;
                                } else if (response.data.dates) {
                                    rule.value = response.data.dates;
                                    var date1 = new Date(response.data.dates.last_date);
                                    var date2 = new Date(response.data.dates.log_date);
                                    var diffTime = Math.abs(date2 - date1);
                                    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                                    rule.value.days = diffDays;
                                    rule.value.allowGoToSettings = response.data.allow_go_to_settings;
                                    displayTimeEntryRuleConfirmation.value = true;
                                } else {
                                    pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
                                }
                            }).catch((error) => {
                                pinesMessageV2({ ty: 'error', m: error?.response?.data.message ? (error.response.data.message in _lang ? _lang[error.response.data.message] : error.response.data.message + ' ' + _lang.feedback_messages.tryAgain) : _lang.feedback_messages.error });
                                if (error?.response?.status == 401){
                                    localStorage.removeItem('api-access-token');
                                    setTimeout(() => window.location = getBaseURL() + 'time_tracking/my_time_logs/', 700);
                                }
                                loader(false)
                            });
                        }
                    } 
                } else {
                    pinesMessageV2({ ty: 'warning', m: _lang.feedback_messages.fillRequiredFields });
                }
            } else {
                pinesMessageV2({ ty: 'error', m: _lang.unauthorizedUser});
            }
        }
        const getMatterRelatedToTask = (caseId) => {
            loader(true)
            axios.get(Api.getApiBaseUrl('core') + '/case/' + caseId, Api.getInitialHeaders())
            .then(response => {
                loader(false)
                if(response.data.case){
                    if(response.data.case.client_id){
                        showClientField.value = 1;
                        showRateFields.value = 1;
                        timeEntry.value.client_id = response.data.case.client_id;
                        client.value.name = response.data.case.client.name;
                        client.value.id = response.data.case.client_id;
                        getSystemRate();
                    } else {
                        showClientField.value = 0;
                        timeEntry.value.client_id = "";
                        client.value = {id: '', name: ''};
                        showRateFields.value = 0;
                    }
                } else {
                    showClientField.value = 0;
                    showRateFields.value = 0;
                }
            }).catch((error) => {
                loader(false)
            });
        }
        return {
            categories,
            timeTypes,
            timeInternalStatuses,
            selectedCategory,
            client,
            nonBillable,
            repeat,
            timeEntry,
            selectedLookup, 
            userRate,
            showRateFields,
            showRateField,
            disabledRate,
            op,
            organizations,
            selectedOrganization,
            notValidated,
            error,
            disabledClient,
            showClientField,
            userActivityLogId,
            showCancelButton,
            disabled,
            rtl,
            spFr,
            taskRelatedLegalCaseId,
            edit,
            userAutocomplete,
            matterAutocomplete,
            taskAutocomplete,
            clientAutocomplete,
            showLoader,
            unauthorized,
            displayTimeEntryRuleConfirmation,
            rule,
            handleSelectedTask,
            handleSelectedClient,
            defineTimeTypes,
            defineInternalStatuses,
            handleSelectedUser,
            handleSelectedMatter,
            handleCheckboxChange,
            handleRadioButtonChange,
            saveTimeLog,
            getSystemRate,
            changeEntity,
            defineOrganizations,
            handleEntityChange,
            validateForm,
            getMatterRelatedToTask,
            addNewModal,
            cancelEdit,
            cancel,
            getmyLastLoggedMatter,
            getmyLastLoggedTask,
            handleRepeatCheckboxChange,
            getMyTimeEntryFormData,
            defineMyLastLoggedMatter,
            loggingRuleConfirmation,
            changeDateOnClick
        }
    },
    template: `
        <div  id="timelogs-add">
            <loader :show="showLoader"></loader>
            <p-dialog header="` + _lang.timeEntryRuleMessageTitle + `" v-model:visible="displayTimeEntryRuleConfirmation" :style="{width: '550px'}" :modal="true">
                <div class="row mx-0 time-entry-dialog"> 
                    <i class="pi pi-exclamation-triangle col-md-1" style="font-size: 2rem"></i>
                    <p class="col-md-11">{{_lang.periodWithNoTimeEntries.sprintf([rule.days, selectedLookup.user])}}</p>
                    <p class="offset-md-1 col-md-11">` + _lang.timeEntryLoggingRuleMessage + `</p>
                </div>
                <template #footer>
                    <p-button v-if="rule.allowGoToSettings" label="` + _lang.goToSettings + `" icon="pi pi-cog" @click="loggingRuleConfirmation('settings')" class="p-button-text"></p-button>
                    <p-button v-if="!rule.allowGoToSettings" label="` + _lang.cancel + `" icon="pi pi-times" @click="loggingRuleConfirmation('cancel')" class="p-button-text"></p-button>
                    <p-button label="` + _lang.addBulkTimeEntires + `" @click="loggingRuleConfirmation('add')" autofocus></p-button>
                </template>
            </p-dialog>
            <h4 v-if="edit" class="box-title">
                <i class="sprite sprite-log-time" style="margin-top:4px;"></i>
                `+ _lang.timeEntry.sprintf([_lang.edit]) +`
            </h4>
            <h4 v-if="!edit && !repeat" class="box-title">
                <i class="sprite sprite-log-time" style="margin-top:4px;"></i>
                `+ _lang.timeEntry.sprintf([_lang.add]) +`
            </h4>
            <h4 v-if="!edit && repeat" class="box-title">
                <i class="sprite sprite-log-time" style="margin-top:4px;"></i>
                `+ _lang.timeEntries.sprintf([_lang.add]) +`
            </h4>
            <div class="container box-body">
                <div class="row mt-2">
                    <div v-for="category of categories" :key="category.key" class="offset-lg-1 col-lg-2 offset-md-1 col-md-4 col-xs-2 p-field-radiobutton">
                        <p-radiobutton :id="category.key" name="category" :value="category" v-model="selectedCategory" @change="handleRadioButtonChange" :disabled="category.disable" :class="{'not-allowed' : category.disable}"/>
                        <label class="timelog-radiobutton-label" :for="category.key">{{category.name}}</label> 
                    </div>
                </div>
                <div class="row">
                    <div v-if="!repeat" class="col-lg-1 col-md-2 col-xs-1">
                        <label class="mt-7">`+ _lang.date +`</label>
                    </div>
                    <div v-if="repeat" class="col-lg-1 col-md-2 col-xs-1">
                        <label class="mt-7">`+ _lang.from +`</label>
                    </div>
                    <div :class="(repeat) ? 'col-lg-7 col-md-10 col-xs-7' : 'col-lg-3 col-md-5 col-xs-3'">
                        <div class="row">
                            <div :class="(repeat) ? 'col-lg-5 col-md-5 col-xs-5' : 'col-lg-12'">
                                <p-calendar v-model="timeEntry.logDate" :class="{'p-invalid': error.date}" :show-icon="true" date-format="yy-mm-dd"></p-calendar>
                            </div>
                            <div v-if="repeat" class="col-lg-1 col-md-1 col-xs-1">
                                <label class="mt-7">`+ _lang.to +`</label>
                            </div>
                            <div v-if="repeat" class="col-lg-5 col-md-5 col-xs-5">
                                <p-calendar v-model="timeEntry.repeat_until" :class="{'p-invalid': error.date}" :show-icon="true" date-format="yy-mm-dd"></p-calendar>
                            </div>
                            <small v-if="error.date" class="p-error col-lg-10 col-xs-7 col-md-12" style="line-height:12px;">`+ _lang.feedback_messages.meetingDatesRule +`</small>
                        </div>
                    </div>
                    <div v-if="!edit" class="col-lg-4 col-md-5 col-xs-4">
                        <div class="row no-gutters">
                            <p-checkbox id="binary-repeat" class="align-self-center col-lg-1 col-md-2 col-xs-1 mt-3" v-model="repeat" :binary="true" @input="handleRepeatCheckboxChange"/>
                            <div class="col-lg-9 col-md-10 col-xs-9 mt-3">
                                <label for="binary-repeat" class="mrl-15">`+ _lang.repeat +`</label>
                                <i class="pi pi-question-circle mt-2 mr-15-ar" v-tooltip.top="'`+ _lang.multipleTimeEntriesInfo +`'"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-15">
                    <label for="timelog-username" class="required-label align-self-center col-lg-1 col-md-2 col-xs-1">`+ _lang.user +`</label>
                    <div class="col-lg-11 col-md-10 col-xs-11">
                        <lookup api-name="users" ref="userAutocomplete" @handlechange="handleSelectedUser" place-holder="`+ _lang.userName +`" :selected="selectedLookup.user" :error="notValidated.user_id" error-message="`+ _lang.user +`" :disable="disabled.user"></lookup>
                    </div>
                </div>
                <div v-if="selectedCategory.key === '1'" class="row mt-15">
                    <label class="required-label align-self-center col-lg-1 col-md-2 col-xs-1">` + _lang.case + `</label>
                    <div class="col-lg-11 col-md-10 col-xs-11">
                        <lookup api-name="cases" ref="matterAutocomplete" @handlechange="handleSelectedMatter" place-holder="`+ _lang.matterLookupPlaceHolder +`" :selected="selectedLookup.matter" :error="notValidated.task_legal_case_id" error-message="` + _lang.case + `" :disable="disabled.matter"></lookup>
                    </div>
                </div>
                <div v-if="selectedCategory.key === '2'" class="row mt-15">
                    <label class="required-label align-self-center col-lg-1 col-md-2 col-xs-1">`+ _lang.task +`</label>
                    <div class="col-lg-11 col-md-10 col-xs-11">
                        <lookup api-name="tasks" ref="taskAutocomplete" @handlechange="handleSelectedTask" place-holder="Start typing" :selected="selectedLookup.task" :error="notValidated.task_legal_case_id" error-message="`+ _lang.task +`" :disable="disabled.task"></lookup>
                    </div>
                </div>
                <div v-if="false" class="row">
                    <div class="mt-20" style="width:92%">
                        <div class="link-style-with-underline pull-right" @click="addNewModal('time_types')"><span class="plus-icon">+ </span>`+_lang.addNewCategory+`</div>
                    </div>
                </div>
                <div class="row">
                    <label for="timelog-category" class="align-self-center col-lg-1 col-md-2 col-xs-1 mt-15">`+ _lang.category +`</label>
                    <div class="col-lg-5 col-md-10 col-xs-5 mt-15">
                        <p-dropdown v-model="timeEntry.time_type_id" id="timelog-category" class="timelog-width-100" :options="timeTypes" option-label="name" option-value="id" placeholder="`+ _lang.selectCategory +`"></p-dropdown>
                    </div>
                    <label for="timelog-internal-status" class="align-self-center col-lg-1 col-md-2 col-xs-1 mt-15">`+ _lang.timeInternalStatus +`</label>
                    <div class="col-lg-5 col-md-10 col-xs-5 mt-15">
                        <p-dropdown v-model="timeEntry.time_internal_status_id" id="timelog-internal-status" class="timelog-width-100" :options="timeInternalStatuses" option-label="name" option-value="id" placeholder="`+ _lang.selectInternalStatus +`"></p-dropdown>
                    </div>
                </div>
                <div class="row mt-10">
                    <label for="timelogeffort" class="required-label align-self-center col-lg-1 col-md-2 col-xs-1">`+ _lang.effort +`</label>
                    <div class="col-lg-4 col-md-10 col-xs-4">
                        <span :class="(rtl) ? 'p-input-icon-left timelog-width-100' : 'p-input-icon-right timelog-width-100'">
                            <i class="pi pi-question-circle" style="z-index:2;" v-tooltip.top="'`+ _lang.supportedTimeUnits +`'"></i>
                            <p-inputtext type="text" aria-describedby="timelogeffort-help" :class="(notValidated.effectiveEffort) ? 'timelog-width-100 p-invalid' : 'timelog-width-100'" id="timelogeffort" v-model="timeEntry.effectiveEffort" placeholder="H:MM" :disabled="disabled.effort" :class="{'not-allowed' : disabled.effort}" autocomplete="off"/>
                            <small v-if="notValidated.effectiveEffort" id="timelogeffort-help" class="p-error" style="line-height:12px">`+ _lang.timeValidateFormat.sprintf([_lang.effort]) +`</small>
                        </span>
                    </div>
                    <div class="col-lg-3 col-md-12 col-xs-2 mt-3">
                        <div class="row justify-content-center no-gutters">
                            <p-checkbox id="binary" class="offset-lg-2 col-lg-2 col-md-1 col-xs-2" v-model="nonBillable" :binary="true" @input="handleCheckboxChange"/>
                            <div class="col-lg-8 col-md-5 col-xs-8">
                                <label for="binary" class="ml-2">`+ _lang.timeTrackingStatus.internal +`</label>
                                <i class="pi pi-question-circle mt-3 ml-2" v-tooltip.top="'`+ _lang.nonBillableCheckboxInfo +`'"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-xs-4">
                        <div class="row">
                            <label v-if="!nonBillable && showClientField" for="timelog-clientname" class="required-label align-self-center col-lg-2 col-md-2 col-xs-2">`+ _lang.client +`</label>
                            <div v-if="!nonBillable && showClientField" class="col-lg-10 col-md-10 col-xs-10">
                                <lookup api-name="clients" ref="clientAutocomplete" @handlechange="handleSelectedClient" place-holder="`+ _lang.case_columns.clientName +`" :selected="client.name" :error="notValidated.client_id" error-message="`+ _lang.client +`" :disable="disabledClient"></lookup>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!nonBillable && showRateFields">
                    <div class="row mt-15">
                        <label for="timelog-user-rate" class="align-self-center col-lg-1 col-md-2 col-xs-1">`+ _lang.userRate +`</label>
                        <div class="col-lg-4 col-md-10 col-xs-4">
                            <p-dropdown v-model="timeEntry.rate_system" id="timelog-user-rate" class="timelog-width-100" :options="userRate" option-label="name" option-value="id"  :disabled="disabled.user_rate" placeholder="`+ _lang.none +`"></p-dropdown>
                        </div>
                        <div v-if="showRateField" class="col-lg-4 col-md-12 col-xs-4">
                            <div class="row">
                                <label for="timelog-rate" class="align-self-center col-lg-2 col-md-2 col-xs-2" >`+ _lang.rate +`</label>
                                <div class="col-lg-10 col-md-10 col-xs-10">
                                    <p-inputtext type="text" class="timelog-width-100" id="timelog-rate" v-model="timeEntry.rate" placeholder="`+ _lang.rate +`" :disabled="disabledRate || disabled.rate" :class="{'not-allowed' : disabledRate}">
                                </div>
                            </div>
                        </div>
                        <div v-if="timeEntry.rate_system === 'system_rate'" class="col-lg-3 col-md-12 col-xs-3">
                            <div class="row no-gutters mt-3">
                                <div id="related-entity" class="link-style-with-underline col-lg-6 col-md-3 offset-md-2 align-self-center col-xs-6" @click="changeEntity">`+_lang.changeEntity+`</div>
                                <i class="pi pi-question-circle mt-3" v-tooltip.top="'`+ _lang.rateAlgorithm +`'"></i>
                            </div>
                            <p-overlaypanel ref="op" append-to="body" :show-close-icon="true" id="overlay_panel" style="width: 450px" :breakpoints="{'960px': '50vw'}">
                                <div class="row">
                                    <label for="timelog-entity" class="align-self-center col-lg-3 col-xs-3">`+ _lang.relatedEntity +`</label>
                                    <div class="col-lg-9 col-xs-9">
                                        <p-dropdown v-model="selectedOrganization" id="timelog-entity" :options="organizations" @change="handleEntityChange" option-label="name" option-value="id" placeholder="Select an Entity" style="width:95%"></p-dropdown>
                                    </div>
                                </div>
                            </p-overlaypanel>
                        </div>
                    </div>
                </div>
                <div class="row mt-10">
                    <label for="timelogs-comment" class="align-self-center col-lg-1 col-md-2 col-xs-1">`+ _lang.description +`</label>
                    <div class="col-lg-11 col-md-10 col-xs-11">
                        <p-textarea v-model="timeEntry.comments" aria-describedby="comments-help" rows="3" cols="40" id="timelogs-comment" :class="(notValidated.comments) ? 'form-control timelog-width-100 p-invalid' : 'form-control timelog-width-100'" dir="auto"></p-textarea>
                        <small id="comments-help" :class="(notValidated.comments) ? 'p-error' : 'd-none'" style="line-height:12px">`+ _lang.commentMinCharacters +`</small>
                    </div>
                </div>
                <div class="row my-3">
                    <div class="offset-1 col-lg-2 col-md-5 col-xs-2">
                        <p-button type="button" label="`+ _lang.save +`" class="width-100 timelog-save-button" v-on:click="saveTimeLog()"/>
                    </div>
                    <div v-if="showCancelButton" class="col-lg-2 col-md-5 col-xs-2">
                        <p-button type="button" label="`+ _lang.cancel +`" class="width-100"  v-on:click="cancel(true)"/>
                    </div>
                </div>
            </div>               
        </div>
    `,
};