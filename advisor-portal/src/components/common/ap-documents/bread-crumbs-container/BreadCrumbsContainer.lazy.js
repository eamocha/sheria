import React, { lazy, Suspense } from 'react';

const LazyBreadCrumbsContainer = lazy(() => import('./BreadCrumbsContainer'));

const BreadCrumbsContainer = props => (
  <Suspense fallback={null}>
    <LazyBreadCrumbsContainer {...props} />
  </Suspense>
);

export default BreadCrumbsContainer;
