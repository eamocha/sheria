import React, { useState } from 'react';
import './LitigationCaseStageExternalReferenceAddForm.scss';
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
    const formId = "litigation-case-stage-external-reference-add-form";

    const [formModalState, setFormModalState] = useState(props?.formModalState ?? false);

    const [formData, setFormData] = useState({
        number: '',
        refDate: null,
        comments: ''
    });

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

        props.addStageExternalReference(e, formData);
        
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
                            Add External/Court Reference
                            <IconButton
                                onClick={() => props.closeForm(false)}
                                classes={{root: classes.closeBtn}}
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
                                        label="Number"
                                        stateKey="number"
                                        required
                                        value={formData.number}
                                        handleChange={handleObjectChange}
                                    />
                                    <APDatePicker
                                        label="Date"
                                        stateKey="refDate"
                                        format="yyyy-MM-dd"
                                        value={formData.refDate}
                                        handleChange={handleDatePickerChange}
                                    />
                                    <APTextFieldInput
                                        label="Comments"
                                        stateKey="comments"
                                        rows={5}
                                        multiline={true}
                                        value={formData.comments}
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
                                Save
                            </Button>
                            <Button
                                color="secondary"
                                onClick={() => props.closeForm(false)}
                            >
                                Cancel
                            </Button>
                        </DialogActions>
                    </JssProvider>
                </MuiThemeProvider>
            </Dialog>
        </Container>
    );
});
