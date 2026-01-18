import Api from "./Api";
import Axios from 'axios';

export default class AdvisorTask extends Api {
    static allRelations = [
        'legalCase',
        'advisor',
        'assignee',
        'advisorTaskLocation',
        'advisorTaskStatus',
        'advisorTaskType',
        'createdByUser',
        'modifiedByUser',
        'advisorTaskReporter',
        'advisorTaskSharedWithUsers.user',
        'attachments',
        'comments.commentCreator.userProfile'
    ];

    static controllerName = 'advisor-tasks';

    static getList(query = {}, relations = [], options = null) {
        query.advisor = {
            value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
        }

        return super.getList(query, relations, options);
    }

    static get(id = 0, query = {}, relations = []) {
        var qs = require('qs');
        var headers = this.getInitialHeaders();

        relations = relations.concat(this.allRelations);

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            params: query,
            paramsSerializer: (params) => {
                return qs.stringify(params, { arrayFormat: 'brackets' })
            },
            headers: headers
        };

        return Axios.get(this.API_BASE_URL + '/advisor-task/' + id + '?expandRelations=' + relations.join(), config);
    }

    static create(data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/advisor-task', data, config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        // headers['Content-Type'] = 'application/json';
        headers['Accept'] = '*/*';

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/advisor-task/' + id, data, config);
    }

    static delete(id) {
        var headers = this.getInitialHeaders();

        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.delete(this.API_BASE_URL + '/advisor-task/' + id, config);
    }

    static bulkDelete(data) {
        var qs = require('qs');
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            params: data,
            paramsSerializer: (params) => {
                return qs.stringify(params, { arrayFormat: 'brackets' })
            },
            headers: headers
        };

        return Axios.delete(this.API_BASE_URL + '/advisor-task/bulk', config);
    }

    static exportGrid(query = {}, relations = this.allRelations) {
        var qs = require('qs');

        query.advisor = {
            value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
        }

        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/octet-stream';

        var config = {
            params: query,
            paramsSerializer: (params) => {
                return qs.stringify(params, { arrayFormat: 'brackets' })
            },
            headers: headers,
            responseType: 'blob'
        };

        return Axios.get(this.API_BASE_URL + '/advisor-tasks/export-grid?expandRelations=' + relations.join(), config);
    }

    static getStatuses(id) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.get(this.API_BASE_URL + '/advisor-task/statuses/' + id, config);
    }

    static updateStatus(id, data) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/advisor-task/status/' + id, data, config);
    }
}
