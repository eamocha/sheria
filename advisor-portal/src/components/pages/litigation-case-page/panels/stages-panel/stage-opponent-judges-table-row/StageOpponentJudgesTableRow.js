import React from 'react';

import './StageOpponentJudgesTableRow.scss';

import {
    TableCell,
    TableRow
} from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <TableRow>
            <TableCell>
                {props?.opponentJudge?.contact_full_details?.firstName + " " + props?.opponentJudge?.contact_full_details?.lastName}
            </TableCell>
            <TableCell>
                {props?.opponentJudge?.comments}
            </TableCell>
        </TableRow>
    );
});
