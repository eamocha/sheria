import React, {
    useEffect,
    useState
} from 'react';

import './OpponentLawyers.scss';

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

import { OpponentLawyersTableRow } from './../LitigationCaseStageForms';
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
    actionsCellBody: {
        width: '17%',
        textAlign: 'center'
    }
});

export default React.memo((props) => {
    const [isExpanded, setIsExpanded] = useState(props?.stageOpponentLawyers?.length > 0 || props?.isExpanded === 1);

    const [stageOpponentLawyers, setStageOpponentLawyers] = useState(props?.stageOpponentLawyers ?? []);

    const [t] = useTranslation();

    useEffect(() => {

        setStageOpponentLawyers(props?.stageOpponentLawyers ?? []);
    }, [props?.stageOpponentLawyers]);

    const openForm = () => {

        setIsExpanded(true);

        props.openForm(true);
    }

    const editItem = (e, item) => {
        setIsExpanded(true);

        props.setEditItem(item);

        props.openEditForm(true);
    }

    const classes = useStyles();

    let rows = stageOpponentLawyers.map((item, key) => {

        return (
            <OpponentLawyersTableRow
                index={key}
                item={{ key: key, ...item }}
                key={"litigation-case-stage-change-form-opponent-lawyers-row-" + key}
                contact={item?.contact}
                contactName={item?.contact_name}
                comments={item?.comments}
                removeOpponentLawyer={props.removeOpponentLawyer}
                editItem={editItem}
            />
        )
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
                    {isExpanded ? <ExpandLess /> : <ExpandMore />} {t("opponent_lawyers")}
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
                        stageOpponentLawyers.length > 0 ?
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
                                                className={classes.actionsCellBody}
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
