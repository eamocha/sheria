import React, { lazy, Suspense } from 'react';

const LazyActionsToolbar = lazy(() => import('./ActionsToolbar'));

const ActionsToolbar = props => (
  <Suspense fallback={null}>
    <LazyActionsToolbar {...props} />
  </Suspense>
);

export default ActionsToolbar;
