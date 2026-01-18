import React, { lazy, Suspense } from 'react';

const LazyOpponentLawyersTableRow = lazy(() => import('./OpponentLawyersTableRow'));

const OpponentLawyersTableRow = props => (
  <Suspense fallback={null}>
    <LazyOpponentLawyersTableRow {...props} />
  </Suspense>
);

export default OpponentLawyersTableRow;
