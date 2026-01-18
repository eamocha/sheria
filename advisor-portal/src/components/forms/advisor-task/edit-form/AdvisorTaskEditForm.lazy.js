import React, { lazy, Suspense } from 'react';

const LazyAdvisorTaskEditForm = lazy(() => import('./AdvisorTaskEditForm'));

const AdvisorTaskEditForm = props => (
  <Suspense fallback={null}>
    <LazyAdvisorTaskEditForm {...props} />
  </Suspense>
);

export default AdvisorTaskEditForm;
