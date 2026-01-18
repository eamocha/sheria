import React, {
    useContext,
    useEffect,
    useState
} from "react";

import './Activity.scss';

import {
    Button,
    Container,
    Grid,
} from '@material-ui/core';

import { APCollapseContainer } from './../../../common/ap-collapse/APCollapse';

import { isFunction } from './../../../../APHelpers';

import APNoteRow from "./../../../common/ap-notes/ap-note-row/APNoteRow.lazy";

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from './../../../../Constants';

import { Context, initialGlobalState } from "./../../../../Store";

import AdvisorTaskComment from "../../../../api/AdvisorTaskComment";
import { useTranslation } from "react-i18next";
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const [notes, setNotes] = useState(props?.advisorTask?.comments ?? []);

    const { t } = useTranslation();

    useEffect(() => {

        setNotes(props?.advisorTask?.comments);
    }, [props?.advisorTask]);

    const openAddNoteForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.advisorTaskNoteAddForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.AdvisorTaskNoteAddForm,
                    submitCallback: isFunction(props?.loadAdvisorTaskData) ? props.loadAdvisorTaskData : null,
                    data: {
                        advisorTask: props?.advisorTask
                    }
                }
            }
        });
    }

    const editNoteFormData = (commentData) => {
        return {
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.advisorTaskNoteEditForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.AdvisorTaskNoteEditForm,
                    submitCallback: isFunction(props?.loadAdvisorTaskData) ? props.loadAdvisorTaskData : null,
                    data: {
                        advisorTask: props?.advisorTask
                    }
                }
            }
        };
    }

    let notesContent = notes?.map((item, key) => {
        return (
            <APNoteRow
                key={'comment-row-' + key}
                loadData={props.loadAdvisorTaskData}
                commentData={item}
                advisorTask={props?.advisorTask}
                isAdvisorComment={true}
                isAdvisorTaskComment={true}
                model={new AdvisorTaskComment()}
                editNoteFormData={editNoteFormData}
            />
        )
    });

    return (
        <APCollapseContainer
            title={t("activity")}
        >
            <Container
                id="advisor-task-notes"
                maxWidth={false}
                className="section no-padding-h"
            >
                <Container
                    maxWidth={false}
                    className="no-padding-h"
                >
                    {notesContent?.length > 0 ? notesContent : <p>{t("No Notes to display.")}</p>}
                </Container>
                <Container
                    maxWidth={false}
                    container
                    className="no-padding-h"
                >
                    <Grid
                        container
                        className="no-padding-h"
                    >
                        <Grid
                            item
                            sm="3"
                        >
                            <Button
                                color="primary"
                                variant="outlined"
                                className="add-btn"
                                onClick={() => openAddNoteForm()}
                            >
                                {t("add_note")}
                            </Button>
                        </Grid>
                        {/* <Grid
                            item
                            sm="9"
                            className="d-flex justify-content-end"
                        >
                            <APPagination
                                numberOfItems={notes.length}
                            />
                        </Grid> */}
                    </Grid>
                </Container>
            </Container>
        </APCollapseContainer>
    );
});
