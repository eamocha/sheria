import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './APModalForm.scss';
import {
    Dialog,
    useMediaQuery,
    useTheme,
    DialogTitle,
    IconButton,
    DialogContent,
    Container,
    createGenerateClassName,
    makeStyles,
    DialogActions,
    Button
} from '@material-ui/core';
import { JssProvider } from 'react-jss';
import CloseIcon from '@material-ui/icons/Close';
import {
    Context,
    initialGlobalState
} from '../../../Store';
import { FORMS_NAMES } from '../../../Constants';

import {
    AddForm as AdvisorTimeLogAddForm,
    EditForm as AdvisorTimeLogEditForm,
    ImportForm as AdvisorTimeLogImportForm
} from '../../forms/time-log/AdvisorTimeLogForms';

import {
    AddForm as TimerAddForm,
    EditForm as TimerEditForm,
    ActiveTimersForm
} from '../../forms/timer/TimerForms';

import AdvisorTaskAddForm from '../../forms/advisor-task/add-form/AdvisorTaskAddForm.lazy';
import LitigationCaseEditForm from '../../LitigationCase/LitigationCaseEditForm/LitigationCaseEditForm.lazy';
import APLegalCaseNoteAddForm from '../../APLegalCaseNoteAddForm/APLegalCaseNoteAddForm.lazy';
import APLegalCaseNoteEditForm from '../../APLegalCaseNoteEditForm/APLegalCaseNoteEditForm.lazy';
// import LitigationCaseStageEditForm from '../../LitigationCase/LitigationCasePageStages/LitigationCaseStageEditForm/LitigationCaseStageEditForm.lazy';
import {
    ChangeForm as LitigationCaseStageChangeForm,
    EditForm as LitigationCaseStageEditForm
} from '../../forms/litigation-case-stage/LitigationCaseStageForms';

import { EditForm as CorporateMatterEditForm } from '../../forms/corporate-matter/CorporateMatterForms';

import AdvisorTaskNoteAddForm from '../../forms/advisor-task/note-add-form/AdvisorTaskNoteAddForm.lazy';

import AdvisorTaskNoteEditForm from '../../forms/advisor-task/note-edit-form/AdvisorTaskNoteEditForm.lazy';

import AdvisorTaskEditForm from '../../forms/advisor-task/edit-form/AdvisorTaskEditForm.lazy';

import {
    AddFolderForm as APDocumentAddFolderForm,
    EditFolderForm as APDocumentEditFolderForm,
    AddFileForm as APDocumentAddFileForm,
    EditFileForm as APDocumentEditFileForm,
} from '../../forms/ap-documents/APDocumentsForms';

import {
    AddForm as HearingAddForm,
    EditForm as HearingEditForm,
    JudgementForm as HearingJudgementForm
} from './../../forms/hearing/HearingForms';
import { useTranslation } from 'react-i18next';
import { isFunction } from '../../../APHelpers';

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

