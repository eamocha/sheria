import Api from 'Api'
import Loader from 'Loader'

export default {
    name: 'FullCalendar',
    emits: ['handlechange', 'handledatechange'],
    components: {
        'loader': Loader,
        'p-fullcalendar': primevue.fullcalendar,
        'p-badge': primevue.badge,
    },
    setup(props, context) {
        const { ref, watch } = Vue;
        const selectedDate = ref('');
        const showLoader = ref(true)
        const selectedToDate = ref('');
        const firstDayOfMonth = ref();
        const lastDayOfMonth = ref();
        const timelogsTotalDetailed = ref();
        const timelogsTotal = ref();
        const timelogsTotalBillable = ref();
        const timelogsTotalNonBillable = ref();
        const timelogsTotalPerMonthInHr = ref(0);
        const timelogsTotalPerMonth = ref();
        const timelogsAmountPerMonth = ref();
        const timelogsTotalPerMonthLanguage = ref();
        const timelogsAmountPerMonthLanguage = ref();
        const handleDateClick = (arg) => {
            selectedDate.value = arg.dateStr
            context.emit('handlechange', selectedDate.value)
        }
        const handleEventMouseEnter = (arg) => {
            var hoveredDate = arg.event._instance.range.end
        }
        const handleDateSelect = (arg) => {
            selectedDate.value = arg.startStr
            var endDate = new Date(arg.endStr);
            endDate.setDate(endDate.getDate() - 1);
            selectedToDate.value = endDate.toISOString().slice(0, 10);
            context.emit('handledatechange', selectedDate.value, selectedToDate.value)
        }
        const fullCalendar = ref(null);
        const changeEvents = (info, successCallback, failureCallback) => {
            var monthNb = info.start.getMonth();
            var month = _lang.jQuery_datepicker_options.monthNames[monthNb];
            timelogsTotalPerMonthLanguage.value = _lang.totalHoursIn.sprintf([month]);
            timelogsAmountPerMonthLanguage.value = _lang.billableAmountIn.sprintf([month]);
            firstDayOfMonth.value = info.startStr.slice(0, 10); 
            lastDayOfMonth.value = info.endStr.slice(0, 10);
            timelogsTotalPerMonth.value = 0;
            timelogsAmountPerMonth.value = 0;
            getMyMonthlyTimeLogsList(successCallback, true)
        }
        var todayDate = new Date().toISOString().slice(0, 10);
        const calendarOptions =  ref({
            locale: _lang.languageSettings.langAbbr,
            initialDate : todayDate,
            showNonCurrentDates: false,
            headerToolbar: {
                left: 'title',
                center: '',
                right: 'prev,next today'
            },
            buttonText:{
                today: _lang.dateFilter.today
            },
            editable: false,
            selectable:true, 
            selectMirror: true, 
            dayMaxEvents: true,
            eventSources: [],
            dateClick: handleDateClick,
            eventMouseEnter : handleEventMouseEnter,
            select : handleDateSelect,
        });
        const loader = (action) => showLoader.value = action;
        const getMyMonthlyTimeLogsList = (successCallback = null) => {
            if (! successCallback){
                fullCalendar.value.calendar.currentData.calendarApi.addEventSource(
                    {
                        id: 1,
                        events: changeEvents,
                        color: '#28A745' 
                    });
            } else {
                loader(true)
                axios.get(Api.getApiBaseUrl() + '/my-time-logs-summary?start_date=' + firstDayOfMonth.value + '&end_date=' + lastDayOfMonth.value, Api.getInitialHeaders())
                .then(response => {
                    loader(false)
                    if(response.data.user_activity_logs_summary){   
                        timelogsTotalDetailed.value = response.data.user_activity_logs_summary;
                        timelogsTotal.value = getTotalTimeLogsPerMonth(response.data.user_activity_logs_summary);
                        timelogsTotalBillable.value = getTotalTimeLogsPerMonth(response.data.user_activity_logs_summary.filter(timeEntry => timeEntry.time_status === 'billable'));
                        timelogsTotalPerMonthInHr.value = timelogsTotal.value.map(item => item.total_effective_effort).reduce((prev, next) => prev + next);
                        timelogsTotalPerMonth.value = timeHrToHrMin(timelogsTotalPerMonthInHr.value)
                        timelogsAmountPerMonth.value = timelogsTotal.value.map(item => item.total_amount ? item.total_amount : 0).reduce((prev, next) => prev + next).toFixed(2);
                        if (successCallback !== null) {
                            successCallback(
                                timelogsTotalBillable.value.map(v => ({ title: v.total_effective_effort_human, date: v.log_date }))
                            )                           
                        }
                        timelogsTotalNonBillable.value = getTotalTimeLogsPerMonth(response.data.user_activity_logs_summary.filter(timeEntry => timeEntry.time_status === 'internal'));
                        let nonBillable = fullCalendar.value.calendar.currentData.calendarApi.getEventSourceById(2);
                        if (nonBillable) nonBillable.remove(); 
                        fullCalendar.value.calendar.currentData.calendarApi.addEventSource(
                            {
                                id: 2,
                                events: timelogsTotalNonBillable.value.map(v => ({ title: v.total_effective_effort_human, date: v.log_date })), 
                                color: 'rgb(164, 164, 171)', 
                            }); 
                    } else {
                        if (successCallback !== null) {
                            successCallback(
                                []
                            )                           
                        }
                        timelogsTotalDetailed.value = '';
                        timelogsTotal.value = '';
                        timelogsTotalPerMonthInHr.value = '';
                        timelogsTotalPerMonth.value = 0;
                        timelogsAmountPerMonth.value = 0;
                    }
                }).catch((error) => {
                    pinesMessageV2({ ty: 'error', m: error?.response?.data.message ? error.response.data.message + ' ' + _lang.feedback_messages.tryAgain : _lang.feedback_messages.error });
                    if (error?.response?.status == 401) 
                        localStorage.removeItem('api-access-token');
                    setTimeout(() => window.location = getBaseURL() + 'time_tracking/my_time_logs/', 700);
                    loader(false)
                });
            } 
        }
        const getTotalTimeLogsPerMonth = (monthTimeLogs) => {
            var result = [];
            monthTimeLogs.forEach(function (timelog) {
                if (!this[timelog.log_date]) {
                    this[timelog.log_date] = { log_date: timelog.log_date, total_effective_effort: 0 , total_amount: 0, total_effective_effort_human : ''};
                    result.push(this[timelog.log_date]);
                }
                this[timelog.log_date].total_effective_effort += parseFloat(timelog.total_effective_effort);
                this[timelog.log_date].total_amount += parseFloat(timelog.total_amount);
            }, {});
            result.forEach(function (day) {
                day.total_effective_effort_human = timeHrToHrMin(day.total_effective_effort)
            }, {});
            return result
        }
        
        const timeHrToHrMin = (hours) => {
            var hr = parseInt(hours);
            var min = Math.round((hours - hr) * 60)
            return min == 0 ? hr + 'h' :  hr + 'h ' + min + 'm' 
        }
        const refetch = () => {
            fullCalendar.value.calendar.currentData.calendarApi.refetchEvents();
        }
        const instance = Vue.getCurrentInstance();
        return { 
            fullCalendar,
            calendarOptions,
            timelogsTotalPerMonth,
            timelogsAmountPerMonth,
            selectedDate,
            selectedToDate,
            timelogsTotalPerMonthLanguage,
            timelogsAmountPerMonthLanguage,
            showLoader,
            getMyMonthlyTimeLogsList,
            refetch
        }
    },
    template: `
        <loader :show="showLoader"></loader>
        <div class="box-body px-3 pt-3 pb-1">
            <div class="timelogs-calendar">
                <p-fullcalendar ref="fullCalendar" :options="calendarOptions">
                </p-fullcalendar>
                <div class="box-total">
                    <h6 class="margin-left20 no-margin-bottom">{{timelogsTotalPerMonthLanguage}}: {{timelogsTotalPerMonth}}</h6>
                    <h6 class="margin-right-20 no-margin-bottom">{{timelogsAmountPerMonthLanguage}}: {{timelogsAmountPerMonth}}<h6>
                </div>
                <div class="row justify-content-center mt-2">
                    <div class="col-md-3">
                        <div class="row">
                            <p-badge class="mr-2" style="background-color: #28A745;"></p-badge>
                            <h6>`+ _lang.timeTrackingStatus.billable +`</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <p-badge class="mr-2" style="background-color: rgb(164, 164, 171);"></p-badge>
                            <h6>`+ _lang.timeTrackingStatus.internal +`</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
};