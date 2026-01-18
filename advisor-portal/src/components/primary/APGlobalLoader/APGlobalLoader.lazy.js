import React, { lazy, Suspense } from 'react';

const LazyAPGlobalLoader = lazy(() => import('./APGlobalLoader'));

const APGlobalLoader = props => (
  <Suspense fallback={null}>
    <LazyAPGlobalLoader {...props} />
  </Suspense>
);

export default APGlobalLoader;
