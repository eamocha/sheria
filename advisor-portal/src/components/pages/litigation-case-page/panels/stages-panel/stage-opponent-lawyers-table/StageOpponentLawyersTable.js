import React from 'react';

import './StageOpponentLawyersTable.scss';

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

import { StageOpponentLawyersTableRow } from '../LitigationCasePageStagesPanel';
 
export default React.memo((props) => {
    if (props?.stage?.stage_opponent_lawyers?.length <= 0) {
        return null;
    }

    let StageOpponentLawyersTableRows = props?.stage?.stage_opponent_lawyers.map((opponentLawyer, key) => {

        return <StageOpponentLawyersTableRow
            key={"litigation-case-stage-opponent-lawyer-row-" + key}
            opponentLawyer={opponentLawyer}
        />
    });

    return (
        <Container
            maxWidth={false}
            className="litigation-case-stage-opponent-lawyers-table"
        >
            <Typography
                variant="body1"
                className="table-caption"
            >
                Opponent Lawyers
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
                        {StageOpponentLawyersTableRows}
                    </TableBody>
                </Table>
            </TableContainer>
        </Container>
    );
});
