import React, { lazy, Suspense } from 'react';

const LazyGridTable = lazy(() => import('./GridTable'));

const GridTable = props => (
  <Suspense fallback={null}>
    <LazyGridTable {...props} />
  </Suspense>
);

export default GridTable;
