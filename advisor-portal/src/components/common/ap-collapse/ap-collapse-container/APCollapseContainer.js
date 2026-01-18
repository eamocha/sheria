import React, { useState } from 'react';

import './APCollapseContainer.scss';

import { 
    Container,
    Button,
    Collapse,
    makeStyles
} from '@material-ui/core';

import { 
    ExpandMore,
    ExpandLess
} from '@material-ui/icons';

const useStyles = makeStyles({
    container: {
        paddingLeft: 0,
        paddingRight: 0,
        marginBottom: 25
    },
    collapse: {
        paddingLeft: 20
    },
    btn: {
        paddingLeft: 2
    }
});

export default React.memo((props) => {
    const [expanded, setExpanded] = useState(props?.expanded ? props.expanded == 1 : false);

    const classes = useStyles();

    return (
        <Container
            maxWidth={false}
            {...props}
            classes={{root: classes.container}}
        >
            <Button
                color="primary"
                onClick={() => setExpanded(prevState => !prevState)}
                classes={{root: classes.btn}}
            >
                {expanded ? <ExpandLess /> : <ExpandMore />} {props?.title}
            </Button>
            <Collapse
                in={expanded}
                classes={{wrapperInner: classes.collapse}}
            >
                {props?.children}
            </Collapse>
        </Container>
    );
});
