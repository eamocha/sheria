import React, { lazy, Suspense } from 'react';

const LazyOpponentJudges = lazy(() => import('./OpponentJudges'));

const OpponentJudges = props => (
  <Suspense fallback={null}>
    <LazyOpponentJudges {...props} />
  </Suspense>
);

export default OpponentJudges;
