import React, {
    useContext,
    useState
} from 'react';
import './APNoteRow.scss';
import {
    Container,
    Button,
    Collapse,
    makeStyles,
    Typography,
    Grid,
    IconButton
} from '@material-ui/core';
import {
    ExpandMore,
    ExpandLess
} from '@material-ui/icons';
import DeleteIcon from '@material-ui/icons/Delete';
import EditIcon from '@material-ui/icons/Edit';
import { format } from 'date-fns';
import { buildErrorMessages, getAdvisorUserFullName, isFunction } from '../../../../APHelpers';
import parse from 'html-react-parser';
import { Context, initialGlobalState } from '../../../../Store';
import { FORMS_MODAL_TITLES, FORMS_NAMES } from '../../../../Constants';

const useStyles = makeStyles({
    container: {
        paddingLeft: 0,
        paddingRight: 0,
        marginBottom: 30,
        borderBottom: '1px solid #e6e6e6',
        '&:last-child': {
            borderBottom: 'none'
        }
    },
    collapse: {
        paddingLeft: 20
    },
    btn: {
        color: '#000',
        fontWeight: '100',
        paddingLeft: 2
    }
});

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [expanded, setExpanded] = useState(props?.expanded ? props.expanded == 1 : true);

    var isAdvisorComment = props?.commentData?.createdByChannel == 'AP';;
    var isAdvisorTaskComment = props?.isAdvisorTaskComment;

    var commentCreatorId = props?.commentData?.id;
    var commentCreatorName = getAdvisorUserFullName(props?.commentData?.comment_creator, props?.commentData?.createdByChannel == 'AP');
    var commentDate = props?.commentData?.createdOn ? format(new Date(props?.commentData?.createdOn), 'y-MM-dd HH:mm') : null;
    var commentTitle = commentCreatorName + ' added a note on ' + commentDate;

    const deleteNote = () => {
        if (window.confirm("Are you sure you want to delete this note?")) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            props.model.constructor.delete(props?.commentData?.id).then((response) => {
                if (isFunction(props?.loadData)) {
                    props.loadData();
                }

                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        severity: "success",
                        text: "Note has been deleted successfully!"
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
                    },
                    globalLoader: initialGlobalState?.globalLoader
                });
            }).finally(() => {
                if (!isFunction(props?.loadData)) {
                    globalStateDispatcher({
                        globalLoader: globalState?.globalLoader
                    });
                }
            });
        }
    }

    const editNote = () => {
        let editNoteFormData = props.editNoteFormData();

        editNoteFormData.modal.form.data['commentData'] = props?.commentData;

        globalStateDispatcher(editNoteFormData);
    }

    const classes = useStyles();

    if (!commentCreatorName || !commentDate) {
        return null;
    }

    return (
        <Container
            maxWidth={false}
            className={classes.container}
        >
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                <Grid
                    container
                    className="no-padding-h"
                >
                    <Grid
                        item
                        xs={8}
                    >
                        <Button
                            color="primary"
                            onClick={() => setExpanded(prevState => !prevState)}
                            classes={{ root: classes.btn }}
                        >
                            {expanded ? <ExpandLess /> : <ExpandMore />} {commentTitle}
                        </Button>
                    </Grid>
                    <Grid
                        item
                        container
                        xs={4}
                        alignItems="center"
                        justify="flex-end"
                    >
                        {
                            !isAdvisorTaskComment || (isAdvisorComment && commentCreatorId != globalState?.user?.id) ?
                                <IconButton
                                    aria-label="Edit"
                                    title="Edit"
                                    size="small"
                                    onClick={() => editNote()}
                                >
                                    <EditIcon />
                                </IconButton>
                                : null
                        }
                        {
                            isAdvisorComment && commentCreatorId != globalState?.user?.id ?
                                <IconButton
                                    aria-label="Delete"
                                    title="Delete"
                                    size="small"
                                    color="error"
                                    onClick={() => deleteNote()}
                                >
                                    <DeleteIcon
                                        color="error"
                                    />
                                </IconButton>
                                :
                                null
                        }
                    </Grid>
                </Grid>
            </Container>
            <Collapse
                in={expanded}
                classes={{ wrapperInner: classes.collapse }}
            >
                {parse(props?.commentData?.comment)}
            </Collapse>
        </Container>
    );
});
