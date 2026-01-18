import React, { lazy, Suspense } from 'react';

const LazyTimeLogsPage = lazy(() => import('./TimeLogsPage'));

const TimeLogsPage = props => (
  <Suspense fallback={null}>
    <LazyTimeLogsPage {...props} />
  </Suspense>
);

export default TimeLogsPage;
