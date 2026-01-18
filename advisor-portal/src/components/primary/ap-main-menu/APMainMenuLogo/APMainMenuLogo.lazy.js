import React, { lazy, Suspense } from 'react';

const LazyAPMainMenuLogo = lazy(() => import('./APMainMenuLogo'));

const APMainMenuLogo = props => (
  <Suspense fallback={null}>
    <LazyAPMainMenuLogo {...props} />
  </Suspense>
);

export default APMainMenuLogo;
