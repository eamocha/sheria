import React, {
    useState,
    useEffect,
    useContext
} from 'react';

import './Container.scss';

import {
    Button,
    Container,
    Link,
} from '@material-ui/core';

import Document from './../../../../api/Document';

import FolderIcon from '@material-ui/icons/Folder';

import {
    FileIcon,
    defaultStyles
} from 'react-file-icon';

import {
    ActionsToolbar,
    GridTable as DocumentsGridTable,
    FileVersionsContainer,
    DropzoneContainer
} from './../APDocuments';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import {
    buildErrorMessages,
    formatDateTime,
    getAdvisorUserFullName,
    getModulePrefix,
    isFunction
} from '../../../../APHelpers';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../Constants';

import convertSize from 'convert-size';

import { useTranslation } from 'react-i18next';

import { MTableBody } from 'material-table';

import MenuButton from '../menu-button/MenuButton';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const module = props?.module ?? '';
    const moduleDisplayName = props?.moduleDisplayName ?? '';
    const moduleRecordId = props?.moduleRecordId ?? '';

    const [currentFolderId, setCurrentFolderId] = useState('');

    const [data, setData] = useState([]);
    const [openFolderState, setOpenFolderState] = useState(false);

    const [rootQuery, setRootQuery] = useState(props?.query ?? '');
    const [documentsQuery, setDocumentsQuery] = useState(props?.query ?? '');

    var lodash = require('lodash');

    let hearingsDocumentsQuery = lodash.cloneDeep(props?.query);
    let documents = [];

    let rootBreadCrumbVal = props?.query ? {
        name: getModulePrefix(moduleDisplayName, moduleRecordId),
        query: rootQuery
    } : '';

    const [rootBreadCrumb, setRootBreadCrumb] = useState(rootBreadCrumbVal);

    const [breadCrumbsItems, setBreadCrumbsItems] = useState([rootBreadCrumbVal]);

    const [fileVersions, setFileVersions] = useState([]);

    const [latestVersionFile, setLatestVersionFile] = useState('');

    const { t } = useTranslation();

    const gridColumns = [
        {
            title: t('actions'),
            cellStyle: {
                width: '5%'
            },
            render: rowData => rowData?.system_document == 0 ?
                <MenuButton
                    rowData={rowData}
                    handleRowActionsMenuClose={handleRowActionsMenuClose}
                /> : null
        },
        {
            title: t('name'),
            headerStyle: {
                minWidth: 200
            },
            cellStyle: {
                minWidth: 200
            },
            field: 'name',
            render: rowData =>
                <Button
                    className="document-icon-name-container"
                >
                    <div
                        className="document-icon-container"
                    >
                        <div
                            className="icon icon-sm"
                        >
                            {
                                rowData?.type == 'folder' ?
                                    <FolderIcon
                                        color="primary"
                                        className="icon folder-icon"
                                        onClick={(e) => rowData?.type == 'folder' ? openFolder(rowData, e) : downloadFile(rowData, e)}
                                    />
                                    :
                                    <FileIcon
                                        className="icon"
                                        onClick={(e) => rowData?.type == 'folder' ? openFolder(rowData, e) : downloadFile(rowData, e)}
                                        extension={rowData?.extension}
                                        {
                                        ...defaultStyles[rowData?.extension]
                                        }
                                    />
                            }

                        </div>
                    </div>
                    <div
                        className="document-name-container"
                    >
                        <Link
                            href="#"
                            onClick={(e) => rowData?.type == 'folder' ? openFolder(rowData, e) : downloadFile(rowData, e)}
                            title={rowData?.type == 'folder' ? "Open" : "Download"}
                            className="primary-link"
                            color="textPrimary"
                        >
                            {rowData?.name}
                        </Link>
                    </div>
                </Button>
        },
        {
            title: t('type'),
            field: 'document_type_id',
            headerStyle: {
                color: 'rgba(0, 0, 0, 0.54)'
            },
            cellStyle: {
                color: 'rgba(0, 0, 0, 0.54)'
            },
            render: rowData => rowData?.legal_case_document_type?.name
        },
        {
            title: t('status'),
            field: 'document_status_id',
            headerStyle: {
                color: 'rgba(0, 0, 0, 0.54)'
            },
            cellStyle: {
                color: 'rgba(0, 0, 0, 0.54)'
            },
            render: rowData => rowData?.legal_case_document_status?.name
        },
        {
            title: t('keywords'),
            headerStyle: {
                color: 'rgba(0, 0, 0, 0.54)'
            },
            cellStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
            },
            field: 'comment'
        },
        {
            title: t('size'),
            width: '1px',
            headerStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
            },
            cellStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
            },
            field: 'size',
            render: rowData => rowData?.size ? convertSize(parseInt(rowData?.size)) : null
        },
        {
            title: t('modified_by'),
            headerStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
                minWidth: 200,
            },
            cellStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
                minWidth: 200,
            },
            field: 'modifiedBy',
            render: rowData => rowData?.document_modifier?.user_profile !== null ? getAdvisorUserFullName(rowData?.document_modifier?.user_profile) : getAdvisorUserFullName(rowData?.document_modifier)
        },
        {
            title: t('modified_on'),
            headerStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
                minWidth: 200,
            },
            cellStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
                minWidth: 200,
            },
            field: 'modifiedOn',
            render: rowData => formatDateTime(new Date(rowData?.modifiedOn))
        },
        {
            title: t('created_by'),
            headerStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
                minWidth: 200,
            },
            cellStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
                minWidth: 200,
            },
            field: 'createdBy',
            render: rowData => rowData?.document_creator?.user_profile !== null ? getAdvisorUserFullName(rowData?.document_creator?.user_profile) : getAdvisorUserFullName(rowData?.document_creator)
        },
        {
            title: t('created_on'),
            headerStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
                minWidth: 200,
            },
            cellStyle: {
                color: 'rgba(0, 0, 0, 0.54)',
                minWidth: 200,
            },
            field: 'createdOn',
            render: rowData => formatDateTime(new Date(rowData?.createdOn))
        },
    ];

    useEffect(() => {

        loadData();
    }, [documentsQuery, openFolderState]);

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        Document.getList(documentsQuery).then((response) => {
            setData(response?.data?.data);
            
            documents = response?.data?.data;

            let currentFolderId = null;

            if (documentsQuery?.parentId) {
                currentFolderId = documentsQuery.parentId?.value;
            } else if (response?.data?.data?.[0]) {
                currentFolderId = response.data.data[0]?.parent;
            }

            setCurrentFolderId(currentFolderId);

            setFileVersions([]);
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
    }

    const handleRowActionsMenuClose = (action, document) => {
        switch (action) {
            case 'rename-folder':
                globalStateDispatcher({
                    modal: {
                        ...globalState?.modal,
                        title: t(FORMS_MODAL_TITLES.APDocumentEditFolderForm),
                        open: true,
                        form: {
                            ...globalState?.modal?.form,
                            id: FORMS_NAMES.APDocumentEditFolderForm,
                            submitCallback: loadData,
                            data: {
                                id: document?.id,
                                name: document?.name,
                                parentFolderId: currentFolderId,
                                module: module,
                                module_record_id: moduleRecordId
                            }
                        }
                    }
                });

                break;

            case 'delete-folder':
                deleteFolder(document);

                break;

            case 'download-file':
                downloadFile(document);

                break;

            case 'edit-file':
                editFile(document);
                break;

            case 'delete-file':
                deleteFile(document);

                break;

            case 'list-versions':
                setFileVersionsData(document);

                break;

            default:
                break;
        }
    };

    const openFolder = (folder, e) => {
        if (e) {
            e.preventDefault();
        }

        setOpenFolderState(true);

        let folderId = folder?.id;
        let initialQuery = props?.query;
        let query = {
            ...initialQuery,
            "parentId": {
                "value": folderId
            }
        };

        let breadCrumb = {
            name: folder?.name,
            query: query
        };

        setBreadCrumbsItems(pervState => [
            ...pervState,
            breadCrumb
        ]);

        setDocumentsQuery(query);
    }

    const downloadFile = (file, e) => {
        if (e) {
            e.preventDefault();
        }

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        Document.download(file?.id).then(response => {
            var fileDownload = require('js-file-download');

            const regex = /filename[^;=\n]*=(?:(\\?['"])(.*?)\1|(?:[^\s]+'.*?')?([^;\n]*))/ig;
            const str = response.headers['content-disposition'];

            let m;
            let numberOfMatches = str.match(regex).length;
            let counter = 0;

            while ((m = regex.exec(str)) !== null) {
                if (counter > 2) {
                    break;
                    return false;
                }

                // This is necessary to avoid infinite loops with zero-width matches
                if (m.index === regex.lastIndex) {
                    regex.lastIndex++;
                }

                // The result can be accessed through the `m`-variable.
                m.forEach((match, groupIndex) => {
                    console.log(`Found match, group ${groupIndex}: ${match}`);
                });

                // this is for UTF8
                if (numberOfMatches > 1 && counter > 0) {
                    fileDownload(response.data, decodeURI(m[3] ? m[3] : ''));
                } else if (numberOfMatches < 2 && counter < 1) {
                    fileDownload(response.data, m[2] ? m[2] : (m[3] ? m[3] : ''));
                }

                counter++;
            }
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
    }

    const deleteFolder = (folder) => {
        if (window.confirm("Are you sure you want to delete this folder?")) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Document.delete(folder?.id).then(response => {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: "Folder has been deleted successfully.",
                        severity: "success"
                    }
                });
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
                loadData();
            });
        }
    }

    const editFile = (file) => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.APDocumentEditFileForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.APDocumentEditFileForm,
                    submitCallback: loadData,
                    data: {
                        file: file
                    }
                }
            }
        });
    }

    const deleteFile = (file) => {
        if (window.confirm("Are you sure you want to delete this file?")) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Document.delete(file?.id).then(response => {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: "File has been deleted successfully.",
                        severity: "success"
                    }
                });
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
                loadData();
            });
        }
    }

    const setFileVersionsData = (file) => {
        setLatestVersionFile(file ?? '');

        setFileVersions(file?.file_versions_folder?.file_versions ?? []);
    }

    const handleBreadCrumbClick = (breadCrumbIndex, documentsQuery) => {
        let breadCrumbs = breadCrumbsItems.slice(0, breadCrumbIndex + 1);

        if (breadCrumbs.length == 1) {
            setOpenFolderState(false);
        }

        setBreadCrumbsItems(breadCrumbs);
        setDocumentsQuery(documentsQuery);
    };

    const [formData, setFormData] = useState({
        name: '',
        parent: currentFolderId,
        type: 'file',
        module: module,
        module_record_id: moduleRecordId,
    });

    const prepareRequestData = (data, file) => {
        let formData = new FormData();

        for (let [key, value] of Object.entries(data)) {
            if (value) {
                formData.append(key, value);
            }
        }

        formData.append("file", file);

        return formData;
    }

    const submit = (file) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        let requestData = prepareRequestData(formData, file);

        Document.create(requestData).then(response => {
            if (isFunction(globalState?.modal?.form?.submitCallback)) {
                globalState.modal.form.submitCallback();
            }

            globalStateDispatcher({
                modal: initialGlobalState?.modal
            });

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "File has been uploaded successfully.",
                    severity: "success"
                }
            });

            loadData();
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
            if (!isFunction(globalState?.modal?.form?.submitCallback)) {
                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            }
        });
    }

    const body = p => (
        <>
            <DropzoneContainer
                uploadFile={submit}
            />
            <MTableBody
                {...p}
            />
        </>
    );

    return (
        <Container
            maxWidth={false}
            className="ap-documents-container"
        >
            <ActionsToolbar
                breadCrumbsItems={breadCrumbsItems}
                handleBreadCrumbClick={handleBreadCrumbClick}
                loadDocuments={loadData}
                currentFolderId={currentFolderId}
                moduleRecordId={moduleRecordId}
                module={module}
            />
            <DocumentsGridTable
                columns={gridColumns}
                data={data}
                loadData={loadData}
                options={{
                    selection: false,
                    showTitle: false,
                }}
                body={body}
            />
            <FileVersionsContainer
                data={fileVersions}
                file={latestVersionFile}
                downloadFile={downloadFile}
                deleteFile={deleteFile}
            />
        </Container>
    );
});
