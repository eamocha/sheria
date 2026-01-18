import React, { lazy, Suspense } from 'react';

const LazyAdvisorTimeLogsAddForm = lazy(() => import('./AdvisorTimeLogsAddForm'));

const AdvisorTimeLogsAddForm = props => (
  <Suspense fallback={null}>
    <LazyAdvisorTimeLogsAddForm {...props} />
  </Suspense>
);

export default AdvisorTimeLogsAddForm;
