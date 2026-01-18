import axios from 'axios';

export default class Api {
    static API_BASE_URL = process.env.REACT_APP_INSTANCE_URL + this.getInstanceNumberFromURL() + '/api/v2/advisor';
    static API_GRANT_TYPE = 'password';
    static CLIENT = {
        id: 2,
        secret: '6nzM5hv18pCrL8fgFFhgYycWNNFgpjY1ymRNou78'
    };

    static allRelations = [];

    static controllerName = '';

    static getInstanceNumberFromURL() {
        if (!process.env.NODE_ENV || process.env.NODE_ENV === 'development') {
            return '';
        }

        let pathname = window.location.pathname.split('/');
    
        return '/' + pathname[1];
    }

    static getList(query = {}, relations = [], options = null) {
        relations = relations.concat(this.allRelations);

        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = this.buildconfigs(headers, query);

        let paginationData = options ? '&per_page=' + options.pageSize + '&page=' + options.page : '';

        if (options && options.orderField) {
            paginationData += '&order_field=' + options.orderField;
            paginationData += '&order_direction=' + options.orderDirection;
        }

        return axios.get(this.API_BASE_URL + '/' + this.controllerName + '?expandRelations=' + relations.join() + paginationData, config);
    }

    static getInitialHeaders() {
        let headers = {
            'Authorization': 'Bearer ' + sessionStorage.getItem('A4L-AP-accessToken')
        };

        return headers;
    }

    static buildconfigs(headers, query) {
        var qs = require('qs');
        return {
            params: query,
            paramsSerializer: (params) => {
                return qs.stringify(params, { arrayFormat: 'brackets' })
            },
            headers: headers
        };
    }

    static getCurrentAdvisor() {
        return JSON.parse(sessionStorage.getItem('A4L-AP-user'));
    }
}
