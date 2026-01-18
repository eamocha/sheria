import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './EditFileForm.scss';

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

    const formId = FORMS_NAMES.APDocumentEditFileForm;

    const [formData, setFormData] = useState({
        name: globalState?.modal?.form?.data?.file?.name ?? '',
        parent: globalState?.modal?.form?.data?.file?.parentFolderId ?? '',
        type: 'file',
        module: globalState?.modal?.form?.data?.file?.module ?? '',
        module_record_id: globalState?.modal?.form?.data?.file?.module_record_id ?? '',
        comment: globalState?.modal?.form?.data?.file?.comment ?? '',
        document_status_id: globalState?.modal?.form?.data?.file?.document_status?.id ?? '',
        document_type_id: globalState?.modal?.form?.data?.file?.document_type?.id ?? ''
    });

    const [listValues, listValuesDispatcher] = useState({
        document_type_id: {
            title: globalState?.modal?.form?.data?.file?.legal_case_document_type?.name ?? '',
            value: globalState?.modal?.form?.data?.file?.legal_case_document_type?.id ?? ''
        },
        document_status_id: {
            title: globalState?.modal?.form?.data?.file?.legal_case_document_status?.name ?? '',
            value: globalState?.modal?.form?.data?.file?.legal_case_document_status?.id ?? ''
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

        Document.update(globalState?.modal?.form?.data?.file?.id ?? '', requestData).then(response => {
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
                label="Name"
                stateKey="name"
                value={formData.name}
                handleChange={handleObjectChange}
            />
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
            <APTextFieldInput
                label="Keywords"
                stateKey="comment"
                rows={3}
                multiline={true}
                value={formData.comment}
                handleChange={handleObjectChange}
            />
        </form>
    );
});
