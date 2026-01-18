import React, { lazy, Suspense } from 'react';

const LazyAPCheckboxBtn = lazy(() => import('./APCheckboxBtn'));

const APCheckboxBtn = props => (
  <Suspense fallback={null}>
    <LazyAPCheckboxBtn {...props} />
  </Suspense>
);

export default APCheckboxBtn;
