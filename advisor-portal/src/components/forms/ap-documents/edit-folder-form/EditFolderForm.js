import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './EditFolderForm.scss';

import { FORMS_NAMES } from './../../../../Constants';

import APTextFieldInput from './../../../common/APForm/APTextFieldInput/APTextFieldInput.lazy';

import {
    Context,
    initialGlobalState
} from './../../../../Store';

import {
    buildErrorMessages,
    isFunction
} from './../../../../APHelpers';

import Document from './../../../../api/Document';
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const formId = FORMS_NAMES.APDocumentEditFolderForm;

    const [folderId, ] = useState(globalState?.modal?.form?.data?.id ?? 0);

    const [formData, setFormData] = useState({
        name: globalState?.modal?.form?.data?.name ?? '',
        parent: globalState?.modal?.form?.data?.parentFolderId ?? '',
        type: 'folder',
        module: globalState?.modal?.form?.data?.module ?? '',
        module_record_id: globalState?.modal?.form?.data?.module_record_id ?? ''
    });

    const handleObjectChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
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
        
        let requestData = prepareRequestData(formData);

        Document.update(folderId, requestData).then(response => {
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
                    text: "Folder has been updated successfully.",
                    severity: "success"
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
                    globalLoader: initialGlobalState?.globalLoader
                });
            }
        });
    }

    const prepareRequestData = (data) => {
        let result = {};

        for (let [key, value] of Object.entries(data)) {
            if (value) {
                result[key] = value;
            }
        }

        return result;
    }

    return (
        <form
            id={formId}
            onSubmit={(e) => submit(e)}
        >
            <APTextFieldInput
                label="Folder Name"
                stateKey="name"
                value={formData.name}
                required
                handleChange={handleObjectChange}
            />
        </form>
    );
});
