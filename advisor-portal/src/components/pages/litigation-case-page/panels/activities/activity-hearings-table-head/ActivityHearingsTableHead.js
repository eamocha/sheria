import React from 'react';

import './ActivityHearingsTableHead.scss';

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
                    size="medium"
                    width="9%"
                >
                    {t("date")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="9%"
                >
                    {t("postpone_until")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="10%"
                >
                    {t("reasons_of_postponement")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="8%"
                >
                    {t("type")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="10%"
                >
                    {t("assignee_s")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="10%"
                >
                    {t("summary")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="11%"
                >
                    {t("judgment")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="14%"
                >
                    {t("comments")}
                </TableCell>
                <TableCell
                    variant="head"
                    size="small"
                    width="9%"
                >
                    {t("documents")}
                </TableCell>
            </TableRow>
        </TableHead>
    );
});
