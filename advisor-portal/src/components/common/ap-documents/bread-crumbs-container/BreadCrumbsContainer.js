import React, {
    useEffect,
    useState
} from 'react';

import './BreadCrumbsContainer.scss';

import {
    Container,
    Breadcrumbs
} from '@material-ui/core';

import {
    BreadCrumbsItem
} from './../APDocuments';
 
export default React.memo((props) => {
    const [breadCrumbsContent, setBreadCrumbsContent] = useState([]);

    useEffect(() => {

        buildBreadCrumbs();
    }, [props?.items]);

    const buildBreadCrumbs = () => {
        let breadCrumbs = props?.items?.map((item, key) => {
            let firstItem = key == 0;
            let lastItem = props.items.length == (key + 1);

            return (
                <BreadCrumbsItem
                    key={"breadcrumb-" + key}
                    index={key}
                    id={item?.id}
                    breadCrumbQuery={item?.query}
                    name={item?.name}
                    firstBreadCrumb={firstItem}
                    lastBreadCrumb={lastItem}
                    handleBreadCrumbClick={props?.handleBreadCrumbClick}
                />
            )
        });

        setBreadCrumbsContent(breadCrumbs);
    };
    
    return (
        <Container
            className="breadcrumbs-container d-flex align-items-center justify-content-start"
        >
            <Breadcrumbs
                className="breadcrumbs-items-wrapper"
            >
                {breadCrumbsContent}
            </Breadcrumbs>
        </Container>
    );
});
