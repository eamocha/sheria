import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './CustomFieldsEdit.scss';

import APAutocompleteList from './../../../common/APForm/APAutocompleteList/APAutocompleteList.lazy';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import {
    formatDate,
    formatDateTime,
    formatTime,
    getValueFromLanguage,
} from '../../../../APHelpers';

import APTextFieldInput from '../../../common/APForm/APTextFieldInput/APTextFieldInput.lazy';

import APDatePicker from '../../../common/APForm/APDatePicker/APDatePicker.lazy';

import { APDateTimePicker } from '../../../common/APForm/APForm';

import Company from '../../../../api/Company';

import Contact from '../../../../api/Contact';

import { getActiveLanguageId } from '../../../../i18n';

import { useTranslation } from 'react-i18next';
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [customFieldsCompaniesList, setCustomFieldsCompaniesList] = useState([]);
    const [customFieldsContactsList, setCustomFieldsContactsList] = useState([]);

    const [formData, setFormData] = useState({
        custom_fields: []
    });

    const [listValues, listValuesDispatcher] = useState({
        custom_fields: {}
    });

    const handleTextCustomFieldsChange = (e, customFieldId) => {
        e.persist();

        let items = [...formData.custom_fields];
        
        let item = {
            id: customFieldId,
            value: e?.target?.value
        };

        items = items.filter(item => {
            return item?.id != customFieldId
        });

        items.push(item);
        
        setFormData((prevState) => ({
            ...prevState,
            custom_fields: items
        }));

        props.parentSetFormData((prevState) => ({
            ...prevState,
            custom_fields: items
        }));
    }

    const handleDateTimeCustomFieldsChange = (customFieldId, dateTime) => {
        let items = [...formData.custom_fields];
        
        let item = {
            id: customFieldId,
            value: formatDateTime(dateTime),
            date: formatDate(dateTime),
            time: formatTime(dateTime),
        };

        items = items.filter(item => {
            return item?.id != customFieldId
        });

        items.push(item);
        
        setFormData((prevState) => ({
            ...prevState,
            custom_fields: items
        }));

        props.parentSetFormData((prevState) => ({
            ...prevState,
            custom_fields: items
        }));
    }

    const handleDateCustomFieldsChange = (customFieldId, date) => {
        let items = [...formData.custom_fields];
        
        let item = {
            id: customFieldId,
            value: formatDate(date)
        };

        items = items.filter(item => {
            return item?.id != customFieldId
        });

        items.push(item);
        
        setFormData((prevState) => ({
            ...prevState,
            custom_fields: items
        }));

        props.parentSetFormData((prevState) => ({
            ...prevState,
            custom_fields: items
        }));
    }

    const handleCustomFieldsListChange = (customFieldId, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changelistValues) => {
        let items = [...formData.custom_fields];
        
        let item = {
            id: customFieldId,
            value: stateValue
        };

        // exclude the target custom_field from the custom_fields list
        items = items.filter(item => {
            return item?.id != customFieldId
        });

        items.push(item);
        
        setFormData((prevState) => ({
            ...prevState,
            custom_fields: items
        }));

        props.parentSetFormData((prevState) => ({
            ...prevState,
            custom_fields: items
        }));

        if (changelistValues) {
            listValuesDispatcher(prevState => ({
                ...prevState,
                custom_fields: {
                    ...prevState.custom_fields,
                    companies: multipleSelection ? defaultValuesWithMultipleSelection : defaultValues
                }
            }));

            props.parentListValuesDispatcher(prevState => ({
                ...prevState,
                custom_fields: {
                    ...prevState.custom_fields,
                    companies: multipleSelection ? defaultValuesWithMultipleSelection : defaultValues
                }
            }));
        }
    }

    const handleCustomFieldsCompaniesList = (e) => {
        e.persist();

        let value = e?.target?.value;

        if (value.length >= 3) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Company.getList({
                name: {value: value},
            }).then((response) => {

                let companiesList = response?.data?.data.map((company, key) => {

                    return {
                        title: company?.name + ' (' +  company?.shortName + ')',
                        value: company?.id
                    }
                });

                setCustomFieldsCompaniesList(companiesList);
            }).catch((error) => {

            }).finally(() => {

                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            });
        }
    }

    const handleCustomFieldsContactsList = (e) => {
        e.persist();

        let value = e?.target?.value;

        if (value.length >= 3) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Contact.getList({
                name: {value: value},
            }).then((response) => {

                let contactsList = response?.data?.data.map((contact, key) => {

                    return {
                        title: contact?.firstName + ' ' +  contact?.lastName,
                        value: contact?.id
                    }
                });

                setCustomFieldsContactsList(contactsList);
            }).catch((error) => {

            }).finally(() => {
                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            });
        }
    }

    const getCustomFieldCompanyLookupValue = (customField) => {
        let values = [];

        let companies = customField?.custom_field_values?.[0]?.custom_field_value_companies ?? [];

        values = companies.map((company, key) => {

            return {
                title: company?.name + ' (' +  company?.shortName + ')',
                value: company?.id
            }
        });

        return values;
    }

    const getCustomFieldContactLookupValue = (customField) => {
        let values = [];

        let contacts = customField?.custom_field_values?.[0]?.custom_field_value_contacts ?? [];

        values = contacts.map((contact, key) => {

            return {
                title: contact?.firstName + ' ' +  contact?.lastName,
                value: contact?.id
            }
        });

        return values;
    }
    
    const [t] = useTranslation();
    const [langId , setLangId] = useState(getActiveLanguageId());
    
    useEffect(() => {
        setLangId(getActiveLanguageId());
    }, [t]);

    let customFieldsContent = props?.customFields.map((item, key) => {
        let content = '';

        switch (item?.type) {
            case 'short_text':
                content = <APTextFieldInput
                    label={getValueFromLanguage(item, 'custom_field_languages', langId ,'', 'customName')}
                    stateKey={item?.id}
                    value={props.getCustomFieldValue(item)}
                    handleChange={handleTextCustomFieldsChange}
                />;
                break;

            case 'date_time':
                content = <APDateTimePicker
                    ampm={false}
                    format="yyyy-MM-dd HH:mm"
                    label={getValueFromLanguage(item, 'custom_field_languages', langId ,'', 'customName')}
                    stateKey={item?.id}
                    value={props.getCustomFieldValue(item)}
                    handleChange={handleDateTimeCustomFieldsChange}
                />;
                break;

            case 'date':
                content = <APDatePicker
                    format="yyyy-MM-dd"
                    label={getValueFromLanguage(item, 'custom_field_languages', langId ,'', 'customName')}
                    stateKey={item?.id}
                    value={props.getCustomFieldValue(item)}
                    handleChange={handleDateCustomFieldsChange}
                />;
                break;

            case 'long_text':
                content = <APTextFieldInput
                    label={getValueFromLanguage(item, 'custom_field_languages', langId ,'', 'customName')}
                    stateKey={item?.id}
                    multiline
                    rows={4}
                    value={props.getCustomFieldValue(item)}
                    handleChange={handleTextCustomFieldsChange}
                />;
                break;

            case 'list':
                let listOptions = [];

                if (item?.type_data) {
                    listOptions = item?.type_data.split(',').map((item, key) => {

                        return {
                            title: item,
                            value: item
                        };
                    });
                }

                content = <APAutocompleteList
                    multiple
                    multipleSelection={true}
                    label={getValueFromLanguage(item, 'custom_field_languages', langId ,'', 'customName')}
                    options={listOptions}
                    optionsLabel="title"
                    stateKey={item?.id}
                    value={props.getCustomFieldValue(item)}
                    valueKey="value"
                    onChange={handleCustomFieldsListChange}
                    textOnChange={handleCustomFieldsListChange}
                />;
                break;

            case 'lookup':
                switch (item?.type_data) {
                    case 'companies':
                        content = <APAutocompleteList
                            multiple
                            multipleSelection={true}
                            label={getValueFromLanguage(item, 'custom_field_languages', langId ,'', 'customName')}
                            stateKey={item?.id}
                            options={customFieldsCompaniesList}
                            optionsLabel="title"
                            value={getCustomFieldCompanyLookupValue(item)}
                            valueKey="value"
                            onChange={handleCustomFieldsListChange}
                            textOnChange={handleCustomFieldsCompaniesList}
                        />;
                        break;

                    case 'contacts':
                        content = content = <APAutocompleteList
                            multiple
                            multipleSelection={true}
                            label={getValueFromLanguage(item, 'custom_field_languages', langId ,'', 'customName')}
                            stateKey={item?.id}
                            options={customFieldsContactsList}
                            optionsLabel="title"
                            value={getCustomFieldContactLookupValue(item)}
                            valueKey="value"
                            onChange={handleCustomFieldsListChange}
                            textOnChange={handleCustomFieldsContactsList}
                        />;
                        break;
                
                    default:
                        break;
                }
                break;
        
            default:
                break;
        }

        return (content)
    });

    return (
        <React.Fragment>
            <p>{t("custom_fields")}</p>
            {customFieldsContent}
        </React.Fragment>
    );
});
