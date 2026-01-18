import { Button } from '@material-ui/core';
import React, { useContext, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useHistory } from 'react-router-dom';
import { buildInstanceURL } from '../../APHelpers';
import AdvisorUserPreferences, { widgetsArray } from '../../api/AdvisorUserPreferences';
import APPageActionsToolbar from '../../components/common/ap-page/ap-page-actions-toolbar/APPageActionsToolbar';
import APPageBody from '../../components/common/ap-page/ap-page-body/APPageBody';
import APPageContainer from '../../components/common/ap-page/ap-page-container/APPageContainer';
import APPageHeader from '../../components/common/ap-page/ap-page-header/APPageHeader';
import APPageTitle from '../../components/common/ap-page/ap-page-title/APPageTitle';
import APCheckboxBtn from '../../components/common/APForm/ap-checkbox-btn/APCheckboxBtn';
import { PAGES_IDS, PAGES_TITLES, PREFERENCES_KEYS } from '../../Constants';
import { Context, initialGlobalState } from '../../Store';
import './DashboardPreferencesPage.scss';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const { t } = useTranslation();



    const [widgetsState, setWidgetsState] = useState([]);

    useEffect(() => {
        loadData();
    }, []);


    const [recordState, setRecordState] = useState(null);

    const loadData = () => {

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorUserPreferences.get({ keyName: { value: PREFERENCES_KEYS.dashboardWidgets } }).then((response) => {
            let data = response?.data?.data;

            var widgetsConfigRecord = data != null ? data :
                {
                    id: null,
                    advisor_user_id: AdvisorUserPreferences.getCurrentAdvisor().id,
                    keyName: PREFERENCES_KEYS.dashboardWidgets,
                    keyValue: null,

                };

            if (widgetsConfigRecord) {
                setRecordState(widgetsConfigRecord)
                if (widgetsConfigRecord.keyValue) {
                    var widgetsConfig = JSON.parse(widgetsConfigRecord.keyValue);
                    widgetsArray.forEach(d => {
                        widgetsConfig.forEach(w => {
                            if (d.name == w.name) {
                                d.isVisible = w.isVisible;
                            }
                        });
                    });
                }
            }
            setWidgetsState(widgetsArray);
        }).catch((error) => {

        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    const handleToggleObjectChange = (e, stateKey) => {
        var array = [];
        widgetsState.forEach(d => {
            if (d.name == stateKey)
                d.isVisible = JSON.parse(e.target.value);
            array.push(d);
        });

        setWidgetsState(array);
    }
    const history = useHistory();
    const savePreferences = (e) => {

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        recordState.keyValue = JSON.stringify(widgetsState);
        AdvisorUserPreferences.update(recordState.id, recordState).then((response) => {

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Preferences updated successfully",
                    severity: "success"
                }
            });

            history.push(`${buildInstanceURL()}/`);
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

    const listItems = widgetsState.map((d) =>
        <APCheckboxBtn
            label={t(d.name)}
            stateKey={d.name}
            color="primary"
            value={d.isVisible}
            handleChange={handleToggleObjectChange}
            title="show_hide_widget"
        />
    );

    return (
        <APPageContainer
            id={PAGES_IDS.advisorUserPreferences}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={PAGES_TITLES.advisorUserPreferences}
                />
            </APPageHeader>

            <APPageBody>
                {listItems}

                <Button
                    color="primary"
                    variant="contained"
                    onClick={(e) => savePreferences(e)}
                >
                    {t("save")}
                </Button>
            </APPageBody>
        </APPageContainer>
    );
});
