import Api from "./Api";
import Axios from 'axios';

export default class Document extends Api {
    static allRelations = [
        'parentDocument',
        'documentCreator.userProfile',
        'documentModifier.userProfile',
        'legalCaseDocumentType',
        'legalCaseDocumentStatus'
    ];

    static controllerName = 'documents';

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

        return Axios.get(this.API_BASE_URL + '/document/' + id + '?expandRelations=' + relations.join(), config);
    }

    static create(data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/document', data, config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();

        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/document/' + id, data, config);
    }

    static download(id) {
        var headers = this.getInitialHeaders();

        headers['Cache-Control'] = 'no-cache; private';

        var config = {
            headers: headers,
            responseType: 'blob'
        };

        return Axios.get(this.API_BASE_URL + '/document/download/' + id, config);
    }

    static delete(id) {
        var headers = this.getInitialHeaders();

        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.delete(this.API_BASE_URL + '/document/' + id, config);
    }
}
