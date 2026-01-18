import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './Details.scss';

import {
    Button,
    Container,
    Grid,
    Typography
} from '@material-ui/core';

import { GeneralInfoItem } from './../CorporateMatterPageGeneralInfoPanel';

import { useTranslation } from 'react-i18next';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import AdvisorUser from '../../../../../../api/AdvisorUser';

import { buildErrorMessages } from '../../../../../../APHelpers';

import LegalCase from '../../../../../../api/LegalCase';

import APAutocompleteList from '../../../../../common/APForm/APAutocompleteList/APAutocompleteList';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const [assigneesList, setAssigneesList] = useState([]);
    const [dataChangeFlag, setDataChangeFlag] = useState(false);

    const { t } = useTranslation()

    useEffect(() => {
        AdvisorUser.getList().then((response) => {
            let list = response?.data?.data;
            let options = [];

            for (var i = 0; i < list.length; i++) {
                let item = list[i];

                options.push({
                    title: item?.firstName + " " + item?.lastName,
                    value: item?.contact_id
                });
            }

            setAssigneesList(options);
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
    }, []);

    const [formData, setFormData] = useState({
        assignees: props?.corporateMatter?.outsources_by_advisor ? props?.corporateMatter?.outsources_by_advisor[0]?.outsource_contacts.map((item, index) => {
            return item?.outsource_contact?.id
        }) : [],
    });

    const [listValues, listValuesDispatcher] = useState({
        assignees: props?.corporateMatter?.outsources_by_advisor ? props?.corporateMatter?.outsources_by_advisor[0]?.outsource_contacts.map((item, index) => {
            return {
                title: item?.outsource_contact?.firstName + " " + item?.outsource_contact?.lastName,
                value: item?.outsource_contact?.id
            }

        }) : [],
    });

    const prepareRequestData = (data) => {
        let formData = new FormData();

        for (let [key, value] of Object.entries(data)) {

            if (key === "assignees") {
                for (var i = 0; i < value.length; i++) {
                    formData.append("assignees[]", value[i]);
                }
            }

        }

        return formData;
    }

    const handleListChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues) => {
        setDataChangeFlag(true);
        setFormData(prevState => ({
            ...prevState,
            [state]: stateValue
        }));
    }

    const save = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        let submitData = prepareRequestData(formData);

        LegalCase.updateOutsourceingContacs(props?.corporateMatter?.outsources_by_advisor[0]?.id, submitData).then((response) => {
            globalStateDispatcher({
                modal: initialGlobalState?.modal
            });

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Corporate Matter has been updated successfully",
                    severity: "success"
                }
            });

            setDataChangeFlag(false);
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
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    const items = [
        {
            label: t("subject"),
            value: props?.corporateMatter?.subject
        },
        {
            label: t("value"),
            value: props?.corporateMatter?.caseValue
        },
        {
            label: t("outsourced_to"),
            value: props?.corporateMatter?.outsourced_to,
            fullWidth: true,
            inline_edit:
                <React.Fragment>
                    <Grid
                        container
                        sm={12}
                        className="field-row"
                    >

                        <Grid
                            sm={8}
                            className="field-row"
                        >
                            <APAutocompleteList
                                multiple
                                options={assigneesList}
                                optionsLabel="title"
                                stateKey="assignees"
                                value={listValues.assignees}
                                valueKey="value"
                                onChange={handleListChange}
                                multipleSelection={true}
                                changeDefaultValues={true}
                                variant="standard"
                            />
                            {formData.assignees.length == 0 ?
                                <Typography
                                    variant="body1"
                                    color="error"
                                >
                                    {t("must_select_oustsourced_to")}
                                </Typography> :

                                null}
                        </Grid>
                        <Grid
                            sm={4}
                            className="field-row"
                        >
                            {formData.assignees.length > 0 && dataChangeFlag
                                ?
                                <Button
                                    color="primary"
                                    variant="contained"
                                    onClick={() => save()}
                                >
                                    {t('save')}
                                </Button>
                                :
                                <></>
                            }

                        </Grid>
                    </Grid>

                </React.Fragment>

        },
        {
            label: t("description"),
            value: props?.corporateMatter?.description,
            fullWidth: true
        },
        {
            label: t("latest_development"),
            value: props?.corporateMatter?.latest_development,
            fullWidth: true
        },
        {
            label: t("status_comments"),
            value: props?.corporateMatter?.statusComments,
            fullWidth: true
        }
    ];

    let rows = [];

    for (var i = 0; i < items.length;) {
        let row = items[i];
        let lastRow = items.length - 2 >= i ? items[i + 1] : false;

        let record = <Grid
            container
            className="field-row"
            key={"corporate-matter-page-general-info-panel-details-field-row-" + i}
        >
            {
                row?.fullWidth ?
                    (
                        <Grid
                            item
                            xs={12}
                        >
                            <Grid
                                container
                                className="field-row"
                            >
                                <GeneralInfoItem
                                    row={row}
                                />
                            </Grid>
                        </Grid>
                    )
                    :
                    (
                        <React.Fragment>
                            <Grid
                                item
                                sm={6}
                                className="field-row"
                            >
                                <GeneralInfoItem
                                    row={row}
                                />
                            </Grid>
                            {
                                // do we still have one last item?
                                lastRow ?
                                    <Grid
                                        item
                                        sm={6}
                                        className="field-row"
                                    >
                                        <GeneralInfoItem
                                            row={lastRow}
                                        />
                                    </Grid>
                                    :
                                    <Grid
                                        item
                                        sm={6}
                                        className="field-row"
                                    />
                            }
                        </React.Fragment>
                    )
            }
        </Grid>;

        rows.push(record);

        if (row?.fullWidth) {
            i++;
        } else {
            i += 2;
        }
    }

    return (
        <Container
            id="corporate-matter-page-general-info-panel-details"
            maxWidth={false}
            className="section"
        >
            {rows}
        </Container>
    );
});
