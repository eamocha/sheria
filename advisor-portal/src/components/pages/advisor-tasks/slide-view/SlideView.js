import React, {
    useContext,
    useEffect,
} from 'react';

import './SlideView.scss';

import {
    Grid,
    Slide,
    Container,
} from '@material-ui/core';

import {
    SlideViewBody,
    SlideViewHeader
} from '../AdvisorTasksPageComponents';

import i18n from "i18next";
import { Context } from '../../../../Store';

export default React.memo((props) => {
    useEffect(() => {

    }, [props?.showSlideView]);

    const [globalState, globalStateDispatcher] = useContext(Context);

    return (
        <Slide
            direction={i18n.dir() == 'rtl' ? "right" : "left"}
            in={props?.showSlideView}
            mountOnEnter
            unmountOnExit
        >
            <Grid
                item
                container
                xs={4}
                id="advisor-task-details-slide-view"
                className={globalState.domDirection}
            >
                <Container
                    className="details-container"
                >
                    <SlideViewHeader
                        data={props?.data}
                        setShowSlideView={props.setShowSlideView}
                    />
                    <SlideViewBody
                        data={props?.data}
                    />
                </Container>
            </Grid>
        </Slide>
    );
});
