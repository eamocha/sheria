import React, { lazy, Suspense } from 'react';

const LazyOpponentJudgesTableRow = lazy(() => import('./OpponentJudgesTableRow'));

const OpponentJudgesTableRow = props => (
  <Suspense fallback={null}>
    <LazyOpponentJudgesTableRow {...props} />
  </Suspense>
);

export default OpponentJudgesTableRow;
