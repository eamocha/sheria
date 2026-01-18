import React, { lazy, Suspense } from 'react';

const LazyAPTextFieldInput = lazy(() => import('./APTextFieldInput'));

const APTextFieldInput = props => (
  <Suspense fallback={null}>
    <LazyAPTextFieldInput {...props} />
  </Suspense>
);

export default APTextFieldInput;
