import React, { lazy, Suspense } from 'react';

const LazyAPPageActionsToolbar = lazy(() => import('./APPageActionsToolbar'));

const APPageActionsToolbar = props => (
  <Suspense fallback={null}>
    <LazyAPPageActionsToolbar {...props} />
  </Suspense>
);

export default APPageActionsToolbar;
