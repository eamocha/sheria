import React, { lazy, Suspense } from 'react';

const LazyOpponentLawyersForm = lazy(() => import('./OpponentLawyersForm'));

const OpponentLawyersForm = props => (
  <Suspense fallback={null}>
    <LazyOpponentLawyersForm {...props} />
  </Suspense>
);

export default OpponentLawyersForm;
