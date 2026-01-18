import React, {
    useState
} from 'react';

import './HearingsRowMenu.scss';

import {
    Button,
    Menu,
    MenuItem
} from '@material-ui/core';

import MenuIcon from '@material-ui/icons/Menu';

import { useTranslation } from 'react-i18next';


export default React.memo((props) => {
    const [menuAnchorEl, setMenuAnchorEl] = useState(false);

    const [t] = useTranslation();

    const handleMenuBtnClick = (e) => {
        setMenuAnchorEl(e.currentTarget);
    };

    const handleMenuClose = (target) => {
        switch (target) {

            case 'DeleteHearing':
                props.handleDeleteHearing(props?.hearing?.id);
                setMenuAnchorEl(null);
                break;

            default:
                setMenuAnchorEl(null);
                break;
        }
    };

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
                    onClick={() => handleMenuClose('DeleteHearing')}
                >
                    {t("delete")}
                </MenuItem>

            </Menu>
        </React.Fragment>
    );
});
