import React from 'react';
import {
    Dialog,
    useMediaQuery,
    useTheme,
    createGenerateClassName,
    makeStyles,
    FormGroup,
    TableContainer,
    TableCell,
    TableBody,
    MuiThemeProvider,
    DialogTitle,
    Container,
    createMuiTheme,
    TableRow,
    TableHead,
    Button,
    DialogActions,
    DialogContent,
    IconButton,
    Radio,
    Table,
    Grid,
    Typography
} from '@material-ui/core';
import { JssProvider } from 'react-jss';
import { ValidatorForm } from 'react-material-ui-form-validator';
import CloseIcon from '@material-ui/icons/Close';
import { getValueFromLanguage } from '../../../../APHelpers';
import { getActiveLanguageId } from '../../../../i18n';
import { useTranslation } from 'react-i18next';

const useStyles = makeStyles({
    dialogPaper: {
        maxWidth: '900px'
    },
    dialogContent: {
        overflowY: 'auto'
    },
    closeBtn: {
        position: 'absolute',
        right: 10,
        top: '50%',
        transform: 'translateY(-50%)'
    },
    formContainer: {
        paddingLeft: 0,
        paddingRight: 0,
        maxHeight: '500px'
    },
    formGroup: {
        marginBottom: 0
    },
    detailsContainer: {
        border: '1px solid #ccc',
        padding: 0
    },
    detailsRowContainer: {
        marginBottom: '15px',
        borderBottom: '1px solid #ccc',
        padding: '7px'
    },
    detailsLastRowContainer: {
        padding: '5px'
    }
});

const theme = {
    overrides: {
        MuiFormGroup: {
            root: {
                marginBottom: 15
            }
        },
        MuiDialogTitle: {
            root: {
                background: '#f5f5f5',
                borderBottom: '1px solid #c8c8c8',
                position: 'relative'
            }
        },
        MuiDialogContent: {
            root: {
                paddingTop: 20
            }
        },
        MuiDialogActions: {
            root: {
                background: '#f5f5f5',
                borderTop: '1px solid #c8c8c8'
            }
        }
    }
};

const generateClassName = createGenerateClassName({
    dangerouslyUseGlobalCSS: true
});

