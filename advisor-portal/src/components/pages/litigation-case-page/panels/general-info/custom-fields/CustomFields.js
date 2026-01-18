import React, {
    useEffect,
    useState
} from 'react';

import './CustomFields.scss';

import {
    Container,
    Grid,
    LinearProgress,
    Typography
} from '@material-ui/core';

import {
    getValueFromLanguage,
} from '../../../../../../APHelpers';

import CustomField from '../../../../../../api/CustomField';
import { getActiveLanguageId } from '../../../../../../i18n';
import { useTranslation } from 'react-i18next';
 
export default React.memo((props) => {
    const [customFields, setCustomFields] = useState([]);

    const [dataLoaded, setDataLoaded] = useState(false);
    
    useEffect(() => {
        
        loadData();
    }, [props?.litigationCase]);

    const loadData = () => {
        // re-initiate the dataLoaded state (for reload)
        setDataLoaded(false);

        CustomField.getList(
            {
                "model": {
                    "value": "legal_case"
                },
                "category": {
                    "value": "litigation"
                },
                "caseType": {
                    "value": props?.litigationCase?.litigationCase?.case_type_id
                }
            }, 
            CustomField.allRelations,
            JSON.stringify([{"relation": "customFieldValues", "filters": [{"name": "recordId", "value": props?.litigationCase?.id}]}])
        ).then((response) => {

            setCustomFields(response?.data?.data);
        }).catch((error) => {

            console.log('loading litigation case custom fields', error);
        }).finally(() => {

            setDataLoaded(true);
        });
    }

    const getCustomFieldValue = (customField) => {
        let value = '';

        switch (customField?.type) {
            case "date":
                value = customField?.custom_field_values?.[0]?.date_value ?? '';
                
                break;

            case "date_time":
                value = customField?.custom_field_values?.[0]?.date_value + ' ' + customField?.custom_field_values?.[0]?.time_value;
                
                break;

            case 'lookup':
                switch (customField?.type_data) {
                    case 'companies':
                        value = getCustomFieldValueCompanies(customField);
                        break;

                    case 'contacts':
                        value = getCustomFieldValueContacts(customField);
                        break;
                
                    default:
                        break;
                }
                break;
            
            default:
                value = customField?.custom_field_values?.[0]?.text_value;
                break;
        }

        return value;
    }

    const getCustomFieldValueCompanies = (customField) => {
        let values = [];

        let companies = customField?.custom_field_values?.[0]?.custom_field_value_companies ?? [];

        values = companies.map((company, key) => {

            return company?.name + ' (' +  company?.shortName + ')';
        });

        return values.join(', ');
    }

    const getCustomFieldValueContacts = (customField) => {
        let values = [];

        let contacts = customField?.custom_field_values?.[0]?.custom_field_value_contacts ?? [];

        values = contacts.map((contact, key) => {

            return contact?.firstName + ' ' +  contact?.lastName;
        });

        return values.join(', ');
    }

    let rows = [];

    const { t } = useTranslation();
    const [langId , setLangId] = useState(getActiveLanguageId());

    useEffect(() => {
        setLangId(getActiveLanguageId());
    }, [t]);

    for (var i = 0; i < customFields.length;) {
        let row = customFields[i];
        let lastRow = customFields.length - 2 >= i ? customFields[i+1] : false;

        let record = <Grid
            container
            className="field-row"
            key={"litigation-case-page-general-info-panel-custom-fields-field-row-" + i}
        >
            <React.Fragment>
                <Grid
                    item
                    sm={6}
                >
                    <Grid
                        container 
                        className="field-row-item"
                    >
                        <Grid
                            item
                            sm={4}
                        >
                            <Typography
                                variant="body1"
                                className="field-row-item-label"
                            >
                                {getValueFromLanguage(row, 'custom_field_languages', langId, '', 'customName') + ":"}
                            </Typography>
                        </Grid>
                        <Grid
                            item
                            sm={8}
                        >
                            <Typography
                                variant="body1"
                                className="field-row-item-value"
                            >
                                {getCustomFieldValue(row)}
                            </Typography>
                        </Grid>
                    </Grid>
                </Grid>
                {
                    // do we still have one last item?
                    lastRow ?
                    <Grid
                        item
                        sm={6}
                    >
                        <Grid
                            container 
                            className="field-row-item"
                        >
                            <Grid
                                item
                                sm={4}
                            >
                                <Typography
                                    variant="body1"
                                    className="field-row-item-label"
                                >
                                    {getValueFromLanguage(lastRow, 'custom_field_languages', langId, '', 'customName')+ ":"}
                                </Typography>
                            </Grid>
                            <Grid
                                item
                                sm={8}
                            >
                                <Typography
                                    variant="body1"
                                    className="field-row-item-value"
                                >
                                    {getCustomFieldValue(lastRow)}
                                </Typography>
                            </Grid>
                        </Grid>
                    </Grid>
                    :
                    <Grid
                        item
                        sm={6}
                    />
                }
            </React.Fragment>
        </Grid>;

        rows.push(record);

        if (row?.fullWidth) {
            i++;
        } else {
            i+=2;
        }
    }

    if (!dataLoaded) {
        return (
            <LinearProgress />
        );
    }

    return (
        <Container
            id="litigation-case-page-general-info-panel-custom-fields"
            maxWidth={false}
            className="section"
        >
            {rows}
        </Container>
    );
});
