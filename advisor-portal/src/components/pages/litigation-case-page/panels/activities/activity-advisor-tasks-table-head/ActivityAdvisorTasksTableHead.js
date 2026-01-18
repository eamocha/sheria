import React from 'react';

import './ActivityAdvisorTasksTableHead.scss';

import {
    TableHead,
    TableRow,
    TableCell
} from '@material-ui/core';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const { t } = useTranslation();
    return (
        <TableHead>
            <TableRow>
                <TableCell
                    variant="head"
                    size="medium"
                    width="4%"
                />
                <TableCell
                    variant="head"
                    size="small"
                    width="4%"
                >
                    {t("id")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="medium"
                    width="25%"
                >
                    {t("description")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="13%"
                >
                    {t("status")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="13%"
                >
                    {t("type")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="14%"
                >
                    {t("due_date")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="13%"
                >
                    {t("priority")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="14%"
                >
                   {t("assignee")} 
                </TableCell>
                {/* <TableCell
                    variant="head"
                    size="small"
                    width="14%"
                >
                    Documents
                </TableCell> */}
            </TableRow>
        </TableHead>
    );
});
