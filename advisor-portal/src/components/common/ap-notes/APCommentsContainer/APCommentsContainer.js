import React, { useContext } from 'react';
import './APCommentsContainer.scss';
import {
    Button,
    Container,
    Grid
} from '@material-ui/core';
import {
    useEffect,
    useState
} from "react";
import { Context } from '../../../Store';
import APPagination from '../APPagination/APPagination';
import APCommentRow from '../APCommentRow/APCommentRow';
import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../Constants';
import { isFunction } from '../../../APHelpers';

export default React.memo((props) => {

    const [model] = useState(props?.model);
    const [query, setQuery] = useState(props?.query ? props.query : {});
    const [comments, setComments] = useState([]);
    const [dataLoaded, setDataLoaded] = useState(false);

    const [globalState, globalStateDispatcher] = useContext(Context);

    useEffect(() => {

        loadData();
    }, [
        props?.query,
        props?.model
    ]);

    const loadData = () => {
        model.constructor.getList(query).then((response) => {

            setComments(response?.data?.data);
            setDataLoaded(true);
        }).catch((error) => {

            setDataLoaded(true);
        });
    }

    const openAddCommentForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: props?.modelName == "legalCase" ? t(FORMS_MODAL_TITLES.legalCaseNoteAddForm) : null,
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: props?.modelName == "legalCase" ? FORMS_NAMES.legalCaseNoteAddForm : "",
                    submitCallback: props?.modelName == "legalCase" ? (isFunction(props?.loadData) ? props.loadData : null) : null,
                    data: {
                        legalCase: props?.legalCase
                    }
                }
            }
        });
    }

    let commentsContent = comments.map((item, key) => {
        return (
            <APCommentRow
                key={'comment-row-' + key}
                model={model}
                loadData={props?.loadData}
                commentData={item}
                legalCase={props?.legalCase}
            />
        )
    });

    if (!dataLoaded) {
        return null;
    }

    return (
        <Container
            maxWidth={false}
            className="no-padding-h"
        >
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                {commentsContent}
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
                            color="secondary"
                            variant="outlined"
                            onClick={() => openAddCommentForm()}
                        >
                            Add Note
                        </Button>
                    </Grid>
                    <Grid
                        item
                        sm="9"
                        className="d-flex justify-content-end"
                    >
                        <APPagination
                            numberOfItems={comments.length}
                        />
                    </Grid>
                </Grid>
            </Container>
        </Container>
    );
});
