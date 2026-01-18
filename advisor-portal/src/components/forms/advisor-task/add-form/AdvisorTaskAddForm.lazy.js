import React, { lazy, Suspense } from 'react';

const LazyAdvisorTaskAddForm = lazy(() => import('./AdvisorTaskAddForm'));

const AdvisorTaskAddForm = props => (
  <Suspense fallback={null}>
    <LazyAdvisorTaskAddForm {...props} />
  </Suspense>
);

export default AdvisorTaskAddForm;
