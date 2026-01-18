import React, { lazy, Suspense } from 'react';

const LazyAPStatusBadge = lazy(() => import('./APStatusBadge'));

const APStatusBadge = props => (
  <Suspense fallback={null}>
    <LazyAPStatusBadge {...props} />
  </Suspense>
);

export default APStatusBadge;
