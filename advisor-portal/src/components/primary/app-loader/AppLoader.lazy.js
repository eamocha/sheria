import React, { lazy, Suspense } from 'react';

const LazyAppLoader = lazy(() => import('./AppLoader'));

const AppLoader = props => (
  <Suspense fallback={null}>
    <LazyAppLoader {...props} />
  </Suspense>
);

export default AppLoader;