const ChooseCaseLitigationDetailModal = (props) => {
    const themeObj = useTheme();
    const fullScreen = useMediaQuery(themeObj.breakpoints.down('md'));
    const classes = useStyles();

    const [t] = useTranslation();

    const handleChange = (e, stage) => {
        props.handleChange(e, stage);
    };

    // const handleSubmit = (e) => {
    //     e.preventDefault();

    //     props.setModalState(false);
    // };

    const propIsNotEmpty = (property) => {
        return (property == null || property == "") ? false : true;
    }

    let rows = props.data.map((item, key) => {
        let stageName = '';

        if (propIsNotEmpty(item.stage_name) && propIsNotEmpty(item.stage_name.stage_name_languages)) {
            stageName = getValueFromLanguage(item?.stage_name, 'stage_name_languages', getActiveLanguageId(), '');
        }

        let status = '';

        if (propIsNotEmpty(item.stage_status) && propIsNotEmpty(item.stage_status.stage_status_languages)) {
            status = getValueFromLanguage(item?.stage_status, 'stage_name_languages', getActiveLanguageId(), '');
        }

        let client = '';

        if (propIsNotEmpty(item.legal_case) && propIsNotEmpty(item.legal_case.client)) {
            let legalCaseClient = item.legal_case.client;

            if (propIsNotEmpty(legalCaseClient.company)) {
                client = legalCaseClient.company.name;
            } else if (propIsNotEmpty(legalCaseClient.contact)) {
                client = legalCaseClient.contact.firstName + " " + legalCaseClient.contact.lastName;
            }
        }

        let clientPosition = '';

        if (propIsNotEmpty(item.stage_client_position) && propIsNotEmpty(item.stage_client_position.client_position_languages)) {
            for (var i = 0; i < item.stage_client_position.client_position_languages.length; i++) {
                let language = item.stage_client_position.client_position_languages[i];

                if (language.language_id === 1) {
                    clientPosition = language.name;
                    break;
                }
            }
        }

        let courtType = '';

        if (propIsNotEmpty(item.stage_court_type)) {
            courtType = item.stage_court_type.name;
        }

        let courtDegree = '';

        if (propIsNotEmpty(item.stage_court_degree)) {
            courtDegree = item.stage_court_degree.name;
        }

        let courtRegion = '';

        if (propIsNotEmpty(item.stage_court_region)) {
            courtRegion = item.stage_court_region.name;
        }

        let court = '';

        if (propIsNotEmpty(item.stage_court)) {
            court = item.stage_court.name;
        }

        let externalReferences = [];

        if (propIsNotEmpty(item.stage_external_references)) {
            for (var i = 0; i < item.stage_external_references.length; i++) {
                let reference = item.stage_external_references[i];

                externalReferences.push(reference.number);
            }
        }

        return (
            <TableRow key={"case-litigation-detail-history-" + key}>
                <TableCell>
                    <FormGroup className={classes.formGroup}>
                        <Radio
                            checked={props.stage == item.id}
                            onChange={(e) => handleChange(e, item)}
                            color="primary"
                            value={item}
                            name="case-litigation-detail-history"
                        />
                    </FormGroup>
                </TableCell>
                <TableCell>
                    {stageName}
                </TableCell>
                <TableCell>
                    <Container className={classes.detailsContainer}>
                        <Grid container className={classes.detailsRowContainer}>
                            <Grid item sm={6}>
                                <Typography variant="body1">{t("status")}</Typography>
                            </Grid>
                            <Grid item sm={6}>
                                <Typography variant="body1">
                                    {status}
                                </Typography>
                            </Grid>
                        </Grid>
                        <Grid container className={classes.detailsRowContainer}>
                            <Grid item sm={6}>
                                <Typography variant="body1">{t("client")}</Typography>
                            </Grid>
                            <Grid item sm={6}>
                                <Typography variant="body1">
                                    {client}
                                </Typography>
                            </Grid>
                        </Grid>
                        <Grid container className={classes.detailsRowContainer}>
                            <Grid item sm={6}>
                                <Typography variant="body1">{t("client_position")}</Typography>
                            </Grid>
                            <Grid item sm={6}>
                                <Typography variant="body1">
                                    {clientPosition}
                                </Typography>
                            </Grid>
                        </Grid>
                        <Grid container className={classes.detailsRowContainer}>
                            <Grid item sm={6}>
                                <Typography variant="body1">{t("court_type")}</Typography>
                            </Grid>
                            <Grid item sm={6}>
                                <Typography variant="body1">
                                    {courtType}
                                </Typography>
                            </Grid>
                        </Grid>
                        <Grid container className={classes.detailsRowContainer}>
                            <Grid item sm={6}>
                                <Typography variant="body1">{t("court_degree")}</Typography>
                            </Grid>
                            <Grid item sm={6}>
                                <Typography variant="body1">
                                    {courtDegree}
                                </Typography>
                            </Grid>
                        </Grid>
                        <Grid container className={classes.detailsRowContainer}>
                            <Grid item sm={6}>
                                <Typography variant="body1">{t("court_region")}</Typography>
                            </Grid>
                            <Grid item sm={6}>
                                <Typography variant="body1">
                                    {courtRegion}
                                </Typography>
                            </Grid>
                        </Grid>
                        <Grid container className={classes.detailsRowContainer}>
                            <Grid item sm={6}>
                                <Typography variant="body1">{t("court")}</Typography>
                            </Grid>
                            <Grid item sm={6}>
                                <Typography variant="body1">
                                    {court}
                                </Typography>
                            </Grid>
                        </Grid>
                        <Grid container className={classes.detailsLastRowContainer}>
                            <Grid item sm={6}>
                                <Typography variant="body1">{t("external_court_reference")}</Typography>
                            </Grid>
                            <Grid item sm={6}>
                                <Typography variant="body1">
                                    {externalReferences.join()}
                                </Typography>
                            </Grid>
                        </Grid>
                    </Container>
                </TableCell>
            </TableRow>
        );
    });

    return (
        <Dialog
            open={props.modalState}
            onClose={props.handleModalClose}
            aria-labelledby="form-dialog-title"
            fullScreen={fullScreen}
            closeAfterTransition
            fullWidth={true}
            maxWidth={"md"}
            disableBackdropClick
            classes={{ paper: classes.dialogPaper }}
        >
            <MuiThemeProvider theme={createMuiTheme(theme)}>
                <JssProvider generateClassName={generateClassName}>
                    <DialogTitle
                        id="form-dialog-title"
                        className={classes.dialogTitle}
                    >
                        {t("stage_histories")}
                        <IconButton
                            onClick={() => props.setModalState(false)}
                            classes={{ root: classes.closeBtn }}
                        >
                            <CloseIcon />
                        </IconButton>
                    </DialogTitle>
                    <DialogContent>
                        <Container maxWidth={false} className={classes.formContainer}>
                            {/* <ValidatorForm id="case-litigation-details-histories-form" onSubmit={(e) => handleSubmit(e)}> */}
                            <TableContainer>
                                <Table>
                                    <TableHead>
                                        <TableRow>
                                            <TableCell variant="head" size="small" width="15%">{t("select")}</TableCell>
                                            <TableCell variant="head" size="medium" width="25%">{t("stage_name")}</TableCell>
                                            <TableCell variant="head" size="medium" width="60%">{t("details")}</TableCell>
                                        </TableRow>
                                    </TableHead>
                                    <TableBody>
                                        {rows}
                                    </TableBody>
                                </Table>
                            </TableContainer>
                            {/* </ValidatorForm> */}
                        </Container>
                    </DialogContent>
                    <DialogActions
                        className={classes.dialogActions}
                    >
                        <Button color="primary" variant="contained" onClick={() => props.setModalState(false)} form="case-litigation-details-histories-form">{t("save")}</Button>
                        <Button color="secondary" onClick={() => props.setModalState(false)}>{t("cancel")}</Button>
                    </DialogActions>
                </JssProvider>
            </MuiThemeProvider>
        </Dialog>
    );
};

export default ChooseCaseLitigationDetailModal;
