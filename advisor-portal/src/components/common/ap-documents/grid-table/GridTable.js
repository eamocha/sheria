import React, { forwardRef } from 'react';

import './GridTable.scss';

import MaterialTable, {
    MTableBody,
    MTableToolbar
} from 'material-table';

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

import {
    MuiThemeProvider,
    createMuiTheme,
    Grid
} from '@material-ui/core';

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

const theme = {
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
        textPrimary: {
            main: '#3b7fc4',
        },
        secondary: {
            // main: '#3b7fc4',
            main: '#c9282d',
            light: '#c9282d'
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
};

export default React.memo((props) => {

    return (
        <MuiThemeProvider
            theme={createMuiTheme(theme)}
        >
            <MaterialTable
                icons={tableIcons}
                columns={props.columns}
                data={props.data}
                options={{
                    showTitle: false,
                    headerStyle: {
                        fontWeight: 700
                    },
                    cellStyle: {
                        fontWeight: 500
                    },
                    pageSize: 10,
                    selection: true
                }}
                actions={[
                    {
                        tooltip: 'Reload',
                        icon: Replay,
                        isFreeAction: true,
                        onClick: (event, data) => props.loadData()
                    }
                ]}
                components={{
                    Toolbar: p => (
                        <Grid
                            container
                        >
                            <Grid
                                className="d-flex justify-content-start"
                                sm="6"
                            >
                                {props?.tableToolbar}
                            </Grid>
                            <Grid
                                sm="6"
                            >
                                <MTableToolbar
                                    {...p}
                                />
                            </Grid>
                        </Grid>
                    ),
                    Body: props?.body ?? (p => (<MTableBody {...p} />))
                }}
                {...props}
            />
        </MuiThemeProvider>
    );
});
