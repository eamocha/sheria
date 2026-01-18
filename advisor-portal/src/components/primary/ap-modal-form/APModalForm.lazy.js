import React, { lazy, Suspense } from 'react';

const LazyAPModalForm = lazy(() => import('./APModalForm'));

const APModalForm = props => (
  <Suspense fallback={null}>
    <LazyAPModalForm {...props} />
  </Suspense>
);

export default APModalForm;
