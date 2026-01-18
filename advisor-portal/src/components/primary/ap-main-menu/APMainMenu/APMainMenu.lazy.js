import React, { lazy, Suspense } from 'react';

const LazyAPMainMenu = lazy(() => import('./APMainMenu'));

const APMainMenu = props => (
  <Suspense fallback={null}>
    <LazyAPMainMenu {...props} />
  </Suspense>
);

export default APMainMenu;
