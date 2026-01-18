import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './LitigationCaseEditForm.scss';
import DateFnsUtils from '@date-io/date-fns';
import { MuiPickersUtilsProvider } from '@material-ui/pickers';
import {
    FORMS_NAMES,
    PRIORITY_OPTIONS
} from '../../../Constants';
import { ValidatorForm } from 'react-material-ui-form-validator';
import APAutocompleteList from '../../common/APForm/APAutocompleteList/APAutocompleteList.lazy';
import {
    Context,
    initialGlobalState
} from '../../../Store';
import MiscList from '../../../api/MiscList';
import {
    formatDate,
    isFunction,
} from '../../../APHelpers';
import APTextFieldInput from '../../common/APForm/APTextFieldInput/APTextFieldInput.lazy';
import APPrioritySign from '../../common/ap-priority-sign/APPrioritySign.lazy';
import APDatePicker from '../../common/APForm/APDatePicker/APDatePicker.lazy';
import { isValid } from 'date-fns';
import ExpandLessIcon from '@material-ui/icons/ExpandLess';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import {
    Button,
    Collapse,
    Container
} from '@material-ui/core';
import CustomField from '../../../api/CustomField';
import LegalCase from '../../../api/LegalCase';
import { CustomFieldsEdit } from '../../forms/litigation-case/LitigationCaseForms';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const formId = FORMS_NAMES.litigationCaseEditForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [litigationCase, setLitigationCase] = useState(globalState?.modal?.form?.data?.litigationCase);
    const [litigationCasePracticeAreasList, setLitigationCasePracticeAreasList] = useState([]);
    // const [legalCaseSuccessProbabilitiesList, setLegalCaseSuccessProbabilitiesList] = useState([]);
    const [litigationCaseCustomFields, setLitigationCaseCustomFields] = useState([]);
    const [moreFields, setMoreFields] = useState(false);

    const [t] = useTranslation();

    const [formData, setFormData] = useState({
        subject: litigationCase?.subject ?? '',
        description: litigationCase?.description ?? '',
        case_type_id: litigationCase?.case_type_id ?? '',
        internalReference: litigationCase?.internalReference ?? '',
        // legal_case_success_probability_id: litigationCase?.legal_case_success_probability_id ?? '',
        priority: litigationCase?.priority ?? '',
        statusComments: litigationCase?.statusComments ?? '',
        latest_development: litigationCase?.latest_development ?? '',
        caseArrivalDate: litigationCase?.caseArrivalDate ?? null,
        arrivalDate: litigationCase?.arrivalDate ?? null,
        dueDate: litigationCase?.dueDate ?? null,
        caseValue: litigationCase?.caseValue ?? '',
        custom_fields: []
    });

    const [listValues, listValuesDispatcher] = useState({
        case_type_id: {
            title: litigationCase?.legal_case_type?.name,
            value: litigationCase?.legal_case_type?.id
        },
        // legal_case_success_probability_id: {
        //     title: getValueFromLanguage(litigationCase?.success_probability, 'success_probability_languages', 1),
        //     value: litigationCase?.success_probability?.id
        // },
        priority: {
            title: litigationCase?.priority,
            value: litigationCase?.priority
        },
        custom_fields: {}
    });

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
                "litigationCasePracticeAreas",
                "legalCaseSuccessProbabilities"
            ]
        }).then((response) => {

            loadLitigationCasePracticeAreas(response?.data?.data?.litigationCasePracticeAreas);

            // loadLegalCaseSuccessProbabilitiesList(response?.data?.data?.legalCaseSuccessProbabilities);

            return CustomField.getList({
                "model": {
                    "value": "legal_case"
                },
                "category": {
                    "value": "litigation"
                },
                "caseType": {
                    "value": litigationCase?.litigationCase?.case_type_id
                }
            }, CustomField.allRelations, JSON.stringify([{ "relation": "customFieldValues", "filters": [{ "name": "recordId", "value": litigationCase?.id }] }]));
        }).then((response) => {

            setLitigationCaseCustomFields(response?.data?.data);

            let items = [];

            for (var i = 0; i < response?.data?.data.length; i++) {
                let dataItem = response.data.data[i];

                let itemValue = getCustomFieldValue(dataItem);

                if (itemValue) {
                    items.push({
                        id: dataItem?.id,
                        value: itemValue
                    });
                }
            }

            setFormData((prevState) => ({
                ...prevState,
                custom_fields: items
            }));
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
                }
            });
        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    const loadLitigationCasePracticeAreas = (data) => {
        let options = [];

        for (var i = 0; i < data.length; i++) {
            let item = data[i];

            options.push({
                title: item?.name,
                value: item?.id
            });
        }

        setLitigationCasePracticeAreasList(options);
    }

    // const loadLegalCaseSuccessProbabilitiesList = (data) => {
    //     let result = loadListWithLanguages(data, 'success_probability_languages', 'name', 'id');

    //     setLegalCaseSuccessProbabilitiesList(result?.options);

    //     /**
    //      * Here we are setting the first task type as default value
    //      * also the formData.legal_case_success_probability_id should be updated
    //      */
    //     listValuesDispatcher(prevState => ({
    //         ...prevState,
    //         legal_case_success_probability_id: result?.defaultItem
    //     }));

    //     setFormData(prevState => ({
    //         ...prevState,
    //         legal_case_success_probability_id: result?.defaultItemValue
    //     }));
    // }

    const getCustomFieldValue = (customField) => {
        let value = '';

        switch (customField?.type) {
            case "date":
                value = customField?.custom_field_values?.[0]?.date_value ?? null;
                break;

            case "date_time":
                value = customField?.custom_field_values?.[0]?.date_value && customField?.custom_field_values?.[0]?.time_value ? (customField?.custom_field_values?.[0]?.date_value) + ' ' + (customField?.custom_field_values?.[0]?.time_value) : null;
                break;

            default:
                value = customField?.custom_field_values?.[0]?.text_value;
                break;
        }

        return value;
    }

    const handleObjectChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
    }

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
    }

    const handleDatePickerChange = (state, date, time = false) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: date === null ? null : time ? (isValid(date) ? date : null) : formatDate(date)
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

        let submitData = prepareRequestData();

        LegalCase.update(litigationCase?.id, submitData).then((response) => {
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
                    text: "Litigation Case has been updated successfully",
                    severity: "success"
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
                    globalLoader: initialGlobalState?.globalLoader
                });
            }
        });
    }

    const prepareRequestData = () => {
        let result = {};

        for (let [key, value] of Object.entries(formData)) {
            if (value && value !== '') {
                result[key] = value;
            }
        }

        return result;
    }

    return (
        <MuiPickersUtilsProvider
            utils={DateFnsUtils}
        >
            <ValidatorForm
                id={formId}
                onSubmit={(e) => submit(e)}
            >
                <APTextFieldInput
                    label={t("name")}
                    stateKey="subject"
                    value={formData.subject}
                    required
                    handleChange={handleObjectChange}
                />
                <APTextFieldInput
                    label={t("description")}
                    stateKey="description"
                    rows={5}
                    multiline={true}
                    value={formData.description}
                    handleChange={handleObjectChange}
                />
                <APTextFieldInput
                    label={t("latest_development")}
                    stateKey="latest_development"
                    rows={5}
                    multiline={true}
                    value={formData.latest_development}
                    handleChange={handleObjectChange}
                />
                <APTextFieldInput
                    label={t("status_comments")}
                    stateKey="statusComments"
                    rows={5}
                    multiline={true}
                    value={formData.statusComments}
                    handleChange={handleObjectChange}
                />
                <APAutocompleteList
                    label={t("practice_area")}
                    required
                    textRequired={true}
                    options={litigationCasePracticeAreasList}
                    optionsLabel="title"
                    stateKey="case_type_id"
                    value={listValues.case_type_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APDatePicker
                    label={t("arrival_date")}
                    stateKey="caseArrivalDate"
                    format="yyyy-MM-dd"
                    value={formData.caseArrivalDate}
                    required
                    handleChange={handleDatePickerChange}
                />
                <APDatePicker
                    label={t("filled_on")}
                    stateKey="arrivalDate"
                    format="yyyy-MM-dd"
                    value={formData.arrivalDate}
                    handleChange={handleDatePickerChange}
                />
                <APDatePicker
                    label={t("due_date")}
                    stateKey="dueDate"
                    format="yyyy-MM-dd"
                    value={formData.dueDate}
                    handleChange={handleDatePickerChange}
                />
                <APTextFieldInput
                    label={t("internal_ref_number")}
                    stateKey="internalReference"
                    value={formData.internalReference}
                    handleChange={handleObjectChange}
                />
                <APAutocompleteList
                    label={t("priority")}
                    options={PRIORITY_OPTIONS}
                    optionsLabel="title"
                    stateKey="priority"
                    value={listValues.priority}
                    valueKey="value"
                    onChange={handleListChange}
                    textOnChange={handleObjectChange}
                    renderOption={option => <APPrioritySign
                        priority={option?.["title"]}
                    />
                    }
                />
                <APTextFieldInput
                    label={t("value")}
                    stateKey="caseValue"
                    value={formData.caseValue}
                    handleChange={handleObjectChange}
                />
                {
                    litigationCaseCustomFields.length > 0 ?
                        <React.Fragment>
                            <Collapse
                                in={moreFields}
                            >
                                <CustomFieldsEdit
                                    customFields={litigationCaseCustomFields}
                                    getCustomFieldValue={getCustomFieldValue}
                                    parentSetFormData={setFormData}
                                    parentListValuesDispatcher={listValuesDispatcher}
                                />
                            </Collapse>
                            <Container
                                className="more-fields-btn-container"
                            >
                                <Button
                                    className="more-fields-btn"
                                    variant="text"
                                    color="primary"
                                    onClick={() => setMoreFields(prevState => !prevState)}
                                    startIcon={moreFields ? <ExpandLessIcon /> : <ExpandMoreIcon />}
                                >
                                    {moreFields ? t('less_fields') : t('more_fields')}
                                </Button>
                            </Container>
                        </React.Fragment>
                        :
                        null
                }
            </ValidatorForm>
        </MuiPickersUtilsProvider>
    );
});
