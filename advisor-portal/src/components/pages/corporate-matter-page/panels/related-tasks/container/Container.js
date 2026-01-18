import React, {
    useContext,
    useEffect,
    useRef,
    useState
} from 'react';

import './Container.scss';

import { Container } from '@material-ui/core';

import {
    ActionsToolbar,
//     Dashboard,
//     Details,
//     CustomFields,
//     Notes,
} from './../CorporateMatterPageRelatedTasksPanel';

import { APPageActionsToolbar } from './../../../../../common/ap-page/APPage';

import APMaterialTable from '../../../../../common/APMaterialTable/APMaterialTable.lazy';

import { Link } from 'react-router-dom';

import {
    addEllipsis,
    buildInstanceURL,
    getValueFromLanguage
} from '../../../../../../APHelpers';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import { MAIN_MENU_TABS_NAMES } from '../../../../../../Constants';

import AdvisorTask from '../../../../../../api/AdvisorTask';
import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../../../../../i18n';
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const tabelRef = useRef();
    const [data, setData] = useState([]);

    const { t } = useTranslation();
    
    const tableColumns = [
        {
            title: t('id'),
            field: 'id',
            render: rowData => <Link
                to={`${buildInstanceURL()}/task/${rowData?.id}`}
                className="primary-link"
            >
                {'T' + rowData?.id}
            </Link>
        },
        {
            title: t('description'),
            field: 'description',
            render: rowData => <Link
                to={`${buildInstanceURL()}/task/${rowData?.id}`} 
                className="primary-link"
            >
                {addEllipsis(rowData?.description)}
            </Link>
        },
        {
            title: t('type'),
            field: 'advisor_task_type_id',
            render: rowData => getValueFromLanguage(rowData?.advisor_task_type, 'advisor_task_type_languages', getActiveLanguageId())
        },
        {
            title: t('due_date'),
            field: 'due_date'
        },
        {
            title: t('status'),
            field: 'advisor_task_status[name]',
            // render: rowData => <StatusBadge status={getValueFromProp(rowData, 'advisor_task_status')} />
        },
        {
            title: t('priority'),
            field: 'priority',
            // render: rowData => <PrioritySign priority={getValueFromProp(rowData, 'priority')} priorityText={getValueFromProp(rowData, 'priority')} />
        },
        {
            title: t('estimated_effort'),
            field: 'estimated_effort'
        },
        {
            title: '',
            field: '',
            // render: rowData => <Button variant="contained" color="primary" onClick={() => handleShowTaskDetailsClick(rowData)}>show details</Button>
        }
    ];

    useEffect(() => {

        loadData();

        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.corporateMatters
            }
        });
    }, []);

    const getFilter = () => {
        return {
            "legalCaseId": {
                "value": props?.corporateMatter?.id
            }
        };
    }

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: false
            }
        });
        tabelRef?.current?.onQueryChange();
    }

    const exportData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorTask.exportGrid({
            "legalCaseId": {
                "value": props?.corporateMatter?.id
            }
        }).then((response) => {
            var fileDownload = require('js-file-download');
            
            fileDownload(response?.data, "Exported Corporate Matter Related Tasks.xls");
        }).catch((error) => {
            
        }).finally(() => {

            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    const deleteData = (data) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        let IDs = [];

        data.map((item, key) => {
            
            return IDs.push(item.id);
        });

        AdvisorTask.bulkDelete({ids: IDs}).then((response) => {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Task has been deleted successfully",
                    severity: "success"
                }
            });

            loadData();
        }).catch((error) => {
            
        }).finally(() => {

            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }
    
    return (
        <Container
            id="corporate-matter-page-related-tasks-panel-container"
            maxWidth={false}
            className="no-padding-h"
        >
            <APPageActionsToolbar>
                <ActionsToolbar
                    corporateMatter={props?.corporateMatter}
                    loadCorporateMatterData={props?.loadCorporateMatterData}
                    loadAdvisorTasks={loadData}
                />
            </APPageActionsToolbar>
            <APMaterialTable
                columns={tableColumns}
                tabelRef={tabelRef}
                apiClass="AdvisorTask"
                apiFunction="getList"
                requestParams={getFilter()}
                loadData={loadData}
                exportData={exportData}
                handleDelete={deleteData}
            />
        </Container>
    );
});
