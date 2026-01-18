import React, { lazy, Suspense } from 'react';

const LazyOpponentJudgesForm = lazy(() => import('./OpponentJudgesForm'));

const OpponentJudgesForm = props => (
  <Suspense fallback={null}>
    <LazyOpponentJudgesForm {...props} />
  </Suspense>
);

export default OpponentJudgesForm;
