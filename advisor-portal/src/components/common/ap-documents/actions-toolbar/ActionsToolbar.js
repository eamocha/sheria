import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './ActionsToolbar.scss';

import {
    Button,
    Container,
    Menu,
    MenuItem,
    Grid
} from '@material-ui/core';

import MenuIcon from '@material-ui/icons/Menu';

import {
    Context,
    initialGlobalState
} from './../../../../Store';

import { isFunction } from './../../../../APHelpers';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../Constants';

import { BreadCrumbsContainer } from '../APDocuments';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const [currentFolderId, setCurrentFolderId] = useState(props?.currentFolderId ?? '');
    const [module, setModule] = useState(props?.module ?? '');
    const [moduleDisplayName, setModuleDisplayName] = useState(props?.moduleDisplayName ?? '');
    const [moduleRecordId, setModuleRecordId] = useState(props?.moduleRecordId ?? '');

    const [menuAnchorEl, setMenuAnchorEl] = useState(null);

    const { t } = useTranslation();

    useEffect(() => {
        
        setCurrentFolderId(props?.currentFolderId ?? '');
    }, [props?.currentFolderId]);

    useEffect(() => {
        
        setModule(props?.module ?? '');
    }, [props?.module]);

    useEffect(() => {
        
        setModuleDisplayName(props?.moduleDisplayName ?? '');
    }, [props?.moduleDisplayName]);

    useEffect(() => {
        
        setModuleRecordId(props?.moduleRecordId ?? '');
    }, [props?.moduleRecordId]);

    const handleMenuClick = (e) => {
        setMenuAnchorEl(e.currentTarget);
    };

    const handleMenuClose = (event) => {
        switch (event) {
            case 'upload-file':
                globalStateDispatcher({
                    modal: {
                        ...globalState?.modal,
                        title: t(FORMS_MODAL_TITLES.APDocumentAddFileForm),
                        open: true,
                        form: {
                            ...globalState?.modal?.form,
                            id: FORMS_NAMES.APDocumentAddFileForm,
                            submitCallback: isFunction(props?.loadDocuments) ? props.loadDocuments : null,
                            data: {
                                parentFolderId: currentFolderId,
                                module: module,
                                module_record_id: moduleRecordId
                            }
                        }
                    }
                });
                
                break;

            case 'create-folder':
                globalStateDispatcher({
                    modal: {
                        ...globalState?.modal,
                        title: t(FORMS_MODAL_TITLES.APDocumentAddFolderForm),
                        open: true,
                        form: {
                            ...globalState?.modal?.form,
                            id: FORMS_NAMES.APDocumentAddFolderForm,
                            submitCallback: isFunction(props?.loadDocuments) ? props.loadDocuments : null,
                            data: {
                                parentFolderId: currentFolderId,
                                module: module,
                                module_record_id: moduleRecordId
                            }
                        }
                    }
                });
                
                break;

            case 'copy-path':
                break;
        
            default:
                break;
        }

        setMenuAnchorEl(null);
    };
    
    return (
        <Container
            maxWidth={false}
            // classes={{root: classes.advancedSearchForm}}
            className="ap-documents-actions-toolbar"
        >
            <Grid
                container
                className="ap-documents-actions-toolbar-sides-container"
            >
                <Grid
                    item
                    sm="6"
                >
                    <BreadCrumbsContainer
                        items={props?.breadCrumbsItems}
                        moduleDisplayName={moduleDisplayName}
                        // rootQuery={props?.rootQuery}
                        handleBreadCrumbClick={props.handleBreadCrumbClick}
                    />
                </Grid>
                <Grid
                    item
                    container
                    justify="flex-end"
                    sm={6}
                    className="ap-documents-actions-toolbar-side right"
                >
                    <Button
                        variant="text"
                        color="primary"
                        // className={classes.advancedSearchBtn}
                        onClick={(e) => handleMenuClick(e)}
                    >
                        <MenuIcon />
                    </Button>
                    <Menu
                        anchorEl={menuAnchorEl}
                        keepMounted
                        getContentAnchorEl={null}
                        anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
                        transformOrigin={{ vertical: "top", horizontal: "center" }}
                        open={Boolean(menuAnchorEl)}
                        onClose={handleMenuClose}
                    >
                        <MenuItem
                            onClick={() => handleMenuClose('upload-file')}
                        >
                            {t("upload_file")}
                        </MenuItem>
                        <MenuItem
                            onClick={() => handleMenuClose('create-folder')}
                        >
                            {t("create_folder")}
                        </MenuItem>
                    </Menu>
                </Grid>
            </Grid>
        </Container>
    );
});
