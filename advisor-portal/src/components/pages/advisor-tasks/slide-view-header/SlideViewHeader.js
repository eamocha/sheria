import React, { useContext, useState } from 'react';

import './SlideViewHeader.scss';

import {
    Button,
    Container,
    Grid,
    IconButton,
    Typography
} from '@material-ui/core';

import { Link } from 'react-router-dom';

import {
    ExpandLess,
    ExpandMore,
} from '@material-ui/icons';

import CloseIcon from '@material-ui/icons/Close';

import { addEllipsis } from '../../../../APHelpers';

import APStatusBadge from '../../../common/ap-status-badge/APStatusBadge';

import { PUBLIC_URL } from '../../../../Constants';
import { Context } from '../../../../Store';

export default React.memo((props) => {
    const advisorTaskStatusCategory = props?.data?.advisor_task_status?.['category'];

    let headerBorderBgColor = advisorTaskStatusCategory ? (advisorTaskStatusCategory === "open" ? "border-blue" : (advisorTaskStatusCategory === "in progress" ? "border-yellow" : "border-green")) : '';

    const [showFullDescription, setShowFullDescription] = useState(false);
    const [globalState, globalStateDispatcher] = useContext(Context);

    const closeSlideView = () => {
        props.setShowSlideView(false);
    }

    return (
        <Container
            className={"slide-view-header " + headerBorderBgColor}
        >
            <Container
                className="title-container"
            >
                <Grid
                    container
                >
                    <Grid
                        item
                        xs={12}
                    >
                        <Link
                            to={`${PUBLIC_URL}/task/${props?.data?.id}`}
                            className="primary-link"
                        >
                            <Typography
                                variant="h6"
                            >
                                {'T' + props?.data?.id}
                            </Typography>
                        </Link>
                        <Typography
                            variant="h6"
                            className="task-description"
                        >
                            {showFullDescription ? props?.data?.description : addEllipsis(props?.data?.description)}
                            {
                                props?.data?.description?.length > 100 ?
                                <Button
                                    variant="text"
                                    color="primary"
                                    onClick={() => setShowFullDescription(prevState => !prevState)}
                                >
                                    {
                                        showFullDescription ?
                                        <React.Fragment>
                                            <ExpandLess /><span>Collapse</span>
                                        </React.Fragment>
                                        :
                                        <React.Fragment>
                                            <ExpandMore /><span>Expand</span>
                                        </React.Fragment>
                                    }
                                </Button>
                                :
                                null
                            }
                        </Typography>
                        <Typography
                            variant="caption"
                            className="task-type"
                        >
                            <span
                                className="task-type"
                            >
                                {props?.data?.advisor_task_type.advisor_task_type_languages[0].name}
                            </span>
                        </Typography>
                    </Grid>
                </Grid>
                {
                    typeof props?.data?.legal_case !== 'undefined' && props?.data?.legal_case !== null ?
                        <Typography
                            variant="body1"
                        >
                            <Grid
                                container
                            >
                                <Grid
                                    item
                                    sm={6}
                                >
                                    <span>Workflow Status: </span>
                                </Grid>
                                <Grid
                                    item
                                    sm={6}
                                >
                                    <APStatusBadge
                                        status={props?.data?.advisor_task_status}
                                    />
                                </Grid>
                            </Grid>
                        </Typography>
                        :
                        null
                }
                <div
                    className={"text-right close " + globalState.domDirection}
                >
                    <IconButton
                        onClick={() => closeSlideView()}
                    >
                        <CloseIcon />
                    </IconButton>
                </div>
            </Container>
        </Container>
    );
});
