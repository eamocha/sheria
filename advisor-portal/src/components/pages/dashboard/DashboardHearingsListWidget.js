import React, {
    useEffect,
    useState,
    forwardRef,
    Fragment,
    useContext
} from 'react';
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
    ImportExport
} from '@material-ui/icons';
import MaterialTable, {
    MTableBody,
    MTableToolbar
} from 'material-table';
import {
    MuiThemeProvider,
    createMuiTheme,
    Container,
    Avatar,
    makeStyles,
    Grid,
    Typography,
    TableFooter,
    TableRow,
    TableCell,
    IconButton
} from '@material-ui/core';
import AdvisorTask from './../../../api/AdvisorTask';
import Hearing from './../../../api/Hearing';
import { addEllipsis, buildInstanceURL, getModulePrefix, getValueFromLanguage } from './../../../APHelpers';
import { Link } from 'react-router-dom';
import DoubleArrowIcon from '@material-ui/icons/DoubleArrow';
import APPrioritySign from './../../common/ap-priority-sign/APPrioritySign.lazy';
import APStatusBadge from './../../common/ap-status-badge/APStatusBadge.lazy';
import './DashboardWidget.scss';
import { useTranslation } from 'react-i18next';
import { Context, initialGlobalState } from '../../../Store';
import VisibilityOffIcon from '@material-ui/icons/VisibilityOff';
import { getActiveLanguageId } from '../../../i18n';
import { FORMS_MODAL_TITLES, FORMS_NAMES } from '../../../Constants';

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
    ImportExport: forwardRef((props, ref) => <ImportExport {...props} ref={ref} />)
};

const useStyles = makeStyles({
    counterAvatarContainer: {
        padding: '0px',
        margin: '0',
        border: '2px solid #fff',
        flex: '0',
        borderRadius: '100%'
    },
    defaultCounterAvatar: {
        color: '#fff',
        backgroundColor: '#205081',
        width: '32px',
        height: '32px',
        fontSize: '16px',
        fontWeight: '600'
    },
    muiToolbarRoot: {
        paddingLeft: '15px'
    },
    viewAllLink: {
        fontSize: '16px'
    },
    doubleArrows: {
        width: '.75em',
        height: '.75em'
    }
});

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
        MuiTableBody: {
            root: {
                height: '265px'
            }
        },
        MuiToolbar: {
            root: {
                '& [class*="MTableToolbar-title"]': {
                    '& h6': {
                        fontSize: '18px'
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
                    borderBottom: 'none',
                }
            }
        }
    },
    palette: {
        secondary: {
            // main: 'rgb(93, 163, 233)',
            main: 'rgb(0, 123, 255)',
        },
    },
});

