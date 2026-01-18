import React, {
    useContext,
    useState
} from 'react';
import './APMainMenuAddItem.scss';
import IconButton from '@material-ui/core/IconButton';
import {
    ListItem,
    Menu,
    MenuItem
} from '@material-ui/core';
import AddCircleOutlineOutlined from '@material-ui/icons/AddCircleOutlineOutlined';
import { Context, initialGlobalState } from '../../../../Store';
import { FORMS_MODAL_TITLES, FORMS_NAMES, DATA_GRIDS } from '../../../../Constants';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [menuAnchor, setMenuAnchor] = useState(false);

    const { t } = useTranslation();

    const handleMenuClick = (targetFormId, targetFormName, targetGrid) => {
        let formData = null;

        if (targetFormId === FORMS_NAMES.litigationCaseHearingAddForm || targetFormId === FORMS_NAMES.advisorTaskAddForm) {
            formData = {
                mode: 'absolute-add'
            };
        }

        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: targetFormName,
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: targetFormId,
                    data: formData,
                    targetGrid: targetGrid ?? null
                }
            }
        });

        setMenuAnchor(null);
    }

    return (
        <ListItem
            classes={{ root: "AP-main-menu-list-item" }}
        >
            <IconButton
                classes={{ root: "AP-main-menu-list-item-icon-btn", label: "AP-main-menu-list-item-icon-label" }}
                edge="start"
                color="inherit"
                onClick={(e) => setMenuAnchor(e.currentTarget)}
            >
                <AddCircleOutlineOutlined
                    classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                /> {t("add")}
            </IconButton>
            <Menu
                anchorEl={menuAnchor}
                getContentAnchorEl={null}
                anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
                transformOrigin={{ vertical: "top", horizontal: "center" }}
                keepMounted
                open={Boolean(menuAnchor)}
                onClose={() => setMenuAnchor(null)}
            >
                <MenuItem
                    onClick={() => handleMenuClick(FORMS_NAMES.advisorTaskAddForm, t(FORMS_MODAL_TITLES.advisorTaskAddForm), DATA_GRIDS.tasks)}
                >
                    {t("task")}
                </MenuItem>
                <MenuItem
                    onClick={() => handleMenuClick(FORMS_NAMES.advisorTimeLogAddForm, t(FORMS_MODAL_TITLES.advisorTimeLogAddForm), DATA_GRIDS.timeLogs)}
                >
                    {t("log_time")}
                </MenuItem>
                <MenuItem
                    onClick={() => handleMenuClick(FORMS_NAMES.hearingAddForm, t(FORMS_MODAL_TITLES.hearingAddForm), DATA_GRIDS.hearings)}
                >
                    {t("hearing")}
                </MenuItem>
            </Menu>
        </ListItem>
    );
});
