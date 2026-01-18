import React from 'react';
import './APMainMenuLogo.scss';
import {
    Button,
    ListItem
} from '@material-ui/core';
import { Link } from 'react-router-dom';
import logo from './../../../../assets/images/app4legal-logo-white.png';
import { buildInstanceURL } from '../../../../APHelpers';
 
export default React.memo((props) => {

    return (
        <ListItem
            className="AP-main-menu-logo"
        >
            <Button
                classes={{ root: "AP-main-menu-logo-btn" }}
            >
                <Link
                    to={`${buildInstanceURL()}/`}
                    className="default-link"
                >
                    <img
                        className="AP-main-menu-logo-img"
                        src={logo}
                        alt="Dashboard"
                    />
                </Link>
            </Button>
        </ListItem>
    );
});
