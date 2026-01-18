import React, { lazy, Suspense } from 'react';

const LazyAPMainMenuAddItem = lazy(() => import('./APMainMenuAddItem'));

const APMainMenuAddItem = props => (
  <Suspense fallback={null}>
    <LazyAPMainMenuAddItem {...props} />
  </Suspense>
);

export default APMainMenuAddItem;
