import React, {
    useContext,
    useState
} from 'react';

import './AdvisorTaskNoteAddForm.scss';

import {
    FormGroup,
    FormLabel
} from '@material-ui/core';

import {
    Context,
    initialGlobalState
} from './../../../../Store';

import { FORMS_NAMES } from './../../../../Constants';

import APTinyMceEditor from './../../../common/APTinyMceEditor/APTinyMceEditor.lazy';

import { buildErrorMessages, isFunction } from './../../../../APHelpers';

import AdvisorTaskComment from './../../../../api/AdvisorTaskComment';
 
export default React.memo((props) => {
    const formId = FORMS_NAMES.AdvisorTaskNoteAddForm;

    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const [formData, setFormData] = useState({
        comment: '',
        advisor_task_id: globalState?.modal?.form?.data?.advisorTask?.id ?? ''
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

        AdvisorTaskComment.create(formData).then((response) => {
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
