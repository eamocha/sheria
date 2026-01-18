import React, { lazy, Suspense } from 'react';

const LazyStageDetailsTableRowItem = lazy(() => import('./StageDetailsTableRowItem'));

const StageDetailsTableRowItem = props => (
  <Suspense fallback={null}>
    <LazyStageDetailsTableRowItem {...props} />
  </Suspense>
);

export default StageDetailsTableRowItem;
