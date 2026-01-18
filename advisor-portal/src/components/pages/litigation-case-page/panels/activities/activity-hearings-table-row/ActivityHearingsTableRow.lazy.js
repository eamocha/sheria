import React, { lazy, Suspense } from 'react';

const LazyActivityHearingsTableRow = lazy(() => import('./ActivityHearingsTableRow'));

const ActivityHearingsTableRow = props => (
  <Suspense fallback={null}>
    <LazyActivityHearingsTableRow {...props} />
  </Suspense>
);

export default ActivityHearingsTableRow;
