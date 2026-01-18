import Api from 'Api'
import Loader from 'Loader'
import { trimHtmlTags } from "Utils";

const { useConfirm } = primevue.useconfirm;
const { usePrimeVue  } = primevue.config ;
export default {
    name: 'DataTable',
    emits: ['handlechange'],
    props: ['loading', 'new-time-entry', 'edited-time-entry'],
    components: {
        'loader': Loader,
        'p-datatable': primevue.datatable,
        'p-column': primevue.column,
        'p-button': primevue.button,
        'p-inputtext': primevue.inputtext,
        'p-confirmdialog': primevue.confirmdialog,
        'p-overlaypanel': primevue.overlaypanel,
        "p-badge": primevue.badge,
    },
    setup(props, context) {
        const useprimevue = usePrimeVue();
        const confirm = useConfirm();
        const { ref, watch } = Vue;
        const dt = ref();
        const op = ref();
        const totalRecords = ref(0);
        const timelogs = ref();
        const timeEntryEdit = ref();
        const timelogsParams = ref({});
        const date = ref();
        const summary = ref();
        const showLoader = ref(true)
        const billableLang = ref(_lang.timeTrackingStatus.billable);
        const nonBillableLang = ref(_lang.timeTrackingStatus.internal);
        const rtl = ref(_lang.languageSettings['langDirection'] === 'rtl' ? 1 : 0);
        const columns = ref([
            {field: 'log_date', header: 'Date'},
            {field: 'title', header: 'Title', url:'www.google.com'},
            {field: 'name', header: 'Name'},
            {field: 'time_type', header: 'Category'},
            {field: 'effective_effort', header: 'Effective Effort'},
            {field: 'rate', header: 'Rate'},
            {field: 'total', header: 'Total'}
        ]);
        const { FilterMatchMode, FilterOperator } = primevue.api;
        const filters = ref({
            'title': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
            'name': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
            'time_type': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
        });
        watch(() => props.newTimeEntry, (newValue, oldValue) => {
            props.loading = false;
            if ((Array.isArray(newValue) && props.newTimeEntry[0].user.id == userIDGlobal) || props.newTimeEntry.user.id == userIDGlobal) {
                props.loading = true;
                if (Array.isArray(newValue)) {
                    var newTimeLog = newValue.filter(timeEntry => timeEntry.log_date === date.value);
                    newTimeLog = newTimeLog.length > 0 ? newTimeLog[0] : '';
                } else var newTimeLog = newValue.log_date === date.value ? newValue : '';
                if (newTimeLog) { 
                    newTimeLog = { ...newTimeLog, showPauseIcon: 1, showTimerCounter: 0 };
                    if (timelogs.value === "") {
                        timelogs.value = [newTimeLog];
                        totalRecords.value = 1
                    } else {
                        timelogs.value.push(newTimeLog)
                        totalRecords.value = totalRecords.value + 1
                    }
                }
                props.loading = false;
            } 
        });
        watch(() => props.editedTimeEntry, (newValue, oldValue) => {
            editTimeEntry(newValue)
        });
        const loader = (action) => showLoader.value = action
        const editTimeEntry = (timeEntry) => {
            props.loading = true;
            var index = timelogs.value.findIndex(x => x.id === timeEntry.id);
            var remove = timelogs.value[index].log_date === timeEntry.log_date ? 0 : 1;
            timelogs.value = timelogs.value.filter(x => x.id !== timeEntry.id)
            timeEntry = { ...timeEntry, showPauseIcon: 1, showTimerCounter: 0 };
            timeEntry = timeEntry.total ? { ...timeEntry, total: parseFloat(timeEntry.total).toFixed(2)} : { ...timeEntry};
            if (!remove) timelogs.value.splice(index, 0, timeEntry)
            props.loading = false;
        }
        const onPage = (event) => {
            props.loading = true;
            var filterParams = getFilterParams(event.filters);
            event.sort = timelogsParams.value.sort
            event.sortField = timelogsParams.value.sortField
            timelogsParams.value = event;
            timelogsParams.value.filterParams = filterParams;
            context.emit('handlechange', filters.value, dt.value);
            getMyTimeLogsList();
        };
        const onSort = (event) => {
            props.loading = true;
            var filterParams = getFilterParams(event.filters);
            event.sort = event.sortOrder === 1 ? 'asc' : 'desc'
            event.page = 0
            timelogsParams.value = event;
            timelogsParams.value.filterParams = filterParams;
            context.emit('handlechange', filters.value, dt.value);
            getMyTimeLogsList();
        };
        const onFilter = () => {
            props.loading = true;
            var filterParams = getFilterParams(filters.value);
            timelogsParams.value.filters = filters.value;
            timelogsParams.value.filterParams = filterParams;
            context.emit('handlechange', filters.value, dt.value);
            getMyTimeLogsList();
        }
        const getFilterParams = (filterObject) => {
            var filterParams = '';
            for (const [key, value] of Object.entries(filterObject)) {
                if(value.constraints[0].value !== null) {
                    filterParams += '&' + key + '[' + value.constraints[0].matchMode + ']=' + value.constraints[0].value;
                } 
            }
            return filterParams;
        }
        const openMoreDetails = (data, event) => {
            summary.value = data
            op.value.toggle(event);
        }
        const redirect = (record) => {
            var url = record.legal_case_id ? getBaseURL() + 'cases/edit/' + record.legal_case_id : getBaseURL() + 'tasks/view/' + record.task_id;
            window.open(url, '_blank');
        }
        const timeEntryOfRunningTimer = ref();
        const timer = ref({
            sec: 0,
            min: 0,
            h: 0,
            secString : '00',
            minString : '00',
            hString : '00',
        });
        const timerClass = ref('timerSm');
        var Interval;
        const showTimer = (timeEntry) => {
            if(!timeEntryOfRunningTimer.value){
                timeEntryOfRunningTimer.value = timeEntry;
                timeEntry.showTimerCounter = 1;
                timerClass.value = 'timerLg';
                playTimer(timeEntry)
            } else {
                saveTimer(timeEntryOfRunningTimer.value, timeEntry)
            }
        }
        const pauseTimer = (timeEntry) => {
            clearInterval(Interval);
            timeEntry.showPauseIcon = 0;
        }
        const playTimer = (timeEntry, continueTimer = false) => {
            if(!continueTimer) {
                if(timeEntry.effective_effort.includes("h") && timeEntry.effective_effort.includes("m")){
                    var arr = timeEntry.effective_effort.split("h");
                    timer.value.h = parseInt(arr[0]);
                    timer.value.min = parseInt(arr[1]);
                }else if(timeEntry.effective_effort.includes("h")){
                    var arr = timeEntry.effective_effort.split("h")
                    timer.value.h = parseInt(arr[0]);
                    timer.value.min = 0;
                }else if(timeEntry.effective_effort.includes("m")){
                    var arr = timeEntry.effective_effort.split("m")
                    timer.value.min = parseInt(arr[0]);
                    timer.value.h = 0;
                }
                timer.value.sec = 0;
                setTimerString('sec', 'secString');
                setTimerString('min', 'minString');
                setTimerString('h', 'hString');
            }
            timeEntryOfRunningTimer.value = timeEntry;
            timerClass.value = 'timerLg';
            timeEntry.showTimerCounter = 1;
            timeEntry.showPauseIcon = 1;
            clearInterval(Interval);
            Interval = setInterval(startTime, 1000);
        }
        const setTimerString = (unit, unitString) => {
            if(timer.value[unit] <= 9){
                timer.value[unitString] = "0" + timer.value[unit];
            }
            if(timer.value[unit] > 9){
                timer.value[unitString] = timer.value[unit];
            }
        }
        const startTime = () => {
            timer.value.sec++;
            setTimerString('sec', 'secString');
            if(timer.value.sec === 60){
                timer.value.sec = 0;
                timer.value.secString = '00';
                timer.value.min++;
                setTimerString('min', 'minString');
                if(timer.value.min === 60){
                    timer.value.min = 0;
                    timer.value.minString = '00';
                    timer.value.h++;
                    setTimerString('h', 'hString');
                }
            }
        }
        const saveTimer = (timeEntry, newTimeEntry = null) => {
            timer.value.min = timer.value.sec >= 30 ? timer.value.min + 1 : timer.value.min;
            var newEffectiveEfforthour = timer.value.h === 0 ? '' : timer.value.h + 'h';
            var newEffectiveEffortmin = timer.value.min === 0 ? '' : timer.value.min + 'm';
            var newEffectiveEffort = newEffectiveEfforthour + ' ' + newEffectiveEffortmin;
            var equal = false;
            if(!newEffectiveEffortmin)  equal = timeEntry.effective_effort === newEffectiveEfforthour ? true : false;
            if(!newEffectiveEfforthour)  equal = timeEntry.effective_effort === newEffectiveEffortmin ? true : false;
            if (newEffectiveEfforthour && newEffectiveEffortmin) equal = timeEntry.effective_effort === newEffectiveEffort ? true : false;
            if (!equal){
                useprimevue.config.locale.accept = _lang.yes;
                useprimevue.config.locale.reject = _lang.no;
                confirm.require({
                    message: _lang.effectiveEffortUpdateMessage.sprintf([newEffectiveEffort]),
                    header: _lang.effectiveEffortUpdate,
                    icon: 'pi pi-info-circle',
                    accept: () => {
                        loader(true)
                        timeEntryEdit.value = {
                            user_id: parseInt(timeEntry.user.id),
                            logDate: timeEntry.log_date,
                            effectiveEffort: newEffectiveEffort,
                            timeStatus: timeEntry.time_status,
                            istaskormatter: timeEntry.legal_case_id ? 'matter' : 'task',
                            client_id: timeEntry.client_id,
                            rate_system: timeEntry.rate_system,
                            rate: timeEntry.rate,
                            time_type_id: timeEntry.time_type_id,
                            time_internal_status_id: timeEntry.time_internal_status ? timeEntry.time_internal_status.id : null,
                            comments: timeEntry.comments
                        };
                        if(timeEntry.legal_case_id) timeEntryEdit.value['legal_case_id'] = timeEntry.legal_case_id;
                        if(timeEntry.task_id) timeEntryEdit.value['task_id'] = timeEntry.task_id;
                        axios.put(Api.getApiBaseUrl('core') + '/time-log/' + timeEntry.id, timeEntryEdit.value, Api.getInitialHeaders()).then((response) => {
                            loader(false)
                            if (response.data.time_log) {
                                pinesMessageV2({ ty: 'success', m: _lang.effectiveEffortUpdated });
                                context.emit('handleeditchange', response.data.time_log)
                                editTimeEntry(response.data.time_log)
                            } else {
                                pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.error });
                            }
                        }).catch((error) => {
                            loader(false)
                            pinesMessageV2({ ty: 'error', m: (error.response.data.message in _lang) ? _lang[error.response.data.message] : error.response.data.message });
                        });
                    },
                });
            }
            clearInterval(Interval);
            timerClass.value = 'timerSm';
            timeEntry.showTimerCounter = 0;
            if(newTimeEntry) playTimer(newTimeEntry); else timeEntryOfRunningTimer.value = null;
        }
        const getMyTimeLogsList = (lazyParams=null, selectedDate=null) => {
            if(lazyParams !== null) timelogsParams.value = lazyParams;
            if(selectedDate !== null) date.value = selectedDate;
            timelogsParams.value.perPage = dt.value.rows;
            var sortingParams = timelogsParams.value.sort ? '&sort['+timelogsParams.value.sort+']='+timelogsParams.value.sortField : ''
            loader(true)
            axios.get(Api.getApiBaseUrl('core') + '/my-time-logs?log_date='+ date.value +'&per_page='+timelogsParams.value.perPage+'&page='+(timelogsParams.value.page+1)+sortingParams+timelogsParams.value.filterParams + '&language=' + languageGlobal, Api.getInitialHeaders())
                .then(response => {
                    loader(false)
                    props.loading = false;
                    if(response.data.user_activity_logs){
                        totalRecords.value = response.data.page_context.total
                        timelogs.value = response.data.user_activity_logs.map(timeEntry => ({ ...timeEntry, showPauseIcon: 1, showTimerCounter: 0 }));
                        timelogs.value = timelogs.value.map(timeEntry =>
                            timeEntry.total ? { ...timeEntry, total: parseFloat(timeEntry.total).toFixed(2)} : { ...timeEntry})
                    } else {
                        timelogs.value = ''
                    }
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error?.response?.data.message ? error.response.data.message + ' ' + _lang.feedback_messages.tryAgain : _lang.feedback_messages.error });
                    if (error?.response?.status == 401) 
                    localStorage.removeItem('api-access-token');
                    setTimeout(() => window.location = getBaseURL() + 'time_tracking/my_time_logs/', 700);
                    loader(false)
                });
        }
        const edit = (timeEntry) => {
            context.emit('edit', timeEntry);
            window.scrollTo({top: 0, behavior: 'smooth'});
        };
        const copy = (timeEntry) => {
            context.emit('copy', timeEntry);
            window.scrollTo({top: 0, behavior: 'smooth'});
        };
        const deleteConfirmation = (timeEntry) => {
            useprimevue.config.locale.accept = _lang.yes;
            useprimevue.config.locale.reject = _lang.no;
            confirm.require({
                message: _lang.timeEntryDeleteConfirmation,
                header: _lang.deleteConfirmation,
                icon: 'pi pi-info-circle',
                acceptClass: 'p-button-danger',
                accept: () => {
                    deleteTimeEntry(timeEntry)
                },
            });
        }
        const deleteTimeEntry = (timeEntry) => {
            loader(true)
            axios.delete(Api.getApiBaseUrl("core") + '/time-log/' + timeEntry.id, Api.getInitialHeaders())
                .then((response) => {
                    loader(false)
                    pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.timeEntryDeleted});
                    timelogs.value = timelogs.value.filter(x => x.id !== timeEntry.id);
                    totalRecords.value = totalRecords.value - 1;
                    context.emit('handledeletechange', timeEntry)
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error?.response?.data.message ? (error.response.data.message in _lang ? _lang[error.response.data.message] : error.response.data.message + ' ' + _lang.feedback_messages.tryAgain) : _lang.feedback_messages.error });
                    if (error?.response?.status == 401) 
                        localStorage.removeItem('api-access-token');
                    setTimeout(() => window.location = getBaseURL() + 'time_tracking/my_time_logs/', 700);
                    loader(false)
                });
        }
        return{
            op,
            dt,
            timelogs,
            totalRecords,
            columns,
            filters,
            summary,
            timeEntryOfRunningTimer,
            timer,
            timeEntryEdit,
            timerClass,
            rtl,
            billableLang,
            nonBillableLang,
            showLoader,
            onPage,
            onSort,
            onFilter,
            getMyTimeLogsList,
            edit,
            copy,
            deleteTimeEntry,
            deleteConfirmation,
            openMoreDetails,
            redirect,
            showTimer, 
            pauseTimer,
            playTimer,
            saveTimer,
            startTime,
            setTimerString,
            editTimeEntry,
            trimHtmlTags
        }
    },
    template: `
        <loader :show="showLoader"></loader>
        <p-datatable :value="timelogs" ref="dt" :lazy="true" responsiveLayout="scroll" :paginator="true" :rows="6"
        v-model:filters="filters" filter-display="menu" :loading="loading" :total-records="totalRecords" @page="onPage($event)" @sort="onSort($event)" @filter="onFilter($event)" responsive-layout="scroll"
        :global-filter-fields="['name', 'time_type']">
            <template #header>
                <div class="row">
                    <h3 class="col-md-6 no-margin">`+ _lang.loggedTime +`</h3>
                    <div v-if="false" class="col-md-6" class="excel-button">
                        <p-button icon="pi pi-external-link" label="`+ _lang.exportToExcel +`" @click="exportCSV($event)" />
                    </div>
                </div> 
            </template>
            <template #empty>`+ _lang.noTimeEntries +`</template>
            <template #loading>
                Loading Time Logs. Please wait.
            </template>
            <p-column field='log_date', header='`+ _lang.date +`'></p-column>
            <p-column field='title', header='`+ _lang.id +`' :sortable="true">
                <template #body="slotProps">
                    <a @click="redirect(slotProps.data)" v-text="slotProps.data.title" class="link-style-with-underline"/>
                </template>
            </p-column> 
            <p-column field="name" header="`+ _lang.name +`" sortable>
                <template #body="slotProps">
                    {{slotProps.data.name}}
                </template>
                <template #filter="{filterModel}">
                    <p-inputtext type="text" v-model="filterModel.value" class="p-column-filter" placeholder="Search by Name"></p-inputtext>
                </template>
            </p-column>
            <p-column field="time_type" header="`+ _lang.category +`" sortable>
                <template #body="{data}">
                    {{data.time_type ? data.time_type : null}}
                </template>
                <template #filter="{filterModel}">
                    <p-inputtext type="text" v-model="filterModel.value" class="p-column-filter" placeholder="Search by Category"></p-inputtext>
                </template>
            </p-column>
            <p-column field="time_status" header="`+ _lang.timeStatus +`" sortable>
                <template #body="{data}">
                    <p-badge v-if="data.time_status === 'billable'" :value="billableLang" severity="success" size="large" class="p-mr-2" style="font-size:0.9rem;height:auto;"></p-badge>
                    <p-badge v-if="data.time_status === 'internal'" :value="nonBillableLang" size="large" class="p-mr-2" style="font-size:0.9rem;background:#A4A4AB;height:auto;"></p-badge>
                </template>
            </p-column> 
            <p-column v-if="false" :class="timerClass">
                <template #body="slotProps">
                    <div class="p-grid">
                        <i v-if="!slotProps.data.showTimerCounter" class="pi pi-clock icon-button-datatable" v-tooltip.top="'`+ _lang.startTimer +`'" @click="showTimer(slotProps.data)"></i>
                        <div v-if="slotProps.data.showTimerCounter" class="btn p-col-2" @click="saveTimer(slotProps.data)"><i class="fa fa-stop" v-tooltip.top="'`+ _lang.stop +`'" style="fontSize: 1.6rem;color:#2884e2;"></i></div>
                        <div v-if="slotProps.data.showTimerCounter && slotProps.data.showPauseIcon" class="btn p-col-2" @click="pauseTimer(slotProps.data)"  v-tooltip.top="'`+ _lang.pause +`'"> <i class="fa fa-pause" style="fontSize: 1.6rem;color:#2884e2;"></i></div>
                        <div v-if="slotProps.data.showTimerCounter && !slotProps.data.showPauseIcon" class="btn p-col-2" @click="playTimer(slotProps.data, true)" v-tooltip.top="'`+ _lang.start +`'"><i class="fa fa-play" style="fontSize: 1.6rem;color:#2884e2;"></i></div>
                        <div v-if="slotProps.data.showTimerCounter" class="p-col-8">{{timer.hString}}h : {{timer.minString}}m : {{timer.secString}}s</div>
                    </div>
                </template>
            </p-column>
            <p-column field='effective_effort', header='`+ _lang.case_columns.effectiveEffort +`' :sortable="true"></p-column>
            <p-column field='rate', header='`+ _lang.rate +`' :sortable="true"></p-column>
            <p-column field='total', header='`+ _lang.total +`' :sortable="true"></p-column>
            <p-column header-style="width:9.5rem;text-align:center" body-style="text-align: center; overflow: visible">
                <template #body="slotProps">
                    <a class="link-style-with-underline" @click="openMoreDetails(slotProps.data, $event)"><p>`+ _lang.moreDetails +`</p></a>
                    <p-overlaypanel ref="op" append-to="body" :show-close-icon="true" id="overlay_panel" style="width: 450px" :breakpoints="{'1920': '30vw', '1536px': '22vw', '960px': '50vw'}">
                        <div class="row">
                        <div class="summary-box col-md-12">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="time-entry-details" class="table table-borderless table-hover mb-5">
                                        <tbody>
                                            <tr>
                                                <th>` + _lang.date + `:</th>
                                                <th style="width: 50%;" class="text-right detail-value">{{summary.log_date}}</th>
                                            </tr>
                                            <tr>
                                                <th v-if="summary.task_id">` + _lang.taskId + `:</th>
                                                <th v-if="summary.legal_case_id">` + _lang.caseId + `:</th>
                                                <th class="text-right detail-value">
                                                    <a @click="redirect(summary)" v-text="summary.title" class="link-style-with-underline"/>
                                                </th>
                                            </tr>
                                            <tr v-if="summary.task_id">
                                                <th style="width:30%">` + _lang.taskTitle + `:</th>
                                                <th class="text-right detail-value">{{summary.name}}</th>
                                            </tr>
                                            <tr v-else-if="summary.legal_case_id">
                                                <th style="width:30%">` + _lang.caseSubject + `:</th>
                                                <th class="text-right detail-value">{{summary.name}}</th>
                                            </tr>
                                            <tr v-if="summary.task_id">
                                                <th style="width:30%">` + _lang.task_description + `:</th>
                                                <th class="text-right detail-value">{{trimHtmlTags(summary.task.description)}}</th>
                                            </tr>
                                            <tr v-if="summary.client">
                                                <th style="width:30%">` + _lang.clientName + `:</th>
                                                <th class="text-right detail-value">{{summary.client.name}}</th>
                                            </tr>
                                            <tr>
                                                <th>` + _lang.efftEffort + `:</th>
                                                <th class="text-right detail-value">{{summary.effective_effort}}</th>
                                            </tr>
                                            <tr>
                                                <th>` + _lang.efftEffortHours + `:</th>
                                                <th class="text-right detail-value">{{summary.effective_effort_in_hr}}h</th>
                                            </tr>
                                            <tr v-if="summary.rate">
                                                <th>` + _lang.rate + `:</th>
                                                <th class="text-right detail-value">{{summary.rate}}</th>
                                            </tr>
                                            <tr v-if="summary.total">
                                                <th>` + _lang.total + `:</th>
                                                <th class="text-right detail-value">{{summary.total}}</th>
                                            </tr>
                                            <tr v-if="summary.billing_status">
                                                <th>` + _lang.billingStatus + `:</th>
                                                <th class="text-right detail-value">{{summary.billing_status}}</th>
                                            </tr>
                                            <tr v-if="summary.time_type">
                                                <th>` + _lang.category + `:</th>
                                                <th class="text-right detail-value">{{summary.time_type}}</th>
                                            </tr>
                                            <tr v-if="summary.time_internal_status">
                                                <th>`+ _lang.timeInternalStatus +`:</th>
                                                <th class="text-right detail-value">{{summary.time_internal_status.name}}</th>
                                            </tr>
                                            <tr>
                                                <th>`+ _lang.timeStatus +`:</th>
                                                <th v-if="summary.time_status === 'billable'" class="text-right detail-value">
                                                    <p-badge :value="'Billable'" severity="success" style="font-size: 0.9rem;"></p-badge>
                                                </th>
                                                <th v-if="summary.time_status === 'internal'" class="text-right detail-value">
                                                    <p-badge :value="'Non-Billable'" severity="danger" style="font-size: 0.9rem;background:#A4A4AB"></p-badge>
                                                </th>
                                            </tr>
                                            <tr v-tooltip.right="summary.comments">
                                                <th >`+ _lang.description +`:</th>
                                                <th class="text-right detail-value text-truncate">{{summary.comments}}</th>
                                            </tr>
                                            <tr>
                                                <th>`+ _lang.createdOn +`:</th>
                                                <th class="text-right detail-value">{{summary.created_on.substring(0, 10)}}</th>
                                            </tr>
                                            <tr>
                                                <th style="width:30%">`+ _lang.createdBy +`:</th>
                                                <th class="text-right detail-value">{{summary.created_by_profile.name}}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </p-overlaypanel>
                </template>
            </p-column>
            <p-column header-style="width: 2.5rem; text-align: center" body-style="text-align: center; overflow: visible">
                <template #body="slotProps">
                    <p-button icon="pi pi-pencil" class="p-button-rounded p-mr-2" v-tooltip.top="'`+ _lang.edit +`'" @click="edit(slotProps.data)"></p-button>
                </template>
            </p-column>
            <p-column header-style="width: 2.5rem; text-align: center" body-style="text-align: center; overflow: visible">
                <template #body="slotProps">
                    <p-button icon="pi pi-copy" class="p-button-rounded p-mr-2" v-tooltip.top="'`+ _lang.copy +`'" @click="copy(slotProps.data)"></p-button>
                </template>
            </p-column>
            <p-column header-style="width: 2.5rem; text-align: center" body-style="text-align: center; overflow: visible">
                <template #body="slotProps">
                    <p-button icon="pi pi-trash" class="p-button-rounded p-mr-2" v-tooltip.top="'`+ _lang.delete +`'" @click="deleteConfirmation(slotProps.data)"></p-button>
                </template>
            </p-column>
        </p-datatable>
        <div>
            <p-confirmdialog></p-confirmdialog>
        </div>
    `,
};