import React, { lazy, Suspense } from 'react';

const LazyBreadCrumbsItem = lazy(() => import('./BreadCrumbsItem'));

const BreadCrumbsItem = props => (
  <Suspense fallback={null}>
    <LazyBreadCrumbsItem {...props} />
  </Suspense>
);

export default BreadCrumbsItem;
