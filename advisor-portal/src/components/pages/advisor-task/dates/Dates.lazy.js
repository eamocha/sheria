import React, { lazy, Suspense } from 'react';

const LazyDates = lazy(() => import('./Dates'));

const Dates = props => (
  <Suspense fallback={null}>
    <LazyDates {...props} />
  </Suspense>
);

export default Dates;
