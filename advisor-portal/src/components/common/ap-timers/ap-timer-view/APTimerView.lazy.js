import React, { lazy, Suspense } from 'react';

const LazyAPTimerView = lazy(() => import('./APTimerView'));

const APTimerView = props => (
  <Suspense fallback={null}>
    <LazyAPTimerView {...props} />
  </Suspense>
);

export default APTimerView;
