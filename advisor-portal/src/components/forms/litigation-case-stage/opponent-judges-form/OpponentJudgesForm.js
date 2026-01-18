import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './OpponentJudgesForm.scss';

import {
    ValidatorForm
} from 'react-material-ui-form-validator';

import {
    Button,
    DialogActions,
    Container,
    createMuiTheme,
    createGenerateClassName,
    Dialog,
    MuiThemeProvider,
    DialogTitle,
    IconButton,
    DialogContent,
    Typography,
    makeStyles
} from '@material-ui/core';

import CloseIcon from '@material-ui/icons/Close';

import { JssProvider } from 'react-jss';

import APAutocompleteList from './../../../common/APForm/APAutocompleteList/APAutocompleteList';

import APTextFieldInput from './../../../common/APForm/APTextFieldInput/APTextFieldInput.lazy';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import MiscList from '../../../../api/MiscList';

import Contact from '../../../../api/Contact';
import { useTranslation } from 'react-i18next';

const useStyles = makeStyles({
    dialogContent: {
        overflowY: 'auto'
    },
    closeBtn: {
        position: 'absolute',
        right: 10,
        top: '50%',
        transform: 'translateY(-50%)'
    },
    validationMessage: {
        marginTop: 20,
        marginBottom: 20,
        color: '#c9282d'
    },
    autocompleteList: {
        width: '100%'
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

export default React.memo((props) => {
    const formId = "litigation-case-stage-opponent-judges-add-form";

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [contactsList, setContactsList] = useState([]);
    const [contactRolesList, setContactRolesList] = useState([]);

    const [showValidationMessage, setShowValidationMessage] = useState(false);

    const [formData, setFormData] = useState({
        contact: '',
        contact_name: '',
        contact_role: '',
        contact_role_name: '',
        comments: ''
    });

    const [t] = useTranslation();

    const [listValues, listValuesDispatcher] = useState({
        contact: {
            title: '',
            value: ''
        },
        contact_role: {
            title: '',
            value: ''
        }
    });

    useEffect(() => {

        loadData();
    }, []);

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        MiscList.getList({
            lists: [
                "legalCaseContactRoles"
            ]
        }).then((response) => {
            loadContactRolesList(response?.data?.data?.legalCaseContactRoles);

        }).catch((error) => {
            let message = error?.response?.data?.message;

            if (error?.response?.data?.message === 'object') {
                message = [];

                Object.keys(error.response.data.message).map((key, index) => {
                    return error.response.data.message?.[key].forEach((item) => {
                        message.push(<p key={key}>- {error.response.data.message[key]}: {item}</p>);
                    });
                });
            }

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                }
            });
        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    const loadContactRolesList = (data) => {
        let list = data;
        let options = [];

        for (var i = 0; i < list.length; i++) {
            let item = list[i];

            options.push({
                title: item.name,
                value: item.id
            });
        }

        setContactRolesList(options);
    }

    const handleJudgeContactChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues) => {
        setFormData(prevState => ({
            ...prevState,
            contact_name: defaultValues?.title,
            contact: defaultValues?.value,
        }));
    }

    const handleJudgeContactRoleChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues) => {
        setFormData(prevState => ({
            ...prevState,
            contact_role_name: defaultValues?.title,
            contact_role: defaultValues?.value,
        }));
    }

    const handleObjectChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
    }

    const getRelatedContactsList = (e) => {
        e.persist();

        let value = e?.target?.value;

        if (value.length >= 3) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Contact.getList({ name: { value: '%' + value + '%' } }).then((response) => {

                let data = response.data.data.map((item, key) => {
                    return {
                        title: item.firstName + " " + item.lastName,
                        value: item.id
                    };
                });

                setContactsList(data);
            }).catch((error) => {
                let message = error?.response?.data?.message;

                if (error?.response?.data?.message === 'object') {
                    message = [];

                    Object.keys(error.response.data.message).map((key, index) => {
                        return error.response.data.message?.[key].forEach((item) => {
                            message.push(<p key={key}>- {error.response.data.message[key]}: {item}</p>);
                        });
                    });
                }

                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: message,
                        severity: "error"
                    }
                });
            }).finally(() => {

                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            });
        }
    }

    const submit = (e) => {
        e.preventDefault();

        props.addStageOpponentJudge(e, formData);

        if (props.formValidation === true) {
            props.closeForm(false);

            // re-initiate the statuses
            setFormData(prevState => ({
                ...prevState.formData,
                contact_name: '',
                contact: '',
                contact_role: '',
                comments: ''
            }));

            listValuesDispatcher(prevState => ({
                ...prevState.defaultValues,
                contact: '',
                contact_role: ''
            }));

            setShowValidationMessage(false);
        } else {
            setShowValidationMessage(true);
        }
    }

    const classes = useStyles();

    return (
        <Container
            maxWidth="sm"
        >
            <Dialog
                open={props?.formModalState ?? false}
                onClose={props.handleFormModalClose}
                aria-labelledby="form-dialog-title"
                closeAfterTransition
                fullWidth={true}
                maxWidth={"sm"}
                disableBackdropClick
            >
                <MuiThemeProvider
                    theme={createMuiTheme(theme)}
                >
                    <JssProvider
                        generateClassName={generateClassName}
                    >
                        <DialogTitle
                            id="form-dialog-title"
                            className={classes.dialogTitle}
                        >
                            {t("add_judge")}
                            <IconButton
                                onClick={() => props.closeForm(false)}
                                classes={{ root: classes.closeBtn }}
                            >
                                <CloseIcon />
                            </IconButton>
                        </DialogTitle>
                        <DialogContent>
                            <Container
                                maxWidth="sm"
                            >
                                <ValidatorForm
                                    id={formId}
                                    onSubmit={(e) => submit(e)}
                                >
                                    <APAutocompleteList
                                        className={classes.autocompleteList}
                                        label={t("contact_name")}
                                        required
                                        textRequired={true}
                                        options={contactsList}
                                        optionsLabel="title"
                                        stateKey="contact"
                                        value={listValues.contact}
                                        valueKey="value"
                                        onChange={handleJudgeContactChange}
                                        textOnChange={getRelatedContactsList}
                                    />
                                    <APAutocompleteList
                                        className={classes.autocompleteList}
                                        label={t("role")}
                                        required
                                        textRequired={true}
                                        options={contactRolesList}
                                        optionsLabel="title"
                                        stateKey="contact_role"
                                        value={listValues.contact_role}
                                        valueKey="value"
                                        onChange={handleJudgeContactRoleChange}
                                    />
                                    <APTextFieldInput
                                        label={t("comments")}
                                        stateKey="comments"
                                        rows={5}
                                        multiline={true}
                                        value={formData.comments}
                                        handleChange={handleObjectChange}
                                    />
                                    {
                                        showValidationMessage ?
                                            (
                                                <Typography
                                                    variant="body1"
                                                    className={classes.validationMessage}
                                                >
                                                    Judge already exists!
                                                </Typography>
                                            )
                                            :
                                            null
                                    }
                                </ValidatorForm>
                            </Container>
                        </DialogContent>
                        <DialogActions
                            className={classes.dialogActions}
                        >
                            <Button
                                color="primary"
                                variant="contained"
                                type="submit"
                                form={formId}
                            >
                                {t("save")}
                            </Button>
                            <Button
                                color="secondary"
                                onClick={() => props.closeForm(false)}
                            >
                                {t("cancel")}
                            </Button>
                        </DialogActions>
                    </JssProvider>
                </MuiThemeProvider>
            </Dialog>
        </Container>
    );
});
