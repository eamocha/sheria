import React, { lazy, Suspense } from 'react';

const LazyAPFileInput = lazy(() => import('./APFileInput'));

const APFileInput = props => (
  <Suspense fallback={null}>
    <LazyAPFileInput {...props} />
  </Suspense>
);

export default APFileInput;
