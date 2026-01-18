import React, { lazy, Suspense } from 'react';

const LazyActivityAdvisorTasksTableHead = lazy(() => import('./ActivityAdvisorTasksTableHead'));

const ActivityAdvisorTasksTableHead = props => (
  <Suspense fallback={null}>
    <LazyActivityAdvisorTasksTableHead {...props} />
  </Suspense>
);

export default ActivityAdvisorTasksTableHead;
