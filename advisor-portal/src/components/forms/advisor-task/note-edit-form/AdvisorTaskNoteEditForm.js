import React, {
    useContext,
    useState
} from 'react';

import './AdvisorTaskNoteEditForm.scss';

import { FORMS_NAMES } from './../../../../Constants';

import {
    FormGroup,
    FormLabel
} from '@material-ui/core';

import AdvisorTaskComment from './../../../../api/AdvisorTaskComment';

import {
    Context,
    initialGlobalState
} from './../../../../Store';

import APTinyMceEditor from './../../../common/APTinyMceEditor/APTinyMceEditor.lazy';

import { isFunction } from './../../../../APHelpers';
 
export default React.memo((props) => {
    const formId = FORMS_NAMES.AdvisorTaskNoteEditForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [formData, setFormData] = useState({
        comment: globalState?.modal?.form?.data?.commentData?.comment ?? '',
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

        AdvisorTaskComment.update(globalState?.modal?.form?.data?.commentData?.id, formData).then((response) => {
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
