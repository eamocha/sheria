import React, { lazy, Suspense } from 'react';

const LazyActivityDetailsRow = lazy(() => import('./ActivityDetailsRow'));

const ActivityDetailsRow = props => (
  <Suspense fallback={null}>
    <LazyActivityDetailsRow {...props} />
  </Suspense>
);

export default ActivityDetailsRow;
