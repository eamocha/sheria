import React, { lazy, Suspense } from 'react';

const LazyAPMaterialTable = lazy(() => import('./APMaterialTable'));

const APMaterialTable = props => (
  <Suspense fallback={null}>
    <LazyAPMaterialTable {...props} />
  </Suspense>
);

export default APMaterialTable;
