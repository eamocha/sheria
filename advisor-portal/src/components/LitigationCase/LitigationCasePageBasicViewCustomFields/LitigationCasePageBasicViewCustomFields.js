import React, {
    useContext,
    useState
} from 'react';
import './LitigationCasePageBasicViewCustomFields.scss';
import {
    Button,
    Container,
    Grid,
    Typography
} from '@material-ui/core';
import {
    getValueFromLanguage,
    isFunction
} from '../../../APHelpers';
import EditIcon from '@material-ui/icons/Edit';
import { Context } from '../../../Store';
import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../Constants';
import { getActiveLanguageId } from '../../../i18n';
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [customFields, ] = useState(props?.litigationCaseCustomFields);

    const getCustomFieldValue = (customField) => {
        let value = '';

        switch (customField?.type) {
            case "short_text":
                value = customField?.custom_field_values?.[0]?.text_value;
                
                break;

            case "date_time":
                value = customField?.custom_field_values?.[0]?.date_value ?? customField?.custom_field_values?.[0]?.time_value;
                
                break;
            
            default:
                break;
        }

        return value;
    }

    let rows = [];

    for (var i = 0; i < customFields.length;) {
        let row = customFields[i];
        let lastRow = customFields.length - 2 >= i ? customFields[i+1] : false;

        let record = <Grid
            container
            className="litigation-case-page-basic-view-field-row"
            key={"litigation-case-page-basic-view-field-row-" + i}
        >
            <React.Fragment>
                <Grid
                    item
                    sm={6}
                >
                    <Grid
                        container 
                        className="litigation-case-page-basic-view-field-row-item"
                    >
                        <Grid
                            item
                            sm={6}
                        >
                            <Typography
                                variant="body1"
                                className="litigation-case-page-basic-view-field-row-item-label"
                            >
                                {getValueFromLanguage(row, 'custom_field_languages', getActiveLanguageId(), '', 'customName') + ":"}
                            </Typography>
                        </Grid>
                        <Grid
                            item
                            sm={6}
                        >
                            <Typography
                                variant="body1"
                                className="litigation-case-page-basic-view-field-row-item-value"
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
                            className="litigation-case-page-basic-view-field-row-item"
                        >
                            <Grid
                                item
                                sm={6}
                            >
                                <Typography
                                    variant="body1"
                                    className="litigation-case-page-basic-view-field-row-item-label"
                                >
                                    {getValueFromLanguage(lastRow, 'custom_field_languages', getActiveLanguageId(), '', 'customName')+ ":"}
                                </Typography>
                            </Grid>
                            <Grid
                                item
                                sm={6}
                            >
                                <Typography
                                    variant="body1"
                                    className="litigation-case-page-basic-view-field-row-item-value"
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

    // const openEditLitigationCaseCustomFieldsForm = () => {
    //     globalStateDispatcher({
    //         modal: {
    //             ...globalState?.modal,
    //             title: FORMS_MODAL_TITLES.litigationCaseCustomFields + ": M" + props?.litigationCase?.id,
    //             open: true,
    //             form: {
    //                 ...globalState?.modal?.form,
    //                 id: FORMS_NAMES.litigationCaseCustomFields,
    //                 submitCallback: isFunction(props?.loadLitigationCaseData) ? props.loadLitigationCaseData : null,
    //                 data: {
    //                     litigationCase: props?.litigationCase,
    //                     customFields: customFields
    //                 }
    //             }
    //         }
    //     });
    // }

    return (
        <Container
            id="litigation-case-page-basic-view-custom-fields"
            maxWidth={false}
        >
            {/* <Button
                variant="outlined"
                color="secondary"
                className="litigation-case-custom-fields-edit-btn"
                onClick={() => openEditLitigationCaseCustomFieldsForm()}
                startIcon={<EditIcon />}
            >
                Edit
            </Button> */}
            {rows}
        </Container>
    );
});
