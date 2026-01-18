import Api from 'Api'
import FullCalendar from 'FullCalendar'
import DataTable from 'DataTable'
import Form from 'Form'

export default {
    name: 'MyTimeLogs',
    components: {
        'full-calendar': FullCalendar,
        'data-table': DataTable,
        'timelog-form': Form,
    },
    setup() {
        const { onMounted, ref } = Vue;
        const dataTable = ref(null);
        const timeEntryForm = ref(null);
        const timeEntryCalendar = ref(null);
        var date = new Date();
        var today = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate();
        var tomorrowDate = new Date(date);
        tomorrowDate.setDate(tomorrowDate.getDate()+1);
        var tomorrow = tomorrowDate.getFullYear()+'-'+(tomorrowDate.getMonth()+1)+'-'+tomorrowDate.getDate();
        const selectedDate = ref(today);
        const selectedToDate = ref(tomorrow);
        const repeat = ref(0);
        const lazyParams = ref({});
        const categories = ref([
            {name: 'Matter', key: '1'}, 
            {name: 'Task', key: '2'}, 
        ]);
        const filters = ref({});
        const dt = ref(null);
        const selectedTimeTypes = ref();
        const timeTypes = ref([
            {name: 'Administration', code: '1'},
            {name: 'Analysis', code: '2'},
            {name: 'Bussiness Development', code: '3'},
            {name: 'Drafting', code: '4'},
            {name: 'Hearing', code: '5'}
        ]);
        const selectedCategory = ref(categories.value[0]);
        const loading = ref(true);
        const handleDateClick = (date) => {
            selectedDate.value = date
            var dateAFter = new Date(date);
            dateAFter.setDate(dateAFter.getDate()+1);
            selectedToDate.value = dateAFter.toISOString().slice(0, 10);
            repeat.value = 0;
            timeEntryForm.value.changeDateOnClick(selectedDate.value, selectedToDate.value, repeat.value)
            loading.value = true;
            dataTable.value.getMyTimeLogsList(null, selectedDate.value);
        }
        const handleDurationDateSelect = (fromDate, toDate) => {
            selectedDate.value = fromDate
            var dateAFter = new Date(fromDate);
            dateAFter.setDate(dateAFter.getDate()+1);
            selectedToDate.value = fromDate === toDate ? dateAFter.toISOString().slice(0, 10) : toDate
            repeat.value = fromDate === toDate ? 0 : 1;
            timeEntryForm.value.changeDateOnClick(selectedDate.value, selectedToDate.value, repeat.value)
            loading.value = true;
            if (fromDate !== toDate) dataTable.value.getMyTimeLogsList(null, selectedDate.value);
        }
        const timeEntryCopyNumb = ref(0);
        const timeEntryEditNumb = ref(0);
        const newTimeEntry = ref();
        const editedTimeEntry = ref();
        const timeEntryEdit = ref();
        const timeEntryCopy = ref();
        const handleFilterChange = (dataTableFilter, dataT) => {
            filters.value = dataTableFilter;
            dt.value = dataT;
        }
        const handleNewTimeEntry = (timeEntry) => {
            newTimeEntry.value = timeEntry;
            timeEntryCalendar.value.refetch();
        }
        const handleEditedTimeEntry = (timeEntry) => {
            editedTimeEntry.value = timeEntry;
            timeEntryCalendar.value.refetch();
        }
        const handleDeletedTimeEntry = (timeEntry) => {
            timeEntryCalendar.value.refetch();
        }
        const handleCancelEdit = (timeEntry) => {
            timeEntryEdit.value = timeEntry;
            timeEntryCopy.value = timeEntry;
        }
        const editTimeEntry = (timeEntry) => {
            timeEntryEdit.value = timeEntry;
            timeEntryEditNumb.value ++
        }
        const copyTimeEntry = (timeEntry) => {
            timeEntryCopy.value = timeEntry;
            timeEntryCopyNumb.value ++
        }
        const initialize = () => {
            if (!localStorage.getItem('api-access-token'))
                axios.get(Api.getApiBaseUrl() + '?token=' + tokenGlobal)
                .then(response => {
                    if (response.data.access_token) {
                        let access_token = response.data.access_token;
                        localStorage.setItem('api-access-token', access_token);
                        loadLazyParams();
                        dataTable.value.getMyTimeLogsList(lazyParams.value, selectedDate.value);
                        timeEntryForm.value.getMyTimeEntryFormData();
                        timeEntryCalendar.value.getMyMonthlyTimeLogsList();
                    } else {
                        pinesMessageV2({ ty: 'error', m: _lang.access_denied });
                    }
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error?.response?.data.message ?? _lang.feedback_messages.error });
                });
            else{
                loadLazyParams();
                dataTable.value.getMyTimeLogsList(lazyParams.value, selectedDate.value);
                timeEntryForm.value.getMyTimeEntryFormData();
                timeEntryCalendar.value.getMyMonthlyTimeLogsList();
            }
        }
        const loadLazyParams = () => {
            loading.value = true;   
            lazyParams.value = {
                page: 0,
                perPage: 5,
                sortField: null,
                sortOrder: null,
                filters: filters.value,
                filterParams: ''
            };
        }
        const redirectToAllTimeEntries = () => {
            window.location = 'time_tracking/all_time_logs/';
        }
        const redirectToMyTimeEntries = () => {
            window.location = 'time_tracking/my_time_logs/';
        }
        onMounted(() => {
            initialize()
        })
        return { 
            lazyParams,
            loading, 
            categories, 
            selectedCategory, 
            timeTypes,
            selectedTimeTypes,
            selectedDate,
            dataTable,
            filters, 
            dt,
            newTimeEntry,
            timeEntryEdit,
            timeEntryCopy,
            editedTimeEntry,
            timeEntryCopyNumb,
            timeEntryEditNumb,
            selectedToDate,
            timeEntryForm,
            timeEntryCalendar,
            repeat,
            handleDateClick,
            handleFilterChange,
            handleNewTimeEntry,
            editTimeEntry,
            copyTimeEntry,
            handleEditedTimeEntry,
            handleDeletedTimeEntry,
            handleCancelEdit,
            redirectToAllTimeEntries,
            redirectToMyTimeEntries,
            handleDurationDateSelect,
        }
    },
    template: `
    <div class="row">
        <div id="gridFiltersContainer" class="gh-top-title margin-left20" >
            <h3 class="mt-10" style="display:inline-block">`+ _lang.timeEntryTitle +`</h3>
            <a class="btn btn-default ml-10 mb-5 mr-10-ar" @click="redirectToMyTimeEntries"  title="`+ _lang.myTimeLogs +`" >`+ _lang.myTimeLogs +`</a>
            <a class="btn btn-default ml-10 mb-5 mr-10-ar" @click="redirectToAllTimeEntries"  title="`+ _lang.allTimeLogs +`" >`+ _lang.allTimeLogs +`</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-5 col-md-6 px-2">
            <full-calendar ref="timeEntryCalendar" @handlechange="handleDateClick" @handledatechange="handleDurationDateSelect"></full-calendar>
        </div>
        <div class="col-lg-7 col-md-6 px-2">
            <timelog-form ref="timeEntryForm" :date="selectedDate" :to-date="selectedToDate" :repeat="repeat" @handlechange="handleNewTimeEntry" @handleeditchange="handleEditedTimeEntry" @handlecanceledit="handleCancelEdit" :time-entry-edit="timeEntryEdit" :time-entry-copy="timeEntryCopy" :edit-clicked="timeEntryEditNumb" :copy-clicked="timeEntryCopyNumb"></timelog-form>
        </div>
    </div>
    <div class="mt-15">
        <data-table ref="dataTable" :loading="loading" :new-time-entry="newTimeEntry" :edited-time-entry="editedTimeEntry" @edit="editTimeEntry" @handleeditchange="handleEditedTimeEntry" @handledeletechange="handleDeletedTimeEntry" @copy="copyTimeEntry"></data-table>
    </div>
    `,
};