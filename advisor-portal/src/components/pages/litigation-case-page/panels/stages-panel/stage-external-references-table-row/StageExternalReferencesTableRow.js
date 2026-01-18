import React from 'react';
import './StageExternalReferencesTableRow.scss';
import {
    TableCell,
    TableRow
} from '@material-ui/core';
 
export default React.memo((props) => {

    return(
        <TableRow>
            <TableCell>
                {props?.externalReference?.number}
            </TableCell>
            <TableCell>
                {props?.externalReference?.refDate}
            </TableCell>
            <TableCell>
                {props?.externalReference?.comments}
            </TableCell>
        </TableRow>
    );
});
