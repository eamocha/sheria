import React, { lazy, Suspense } from 'react';

const LazyAPGlobalWalkThrough = lazy(() => import('./APGlobalWalkThrough'));

const APGlobalWalkThrough = props => (
  <Suspense fallback={null}>
    <LazyAPGlobalWalkThrough {...props} />
  </Suspense>
);

export default APGlobalWalkThrough;
