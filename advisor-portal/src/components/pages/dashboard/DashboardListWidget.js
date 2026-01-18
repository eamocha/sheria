import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import {
    Container,
    Avatar,
    makeStyles,
    Grid,
    Typography,
    IconButton
} from '@material-ui/core';
import AdvisorTask from './../../../api/AdvisorTask';
import Hearing from './../../../api/Hearing';
import { Link } from 'react-router-dom';
import DoubleArrowIcon from '@material-ui/icons/DoubleArrow';
import './DashboardWidget.scss';
import DashboardListWidgetRow from './DashboardListWidgetRow';
import { useTranslation } from 'react-i18next';
import { Context } from '../../../Store';
import VisibilityOffIcon from '@material-ui/icons/VisibilityOff';
import { buildInstanceURL } from '../../../APHelpers';

const useStyles = makeStyles({
    counterAvatarContainer: {
        padding: '0px',
        margin: '0',
        border: '2px solid #fff',
        flex: '0',
        borderRadius: '100%'
    },
    defaultCounterAvatar: {
        color: '#fff',
        backgroundColor: '#205081',
        width: '32px',
        height: '32px',
        fontSize: '16px',
        fontWeight: '600'
    },
    doubleArrows: {
        width: '.75em',
        height: '.75em'
    }
});

const DashboardListWidget = React.memo((props) => {
    const [title, setTitle] = useState(props?.widgetTitle);
    const [model, setModel] = useState(props?.model);
    const [query, setQuery] = useState(props?.query);
    const [data, setData] = useState([]);
    const { t } = useTranslation();
    const widgetId = props?.widgetId;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const maxRows = 5;

    useEffect(() => {
        setTitle(props?.widgetTitle)
    }, [t]);

    useEffect(() => {

        loadData();
    }, [props?.query]);

    const loadData = () => {
        if (model == 'Hearing') {
            Hearing.getList(query).then((response) => {

                setData(response.data.data);
            }).catch((errors) => {

            });
        } else {
            AdvisorTask.getList(query).then((response) => {

                setData(response.data.data);
            }).catch((errors) => {

            });
        }
    }

    const classes = useStyles();

    if (!data) {
        return null;
    }

    let rows = data.slice(0, maxRows).map((item, key) => {
        return <DashboardListWidgetRow
            key={key}
            rowData={item}
            model={model}
        />
    });

    return (
        <Container
            className="dashboard-widget dashboard-list-widget no-padding-h"
        >
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                <Grid
                    container
                    className="dashboard-widget-header"
                >
                    <Grid
                        sm="8"
                        className="dashboard-widget-title-container"
                    >
                        <Typography
                            className="dashboard-widget-title"
                        >
                            {title}
                        </Typography>
                    </Grid>
                    <IconButton
                        className={"hide-wideget-btn close-button " + globalState.domDirection}
                        onClick={(e) => props?.hideWidget(props?.name)}
                        size="small"
                        title={t("hide_widget")}
                        color="inherit"
                    >
                        <VisibilityOffIcon
                            fontSize="small"
                        />
                    </IconButton>
                    <Grid
                        sm="2"
                        className={"d-flex align-items-center small-circle " + globalState.domDirection}
                    >
                        <Container
                            className={classes.counterAvatarContainer + " d-flex align-items-center justify-content-end"}
                        >
                            <Avatar
                                className={classes.defaultCounterAvatar}
                                component='span'
                            >
                                {data.length <= maxRows ? data.length : maxRows + '+'}
                            </Avatar>
                        </Container>
                    </Grid>
                </Grid>
            </Container>
            <Container
                maxWidth={false}
                className="dashboard-list-widget-body no-padding-h"
            >
                {rows.length > 0 ? rows : <p className="no-records"> {t("no_records_to_display")} </p>}
            </Container>
            <Container
                maxWidth={false}
                className="dashboard-list-widget-footer no-padding-h"
            >
                <Link
                    to={model == 'Hearing' ? `${buildInstanceURL()}/hearings` : `${buildInstanceURL()}/tasks/` + widgetId}
                    className={"view-all link gray-link"}
                >
                    <div
                        className="d-flex align-items-center justify-content-space-between"
                    >
                        <Typography
                            variant="body1"
                        >
                            {t("view_all")}
                        </Typography>
                        <DoubleArrowIcon
                            className={classes.doubleArrows}
                        />
                    </div>
                </Link>
            </Container>
        </Container>
    );
});

export default DashboardListWidget;
