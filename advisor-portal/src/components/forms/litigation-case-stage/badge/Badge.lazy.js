import React, { lazy, Suspense } from 'react';

const LazyBadge = lazy(() => import('./Badge'));

const Badge = props => (
  <Suspense fallback={null}>
    <LazyBadge {...props} />
  </Suspense>
);

export default Badge;
