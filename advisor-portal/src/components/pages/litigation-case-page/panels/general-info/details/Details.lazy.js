import React, { lazy, Suspense } from 'react';

const LazyDetails = lazy(() => import('./Details'));

const Details = props => (
  <Suspense fallback={null}>
    <LazyDetails {...props} />
  </Suspense>
);

export default Details;
