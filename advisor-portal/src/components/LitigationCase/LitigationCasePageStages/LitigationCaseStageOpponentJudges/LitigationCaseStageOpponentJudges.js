import React, { useState } from 'react';
import './LitigationCaseStageOpponentJudges.scss';
import {
    Container,
    Button,
    Collapse,
    Table,
    TableContainer,
    TableHead,
    TableRow,
    TableCell,
    Paper,
    TableBody,
    IconButton,
    makeStyles
} from '@material-ui/core';
import {
    ExpandLess,
    ExpandMore
} from '@material-ui/icons';
import AddCircleIcon from '@material-ui/icons/AddCircle';
import DeleteIcon from '@material-ui/icons/Delete';
import { useTranslation } from 'react-i18next';

const useStyles = makeStyles({
    container: {
        paddingLeft: 0,
        paddingRight: 0
    },
    tableContainer: {
        marginTop: 20,
        marginBottom: 30
    },
    collapseBtn: {
        fontWeight: 'bold',
        paddingLeft: 0
    },
    numberTableCell: {
        width: '33%'
    },
    commentsTableCell: {
        width: '50%'
    },
    actionsTableCell: {
        width: '17%'
    }
});

export default React.memo((props) => {
    const [isExpanded, setIsExpanded] = useState(props?.stageJudges?.length > 0);
    const [stageJudges, setStageJudges] = useState(props?.stageJudges ?? []);

    const [t] = useTranslation();

    const openForm = (e) => {

        setIsExpanded(true);

        props.openForm(true);
    }

    const classes = useStyles();

    let rows = stageJudges.map((item, key) => {
        return (
            <TableRow key={key}>
                <TableCell>
                    {item.contactName}
                </TableCell>
                <TableCell>
                    {item.comments}
                </TableCell>
                <TableCell>
                    <IconButton
                    // onClick={(e) => props.removeItem(e, key)}
                    >
                        <DeleteIcon />
                    </IconButton>
                </TableCell>
            </TableRow>
        );
    });

    return (
        <React.Fragment>
            <Container
                className={classes.container}
            >
                <Button
                    color="primary"
                    onClick={() => setIsExpanded(prevState => !prevState)}
                    className={classes.collapseBtn}
                >
                    {isExpanded ? <ExpandLess /> : <ExpandMore />} {t("judge")}
                </Button>
                <IconButton
                    onClick={() => openForm()}
                >
                    <AddCircleIcon />
                </IconButton>
                <Collapse
                    in={isExpanded}
                >
                    {
                        stageJudges.length > 0 ?
                            <TableContainer
                                component={Paper}
                                className={classes.tableContainer}
                            >
                                <Table
                                    size="small"
                                >
                                    <TableHead>
                                        <TableRow>
                                            <TableCell
                                                variant="head"
                                                size="medium"
                                                className={classes.nameTableCell}
                                            >
                                                {t("name")}
                                            </TableCell>
                                            <TableCell
                                                variant="head"
                                                size="medium"
                                                className={classes.commentsTableCell}
                                            >
                                                {t("comments")}
                                            </TableCell>
                                            <TableCell
                                                variant="head"
                                                size="small"
                                                className={classes.actionsTableCell}
                                            >
                                                {t("actions")}
                                            </TableCell>
                                        </TableRow>
                                    </TableHead>
                                    <TableBody>
                                        {rows}
                                    </TableBody>
                                </Table>
                            </TableContainer>
                            :
                            <p className="text-center">{t("no_data")}</p>
                    }
                </Collapse>
            </Container>
        </React.Fragment>
    );
});
