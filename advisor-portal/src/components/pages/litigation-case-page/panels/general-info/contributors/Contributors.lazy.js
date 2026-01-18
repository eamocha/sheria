import React, { lazy, Suspense } from 'react';

const LazyContributors = lazy(() => import('./Contributors'));

const Contributors = props => (
  <Suspense fallback={null}>
    <LazyContributors {...props} />
  </Suspense>
);

export default Contributors;
