import React, {
    useContext,
    useEffect,
    useRef
} from 'react';

import './LitigationCasesPage.scss';

import {
    APPageContainer,
    APPageBody,
    APPageHeader,
    APPageTitle,
} from './../../components/common/ap-page/APPage';

import APMaterialTable from '../../components/common/APMaterialTable/APMaterialTable.lazy';

import { Context } from '../../Store';

import {
    PAGES_IDS,
    PAGES_TITLES,
    MAIN_MENU_TABS_NAMES,
    LEGAL_CASES_CATEGORIES
} from '../../Constants';

import LegalCase from '../../api/LegalCase';

import { Link } from 'react-router-dom';

import {
    addEllipsis,
    buildErrorMessages,
    buildInstanceURL
} from '../../APHelpers';

import APPrioritySign from '../../components/common/ap-priority-sign/APPrioritySign.lazy';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {

    const [globalState, globalStateDispatcher] = useContext(Context);
    const tabelRef = useRef();

    const { t } = useTranslation();
    const tableColumns = [
        {
            title: t('id'),
            field: 'id',
            render: rowData => <Link
                to={`${buildInstanceURL()}/litigation-case/${rowData?.id}`}
                className="primary-link"
            >
                {'M' + rowData?.id}
            </Link>
        },
        {
            title: t('subject'),
            field: 'subject',
            render: rowData => <Link
                to={`${buildInstanceURL()}/litigation-case/${rowData?.id}`}
                className="primary-link"
            >
                {rowData?.subject}
            </Link>
        },
        {
            title: t('description'),
            field: 'description',
            render: rowData => addEllipsis(rowData?.description),
            sorting: false
        },
        {
            title: t('practice_area'),
            field: 'legal_case_type[name]',
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
            title: t('value'),
            field: 'caseValue'
        },
        {
            title: t('arrival_date'),
            field: 'arrivalDate'
        },
        {
            title: t('due_date'),
            field: 'dueDate'
        },
    ];

    useEffect(() => {
        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.litigationCases
            }
        });
    }, []);

    const getFilter = () => {
        return {
            category: {
                value: LEGAL_CASES_CATEGORIES.litigationCases
            }
        }
    }

    const loadData = () => {
        tabelRef?.current?.onQueryChange();
    }

    const exportData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        LegalCase.exportGrid({
            category: {
                value: LEGAL_CASES_CATEGORIES.litigationCases
            }
        }).then((response) => {
            var fileDownload = require('js-file-download');

            fileDownload(response.data, "Exported Litigation Cases.xls");
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

    return (
        <APPageContainer
            id={PAGES_IDS.litigationCasesPage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={PAGES_TITLES.litigationCasesPage}
                />
            </APPageHeader>
            <APPageBody>
                <APMaterialTable
                    tabelRef={tabelRef}
                    apiClass="LegalCase"
                    apiFunction="getList"
                    requestParams={getFilter()}
                    columns={tableColumns}
                    loadData={loadData}
                    exportData={exportData}
                    options={{
                        selection: false,
                    }}
                />
            </APPageBody>
        </APPageContainer>
    );
});
