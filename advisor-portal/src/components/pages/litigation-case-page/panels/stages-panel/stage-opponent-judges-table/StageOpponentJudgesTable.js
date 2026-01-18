import React from 'react';

import './StageOpponentJudgesTable.scss';

import {
    Container,
    Paper,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Typography
} from '@material-ui/core';

import { StageOpponentJudgesTableRow } from '../LitigationCasePageStagesPanel';
 
export default React.memo((props) => {
    if (props?.stage?.stage_judges?.length <= 0) {
        return null;
    }

    let StageOpponentJudgesTableRows = props?.stage?.stage_judges.map((opponentJudge, key) => {

        return <StageOpponentJudgesTableRow
            key={"litigation-case-stage-opponent-judge-row-" + key}
            opponentJudge={opponentJudge}
        />
    });

    return (
        <Container
            maxWidth={false}
            className="litigation-case-stage-opponent-judges-table"
        >
            <Typography
                variant="body1"
                className="table-caption"
            >
                Judges
            </Typography>
            <TableContainer
                component={Paper}
            >
                <Table
                    size="small"
                >
                    <TableHead>
                        <TableRow>
                            <TableCell
                                variant="head" 
                                size="medium" 
                                width="50%"
                            >
                                Name
                            </TableCell>
                            <TableCell
                                variant="head" 
                                size="small" 
                                width="50%"
                            >
                                Comments
                            </TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {StageOpponentJudgesTableRows}
                    </TableBody>
                </Table>
            </TableContainer>
        </Container>
    );
});
