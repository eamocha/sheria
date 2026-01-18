import React, {
    useEffect,
    useState
} from 'react';

import './ActivityHearingsTable.scss';

import {
    Container,
    Button,
    Collapse,
    TableContainer,
    Table,
    TableBody,
    Paper
} from '@material-ui/core';

import {
    ExpandLess,
    ExpandMore
} from '@material-ui/icons';

import {
    ActivityHearingsTableHead,
    ActivityHearingsTableRow
} from '../LitigationCasePageActivitiesPanel';

import { useTranslation } from 'react-i18next';
import { sortBy } from 'lodash';
import { concatStrings } from '../../../../../../APHelpers';

export default React.memo((props) => {

    const dateDesc = (b, a) => {
        let aDate = new Date(concatStrings([a?.startDate, a?.startTime]));
        let bDate = new Date(concatStrings([b?.startDate, b?.startTime]));

        if (aDate < bDate) {
            return -1;
        }
        if (aDate > bDate) {
            return 1;
        }
        return 0;
    }

    const [hearings, setHearings] = useState(props?.hearings.sort(dateDesc) ?? []);
    const [isExpanded, setIsExpanded] = useState(props?.isExpanded ? props?.isExpanded === 0 ? false : true : true);


    const [t] = useTranslation();

    useEffect(() => {

        setHearings(props?.hearings ?? []);
    }, [props?.hearings]);

    let tableBodyContent = hearings.map((item, key) => {
        return (
            <ActivityHearingsTableRow
                key={'litigation-case-activity-hearing-table-record-' + key}
                hearing={item}
                litigationCase={props?.litigationCase}
                loadActivities={props?.loadActivities}
            />
        );
    });

    return (
        <Container
            maxWidth={false}
            className="litigation-case-hearings-table"
        >
            <Button
                color="primary"
                onClick={() => setIsExpanded(prevState => !prevState)}
                className="hearings-table-caption"
            >
                {isExpanded ? <ExpandLess /> : <ExpandMore />} {t("hearings")}
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
                        <ActivityHearingsTableHead />
                        <TableBody>
                            {tableBodyContent}
                        </TableBody>
                    </Table>
                </TableContainer>
            </Collapse>
        </Container>
    );
});
