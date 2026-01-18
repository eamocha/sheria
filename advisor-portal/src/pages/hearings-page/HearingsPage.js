import React, {
    useContext,
    useEffect,
    useRef,
    useState
} from 'react';

import './HearingsPage.scss';

import APMaterialTable from '../../components/common/APMaterialTable/APMaterialTable.lazy';

import {
    Context,
    initialGlobalState
} from '../../Store';

import {
    DATA_GRIDS,
    FORMS_MODAL_TITLES,
    FORMS_NAMES,
    LEGAL_CASES_CATEGORIES,
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
    getAdvisorUserFullName,
    getValueFromLanguage
} from '../../APHelpers';

import { ActionsToolbar } from '../../components/pages/time-logs/TimeLogsPageComponents';

import {
    APPageContainer,
    APPageActionsToolbar,
    APPageBody,
    APPageHeader,
    APPageTitle
} from '../../components/common/ap-page/APPage';

import Hearing from '../../api/Hearing';

import {
    format,
    isValid
} from 'date-fns';

import { useTranslation } from 'react-i18next';

import HearingsRowMenu from './hearings-row-menu/HearingsRowMenu.lazy';
import { getActiveLanguageId } from '../../i18n';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const tabelRef = useRef();

    const [queryStatus, setQueryStatus] = useState({
        "Advisor": {
            "value": globalState?.user?.data?.id
        }
    });

    const upcomingFilterQuery = {
        "StartDate": {
            "value": format(
                new Date(),
                'yyyy-MM-dd'
            ),
            "operator": "greaterThan"
        },
        "Assignees": {
            "value": [
                globalState?.user?.data?.id
            ]
        },
        "Advisor": {
            "value": globalState?.user?.data?.id
        }
    }
    const todayFilterQuery = {
        "StartDate": {
            "value": format(
                new Date(),
                'yyyy-MM-dd'
            )
        },
        "Assignees": {
            "value": [
                globalState?.user?.data?.id
            ]
        },
        "Advisor": {
            "value": globalState?.user?.data?.id
        }
    }

    let { filter } = useParams();

    let query = {
        "Advisor": {
            "value": globalState?.user?.data?.id
        }
    }

    const { t } = useTranslation();

    const tableColumns = [
        {
            title: "",
            render: rowData =>
                rowData.createdByChannel == 'AP'
                    ?
                    <HearingsRowMenu
                        hearing={rowData}
                        handleDeleteHearing={handleDeleteHearing}
                    />
                    :
                    <></>
        },
        {
            title: t('date'),
            field: 'startDate',
            render: rowData => <Link
                href="#"
                onClick={(e) => openEditForm(e, rowData)}
                className="primary-link"
                title="Edit"
            >
                {rowData?.startDate + " " + (isValid(rowData?.startTime) ? (rowData?.startTime.getHours() + ":" + rowData?.startTime.getMinutes()) : rowData?.startTime)}
            </Link>,
            defaultSort: "desc"
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
            title: t('type'),
            field: 'type',
            render: rowData => getValueFromLanguage(rowData?.hearing_type, 'hearing_type_languages', getActiveLanguageId(), ''),
            sorting: false
        },
        {
            title: t('stage'),
            field: 'stage',
            render: rowData => getValueFromLanguage(rowData?.hearing_stage?.stage_name, 'stage_name_languages', getActiveLanguageId(), ''),
            sorting: false
        },
        {
            title: t('assignee_s'),
            field: 'type',
            render: rowData => rowData?.assignees?.map((item, index) => {
                return getAdvisorUserFullName(item.user, item.user_type != "A4L");
            }).join(', '),
            sorting: false
        },
        {
            title: t('summary'),
            field: 'summary',
            render: rowData => addEllipsis(rowData?.summary, 50),
            sorting: false
        },
        {
            title: t('comments'),
            field: 'comments',
            render: rowData => addEllipsis(rowData?.comments, 50),
            sorting: false
        },
        {
            title: t('judged_question'),
            field: 'judged',
            render: rowData => rowData?.judged == 'yes' ? 'Yes' : 'No'
        },
        {
            title: t('judgment'),
            field: 'judgment',
            render: rowData => addEllipsis(rowData?.judgment, 50),
            sorting: false
        }
    ];

    const handleDeleteHearing = (id) => {
        if (window.confirm("Are you sure you want to delete this hearing?")) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Hearing.delete(id).then((response) => {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: "Hearing has been deleted successfully",
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
                    },
                    globalLoader: initialGlobalState?.globalLoader
                });
            }).finally(() => {
                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            });

        }
    }

    useEffect(() => {
        console.log("filter", filter);
        if (filter == 'up-coming-hearings') {
            query = upcomingFilterQuery;
        } else if (filter == 'today-hearings') {
            query = todayFilterQuery;
        } else {
            query = {
                "Advisor": {
                    "value": globalState?.user?.data?.id
                }
            }
        }

        setQueryStatus(query);

        loadData(query);
    }, [filter]);

    useEffect(() => {
        if (globalState?.gridToReload == DATA_GRIDS?.hearings) {
            if (filter == 'up-coming-hearings') {
                query = upcomingFilterQuery;
            } else if (filter == 'today-hearings') {
                query = todayFilterQuery;
            } else {
                query = {
                    "Advisor": {
                        "value": globalState?.user?.data?.id
                    }
                }
            }

            setQueryStatus(query);

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
                activeTab: MAIN_MENU_TABS_NAMES.hearings
            }
        });
    }, []);

    const getFilter = () => {
        if (filter == 'up-coming-hearings') {
            return upcomingFilterQuery;
        } else if (filter == 'today-hearings') {
            return todayFilterQuery;
        } else {
            return {
                "Advisor": {
                    "value": globalState?.user?.data?.id
                }
            }
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

    const exportData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        Hearing.exportGrid(queryStatus).then((response) => {
            var fileDownload = require('js-file-download');

            fileDownload(response?.data, "Exported Hearings.xls");
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
                title: t(FORMS_MODAL_TITLES.hearingAddForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.hearingAddForm,
                    submitCallback: loadData,
                }
            }
        });
    }

    const openEditForm = (e, hearingRecord) => {
        e.preventDefault();

        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.hearingEditForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.hearingEditForm,
                    submitCallback: loadData,
                    data: {
                        hearing: hearingRecord
                    }
                }
            }
        });
    }

    return (
        <APPageContainer
            id={PAGES_IDS.hearingsPage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={PAGES_TITLES.hearingsPage}
                />
            </APPageHeader>
            <APPageActionsToolbar>
                <ActionsToolbar
                    openAddForm={openAddForm}
                />
            </APPageActionsToolbar>
            <APPageBody>
                <APMaterialTable
                    tabelRef={tabelRef}
                    columns={tableColumns}
                    apiClass="Hearing"
                    apiFunction="getList"
                    requestParams={getFilter()}
                    loadData={loadData}
                    exportData={exportData}
                    // handleDelete={deleteData}
                    options={{
                        selection: false
                    }}
                />
            </APPageBody>
        </APPageContainer>
    );
});
