import React, { lazy, Suspense } from 'react';

const LazyStageOpponentJudgesTableRow = lazy(() => import('./StageOpponentJudgesTableRow'));

const StageOpponentJudgesTableRow = props => (
  <Suspense fallback={null}>
    <LazyStageOpponentJudgesTableRow {...props} />
  </Suspense>
);

export default StageOpponentJudgesTableRow;
