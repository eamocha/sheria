import React, { lazy, Suspense } from 'react';

const LazyOpponentJudgesEditForm = lazy(() => import('./OpponentJudgesEditForm'));

const OpponentJudgesEditForm = props => (
  <Suspense fallback={null}>
    <LazyOpponentJudgesEditForm {...props} />
  </Suspense>
);

export default OpponentJudgesEditForm;
