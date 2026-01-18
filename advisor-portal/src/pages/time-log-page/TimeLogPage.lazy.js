import React, { lazy, Suspense } from 'react';

const LazyTimeLogPage = lazy(() => import('./TimeLogPage'));

const TimeLogPage = props => (
  <Suspense fallback={null}>
    <LazyTimeLogPage {...props} />
  </Suspense>
);

export default TimeLogPage;
