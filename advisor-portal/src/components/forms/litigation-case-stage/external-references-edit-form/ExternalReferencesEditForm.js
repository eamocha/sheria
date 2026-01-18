import React, {
    useEffect,
    useState
} from 'react';
import './ExternalReferencesEditForm.scss';
import { ValidatorForm } from 'react-material-ui-form-validator';
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
    makeStyles
} from '@material-ui/core';
import CloseIcon from '@material-ui/icons/Close';
import { JssProvider } from 'react-jss';
import { isValid } from 'date-fns';
import APTextFieldInput from '../../../common/APForm/APTextFieldInput/APTextFieldInput.lazy';
import APDatePicker from '../../../common/APForm/APDatePicker/APDatePicker.lazy';
import { formatDate } from '../../../../APHelpers';
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
    const formId = "litigation-case-stage-external-reference-edit-form";

    const [formData, setFormData] = useState({
        key: props?.item?.key,
        number: props?.item?.number,
        refDate: props?.item?.refDate,
        comments: props?.item?.comments
    });

    const [t] = useTranslation();
    useEffect(() => {

        setFormData({
            key: props?.item?.key,
            number: props?.item?.number,
            refDate: props?.item?.refDate,
            comments: props?.item?.comments
        });
    }, [props?.item]);

    const handleDatePickerChange = (state, date, time = false) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: date === null ? null : time ? (isValid(date) ? date : null) : formatDate(date)
        }));
    }

    const handleObjectChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
    }

    const submit = (e) => {
        e.preventDefault();

        props.handleEditItem(e, formData);

        props.closeForm(false);

        // re-initiate the state
        setFormData(prevState => ({
            ...prevState.formData,
            number: '',
            refDate: null,
            comments: ''
        }));
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
                            Edit External/Court Reference
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
                                    <APTextFieldInput
                                        label={t("number")}
                                        stateKey="number"
                                        required
                                        value={props.item.number}
                                        handleChange={handleObjectChange}
                                    />
                                    <APDatePicker
                                        label={t("date")}
                                        stateKey="refDate"
                                        format="yyyy-MM-dd"
                                        value={props.item.refDate}
                                        handleChange={handleDatePickerChange}
                                    />
                                    <APTextFieldInput
                                        label={t("comments")}
                                        stateKey="comments"
                                        rows={5}
                                        multiline={true}
                                        value={props.item.comments}
                                        handleChange={handleObjectChange}
                                    />
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
