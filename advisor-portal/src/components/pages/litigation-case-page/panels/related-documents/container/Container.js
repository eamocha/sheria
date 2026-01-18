import React, {
    useContext,
    useEffect,
} from 'react';

import './Container.scss';

import { Container } from '@material-ui/core';

import {
    Context,
} from './../../../../../../Store';

import { MAIN_MENU_TABS_NAMES } from './../../../../../../Constants';

import {
    Container as APDocumentsContainer
} from './../../../../../common/ap-documents/APDocuments';
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    useEffect(() => {
        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.litigationCases
            }
        });
    }, [props?.litigationCase]);

    return (
        <Container
            id="litigation-case-page-related-documents-panel-container"
            maxWidth={false}
            className="no-padding-h"
        >
            <APDocumentsContainer
                module="case"
                moduleDisplayName="legal-case"
                moduleRecordId={props?.litigationCase?.id}
                query={{
                    "module": {
                        "value": "case"
                    },
                    "moduleRecordId": {
                        "value": props?.litigationCase?.id
                    },
                    "parentId": {
                        "value": props?.litigationCase?.root_folder?.id
                    }
                }}
            />
        </Container>
    );
});
