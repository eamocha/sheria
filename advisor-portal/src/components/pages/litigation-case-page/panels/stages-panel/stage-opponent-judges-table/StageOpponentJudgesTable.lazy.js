import React, { lazy, Suspense } from 'react';

const LazyStageOpponentJudgesTable = lazy(() => import('./StageOpponentJudgesTable'));

const StageOpponentJudgesTable = props => (
  <Suspense fallback={null}>
    <LazyStageOpponentJudgesTable {...props} />
  </Suspense>
);

export default StageOpponentJudgesTable;
