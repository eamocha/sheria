import React, { lazy, Suspense } from 'react';

const LazyActivityHearingsTableHead = lazy(() => import('./ActivityHearingsTableHead'));

const ActivityHearingsTableHead = props => (
  <Suspense fallback={null}>
    <LazyActivityHearingsTableHead {...props} />
  </Suspense>
);

export default ActivityHearingsTableHead;
