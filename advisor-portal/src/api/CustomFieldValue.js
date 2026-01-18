import Api from "./Api";
import Axios from 'axios';

export default class CustomFieldValue extends Api {   
    static allRelations = [];

    static update(data){
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';
        
        var config = {
            headers: headers
        };

        return Axios.put(this.API_BASE_URL + '/custom-fields-values', data, config);
    }
}
