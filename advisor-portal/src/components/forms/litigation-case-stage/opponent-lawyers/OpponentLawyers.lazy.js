import React, { lazy, Suspense } from 'react';

const LazyOpponentLawyers = lazy(() => import('./OpponentLawyers'));

const OpponentLawyers = props => (
  <Suspense fallback={null}>
    <LazyOpponentLawyers {...props} />
  </Suspense>
);

export default OpponentLawyers;
