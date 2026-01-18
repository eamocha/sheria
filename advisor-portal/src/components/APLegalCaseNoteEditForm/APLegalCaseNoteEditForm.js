import React, {
    useContext,
    useState
} from 'react';
import './APLegalCaseNoteEditForm.scss';
import { FORMS_NAMES } from '../../Constants';
import {
    FormGroup,
    FormLabel
} from '@material-ui/core';
import LegalCaseComment from './../../api/LegalCaseComment';
import {
    Context,
    initialGlobalState
} from './../../Store';
import APTinyMceEditor from '../common/APTinyMceEditor/APTinyMceEditor';
import { buildErrorMessages, isFunction } from '../../APHelpers';
 
export default React.memo((props) => {
    const formId = FORMS_NAMES.legalCaseNoteEditForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [formData, setFormData] = useState({
        comment: globalState?.modal?.form?.data?.commentData?.comment ?? '',
        case_id: globalState?.modal?.form?.data?.legalCase?.id ?? ''
    });

    const handleEditorChange = (content, editor) => {
        
        setFormData(prevState => ({
            ...prevState,
            comment: content
        }));
    }

    const submit = (e) => {
        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        LegalCaseComment.update(globalState?.modal?.form?.data?.commentData?.id, formData).then((response) => {
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
                    text: "Note has be updated successfully!"
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
                    Note
                </FormLabel>
                <APTinyMceEditor
                    handleEditorChange={handleEditorChange}
                    value={formData.comment}
                />
            </FormGroup>
        </form>
    );
});
