import React, { lazy, Suspense } from 'react';

const LazyOpponentLawyersEditForm = lazy(() => import('./OpponentLawyersEditForm'));

const OpponentLawyersEditForm = props => (
  <Suspense fallback={null}>
    <LazyOpponentLawyersEditForm {...props} />
  </Suspense>
);

export default OpponentLawyersEditForm;
