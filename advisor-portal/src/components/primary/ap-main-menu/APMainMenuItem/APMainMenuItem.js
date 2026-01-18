import React, { useContext } from 'react';
import './APMainMenuItem.scss';
import { IconButton, ListItem } from '@material-ui/core';
import { Link } from 'react-router-dom';
import { Context } from '../../../../Store';
import { buildInstanceURL } from '../../../../APHelpers';
 
export default React.memo((props) => {

    const [globalState, ] = useContext(Context);

    return (
        <ListItem
            classes={{ root: "AP-main-menu-list-item" + (globalState?.mainMenu?.activeTab === props?.tabName ? " active" : "") }}
        >
            <IconButton
                classes={{ root: "AP-main-menu-list-item-icon-btn", label: "AP-main-menu-list-item-icon-label" }}
                edge="start"
                color="inherit"
            >
                <Link
                    to={`${buildInstanceURL()}/${props?.link}`}
                    className={"AP-main-menu-list-item-link default-link" + (globalState?.mainMenu?.activeTab === props?.tabName ? " active" : "")}
                >
                    {props?.icon} {props?.title}
                </Link>
            </IconButton>
        </ListItem>
    );
});
