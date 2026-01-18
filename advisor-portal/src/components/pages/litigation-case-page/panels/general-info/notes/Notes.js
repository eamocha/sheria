import React, {
    useContext,
    useEffect,
    useState
} from "react";

import './Notes.scss';

import {
    Button,
    Container,
    Grid,
    LinearProgress
} from '@material-ui/core';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import APPagination from './../../../../../common/ap-pagination/APPagination.lazy';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../../../Constants';

import { isFunction } from '../../../../../../APHelpers';

import APNoteRow from "../../../../../common/ap-notes/ap-note-row/APNoteRow";

import LegalCaseComment from './../../../../../../api/LegalCaseComment';
import { useTranslation } from "react-i18next";

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [query, setQuery] = useState({
        'legalCaseId': {
            'value': props?.litigationCase?.id
        }
    });

    const { t } = useTranslation();

    const [notes, setNotes] = useState([]);

    const [dataLoaded, setDataLoaded] = useState(false);

    useEffect(() => {

        setDataLoaded(false);

        loadData();
    }, [props?.litigationCase]);

    const loadData = () => {
        LegalCaseComment.getList(query).then((response) => {

            setNotes(response?.data?.data);

        }).catch((error) => {

            console.log("litigation case page general info panel notes", error);
        }).finally(() => {

            setDataLoaded(true);
        });
    }

    const openAddNoteForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.legalCaseNoteAddForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.legalCaseNoteAddForm,
                    submitCallback: isFunction(props?.loadLitigationCaseData) ? props.loadLitigationCaseData : null,
                    data: {
                        legalCase: props?.litigationCase
                    }
                }
            }
        });
    }

    const editNoteFormData = (commentData) => {
        return {
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.legalCaseNoteEditForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.legalCaseNoteEditForm,
                    submitCallback: isFunction(props?.loadLitigationCaseData) ? props.loadLitigationCaseData : null,
                    data: {
                        legalCase: props?.litigationCase
                    }
                }
            }
        };
    }

    if (!dataLoaded) {
        return (
            <LinearProgress />
        );
    }

    let notesContent = notes.map((item, key) => {
        return (
            <APNoteRow
                key={'comment-row-' + key}
                loadData={props?.loadLitigationCaseData}
                commentData={item}
                legalCase={props?.legalCase}
                editNoteFormData={editNoteFormData}
                model={new LegalCaseComment()}
            />
        )
    });


    return (
        <Container
            id="litigation-case-page-general-info-panel-notes"
            maxWidth={false}
            className="section no-padding-h"
        >
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                {notesContent}
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
    );
});
