import React from 'react';

import './StageOpponentLawyersTableRow.scss';

import {
    TableCell,
    TableRow
} from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <TableRow>
            <TableCell>
                {props?.opponentLawyer?.contact_full_details?.firstName + " " + props?.opponentLawyer?.contact_full_details?.lastName}
            </TableCell>
            <TableCell>
                {props?.opponentLawyer?.comments}
            </TableCell>
        </TableRow>
    );
});
