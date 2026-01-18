import React from 'react';

import {
    TableCell,
    TableRow,
    TableHead,
} from '@material-ui/core';

import './TableHeader.scss';
 
export default React.memo((props) => {
    return (
        <TableHead>
            <TableRow>
                <TableCell 
                    variant="head"
                    size="small"
                    width="15%"
                >
                    Select
                </TableCell>
                <TableCell 
                    variant="head"
                    size="medium"
                    width="25%"
                >
                    Stage Name
                </TableCell>
                <TableCell 
                    variant="head"
                    size="medium"
                    width="60%"
                >
                    Details
                </TableCell>
            </TableRow>
        </TableHead>
    );
});
