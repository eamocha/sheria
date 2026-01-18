import React, {
    useContext,
    useState
} from 'react';

import './HearingsTableRowMenu.scss';

import {
    Button,
    Menu,
    MenuItem
} from '@material-ui/core';

import MenuIcon from '@material-ui/icons/Menu';

import { Context, initialGlobalState } from './../../../../../../Store';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../../../Constants';

import { useTranslation } from 'react-i18next';
import Hearing from '../../../../../../api/Hearing';
import { buildErrorMessages, isFunction } from '../../../../../../APHelpers';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const [menuAnchorEl, setMenuAnchorEl] = useState(false);

    const [t] = useTranslation();

    const handleMenuBtnClick = (e) => {
        setMenuAnchorEl(e.currentTarget);
    };

    const handleMenuClose = (target) => {
        switch (target) {
            case 'HearingForm':
                openEditHearingForm();

                break;

            case 'HearingJudgmentForm':
                 handleHearingJudgmentForm();

                break;

            case 'DeleteHearing':
                handleDeleteHearing();

                break;

            default:
                setMenuAnchorEl(null);

                break;
        }
    };

    const openEditHearingForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.hearingEditForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.hearingEditForm,
                    submitCallback: props?.loadActivities,
                    data: {
                        hearing: props?.hearing,
                        legalCase: props?.litigationCase,
                        addHearingOnLegalCase: true,
                        currentStage: props?.currentStage
                    }
                }
            }
        });

        setMenuAnchorEl(null);
    };

    const handleHearingJudgmentForm = () =>{
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.hearingJudgementForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.hearingJudgementForm,
                    submitCallback: props?.loadActivities,
                    data: {
                        hearing: props?.hearing,
                        legalCase: props?.litigationCase,
                        addHearingOnLegalCase: true,
                        currentStage: props?.currentStage
                    }
                }
            }
        });

        setMenuAnchorEl(null);
    }

    const handleDeleteHearing = () => {
        if (window.confirm("Are you sure you want to delete this hearing?")) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Hearing.delete(props?.hearing?.id).then((response) => {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: "Hearing has been deleted successfully",
                        severity: "success"
                    }
                });

                if (isFunction(props?.loadActivities)) {
                    props?.loadActivities();
                }
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
                    globalLoader: initialGlobalState?.globalLoader
                });
            });

        }


    }

    return (
        <React.Fragment>
            <Button
                className="menu-btn"
                aria-controls="litigation-case-activity-hearing-item-actions-menu"
                aria-haspopup="true"
                onClick={(e) => handleMenuBtnClick(e)}
                size="small"
            >
                <MenuIcon
                    fontSize="small"
                />
            </Button>
            <Menu
                id={"litigation-case-activity-hearing-item-actions-menu-" + props?.rowkey}
                anchorEl={menuAnchorEl}
                keepMounted
                getContentAnchorEl={null}
                anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
                transformOrigin={{ vertical: "top", horizontal: "center" }}
                open={Boolean(menuAnchorEl)}
                onClose={handleMenuClose}
            >
                <MenuItem
                    onClick={() => handleMenuClose('HearingForm')}
                >
                    {t("open")}
                </MenuItem>
                {
                    props?.hearing?.judged === 'no' ?
                        <MenuItem
                            onClick={() => handleMenuClose('HearingJudgmentForm')}
                        >
                            {t("set_judgment")}
                        </MenuItem>
                        :
                        null
                }
                {
                    props?.hearing?.createdByChannel === 'AP' ?
                        <MenuItem
                            onClick={() => handleMenuClose('DeleteHearing')}
                        >
                            {t("delete")}
                        </MenuItem> : null
                }
            </Menu>
        </React.Fragment>
    );
});
