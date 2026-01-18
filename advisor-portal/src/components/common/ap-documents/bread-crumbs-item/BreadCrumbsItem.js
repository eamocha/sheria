import React from 'react';

import './BreadCrumbsItem.scss';

import { Typography } from '@material-ui/core';

import { Link } from 'react-router-dom';

import FolderOpenIcon from '@material-ui/icons/FolderOpen';
 
export default React.memo((props) => {
    var name = props?.name;
    var breadCrumbQuery = props?.breadCrumbQuery ?? {};
    var firstBreadCrumb = props?.firstBreadCrumb ?? false;
    var lastBreadCrumb = props?.lastBreadCrumb ?? false;

    const goTo = (e) => {
        e.preventDefault();

        // let folderId = props?.id;
        // let initialQuery = props?.query;
        // let query = {
        //     ...initialQuery,
        //     "parentId": {
        //         "value": folderId
        //     }
        // };

        props.handleBreadCrumbClick(props?.index, breadCrumbQuery);
    }

    let content = <Link
        href="#"
        className="breadcrumbs-item"
        onClick={(e) => goTo(e)}
        title={"Go To " + name}
    >
        {name}
    </Link>;

    /**
     * the last breadcrumb (which is the current breadcrumb)
     * should not be clickable
     */
    if (lastBreadCrumb) {
        content = <Typography
            color="textPrimary"
            className="breadcrumbs-item"
        >
            {name}
        </Typography>;
    }

    /**
     * the first breadcrumb should have the Home Icon
     * and this code block should be always under the block of if (lastBreadCrumb)
     * because if there is only one breadCrumb, that means it's the first & the last at the same time
     * so in this order of the blocks, both cases will be covered
     */
    if (firstBreadCrumb) {
        content = <React.Fragment>
            <FolderOpenIcon
                className="vertical-align-middle"
            />
            {content}
        </React.Fragment>;
    }

    return content;
});
