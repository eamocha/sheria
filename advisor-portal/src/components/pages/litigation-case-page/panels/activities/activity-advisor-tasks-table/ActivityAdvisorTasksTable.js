import React, {
    useEffect,
    useState
} from 'react';

import './ActivityAdvisorTasksTable.scss';

import {
    Container,
    Button,
    Collapse,
    TableContainer,
    Table,
    TableBody,
    TableFooter,
    TableRow,
    Paper
} from '@material-ui/core';

import {
    ExpandLess,
    ExpandMore
} from '@material-ui/icons';

import {
    ActivityAdvisorTasksTableHead,
    ActivityAdvisorTasksTableRow
} from '../LitigationCasePageActivitiesPanel';
 
export default React.memo((props) => {
    const [advisorTasks, setAdvisorTasks] = useState(props?.advisorTasks ?? []);
    const [isExpanded, setIsExpanded] = useState(props?.isExpanded ? props?.isExpanded === 0 ? false : true : true);

    useEffect(() => {

        setAdvisorTasks(props?.advisorTasks ?? []);
    }, [props?.advisorTasks]);

    let tableBodyContent = advisorTasks.map((item, key) => {
        return (
            <ActivityAdvisorTasksTableRow
                key={'litigation-case-activity-advisor-tasks-table-record-' + key}
                advisorTask={item}
                setActiveAdvisorTask={props.setActiveAdvisorTask}
                setShowAdvisorTaskSlideView={props.setShowAdvisorTaskSlideView}
                loadActivities={props.loadActivities}
            />
        );
    });

    return (
        <Container
            maxWidth={false}
            className="litigation-case-advisor-tasks-table"
        >
            <Button
                color="primary"
                onClick={() => setIsExpanded(prevState => !prevState)}
                className="advisor-tasks-table-caption"
            >
                {isExpanded ? <ExpandLess /> : <ExpandMore />} {props?.title}
            </Button>
            <Collapse
                in={isExpanded}
                classes={{
                    wrapperInner: "collapse"
                }}
            >
                <TableContainer
                    component={Paper}
                    className="table-container"
                >
                    <Table
                        size="small"
                    >
                        <ActivityAdvisorTasksTableHead />
                        <TableBody>
                            {tableBodyContent}
                        </TableBody>
                        <TableFooter>
                            <TableRow>
                                {/* <TablePagination
                                    rowsPerPageOptions={[5, 10, 20, { label: 'All', value: -1 }]}
                                    colSpan={8}
                                    count={props.data.length}
                                    rowsPerPage={rowsPerPage}
                                    page={page}
                                    SelectProps={{
                                        inputProps: { 'aria-label': 'rows per page' },
                                        native: true,
                                    }}
                                    onChangePage={handleChangePage}
                                    onChangeRowsPerPage={handleChangeRowsPerPage}
                                    ActionsComponent={TablePaginationActions}
                                /> */}
                            </TableRow>
                        </TableFooter>
                    </Table>
                </TableContainer>
            </Collapse>
        </Container>
    );
});
