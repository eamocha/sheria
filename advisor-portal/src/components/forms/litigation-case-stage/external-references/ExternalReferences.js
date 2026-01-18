import React, {
    useEffect,
    useState
} from 'react';

import './ExternalReferences.scss';

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

import EditIcon from '@material-ui/icons/Edit';
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
    actionsCellHeader: {
        width: '17%',
        textAlign: 'center'
    },
    actionsCellBody: {
        display: 'flex',
    }
});

export default React.memo((props) => {
    const [isExpanded, setIsExpanded] = useState(props?.stageExternalReferences?.length > 0 || props?.isExpanded === 1);
    const [stageExternalReferences, setStageExternalReferences] = useState(props?.stageExternalReferences ?? []);

    const [t] = useTranslation();

    useEffect(() => {

        setStageExternalReferences(props?.stageExternalReferences ?? []);
    }, [props?.stageExternalReferences]);

    const openForm = () => {
        setIsExpanded(true);

        props.openForm(true);
    }

    const editItem = (e, key, item) => {
        setIsExpanded(true);

        props.handleSetEditItem(e, { key: key, ...item });
        props.openEditForm(true);
    }

    const classes = useStyles();

    let rows = stageExternalReferences.map((item, key) => {
        return (
            <TableRow
                key={key}
            >
                <TableCell>
                    {item?.number}
                </TableCell>
                <TableCell>
                    {item?.comments}
                </TableCell>
                <TableCell
                    className={classes.actionsCellBody}
                >
                    <IconButton
                        title={t("edit")}
                        size="small"
                        onClick={(e) => editItem(e, key, item)}
                    >
                        <EditIcon />
                    </IconButton>
                    <IconButton
                        title={t("delete")}
                        size="small"
                        onClick={(e) => props.removeItem(e, key)}
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
                    {isExpanded ? <ExpandLess /> : <ExpandMore />} {t("external_Court_reference_details") + ":"}
                </Button>
                <IconButton
                    onClick={() => openForm(true)}
                >
                    <AddCircleIcon />
                </IconButton>
                <Collapse
                    in={isExpanded}
                >
                    {
                        stageExternalReferences.length > 0 ?
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
                                                className={classes.numberTableCell}
                                            >
                                                {t("number")}
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
                                                className={classes.actionsCellHeader}
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
