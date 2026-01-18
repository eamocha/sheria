import React, { lazy, Suspense } from 'react';

const LazyAdvisorTaskPage = lazy(() => import('./AdvisorTaskPage'));

const AdvisorTaskPage = props => (
  <Suspense fallback={null}>
    <LazyAdvisorTaskPage {...props} />
  </Suspense>
);

export default AdvisorTaskPage;
