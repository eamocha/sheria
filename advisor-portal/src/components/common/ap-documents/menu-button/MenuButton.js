import React, {
    useState
} from 'react';

import './MenuButton.scss';

import {
    Button,
    MenuItem,
    Menu,
} from '@material-ui/core';

import MenuIcon from '@material-ui/icons/Menu';

import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const { t } = useTranslation();

    const [menuState, setMenuState] = useState({ open: false, anchorEl: null });

    const handleClick = (event) => {

        setMenuState(prevState => ({
            open: true,
            anchorEl: event.target
        }));
    };

    const handleRequestClose = (action, document) => {
        setMenuState(prevState => ({
            ...prevState,
            open: false
        }));
        props?.handleRowActionsMenuClose(action, document)
    };

    return (
        <React.Fragment>
            <Button
                aria-controls={"documents-row-actions-menu-" + props?.rowData?.id}
                aria-haspopup="true"
                onClick={(e) => handleClick(e)}
            >
                <MenuIcon />
            </Button>
            <Menu
                id={"documents-row-actions-menu-" + props?.rowData?.id}
                anchorEl={menuState.anchorEl}
                keepMounted
                getContentAnchorEl={null}
                anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
                transformOrigin={{ vertical: "top", horizontal: "center" }}
                open={menuState.open}
                onClose={() => handleRequestClose(null)}
            >
                {
                    props?.rowData?.type == 'file' ?
                        <MenuItem
                            onClick={() => handleRequestClose('download-file', props?.rowData)}
                        >
                            {t('download')}
                        </MenuItem>
                        :
                        null
                }
                <MenuItem
                    onClick={() => handleRequestClose(props?.rowData?.type == 'folder' ? 'rename-folder' : 'edit-file', props?.rowData)}
                >
                    {props?.rowData?.type == 'folder' ? t('rename') : t('edit')}
                </MenuItem>
                <MenuItem
                    onClick={() => handleRequestClose(props?.rowData?.type == 'folder' ? 'delete-folder' : 'delete-file', props?.rowData)}
                >
                    {t('delete')}
                </MenuItem>

                {
                    props?.rowData?.file_versions_folder?.file_versions?.length > 0 ?
                        (
                            <MenuItem
                                onClick={() => handleRequestClose('list-versions', props?.rowData)}
                            >
                                {t('list_versions')}
                            </MenuItem>
                        )
                        :
                        null
                }
            </Menu>
        </React.Fragment>
    );
});
