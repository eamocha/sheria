import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './AdvisorTimeLogsAddForm.scss';
import APAutocompleteList from '../../common/APForm/APAutocompleteList/APAutocompleteList';
import AdvisorUser from '../../../api/AdvisorUser';
import {
    Context,
    initialGlobalState
} from '../../../Store';
import {
    formatDate,
    loadListWithLanguages,
    loadUsersList,
    isFunction
} from './../../../APHelpers';
import {
    FormControl,
    FormControlLabel,
    FormGroup,
    Radio,
    RadioGroup
} from '@material-ui/core';
import LegalCase from '../../../api/LegalCase';
import MiscList from '../../../api/MiscList';
import {
    KeyboardDatePicker,
    MuiPickersUtilsProvider
} from '@material-ui/pickers';
import APTextFieldInput from '../../common/APForm/APTextFieldInput/APTextFieldInput';
import DateFnsUtils from '@date-io/date-fns';
import AdvisorTimeLog from '../../../api/AdvisorTimeLog';
import { isValid } from 'date-fns';
import { FORMS_NAMES } from '../../../Constants';
 
export default React.memo((props) => {

    const formId = FORMS_NAMES.advisorTimeLogAddForm;

    const logTimeOnOptions = {
        legalCase: {
            label: 'Matter',
            value: 'legalCase'
        },
        advisorTask: {
            label: 'Task',
            value: 'advisorTask'
        }
    };

    const advisorTimeLogStatusOptions = {
        billable: {
            label: 'Billable',
            value: 'billable'
        },
        internal: {
            label: 'Non Billable',
            value: 'internal'
        }
    };

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [formData, setFormData] = useState({
        advisor_id: '',
        legal_case_id: '',
        task_id: '',
        timeStatus: '',
        time_type_id: '',
        logDate: null,
        effectiveEffort: '',
        comments: '',
    });

    const [logTimeOn, setLogTimeOn] = useState(logTimeOnOptions.legalCase.value);

    const [advisorsList, setAdvisorsList] = useState([]);
    const [legalCasesList, setLegalCasesList] = useState([]);
    const [advisorTasksList, setAdvisorTasksList] = useState([]);
    const [timeTypesList, setTimeTypesList] = useState([]);

    const [listValues, listValuesDispatcher] = useState({
        advisor_id: {
            title: '',
            value: ''
        },
        legal_case_id: {
            title: '',
            value: ''
        },
        advisor_task_id: {
            title: '',
            value: ''
        },
        time_type_id: {
            title: '',
            value: ''
        }
    });

    useEffect(() => {

        loadListsData();
    }, []);

    const loadListsData = () => {

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorUser.getList().then((response) => {
            let data = loadUsersList(response?.data?.data);

            setAdvisorsList(data);

            return MiscList.getList({
                lists: [
                    "timeTypes"
                ]
            });
        }).then((response) => {

            loadTimeTypesList(response?.data?.data?.timeTypes);
        }).catch((error) => {

        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
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

    const handleDatePickerChange = (state, date, isTime = false) => {

        setFormData(prevState => ({
            ...prevState,
            [state]: date === null ? null : isTime ? (isValid(date) ? date : null) : formatDate(date)
        }));
    }

    const loadTimeTypesList = (data) => {
        let results = loadListWithLanguages(data, 'time_type_languages', 'name', 'id');

        setTimeTypesList(results?.options);

        /**
         * Here we are setting the first task type as default value
         * also the formData.time_type_id should be updated
         */
        listValuesDispatcher(prevState => ({
            ...prevState,
            time_type_id: results?.defaultItem
        }));

        setFormData(prevState => ({
            ...prevState,
            time_type_id: results?.defaultItemValue
        }));
    }

    const getLegalCasesList = (e) => {
        e.persist();

        let value = e.target.value;

        if (value.length >= 2) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            LegalCase.getList({
                SubjectOrPrefix:
                {
                    subject: '%' + value + '%',
                    prefix: '%' + value + '%',
                    operator: 'or'
                }
            }).then((response) => {
                setLegalCasesList(response?.data?.data);
            }).catch((error) => {

            }).finally(() => {
                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            });
        }
    }

    const getAdvisorTasksList = (e) => {
        e.persist();

        let value = e.target.value;

        if (value.length >= 2) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            LegalCase.getList({
                SubjectOrPrefix:
                {
                    subject: '%' + value + '%',
                    prefix: '%' + value + '%',
                    operator: 'or'
                }
            }).then((response) => {
                setLegalCasesList(response?.data?.data);
            }).catch((error) => {

            }).finally(() => {
                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            });
        }
    }

    const submit = (e) => {
        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });
        
        let requestData = prepareRequestData();

        AdvisorTimeLog.create(requestData).then((response) => {
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
                    text: "Time Log has been created successfully",
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
                }
            });
        }).finally(() => {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    const prepareRequestData = () => {
        let result = {};

        for (let [key, value] of Object.entries(formData)) {
            if (value !== null && value !== '') {
                result[key] = value;
            }
        }

        return result;
    }

    return (
        <MuiPickersUtilsProvider
            utils={DateFnsUtils}
        >
            <form
                id={formId}
                onSubmit={(e) => submit(e)}
            >
                <APAutocompleteList
                    label="User"
                    required
                    textRequired={true}
                    options={advisorsList}
                    optionsLabel="title"
                    stateKey="advisor_id"
                    value={listValues.advisor_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <FormGroup>
                    <FormControl
                        component="fieldset"
                    >
                        <RadioGroup
                            name="time-log-on-radio-group"
                            value={logTimeOn}
                            row
                        >
                            <FormControlLabel
                                value={logTimeOnOptions.legalCase.value}
                                control={
                                    <Radio
                                        checked={logTimeOn === logTimeOnOptions.legalCase.value}
                                    />
                                }
                                label={logTimeOnOptions.legalCase.label}
                                onChange={() => setLogTimeOn(logTimeOnOptions.legalCase.value)}
                            />
                            <FormControlLabel
                                value={logTimeOnOptions.advisorTask.value}
                                control={
                                    <Radio
                                        checked={logTimeOn === logTimeOnOptions.advisorTask.value}
                                    />
                                }
                                label={logTimeOnOptions.advisorTask.label}
                                onChange={() => setLogTimeOn(logTimeOnOptions.advisorTask.value)}
                            />
                        </RadioGroup>
                    </FormControl>
                </FormGroup>
                {
                    logTimeOn === logTimeOnOptions.legalCase.value ? 
                    (
                        <APAutocompleteList
                            label="Matter"
                            required
                            textRequired={true}
                            options={legalCasesList}
                            optionsLabel="subject"
                            optionsLabelArray={['prefix', "subject"]}
                            stateKey="legal_case_id"
                            value={listValues.legal_case_id}
                            valueKey="id"
                            onChange={handleListChange}
                            textOnChange={getLegalCasesList}
                        />
                    )
                    :
                    (
                        <APAutocompleteList
                            label="Task: e.g. T20"
                            required
                            textRequired={true}
                            options={advisorTasksList}
                            optionsLabel="subject"
                            stateKey="advisor_task_id"
                            value={listValues.advisor_task_id}
                            valueKey="id"
                            onChange={handleListChange}
                            textOnChange={getAdvisorTasksList}
                        />
                    )
                }
                <FormGroup>
                    <FormControl
                        component="fieldset"
                    >
                        <RadioGroup
                            name="time-log-status-radio-group"
                            value={formData.timeStatus}
                            onChange={(e) => handleObjectChange(e, 'timeStatus')}
                            row
                        >
                            <FormControlLabel
                                value={advisorTimeLogStatusOptions.billable.value}
                                control={
                                    <Radio
                                        checked={formData.timeStatus === advisorTimeLogStatusOptions.billable.value}
                                    />
                                }
                                label={advisorTimeLogStatusOptions.billable.label}
                            />
                            <FormControlLabel
                                value={advisorTimeLogStatusOptions.internal.value}
                                control={
                                    <Radio
                                        checked={formData.timeStatus === advisorTimeLogStatusOptions.internal.value}
                                    />
                                }
                                label={advisorTimeLogStatusOptions.internal.label}
                            />
                        </RadioGroup>
                    </FormControl>
                </FormGroup>
                <APAutocompleteList
                    label="Time spent on"
                    options={timeTypesList}
                    optionsLabel="title"
                    stateKey="time_type_id"
                    value={listValues.time_type_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <FormGroup>
                    <FormControl>
                        <KeyboardDatePicker
                            label="Date"
                            required
                            inputVariant="outlined"
                            autoOk
                            variant="inline"
                            format="yyyy-MM-dd"
                            value={formData.logDate}
                            onChange={(date) => handleDatePickerChange('logDate', date)}
                            inputadornmentprops={{ position: "end" }}
                        />
                    </FormControl>
                </FormGroup>
                <APTextFieldInput
                    label="Eff. Effort"
                    stateKey="effectiveEffort"
                    value={formData.effectiveEffort}
                    required
                    handleChange={handleObjectChange}
                />
                <APTextFieldInput
                    label="Comments"
                    stateKey="comments"
                    rows={5}
                    multiline={true}
                    value={formData.comments}
                    handleChange={handleObjectChange}
                />
            </form>
        </MuiPickersUtilsProvider>
    );
});
