import React, {
    useContext,
    useState
} from 'react';
import './APLegalCaseNoteAddForm.scss';
import {
    FormGroup,
    FormLabel
} from '@material-ui/core';
import LegalCaseComment from './../../api/LegalCaseComment';
import {
    Context,
    initialGlobalState
} from './../../Store';
import { FORMS_NAMES } from './../../Constants';
import APTinyMceEditor from '../common/APTinyMceEditor/APTinyMceEditor';
import {
    buildErrorMessages,
    isFunction
} from '../../APHelpers';
import APMultiFileUploadInput from '../common/APForm/APMultiFileUploadInput/APMultiFileUploadInput';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const formId = FORMS_NAMES.legalCaseNoteAddForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [formData, setFormData] = useState({
        comment: '',
        case_id: globalState?.modal?.form?.data?.legalCase?.id ?? '',
        files: []
    });

    const { t } = useTranslation();
    
    const handleEditorChange = (content, editor) => {

        setFormData(prevState => ({
            ...prevState,
            comment: content
        }));
    }

    const prepareRequestData = (data) => {
        let formData = new FormData();

        for (let [key, value] of Object.entries(data)) {

            if (value && key === "files") {
                for (var i = 0; i < value.length; i++) {
                    formData.append("files[]", value[i]?.value);
                }
            } else {
                formData.append(key, value);
            }
        }

        return formData;
    }


    const submit = (e) => {
        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        let data = prepareRequestData(formData)

        LegalCaseComment.create(data).then((response) => {
            if (isFunction(globalState?.modal?.form?.submitCallback)) {
                globalState.modal.form.submitCallback();
            }

            globalStateDispatcher({
                modal: initialGlobalState?.modal
            });

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    severity: "success",
                    text: "Note has be added successfully!"
                }
            });
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                },
                globalLoader: initialGlobalState?.globalLoader
            });
        }).finally(() => {
            if (!isFunction(globalState?.modal?.form?.submitCallback)) {
                globalStateDispatcher({
                    globalLoader: globalState?.globalLoader
                });
            }
        });
    }

    const handleFilesChange = (files) => {
        setFormData(prevState => ({
            ...prevState,
            files: files
        }));
    }

    return (
        <form
            id={formId}
            onSubmit={(e) => submit(e)}
        >
            <FormGroup>
                <FormLabel
                    style={{
                        marginBottom: 20
                    }}
                >
                    {t("note")}
                </FormLabel>
                <APTinyMceEditor
                    handleEditorChange={handleEditorChange}
                    value={formData.comment}
                />
                <APMultiFileUploadInput
                    handleFilesChange={handleFilesChange}
                />
            </FormGroup>
        </form>
    );
});
