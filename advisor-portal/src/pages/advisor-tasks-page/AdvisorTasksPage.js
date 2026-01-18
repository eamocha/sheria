import React, {
    useContext,
    useEffect,
    useRef,
    useState
} from 'react';

import './AdvisorTasksPage.scss';

import APMaterialTable from '../../components/common/APMaterialTable/APMaterialTable.lazy';

import {
    Context,
    initialGlobalState
} from '../../Store';

import {
    Button,
} from '@material-ui/core';

import {
    DATA_GRIDS,
    FORMS_MODAL_TITLES,
    FORMS_NAMES,
    MAIN_MENU_TABS_NAMES,
    PAGES_IDS,
    PAGES_TITLES
} from '../../Constants';

import {
    Link,
    useParams
} from 'react-router-dom';

import {
    addEllipsis,
    buildErrorMessages,
    buildInstanceURL,
    getValueFromLanguage
} from './../../APHelpers';

import AdvisorTask from './../../api/AdvisorTask';

import {
    ActionsToolbar,
    SlideView
} from './../../components/pages/advisor-tasks/AdvisorTasksPageComponents';

import {
    APPageActionsToolbar,
    APPageHeader,
    APPageContainer,
    APPageBody,
    APPageTitle
} from '../../components/common/ap-page/APPage';

import APPrioritySign from './../../components/common/ap-priority-sign/APPrioritySign.lazy';

import APStatusBadge from '../../components/common/ap-status-badge/APStatusBadge.lazy';

import { format } from 'date-fns';

import { useTranslation } from 'react-i18next';

import { getActiveLanguageId } from '../../i18n';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const tabelRef = useRef();

    const upcomingFilterQuery = {
        "DueDate": {
            "value": format(
                new Date(),
                'yyyy-MM-dd'
            ),
            "operator": "greaterThan"
        },
        "Assignee": {
            "value": globalState?.user?.data?.id
        }
    }
    const todayFilterQuery = {
        "DueDate": {
            "value": format(
                new Date(),
                'yyyy-MM-dd'
            )
        },
        "Assignee": {
            "value": globalState?.user?.data?.id
        }
    }

    let { filter } = useParams();

    let query = {};

    const [showSlideView, setShowSlideView] = useState(false);

    const [activeAdvisorTask, setActiveAdvisorTask] = useState('');

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
            render: rowData => getValueFromLanguage(rowData?.advisor_task_type, 'advisor_task_type_languages', getActiveLanguageId()),
            sorting: false
        },
        {
            title: t('due_date'),
            field: 'due_date'
        },
        {
            title: t('workflow_status'),
            field: 'advisor_task_status[name]',
            render: rowData => <APStatusBadge
                status={rowData?.advisor_task_status}
            />,
            sorting: false
        },
        {
            title: t('priority'),
            field: 'priority',
            render: rowData => <APPrioritySign
                priority={rowData?.priority}
                priorityText={rowData?.priority}
            />
        },
        {
            title: t('estimated_effort'),
            field: 'estimated_effort',
            sorting: false
        },
        {
            title: '',
            field: '',
            render: rowData => <Button
                variant="contained"
                color="primary"
                onClick={() => handleshowSlideViewClick(rowData)}
            >
                {t("show_details")}
            </Button>
        }
    ];

    useEffect(() => {
        if (filter == 'up-coming-tasks') {
            query = upcomingFilterQuery;
        } else if (filter == 'today-tasks') {
            query = todayFilterQuery;
        }

        loadData(query);
    }, [filter])



    useEffect(() => {
        if (globalState?.gridToReload == DATA_GRIDS?.tasks) {
            if (filter == 'up-coming-tasks') {
                query = upcomingFilterQuery;
            } else if (filter == 'today-tasks') {
                query = todayFilterQuery;
            }

            loadData(query);

            globalStateDispatcher({
                gridToReload: initialGlobalState?.gridToReload
            });
        }
    }, [globalState?.gridToReload]);


    useEffect(() => {
        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.advisorTasks
            }
        });
    }, []);

    const getFilter = () => {
        if (filter == 'up-coming-tasks') {
            return upcomingFilterQuery;
        } else if (filter == 'today-tasks') {
            return todayFilterQuery;
        }
        else {
            return undefined;
        }

    }

    const [currentFilter, setCurrentFilter] = useState(filter);

    const loadData = (filterQuery) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: false
            }
        });
        if (currentFilter != filter || filterQuery == 'reload') {
            setCurrentFilter(filter);
            tabelRef?.current?.onQueryChange();
        }
    }

    const handleshowSlideViewClick = (data) => {
        setShowSlideView(true);
        setActiveAdvisorTask(data);
    }

    const exportData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorTask.exportGrid().then((response) => {
            var fileDownload = require('js-file-download');

            fileDownload(response?.data, "Exported Tasks.xls");
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                }
            });
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

        AdvisorTask.bulkDelete({ ids: IDs }).then((response) => {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Task(s) has been deleted successfully",
                    severity: "success"
                }
            });

            loadData();
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                }
            });
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
                title: t(FORMS_MODAL_TITLES.advisorTaskAddForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.advisorTaskAddForm,
                    submitCallback: loadData,
                }
            }
        });
    }

    return (
        <APPageContainer
            id={PAGES_IDS.advisorTasksPage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={PAGES_TITLES.advisorTasksPage}
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
                    apiClass="AdvisorTask"
                    apiFunction="getList"
                    requestParams={getFilter()}
                    loadData={loadData}
                    exportData={exportData}
                    handleDelete={deleteData}
                />
                <SlideView
                    showSlideView={showSlideView}
                    setShowSlideView={setShowSlideView}
                    data={activeAdvisorTask}
                />
            </APPageBody>
        </APPageContainer>
    );
});
