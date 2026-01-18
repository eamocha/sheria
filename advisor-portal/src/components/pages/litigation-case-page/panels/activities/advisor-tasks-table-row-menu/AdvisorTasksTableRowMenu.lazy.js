import React, { lazy, Suspense } from 'react';

const LazyAdvisorTasksTableRowMenu = lazy(() => import('./AdvisorTasksTableRowMenu'));

const AdvisorTasksTableRowMenu = props => (
  <Suspense fallback={null}>
    <LazyAdvisorTasksTableRowMenu {...props} />
  </Suspense>
);

export default AdvisorTasksTableRowMenu;