const generateClassName = createGenerateClassName({
    dangerouslyUseGlobalCSS: true
});

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [formId, setFormId] = useState(globalState?.modal?.form?.id);
    const [activeForm, setActiveForm] = useState('');

    const { t } = useTranslation();

    useEffect(() => {

        setActiveFormHandler();
        setFormId(globalState?.modal?.form?.id);
    }, [globalState?.modal?.form]);

    const setActiveFormHandler = () => {
        switch (globalState?.modal?.form?.id) {
            case FORMS_NAMES.advisorTimeLogAddForm:
                setActiveForm(
                    <AdvisorTimeLogAddForm
                        data={globalState?.modal?.form?.data}
                    />
                );
                break;

            case FORMS_NAMES.advisorTimeLogEditForm:
                setActiveForm(
                    <AdvisorTimeLogEditForm />
                );
                break;

            case FORMS_NAMES.advisorTaskAddForm:
                setActiveForm(
                    <AdvisorTaskAddForm
                        data={globalState?.modal?.form?.data}
                    />
                );
                break;

            case FORMS_NAMES.advisorTaskEditForm:
                setActiveForm(
                    <AdvisorTaskEditForm
                        data={globalState?.modal?.form?.data}
                    />
                );
                break;

            case FORMS_NAMES.AdvisorTaskNoteAddForm:
                setActiveForm(
                    <AdvisorTaskNoteAddForm />
                );
                break;

            case FORMS_NAMES.AdvisorTaskNoteEditForm:
                setActiveForm(
                    <AdvisorTaskNoteEditForm />
                );
                break;

            case FORMS_NAMES.litigationCaseEditForm:
                setActiveForm(
                    <LitigationCaseEditForm
                        data={globalState?.modal?.form?.data}
                    />
                );
                break;

            case FORMS_NAMES.legalCaseNoteAddForm:
                setActiveForm(
                    <APLegalCaseNoteAddForm
                        data={globalState?.modal?.form?.data}
                    />
                );
                break;

            case FORMS_NAMES.legalCaseNoteEditForm:
                setActiveForm(
                    <APLegalCaseNoteEditForm
                        data={globalState?.modal?.form?.data}
                    />
                );
                break;

            case FORMS_NAMES.litigationCaseStageEditForm:
                setActiveForm(
                    <LitigationCaseStageEditForm
                        data={globalState?.modal?.form?.data}
                    />
                );
                break;

            case FORMS_NAMES.litigationCaseStageChangeForm:
                setActiveForm(
                    <LitigationCaseStageChangeForm />
                );
                break;

            case FORMS_NAMES.corporateMatterEditForm:
                setActiveForm(
                    <CorporateMatterEditForm />
                );
                break;

            case FORMS_NAMES.APDocumentAddFolderForm:
                setActiveForm(
                    <APDocumentAddFolderForm />
                );
                break;

            case FORMS_NAMES.APDocumentEditFolderForm:
                setActiveForm(
                    <APDocumentEditFolderForm />
                );
                break;

            case FORMS_NAMES.APDocumentAddFileForm:
                setActiveForm(
                    <APDocumentAddFileForm />
                );
                break;

            case FORMS_NAMES.APDocumentEditFileForm:
                setActiveForm(
                    <APDocumentEditFileForm />
                );
                break;

            case FORMS_NAMES.hearingAddForm:
                setActiveForm(
                    <HearingAddForm />
                );
                break;


            case FORMS_NAMES.hearingEditForm:
                setActiveForm(
                    <HearingEditForm />
                );
                break;

            case FORMS_NAMES.hearingJudgementForm:
                setActiveForm(
                    <HearingJudgementForm />
                );
                break;

            case FORMS_NAMES.advisorTimeLogImportForm:
                setActiveForm(
                    <AdvisorTimeLogImportForm />
                );
                break;

            case FORMS_NAMES.timerAddform:
                setActiveForm(
                    <TimerAddForm />
                );
                break;

            case FORMS_NAMES.timerEditform:
                setActiveForm(
                    <TimerEditForm />
                );
                break;

            case FORMS_NAMES.activeTimersform:
                setActiveForm(
                    <ActiveTimersForm />
                );
                break;

            default:
                break;
        }
    };

    const closeModal = () => {
        globalStateDispatcher({
            modal: initialGlobalState?.modal
        });

        if (isFunction(globalState?.modal?.form?.closeCallback)) {
            globalState.modal.form.closeCallback();
        }
    };

    const themeObj = useTheme();
    const fullScreen = useMediaQuery(themeObj.breakpoints.down('sm'));
    const classes = useStyles();

    return (
        <Dialog
            open={globalState?.modal?.open}
            onClose={() => closeModal()}
            aria-labelledby="form-dialog-title"
            fullScreen={fullScreen}
            closeAfterTransition
            fullWidth={true}
            maxWidth={"md"}
            disableBackdropClick
            className="APModalForm"
        >
            <JssProvider
                generateClassName={generateClassName}
            >
                <DialogTitle
                    id="form-dialog-title"
                    className={classes.dialogTitle}
                >
                    {globalState?.modal?.title}
                    <IconButton
                        onClick={() => closeModal()}
                        classes={{ root: classes.closeBtn + " " + globalState.domDirection }}
                    >
                        <CloseIcon />
                    </IconButton>
                </DialogTitle>
                <DialogContent>
                    <Container
                        maxWidth="sm"
                    >
                        {activeForm}
                    </Container>
                </DialogContent>
                <DialogActions
                    className={classes.dialogActions}
                >
                    {
                        globalState?.modal?.showSaveButton ?
                            <Button
                                color="primary"
                                variant="contained"
                                type="submit"
                                form={formId}
                            >
                                {t('save')}
                            </Button>
                            : null
                    }

                    <Button
                        color="secondary"
                        onClick={() => closeModal()}
                    >
                        {t("cancel")}
                    </Button>
                </DialogActions>
            </JssProvider>
        </Dialog>
    );
});
