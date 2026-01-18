import Api from "./Api";
import Axios from 'axios';

export default class LitigationCaseStage extends Api {
    static allRelations = [
        'stageNameLanguages'
    ];

    static controllerName = 'case-stages';

    static get(id = 0, query = {}, relations = this.allRelations) {
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

        return Axios.get(this.API_BASE_URL + '/case-stage/' + id + '?expandRelations=' + relations.join(), config);
    }

    static create(data) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/case-stage', data, config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/case-stage/' + id, data, config);
    }
}
