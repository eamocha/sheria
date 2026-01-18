import React, { lazy, Suspense } from 'react';

const LazyPageNotFound = lazy(() => import('./PageNotFound'));

const PageNotFound = props => (
  <Suspense fallback={null}>
    <LazyPageNotFound {...props} />
  </Suspense>
);

export default PageNotFound;
