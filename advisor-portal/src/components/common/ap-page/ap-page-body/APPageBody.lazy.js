import React, { lazy, Suspense } from 'react';

const LazyAPPageBody = lazy(() => import('./APPageBody'));

const APPageBody = props => (
  <Suspense fallback={null}>
    <LazyAPPageBody {...props} />
  </Suspense>
);

export default APPageBody;
