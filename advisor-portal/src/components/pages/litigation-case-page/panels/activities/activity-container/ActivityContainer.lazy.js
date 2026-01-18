import React, { lazy, Suspense } from 'react';

const LazyActivityContainer = lazy(() => import('./ActivityContainer'));

const ActivityContainer = props => (
  <Suspense fallback={null}>
    <LazyActivityContainer {...props} />
  </Suspense>
);

export default ActivityContainer;
