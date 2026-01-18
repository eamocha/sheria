import React, {
    useContext,
    useEffect,
    useRef
} from 'react';

import './TimeLogsPage.scss';

import APMaterialTable from '../../components/common/APMaterialTable/APMaterialTable.lazy';

import AdvisorTimeLog from '../../api/AdvisorTimeLog';

import { Context, initialGlobalState } from '../../Store';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES,
    DATA_GRIDS,
    LEGAL_CASES_CATEGORIES,
    MAIN_MENU_TABS_NAMES,
    PAGES_IDS,
    PAGES_TITLES
} from '../../Constants';

import { Link, useHistory } from 'react-router-dom';

import {
    addEllipsis, buildInstanceURL
} from '../../APHelpers';

import { ActionsToolbar } from '../../components/pages/time-logs/TimeLogsPageComponents';

import {
    APPageContainer,
    APPageActionsToolbar,
    APPageBody,
    APPageHeader,
    APPageTitle
} from '../../components/common/ap-page/APPage';
import { useTranslation } from 'react-i18next';

import * as qs from 'query-string';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const tabelRef = useRef();

    const { t } = useTranslation();

    const history = useHistory();
    
    const tableColumns = [
        {
            title: t('id'),
            field: 'id',
            render: rowData => <Link
                href="#"
                onClick={(e) => openEditForm(e, rowData)}
                className="primary-link"
                title="Edit"
            >
                {"TL" + rowData.id}
            </Link>
        },
        {
            title: t('date'),
            field: 'logDate'
        },
        {
            title: t('status'),
            field: 'timeStatus',
            render: rowData => rowData?.timeStatus == 'internal' ? 'Non-Billable' : 'Billable'
        },
        {
            title: t('matter_name'),
            field: 'legal_cases.subject',
            render: rowData => <Link
                to={(rowData?.legal_case?.category == LEGAL_CASES_CATEGORIES.litigationCases ? `${buildInstanceURL()}/litigation-case/` : `${buildInstanceURL()}/corporate-matter/`) + rowData?.legal_case_id}
                className="primary-link"
            >
                {rowData?.legal_case?.subject}
            </Link>,
            sorting: false
        },
        {
            title: t('task_description'),
            field: 'advisor_task_id',
            render: rowData => <Link
                to={`${buildInstanceURL()}/task/${rowData?.advisor_task_id}`}
                className="primary-link"
            >
                {addEllipsis(rowData?.advisor_task?.description, 50)}
            </Link>,
            sorting: false
        },
        {
            title: t('effective_effort'),
            field: 'effectiveEffort',
            sorting: false
        },
        {
            title: t('time_spent_on'),
            field: 'time_type_id',
            render: rowData => rowData?.time_type?.time_type_languages[0].name,
            sorting: false
        },
        {
            title: t('comments'),
            field: 'comments',
            render: rowData => addEllipsis(rowData?.comments, 50),
            sorting: false
        }
    ];

    /**
     * gridToReload should be reset in this useEffect,
     * to prevent calling loadData() twice if the component is just mounted
     */
    useEffect(() => {
        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.advisorTimeLogs
            },
            gridToReload: initialGlobalState?.gridToReload
        });
    }, []);
    
    useEffect(() => {
        return history.listen((currentLocation) => {
            loadData();
        })
    }, [history]);

    useEffect(() => {
        if (globalState?.gridToReload == DATA_GRIDS?.timeLogs) {
            loadData();

            globalStateDispatcher({
                gridToReload: initialGlobalState?.gridToReload
            });
        }
    }, [globalState?.gridToReload]);

    const getFilter = () => {
        const parsed = qs.parse(window.location.search);

        let filters = {
            Advisor: {
                value: globalState?.user?.data?.id
            }
        }

        if (parsed.caseId) {
            filters.LegalCase = { value: parsed.caseId }
        }

        return filters;
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

        AdvisorTimeLog.exportGrid().then((response) => {
            var fileDownload = require('js-file-download');

            fileDownload(response?.data, "Exported Time Logs.xls");
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


    const importData = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.importLogsData),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.advisorTimeLogImportForm,
                    submitCallback: loadData,
                }
            }
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

        AdvisorTimeLog.bulkDelete({ ids: IDs }).then((response) => {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Time Log record has been deleted successfully",
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

    const openAddForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.advisorTimeLogAddForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.advisorTimeLogAddForm,
                    submitCallback: loadData,
                }
            }
        });
    }

    const openEditForm = (e, timeLogRecord) => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.advisorTimeLogEditForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.advisorTimeLogEditForm,
                    submitCallback: loadData,
                    data: {
                        timeLog: timeLogRecord
                    }
                }
            }
        });
    }

    return (
        <APPageContainer
            id={PAGES_IDS.timeLogsPage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={PAGES_TITLES.timeLogsPage}
                />
            </APPageHeader>
            <APPageActionsToolbar>
                <ActionsToolbar
                    openAddForm={openAddForm}
                />
            </APPageActionsToolbar>
            <APPageBody>
                <APMaterialTable
                    columns={tableColumns}
                    tabelRef={tabelRef}
                    apiClass="AdvisorTimeLog"
                    apiFunction="getList"
                    requestParams={getFilter()}
                    loadData={loadData}
                    exportData={exportData}
                    handleDelete={deleteData}
                    importData={importData}
                />
            </APPageBody>
        </APPageContainer>
    );
});
