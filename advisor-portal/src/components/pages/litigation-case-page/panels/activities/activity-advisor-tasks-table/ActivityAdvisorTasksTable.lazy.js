import React, { lazy, Suspense } from 'react';

const LazyActivityAdvisorTasksTable = lazy(() => import('./ActivityAdvisorTasksTable'));

const ActivityAdvisorTasksTable = props => (
  <Suspense fallback={null}>
    <LazyActivityAdvisorTasksTable {...props} />
  </Suspense>
);

export default ActivityAdvisorTasksTable;
