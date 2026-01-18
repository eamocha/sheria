import React, {
    forwardRef,
    useContext,
    useEffect,
    useRef,
    useState
} from 'react';

import './APMaterialTable.scss';

import MaterialTable, { MaterialTableProps } from 'material-table';
import { TablePagination, TablePaginationProps } from '@material-ui/core';

import {
    AddBox,
    ArrowUpward,
    Check,
    ChevronLeft,
    ChevronRight,
    Clear,
    DeleteOutline,
    Edit,
    FilterList,
    FirstPage,
    LastPage,
    Remove,
    SaveAlt,
    Search,
    ViewColumn,
    Delete,
    Replay,
    ImportExport,
    ArrowDownward
} from '@material-ui/icons';

import {
    MuiThemeProvider,
    createMuiTheme
} from '@material-ui/core';

import {
    buildErrorMessages,
    isFunction
} from './../../../APHelpers';
import { useTranslation } from 'react-i18next';
import { DEFAULT_PAGE_SIZE, LEGAL_CASES_CATEGORIES } from '../../../Constants';

import LegalCase from '../../../api/LegalCase';
import Hearing from '../../../api/Hearing';
import { Context } from '../../../Store';
import AdvisorTask from '../../../api/AdvisorTask';
import AdvisorTimeLog from '../../../api/AdvisorTimeLog';

const tableIcons = {
    Add: forwardRef((props, ref) => <AddBox {...props} ref={ref} />),
    Check: forwardRef((props, ref) => <Check {...props} ref={ref} />),
    Clear: forwardRef((props, ref) => <Clear {...props} ref={ref} />),
    Delete: forwardRef((props, ref) => <DeleteOutline {...props} ref={ref} />),
    DetailPanel: forwardRef((props, ref) => <ChevronRight {...props} ref={ref} />),
    Edit: forwardRef((props, ref) => <Edit {...props} ref={ref} />),
    Export: forwardRef((props, ref) => <SaveAlt {...props} ref={ref} />),
    Filter: forwardRef((props, ref) => <FilterList {...props} ref={ref} />),
    FirstPage: forwardRef((props, ref) => <FirstPage {...props} ref={ref} />),
    LastPage: forwardRef((props, ref) => <LastPage {...props} ref={ref} />),
    NextPage: forwardRef((props, ref) => <ChevronRight {...props} ref={ref} />),
    PreviousPage: forwardRef((props, ref) => <ChevronLeft {...props} ref={ref} />),
    ResetSearch: forwardRef((props, ref) => <Clear {...props} ref={ref} />),
    Search: forwardRef((props, ref) => <Search {...props} ref={ref} />),
    SortArrow: forwardRef((props, ref) => <ArrowUpward {...props} ref={ref} />),
    ThirdStateCheck: forwardRef((props, ref) => <Remove {...props} ref={ref} />),
    ViewColumn: forwardRef((props, ref) => <ViewColumn {...props} ref={ref} />),
    Replay: forwardRef((props, ref) => <Replay {...props} ref={ref} />),
    ImportExport: forwardRef((props, ref) => <ImportExport {...props} ref={ref} />),
    ArrowDownward: forwardRef((props, ref) => <ArrowDownward {...props} ref={ref} />)
};

const theme = createMuiTheme({
    overrides: {
        MuiPaper: {
            root: {
                border: '1px solid rgba(200, 200, 200, 1)',
                boxShadow: 'none'
            },
            elevation2: {
                boxShadow: 'none'
            }
        },
        MuiToolbar: {
            root: {
                '& [class*="MTableToolbar-title"]': {
                    '& h6': {
                        fontSize: 14
                    }
                }
            }
        },
        MuiTableCell: {
            root: {
                borderBottom: '1px solid rgba(200, 200, 200, 1)',
            }
        },
        MuiTableFooter: {
            root: {
                '[class*="MuiTableCell-root"]': {
                    borderBottom: 'none'
                }
            }
        }
    },
    palette: {
        primary: {
            light: '#3b7fc4',
            main: '#205081',
        },
        secondary: {
            main: '#3b7fc4'
        },
        error: {
            main: '#c9282d'
        }
    },
    typography: {
        fontFamily: [
            '-apple-system',
            'BlinkMacSystemFont',
            '"Segoe UI"',
            'Roboto',
            '"Helvetica Neue"',
            'Arial',
            'sans-serif',
            '"Apple Color Emoji"',
            '"Segoe UI Emoji"',
            '"Segoe UI Symbol"',
        ].join(','),
    },
});

