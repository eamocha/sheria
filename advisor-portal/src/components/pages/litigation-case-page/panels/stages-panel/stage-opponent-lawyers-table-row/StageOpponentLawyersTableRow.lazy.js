import React, { lazy, Suspense } from 'react';

const LazyStageOpponentLawyersTableRow = lazy(() => import('./StageOpponentLawyersTableRow'));

const StageOpponentLawyersTableRow = props => (
  <Suspense fallback={null}>
    <LazyStageOpponentLawyersTableRow {...props} />
  </Suspense>
);

export default StageOpponentLawyersTableRow;
