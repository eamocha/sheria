import Api from "./Api";
import Axios from 'axios';

export default class AdvisorTaskStatus extends Api {

    static allRelations = [
        'advisorTasks'
    ];

    static controllerName = 'advisor-task-statuses';

}
