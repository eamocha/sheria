import Api from "./Api";
import Axios from 'axios';

export default class AdvisorUserPreferences extends Api {

    static controllerName = 'advisor-user-preferences';
    
    static getList(query = {}, relations = [], options = null) {
        query.advisor = {
            value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
        }

        return super.getList(query, relations, options);
    }

    static get(query = {}) {
        query.advisor = {
            value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
        }

        var qs = require('qs');

        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            params: query,
            paramsSerializer: (params) => {
                return qs.stringify(params, { arrayFormat: 'brackets' })
            },
            headers: headers
        };

        return Axios.get(this.API_BASE_URL + '/advisor-user-preference', config);
    }


    static create(data) {
        var headers = this.getInitialHeaders();

        // headers['Content-Type'] = 'application/json';
        headers['Accept'] = '*/*';

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/advisor-user-preference', data, config);

    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        // headers['Content-Type'] = 'application/json';
        headers['Accept'] = '*/*';

        var config = {
            headers: headers
        };
        if (id != null)
            return Axios.post(this.API_BASE_URL + '/advisor-user-preference/' + id, data, config);
        else
            return Axios.post(this.API_BASE_URL + '/advisor-user-preference', data, config);
    }

}

export const widgetsArray = [
    { key: "DashboardWidgetToday", name: "my_Tasks_for_today", isVisible: true },
    { key: "DashboardWidgetUpcoming", name: "my_upcoming_tasks", isVisible: true },
    { key: "AdvisorAssignedTasksWidget", name: "tasks_assigned_to_me_by_status", isVisible: true },

    { key: "DashboardHearingsListWidgetToday", name: "my_hearings_today", isVisible: true },
    { key: "DashboardHearingsListWidgetUpcoming", name: "my_upcoming_hearings", isVisible: true },

    { key: "AdvisorRequestedTasksWidget", name: "tasks_requested_by_me_by_status", isVisible: true },
    { key: "AdvisorTimeLogsWidget", name: "my_billable_vs_non_billable_hours", isVisible: true },
    { key: "AdvisorTimeLogsTodayWidget", name: "tasks_requested_by_me_by_status_logged_today", isVisible: true },
];