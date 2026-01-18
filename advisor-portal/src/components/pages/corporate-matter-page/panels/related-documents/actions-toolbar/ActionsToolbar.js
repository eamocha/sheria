import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './ActionsToolbar.scss';

import {
    Button,
    Container,
    LinearProgress,
    Menu,
    MenuItem
} from '@material-ui/core';

import AddIcon from '@material-ui/icons/Add';

import ArrowDropDownIcon from '@material-ui/icons/ArrowDropDown';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import { isFunction } from '../../../../../../APHelpers';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../../../Constants';

import WorkflowStatus from '../../../../../../api/WorkflowStatus';

import WorkflowStatusTransition from '../../../../../../api/WorkflowStatusTransition';

import LegalCase from '../../../../../../api/LegalCase';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [corporateMatter,] = useState(props?.corporateMatter);

    const [dataLoaded, setDataLoaded] = useState(false);

    const { t } = useTranslation();

    useEffect(() => {

        loadData();
    }, [props?.corporateMatter]);

    const loadData = () => {
        // re-initiate the dataLoaded state (for reload)
        setDataLoaded(false);

        WorkflowStatus.getList({
            "legalCaseId": {
                "value": corporateMatter?.id
            }
        }).catch((error) => {

            console.log('loading corporate matter workflow statuses and transitions', error);
        }).finally(() => {

            setDataLoaded(true);
        });
    };

    const openCorporateMatterEditForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.corporateMatterEditForm) + ": M" + corporateMatter?.id,
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.corporateMatterEditForm,
                    submitCallback: isFunction(props?.loadCorporateMatterData) ? props.loadCorporateMatterData : null,
                    data: {
                        corporateMatter: corporateMatter
                    }
                }
            }
        });
    }

    return (
        <Container
            maxWidth={false}
            className="corporate-matter-page-general-info-panel-actions-toolbar btns-container no-padding-h"
        >
            <Button
                className={"edit-btn " + globalState.domDirection}
                variant="contained"
                color="secondary"
                onClick={() => openCorporateMatterEditForm()}
                startIcon={<AddIcon />}
            >
                {t("add_task")}
            </Button>
        </Container>
    );
});
