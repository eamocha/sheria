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
import { Context } from '../../../../Store';
import { FORMS_MODAL_TITLES, FORMS_NAMES } from '../../../../Constants';

export default React.memo((props) => {

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [menuAnchor, setMenuAnchor] = useState(false);

    const handleMenuClick = (targetFormId, targetFormName) => {
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
                    data: formData
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
                /> Add
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
                    onClick={() => handleMenuClick(FORMS_NAMES.advisorTaskAddForm, t(FORMS_MODAL_TITLES.advisorTaskAddForm))}
                >
                    Task
                </MenuItem>
                <MenuItem
                    onClick={() => handleMenuClick(FORMS_NAMES.advisorTimeLogAddForm, t(FORMS_MODAL_TITLES.advisorTimeLogAddForm))}
                >
                    Log Time
                </MenuItem>
                <MenuItem
                    onClick={() => handleMenuClick(FORMS_NAMES.litigationCaseHearingAddForm, t(FORMS_MODAL_TITLES.litigationCaseHearingAddForm))}
                >
                    Hearing
                </MenuItem>
            </Menu>
        </ListItem>
    );
});
