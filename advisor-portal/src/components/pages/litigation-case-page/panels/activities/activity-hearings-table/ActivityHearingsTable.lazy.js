import React, { lazy, Suspense } from 'react';

const LazyActivityHearingsTable = lazy(() => import('./ActivityHearingsTable'));

const ActivityHearingsTable = props => (
  <Suspense fallback={null}>
    <LazyActivityHearingsTable {...props} />
  </Suspense>
);

export default ActivityHearingsTable;
