import Api from "./Api";
import Axios from 'axios';

export default class AdvisorTimeLog extends Api {

    static allRelations = [
        'advisorUser',
        'advisorTask',
        'legalCase',
        'timeType'
    ];

    static controllerName = 'advisor-time-logs';

    static get(id = 0, relations = []) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        relations = relations.concat(this.allRelations);

        return Axios.get(this.API_BASE_URL + '/advisor-time-log/' + id + "?expandRelations=" + relations.join(), config);
    }

    static create(data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/advisor-time-log', data, config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/advisor-time-log/' + id, data, config);
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

        return Axios.delete(this.API_BASE_URL + '/advisor-time-log/bulk', config);
    }

    static exportGrid(query = {}) {
        var qs = require('qs');

        // query.advisor = {
        //     value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
        // }

        var headers = this.getInitialHeaders();

        var config = {
            // params: query,
            // paramsSerializer: (params) => {
            //     return qs.stringify(params, { arrayFormat: 'brackets' })
            // },
            headers: headers,
            responseType: 'blob'
        };

        return Axios.get(this.API_BASE_URL + '/advisor-time-log/export-grid', config);
    }

    static importExcelFile(data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/import-advisor-time-log', data, config);
    }

    static downloadTemplate() {
        var headers = this.getInitialHeaders();

        headers['Cache-Control'] = 'no-cache; private';

        var config = {
            headers: headers,
            responseType: 'blob'
        };

        return Axios.get(this.API_BASE_URL + '/download-advisor-time-log-template', config);
    }
}
