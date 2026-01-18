import Api from "./Api";
import Axios from 'axios';

export default class AdvisorTaskComment extends Api {
    static allRelations = [
        'advisorTask',
        'commentCreator.userProfile'
    ];

    static create(data) {
        var headers = this.getInitialHeaders();
        
        var config = {
            headers: headers
        };

        return Axios.post(this.API_BASE_URL + '/advisor-task-comment', data, config);
    }

    static update(id, data) {
        var headers = this.getInitialHeaders();
        
        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/advisor-task-comment/' + id, data, config);
    }

    static delete(id) {
        var headers = this.getInitialHeaders();
        
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.delete(this.API_BASE_URL + '/advisor-task-comment/' + id, config);
    }
}
