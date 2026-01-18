import React, { lazy, Suspense } from 'react';

const LazyProfileForm = lazy(() => import('./ProfileForm'));

export default (props) => (
    <Suspense fallback={null}>
        <LazyProfileForm {...props} />
    </Suspense>
);
