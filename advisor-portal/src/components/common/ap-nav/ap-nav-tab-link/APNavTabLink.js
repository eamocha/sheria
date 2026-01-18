import React from 'react';

import './APNavTabLink.scss';

import { Tab } from '@material-ui/core';

import { Link } from 'react-router-dom';

export default React.memo((props) => {
    return (
        <Tab
            component={Link}
            {...props}
        />
    );
});