const apis = {
    "LegalCase": LegalCase,
    "Hearing": Hearing,
    "AdvisorTask": AdvisorTask,
    "AdvisorTimeLog": AdvisorTimeLog,
}

export default React.memo((props) => {
    const [t] = useTranslation();
    const [globalState, globalStateDispatcher] = useContext(Context);

    let defaultActions = [
        isFunction(props?.handleDelete) ? {
            tooltip: t('delete_all_selected_rows'),
            icon: Delete,
            onClick: (e, data) => window.confirm(t("are_you_sure_delete_selected")) ? props.handleDelete(data) : null
        } : null,
        {
            tooltip: t('reload'),
            icon: Replay,
            isFreeAction: true,
            onClick: (e, data) => props.loadData()
        },
        {
            tooltip: t('export'),
            icon: ImportExport,
            isFreeAction: true,
            onClick: (e, data) => props.exportData()
        }
    ]

    if (props.importData) {
        defaultActions.push({
            tooltip: t('import'),
            icon: ArrowDownward,
            isFreeAction: true,
            onClick: (e, data) => props.importData()
        })
    }

    const [actions, setActions] = useState(defaultActions);

    useEffect(() => {
        setActions(defaultActions);
    }, [t]);

    const getQueryParams = (query) => {

        let queryParams = { pageSize: query.pageSize, page: query.page + 1 };

        if (query.orderBy) {
            queryParams['orderField'] = query.orderBy.field;
            queryParams['orderDirection'] = query.orderDirection
        }

        return queryParams;
    }

    return (
        <MuiThemeProvider
            theme={theme}
        >
            <MaterialTable
                tableRef={props?.tabelRef}
                {...props}
                icons={tableIcons}
                columns={props?.columns}
                data={query =>
                    new Promise((resolve, reject) => {
                        apis[props.apiClass][props.apiFunction](
                            props.requestParams, [],
                            getQueryParams(query))
                            .then((response) => {
                                resolve({
                                    data: response?.data?.data?.data,
                                    page: response?.data?.data?.current_page - 1,
                                    totalCount: response?.data?.data?.total,
                                });

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
                            });

                    })
                }
                options={{
                    showTitle: false,
                    headerStyle: {
                        fontWeight: 700
                    },
                    cellStyle: {
                        fontWeight: 500
                    },
                    pageSize: DEFAULT_PAGE_SIZE,
                    selection: true,
                    ...props?.options
                }}
                actions={actions}
                components={{
                    Pagination: PatchedPagination,
                }}
            />
        </MuiThemeProvider>
    );
});




export const PatchedPagination = (props) => {
    const {
        ActionsComponent,
        onChangePage,
        onChangeRowsPerPage,
        ...tablePaginationProps
    } = props;

    return (
        <TablePagination
            {...tablePaginationProps}
            // @ts-expect-error onChangePage was renamed to onPageChange
            onPageChange={onChangePage}
            onRowsPerPageChange={onChangeRowsPerPage}
            ActionsComponent={(subprops) => {
                const { onPageChange, ...actionsComponentProps } = subprops;
                return (
                    // @ts-expect-error ActionsComponent is provided by material-table
                    <ActionsComponent
                        {...actionsComponentProps}
                        onChangePage={onPageChange}
                    />
                );
            }}
        />
    );
}