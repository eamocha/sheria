import Api from "./Api";
import Axios from 'axios';

export default class LegalCaseComment extends Api {
    static allRelations = [
        'legalCase',
        'commentCreator.userProfile'
    ];

    static controllerName = 'legal-case-comments';

    static getList(query = {}, relations = [], options = null) {

        query.advisor = {
            value: this.getCurrentAdvisor() === null ? '' : this.getCurrentAdvisor().id
        }

        return super.getList(query, relations, options);
    }

    static create(data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/legal-case-comment', data, config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/legal-case-comment/' + id, data, config);
    }

    static delete(id) {
        var headers = this.getInitialHeaders();

        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.delete(this.API_BASE_URL + '/legal-case-comment/' + id, config);
    }
}