const DashboardHearingsListWidget = React.memo((props) => {

    const [title, setTitle] = useState(props?.widgetTitle);
    const [model, setModel] = useState(props?.model);
    const [query, setQuery] = useState(props?.query);
    const [data, setData] = useState([]);
    const { t } = useTranslation();
    const widgetId = props?.widgetId;

    const [globalState, globalStateDispatcher] = useContext(Context);

    useEffect(() => {
        setTitle(props?.widgetTitle)
    }, [t]);

    const maxRows = 5;
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    const defaultCellStyle = {
        fontSize: '13px'
    };

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

    const columns = [
        {
            title: t('date_and_time'),
            cellStyle: {
                width: '20%',
                ...defaultCellStyle
            },
            render: rowData => <Link
            href="#"
            onClick={(e) => openEditForm(e, rowData)}
            className="primary-link"
            title="Edit"
        >
            { days[new Date(rowData.startDate).getDay()] + " " + rowData.startDate + " " + rowData.startTime}
        </Link>
        },
        {
            title: t('hearing_type'),
            field: 'type',
            cellStyle: {
                width: '10%',
                ...defaultCellStyle
            },
            render: rowData => getValueFromLanguage(rowData?.hearing_type, 'hearing_type_languages', getActiveLanguageId(), null)
        },
        {
            title: t('court'),
            field: 'court_id',
            cellStyle: {
                width: '15%',
                ...defaultCellStyle
            },
            render: rowData => rowData?.hearing_stage?.stage_court?.name
        },
        {
            title: t('court_region'),
            render: rowData => rowData?.hearing_stage?.stage_court_region?.name
        },
        {
            title: t('litigation_case'),
            cellStyle: {
                width: '20%',
                ...defaultCellStyle
            },
            render: rowData => {
                let title = getModulePrefix('case', rowData?.legal_case?.id) + ": " + addEllipsis(rowData?.legal_case?.subject, 20);

                return <Link
                    title={title}
                    to={`${buildInstanceURL()}/litigation-case/${rowData?.legal_case?.id}`}
                    className="primary-link"
                >
                    {title}
                </Link>;
            }
        },
        // {
        //     title: '',
        //     render: rowData => (
        //         <div
        //             className="advisor-task-dashboard-row"
        //         >
        //             <div>
        //                 <PrioritySign
        //                     priority={rowData?.priority}
        //                     priorityText=""
        //                 />
        //             </div>
        //             <div>
        //                 <div>
        //                     <p
        //                         className="no-margin"
        //                     >
        //                         <Link
        //                             title={model == 'AdvisorTask' ? getModulePrefix('advisor-task', rowData?.id) : getModulePrefix('hearing', rowData?.id)}
        //                             to={model == 'AdvisorTask' ? ('/task/' + rowData?.id) : ('/hearing/' + rowData?.id)}
        //                             className="primary-link d-inline-important"
        //                         >
        //                             {model == 'AdvisorTask' ? getModulePrefix('advisor-task', rowData?.id) : getModulePrefix('hearing', rowData?.id)}
        //                         </Link>
        //                         <Typography
        //                             className="d-inline"
        //                         >
        //                             {': ' + rowData?.description}
        //                         </Typography>
        //                     </p>
        //                 </div>
        //                 {getValueFromLanguage(rowData?.advisor_task_type, 'advisor_task_type_languages', 1, '')}
        //             </div>
        //             <div
        //                 className="d-flex align-items-center justify-content-end"
        //             >
        //                 <StatusBadge
        //                     status={rowData?.advisor_task_status}
        //                 />
        //             </div>
        //         </div>
        //     )
        // }
    ];

    useEffect(() => {

        loadData();
    }, []);

    const loadData = () => {
        Hearing.getList(query).then((response) => {

            setData(response.data.data);
        }).catch((errors) => {
           
        }).finally(() => {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    const classes = useStyles();

    if (!data) {
        return null;
    }

    return (
        <MuiThemeProvider
            theme={theme}
        >
            <Container
                maxWidth={false}
                className="dashboard-widget no-padding-h"
            >
                <Grid
                    container
                    className="dashboard-widget-header"
                >
                    <Grid
                        sm="10"
                        className="dashboard-widget-title-container"
                    >
                        <Typography
                            className="dashboard-widget-title"
                        >
                            {title}
                        </Typography>
                    </Grid>
                    <IconButton
                        className={"hide-wideget-btn close-button " + globalState.domDirection}
                        onClick={(e) => props?.hideWidget(props?.name)}
                        size="small"
                        title={t("hide_widget")}
                        color="inherit"
                    >
                        <VisibilityOffIcon
                            fontSize="small"
                        />
                    </IconButton>
                    <Grid
                        sm="2"
                        className={"d-flex align-items-center justify-content-end small-circle " + globalState.domDirection}
                    >
                        <Container
                            className={classes.counterAvatarContainer + " d-flex align-items-center justify-content-end"}
                        >
                            <Avatar
                                className={classes.defaultCounterAvatar}
                                component='span'
                            >
                                {data.length <= maxRows ? data.length : maxRows + '+'}
                            </Avatar>
                        </Container>
                    </Grid>
                </Grid>
                <MaterialTable
                    localization={{
                        body: {
                            emptyDataSourceMessage: (
                                t("no_records_to_display")
                            ),
                        },
                    }}
                    {...props}
                    icons={tableIcons}
                    columns={columns}
                    data={data.slice(0, maxRows)}
                    options={{
                        // header: false,
                        // toolbar: false,
                        showTitle: false,
                        cellStyle: {
                            fontWeight: 500
                        },
                        paging: false,
                        pageSize: maxRows,
                        search: false,
                        maxBodyHeight: 363
                    }}
                    components={{
                        // Toolbar: props => (
                        //     <Grid
                        //         container
                        //     >
                        //         <Grid
                        //             sm="6"
                        //         >
                        //             <MTableToolbar
                        //                 {...props}
                        //                 classes={
                        //                     {
                        //                         root: classes.muiToolbarRoot
                        //                     }
                        //                 }
                        //             />
                        //         </Grid>
                        //         <Grid
                        //             sm="6"
                        //             className="d-flex align-items-center justify-content-end"
                        //         >
                        //             <Container
                        //                 className={classes.counterAvatarContainer + " d-flex align-items-center justify-content-end"}
                        //             >
                        //                 <Avatar
                        //                     className={classes.defaultCounterAvatar}
                        //                     component='span'
                        //                 >
                        //                     {data.length <= maxRows ? data.length  : maxRows + '+'}
                        //                 </Avatar>
                        //             </Container>
                        //         </Grid>
                        //     </Grid>
                        // ),
                        Body: props => (
                            <Fragment>
                                <MTableBody
                                    {...props}
                                />
                                {
                                /* <TableFooter>
                                    <TableRow>
                                        <TableCell
                                            colSpan="4"
                                        >
                                            <Link
                                                to={`${buildInstanceURL()}/hearings/`+ widgetId}
                                                className={classes.viewAllLink + " link gray-link"}
                                            >
                                                <div
                                                    className="d-flex align-items-center justify-content-space-between"
                                                >
                                                    <Typography
                                                        variant="body1"
                                                    >
                                                        View All
                                                    </Typography>
                                                    <DoubleArrowIcon 
                                                        className={classes.doubleArrows}
                                                    />
                                                </div>
                                            </Link>
                                        </TableCell>
                                    </TableRow>
                                </TableFooter> */}
                            </Fragment>
                        )
                    }}
                />
                <TableCell className="widget-footer"
                >
                    <Link
                        to={`${buildInstanceURL()}/hearings/` + widgetId}
                        className={classes.viewAllLink + " link gray-link"}
                    >
                        <div
                            className="d-flex align-items-center justify-content-space-between"
                        >
                            <Typography
                                variant="body1"
                            >
                                {t("view_all")}
                            </Typography>
                            <DoubleArrowIcon
                                className={classes.doubleArrows}
                            />
                        </div>
                    </Link>
                </TableCell>
            </Container>
        </MuiThemeProvider>
    );
});

export default DashboardHearingsListWidget;
