import React from 'react';
import './App.scss';
import './assets/styles/style.scss';
import './assets/styles/style-rtl.scss';
import AppLoader from './components/primary/app-loader/AppLoader.lazy';
import Store from './Store';

export default React.memo((props) => {
    return (
        <Store>
        <AppLoader></AppLoader>
        </Store >
    );
});
