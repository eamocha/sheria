import React, { lazy, Suspense } from 'react';

const LazyStageContainer = lazy(() => import('./StageContainer'));

const StageContainer = props => (
  <Suspense fallback={null}>
    <LazyStageContainer {...props} />
  </Suspense>
);

export default StageContainer;
