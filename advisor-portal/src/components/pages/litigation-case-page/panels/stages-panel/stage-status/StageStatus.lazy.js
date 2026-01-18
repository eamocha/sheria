import React, { lazy, Suspense } from 'react';

const LazyStageStatus = lazy(() => import('./StageStatus'));

const StageStatus = props => (
  <Suspense fallback={null}>
    <LazyStageStatus {...props} />
  </Suspense>
);

export default StageStatus;
