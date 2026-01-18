import React from 'react';

import './OpponentLawyersTableRow.scss';

import {
    TableRow,
    TableCell,
    IconButton,
} from '@material-ui/core';

import DeleteIcon from '@material-ui/icons/Delete';

import EditIcon from '@material-ui/icons/Edit';
 
export default React.memo((props) => {

    return (
        <TableRow>
            <TableCell>
                {props?.contactName}
            </TableCell>
            <TableCell>
                {props?.comments}
            </TableCell>
            <TableCell
                style={{display: 'flex'}}
            >
                <IconButton
                    title="Edit"
                    size="small"
                    onClick={(e) => props.editItem(e, props?.item)}
                >
                    <EditIcon />
                </IconButton>
                <IconButton
                    title="Delete"
                    size="small"
                    onClick={(e) => props.removeOpponentLawyer(e, props?.index)}
                >
                    <DeleteIcon />
                </IconButton>
            </TableCell>
        </TableRow>
    );
});
