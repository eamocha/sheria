import Api from "./Api";
import Axios from 'axios';

export default class LitigationCaseDetail extends Api {
    static allRelations = [
        'stageName.stageNameLanguages',
        'stageStatus.stageStatusLanguages',
        'modifiedByUser',
        'stageClientPosition',
        'stageCourt',
        'stageCourtType',
        'stageCourtDegree',
        'stageCourtRegion',
        'stageOpponents',
        'stageExternalReferences',
        'stageOpponentLawyers',
        'stageOpponentLawyers.contactFullDetails',
        'stageOpponentLawyers.contactRoleFullDetails',
        'stageJudges',
        'stageJudges.contactFullDetails',
        'stageJudges.contactRoleFullDetails',
        'stageHearings',
        'stageHearings.hearingType.hearingTypeLanguages',
        'stageHearings.assignees.advisor',
        'stageHearings.hearingDocuments.document',
        'stageHearings.legalCase',
        'stageAdvisorTasks',
        'stageAdvisorTasks.advisorTaskType',
        'stageAdvisorTasks.advisorTaskStatus',
        'stageAdvisorTasks.assignee'
    ];

    static controllerName = 'case-litigation-details';

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

        return Axios.get(this.API_BASE_URL + '/case-litigation-detail/' + id + '?expandRelations=' + relations.join(), config);
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
