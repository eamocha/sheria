import Api from "./Api";
import Axios from 'axios';

export default class MiscList extends Api {
    
    static controllerName = 'lists';

    static get(id = 0) {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = {
            headers: headers
        };

        return Axios.get(this.API_BASE_URL + '/cases', config);
    }
}
