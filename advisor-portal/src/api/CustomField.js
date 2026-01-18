import Api from "./Api";
import Axios from 'axios';

export default class CustomField extends Api {
    static allRelations = [
        'customFieldLanguages'
    ];

    static controllerName = 'custom-fields';

    static getList(query = {}, relations = this.allRelations, relationsWithFilters = '') {
        var headers = this.getInitialHeaders();

        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';

        var config = this.buildconfigs(headers, query);

        return Axios.get(this.API_BASE_URL + '/custom-fields?expandRelations=' + (relations.join()) + '&expandRelationsWithFilters=' + relationsWithFilters, config);
    }
}
