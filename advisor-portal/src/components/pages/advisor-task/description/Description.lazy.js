import React, { lazy, Suspense } from 'react';

const LazyDescription = lazy(() => import('./Description'));

const Description = props => (
  <Suspense fallback={null}>
    <LazyDescription {...props} />
  </Suspense>
);

export default Description;
