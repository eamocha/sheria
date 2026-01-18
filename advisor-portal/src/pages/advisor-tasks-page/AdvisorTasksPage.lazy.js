import React, { lazy, Suspense } from 'react';

const LazyAdvisorTasksPage = lazy(() => import('./AdvisorTasksPage'));

const AdvisorTasksPage = props => (
  <Suspense fallback={null}>
    <LazyAdvisorTasksPage {...props} />
  </Suspense>
);

export default AdvisorTasksPage;
