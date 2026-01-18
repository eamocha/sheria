import React, { lazy, Suspense } from 'react';

const LazyStageDetailsTableRow = lazy(() => import('./StageDetailsTableRow'));

const StageDetailsTableRow = props => (
  <Suspense fallback={null}>
    <LazyStageDetailsTableRow {...props} />
  </Suspense>
);

export default StageDetailsTableRow;
