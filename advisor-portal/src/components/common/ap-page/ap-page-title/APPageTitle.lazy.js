import React, { lazy, Suspense } from 'react';

const LazyAPPageTitle = lazy(() => import('./APPageTitle'));

const APPageTitle = props => (
  <Suspense fallback={null}>
    <LazyAPPageTitle {...props} />
  </Suspense>
);

export default APPageTitle;
