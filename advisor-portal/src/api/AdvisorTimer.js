import Api from "./Api";
import Axios from 'axios';

export default class AdvisorTimer extends Api {

    static allRelations = [
        'advisorUser',
        'advisorTask',
        'legalCase',
        'timeType',
        'timeLogs',
    ];

    static controllerName = 'advisor-timers';

    static getList(query = {}, relations = [], options = null) {
        query.advisor = {
            value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
        }

        return super.getList(query, relations, options);
    }

    static get(query = {}, relations = []) {
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

        relations = relations.concat(this.allRelations);
        return Axios.get(this.API_BASE_URL + '/advisor-timer?expandRelations=' + relations.join(), config);
    }


    static create(data) {
        var headers = this.getInitialHeaders();

        // headers['Content-Type'] = 'application/json';
        headers['Accept'] = '*/*';

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/advisor-timer', data, config);

    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        // headers['Content-Type'] = 'application/json';
        headers['Accept'] = '*/*';

        var config = {
            headers: headers
        };
        if (data.status == 'end')
            return Axios.post(this.API_BASE_URL + '/advisor-timer-end/' + id, data, config);
        else
            return Axios.post(this.API_BASE_URL + '/advisor-timer/' + id, data, config);
    }

    static delete(id) {
        var headers = this.getInitialHeaders();

        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.delete(this.API_BASE_URL + '/advisor-timer/' + id, config);
    }

}
