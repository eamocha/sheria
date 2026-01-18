import React from 'react';

import './FileVersionsRow.scss';

import {
    IconButton,
    TableCell,
    TableRow
} from '@material-ui/core';

import { formatDateTime, getAdvisorUserFullName } from '../../../../APHelpers';

import { Link } from 'react-router-dom';

import DeleteIcon from '@material-ui/icons/Delete';
 
export default React.memo((props) => {
    return (
        <TableRow>
            <TableCell>
                <Link
                    href="#"
                    onClick={(e) => props?.file?.type == 'file' ? props?.downloadFile(props?.file, e) : null}
                    title={props?.file?.type == 'file' ? "Download" : ""}
                    className="primary-link"
                    color="textPrimary"
                >
                    {props?.fileName}
                </Link>
            </TableCell>
            <TableCell>
                {props?.file?.document_creator?.user_profile !== null ? getAdvisorUserFullName(props?.file?.document_creator?.user_profile) : getAdvisorUserFullName(props?.file?.document_creator)}
            </TableCell>
            <TableCell>
                {formatDateTime(new Date(props?.file?.createdOn))}
            </TableCell>
            {
                !props?.IslatestVersion ?
                (
                    <TableCell>
                        <IconButton
                            size="small"
                            title="Delete"
                            onClick={() => props.deleteFile(props?.file)}
                        >
                            <DeleteIcon />
                        </IconButton>
                    </TableCell>
                )
                :
                <TableCell />
            }
        </TableRow>
    );
});
