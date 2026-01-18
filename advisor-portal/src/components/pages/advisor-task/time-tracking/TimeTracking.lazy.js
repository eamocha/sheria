import React, { lazy, Suspense } from 'react';

const LazyTimeTracking = lazy(() => import('./TimeTracking'));

const TimeTracking = props => (
  <Suspense fallback={null}>
    <LazyTimeTracking {...props} />
  </Suspense>
);

export default TimeTracking;
