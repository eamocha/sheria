import Api from "./Api";
import Axios from 'axios';

class Hearing extends Api {
    static allRelations = [
        'hearingType.hearingTypeLanguages',
        'hearingDocuments.document',
        'assignees.user.userProfile',
        'legalCase',
        'hearingStage.stageName.stageNameLanguages',
        'hearingStage.stageCourtRegion',
        'hearingStage.stageCourt'
    ];

    static controllerName = 'case-hearings';

    static create(data) {
        var headers = this.getInitialHeaders();

        // headers['Content-Type'] = 'multipart/form-data';
        // headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/case-hearing', data, config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/case-hearing/' + id, data, config);
    }

    static delete(id) {
        var headers = this.getInitialHeaders();

        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.delete(this.API_BASE_URL + '/case-hearing/' + id, config);
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

        return Axios.delete(this.API_BASE_URL + '/case-hearings/bulk', config);
    }

    static exportGrid(query = {}, relations = this.allRelations) {
        var qs = require('qs');

        query.user = {
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

        return Axios.get(this.API_BASE_URL + '/case-hearings/export-grid?expandRelations=' + relations.join(), config);
    }

    // static get(id = 0) {
    //     var headers = this.getInitialHeaders();

    //     headers['Content-Type'] = 'application/json';
    //     headers['Accept'] = 'application/json';

    //     var config = {
    //         headers: headers
    //     };

    //     return Axios.get(this.API_BASE_URL + '/task/' + id + '?expandRelations=taskContributors,taskAssignee,taskReporter', config);
    // }

    // static getStatuses(id) {
    //     var headers = this.getInitialHeaders();

    //     var config = {
    //         headers: headers
    //     };

    //     return Axios.get(this.API_BASE_URL + '/task/statuses/' + id, config);
    // }

    // static updateStatus(id, data) {
    //     var headers = this.getInitialHeaders();

    //     headers['Content-Type'] = 'application/json';
    //     headers['Accept'] = 'application/json';

    //     var config = {
    //         headers: headers
    //     };

    //     return Axios.put(this.API_BASE_URL + '/task/status/' + id, data, config);
    // }
}

export default Hearing;
