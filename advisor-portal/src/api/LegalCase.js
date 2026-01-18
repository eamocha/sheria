import Api from "./Api";
import Axios from 'axios';

export default class LegalCase extends Api {
    static allRelations = [
        'legalCaseType',
        'successProbability',
        'caseStatus',
        'client.contact',
        'client.company',
        'rootFolder',
        'outsourcesByAdvisor.outsourceContacts.outsourceContact'
    ];

    static controllerName = 'cases';

    static getList(query = {}, relations = [], options = null) {

        query.outsource = {
            value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().company_id
        }

        return super.getList(query, relations, options);
    }

    static get(id, relations = this.allRelations) {
        var qs = require('qs');

        var query = {
            advisor: {
                value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
            },
            OutsourcedCompany: {}
        };

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

        return Axios.get(this.API_BASE_URL + '/case/' + id + '?expandRelations=' + relations.join(), config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/case/' + id, data, config);
    }

    static getStatuses(id) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.get(this.API_BASE_URL + '/case/statuses/' + id, config);
    }

    static updateStatus(id, data) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/case/status/' + id, data, config);
    }

    static exportGrid(query) {
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

        return Axios.get(this.API_BASE_URL + '/cases/export-grid?expandRelations=legalCaseType,caseStatus,client.contact,client.company', config);
    }

    static search(query, relations = '') {
        var qs = require('qs');

        query.advisor = {
            value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
        }

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

        return Axios.get(this.API_BASE_URL + '/cases/search?expandRelations=' + relations, config);
    }

    static updateStage(id, data) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/case/stage/' + id, data, config);
    }

    static updateOutsourceingContacs(id, data) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/case/outsourcing-contacts/' + id, data, config);
    }
}
