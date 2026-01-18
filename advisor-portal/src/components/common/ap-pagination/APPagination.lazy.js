import React, { lazy, Suspense } from 'react';

const LazyAPPagination = lazy(() => import('./APPagination'));

const APPagination = props => (
  <Suspense fallback={null}>
    <LazyAPPagination {...props} />
  </Suspense>
);

export default APPagination;
