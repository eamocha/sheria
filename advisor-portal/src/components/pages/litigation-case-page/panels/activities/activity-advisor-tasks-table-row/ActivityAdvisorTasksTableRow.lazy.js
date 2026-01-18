import React, { lazy, Suspense } from 'react';

const LazyActivityAdvisorTasksTableRow = lazy(() => import('./ActivityAdvisorTasksTableRow'));

const ActivityAdvisorTasksTableRow = props => (
  <Suspense fallback={null}>
    <LazyActivityAdvisorTasksTableRow {...props} />
  </Suspense>
);

export default ActivityAdvisorTasksTableRow;
