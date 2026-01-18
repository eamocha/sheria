import React, { lazy, Suspense } from 'react';

const LazyStageOpponentLawyersTable = lazy(() => import('./StageOpponentLawyersTable'));

const StageOpponentLawyersTable = props => (
  <Suspense fallback={null}>
    <LazyStageOpponentLawyersTable {...props} />
  </Suspense>
);

export default StageOpponentLawyersTable;
