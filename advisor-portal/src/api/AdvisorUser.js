import Api from "./Api";
import Axios from 'axios';

export default class AdvisorUser extends Api {

    static controllerName = 'advisors';

    static allRelations = [
        'company'
    ];

    static getList(query = {}, relations = [], options = null) {

        let currentAdvisor = this.getCurrentAdvisor();

        if (currentAdvisor !== null) {
            if ('company_id' in currentAdvisor && currentAdvisor.company_id !== null) {
                query.company = {
                    value: currentAdvisor.company_id
                };
            } else if ('id' in currentAdvisor && currentAdvisor.id !== null) {
                query.advisor = {
                    value: currentAdvisor.id
                };
            }
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

        return Axios.get(this.API_BASE_URL + '/advisor/' + id + '?expandRelations=' + relations.join(), config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/advisor/' + id, data, config);
    }
}
