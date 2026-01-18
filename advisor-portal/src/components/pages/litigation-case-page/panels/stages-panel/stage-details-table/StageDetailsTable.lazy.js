import React, { lazy, Suspense } from 'react';

const LazyStageDetailsTable = lazy(() => import('./StageDetailsTable'));

const StageDetailsTable = props => (
  <Suspense fallback={null}>
    <LazyStageDetailsTable {...props} />
  </Suspense>
);

export default StageDetailsTable;
