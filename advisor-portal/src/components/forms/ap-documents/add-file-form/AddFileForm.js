import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './AddFileForm.scss';

import { FORMS_NAMES } from './../../../../Constants';

import {
    APTextFieldInput,
    APFileInput,
    APAutocompleteList
} from './../../../common/APForm/APForm';

import {
    Context,
    initialGlobalState
} from './../../../../Store';

import {
    buildErrorMessages,
    defaultLoadList,
    isFunction
} from './../../../../APHelpers';

import Document from './../../../../api/Document';

import MiscList from '../../../../api/MiscList';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const formId = FORMS_NAMES.APDocumentAddFileForm;

    const [formData, setFormData] = useState({
        name: '',
        parent: globalState?.modal?.form?.data?.parentFolderId ?? '',
        type: 'file',
        module: globalState?.modal?.form?.data?.module ?? '',
        module_record_id: globalState?.modal?.form?.data?.module_record_id ?? '',
        comments: '',
        document_status_id: '',
        document_type_id: ''
    });

    const [listValues, listValuesDispatcher] = useState({
        document_type_id: {
            title: '',
            value: ''
        },
        document_status_id: {
            title: '',
            value: ''
        }
    });

    const [fileInput, setFileInput] = useState('');

    const [legalCaseDocumentTypesList, setLegalCaseDocumentTypesList] = useState([]);

    const [legalCaseDocumentStatusesList, setLegalCaseDocumentStatusesList] = useState([]);

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
                "legalCaseDocumentTypes",
                "legalCaseDocumentStatuses",
            ]
        }).then((response) => {
            loadLegalCaseDocumentTypesList(response?.data?.data?.legalCaseDocumentTypes);
            loadLegalCaseDocumentStatusesList(response?.data?.data?.legalCaseDocumentStatuses);
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

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
    };

    const loadLegalCaseDocumentTypesList = (data) => {
        let result = defaultLoadList(data, 'name', 'id');

        setLegalCaseDocumentTypesList(result);
    }

    const loadLegalCaseDocumentStatusesList = (data) => {
        let result = defaultLoadList(data, 'name', 'id');

        setLegalCaseDocumentStatusesList(result);
    }

    const handleObjectChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
    }

    const handleFileInputChange = (e) => {
        e.persist();

        globalStateDispatcher({
            modal: {
                ...globalState?.modal,
                showSaveButton: e?.target?.isAllowed ?? true
            }
        });

        setFileInput(e?.target?.files[0] ? e.target.files[0] : null);
    };

    const handleListChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: stateValue
        }));

        if (changeDefaultValues) {
            listValuesDispatcher(prevState => ({
                ...prevState,
                [state]: multipleSelection ? defaultValuesWithMultipleSelection : defaultValues
            }));
        }
    };

    const submit = (e) => {
        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        let requestData = prepareRequestData(formData);

        Document.create(requestData).then(response => {
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
                    text: "File has been uploaded successfully.",
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
        let formData = new FormData();

        for (let [key, value] of Object.entries(data)) {
            if (value) {
                formData.append(key, value);
            }
        }

        formData.append("file", fileInput);

        return formData;
    }

    return (
        <form
            id={formId}
            onSubmit={(e) => submit(e)}
        >
            {
                formData.module == 'case' ?
                    <React.Fragment>
                        <APAutocompleteList
                            label="Document Type"
                            options={legalCaseDocumentTypesList}
                            optionsLabel="title"
                            stateKey="document_type_id"
                            value={listValues.document_type_id}
                            valueKey="value"
                            onChange={handleListChange}
                        />
                        <APAutocompleteList
                            label="Document Status"
                            options={legalCaseDocumentStatusesList}
                            optionsLabel="title"
                            stateKey="document_status_id"
                            value={listValues.document_status_id}
                            valueKey="value"
                            onChange={handleListChange}
                        />
                    </React.Fragment>
                    :
                    null
            }
            <APFileInput
                label="Upload Document"
                name="file"
                type="file"
                required
                handleChange={handleFileInputChange}
            />
            <APTextFieldInput
                label="Keywords"
                stateKey="comments"
                rows={3}
                multiline={true}
                value={formData.comments}
                handleChange={handleObjectChange}
            />
        </form>
    );
});
