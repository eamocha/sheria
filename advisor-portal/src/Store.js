import React from 'react';
import Reducer from "./Reducer";
import {
    createContext,
    useReducer,
} from "react";

export const initialGlobalState = {
    user: {
        loggedIn: sessionStorage.getItem("A4L-AP-accessToken") ? true : false,
        data: sessionStorage.getItem("A4L-AP-user") ? JSON.parse(sessionStorage.getItem("A4L-AP-user")) : {}
    },
    modal: {
        title: '',
        open: false,
        showSaveButton: true,
        form: {
            id: '',
            name: '',
            submitCallback: '',
            openedFromMainMenu: false,
            data: {},
            targetGrid: null
        }
    },
    globalLoader: {
        open: false
    },
    globalWalkThrough: {
        open: false
    },
    mainMenu: {
        activeTab: ''
    },
    notificationBar: {
        open: false,
        text: '',
        severity: ''
    },
    litigationCasePage: {
        currentId: '',
        activeNavPanelIndex: 0
    },
    afterActionCallbackFunction: '',
    domDirection: 'ltr',
    broadCastChannel: null,
    gridToReload: null,
    urlToGo: null
};

const Store = ({ children }) => {
    const [state, dispatch] = useReducer(Reducer, initialGlobalState);

    return (
        <Context.Provider
            value={[state, dispatch]}
        >
            {children}
        </Context.Provider>
    );
};

export const Context = createContext(initialGlobalState);
export default Store;
