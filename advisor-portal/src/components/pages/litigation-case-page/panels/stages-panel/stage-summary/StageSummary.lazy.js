import React, { lazy, Suspense } from 'react';

const LazyStageSummary = lazy(() => import('./StageSummary'));

const StageSummary = props => (
  <Suspense fallback={null}>
    <LazyStageSummary {...props} />
  </Suspense>
);

export default StageSummary;
