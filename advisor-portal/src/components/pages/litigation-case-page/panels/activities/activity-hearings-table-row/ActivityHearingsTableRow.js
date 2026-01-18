import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './ActivityHearingsTableRow.scss';

import {
    Container,
    Button,
    TableContainer,
    Table,
    TableHead,
    TableRow,
    TableCell,
    TableBody,
    Paper,
    makeStyles,
    Collapse,
    Link,
    IconButton,
    Grid
} from '@material-ui/core';

import {
    ExpandLess,
    ExpandMore
} from '@material-ui/icons';

import GetAppIcon from '@material-ui/icons/GetApp';

import ClearIcon from '@material-ui/icons/Clear';

// import HearingDocument from '../../../api-agent/HearingDocument';

// import Document from '../../../api-agent/Document';

import {
    concatStrings,
    addEllipsis,
    getAdvisorUserFullName,
    buildErrorMessages,
    isFunction
} from './../../../../../../APHelpers';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import Document from '../../../../../../api/Document';

import { HearingsTableRowMenu } from './../LitigationCasePageActivitiesPanel';

export default React.memo((props) => {
    const [hearing, setHearing] = useState(props?.hearing ?? '');
    const [title, setTitle] = useState('');
    const [isExpanded, setIsExpanded] = useState(props?.isExpanded ? props?.isExpanded === 0 ? false : true : true);

    const [globalState, globalStateDispatcher] = useContext(Context);

    const downloadDocument = (id) => {

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        Document.download(id).then(response => {
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

    const deleteDocument = (id) => {
        if (window.confirm("Are you sure you want to delete this document?")) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Document.delete(id).then(response => {
                globalStateDispatcher({
                    globalLoader: {
                        ...globalState?.globalLoader,
                        open: false
                    }
                });

                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: "Hearing Document has been deleted successfully",
                        severity: "success"
                    }
                });

                if (isFunction(props?.afterActionReloadFunction)) {
                    props.afterActionReloadFunction();
                }
            }).catch(error => {
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
                    globalLoader: {
                        ...globalState?.globalLoader,
                        open: false
                    }
                });
            });
        }
    }

    let hearingType = '';

    if (hearing?.hearing_type) {
        for (var i = 0; i < hearing.hearing_type?.hearing_type_languages?.length; i++) {
            let type = hearing.hearing_type?.hearing_type_languages[i];

            if (type?.language_id === 1) {
                hearingType = type;
                break;
            }
        }
    }

    let assignees = '';

    if (hearing?.assignees) {
        assignees = hearing?.assignees.map(item => {
            if (item) {
                return getAdvisorUserFullName(item.user, item.user_type != "A4L");
            }
            return null;
        }).join(', ');
    }

    let documents = '';

    if (hearing?.hearing_documents) {
        documents = hearing.hearing_documents.map((item, documentsKey) => {
            let document = item?.document;
            if (document) {
                return (
                    <Grid
                        key={"litigation-case-hearing-table-record-documents-" + documentsKey}
                        container
                    // className={classes.hearingDocumentContainer}
                    >
                        <Grid
                            // className={classes.hearingDocumentNameContainer}
                            item
                            sm={9}
                        >
                            <Link
                                // className={classes.hearingDocumentName}
                                key={"hearing-" + item?.id + "-document-" + document?.id}
                                title="Download Document"
                                onClick={() => downloadDocument(document?.id)}
                            >
                                {concatStrings([
                                    document?.name ?? '',
                                    document?.extension ?? ''
                                ], '.')}
                                &nbsp;
                                <GetAppIcon
                                    fontSize="small"
                                />
                            </Link>
                        </Grid>
                        <Grid
                            // className={classes.hearingDocumentDeleteBtnContainer}
                            item
                            sm={3}
                        >
                            <IconButton
                                // className={classes.hearingDocumentDeleteBtn}
                                color="secondary"
                                title="Delete Document"
                                onClick={() => deleteDocument(document?.id)}
                            >
                                <ClearIcon
                                // className={classes.hearingDocumentDeleteBtnIcon}
                                />
                            </IconButton>
                        </Grid>
                    </Grid>
                );
            }

            return null;
        });
    }

    return (
        <TableRow>
            <TableCell>
                {/* <HearingRecordDropDownMenu
                    matter={props.matter}
                    currentStage={props.stage}
                    hearing={item}
                    setActiveFormModal={props.setActiveFormModal}
                    setActiveFormModelData={props.setActiveFormModelData}
                    setFormModalState={props.setFormModalState}
                    setAfterActionReloadFunction={props.setAfterActionReloadFunction}
                    afterActionReloadFunction={props.afterActionReloadFunction}
                    classes={classes}
                    rowkey={key}
                    setGlobalLoader={props.setGlobalLoader}
                    setNotificationBarText={props.setNotificationBarText}
                    setNotificationBarSeverity={props.setNotificationBarSeverity}
                    setNotificationBarState={props.setNotificationBarState}
                /> */}
                <HearingsTableRowMenu
                    hearing={hearing}
                    litigationCase={props?.litigationCase}
                    loadActivities={props?.loadActivities}
                />
            </TableCell>
            <TableCell>
                {concatStrings([hearing?.startDate, hearing?.startTime])}
            </TableCell>
            <TableCell>
                {concatStrings([hearing?.postponedDate, hearing?.postponedTime])}
            </TableCell>
            <TableCell>
                {addEllipsis(hearing?.reasons_of_postponement)}
            </TableCell>
            <TableCell>
                {hearingType?.name}
            </TableCell>
            <TableCell>
                {assignees}
            </TableCell>
            <TableCell>
                {addEllipsis(hearing?.summary)}
            </TableCell>
            <TableCell>
                {addEllipsis(hearing?.judgment)}
            </TableCell>
            <TableCell>
                {addEllipsis(hearing?.comments)}
            </TableCell>
            <TableCell>
                {documents}
            </TableCell>
        </TableRow>
    );
});
